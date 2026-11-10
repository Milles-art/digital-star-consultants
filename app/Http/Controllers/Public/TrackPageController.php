<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackPageController extends Controller
{
    public function form(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if ($request->has('reference')) {
            $reference = $request->input('reference');
            if (is_string($reference)) {
                $request->merge(['reference' => strtoupper(trim($reference))]);
            }
            $validated = $request->validate([
                'reference' => ['required', 'string', 'regex:/\A'.\App\Support\SubmissionReference::PATTERN.'\z/'],
            ], ['reference.regex' => __('public.track_invalid')]);

            return redirect()->route('public.track.show', $validated['reference']);
        }

        return view('track.form');
    }

    public function show(Request $request, string $reference): View
    {
        $submission = Submission::with('service')
            ->where('reference_number', strtoupper($reference))
            ->first();

        $timeline = $this->timelineFor($submission?->status);

        $customerName = null;
        if ($submission) {
            $parts = preg_split('/\s+/', trim((string) $submission->customer_name), -1, PREG_SPLIT_NO_EMPTY);
            $customerName = $parts[0] ?? __('site.track.customer_fallback');
        }

        return view('track.show', [
            'reference' => $reference,
            'submission' => $submission,
            'customerName' => $customerName,
            'timeline' => $timeline,
        ]);
    }

    private function timelineFor(?string $status): array
    {
        $steps = [
            ['key' => 'pending', 'label' => __('site.track.tl_received_label'), 'description' => __('site.track.tl_received_desc')],
            ['key' => 'in_progress', 'label' => __('site.track.tl_processing_label'), 'description' => __('site.track.tl_processing_desc')],
            ['key' => 'completed', 'label' => __('site.track.tl_completed_label'), 'description' => __('site.track.tl_completed_desc')],
        ];

        if ($status === Submission::STATUS_AWAITING_CUSTOMER) {
            $steps = [
                ['key' => 'pending', 'label' => __('site.track.tl_received_label'), 'description' => __('site.track.tl_received_desc')],
                ['key' => 'awaiting_customer', 'label' => __('site.track.tl_action_label'), 'description' => __('site.track.tl_action_desc')],
                ['key' => 'in_progress', 'label' => __('site.track.tl_resume_label'), 'description' => __('site.track.tl_resume_desc')],
                ['key' => 'completed', 'label' => __('site.track.tl_completed_label'), 'description' => __('site.track.tl_completed_desc')],
            ];
        } elseif (in_array($status, [Submission::STATUS_REJECTED, Submission::STATUS_CANCELLED], true)) {
            $steps = [
                ['key' => 'pending', 'label' => __('site.track.tl_received_label'), 'description' => __('site.track.tl_received_desc')],
                ['key' => $status, 'label' => $status === Submission::STATUS_REJECTED ? __('site.track.tl_not_approved_label') : __('site.track.tl_cancelled_label'), 'description' => $status === Submission::STATUS_REJECTED ? __('site.track.tl_not_approved_desc') : __('site.track.tl_cancelled_desc')],
            ];
        }

        $rank = match ($status) {
            Submission::STATUS_PENDING, null => 0,
            Submission::STATUS_IN_PROGRESS => 1,
            Submission::STATUS_COMPLETED => 2,
            Submission::STATUS_AWAITING_CUSTOMER => 1,
            default => 1,
        };

        return collect($steps)->map(function (array $step, int $index) use ($status, $rank): array {
            $state = 'upcoming';
            if ($step['key'] === $status) {
                $state = 'current';
            } elseif ($step['key'] === Submission::STATUS_PENDING && $status !== null) {
                $state = 'done';
            } elseif ($index < $rank) {
                $state = 'done';
            }

            if ($status === Submission::STATUS_COMPLETED && $step['key'] === 'completed') {
                $state = 'done';
            }

            if (in_array($status, [Submission::STATUS_REJECTED, Submission::STATUS_CANCELLED], true) && $step['key'] === $status) {
                $state = 'current danger';
            }

            return $step + ['state' => $state];
        })->all();
    }
}
