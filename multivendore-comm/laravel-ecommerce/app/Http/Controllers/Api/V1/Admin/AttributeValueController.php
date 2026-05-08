<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function store(Request $request, Attribute $attribute): JsonResponse { return response()->json(['data' => $attribute->values()->create($request->validate(['value'=>'required|string|max:255','sort_order'=>'integer']))], 201); }
    public function update(Request $request, AttributeValue $attributeValue): JsonResponse { $attributeValue->update($request->validate(['value'=>'required|string|max:255','sort_order'=>'integer'])); return response()->json(['data' => $attributeValue->fresh()]); }
    public function destroy(AttributeValue $attributeValue): JsonResponse { $attributeValue->delete(); return response()->json(['message' => 'Deleted.']); }
}
