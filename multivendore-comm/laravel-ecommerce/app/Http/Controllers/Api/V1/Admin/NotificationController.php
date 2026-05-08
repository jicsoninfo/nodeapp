<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function broadcast(Request $request): JsonResponse
    {
        $request->validate(['title' => 'required|string|max:255', 'body' => 'required|string', 'topic' => 'sometimes|string']);
        $topic = $request->topic ?? 'all_users';
        SendPushNotification::dispatchToTopic($topic, $request->title, $request->body);
        return response()->json(['message' => "Broadcast dispatched to topic '{$topic}'."]);
    }

    public function sendToUser(Request $request, User $user): JsonResponse
    {
        $request->validate(['title' => 'required|string|max:255', 'body' => 'required|string']);
        SendPushNotification::dispatch($user, $request->title, $request->body);
        return response()->json(['message' => "Notification dispatched to {$user->email}."]);
    }
}
