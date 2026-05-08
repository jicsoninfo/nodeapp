<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateStoreRequest;
use App\Http\Resources\V1\VendorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => new VendorResource($request->user()->vendor->load('profile', 'translations', 'policies'))]);
    }

    public function update(UpdateStoreRequest $request): JsonResponse
    {
        $vendor = $request->user()->vendor;
        $this->authorize('update', $vendor);

        $data = $request->validated();
        if (isset($data['store_name'])) $vendor->update(['store_name' => $data['store_name']]);
        if (isset($data['profile']))    $vendor->profile()->update($data['profile']);

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $t) {
                $vendor->translations()->updateOrCreate(['lang_code' => $t['lang_code']], $t);
            }
        }

        return response()->json(['data' => new VendorResource($vendor->fresh('profile')), 'message' => 'Store updated.']);
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate(['logo' => 'required|image|max:2048']);
        $path = $request->file('logo')->store("vendors/{$request->user()->vendor->id}/logo", 's3');
        $request->user()->vendor->profile()->update(['logo_url' => $path]);
        return response()->json(['logo_url' => $path]);
    }

    public function uploadBanner(Request $request): JsonResponse
    {
        $request->validate(['banner' => 'required|image|max:5120']);
        $path = $request->file('banner')->store("vendors/{$request->user()->vendor->id}/banner", 's3');
        $request->user()->vendor->profile()->update(['banner_url' => $path]);
        return response()->json(['banner_url' => $path]);
    }
}
