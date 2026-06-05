<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp, public ?string $channel = null)
    {
    }

    public function via(object $notifiable): array
    {
        // Currently supporting mail channel only
        // To add SMS support, install: composer require vonage/laravel-notification-channel
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de connexion OTP')
            ->greeting('Bonjour,')
            ->line('Voici votre code de connexion :')
            ->line("**{$this->otp}**")
            ->line('Ce code expire dans 10 minutes.')
            ->line('Si vous n\'avez pas demande ce code, ignorez cet e-mail.');
    }
}
