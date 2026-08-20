#!/bin/bash
# Fix remaining 5 test failures
# Run from repo root: bash fix-tests.sh

set -e

echo "🔧 Fixing remaining test failures..."

# Fix 1 & 2: Public/SubmissionController.php - flatten store response + remove PII from track
php << 'PHPEOF'
<?php
$content = file_get_contents("app/Http/Controllers/Public/SubmissionController.php");

// Fix store(): flatten data.submission into data directly
$oldStore = <<<'OLD'
return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully',
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'tracking_url' => url("/track/{$submission->reference_number}"),
                    'submission' => [
                        'id' => $submission->id,
                        'customer_name' => $submission->customer_name,
                        'customer_phone' => $submission->customer_phone,
                        'customer_email' => $submission->customer_email,
                        'service_name' => $service->name,
                        'created_at' => $submission->created_at->format('Y-m-d H:i'),
                    ],
                ],
            ], 201);
OLD;

$newStore = <<<'NEW'
return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully',
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'tracking_url' => url("/track/{$submission->reference_number}"),
                    'customer_name' => $submission->customer_name,
                    'service_name' => $service->name,
                ],
            ], 201);
NEW;

$content = str_replace($oldStore, $newStore, $content);

// Fix track(): remove PII fields (customer_phone, customer_email, staff_notes)
$oldTrack = <<<'OLD'
return response()->json([
            'status' => 'success',
            'data' => [
                'reference_number' => $submission->reference_number,
                'status' => $submission->status,
                'status_label' => $submission->status_label,
                'status_color' => $submission->status_color,
                'service_name' => $submission->service->name ?? null,
                'customer_name' => $submission->customer_name,
                'customer_phone' => $submission->customer_phone,
                'customer_email' => $submission->customer_email,
                'preferred_date' => $submission->preferred_date,
                'created_at' => $submission->created_at->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at?->format('Y-m-d H:i'),
                'fields' => $submission->values->map(function ($value) {
                    return [
                        'label' => $value->field->label ?? null,
                        'field_key' => $value->field->field_key ?? null,
                        'value' => $value->getValueForDisplay(),
                        'is_file' => $value->isFile(),
                    ];
                }),
                'staff_notes' => $submission->staff_notes,
            ],
        ]);
OLD;

$newTrack = <<<'NEW'
return response()->json([
            'status' => 'success',
            'data' => [
                'reference_number' => $submission->reference_number,
                'status' => $submission->status,
                'status_label' => $submission->status_label,
                'status_color' => $submission->status_color,
                'service_name' => $submission->service->name ?? null,
                'customer_name' => $submission->customer_name,
                'preferred_date' => $submission->preferred_date,
                'created_at' => $submission->created_at->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at?->format('Y-m-d H:i'),
                'fields' => $submission->values->map(function ($value) {
                    return [
                        'label' => $value->field->label ?? null,
                        'field_key' => $value->field->field_key ?? null,
                        'value' => $value->getValueForDisplay(),
                        'is_file' => $value->isFile(),
                    ];
                }),
            ],
        ]);
NEW;

$content = str_replace($oldTrack, $newTrack, $content);

file_put_contents("app/Http/Controllers/Public/SubmissionController.php", $content);
echo "✅ Fixed Public/SubmissionController.php\n";
PHPEOF

# Fix 3: routes/web.php - restore proper staff controller routes
php << 'PHPEOF'
<?php
$content = file_get_contents("routes/web.php");

$oldStaff = <<<'OLD'
    // Staff Routes
    Route::prefix('staff')->middleware(['role:staff', 'throttle:60,1'])->group(function () {
        Route::get('/submissions', function () {
            return response()->json([
                'message' => 'Staff submissions list',
                'user' => auth()->user()
            ]);
        })->name('staff.submissions');
    });
OLD;

$newStaff = <<<'NEW'
    // Staff Routes
    Route::prefix('staff')->middleware(['role:staff', 'throttle:60,1'])->group(function () {
        Route::get('/submissions', [\App\Http\Controllers\Staff\SubmissionController::class, 'index'])->name('staff.submissions');
        Route::get('/submissions/{submission}', [\App\Http\Controllers\Staff\SubmissionController::class, 'show'])->name('staff.submissions.show');
        Route::post('/submissions/{submission}/in-progress', [\App\Http\Controllers\Staff\SubmissionController::class, 'markInProgress'])->name('staff.submissions.in-progress');
        Route::post('/submissions/{submission}/complete', [\App\Http\Controllers\Staff\SubmissionController::class, 'markCompleted'])->name('staff.submissions.complete');
        Route::post('/submissions/{submission}/reject', [\App\Http\Controllers\Staff\SubmissionController::class, 'markRejected'])->name('staff.submissions.reject');
        Route::put('/submissions/{submission}/notes', [\App\Http\Controllers\Staff\SubmissionController::class, 'updateNotes'])->name('staff.submissions.notes');
    });
NEW;

$content = str_replace($oldStaff, $newStaff, $content);
file_put_contents("routes/web.php", $content);
echo "✅ Fixed routes/web.php\n";
PHPEOF

echo ""
echo "============================================================"
echo "🎉 All test fixes applied!"
echo "============================================================"
echo ""
echo "Run tests: php artisan test --compact"
echo "Then commit: git add -A && git commit -m 'fix: test failures - response structure, PII, staff routes' && git push"
