<?php

namespace App\Http\Middleware;

use App\Models\Document;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDocumentAccess
{
    /**
     * Middleware pour vérifier l'accès aux documents
     * 
     * ✅ Gère à la fois les IDs (string) et les objets Document
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ CORRECTION : Récupérer le document depuis la route
        $document = $request->route('document');

        // ✅ Si c'est une string/int (ID), charger le Document
        if (is_string($document) || is_numeric($document)) {
            $document = Document::find($document);
        }

        // ✅ Vérifier que le document existe
        if ($document instanceof Document) {
            if (!$document->canRead($user)) {
                abort(403, 'Accès non autorisé.');
            }
        }

        // ✅ Vérifier les permissions
        if (!$document->canAccess($user)) {
            abort(403, "Vous n'avez pas accès à ce document");
        }

        return $next($request);
    }
}