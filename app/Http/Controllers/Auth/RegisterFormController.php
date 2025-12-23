<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\AuthorizedEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterFormRequest;

class RegisterFormController extends Controller
{
    private function defaultParcour(): Parcour
    {
        // Si vous avez un seul parcours actif, on prend le premier.
        // Vous pouvez aussi remplacer par ->where('slug','medecine') si vous avez un slug.
        $parcour = Parcour::query()
            ->where('status', true)
            ->orderBy('id')
            ->first();

        abort_if(!$parcour, 500, 'Aucun parcours actif n’est configuré.');

        return $parcour;
    }

    public function showRegistrationForm($token)
    {
        try {
            $authorizedEmail = AuthorizedEmail::query()
                ->where('verification_token', $token)
                ->where('is_registered', false)
                ->where('token_expires_at', '>', now())
                ->firstOrFail();

            $defaultParcour = $this->defaultParcour();

            return view('livewire.auth.register-form', [
                'email'          => $authorizedEmail->email,
                'token'          => $token,
                'niveaux'        => Niveau::where('status', true)->get(),
                'defaultParcour' => $defaultParcour, // utile pour affichage "lecture seule"
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
            $authorizedEmail = AuthorizedEmail::query()
                ->where('verification_token', $token)
                ->where('is_registered', false)
                ->where('token_expires_at', '>', now())
                ->firstOrFail();

            $defaultParcour = $this->defaultParcour();

            $user = DB::transaction(function () use ($request, $authorizedEmail, $defaultParcour) {

                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $authorizedEmail->email,
                    'password'  => Hash::make($request->password),
                    'niveau_id' => $request->niveau_id,
                    'parcour_id'=> $defaultParcour->id, // <- imposé ici
                ]);

                $user->profil()->create([
                    'sexe'      => $request->sexe,
                    'telephone' => $request->telephone,
                ]);

                $user->assignRole('student');

                // Invalider le token après utilisation
                $authorizedEmail->update([
                    'is_registered'       => true,
                    'verification_token'  => null,
                    'token_expires_at'    => null,
                ]);

                return $user;
            });

            Auth::login($user);

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
