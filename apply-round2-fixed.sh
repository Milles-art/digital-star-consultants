#!/bin/bash
set -e

echo "=== Applying Round 2 Security Patches ==="

cat > app/Http/Controllers/Admin/ContactMessageController.php << 'EOF'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View|JsonResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        $query = ContactMessage::query()->latest();

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('subject', 'like', $search)
                    ->orWhere('message', 'like', $search);
            });
        }

        $messages = $query->paginate($request->integer('per_page', 20));

        if (! $request->expectsJson()) {
            return view('admin.contact-messages.index', compact('messages'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $messages->through(function (ContactMessage $msg) {
                return [
                    'id' => $msg->id,
                    'name' => $msg->name,
                    'email' => $msg->email,
                    'phone' => $msg->phone,
                    'subject' => $msg->subject,
                    'message' => $msg->message,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at?->format('Y-m-d H:i'),
                ];
            }),
        ]);
    }

    public function show(ContactMessage $contactMessage): View|JsonResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        if (! request()->expectsJson()) {
            return view('admin.contact-messages.show', ['message' => $contactMessage]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
                'phone' => $contactMessage->phone,
                'subject' => $contactMessage->subject,
                'message' => $contactMessage->message,
                'is_read' => $contactMessage->is_read,
                'created_at' => $contactMessage->created_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        $contactMessage->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Message deleted',
        ]);
    }
}
EOF

cat > app/Services/SubmissionService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Jobs\SendNewSubmissionEmailJob;
use Illuminate\Support\Facades\DB;
use Exception;

class SubmissionService
{
    public function createSubmission(Service $service, array $data): Submission
    {
        return DB::transaction(function () use ($service, $data) {
            try {
                $submission = Submission::create([
                    'service_id' => $service->id,
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'customer_notes' => $data['customer_notes'] ?? null,
                    'preferred_date' => $data['preferred_date'] ?? null,
                    'status' => 'pending',
                ]);

                if (!empty($data['fields'])) {
                    foreach ($service->fields as $field) {
                        $value = $data['fields'][$field->field_key] ?? null;
                        $filePath = null;

                        if ($field->field_type === 'file' && isset($data['files'][$field->field_key])) {
                            $file = $data['files'][$field->field_key];
                            $filePath = $file->store("submissions/{$submission->id}", 'private');
                        }

                        SubmissionFieldValue::create([
                            'submission_id' => $submission->id,
                            'service_field_id' => $field->id,
                            'value' => $value,
                            'file_path' => $filePath,
                        ]);
                    }
                }

                SendNewSubmissionEmailJob::dispatch($submission);

                return $submission;
            } catch (Exception $e) {
                throw new Exception('Failed to create submission: ' . $e->getMessage());
            }
        });
    }

    public function assignToStaff(Submission $submission, int $staffId): Submission
    {
        $submission->processed_by = $staffId;
        $submission->save();

        return $submission;
    }

    public function markAsCompleted(Submission $submission): Submission
    {
        $submission->status = 'completed';
        $submission->completed_at = now();
        $submission->save();

        return $submission;
    }

    public function markAsInProgress(Submission $submission): Submission
    {
        $submission->status = 'in_progress';
        $submission->save();

        return $submission;
    }

    public function markAsRejected(Submission $submission, ?string $reason = null): Submission
    {
        $submission->status = 'rejected';
        $submission->staff_notes = $reason ?? $submission->staff_notes;
        $submission->save();

        return $submission;
    }
}
EOF

sed -i "s/'after_commit' => false/'after_commit' => true/g" config/queue.php

cat > app/Http/Controllers/Public/ContactController.php << 'EOF'
<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function store(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([
            'name' => strip_tags($validated['name']),
            'email' => strip_tags($validated['email']),
            'phone' => isset($validated['phone']) ? strip_tags($validated['phone']) : null,
            'subject' => isset($validated['subject']) ? strip_tags($validated['subject']) : null,
            'message' => strip_tags($validated['message']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thank you! Your message has been sent successfully.',
            ], 201);
        }

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
EOF

cat > app/Http/Controllers/Admin/SubmissionFileController.php << 'EOF'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use Illuminate\Support\Facades\Storage;

class SubmissionFileController extends Controller
{
    public function download(Submission $submission, SubmissionFieldValue $value)
    {
        $this->authorize('view', $submission);

        if ($value->submission_id !== $submission->id) {
            abort(404, 'File not found');
        }

        if (!$value->isFile() || !$value->hasFile()) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download(
            $value->file_path,
            basename($value->file_path)
        );
    }
}
EOF

rm -f apply-all.sh apply-round2.sh
git add apply-all.sh apply-round2.sh 2>/dev/null || true

echo "=== Syntax checking ==="
php -l app/Http/Controllers/Admin/ContactMessageController.php
php -l app/Services/SubmissionService.php
php -l config/queue.php
php -l app/Http/Controllers/Public/ContactController.php
php -l app/Http/Controllers/Admin/SubmissionFileController.php

echo "=== Committing ==="
git add -A
git commit -m "security: round 2 hardening — file disk, SQLi, queue after_commit, XSS sanitization

- ContactMessageController: bound-parameter LIKE search (SQLi fix),
  return whitelisted fields instead of raw model (data leak fix)
- SubmissionService: store uploads on 'private' disk instead of 'public'
- SubmissionFileController: download from 'private' disk to match storage
- config/queue.php: set after_commit=true on all connections (rollback safety)
- ContactController: strip_tags on stored input (stored XSS prevention)"

git push origin digital-star-consultants

echo "=== DONE ==="
