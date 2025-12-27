<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Création de compte</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
    <tr>
        <td align="center" style="padding:0 12px;">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="width:600px;max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 10px 30px rgba(17,24,39,.08);">

                <tr>
                    <td style="background:#4f46e5;height:6px;line-height:6px;font-size:0;">&nbsp;</td>
                </tr>

                <tr>
                    <td style="padding:20px 24px 0 24px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:700;color:#111827;">
                            {{ $appName ?? config('app.name') }}
                        </div>
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;margin-top:6px;">
                            {{ $orgName ?? '' }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 24px 8px 24px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#111827;">
                            Bonjour <strong>{{ $name }}</strong>,
                        </div>

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#374151;margin-top:10px;">
                            Votre compte a été créé. Pour finaliser l’accès, veuillez définir votre mot de passe.
                        </div>

                        @if(!empty($temporaryPassword))
                            <div style="margin-top:16px;border:1px solid #e5e7eb;background:#f9fafb;border-radius:12px;padding:14px 16px;">
                                <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:700;color:#111827;margin-bottom:10px;">
                                    Identifiants
                                </div>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;padding:6px 0;width:42%;">
                                            Email
                                        </td>
                                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#111827;padding:6px 0;">
                                            {{ $email }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;padding:6px 0;">
                                            Mot de passe temporaire
                                        </td>
                                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#111827;padding:6px 0;">
                                            <strong>{{ $temporaryPassword }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:18px;">
                            <tr>
                                <td bgcolor="#4f46e5" style="border-radius:10px;">
                                    <a href="{{ $url }}"
                                       style="display:inline-block;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:10px;">
                                        Définir mon mot de passe
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#6b7280;margin-top:12px;">
                            ⏱️ Lien valable <strong style="color:#111827;">{{ $validityHours }} heures</strong>.
                        </div>

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#6b7280;margin-top:10px;">
                            Si le bouton ne fonctionne pas, copiez-collez ce lien :
                            <div style="word-break:break-all;color:#111827;margin-top:6px;">
                                {{ $url }}
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px;">
                        <div style="height:1px;background:#e5e7eb;margin:18px 0;"></div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px 22px 24px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.7;color:#6b7280;">
                            Si vous n’êtes pas à l’origine de cette action, ignorez simplement ce message.
                        </div>

                        {{-- INFO BAS EMAIL (Genre ici) --}}
                        @php
                            $gender = null;
                            if (!empty($sexe)) {
                                $gender = $sexe === 'homme' ? 'Homme' : ($sexe === 'femme' ? 'Femme' : null);
                            }
                        @endphp

                        @if($gender)
                            <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6b7280;margin-top:10px;">
                                Genre : <strong style="color:#111827;">{{ $gender }}</strong>
                            </div>
                        @endif

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#374151;margin-top:14px;">
                            Cordialement,<br>
                            <strong style="color:#111827;">Équipe {{ $appName ?? config('app.name') }}</strong>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#9ca3af;margin-top:14px;">
                © {{ date('Y') }} {{ $appName ?? config('app.name') }}
            </div>

        </td>
    </tr>
</table>
</body>
</html>
