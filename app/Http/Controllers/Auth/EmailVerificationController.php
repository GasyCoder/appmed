<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AuthorizedEmail;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    public function index()
    {
        return view('livewire.auth.email-verification');
    }

    public function verifyEmailStudent(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255']
        ]);

        $authorizedEmail = AuthorizedEmail::where('email', $request->email)->first();

        if (!$authorizedEmail) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Cette adresse email n\'est pas autorisée.']);
        }

        if ($authorizedEmail->is_registered) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Un compte existe déjà avec cette adresse email.']);
        }

        // Mettre à jour avec un nouveau token
        $authorizedEmail->update([
            'verification_token' => Str::uuid(),
            'token_expires_at' => now()->addHours(2)
        ]);

        // Rediriger avec le token
        return redirect()->route('register.form', ['token' => $authorizedEmail->verification_token]);
    }
}
