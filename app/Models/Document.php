<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
        'download_count', 'view_count', 'is_archive',
    ];

    protected $casts = [
        'is_actif'        => 'boolean',
        'download_count'  => 'integer',
        'view_count'      => 'integer',
        'converted_at'    => 'datetime',
        'is_archive'      => 'boolean',
        'deleted_at'      => 'datetime',
    ];

    // Relations
    public function programme() { return $this->belongsTo(Programme::class); }
    public function teacher()   { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function uploader()  { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function niveau()    { return $this->belongsTo(Niveau::class); }
    public function parcour()   { return $this->belongsTo(Parcour::class); }
    public function semestre()  { return $this->belongsTo(Semestre::class); }
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
        $ext = $this->extensionFromPath();
        return in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'csv'], true);
    }

    public function isViewerLocalType(): bool
    {
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

    public function isGoogleLink(): bool
    {
        $url = (string) ($this->file_path ?? '');
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        return str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com');
    }

    public function googleId(): ?string
    {
        $url = (string) ($this->file_path ?? '');
        if ($url === '') return null;
        return self::extractGoogleId($url);
    }

    /**
     * ✅ “Convertible” = lien Google avec ID exploitable
     */
    public function canExternalDownload(): bool
    {
        return $this->isExternalLink() && $this->isGoogleLink() && self::extractGoogleId((string)$this->file_path) !== null;
    }


    public static function extractGoogleId(string $url): ?string
    {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        if ($host === '') return null;

        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

        // /d/<ID>
        if (preg_match('~/(?:file/d|document/d|spreadsheets/d|presentation/d)/([^/]+)~', $path, $m)) {
            return $m[1];
        }

        // ?id=<ID> ou uc?export=download&id=<ID>
        $query = (string) (parse_url($url, PHP_URL_QUERY) ?? '');
        if ($query !== '') {
            parse_str($query, $qs);
            if (!empty($qs['id'])) return (string) $qs['id'];
        }

        return null;
    }


    public function isExternalFileUrl(): bool
    {
        if (!$this->isExternalLink()) return false;

        $url = (string) ($this->file_path ?? '');
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: '');

        // Si extension connue => très probablement un fichier
        return in_array($ext, ['pdf','ppt','pptx','doc','docx','xls','xlsx','csv','zip','rar'], true);
    }

    public function isExternalWebPage(): bool
    {
        return $this->isExternalLink() && !$this->isExternalFileUrl();
    }



    public function externalReadUrl(): string
    {
        $url = (string) ($this->file_path ?? '');
        if ($url === '') return $url;

        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        $id = self::extractGoogleId($url);

        // ✅ Google Drive => preview (lecture)
        if ($id && str_contains($host, 'drive.google.com')) {
            return "https://drive.google.com/file/d/{$id}/preview";
        }

        // ✅ Google Docs => preview
        if ($id && str_contains($host, 'docs.google.com')) {
            if (str_contains($url, '/presentation/')) return "https://docs.google.com/presentation/d/{$id}/preview";
            if (str_contains($url, '/spreadsheets/')) return "https://docs.google.com/spreadsheets/d/{$id}/preview";
            if (str_contains($url, '/document/')) return "https://docs.google.com/document/d/{$id}/preview";
        }

        // ✅ Si c’est un fichier PDF externe direct, on tente ouverture directe (souvent lisible)
        // Exemple: .../rapport.pdf
        if ($this->isExternalFileUrl()) {
            $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: '');
            if ($ext === 'pdf') {
                return $url; // le navigateur affichera généralement le PDF
            }

            // PPT/PPTX externes => gview tente lecture
            if (in_array($ext, ['ppt','pptx'], true)) {
                return 'https://docs.google.com/gview?embedded=1&url=' . urlencode($url);
            }

            // Pour doc/xls externes => souvent téléchargement, on garde l'url
            return $url;
        }

        // ✅ Sinon c’est une page web (article) => ouvrir tel quel
        return $url;
    }



    public function externalDownloadUrl(): string
    {
        $url = (string) ($this->file_path ?? '');
        if ($url === '') return $url;

        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        $id = $this->googleId();

        if (!$id) return $url;

        // ✅ Exports “propres” selon type Docs
        if (str_contains($host, 'docs.google.com')) {
            if (str_contains($url, '/document/')) {
                return "https://docs.google.com/document/d/{$id}/export?format=docx";
            }
            if (str_contains($url, '/spreadsheets/')) {
                return "https://docs.google.com/spreadsheets/d/{$id}/export?format=xlsx";
            }
            if (str_contains($url, '/presentation/')) {
                return "https://docs.google.com/presentation/d/{$id}/export/pptx";
            }
        }

        // ✅ Drive “classique”
        if (str_contains($host, 'drive.google.com')) {
            return "https://drive.google.com/uc?export=download&id={$id}";
        }

        return $url;
    }


    // ----------------------------
    // Compteurs (✅ seuls les étudiants comptent)
    // ----------------------------

    protected function shouldCountFor(?User $user): bool
    {
        return $user && $user->hasRole('student');
    }

    /**
     * Vue unique par user (1 fois par document) - ✅ seulement étudiant
     */
    public function registerView(?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$this->shouldCountFor($user)) return;

        DB::transaction(function () use ($user) {
            $view = $this->views()->firstOrCreate(['user_id' => $user->id]);

            if ($view->wasRecentlyCreated) {
                $this->increment('view_count');
            }
        });
    }


    /**
     * Download - ✅ seulement étudiant
     * (Si vous voulez "unique par étudiant", on peut faire une table downloads aussi.)
     */
    public function registerDownload(?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$this->shouldCountFor($user)) return;

        // Atomic increment
        $this->increment('download_count');
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

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) ($this->file_size ?? 0);
        if ($bytes <= 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function getExtensionAttribute(): string
    {
        $ext = $this->extensionFromPath();
        return $ext !== '' ? $ext : ($this->isExternalLink() ? 'link' : '');
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archive', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archive', false);
    }
}