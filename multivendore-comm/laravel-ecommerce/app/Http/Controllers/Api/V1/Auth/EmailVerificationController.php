<?php
namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent.']);
    }

    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }
        $request->fulfill();
        event(new Verified($request->user()));
        return response()->json(['message' => 'Email verified successfully.']);
    }
}
