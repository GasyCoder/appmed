<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uploaded_by', 'niveau_id', 'parcour_id', 'semestre_id', 'programme_id',
        'title', 'file_path', 'protected_path', 'original_filename', 'original_extension',
        'converted_from', 'converted_at', 'file_type', 'file_size', 'is_actif',
        'download_count', 'view_count',
    ];

    protected $dates = ['deleted_at', 'converted_at'];

    protected $casts = [
        'is_actif' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'converted_at' => 'datetime',
    ];

    // Relations
    public function programme() { return $this->belongsTo(Programme::class); }
    public function teacher() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function niveau() { return $this->belongsTo(Niveau::class); }
    public function parcour() { return $this->belongsTo(Parcour::class); }
    public function semestre() { return $this->belongsTo(Semestre::class); }
    public function views(): HasMany { return $this->hasMany(DocumentView::class); }

    // ----------------------------
    // Règles / Helpers
    // ----------------------------

    public function isExternalLink(): bool
    {
        return Str::startsWith((string) $this->file_path, ['http://', 'https://']);
    }

    public function extensionFromPath(): string
    {
        $path = (string) ($this->file_path ?? '');

        if ($this->isExternalLink()) {
            $urlPath = (string) (parse_url($path, PHP_URL_PATH) ?? '');
            return strtolower(pathinfo($urlPath, PATHINFO_EXTENSION) ?: '');
        }

        return strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: '');
    }

    public function isDirectDownloadType(): bool
    {
        // DOC/Office => téléchargement direct obligatoire
        $ext = $this->extensionFromPath();
        return in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'csv'], true);
    }

    public function isViewerLocalType(): bool
    {
        // viewer obligatoire UNIQUEMENT pour local PDF/PPTX
        if ($this->isExternalLink()) return false;

        $ext = $this->extensionFromPath();
        return in_array($ext, ['pdf', 'ppt', 'pptx'], true);
    }

    public function isPdfLocal(): bool
    {
        return !$this->isExternalLink() && $this->extensionFromPath() === 'pdf';
    }

    // ----------------------------
    // Google Drive / Docs helpers
    // ----------------------------

    public static function extractGoogleId(string $url): ?string
    {
        if (preg_match('~drive\.google\.com/file/d/([^/]+)~', $url, $m)) return $m[1];
        if (preg_match('~drive\.google\.com/open\?id=([^&]+)~', $url, $m)) return $m[1];
        if (preg_match('~drive\.google\.com/uc\?id=([^&]+)~', $url, $m)) return $m[1];
        if (preg_match('~docs\.google\.com/(document|spreadsheets|presentation)/d/([^/]+)~', $url, $m)) return $m[2];
        return null;
    }

    /**
     * URL de lecture EXTERNE (nouvel onglet)
     * - Drive => preview
     * - Docs/Sheets/Slides => preview
     * - Autres pdf/ppt => gview
     * - Sinon => lien brut
     */
    public function externalReadUrl(): string
    {
        $url = (string) ($this->file_path ?? '');
        if ($url === '') return $url;

        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        $id = self::extractGoogleId($url);

        if ($id && str_contains($host, 'drive.google.com')) {
            return "https://drive.google.com/file/d/{$id}/preview";
        }

        if ($id && str_contains($host, 'docs.google.com')) {
            if (str_contains($url, '/presentation/')) return "https://docs.google.com/presentation/d/{$id}/preview";
            if (str_contains($url, '/spreadsheets/')) return "https://docs.google.com/spreadsheets/d/{$id}/preview";
            if (str_contains($url, '/document/')) return "https://docs.google.com/document/d/{$id}/preview";
        }

        $ext = $this->extensionFromPath();
        if (in_array($ext, ['pdf', 'ppt', 'pptx'], true)) {
            return 'https://docs.google.com/gview?embedded=1&url=' . urlencode($url);
        }

        return $url;
    }

    /**
     * URL de téléchargement EXTERNE
     * - Drive/Docs => uc?export=download&id=
     * - Sinon => lien brut
     */
    public function externalDownloadUrl(): string
    {
        $url = (string) ($this->file_path ?? '');
        if ($url === '') return $url;

        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        $id = self::extractGoogleId($url);

        if ($id && (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com'))) {
            return "https://drive.google.com/uc?export=download&id={$id}";
        }

        return $url;
    }

    // ----------------------------
    // Compteurs
    // ----------------------------

    /**
     * Vue unique par user (1 fois par document)
     */
    public function registerView(?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$user) return;

        try {
            DB::beginTransaction();

            $exists = $this->views()->where('user_id', $user->id)->exists();
            if (!$exists) {
                $this->views()->create(['user_id' => $user->id]);
                $this->increment('view_count');
                $this->refresh();
                Log::info("Document {$this->id}: Vue enregistrée pour user {$user->id}. Total: {$this->view_count}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Document {$this->id}: Erreur registerView - " . $e->getMessage());
        }
    }

    public function registerDownload(?User $user = null): void
    {
        try {
            DB::beginTransaction();
            $this->increment('download_count');
            $this->refresh();
            DB::commit();

            $userId = $user?->id ?? (Auth::id() ?? 'guest');
            Log::info("Document {$this->id}: Téléchargement enregistré pour user {$userId}. Total: {$this->download_count}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Document {$this->id}: Erreur registerDownload - " . $e->getMessage());
        }
    }

    // ----------------------------
    // Access + fichiers
    // ----------------------------

    public function canAccess(User $user): bool
    {
        if ($user->hasRole(['admin', 'teacher'])) return true;

        if ($user->hasRole('student')) {
            if (!$this->is_actif) return false;
            if ($this->niveau_id && $this->niveau_id !== $user->niveau_id) return false;
            if ($this->parcour_id && $this->parcour_id !== $user->parcour_id) return false;
            return true;
        }

        return false;
    }

    public function fileExists(): bool
    {
        if (empty($this->file_path)) return false;

        if ($this->isExternalLink()) return true;

        return Storage::disk('public')->exists($this->file_path);
    }

    public function getDisplayFilename(): string
    {
        return $this->original_filename ?: basename((string) $this->file_path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = (int) ($this->file_size ?? 0);
        if ($bytes <= 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function getExtensionAttribute()
    {
        // compat: si ancien code l’utilise
        $ext = $this->extensionFromPath();
        return $ext !== '' ? $ext : ($this->isExternalLink() ? 'link' : '');
    }
}
