<?php
namespace App\Listeners\User;
use App\Events\User\UserRegistered;
use App\Notifications\User\WelcomeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmail implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(UserRegistered $event): void
    {
        $event->user->notify(new WelcomeNotification());
    }
}
