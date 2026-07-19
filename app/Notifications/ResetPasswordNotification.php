<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $token,
        private readonly string $resetLocale
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function token(): string
    {
        return $this->token;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'locale' => $this->resetLocale,
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject(__('auth.reset_password_mail_subject'))
            ->greeting(__('auth.reset_password_mail_greeting', ['name' => $notifiable->name]))
            ->line(__('auth.reset_password_mail_intro'))
            ->action(__('auth.reset_password_mail_action'), $url)
            ->line(__('auth.reset_password_mail_expire', ['count' => config('auth.passwords.users.expire')]))
            ->line(__('auth.reset_password_mail_outro'));
    }
}
