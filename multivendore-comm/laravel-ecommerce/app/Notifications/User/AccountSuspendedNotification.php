<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSuspendedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(public readonly string $reason = '') {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your account has been suspended')
            ->greeting("Hi {$notifiable->full_name},")
            ->line('Your marketplace account has been temporarily suspended.');

        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }

        return $mail
            ->action('Contact Support', url('/support'))
            ->line('If you believe this is a mistake, please reach out to our support team.')
            ->salutation('Marketplace Trust & Safety');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'account_suspended',
            'channel' => 'in_app',
            'title'   => 'Account suspended',
            'body'    => $this->reason ?: 'Your account has been suspended. Contact support for details.',
        ];
    }
}
