<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class TeacherAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $validityInHours = 48;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'password.set',
            Carbon::now()->addHours($this->validityInHours),
            [
                'token' => $this->token,
                'email' => $notifiable->email
            ]
        );

        return (new MailMessage)
                ->subject('Bienvenue sur la plateforme de la faculté de Médecine')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Bienvenue sur la plateforme de la faculté de Médecine.')
                ->line('Votre compte a été activé. Veuillez créer votre propre mot de passe en cliquant sur le bouton ci-dessous.')
                ->action('Créer mon mot de passe', $url)
                ->line('Attention : ce lien est valable pendant 48 heures uniquement.')
                ->line('Si vous n\'avez pas demandé la création de ce compte, aucune action n\'est requise de votre part.');
    }
}