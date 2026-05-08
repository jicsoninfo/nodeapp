<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->canLogin()) {
            return response()->json([
                'message' => "Account is {$user->status->value}. Please contact support.",
            ], 403);
        }

        $token = $user->createToken(
            $request->device_name ?? 'api',
            $user->getRoleNames()->map(fn ($r) => "role:{$r}")->toArray()
        )->plainTextToken;

        return response()->json([
            'data'  => new UserResource($user->load('profile')),
            'token' => $token,
        ]);
    }
}
