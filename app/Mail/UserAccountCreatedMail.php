<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class UserAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $token,
        public ?string $temporaryPassword = null,
        public int $validityHours = 48,
        public ?string $sexe = null, // homme|femme|null
        public string $appName = 'EPIRC',
        public string $orgName = 'Faculté de Médecine — Université de Mahajanga',
    ) {}

    public function build(): self
    {
        $url = URL::temporarySignedRoute(
            'password.set',
            Carbon::now()->addHours($this->validityHours),
            ['token' => $this->token, 'email' => $this->email]
        );

        return $this
            ->subject('Création de compte EPIRC – Faculté de Médecine (UMG)')
            ->view('emails.users-account-created', [
                'appName' => $this->appName,
                'orgName' => $this->orgName,
                'name' => $this->name,
                'email' => $this->email,
                'temporaryPassword' => $this->temporaryPassword,
                'url' => $url,
                'validityHours' => $this->validityHours,
                'sexe' => $this->sexe,
            ]);
    }
}
