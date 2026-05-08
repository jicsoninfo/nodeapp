<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Models\SearchQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:200']);

        $results = Product::search($request->q)
            ->where('status', 'active')
            ->when($request->category_id, fn ($s) => $s->where('category_id', $request->category_id))
            ->paginate(20);

        // Log search
        SearchQuery::create([
            'user_id'       => $request->user()?->id,
            'session_id'    => $request->header('X-Session-Id'),
            'query'         => $request->q,
            'lang_code'     => $request->header('Accept-Language', 'en'),
            'results_count' => $results->total(),
        ]);

        return response()->json(['data' => ProductResource::collection($results)]);
    }
}
