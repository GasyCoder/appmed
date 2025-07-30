<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function serve($id)
    {
        $document = Document::findOrFail($id);

        // Vérifier l'accès (basé sur la méthode canAccess du modèle)
        if (!$document->canAccess(Auth::user())) {
            abort(403, 'Interdit');
        }

        // Vérifier l'existence du fichier
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        // Vérifier la lisibilité
        $filePath = Storage::disk('public')->path($document->file_path);
        if (!is_readable($filePath)) {
            abort(500, 'Fichier non lisible');
        }

        // Enregistrer une vue
        $document->registerView();

        // Servir le fichier
        return response()->file($filePath, [
            'Content-Type' => $document->file_type,
            'Content-Disposition' => 'inline; filename="' . $document->getDisplayFilename() . '"',
        ]);
    }
}