<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to the Marketplace! 🎉')
            ->greeting("Welcome, {$notifiable->full_name}!")
            ->line('We are thrilled to have you on board.')
            ->line('Here is what you can do right now:')
            ->line('🛍️ Browse thousands of products from verified vendors')
            ->line('❤️ Save your favorites to wishlists')
            ->line('⚡ Get fast delivery across the globe')
            ->action('Start Shopping', url('/'))
            ->line('Use code **NEWUSER** for an instant discount on your first order.')
            ->salutation('Happy Shopping! — The Marketplace Team');
    }
}
