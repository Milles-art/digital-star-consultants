<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use Illuminate\Support\Facades\Storage;

class SubmissionFileController extends Controller
{
    /**
     * Stream a private uploaded submission file to an authorized staff/admin user.
     * GET /admin/submissions/{submission}/files/{value}
     *
     * Access control:
     *  - Requires session auth + the ['auth', 'role:admin,ceo,gm'] middleware
     *    group the route sits behind.
     *  - Re-checked against SubmissionPolicy::view() below, mirroring
     *    Admin\SubmissionController::show() (management can view any
     *    submission, staff only ones assigned to them).
     *  - $value is verified to actually belong to $submission — without
     *    this check, someone with access to *any* submission could pass a
     *    mismatched {submission}/{value} pair and potentially read a file
     *    belonging to a submission they aren't authorized for (IDOR).
     */
    public function download(Submission $submission, SubmissionFieldValue $value)
    {
        $this->authorize('view', $submission);

        if ($value->submission_id !== $submission->id) {
            abort(404, 'File not found');
        }

        if (!$value->isFile() || !$value->hasFile()) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download(
            $value->file_path,
            basename($value->file_path)
        );
    }
}
