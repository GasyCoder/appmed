<?php

namespace App\Models;

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
        'semestre_id'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_actif' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
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

    // Validation de fichier
    public function validateFile($file)
    {
        return in_array($file->getMimeType(), self::getAllowedFileTypes());
    }

    // Méthodes d'accès
    public function canAccess(User $user): bool
    {
        if ($user->hasRole(['admin', 'teacher'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $this->is_actif &&
                   $this->niveau_id === $user->niveau_id &&
                   $this->parcour_id === $user->parcour_id;
        }

        return false;
    }

    public function canDownload(User $user): bool
    {
        return $user->hasRole(['admin', 'teacher']);
    }

    // Scopes
    public function scopeForUser($query, User $user)
    {
        if ($user->hasRole(['admin', 'teacher'])) {
            return $query;
        }

        return $query->where('is_actif', true);
    }

    // Compteurs
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
        $this->save();
    }

    // Types de fichiers autorisés
    public static function getAllowedFileTypes()
    {
        return [
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png'
        ];
    }

    // Attributs et méthodes d'URL
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    public function getSecureUrl(): string
    {
        if (config('filesystems.default') === 's3') {
            return Storage::temporaryUrl($this->file_path, now()->addMinutes(5));
        }

        return url(Storage::url($this->file_path));
    }

    public function registerView(): void
    {
        $this->increment('view_count');
        $this->save();
    }

    public function isPdf(): bool
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes === 0) return '0 Bytes';

        $k = 1024;
        $decimals = 2;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $i = floor(log($bytes) / log($k));

        return number_format($bytes / pow($k, $i), $decimals) . ' ' . $sizes[$i];
    }

    public function getExtensionAttribute()
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    // Sécurisation des fichiers (optionnel)
    public function secureFileUpload($file)
    {
        // Enregistrer le fichier de manière sécurisée
        $path = $file->storeAs('documents', $file->getClientOriginalName(), 'public');
        $this->file_path = $path;
        $this->file_size = $file->getSize();
        $this->file_type = $file->getMimeType();
        $this->save();
    }

    public function deleteFile()
    {
        if ($this->fileExists()) {
            Storage::disk('public')->delete($this->file_path);
        }
        $this->delete();
    }
}
