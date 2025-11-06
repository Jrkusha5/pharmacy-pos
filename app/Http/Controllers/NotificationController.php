<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user
     */
    public function index(Request $request)
    {
        $query = Notification::forCurrentUser()
            ->with('item')
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter unread only
        if ($request->has('unread') && $request->unread) {
            $query->unread();
        }

        // Limit for dropdown
        if ($request->has('limit')) {
            $notifications = $query->limit($request->limit)->get();
        } else {
            $notifications = $query->paginate(20);
        }

        if ($request->expectsJson() || $request->has('limit')) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => Notification::forCurrentUser()->unread()->count(),
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (for badge)
     */
    public function unreadCount()
    {
        $count = Notification::forCurrentUser()->unread()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::forCurrentUser()->findOrFail($id);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => Notification::forCurrentUser()->unread()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::forCurrentUser()->unread()->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::forCurrentUser()->findOrFail($id);
        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => Notification::forCurrentUser()->unread()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Notification deleted');
    }
}

