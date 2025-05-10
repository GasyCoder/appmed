<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Parcour;
use Livewire\Component;
use App\Models\AuthorizedEmail;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class RegisterForm extends Component
{
    use LivewireAlert;
    public $email;
    public $name = '';
    public $sexe = '';
    public $telephone = '';
    public $password = '';
    public $password_confirmation = '';
    public $niveau_id = '';
    public $parcour_id = '';
    public $terms = false;
    private $authorizedEmail;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'niveau_id' => ['required', 'exists:niveaux,id'],
        'parcour_id' => ['required', 'exists:parcours,id'],
        'sexe' => ['required', 'in:homme,femme'],
        'telephone' => ['required', 'string'],
        'terms' => ['accepted']
    ];

    public function mount($token)
    {
        try {
            $this->authorizedEmail = AuthorizedEmail::where('verification_token', $token)
                ->where('is_registered', false)
                ->where('token_expires_at', '>', now())
                ->firstOrFail();

            $this->email = $this->authorizedEmail->email;
        } catch (\Exception $e) {
            $this->alert('error', 'Le lien d\'inscription est invalide ou a expiré.');
            return redirect()->route('inscription');
        }
    }

    public function registerStudent()
    {
        // Revérifier la validité du token avant l'inscription
        if (!$this->authorizedEmail ||
            $this->authorizedEmail->token_expires_at < now()) {
            $this->alert('error', 'Le lien d\'inscription a expiré. Veuillez recommencer.');
            return redirect()->route('inscription');
        }

        $this->validate($this->rules);

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
            ]);

            $user->profil()->create([
                'sexe' => $this->sexe,
                'telephone' => $this->telephone,
            ]);

            $user->assignRole('student');

            $this->authorizedEmail->update([
                'is_registered' => true,
                'verification_token' => null, // Invalider le token après utilisation
                'token_expires_at' => null
            ]);

            auth()->login($user);

            return redirect()->route('studentEspace');

        } catch (\Exception $e) {
            $this->alert('error', 'Une erreur est survenue lors de l\'inscription.');
        }
    }

    public function render()
    {
        return view('livewire.auth.register-form', [
            'niveaux' => Niveau::where('status', true)->get(),
            'parcours' => Parcour::where('status', true)->get(),
        ])->layout('layouts.guest');
    }
}
