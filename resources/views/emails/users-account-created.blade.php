{{-- users-account-created.blade.php --}}
@component('mail::message')
{{-- En-tête --}}
<div style="background: linear-gradient(135deg, #4338ca 0%, #6d28d9 50%, #4338ca 100%); padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 40px;">
    <h1 style="font-size: 28px; font-weight: 800; color: white; text-align: center; margin-bottom: 12px; letter-spacing: -0.025em;">
        Bienvenue sur la plateforme
    </h1>
    <p style="font-size: 20px; color: rgba(255, 255, 255, 0.9); text-align: center; font-weight: 500;">
        Faculté de Médecine - Université de Mahajanga
    </p>
</div>
{{-- Salutation --}}
Bonjour **{{ $name }}**,
votre compte a été créé avec succès.

{{-- Informations de connexion --}}
@if($temporaryPassword)
@component('mail::panel')
### Vos identifiants de connexion :
- **Email :** {{ $email }}
- **Mot de passe temporaire :** {{ $temporaryPassword }}
@endcomponent
@endif

Pour des raisons de sécurité, veuillez créer votre propre mot de passe en cliquant sur le bouton ci-dessous.

@component('mail::button', ['url' => $url, 'color' => 'blue'])
🔒 Créer mon mot de passe
@endcomponent

@component('mail::panel')
⚠️ **Attention :** ce lien est valable pendant {{ $validityHours }} heures uniquement.
@endcomponent

Si vous n'avez pas demandé la création de ce compte, aucune action n'est requise de votre part.

Cordialement,<br>
L'équipe de la Faculté de Médecine - Université de Mahajanga.
@endcomponent
