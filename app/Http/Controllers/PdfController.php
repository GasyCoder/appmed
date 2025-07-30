<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PdfController extends Controller
{
    public function show($filename)
    {
        try {
            // 1. Vérification d'authentification
            if (!Auth::check()) {
                return response()->json(['error' => 'Non autorisé'], 401);
            }

            // 2. Décoder le nom de fichier (gestion des %20, etc.)
            $decodedFilename = urldecode($filename);
            
            // 3. Recherche robuste du document
            $document = Document::where('file_path', 'documents/' . $decodedFilename)
                              ->orWhere('file_path', $decodedFilename)
                              ->orWhere('file_path', 'like', '%' . basename($decodedFilename))
                              ->first();

            if (!$document) {
                Log::warning("Document non trouvé: $decodedFilename");
                return response()->json(['error' => 'Document non trouvé'], 404);
            }

            // 4. Vérification des permissions (si la méthode existe)
            if (method_exists($document, 'canAccess') && !$document->canAccess(Auth::user())) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }

            // 5. Chercher le fichier dans plusieurs emplacements possibles
            $possiblePaths = [
                storage_path('app/public/documents/' . $decodedFilename),
                storage_path('app/public/documents/' . basename($document->file_path)),
                storage_path('app/public/' . $document->file_path),
                public_path('storage/documents/' . $decodedFilename),
                public_path('storage/documents/' . basename($document->file_path))
            ];

            $filePath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }

            if (!$filePath) {
                Log::error("Fichier physique manquant: $decodedFilename. Chemins testés: " . implode(', ', $possiblePaths));
                return response()->json(['error' => 'Fichier introuvable sur le serveur'], 404);
            }

            // 6. Vérification de la taille (max 50MB)
            $fileSize = filesize($filePath);
            if ($fileSize > 50 * 1024 * 1024) {
                return response()->json(['error' => 'Fichier trop volumineux'], 413);
            }

            // 7. Vérification du type MIME
            $mimeType = mime_content_type($filePath);
            $allowedTypes = [
                'application/pdf', 
                'application/vnd.ms-powerpoint', 
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ];
            
            if (!in_array($mimeType, $allowedTypes)) {
                return response()->json(['error' => 'Type de fichier non supporté: ' . $mimeType], 415);
            }

            // 8. Incrémenter les vues
            $this->incrementView($document);

            // 9. Informations enseignant
            $teacherInfo = $this->getTeacherInfo($document);

            // 10. Créer une URL sécurisée pour le fichier
            $fileUrl = asset('storage/documents/' . urlencode(basename($document->file_path)));
            
            return response()->json([
                'file_url' => $fileUrl,
                'filename' => basename($document->file_path),
                'mime' => $mimeType,
                'size' => $fileSize,
                'teacherInfo' => $teacherInfo,
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur chargement $filename: " . $e->getMessage());
            return response()->json([
                'error' => 'Erreur serveur',
                'message' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Visualiseur PDF uniquement
     */
    public function viewerPdf($filename)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Connexion requise');
            }

            $decodedFilename = urldecode($filename);
            
            // Recherche du document
            $document = Document::where('file_path', 'documents/' . $decodedFilename)
                            ->orWhere('file_path', $decodedFilename)
                            ->orWhere('file_path', 'like', '%' . basename($decodedFilename))
                            ->first();

            if (!$document) {
                return view('pdf.error', [
                    'error' => 'Document non trouvé',
                    'filename' => $decodedFilename
                ]);
            }

            // Vérification que c'est bien un PDF
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($extension !== 'pdf') {
                return redirect()->route('document.viewer', ['filename' => urlencode($filename)])
                    ->with('error', 'Ce fichier n\'est pas un PDF');
            }

            // Vérification des permissions
            if (method_exists($document, 'canAccess') && !$document->canAccess(Auth::user())) {
                return view('pdf.error', [
                    'error' => 'Accès non autorisé',
                    'filename' => $decodedFilename
                ]);
            }

            // Chercher le fichier physique
            $filePath = $this->findFilePath($document, $decodedFilename);
            if (!$filePath) {
                return view('pdf.error', [
                    'error' => 'Fichier introuvable sur le serveur',
                    'filename' => $decodedFilename
                ]);
            }

            // Incrémenter les vues
            $this->incrementView($document);

            $teacherInfo = $this->getTeacherInfo($document);

            // Vue spécifique pour PDF avec PDF.js
            return view('pdf.viewer-embedded', [
                'filename' => basename($document->file_path),
                'teacherInfo' => $teacherInfo,
                'document' => $document
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur PDF viewer $filename: " . $e->getMessage());
            return view('pdf.error', [
                'error' => 'Erreur serveur: ' . $e->getMessage(),
                'filename' => $decodedFilename ?? $filename
            ]);
        }
    }

    /**
     * Visualiseur PowerPoint uniquement
     */
    public function viewerPpt($filename)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Connexion requise');
            }

            $decodedFilename = urldecode($filename);
            
            // Recherche du document
            $document = Document::where('file_path', 'documents/' . $decodedFilename)
                            ->orWhere('file_path', $decodedFilename)
                            ->orWhere('file_path', 'like', '%' . basename($decodedFilename))
                            ->first();

            if (!$document) {
                return view('pdf.error', [
                    'error' => 'Document non trouvé',
                    'filename' => $decodedFilename
                ]);
            }

            // Vérification que c'est bien un PowerPoint
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($extension, ['ppt', 'pptx'])) {
                return redirect()->route('document.viewer', ['filename' => urlencode($filename)])
                    ->with('error', 'Ce fichier n\'est pas une présentation PowerPoint');
            }

            // Vérification des permissions
            if (method_exists($document, 'canAccess') && !$document->canAccess(Auth::user())) {
                return view('pdf.error', [
                    'error' => 'Accès non autorisé',
                    'filename' => $decodedFilename
                ]);
            }

            // Chercher le fichier physique
            $filePath = $this->findFilePath($document, $decodedFilename);
            if (!$filePath) {
                return view('pdf.error', [
                    'error' => 'Fichier introuvable sur le serveur',
                    'filename' => $decodedFilename
                ]);
            }

            // Incrémenter les vues
            $this->incrementView($document);

            $teacherInfo = $this->getTeacherInfo($document);

            // Vue spécifique pour PowerPoint
            return view('pdf.powerpoint-viewer', [
                'filename' => basename($document->file_path),
                'teacherInfo' => $teacherInfo,
                'document' => $document
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur PowerPoint viewer $filename: " . $e->getMessage());
            return view('pdf.error', [
                'error' => 'Erreur serveur: ' . $e->getMessage(),
                'filename' => $decodedFilename ?? $filename
            ]);
        }
    }

    /**
     * Visualiseur générique pour autres types de documents
     */
    public function viewerDocument($filename)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Connexion requise');
            }

            $decodedFilename = urldecode($filename);
            
            // Recherche du document
            $document = Document::where('file_path', 'documents/' . $decodedFilename)
                            ->orWhere('file_path', $decodedFilename)
                            ->orWhere('file_path', 'like', '%' . basename($decodedFilename))
                            ->first();

            if (!$document) {
                return view('pdf.error', [
                    'error' => 'Document non trouvé',
                    'filename' => $decodedFilename
                ]);
            }

            // Vérification des permissions
            if (method_exists($document, 'canAccess') && !$document->canAccess(Auth::user())) {
                return view('pdf.error', [
                    'error' => 'Accès non autorisé',
                    'filename' => $decodedFilename
                ]);
            }

            // Chercher le fichier physique
            $filePath = $this->findFilePath($document, $decodedFilename);
            if (!$filePath) {
                return view('pdf.error', [
                    'error' => 'Fichier introuvable sur le serveur',
                    'filename' => $decodedFilename
                ]);
            }

            // Incrémenter les vues
            $this->incrementView($document);

            // Déterminer le type de fichier et la vue appropriée
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $teacherInfo = $this->getTeacherInfo($document);

            switch ($extension) {
                case 'doc':
                case 'docx':
                    return view('pdf.document-viewer', [
                        'filename' => basename($document->file_path),
                        'teacherInfo' => $teacherInfo,
                        'document' => $document,
                        'documentType' => 'Word'
                    ]);
                    
                case 'xls':
                case 'xlsx':
                    return view('pdf.document-viewer', [
                        'filename' => basename($document->file_path),
                        'teacherInfo' => $teacherInfo,
                        'document' => $document,
                        'documentType' => 'Excel'
                    ]);
                    
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'webp':
                    return view('pdf.image-viewer', [
                        'filename' => basename($document->file_path),
                        'teacherInfo' => $teacherInfo,
                        'document' => $document,
                        'fileUrl' => asset('storage/documents/' . urlencode(basename($document->file_path)))
                    ]);
                    
                default:
                    // Téléchargement direct pour les autres formats
                    return response()->download($filePath, basename($document->file_path));
            }

        } catch (\Exception $e) {
            Log::error("Erreur document viewer $filename: " . $e->getMessage());
            return view('pdf.error', [
                'error' => 'Erreur serveur: ' . $e->getMessage(),
                'filename' => $decodedFilename ?? $filename
            ]);
        }
    }

    // Nouvelle méthode pour servir directement le fichier PDF
    public function serve($filename)
    {
        try {
            if (!Auth::check()) {
                abort(401, 'Non autorisé');
            }

            $decodedFilename = urldecode($filename);
            
            $document = Document::where('file_path', 'documents/' . $decodedFilename)
                              ->orWhere('file_path', $decodedFilename)
                              ->orWhere('file_path', 'like', '%' . basename($decodedFilename))
                              ->first();

            if (!$document) {
                abort(404, 'Document non trouvé');
            }

            if (method_exists($document, 'canAccess') && !$document->canAccess(Auth::user())) {
                abort(403, 'Accès refusé');
            }

            $filePath = $this->findFilePath($document, $decodedFilename);
            if (!$filePath) {
                abort(404, 'Fichier introuvable');
            }

            // Servir le fichier avec les bonnes en-têtes pour PDF.js
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"'
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur serve $filename: " . $e->getMessage());
            abort(500, 'Erreur serveur');
        }
    }

    public function download($filename)
    {
        try {
            if (!Auth::check()) {
                abort(401, 'Non autorisé');
            }

            $decodedFilename = urldecode($filename);
            
            $document = Document::where('file_path', 'documents/' . $decodedFilename)
                              ->orWhere('file_path', $decodedFilename)
                              ->orWhere('file_path', 'like', '%' . basename($decodedFilename))
                              ->first();

            if (!$document) {
                abort(404, 'Document non trouvé');
            }

            if (method_exists($document, 'canAccess') && !$document->canAccess(Auth::user())) {
                abort(403, 'Accès refusé');
            }

            $filePath = $this->findFilePath($document, $decodedFilename);
            if (!$filePath) {
                abort(404, 'Fichier introuvable');
            }

            return response()->download($filePath, basename($document->file_path));

        } catch (\Exception $e) {
            Log::error("Erreur download $filename: " . $e->getMessage());
            abort(500, 'Erreur serveur');
        }
    }

    /**
     * Méthode utilitaire pour trouver le chemin du fichier
     */
    private function findFilePath($document, $decodedFilename)
    {
        $possiblePaths = [
            storage_path('app/public/documents/' . $decodedFilename),
            storage_path('app/public/documents/' . basename($document->file_path)),
            storage_path('app/public/' . $document->file_path),
            public_path('storage/documents/' . $decodedFilename),
            public_path('storage/documents/' . basename($document->file_path))
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function incrementView($document)
    {
        try {
            if ($document && method_exists($document, 'registerView')) {
                $document->registerView();
            }
        } catch (\Exception $e) {
            Log::warning("Erreur incrémentation vues: " . $e->getMessage());
        }
    }

    private function getTeacherInfo($document)
    {
        try {
            if (!$document || !$document->teacher) {
                return null;
            }

            $teacher = $document->teacher;
            return [
                'name' => $teacher->name ?? 'Enseignant',
                'grade' => optional($teacher->profil)->grade ?? '',
                'title' => $document->title ?? 'Document'
            ];
        } catch (\Exception $e) {
            Log::warning("Erreur infos enseignant: " . $e->getMessage());
            return [
                'name' => 'Enseignant',
                'grade' => '',
                'title' => $document->title ?? 'Document'
            ];
        }
    }
}