<?php
namespace App\Http\Controllers\Api\V1\Public;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->status->isVisible(), 404);
        $reviews = Review::where('product_id', $product->id)->approved()
            ->when($request->rating, fn ($q) => $q->where('rating', $request->rating))
            ->with('user.profile','media')
            ->latest()->paginate(10);
        return response()->json([
            'data'  => $reviews,
            'stats' => [
                'avg_rating'    => $product->avg_rating,
                'total_reviews' => $product->total_reviews,
                'by_rating'     => Review::where('product_id', $product->id)->approved()
                    ->selectRaw('rating, COUNT(*) as count')->groupBy('rating')->pluck('count','rating'),
            ],
        ]);
    }
}
