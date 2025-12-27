<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use App\Models\AuthorizedEmail;
use App\Models\User;
use App\Jobs\SendAnnouncementEmailsJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementsManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterActive = 'all'; // all|active|inactive
    public string $filterType = 'all';   // all|info|success|warning|danger
    public int $perPage = 10;

    public bool $showModal = false;
    public ?int $announcementId = null;

    // Form
    public string $type = 'info';
    public string $title = '';
    public string $body = '';
    public string $action_label = 'En savoir plus';
    public ?string $action_url = null;
    public bool $is_active = true;
    public ?string $starts_at = null; // datetime-local string
    public ?string $ends_at = null;   // datetime-local string

    // Audience in-app
    public bool $audienceAll = true;
    public array $audienceRoles = []; // ['teacher','student']

    // Email notification
    public bool $sendEmail = false;
    public bool $notifyAll = true;
    public array $notifyRoles = []; // ['teacher','student']

    // Delete confirm
    public ?int $confirmingDeleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterActive' => ['except' => 'all'],
        'filterType' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        abort_if(!Auth::user()?->hasRole('admin'), 403);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterActive(): void { $this->resetPage(); }
    public function updatingFilterType(): void { $this->resetPage(); }

    public function updatedAudienceAll($value): void
    {
        if ((bool) $value) {
            $this->audienceRoles = [];
        }
    }

    public function updatedSendEmail($value): void
    {
        if (!(bool) $value) {
            $this->notifyAll = true;
            $this->notifyRoles = [];
        }
    }

    public function updatedNotifyAll($value): void
    {
        if ((bool) $value) {
            $this->notifyRoles = [];
        }
    }

    protected function rules(): array
    {
        return [
            'type' => 'required|in:info,success,warning,danger',
            'title' => 'required|string|min:3|max:190',
            'body' => 'required|string|min:5',
            'action_label' => 'nullable|string|max:60',
            'action_url' => 'nullable|string|max:2048', // URL optional; tu peux mettre "url" si tu veux strict
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',

            'audienceAll' => 'boolean',
            'audienceRoles' => 'array',
            'audienceRoles.*' => 'in:teacher,student',

            'sendEmail' => 'boolean',
            'notifyAll' => 'boolean',
            'notifyRoles' => 'array',
            'notifyRoles.*' => 'in:teacher,student',
        ];
    }

    protected function validateAudienceAndNotify(): void
    {
        // Audience (in-app) : si pas all, au moins 1 rôle
        if (!$this->audienceAll && count($this->audienceRoles) === 0) {
            $this->addError('audienceRoles', "Choisissez au moins un rôle (enseignant / étudiant) ou cochez 'Tout le monde'.");
        }

        // Email notify : si sendEmail true et pas notifyAll, au moins 1 rôle
        if ($this->sendEmail && !$this->notifyAll && count($this->notifyRoles) === 0) {
            $this->addError('notifyRoles', "Choisissez au moins un rôle à notifier (teacher / student) ou cochez 'Tous'.");
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            $this->dispatch('scrollToTop'); // optionnel
            throw new \RuntimeException('Validation roles failed');
        }
    }

    public function openCreate(): void
    {
        $this->resetValidation();

        $this->announcementId = null;
        $this->type = 'info';
        $this->title = '';
        $this->body = '';
        $this->action_label = 'En savoir plus'; // ✅ défaut
        $base = rtrim(config('app.url'), '/');
        $this->action_url = $base;

        $this->is_active = true;
        $this->starts_at = null;
        $this->ends_at = null;

        $this->audienceAll = true;
        $this->audienceRoles = [];

        $this->sendEmail = false;
        $this->notifyAll = true;
        $this->notifyRoles = [];

        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $a = Announcement::query()->findOrFail($id);

        $this->announcementId = $a->id;
        $this->type = $a->type;
        $this->title = $a->title;
        $this->body = $a->body;
        $this->action_label = $a->action_label;
        $base = rtrim(config('app.url'), '/');
        $this->action_url = $a->action_url ?: $base; 
        $this->is_active = (bool) $a->is_active;

        $this->starts_at = $a->starts_at ? $a->starts_at->format('Y-m-d\TH:i') : null;
        $this->ends_at = $a->ends_at ? $a->ends_at->format('Y-m-d\TH:i') : null;

        // Audience
        if (is_null($a->audience_roles)) {
            $this->audienceAll = true;
            $this->audienceRoles = [];
        } else {
            $this->audienceAll = false;
            $this->audienceRoles = array_values($a->audience_roles);
        }

        // Email notify (par défaut: off en edit, tu peux changer si tu veux)
        $this->sendEmail = false;
        $this->notifyAll = true;
        $this->notifyRoles = [];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteConfirmed(): void
    {
        if (!$this->confirmingDeleteId) return;

        Announcement::query()->whereKey($this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;

        session()->flash('success', "Annonce supprimée.");
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $a = Announcement::query()->findOrFail($id);
        $a->update(['is_active' => !$a->is_active]);
    }

    private function parseDateTimeLocal(?string $value): ?Carbon
    {
        if (!$value) return null;
        return Carbon::parse($value);
    }

    private function buildRecipientEmails(): array
    {
        // Si notifyAll => teacher + student
        $roles = $this->notifyAll ? ['teacher', 'student'] : $this->notifyRoles;

        $emails = [];

        if (in_array('teacher', $roles, true)) {
            $teacherEmails = User::query()
                ->role('teacher')
                ->where('status', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->all();

            $emails = array_merge($emails, $teacherEmails);
        }

        if (in_array('student', $roles, true)) {
            $studentEmails = AuthorizedEmail::query()
                ->where('is_registered', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->all();

            $emails = array_merge($emails, $studentEmails);
        }

        // Normalize & unique
        $emails = array_values(array_unique(array_filter(array_map(fn($e) => strtolower(trim((string)$e)), $emails))));

        return $emails;
    }

    public function save(): void
    {
        $this->resetValidation();
        $this->validate();
        $this->validateAudienceAndNotify();

        $startsAt = $this->parseDateTimeLocal($this->starts_at);
        $endsAt   = $this->parseDateTimeLocal($this->ends_at);

        $audienceRoles = $this->audienceAll ? null : array_values(array_unique($this->audienceRoles));

        DB::beginTransaction();
        try {
            $payload = [
                'type' => $this->type,
                'title' => $this->title,
                'body' => $this->body,
                'action_label' => $this->action_label ?: null,
                'action_url' => $this->action_url ?: null,
                'is_active' => (bool) $this->is_active,
                'audience_roles' => $audienceRoles,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ];

            if ($this->announcementId) {
                $a = Announcement::query()->findOrFail($this->announcementId);
                $a->update($payload);
            } else {
                $payload['created_by'] = Auth::id();
                $a = Announcement::create($payload);
                $this->announcementId = $a->id;
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->addError('general', "Erreur lors de l’enregistrement.");
            return;
        }

        // Queue emails
        $queuedCount = 0;
        if ($this->sendEmail) {
            $emails = $this->buildRecipientEmails();

            if (count($emails) > 0) {
                // Chunk pour éviter un seul job trop long
                foreach (array_chunk($emails, 80) as $chunk) {
                    SendAnnouncementEmailsJob::dispatch($this->announcementId, $chunk)
                        ->onQueue('mail');
                    $queuedCount += count($chunk);
                }
            }
        }

        $this->showModal = false;
        $this->resetForm();

        session()->flash(
            'success',
            $queuedCount > 0
                ? "Annonce enregistrée. $queuedCount email(s) mis en file d’attente (queue)."
                : "Annonce enregistrée."
        );

        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'announcementId','type','title','body','action_url',
            'is_active','starts_at','ends_at',
            'audienceAll','audienceRoles',
            'sendEmail','notifyAll','notifyRoles',
            'showModal','confirmingDeleteId',
        ]);

        $this->action_label = 'En savoir plus'; // ✅ défaut
        $this->type = 'info';
        $this->is_active = true;
        $this->audienceAll = true;
        $this->notifyAll = true;

        $this->resetValidation();
    }


    public function render()
    {
        $q = Announcement::query()
            ->withCount('views')
            ->when($this->search, function ($qq) {
                $s = trim($this->search);
                $qq->where(function ($w) use ($s) {
                    $w->where('title', 'like', "%{$s}%")
                      ->orWhere('body', 'like', "%{$s}%");
                });
            })
            ->when($this->filterType !== 'all', fn($qq) => $qq->where('type', $this->filterType))
            ->when($this->filterActive === 'active', fn($qq) => $qq->where('is_active', true))
            ->when($this->filterActive === 'inactive', fn($qq) => $qq->where('is_active', false))
            ->orderByDesc('created_at');

        return view('livewire.admin.announcements-manager', [
            'announcements' => $q->paginate($this->perPage),
        ]);
    }
}
