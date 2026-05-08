<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reviews = Review::where('user_id', $request->user()->id)->with('product.translations')->latest()->paginate(15);
        return response()->json(['data' => $reviews]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id'    => 'required|uuid|exists:products,id',
            'order_item_id' => 'nullable|uuid|exists:order_items,id',
            'rating'        => 'required|integer|min:1|max:5',
            'title'         => 'nullable|string|max:255',
            'body'          => 'nullable|string|max:5000',
        ]);

        $review = Review::create(array_merge($data, [
            'user_id'              => $request->user()->id,
            'is_verified_purchase' => (bool) ($data['order_item_id'] ?? false),
            'status'               => 'pending',
            'lang_code'            => app()->getLocale(),
        ]));

        return response()->json(['data' => $review, 'message' => 'Review submitted and pending approval.'], 201);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        $this->authorize('update', $review);
        $data = $request->validate(['rating' => 'sometimes|integer|min:1|max:5', 'title' => 'nullable|string|max:255', 'body' => 'nullable|string|max:5000']);
        $review->update($data);
        return response()->json(['data' => $review->fresh()]);
    }

    public function destroy(Request $request, Review $review): JsonResponse
    {
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(['message' => 'Review deleted.']);
    }

    public function markHelpful(Request $request, Review $review): JsonResponse
    {
        $review->increment('helpful_votes');
        return response()->json(['helpful_votes' => $review->fresh()->helpful_votes]);
    }
}
