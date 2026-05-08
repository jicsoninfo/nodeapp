<?php
namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token' => $token]);
    }
}
