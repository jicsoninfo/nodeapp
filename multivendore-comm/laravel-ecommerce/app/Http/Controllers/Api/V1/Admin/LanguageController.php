<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(): JsonResponse { return response()->json(['data' => Language::all()]); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => Language::create($request->validate(['code'=>'required|size:2|unique:languages,code','name'=>'required|max:100','native_name'=>'required|max:100','direction'=>'in:ltr,rtl']))], 201); }
    public function show(Language $language): JsonResponse { return response()->json(['data' => $language]); }
    public function update(Request $request, Language $language): JsonResponse { $language->update($request->validate(['is_active'=>'boolean','is_default'=>'boolean'])); return response()->json(['data' => $language->fresh()]); }
    public function destroy(Language $language): JsonResponse { abort_if($language->is_default, 422, 'Cannot delete the default language.'); $language->delete(); return response()->json(['message' => 'Deleted.']); }
}
