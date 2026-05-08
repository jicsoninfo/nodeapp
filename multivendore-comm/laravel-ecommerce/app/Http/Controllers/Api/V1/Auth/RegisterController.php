<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
                'status'   => 'pending',
            ]);

            UserProfile::create([
                'user_id'    => $user->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'locale'     => $request->locale ?? app()->getLocale(),
                'timezone'   => $request->timezone ?? 'UTC',
            ]);

            $user->assignRole('buyer');

            $user->sendEmailVerificationNotification();

            return $user;
        });

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data'    => new UserResource($user->load('profile')),
            'token'   => $token,
            'message' => 'Registration successful. Please verify your email.',
        ], 201);
    }
}
