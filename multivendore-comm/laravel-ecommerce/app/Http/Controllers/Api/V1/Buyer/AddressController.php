<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->addresses()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label'       => 'required|string|max:50',
            'full_name'   => 'required|string|max:200',
            'line1'       => 'required|string|max:255',
            'line2'       => 'nullable|string|max:255',
            'city'        => 'required|string|max:100',
            'state'       => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country_code'=> 'required|string|size:2',
            'is_default'  => 'boolean',
        ]);

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($data);
        return response()->json(['data' => $address], 201);
    }

    public function show(Request $request, Address $address): JsonResponse
    {
        abort_if($address->user_id !== $request->user()->id, 403);
        return response()->json(['data' => $address]);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        abort_if($address->user_id !== $request->user()->id, 403);
        $data = $request->validate([
            'label'       => 'sometimes|string|max:50',
            'full_name'   => 'sometimes|string|max:200',
            'line1'       => 'sometimes|string|max:255',
            'line2'       => 'nullable|string|max:255',
            'city'        => 'sometimes|string|max:100',
            'state'       => 'nullable|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country_code'=> 'sometimes|string|size:2',
        ]);
        $address->update($data);
        return response()->json(['data' => $address->fresh()]);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        abort_if($address->user_id !== $request->user()->id, 403);
        $address->delete();
        return response()->json(['message' => 'Address deleted.']);
    }

    public function setDefault(Request $request, Address $address): JsonResponse
    {
        abort_if($address->user_id !== $request->user()->id, 403);
        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return response()->json(['data' => $address->fresh(), 'message' => 'Default address updated.']);
    }
}
