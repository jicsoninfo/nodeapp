<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): JsonResponse { return response()->json(['data' => Setting::orderBy('group')->orderBy('key')->get()->groupBy('group')]); }
    public function update(Request $request): JsonResponse
    {
        $request->validate(['settings' => 'required|array', 'settings.*.key' => 'required|string', 'settings.*.value' => 'present']);
        foreach ($request->settings as $s) { Setting::set($s['key'], $s['value']); }
        return response()->json(['message' => count($request->settings) . ' settings updated.']);
    }
}
