<?php
namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    private const SUPPORTED = ['google', 'facebook', 'apple'];

    public function handleCallback(Request $request, string $provider): JsonResponse
    {
        abort_unless(in_array($provider, self::SUPPORTED), 422, "Provider {$provider} is not supported.");

        $request->validate(['access_token' => 'required|string']);

        // Verify token with provider and extract user info
        $socialUser = $this->verifyToken($provider, $request->access_token);

        $user = DB::transaction(function () use ($socialUser, $provider) {
            $user = User::where('email', $socialUser['email'])->first();

            if (! $user) {
                $user = User::create([
                    'email'             => $socialUser['email'],
                    'password'          => bcrypt(Str::random(32)),
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]);
                UserProfile::create([
                    'user_id'    => $user->id,
                    'first_name' => $socialUser['first_name'] ?? null,
                    'last_name'  => $socialUser['last_name'] ?? null,
                    'avatar_url' => $socialUser['avatar'] ?? null,
                    'locale'     => 'en',
                    'timezone'   => 'UTC',
                ]);
                $user->assignRole('buyer');
            }

            return $user;
        });

        $token = $user->createToken("social:{$provider}")->plainTextToken;

        return response()->json([
            'data'  => new UserResource($user->load('profile')),
            'token' => $token,
        ]);
    }

    private function verifyToken(string $provider, string $token): array
    {
        // Stub — integrate with Google/Facebook/Apple SDK per provider
        return ['email' => 'social@example.com', 'first_name' => 'Social', 'last_name' => 'User', 'avatar' => null];
    }
}
