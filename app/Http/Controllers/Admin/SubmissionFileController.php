<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionFileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Download a private submission file (staff / management only).
     * Public users can never access these files.
     */
    public function download(Request $request, Submission $submission, SubmissionFieldValue $value): StreamedResponse
    {
        // Ensure the value belongs to this submission
        if ($value->submission_id !== $submission->id) {
            abort(404);
        }

        $this->authorize('view', $submission);

        if (! $value->file_path || ! Storage::disk('private')->exists($value->file_path)) {
            abort(404, 'File not found');
        }

        $filename = basename($value->file_path);

        return Storage::disk('private')->download(
            $value->file_path,
            $filename,
            [
                'Content-Type' => Storage::disk('private')->mimeType($value->file_path) ?? 'application/octet-stream',
            ]
        );
    }
}
