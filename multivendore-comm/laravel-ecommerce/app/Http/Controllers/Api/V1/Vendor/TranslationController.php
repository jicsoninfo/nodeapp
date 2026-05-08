<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->vendor->translations()->get()]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate(['translations' => 'required|array', 'translations.*.lang_code' => 'required|string|exists:languages,code', 'translations.*.description' => 'nullable|string|max:2000']);
        $vendor = $request->user()->vendor;
        foreach ($request->translations as $t) {
            $vendor->translations()->updateOrCreate(['lang_code' => $t['lang_code']], $t);
        }
        return response()->json(['data' => $vendor->translations()->get(), 'message' => 'Translations updated.']);
    }
}
