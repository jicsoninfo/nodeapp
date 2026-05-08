<?php
namespace App\Http\Controllers\Api\V1\Public;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Brand::verified()->orderBy('name')->get()]);
    }

    public function show(Brand $brand): JsonResponse
    {
        return response()->json(['data' => $brand]);
    }
}
