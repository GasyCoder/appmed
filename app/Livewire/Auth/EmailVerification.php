<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\AuthorizedEmail;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EmailVerification extends Component
{
    use LivewireAlert;
    public $email = '';

    protected $rules = [
        'email' => ['required', 'email', 'max:255']
    ];

    public function verifyEmailStudent()
    {
        $this->validate($this->rules);

        $authorizedEmail = AuthorizedEmail::where('email', $this->email)->first();

        if (!$authorizedEmail) {
            $this->addError('email', 'Cette adresse email n\'est pas autorisée.');
            return false;
        }

        if ($authorizedEmail->is_registered) {
            $this->addError('email', 'Un compte existe déjà avec cette adresse email.');
            return false;
        }

        // Mettre à jour avec le nouveau token
        $authorizedEmail->update([
            'verification_token' => Str::uuid(),
            'token_expires_at' => now()->addHours(2)
        ]);

        return $this->redirect(route('register.form', ['token' => $authorizedEmail->verification_token]));
    }

    public function render()
    {
        return view('livewire.auth.email-verification')->layout('layouts.guest');
    }
}
