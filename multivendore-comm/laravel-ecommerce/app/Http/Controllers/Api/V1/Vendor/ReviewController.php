<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $reviews  = Review::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->with(['user.profile', 'product.translations'])->latest()->paginate(20);
        return response()->json(['data' => $reviews]);
    }

    public function reply(Request $request, Review $review): JsonResponse
    {
        abort_if($review->product->vendor_id !== $request->user()->vendor->id, 403);
        $request->validate(['reply' => 'required|string|max:1000']);
        $review->update(['vendor_reply' => $request->reply, 'vendor_replied_at' => now()]);
        return response()->json(['message' => 'Reply posted.']);
    }
}
