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

        // Récupérer le grade et le nom
        $grade = optional($notifiable->profil)->grade;
        $nameWithGrade = $grade ? "{$grade}. {$notifiable->name}" : $notifiable->name;

        return (new MailMessage)
            ->subject('Bienvenue sur la plateforme de la faculté de Médecine')
            ->markdown('emails.teacher-account-created', [
                'url' => $url,
                'name' => $nameWithGrade,
                'email' => $notifiable->email,
                'temporaryPassword' => $this->temporaryPassword,
                'validityHours' => $this->validityInHours
            ]);
    }
}
