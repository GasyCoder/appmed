<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Réinitialisation du mot de passe</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
    <tr>
        <td align="center" style="padding:0 12px;">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="width:600px;max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(17,24,39,.08);">

                {{-- Accent bar --}}
                <tr>
                    <td style="background:#111827;height:6px;line-height:6px;font-size:0;">&nbsp;</td>
                </tr>

                {{-- Header --}}
                <tr>
                    <td style="padding:22px 24px 10px 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;">
                                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:800;color:#111827;">
                                        {{ $appName ?? config('app.name') }}
                                    </div>
                                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;margin-top:6px;">
                                        {{ $orgName ?? '' }}
                                    </div>
                                </td>
                                <td style="vertical-align:middle;text-align:right;">
                                    @if(!empty($logoUrl))
                                        <img src="{{ $logoUrl }}" alt="Logo" width="56" height="56"
                                             style="display:inline-block;object-fit:contain;border-radius:12px;">
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Content --}}
                <tr>
                    <td style="padding:10px 24px 8px 24px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:800;color:#111827;line-height:1.25;">
                            Réinitialisation du mot de passe
                        </div>

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#374151;margin-top:12px;">
                            Bonjour <strong style="color:#111827;">{{ $name ?? 'Utilisateur' }}</strong>,
                        </div>

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#374151;margin-top:10px;">
                            Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour continuer.
                        </div>

                        {{-- CTA Button --}}
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:18px;">
                            <tr>
                                <td bgcolor="#111827" style="border-radius:12px;">
                                    <a href="{{ $url }}"
                                       style="display:inline-block;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:800;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:12px;">
                                        Réinitialiser mon mot de passe
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Validity --}}
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#6b7280;margin-top:12px;">
                            ⏱️ Ce lien est valable <strong style="color:#111827;">{{ $validityHours ?? 1 }} heure(s)</strong>.
                        </div>

                        {{-- Fallback link --}}
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#6b7280;margin-top:10px;">
                            Si le bouton ne fonctionne pas, copiez-collez ce lien :
                            <div style="word-break:break-all;color:#111827;margin-top:6px;">
                                {{ $url }}
                            </div>
                        </div>
                    </td>
                </tr>

                {{-- Divider --}}
                <tr>
                    <td style="padding:0 24px;">
                        <div style="height:1px;background:#e5e7eb;margin:18px 0;"></div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:0 24px 22px 24px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.7;color:#6b7280;">
                            Si vous n’êtes pas à l’origine de cette demande, ignorez simplement ce message.
                        </div>

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
