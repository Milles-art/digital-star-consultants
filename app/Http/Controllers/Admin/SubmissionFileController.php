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
