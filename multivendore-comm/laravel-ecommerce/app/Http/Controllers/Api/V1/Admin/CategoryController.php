<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Category::with('translations','children')->root()->orderBy('sort_order')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['parent_id' => 'nullable|uuid|exists:categories,id', 'slug' => 'required|string|unique:categories,slug', 'sort_order' => 'integer', 'is_active' => 'boolean', 'translations' => 'required|array', 'translations.*.lang_code' => 'required|string|exists:languages,code', 'translations.*.name' => 'required|string|max:200']);
        $parent   = isset($data['parent_id']) ? Category::find($data['parent_id']) : null;
        $category = Category::create(['parent_id' => $data['parent_id'] ?? null, 'slug' => $data['slug'], 'depth' => $parent ? $parent->depth + 1 : 0, 'sort_order' => $data['sort_order'] ?? 0, 'is_active' => $data['is_active'] ?? true]);
        foreach ($data['translations'] as $t) { $category->translations()->create($t); }
        return response()->json(['data' => $category->load('translations')], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json(['data' => $category->load('translations','children.translations')]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate(['slug' => 'sometimes|unique:categories,slug,'.$category->id, 'sort_order' => 'integer', 'is_active' => 'boolean']);
        $category->update($data);
        if ($request->translations) {
            foreach ($request->translations as $t) { $category->translations()->updateOrCreate(['lang_code' => $t['lang_code']], $t); }
        }
        return response()->json(['data' => $category->fresh('translations')]);
    }

    public function destroy(Category $category): JsonResponse
    {
        abort_if($category->children()->exists(), 422, 'Cannot delete a category that has children.');
        $category->delete();
        return response()->json(['message' => 'Category deleted.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*.id' => 'required|uuid', 'order.*.sort_order' => 'required|integer']);
        foreach ($request->order as $item) { Category::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]); }
        return response()->json(['message' => 'Categories reordered.']);
    }
}
