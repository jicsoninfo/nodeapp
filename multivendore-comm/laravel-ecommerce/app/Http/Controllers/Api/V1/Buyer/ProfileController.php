<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => new UserResource($request->user()->load('profile', 'addresses'))]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'   => 'sometimes|string|max:100',
            'last_name'    => 'sometimes|string|max:100',
            'locale'       => 'sometimes|string|max:5',
            'timezone'     => 'sometimes|timezone',
            'date_of_birth'=> 'sometimes|date|before:today',
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json(['data' => new UserResource($request->user()->fresh('profile'))]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate(['avatar' => 'required|image|max:2048']);
        $path = $request->file('avatar')->store("avatars/{$request->user()->id}", 's3');
        $request->user()->profile()->update(['avatar_url' => $path]);
        return response()->json(['avatar_url' => $path]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);
        $request->user()->tokens()->delete();
        $request->user()->delete();
        return response()->json(['message' => 'Account deleted.']);
    }
}
