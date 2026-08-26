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

        return view('track.show', [
            'reference' => $reference,
            'submission' => $submission,
        ]);
    }
}
