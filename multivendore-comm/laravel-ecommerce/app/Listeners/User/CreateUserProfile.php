<?php
namespace App\Listeners\User;
use App\Events\User\UserRegistered;
use App\Models\UserProfile;

class CreateUserProfile
{
    public function handle(UserRegistered $event): void
    {
        UserProfile::firstOrCreate(
            ['user_id' => $event->user->id],
            ['locale' => app()->getLocale(), 'timezone' => 'UTC']
        );
    }
}
