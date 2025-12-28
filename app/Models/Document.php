<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'source_url',

        'niveau_id',
        'parcour_id',
        'semestre_id',
        'programme_id',

        'uploaded_by',
        'is_actif',
        'is_archive',

        'view_count',
        'download_count',

        'original_filename',
        'original_extension',
        'converted_from',
        'converted_at',
        'protected_path',
        'file_type',

        // ✅ compat
        'file_size',
        'file_size_bytes',
    ];

    protected $casts = [
        'is_actif' => 'bool',
        'is_archive' => 'bool',
        'view_count' => 'int',
        'download_count' => 'int',

        'file_size' => 'int',
        'file_size_bytes' => 'int',

        'converted_at' => 'datetime',
    ];


    /* Relations */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // compat viewer
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function niveau(): BelongsTo { return $this->belongsTo(Niveau::class); }
    public function parcour(): BelongsTo { return $this->belongsTo(Parcour::class); }
    public function semestre(): BelongsTo { return $this->belongsTo(Semestre::class); }

    public function views(): HasMany
    {
        return $this->hasMany(DocumentView::class);
    }

    /* Access */
    public function canAccess(User $user): bool
    {
        if ($user->hasRole('teacher')) {
            return (int) $this->uploaded_by === (int) $user->id;
        }

        if ((int) $this->niveau_id !== (int) $user->niveau_id) return false;

        if (!empty($this->parcour_id) && (int) $this->parcour_id !== (int) $user->parcour_id) {
            return false;
        }

        return true;
    }

    /* External helpers */
    public function externalUrl(): ?string
    {
        $url = trim((string) ($this->source_url ?: $this->file_path ?: ''));
        if ($url === '') return null;

        return (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) ? $url : null;
    }

    public function isExternalLink(): bool
    {
        $url = (string) ($this->source_url ?: $this->file_path);
        return Str::startsWith($url, ['http://', 'https://']);
    }

    /**
     * Google link ?
     */
    public function isGoogleLink(): bool
    {
        $url  = (string) ($this->source_url ?: $this->file_path);
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        return str_contains($host, 'drive.google.com')
            || str_contains($host, 'docs.google.com');
    }


    /**
     * Extraction robuste de l'ID Google (Drive / Docs / Slides / Sheets).
     */
    private function extractGoogleId(string $url): ?string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        $query = (string) (parse_url($url, PHP_URL_QUERY) ?? '');

        // /d/{id}
        if (preg_match('~/d/([a-zA-Z0-9_-]+)~', $path, $m)) {
            return $m[1];
        }

        // ?id={id}
        if (preg_match('~(?:^|&)id=([a-zA-Z0-9_-]+)(?:&|$)~', $query, $m)) {
            return $m[1];
        }

        // parfois id= est dans l'URL brute
        if (preg_match('~id=([a-zA-Z0-9_-]+)~', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function externalReadUrl(): string
    {
        $url = (string) ($this->source_url ?: $this->file_path);
        if ($url === '') return $url;

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

        // ---- GOOGLE DRIVE ----
        if (str_contains($host, 'drive.google.com')) {
            $id = $this->extractGoogleId($url);

            // si on a un ID, on force le preview
            if ($id) {
                return "https://drive.google.com/file/d/{$id}/preview";
            }

            // fallback: laisses l'URL telle quelle
            return $url;
        }

        // ---- GOOGLE DOCS ----
        if (str_contains($host, 'docs.google.com')) {
            $id = $this->extractGoogleId($url);
            if (!$id) return $url;

            if (str_contains($path, '/document/')) {
                return "https://docs.google.com/document/d/{$id}/preview";
            }

            if (str_contains($path, '/presentation/')) {
                return "https://docs.google.com/presentation/d/{$id}/preview";
            }

            if (str_contains($path, '/spreadsheets/')) {
                return "https://docs.google.com/spreadsheets/d/{$id}/preview";
            }

            // fallback docs.google
            return $url;
        }

        // autres liens externes
        return $url;
    }

    public function externalDownloadUrl(): ?string
    {
        $url = (string) ($this->source_url ?: $this->file_path);
        if ($url === '') return null;

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

        // Drive
        if (str_contains($host, 'drive.google.com')) {
            $id = $this->extractGoogleId($url);
            return $id ? "https://drive.google.com/uc?export=download&id={$id}" : null;
        }

        // Docs
        if (str_contains($host, 'docs.google.com')) {
            $id = $this->extractGoogleId($url);
            if (!$id) return null;

            if (str_contains($path, '/document/')) {
                return "https://docs.google.com/document/d/{$id}/export?format=pdf";
            }
            if (str_contains($path, '/presentation/')) {
                return "https://docs.google.com/presentation/d/{$id}/export/pdf";
            }
            if (str_contains($path, '/spreadsheets/')) {
                return "https://docs.google.com/spreadsheets/d/{$id}/export?format=pdf";
            }
        }

        return null;
    }


    /* Local helpers */
    public function extensionFromPath(): string
    {
        $raw = (string) ($this->source_url ?: $this->file_path ?: '');
        if ($raw === '') return '';

        if (Str::startsWith($raw, ['http://', 'https://'])) {
            $urlPath = (string) (parse_url($raw, PHP_URL_PATH) ?? '');
            return strtolower((string) pathinfo($urlPath, PATHINFO_EXTENSION));
        }

        return strtolower((string) pathinfo($raw, PATHINFO_EXTENSION));
    }

    public function isPdfLocal(): bool
    {
        return !$this->isExternalLink()
            && !empty($this->file_path)
            && $this->extensionFromPath() === 'pdf';
    }

    public function fileExists(): bool
    {
        if ($this->isExternalLink()) return false;
        if (empty($this->file_path)) return false;

        return Storage::disk('public')->exists($this->file_path);
    }

    public function getDisplayFilename(?string $forceExt = null): string
    {
        $title = trim((string) $this->title);
        if ($title === '') $title = 'document';

        $safe = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $title);

        $ext = strtolower((string) ($forceExt ?: $this->extensionFromPath() ?: 'pdf'));

        if (!str_ends_with(strtolower($safe), '.' . $ext)) {
            $safe .= '.' . $ext;
        }

        return $safe;
    }

    /* Counters unique par user (sans DocumentDownload) */
    public function registerView(User $user): bool
    {
        // si vous voulez compter pour tous les rôles, supprimez cette condition
        if (!$user->hasRole('student')) return false;

        $row = $this->views()->firstOrCreate(
            ['user_id' => $user->id],
            ['viewed_at' => null, 'downloaded_at' => null]
        );

        if (is_null($row->viewed_at)) {
            $row->forceFill(['viewed_at' => now()])->save();
            $this->increment('view_count');
            return true;
        }

        return false;
    }

    public function registerDownload(User $user): bool
    {
        // idem: si vous voulez tous rôles, supprimez cette condition
        if (!$user->hasRole('student')) return false;

        $row = $this->views()->firstOrCreate(
            ['user_id' => $user->id],
            ['viewed_at' => null, 'downloaded_at' => null]
        );

        if (is_null($row->downloaded_at)) {
            $row->forceFill(['downloaded_at' => now()])->save();
            $this->increment('download_count');
            return true;
        }

        return false;
    }

    /* Size formatted */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = (int) ($this->file_size_bytes ?? 0);
        if ($bytes <= 0) return '';

        $units = ['o','Ko','Mo','Go','To'];
        $i = 0;
        $val = (float) $bytes;

        while ($val >= 1024 && $i < count($units) - 1) {
            $val /= 1024;
            $i++;
        }

        $out = number_format($val, 1, '.', '');
        $out = rtrim(rtrim($out, '0'), '.');

        return $out . ' ' . $units[$i];
    }



    public function canRead(User $user): bool
    {
        // Admin
        if ($user->hasRole('admin')) return true;

        // Teacher: voit les docs des niveaux qu'il enseigne
        if ($user->hasRole('teacher')) {
            $allowedNiveauIds = $user->teacherNiveauIds(); // pivot niveau_user
            return $allowedNiveauIds->contains((int)$this->niveau_id)
                || (int)$this->uploaded_by === (int)$user->id; // ses propres docs toujours
        }

        // Student: uniquement son niveau (+ parcours) et partagé
        if ($user->hasRole('student')) {
            if (!(bool)$this->is_actif) return false;

            if ((int)$this->niveau_id !== (int)$user->niveau_id) return false;

            // si ton parcours est obligatoire côté étudiant
            if (!empty($this->parcour_id) && (int)$this->parcour_id !== (int)$user->parcour_id) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function canManage(User $user): bool
    {
        if ($user->hasRole('admin')) return true;

        // Seul l'uploader peut modifier/supprimer/archiver/toggle status
        return $user->hasRole('teacher') && (int)$this->uploaded_by === (int)$user->id;
    }

    /**
     * Scope “visible dans l’index” (fusion teacher/student).
     */
    public function scopeVisibleTo(Builder $q, User $user): Builder
    {
        // Admin: tout
        if ($user->hasRole('admin')) {
            return $q;
        }

        // Teacher: docs partagés dans ses niveaux + ses docs (même brouillons)
        if ($user->hasRole('teacher')) {
            $niveauIds = $user->teacherNiveauIds()->map(fn($v) => (int)$v)->values()->all();

            return $q->where(function ($qq) use ($user, $niveauIds) {
                $qq->whereIn('niveau_id', $niveauIds)->where('is_actif', true)
                ->orWhere('uploaded_by', $user->id);
            });
        }

        // Student: uniquement niveau (+ parcours) et actifs
        if ($user->hasRole('student')) {
            return $q->where('is_actif', true)
                    ->where('niveau_id', $user->niveau_id)
                    ->when(!empty($user->parcour_id), fn($qq) => $qq->where(function($w) use ($user){
                        $w->whereNull('parcour_id')->orWhere('parcour_id', $user->parcour_id);
                    }));
        }

        // autres
        return $q->whereRaw('1=0');
    }
}
