<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Http\Controllers\Controller;
use App\Models\SearchQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $history = SearchQuery::where('user_id', $request->user()->id)->latest('searched_at')->take(20)->get(['query', 'lang_code', 'searched_at']);
        return response()->json(['data' => $history]);
    }

    public function clear(Request $request): JsonResponse
    {
        SearchQuery::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Search history cleared.']);
    }
}
