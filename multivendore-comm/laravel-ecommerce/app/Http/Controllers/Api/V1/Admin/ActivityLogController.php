<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = Activity::with('causer','subject')
            ->when($request->log_name, fn ($q) => $q->inLog($request->log_name))
            ->when($request->causer_id, fn ($q) => $q->causedBy(\App\Models\User::find($request->causer_id)))
            ->latest()->paginate(50);
        return response()->json(['data' => $logs]);
    }
}
