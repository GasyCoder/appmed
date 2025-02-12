@component('mail::message')
{{-- En-tête --}}
# Bienvenue sur la plateforme de la Faculté de Médecine - Université de Mahajanga

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

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Créer mon mot de passe
@endcomponent

@component('mail::panel')
⚠️ **Attention :** ce lien est valable pendant {{ $validityHours }} heures uniquement.
@endcomponent

Si vous n'avez pas demandé la création de ce compte, aucune action n'est requise de votre part.

Cordialement,<br>
L'équipe de la Faculté de Médecine - Université de Mahajanga.
@endcomponent
