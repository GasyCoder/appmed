<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function serve(Document $document)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        // Sécurité d'accès (ton modèle a déjà canAccess)
        abort_unless($document->canAccess($user), 403);

        // ✅ Compter la vue UNIQUEMENT pour les étudiants (unique par étudiant grâce à document_views + registerView())
        if ($user->hasRole('student')) {
            $document->registerView(); // incrémente view_count seulement si 1ère vue de cet étudiant
        }

        // Vérifier que le fichier existe
        abort_unless($document->fileExists(), 404);

        $absolutePath = Storage::disk('public')->path($document->file_path);
        $mime = $document->file_type ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$document->getDisplayFilename().'"',
        ]);
    }
}
