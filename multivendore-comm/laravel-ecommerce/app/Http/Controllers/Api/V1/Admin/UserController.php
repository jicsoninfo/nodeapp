<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('profile')->when($request->role, fn ($q) => $q->role($request->role))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('email', 'like', "%{$request->search}%"))
            ->latest()->paginate(25);
        return response()->json(['data' => UserResource::collection($users)]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => new UserResource($user->load('profile', 'vendor', 'orders'))]);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate(['status' => 'required|in:active,suspended,banned']);
        $user->update(['status' => $request->status]);
        if ($request->status !== 'active') $user->tokens()->delete();
        return response()->json(['data' => new UserResource($user->fresh()), 'message' => "User status set to {$request->status}."]);
    }

    public function assignRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $user->syncRoles([$request->role]);
        return response()->json(['message' => "Role '{$request->role}' assigned."]);
    }

    public function impersonate(Request $request, User $user): JsonResponse
    {
        abort_if($user->hasRole('admin'), 403, 'Cannot impersonate another admin.');
        $token = $user->createToken('impersonate:' . $request->user()->id)->plainTextToken;
        return response()->json(['token' => $token, 'message' => "Impersonating {$user->email}."]);
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if($user->hasRole('admin'), 403, 'Cannot delete admin accounts.');
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }
}
