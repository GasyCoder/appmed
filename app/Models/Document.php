<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'is_actif',
        'download_count',
        'view_count',
        'protected_path',
        'niveau_id',
        'parcour_id',
        'semestre_id',
        // Nouvelles colonnes pour la conversion
        'original_filename',
        'original_extension',
        'converted_from',
        'converted_at'
    ];

    protected $dates = ['deleted_at', 'converted_at'];

    protected $casts = [
        'is_actif' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'converted_at' => 'datetime',
    ];

    // Relations
    public function teacher()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function parcour()
    {
        return $this->belongsTo(Parcour::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    // ✅ Nouvelles méthodes pour la conversion
    public function wasConverted(): bool
    {
        return !empty($this->converted_from);
    }

    public function getConversionInfo(): array
    {
        return [
            'was_converted' => $this->wasConverted(),
            'original_extension' => $this->original_extension,
            'converted_from' => $this->converted_from,
            'converted_at' => $this->converted_at,
            'current_extension' => $this->getExtensionAttribute()
        ];
    }

    public function getConversionStatusAttribute(): string
    {
        if (!$this->wasConverted()) {
            return 'original';
        }

        return "converti de {$this->converted_from} en " . $this->getExtensionAttribute();
    }

    // ✅ SOLUTION 1: Méthode d'accès simplifiée et debuggée
    public function canAccess(User $user): bool
    {
        try {
            // Admin et enseignants ont toujours accès
            if ($user->hasRole(['admin', 'teacher'])) {
                Log::info("Document {$this->id}: Admin/Teacher access granted for user {$user->id}");
                return true;
            }

            // Pour les étudiants - vérifications étape par étape
            if ($user->hasRole('student')) {
                // Le document doit être actif
                if (!$this->is_actif) {
                    Log::warning("Document {$this->id}: Document not active for student {$user->id}");
                    return false;
                }

                // Vérifier le niveau
                if ($this->niveau_id && $this->niveau_id !== $user->niveau_id) {
                    Log::warning("Document {$this->id}: Niveau mismatch. Doc: {$this->niveau_id}, User: {$user->niveau_id}");
                    return false;
                }

                // Vérifier le parcours
                if ($this->parcour_id && $this->parcour_id !== $user->parcour_id) {
                    Log::warning("Document {$this->id}: Parcour mismatch. Doc: {$this->parcour_id}, User: {$user->parcour_id}");
                    return false;
                }

                Log::info("Document {$this->id}: Student access granted for user {$user->id}");
                return true;
            }

            Log::warning("Document {$this->id}: No role match for user {$user->id}");
            return false;

        } catch (\Exception $e) {
            Log::error("Error checking document access: " . $e->getMessage());
            return false;
        }
    }

    // ✅ SOLUTION 2: Vérification complète de l'existence du fichier
    public function fileExists(): bool
    {
        try {
            if (empty($this->file_path)) {
                Log::error("Document {$this->id}: Empty file_path");
                return false;
            }

            $exists = Storage::disk('public')->exists($this->file_path);
            if (!$exists) {
                Log::error("Document {$this->id}: File not found at path: {$this->file_path}");
            }

            return $exists;
        } catch (\Exception $e) {
            Log::error("Document {$this->id}: Error checking file existence: " . $e->getMessage());
            return false;
        }
    }

    // ✅ SOLUTION 3: URL sécurisée avec vérifications
    public function getSecureUrl(): string
    {
        try {
            // Vérifier que le fichier existe
            if (!$this->fileExists()) {
                throw new \Exception("File does not exist");
            }

            // Pour S3 ou autres cloud storage
            if (config('filesystems.default') === 's3') {
                return Storage::temporaryUrl($this->file_path, now()->addHours(2));
            }

            // Pour stockage local - avec timestamp pour éviter le cache
            $url = Storage::url($this->file_path) . '?v=' . $this->updated_at->timestamp;
            
            Log::info("Document {$this->id}: Generated URL: " . $url);
            return $url;

        } catch (\Exception $e) {
            Log::error("Document {$this->id}: Error generating secure URL: " . $e->getMessage());
            // Retourner une URL d'erreur ou placeholder
            return url('/images/document-error.png');
        }
    }

    // ✅ SOLUTION 4: Validation robuste du PDF
    public function isPdf(): bool
    {
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        $mimeType = $this->file_type;
        
        return $extension === 'pdf' || $mimeType === 'application/pdf';
    }

    // ✅ SOLUTION 5: Vérification de la lisibilité du fichier
    public function isReadable(): bool
    {
        try {
            if (!$this->fileExists()) {
                return false;
            }

            $filePath = Storage::disk('public')->path($this->file_path);
            
            // Vérifier les permissions de lecture
            if (!is_readable($filePath)) {
                Log::error("Document {$this->id}: File not readable: {$filePath}");
                return false;
            }

            // Vérifier la taille du fichier
            $fileSize = filesize($filePath);
            if ($fileSize === false || $fileSize === 0) {
                Log::error("Document {$this->id}: File empty or corrupted: {$filePath}");
                return false;
            }

            // Pour les PDF, vérifier l'en-tête
            if ($this->isPdf()) {
                $handle = fopen($filePath, 'rb');
                $header = fread($handle, 4);
                fclose($handle);
                
                if ($header !== '%PDF') {
                    Log::error("Document {$this->id}: Invalid PDF header");
                    return false;
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Document {$this->id}: Error checking readability: " . $e->getMessage());
            return false;
        }
    }

    // ✅ SOLUTION 6: Méthode de diagnostic complète
    public function diagnose(): array
    {
        $diagnosis = [
            'id' => $this->id,
            'title' => $this->title,
            'file_path' => $this->file_path,
            'is_active' => $this->is_actif,
            'file_exists' => false,
            'is_readable' => false,
            'file_size' => $this->file_size,
            'actual_file_size' => null,
            'is_pdf' => $this->isPdf(),
            'was_converted' => $this->wasConverted(),
            'conversion_info' => $this->getConversionInfo(),
            'url' => null,
            'errors' => []
        ];

        try {
            // Test d'existence
            $diagnosis['file_exists'] = $this->fileExists();
            if (!$diagnosis['file_exists']) {
                $diagnosis['errors'][] = 'File does not exist at path: ' . $this->file_path;
            }

            // Test de lisibilité
            if ($diagnosis['file_exists']) {
                $diagnosis['is_readable'] = $this->isReadable();
                if (!$diagnosis['is_readable']) {
                    $diagnosis['errors'][] = 'File exists but is not readable';
                }

                // Taille réelle du fichier
                $filePath = Storage::disk('public')->path($this->file_path);
                $diagnosis['actual_file_size'] = filesize($filePath);
                
                if ($diagnosis['actual_file_size'] !== $this->file_size) {
                    $diagnosis['errors'][] = 'File size mismatch. DB: ' . $this->file_size . ', Actual: ' . $diagnosis['actual_file_size'];
                }
            }

            // Test URL
            try {
                $diagnosis['url'] = $this->getSecureUrl();
            } catch (\Exception $e) {
                $diagnosis['errors'][] = 'Error generating URL: ' . $e->getMessage();
            }

        } catch (\Exception $e) {
            $diagnosis['errors'][] = 'General error: ' . $e->getMessage();
        }

        return $diagnosis;
    }

    // ✅ SOLUTION 7: Méthode de réparation automatique
    public function repair(): bool
    {
        try {
            Log::info("Attempting to repair document {$this->id}");

            // Vérifier et corriger le chemin du fichier
            if (!$this->fileExists() && !empty($this->file_path)) {
                // Essayer de trouver le fichier avec différents chemins
                $possiblePaths = [
                    $this->file_path,
                    'documents/' . basename($this->file_path),
                    'uploads/' . basename($this->file_path),
                    'files/' . basename($this->file_path),
                ];

                foreach ($possiblePaths as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Log::info("Found file at alternative path: {$path}");
                        $this->file_path = $path;
                        $this->save();
                        return true;
                    }
                }
            }

            // Recalculer la taille du fichier
            if ($this->fileExists()) {
                $filePath = Storage::disk('public')->path($this->file_path);
                $actualSize = filesize($filePath);
                
                if ($actualSize !== $this->file_size) {
                    Log::info("Updating file size for document {$this->id}: {$this->file_size} -> {$actualSize}");
                    $this->file_size = $actualSize;
                    $this->save();
                }
                
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Error repairing document {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    // Méthodes existantes améliorées...
    public function views()
    {
        return $this->hasMany(DocumentView::class);
    }

    public function registerView(): void
    {
        if (Auth::check()) {
            try {
                // Vérifier que le document est accessible
                if (!$this->canAccess(Auth::user())) {
                    Log::warning("Attempted view registration for inaccessible document {$this->id} by user " . Auth::id());
                    return;
                }

                // Créer une nouvelle vue uniquement si elle n'existe pas déjà
                $view = $this->views()->firstOrCreate([
                    'user_id' => Auth::id()
                ]);

                // Mettre à jour le compteur total de vues seulement si c'est une nouvelle vue
                if ($view->wasRecentlyCreated) {
                    $this->increment('view_count');
                    Log::info("New view registered for document {$this->id} by user " . Auth::id());
                }

            } catch (\Exception $e) {
                Log::error('Error registering document view: ' . $e->getMessage());
            }
        }
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes === 0) return '0 Bytes';

        $k = 1024;
        $decimals = 2;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

        $i = floor(log($bytes) / log($k));
        return number_format($bytes / pow($k, $i), $decimals) . ' ' . $sizes[$i];
    }

    public function getExtensionAttribute()
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    // ✅ Méthode pour obtenir le nom de fichier original ou actuel
    public function getDisplayFilename(): string
    {
        return $this->original_filename ?: basename($this->file_path);
    }

    // ✅ Scope pour les documents convertis
    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_from');
    }

    // ✅ Scope pour les documents par format original
    public function scopeByOriginalFormat($query, $format)
    {
        return $query->where('original_extension', $format);
    }
}