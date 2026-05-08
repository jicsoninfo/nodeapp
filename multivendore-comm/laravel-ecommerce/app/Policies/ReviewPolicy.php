<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(['admin', 'moderator'])) return true;
        return null;
    }

    public function create(User $user): Response|bool
    {
        return $user->hasVerifiedEmail()
            ? true
            : Response::deny('You must verify your email to leave reviews.');
    }

    public function update(User $user, Review $review): Response|bool
    {
        if ($review->user_id !== $user->id) {
            return Response::deny('You can only edit your own reviews.');
        }

        if ($review->status === 'approved') {
            return Response::deny('Approved reviews cannot be edited.');
        }

        return true;
    }

    public function delete(User $user, Review $review): Response|bool
    {
        return $review->user_id === $user->id
            ? true
            : Response::deny('You can only delete your own reviews.');
    }

    public function moderate(User $user, Review $review): Response|bool
    {
        return Response::deny('Only admins and moderators can moderate reviews.');
    }
}
