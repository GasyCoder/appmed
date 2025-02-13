<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class UserAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $temporaryPassword;
    protected $validityInHours = 48;

    public function __construct($token, $temporaryPassword = null)
    {
        $this->token = $token;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    protected function getFormattedName($notifiable)
    {
        $profil = $notifiable->profil;

        if (!$profil) {
            return $notifiable->name;
        }

        if ($notifiable->hasRole('teacher')) {
            return $profil->grade ? "{$profil->grade}. {$notifiable->name}" : $notifiable->name;
        }

        if ($notifiable->hasRole('student')) {
            $prefix = match($profil->sexe) {
                'homme' => 'M.',
                'femme' => 'Mlle/Mme',
                default => ''
            };
            return $prefix ? "{$prefix} {$notifiable->name}" : $notifiable->name;
        }

        return $notifiable->name;
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
            ->subject('Bienvenue sur la plateforme de la Faculté de Médecine')
            ->markdown('emails.users-account-created', [
                'url' => $url,
                'name' => $this->getFormattedName($notifiable),
                'email' => $notifiable->email,
                'temporaryPassword' => $this->temporaryPassword,
                'validityHours' => $this->validityInHours
            ]);
    }
}
