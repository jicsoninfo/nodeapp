<?php
namespace App\Listeners\User;
use App\Events\User\UserEmailVerified;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendEmailVerifiedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(UserEmailVerified|Verified $event): void
    {
        Log::info('User email verified', ['user_id' => $event->user->id ?? null]);
    }
}
