<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'telephone' => ['required', 'string', 'min:8', 'max:30'],
            'sexe' => ['required', 'in:homme,femme'],
            'niveau_id' => ['required', 'exists:niveaux,id'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est requis.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'niveau_id.required' => 'Le niveau est requis.',
            'sexe.required' => 'Le sexe est requis.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.'
        ];
    }
}
