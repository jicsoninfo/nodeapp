<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(): JsonResponse { return response()->json(['data' => Brand::all()]); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => Brand::create($request->validate(['name'=>'required|unique:brands,name|max:200','slug'=>'required|unique:brands,slug|max:200','logo_url'=>'nullable|url','is_verified'=>'boolean']))], 201); }
    public function show(Brand $brand): JsonResponse { return response()->json(['data' => $brand]); }
    public function update(Request $request, Brand $brand): JsonResponse { $brand->update($request->validate(['name'=>'sometimes|max:200','logo_url'=>'nullable|url','is_verified'=>'boolean'])); return response()->json(['data' => $brand->fresh()]); }
    public function destroy(Brand $brand): JsonResponse { $brand->delete(); return response()->json(['message' => 'Brand deleted.']); }
    public function verify(Brand $brand): JsonResponse { $brand->update(['is_verified' => true]); return response()->json(['message' => 'Brand verified.']); }
}
