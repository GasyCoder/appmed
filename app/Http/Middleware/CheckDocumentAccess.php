<?php

namespace App\Http\Middleware;

use App\Models\Document;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CheckDocumentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Non connecté
        if (!$user) {
            abort(403);
        }

        // IMPORTANT:
        // Si la route n'a pas de {document}, on ne bloque pas ici.
        // Le contrôle d'accès des pages est fait par middleware role:teacher/student/admin.
        $document = $request->route('document');
        if (!$document instanceof Document) {
            return $next($request);
        }

        // Admin : full access
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Teacher : autorisé si c'est lui qui a uploadé
        // (optionnel: ou si le document est dans un niveau qu'il enseigne)
        if ($user->hasRole('teacher')) {
            $ok = ((int) $document->uploaded_by === (int) $user->id);

            // Optionnel si tu veux aussi autoriser par niveau
            if (!$ok && method_exists($user, 'teacherNiveaux')) {
                $niveauIds = $user->teacherNiveaux->pluck('id')->all(); // teacherNiveaux => models Niveau
                $ok = in_array((int) $document->niveau_id, array_map('intval', $niveauIds), true);
            }

            abort_unless($ok, 403);
            return $next($request);
        }

        // Student : doc actif + même niveau (+ même parcours si la colonne existe)
        if ($user->hasRole('student')) {
            abort_unless((bool) $document->is_actif, 403);
            abort_unless((int) $document->niveau_id === (int) $user->niveau_id, 403);

            if (Schema::hasColumn('documents', 'parcour_id') && !empty($user->parcour_id)) {
                abort_unless((int) $document->parcour_id === (int) $user->parcour_id, 403);
            }

            return $next($request);
        }

        abort(403);
    }
}
