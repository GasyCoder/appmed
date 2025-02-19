<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\AuthorizedEmail;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterFormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterFormController extends Controller
{
    public function showRegistrationForm($token)
    {
        try {
            $authorizedEmail = AuthorizedEmail::where('verification_token', $token)
                ->where('is_registered', false)
                ->where('token_expires_at', '>', now())
                ->firstOrFail();

            return view('livewire.auth.register-form', [
                'email' => $authorizedEmail->email,
                'token' => $token,
                'niveaux' => Niveau::where('status', true)->get(),
                'parcours' => Parcour::where('status', true)->get(),
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('inscription')
                ->with('error', 'Le lien d\'inscription est invalide ou a expiré.');
        }
    }

    public function register(RegisterFormRequest $request, $token)
    {
        try {
            $authorizedEmail = AuthorizedEmail::where('verification_token', $token)
                ->where('is_registered', false)
                ->where('token_expires_at', '>', now())
                ->firstOrFail();

            $user = User::create([
                'name' => $request->name,
                'email' => $authorizedEmail->email,
                'password' => Hash::make($request->password),
                'niveau_id' => $request->niveau_id,
                'parcour_id' => $request->parcour_id,
            ]);

            $user->profil()->create([
                'sexe' => $request->sexe,
                'telephone' => $request->telephone,
            ]);

            $user->assignRole('student');

            // Invalider le token après utilisation
            $authorizedEmail->update([
                'is_registered' => true,
                'verification_token' => null,
                'token_expires_at' => null
            ]);

            auth()->login($user);

            return redirect()
                ->route('studentEspace')
                ->with('success', 'Inscription réussie !');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'inscription.')
                ->withInput();
        }
    }
}
