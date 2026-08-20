<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubmissionFieldValue;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SubmissionFileController extends Controller
{
    use AuthorizesRequests;

    public function download(SubmissionFieldValue $value): BinaryFileResponse
    {
        abort_unless($value->isFile() && $value->hasFile(), 404);

        $this->authorize('view', $value->submission);

        return Storage::disk('local')->download($value->file_path, basename($value->file_path));
    }
}
