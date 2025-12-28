<?php

namespace App\Livewire\Admin;

use App\Mail\UserAccountCreatedMail;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTeacher extends Component
{
    use WithPagination, LivewireAlert;

    // Listing / Filters
    public string $search = '';
    public int $perPage = 10;
    public string $niveau_filter = '';

    // Modal
    public bool $showUserModal = false;
    public bool $isLoading = false;

    // Form fields (simplifié)
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public array $selectedTeacherNiveaux = [];
    public bool $status = true;

    // Parcours unique par défaut
    public ?int $defaultParcourId = null;

    // Delete confirm
    public ?int $pendingDeleteUserId = null;

    protected $listeners = [
        'deleteConfirmed' => 'deleteUser',
        'refresh' => '$refresh',
    ];

    public function mount(): void
    {
        abort_if(!Auth::user()?->hasRole('admin'), 403, 'Non autorisé.');

        // Parcours unique => premier actif
        $this->defaultParcourId = Parcour::query()
            ->where('status', true)
            ->orderBy('id')
            ->value('id');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'selectedTeacherNiveaux' => ['required', 'array', 'min:1'],
            'selectedTeacherNiveaux.*' => ['integer', 'exists:niveaux,id'],
            'status' => ['boolean'],
        ];
    }

    protected array $messages = [
        'name.required' => 'Le nom est requis.',
        'email.required' => 'L\'email est requis.',
        'email.email' => 'L\'email doit être valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'selectedTeacherNiveaux.required' => 'Sélectionnez au moins un niveau.',
        'selectedTeacherNiveaux.min' => 'Sélectionnez au moins un niveau.',
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }
    public function updatedNiveauFilter(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showUserModal = true;
    }

    public function resetForm(): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'selectedTeacherNiveaux',
            'status',
            'isLoading',
            'pendingDeleteUserId',
        ]);

        $this->status = true;
        $this->resetValidation();
        $this->showUserModal = false;
    }

    /**
     * Create OR Update (simplifié)
     */
    public function createTeacher(): void
    {
        $this->isLoading = true;

        try {
            $this->validate();

            $isUpdate = (bool) $this->userId;

            if (!$this->defaultParcourId) {
                $this->alert('error', 'Erreur', [
                    'toast' => true, 'position' => 'center',
                    'timer' => 2600, 'showConfirmButton' => true,
                    'confirmButtonText' => 'OK',
                    'text' => 'Aucun parcours actif trouvé (table parcours vide).',
                ]);
                return;
            }

            DB::transaction(function () use ($isUpdate) {

                if ($isUpdate) {
                    $user = User::role('teacher')->findOrFail($this->userId);

                    $user->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'status' => (bool) $this->status,
                    ]);

                    $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);

                    // Parcours unique
                    $user->teacherParcours()->sync([$this->defaultParcourId]);

                    return;
                }

                // CREATE
                $token = Str::random(64);
                $temporaryPassword = Str::random(10);

                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'status' => (bool) $this->status,
                    'password' => Hash::make($temporaryPassword),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('teacher');

                $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);

                // Parcours unique
                $user->teacherParcours()->sync([$this->defaultParcourId]);

                // Lien reset password (48h)
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => Hash::make($token), 'created_at' => now()]
                );

                // Email
                Mail::to($user->email)->send(new UserAccountCreatedMail(
                    name: $user->name,
                    email: $user->email,
                    token: $token,
                    temporaryPassword: $temporaryPassword,
                    validityHours: 48,
                    sexe: null,
                    appName: 'EPIRC',
                    orgName: 'Faculté de Médecine — Université de Mahajanga'
                ));
            });

            $msg = $isUpdate
                ? 'Enseignant mis à jour.'
                : 'Compte enseignant créé. Email envoyé.';

            $this->resetForm();

            $this->alert('success', 'Succès', [
                'toast' => true, 'position' => 'center',
                'timer' => 2200, 'timerProgressBar' => true,
                'showConfirmButton' => false,
                'text' => $msg,
            ]);

        } catch (\Throwable $e) {
            Log::error('UsersTeacher createTeacher error', ['error' => $e->getMessage()]);

            $this->alert('error', 'Erreur', [
                'toast' => true, 'position' => 'center',
                'timer' => 2800, 'timerProgressBar' => true,
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'text' => $e->getMessage(),
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Edit (remplit uniquement les champs simplifiés)
     */
    public function editTeacher(int $userId): void
    {
        try {
            $user = User::with(['teacherNiveaux'])
                ->role('teacher')
                ->findOrFail($userId);

            $this->userId = $user->id;
            $this->name = (string) $user->name;
            $this->email = (string) $user->email;
            $this->status = (bool) $user->status;

            $this->selectedTeacherNiveaux = $user->teacherNiveaux->pluck('id')->map(fn($v) => (int)$v)->toArray();

            $this->resetValidation();
            $this->showUserModal = true;

        } catch (\Throwable $e) {
            Log::error('UsersTeacher editTeacher error', [
                'error' => $e->getMessage(), 'user_id' => $userId,
            ]);

            $this->alert('error', 'Erreur', [
                'toast' => true, 'position' => 'center',
                'timer' => 2200, 'showConfirmButton' => false,
                'text' => 'Erreur lors du chargement.',
            ]);
        }
    }

    public function confirmDelete(int $userId): void
    {
        $this->pendingDeleteUserId = $userId;

        $this->alert('warning', 'Confirmer la suppression', [
            'text' => 'Voulez-vous vraiment supprimer cet enseignant ? Cette action est irréversible.',
            'toast' => false,
            'position' => 'center',
            'showCancelButton' => true,
            'cancelButtonText' => 'Annuler',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Oui, supprimer',
            'allowOutsideClick' => false,
            'timer' => null,
            'onConfirmed' => 'deleteConfirmed',
            'data' => ['id' => $userId],
        ]);
    }

    public function deleteUser($payload = null): void
    {
        $id = 0;

        if (is_numeric($payload)) $id = (int) $payload;

        if (is_array($payload)) {
            $id = (int) (
                $payload['id'] ??
                $payload['userId'] ??
                ($payload['data']['id'] ?? 0)
            );
        }

        if ($id <= 0) $id = (int) ($this->pendingDeleteUserId ?? 0);

        if ($id <= 0) {
            $this->alert('error', 'Erreur', [
                'toast' => true, 'position' => 'center',
                'timer' => 2200, 'showConfirmButton' => false,
                'text' => 'ID utilisateur introuvable pour suppression.',
            ]);
            return;
        }

        try {
            $user = User::role('teacher')->findOrFail($id);
            $user->delete();

            $this->pendingDeleteUserId = null;

            $this->alert('success', 'Supprimé', [
                'toast' => true, 'position' => 'center',
                'timer' => 2000, 'showConfirmButton' => false,
                'text' => 'Enseignant supprimé.',
            ]);

        } catch (\Throwable $e) {
            Log::error('UsersTeacher deleteUser error', ['error' => $e->getMessage(), 'user_id' => $id]);

            $this->alert('error', 'Erreur', [
                'toast' => true, 'position' => 'center',
                'timer' => 2400, 'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'text' => 'Suppression impossible.',
            ]);
        }
    }

    public function toggleUserStatus(int $userId): void
    {
        try {
            $user = User::role('teacher')->findOrFail($userId);
            $user->update(['status' => !$user->status]);

            $this->alert('success', 'OK', [
                'toast' => true, 'position' => 'center',
                'timer' => 1600, 'showConfirmButton' => false,
                'text' => 'Statut mis à jour.',
            ]);
        } catch (\Throwable $e) {
            $this->alert('error', 'Erreur', [
                'toast' => true, 'position' => 'center',
                'timer' => 2000, 'showConfirmButton' => false,
                'text' => 'Mise à jour impossible.',
            ]);
        }
    }

    public function render()
    {
        $teachers = User::query()
            ->with(['teacherNiveaux'])
            ->role('teacher')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->niveau_filter, function ($query) {
                $query->whereHas('teacherNiveaux', fn ($q) => $q->where('niveaux.id', $this->niveau_filter));
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-teacher', [
            'teachers' => $teachers,
            'niveaux' => Niveau::query()->where('status', true)->orderBy('name')->get(),
            'type' => 'teacher',
        ]);
    }
}
