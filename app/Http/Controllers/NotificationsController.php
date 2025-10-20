<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReportNotification;
use App\Models\MessageReportNotification;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function markRead(Request $request, string $type, int $id)
    {
        $userId = Auth::id();

        $notification = match ($type) {
            'report' => ReportNotification::where('id', $id)->where('user_id', $userId)->first(),
            'message_report' => MessageReportNotification::where('id', $id)->where('user_id', $userId)->first(),
            default => null,
        };

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        $userId = Auth::id();

        ReportNotification::where('user_id', $userId)->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        MessageReportNotification::where('user_id', $userId)->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}


