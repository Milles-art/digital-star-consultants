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

    /**
     * List contact messages (management only).
     */
    public function index(Request $request): View|JsonResponse
    {
        // Only management can view contact messages
        abort_unless(auth()->user()?->isManagement(), 403);

        $query = ContactMessage::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate($request->integer('per_page', 20));

        if (! $request->expectsJson()) {
            return view('admin.contact-messages.index', compact('messages'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    /**
     * Show a single contact message.
     */
    public function show(ContactMessage $contactMessage): View|JsonResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        if (! request()->expectsJson()) {
            return view('admin.contact-messages.show', ['message' => $contactMessage]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $contactMessage,
        ]);
    }

    /**
     * Delete a contact message.
     */
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
