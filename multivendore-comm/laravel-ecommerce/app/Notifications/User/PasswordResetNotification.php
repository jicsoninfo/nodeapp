<?php

namespace App\Notifications\User;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Branded password reset notification.
 */
class PasswordResetNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hi there,')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('The Marketplace Team');
    }
}
