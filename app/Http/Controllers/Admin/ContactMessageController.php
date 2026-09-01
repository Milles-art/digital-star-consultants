<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class ContactMessageController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View|JsonResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        if (! Schema::hasTable('contact_messages')) {
            $messages = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            if (! $request->expectsJson()) {
                return view('admin.contact-messages.index', compact('messages'))->with('schemaMissing', true);
            }
            return response()->json(['status' => 'success', 'data' => $messages]);
        }

        $query = ContactMessage::query()->latest();

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                foreach (['name', 'email', 'message', 'phone', 'subject'] as $column) {
                    if (Schema::hasColumn('contact_messages', $column)) {
                        $method = $column === 'name' ? 'where' : 'orWhere';
                        $q->{$method}($column, 'like', $search);
                    }
                }
            });
        }

        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $messages = $query->paginate($perPage);

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
                    'message' => $msg->message,
                    'is_read' => $msg->read_at !== null,
                    'created_at' => $msg->created_at?->format('Y-m-d H:i'),
                ];
            }),
        ]);
    }

    public function show(ContactMessage $contactMessage): View|JsonResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        if ($contactMessage->read_at === null) {
            $contactMessage->forceFill(['read_at' => now()])->save();
        }

        if (! request()->expectsJson()) {
            return view('admin.contact-messages.show', ['message' => $contactMessage]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
                'message' => $contactMessage->message,
                'is_read' => $contactMessage->read_at !== null,
                'created_at' => $contactMessage->created_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    public function destroy(ContactMessage $contactMessage): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        $contactMessage->delete();

        if (! request()->expectsJson()) {
            return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted successfully.');
        }

        return response()->json(['status' => 'success', 'message' => 'Message deleted']);
    }
}
