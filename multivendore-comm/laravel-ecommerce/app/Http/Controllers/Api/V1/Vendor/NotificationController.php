<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = UserNotification::where('user_id', $request->user()->id)->latest()->paginate(20);
        return response()->json(['data' => $notifications]);
    }
}
