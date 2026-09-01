<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Keep the admin shell usable even before the post-V27 notification
        // migration has been applied to an existing local database.
        if (! Schema::hasTable('notifications')) {
            $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                20,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $unreadCount = 0;

            return view('admin.notifications.index', compact('notifications', 'unreadCount'));
        }

        $notifications = $user->notifications()->latest()->paginate(20);
        $unreadCount = $user->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        return redirect($item->data['url'] ?? route('admin.notifications.index'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
