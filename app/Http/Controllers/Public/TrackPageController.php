<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackPageController extends Controller
{
    public function form(): View
    {
        return view('track.form');
    }

    public function show(Request $request, string $reference): View
    {
        $submission = Submission::with('service')
            ->where('reference_number', $reference)
            ->first();

        $timeline = $this->timelineFor($submission?->status);

        $customerName = null;
        if ($submission) {
            $parts = preg_split('/\s+/', trim((string) $submission->customer_name), -1, PREG_SPLIT_NO_EMPTY);
            $customerName = $parts[0] ?? 'Customer';
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
            ['key' => 'pending', 'label' => 'Request received', 'description' => 'Your application has been received successfully.'],
            ['key' => 'in_progress', 'label' => 'Being processed', 'description' => 'Our team is reviewing and working on your request.'],
            ['key' => 'completed', 'label' => 'Completed', 'description' => 'Your request has been completed.'],
        ];

        if ($status === Submission::STATUS_AWAITING_CUSTOMER) {
            $steps = [
                ['key' => 'pending', 'label' => 'Request received', 'description' => 'Your application has been received successfully.'],
                ['key' => 'awaiting_customer', 'label' => 'Action needed from you', 'description' => 'We need additional information or documents before we can continue.'],
                ['key' => 'in_progress', 'label' => 'Processing resumes', 'description' => 'We will continue once the requested information is received.'],
                ['key' => 'completed', 'label' => 'Completed', 'description' => 'Your request has been completed.'],
            ];
        } elseif (in_array($status, [Submission::STATUS_REJECTED, Submission::STATUS_CANCELLED], true)) {
            $steps = [
                ['key' => 'pending', 'label' => 'Request received', 'description' => 'Your application was received successfully.'],
                ['key' => $status, 'label' => $status === Submission::STATUS_REJECTED ? 'Request not approved' : 'Request cancelled', 'description' => $status === Submission::STATUS_REJECTED ? 'Please contact us for more information about your request.' : 'This request is no longer active.'],
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
