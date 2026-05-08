<?php
namespace App\Http\Controllers\Api\V1\Public;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $locale = app()->getLocale();
        $cats   = Category::root()->active()->with('translations','children.translations')->orderBy('sort_order')->get();
        return response()->json(['data' => $cats->map(fn ($c) => [
            'id'          => $c->id,
            'slug'        => $c->slug,
            'name'        => $c->getTranslation($locale)?->name,
            'description' => $c->getTranslation($locale)?->description,
            'children'    => $c->children->map(fn ($ch) => ['id' => $ch->id, 'slug' => $ch->slug, 'name' => $ch->getTranslation($locale)?->name]),
        ])]);
    }

    public function tree(): JsonResponse
    {
        $locale = app()->getLocale();
        $tree   = $this->buildTree(null, $locale);
        return response()->json(['data' => $tree]);
    }

    private function buildTree(?string $parentId, string $locale): array
    {
        return Category::where('parent_id', $parentId)->active()->with('translations')->orderBy('sort_order')->get()
            ->map(fn ($c) => [
                'id'       => $c->id, 'slug' => $c->slug, 'depth' => $c->depth,
                'name'     => $c->getTranslation($locale)?->name,
                'children' => $this->buildTree($c->id, $locale),
            ])->toArray();
    }

    public function show(Category $category): JsonResponse
    {
        $locale = app()->getLocale();
        return response()->json(['data' => [
            'id'          => $category->id,
            'slug'        => $category->slug,
            'name'        => $category->getTranslation($locale)?->name,
            'description' => $category->getTranslation($locale)?->description,
        ]]);
    }

    public function products(Category $category): JsonResponse
    {
        $products = \App\Models\Product::where('category_id', $category->id)
            ->active()->with(['translations','variants','media','brand','vendor.profile'])
            ->paginate(20);
        return response()->json(['data' => ProductResource::collection($products)]);
    }
}
