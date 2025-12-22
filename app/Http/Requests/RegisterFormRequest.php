<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'niveau_id' => ['required', 'exists:niveaux,id'],
            'parcour_id' => ['required', 'exists:parcours,id'],
            'sexe' => ['required', 'in:homme,femme'],
            'telephone' => ['required', 'string'],
            'terms' => ['accepted']
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
            'parcour_id.required' => 'Le parcours est requis.',
            'sexe.required' => 'Le sexe est requis.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.'
        ];
    }
}
