<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'academic_year',
        'type',
        'niveau_id',
        'parcour_id',
        'semestre_id',
        'start_date',
        'end_date',
        'is_active',
        'uploaded_by',
        'view_count',
        'download_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'view_count' => 'integer',
        'download_count' => 'integer',
    ];

    // ✅ NOUVELLE RELATION
    public function views(): HasMany
    {
        return $this->hasMany(ScheduleView::class);
    }

    // Relations
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

    // Accessors
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' octets';
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getExtensionAttribute()
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    public function isPdf()
    {
        return $this->extension === 'pdf';
    }

    public function isImage()
    {
        return in_array($this->extension, ['jpg', 'jpeg', 'png', 'gif']);
    }

    // ✅ NOUVELLE MÉTHODE - Enregistrer une vue unique
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
                Log::info("Schedule {$this->id}: Vue enregistrée pour user {$user->id}. Total: {$this->view_count}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Schedule {$this->id}: Erreur registerView - " . $e->getMessage());
        }
    }

    // Méthodes (anciennes - gardées pour compatibilité)
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    // ✅ NOUVELLE MÉTHODE - Enregistrer un téléchargement
    public function registerDownload(?User $user = null): void
    {
        try {
            DB::beginTransaction();
            $this->increment('download_count');
            $this->refresh();
            DB::commit();

            $userId = $user?->id ?? (Auth::id() ?? 'guest');
            Log::info("Schedule {$this->id}: Téléchargement enregistré pour user {$userId}. Total: {$this->download_count}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Schedule {$this->id}: Erreur registerDownload - " . $e->getMessage());
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    public function scopeForParcour($query, $parcourId)
    {
        return $query->where('parcour_id', $parcourId);
    }

    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where(function($q) use ($now) {
            $q->where('start_date', '<=', $now)
              ->orWhereNull('start_date');
        })
        ->where(function($q) use ($now) {
            $q->where('end_date', '>=', $now)
              ->orWhereNull('end_date');
        });
    }

    // ✅ NOUVEAU SCOPE - Schedules non vus par un user
    public function scopeUnviewedBy($query, User $user)
    {
        return $query->whereDoesntHave('views', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }
}