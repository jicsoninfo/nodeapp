<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = UserNotification::where('user_id', $request->user()->id)->latest()->paginate(20);
        return response()->json(['data' => $notifications, 'unread_count' => UserNotification::where('user_id', $request->user()->id)->unread()->count()]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = UserNotification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->markRead();
        return response()->json(['message' => 'Marked as read.']);
    }

    public function readAll(Request $request): JsonResponse
    {
        UserNotification::where('user_id', $request->user()->id)->unread()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        UserNotification::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Notification deleted.']);
    }
}
