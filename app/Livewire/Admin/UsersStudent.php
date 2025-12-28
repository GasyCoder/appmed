<?php

namespace App\Livewire\Admin;

use App\Mail\UserAccountCreatedMail;
use App\Models\AuthorizedEmail;
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

class UsersStudent extends Component
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
    public string $niveau_id = '';
    public bool $status = true;

    // Parcours auto
    public ?int $defaultParcourId = null;

    // Delete confirm
    public ?int $pendingDeleteUserId = null;

    protected $listeners = [
        'refresh' => '$refresh',
        'deleteConfirmed' => 'deleteUser',
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
            'niveau_id' => ['required', 'integer', 'exists:niveaux,id'],
            'status' => ['boolean'],
        ];
    }

    protected array $messages = [
        'name.required' => 'Le nom est requis.',
        'email.required' => "L'email est requis.",
        'email.email' => "L'email doit être valide.",
        'email.unique' => 'Cet email est déjà utilisé.',
        'niveau_id.required' => 'Le niveau est requis.',
        'niveau_id.exists' => 'Niveau invalide.',
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }
    public function updatedNiveauFilter(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm(keepModalOpen: true);
    }

    public function resetForm(bool $keepModalOpen = false): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'niveau_id',
            'status',
            'isLoading',
            'pendingDeleteUserId',
        ]);

        $this->status = true;
        $this->resetValidation();
        $this->showUserModal = $keepModalOpen;
    }

    /**
     * Create OR Update (simplifié)
     * - Admin ne gère plus Profil ici.
     * - Parcour assigné automatiquement via defaultParcourId.
     */
    public function createStudent(): void
    {
        $this->isLoading = true;

        try {
            $this->validate();

            if (!$this->defaultParcourId) {
                $this->alert('error', 'Erreur', [
                    'toast' => true,
                    'position' => 'center',
                    'timer' => 2600,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'OK',
                    'text' => 'Aucun parcours actif trouvé (table parcours vide).',
                ]);
                return;
            }

            $isUpdate = (bool) $this->userId;

            DB::transaction(function () use ($isUpdate) {

                if ($isUpdate) {
                    $user = User::role('student')->findOrFail($this->userId);

                    $user->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'status' => (bool) $this->status,
                        'niveau_id' => (int) $this->niveau_id,
                        'parcour_id' => (int) $this->defaultParcourId,
                    ]);

                    return;
                }

                // CREATE
                $token = Str::random(64);
                $temporaryPassword = Str::random(10);

                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'status' => (bool) $this->status,
                    'niveau_id' => (int) $this->niveau_id,
                    'parcour_id' => (int) $this->defaultParcourId,
                    'password' => Hash::make($temporaryPassword),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('student');

                // AuthorizedEmail (si tu utilises cette table)
                AuthorizedEmail::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'is_registered' => false,
                        'verification_token' => null,
                        'token_expires_at' => null,
                    ]
                );

                // Reset password token (48h côté mail)
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => Hash::make($token), 'created_at' => now()]
                );

                Mail::to($user->email)->send(new UserAccountCreatedMail(
                    name: $user->name,
                    email: $user->email,
                    token: $token,
                    temporaryPassword: $temporaryPassword,
                    sexe: null,
                    appName: 'EPIRC',
                    orgName: 'Faculté de Médecine — Université de Mahajanga',
                ));
            });

            $msg = $isUpdate
                ? 'Étudiant mis à jour.'
                : 'Compte étudiant créé. Email envoyé.';

            $this->resetForm();

            $this->alert('success', 'Succès', [
                'toast' => true,
                'position' => 'center',
                'timer' => 2200,
                'timerProgressBar' => true,
                'showConfirmButton' => false,
                'text' => $msg,
            ]);

        } catch (\Throwable $e) {
            Log::error('UsersStudent createStudent error', ['error' => $e->getMessage()]);

            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'center',
                'timer' => 2800,
                'timerProgressBar' => true,
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'text' => $e->getMessage(),
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function editStudent(int $userId): void
    {
        try {
            $user = User::query()
                ->role('student')
                ->with(['niveau'])
                ->findOrFail($userId);

            $this->userId = $user->id;
            $this->name = (string) $user->name;
            $this->email = (string) $user->email;
            $this->status = (bool) $user->status;
            $this->niveau_id = (string) $user->niveau_id;

            $this->resetValidation();
            $this->showUserModal = true;

        } catch (\Throwable $e) {
            Log::error('UsersStudent editStudent error', ['error' => $e->getMessage(), 'user_id' => $userId]);

            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'center',
                'timer' => 2200,
                'showConfirmButton' => false,
                'text' => 'Impossible de charger les données de cet étudiant.',
            ]);
        }
    }

    /**
     * Confirmation UI avant suppression
     */
    public function confirmDelete(int $userId): void
    {
        $this->pendingDeleteUserId = $userId;

        $this->alert('warning', 'Confirmer la suppression', [
            'text' => 'Voulez-vous vraiment supprimer cet étudiant ? Cette action est irréversible.',
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

    /**
     * Listener après confirmation
     */
    public function deleteUser($payload = null): void
    {
        $id = 0;

        if (is_numeric($payload)) $id = (int) $payload;

        if (is_array($payload)) {
            $id = (int) (
                $payload['id']
                ?? $payload['userId']
                ?? ($payload['data']['id'] ?? 0)
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
            $user = User::role('student')->findOrFail($id);
            $user->delete();

            $this->pendingDeleteUserId = null;

            $this->alert('success', 'Supprimé', [
                'toast' => true,
                'position' => 'center',
                'timer' => 2000,
                'showConfirmButton' => false,
                'text' => 'Étudiant supprimé.',
            ]);

        } catch (\Throwable $e) {
            Log::error('UsersStudent deleteUser error', ['error' => $e->getMessage(), 'user_id' => $id]);

            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'center',
                'timer' => 2400,
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'text' => 'Suppression impossible.',
            ]);
        }
    }

    public function toggleUserStatus(int $userId): void
    {
        try {
            $user = User::role('student')->findOrFail($userId);
            $user->update(['status' => !$user->status]);

            $this->alert('success', 'OK', [
                'toast' => true,
                'position' => 'center',
                'timer' => 1600,
                'showConfirmButton' => false,
                'text' => 'Statut mis à jour.',
            ]);
        } catch (\Throwable $e) {
            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'center',
                'timer' => 2000,
                'showConfirmButton' => false,
                'text' => 'Erreur lors de la mise à jour du statut.',
            ]);
        }
    }

    public function render()
    {
        $students = User::query()
            ->with(['niveau'])
            ->role('student')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->niveau_filter, function ($query) {
                $query->where('niveau_id', (int) $this->niveau_filter);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-student', [
            'students' => $students,
            'niveaux' => Niveau::query()->where('status', true)->orderBy('name')->get(),
            'type' => 'student',
        ]);
    }
}
