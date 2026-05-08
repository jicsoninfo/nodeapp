<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index(): JsonResponse { return response()->json(['data' => Attribute::with('values','translations')->get()]); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => Attribute::create($request->validate(['name'=>'required|unique:attributes,name|max:100','type'=>'required|in:text,number,boolean,color,size,select']))], 201); }
    public function show(Attribute $attribute): JsonResponse { return response()->json(['data' => $attribute->load('values','translations')]); }
    public function update(Request $request, Attribute $attribute): JsonResponse { $attribute->update($request->validate(['name'=>'sometimes|max:100','type'=>'sometimes|in:text,number,boolean,color,size,select'])); return response()->json(['data' => $attribute->fresh()]); }
    public function destroy(Attribute $attribute): JsonResponse { $attribute->delete(); return response()->json(['message' => 'Attribute deleted.']); }
}
