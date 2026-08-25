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
