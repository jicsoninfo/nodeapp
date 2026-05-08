<?php

namespace App\Notifications\User;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Overrides Laravel's default VerifyEmail notification with branded styling.
 */
class EmailVerificationNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->greeting('Almost there!')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $url)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('The Marketplace Team');
    }
}
