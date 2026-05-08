<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reviews = Review::with(['user.profile','product.translations'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(25);
        return response()->json(['data' => $reviews]);
    }

    public function approve(Review $review): JsonResponse
    {
        $review->update(['status' => 'approved']);
        return response()->json(['message' => 'Review approved.']);
    }

    public function reject(Review $review): JsonResponse
    {
        $review->update(['status' => 'rejected']);
        return response()->json(['message' => 'Review rejected.']);
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();
        return response()->json(['message' => 'Review deleted.']);
    }
}
