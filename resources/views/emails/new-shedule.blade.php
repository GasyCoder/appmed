<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau cours programmé</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; background-color: #f3f4f6; color: #1f2937;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-top: 40px; margin-bottom: 40px;">
        {{-- En-tête --}}
        <div style="background: linear-gradient(135deg, #4338ca 0%, #6d28d9 50%, #4338ca 100%); padding: 32px; border-radius: 12px 12px 0 0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h1 style="font-size: 28px; font-weight: 800; color: white; text-align: center; margin-bottom: 12px; letter-spacing: -0.025em;">
                Nouveau Emploi du temps
            </h1>
            <p style="font-size: 20px; color: rgba(255, 255, 255, 0.9); text-align: center; font-weight: 500; margin: 0;">
                Faculté de Médecine - Université de Mahajanga
            </p>
        </div>

        {{-- Contenu principal --}}
        <div style="padding: 32px;">
            <p style="font-size: 16px; color: #4b5563; margin-bottom: 24px;">
                Bonjour {{ $teacher->getFullNameWithGradeAttribute() }},
            </p>

            <p style="font-size: 16px; color: #4b5563; margin-bottom: 24px;">
                Un nouveau cours a été programmé pour vous. Voici les détails :
            </p>

            {{-- Carte des détails du cours --}}
            <div style="background-color: #f8fafc; border-radius: 8px; padding: 24px; margin-bottom: 32px; border: 1px solid #e2e8f0;">
                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">📅</span>
                    <strong style="color: #1f2937;">Période :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">
                        Du {{ Carbon\Carbon::parse($lesson->start_date)->format('d/m/Y') }}
                        au {{ Carbon\Carbon::parse($lesson->end_date)->format('d/m/Y') }}
                    </span>
                </div>
                <div style="margin-bottom: 16px; display: flex;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">📅</span>
                    <strong style="color: #1f2937;">Jour :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $weekDay }}</span>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">⏰</span>
                    <strong style="color: #1f2937;">Horaire :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $startTime }} - {{ $endTime }}</span>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">📍</span>
                    <strong style="color: #1f2937;">Salle :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $lesson->salle }}</span>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">🎓</span>
                    <strong style="color: #1f2937;">Niveau :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $niveauName }}</span>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">📚</span>
                    <strong style="color: #1f2937;">Parcours :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $parcourName }}</span>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">📖</span>
                    <strong style="color: #1f2937;">UE :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $ue->name }}</span>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">📝</span>
                    <strong style="color: #1f2937;">EC :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $programme->name }}</span>
                </div>

                <div>
                    <span style="font-size: 16px; color: #4b5563; margin-right: 8px;">🔍</span>
                    <strong style="color: #1f2937;">Type de cours :</strong>
                    <span style="margin-left: 8px; color: #4b5563;">{{ $lesson->type_cours }}</span>
                </div>
            </div>

            {{-- Bouton d'action --}}
            <div style="text-align: center; margin-top: 32px;">
                <a href="{{ $url }}" style="display: inline-block; background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%); color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    Voir mon emploi du temps
                </a>
            </div>

            {{-- Pied de mail --}}
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="font-size: 14px; color: #6b7280;">
                    Ceci est un message automatique, merci de ne pas y répondre.
                </p>
                <p style="font-size: 14px; color: #6b7280;">
                    © {{ date('Y') }} Faculté de Médecine - Université de Mahajanga. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
