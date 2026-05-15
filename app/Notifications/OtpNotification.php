<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp, public ?string $channel = null)
    {
    }

    public function via(object $notifiable): array
    {
        return $this->channel === 'sms' ? ['vonage'] : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de connexion OTP')
            ->greeting('Bonjour,')
            ->line('Voici votre code de connexion :')
            ->line("**{$this->otp}**")
            ->line('Ce code expire dans 10 minutes.')
            ->line('Si vous n’avez pas demandé ce code, ignorez cet e-mail.');
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage())
            ->content("Votre code OTP est {$this->otp}. Il expire dans 10 minutes.");
    }
}
