<?php

namespace App\Livewire\Admin;

use App\Mail\UserAccountCreatedMail;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\User;
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

    // Tabs (modal)
    public string $activeTab = 'personal';

    // Form fields
    public ?int $userId = null;

    // Information personnel
    public string $name = '';
    public string $email = '';
    public ?string $telephone = null;
    public ?string $sexe = null; // homme|femme|null

    // Pédagogique
    public ?string $grade = null;
    public array $selectedTeacherNiveaux = [];
    public ?string $departement = null; // spécialité (optionnel)

    // Account
    public bool $status = true;

    // Parcours unique par défaut
    public ?int $defaultParcourId = null;

    protected $listeners = [
        'deleteConfirmed' => 'deleteUser',
        'refresh' => '$refresh',
    ];

    public function mount(): void
    {
        abort_if(!auth()->user()?->hasRole('admin'), 403, 'Non autorisé.');

        // Parcours unique => premier actif
        $this->defaultParcourId = Parcour::query()
            ->where('status', true)
            ->orderBy('id')
            ->value('id');
    }

    protected function rules(): array
    {
        return [
            // Personal
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'telephone' => 'nullable|string|max:20',
            'sexe' => 'nullable|in:homme,femme',

            // Pedago
            'grade' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'selectedTeacherNiveaux' => 'required|array|min:1',

            // Account
            'status' => 'boolean',
        ];
    }

    protected array $messages = [
        'name.required' => 'Le nom est requis.',
        'email.required' => 'L\'email est requis.',
        'email.email' => 'L\'email doit être valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'selectedTeacherNiveaux.required' => 'Sélectionnez au moins un niveau.',
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }
    public function updatedNiveauFilter(): void { $this->resetPage(); }

    public function resetForm(): void
    {
        $this->reset([
            'userId','name','email','telephone','sexe',
            'grade','selectedTeacherNiveaux','departement',
            'status','showUserModal','isLoading'
        ]);

        $this->status = true;
        $this->activeTab = 'personal';
        $this->resetValidation();
    }

    protected function formattedTeacherName(User $user): string
    {
        $grade = $user->profil?->grade;
        return $grade ? "{$grade}. {$user->name}" : $user->name;
    }

    public function createTeacher(): void
    {
        $this->isLoading = true;
        $this->validate();
        $isUpdate = (bool) $this->userId;

        try {
            DB::transaction(function () use ($isUpdate) {

                if ($isUpdate) {
                    $user = User::role('teacher')->findOrFail($this->userId);

                    $user->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'status' => (bool) $this->status,
                    ]);

                    $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);

                    if ($this->defaultParcourId) {
                        $user->teacherParcours()->sync([$this->defaultParcourId]);
                    }

                    $user->profil()->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'telephone' => $this->telephone,
                            'sexe' => $this->sexe,
                            'grade' => $this->grade,
                            'departement' => $this->departement,
                        ]
                    );

                    return;
                }

                // CREATE
                $token = Str::random(64);
                $temporaryPassword = Str::random(8);

                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'status' => (bool) $this->status,
                    'password' => Hash::make($temporaryPassword),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('teacher');

                $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);

                if ($this->defaultParcourId) {
                    $user->teacherParcours()->sync([$this->defaultParcourId]);
                }

                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => Hash::make($token), 'created_at' => now()]
                );

                $user->profil()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'telephone' => $this->telephone,
                        'sexe' => $this->sexe,
                        'grade' => $this->grade,
                        'departement' => $this->departement,
                    ]
                );

                Mail::to($user->email)->send(new UserAccountCreatedMail(
                    name: $this->formattedTeacherName($user),
                    email: $user->email,
                    token: $token,
                    temporaryPassword: $temporaryPassword,
                    validityHours: 48,
                    sexe: $this->sexe,
                    appName: 'EPIRC',
                    orgName: 'Faculté de Médecine — Université de Mahajanga'
                ));
            });

            $msg = $isUpdate ? 'Enseignant mis à jour.' : 'Compte enseignant créé. Email envoyé.';

            $this->resetForm();
            $this->showUserModal = false;

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
                'timer' => 2600, 'timerProgressBar' => true,
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'text' => 'Une erreur est survenue.',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function editTeacher(int $userId): void
    {
        try {
            $user = User::with(['teacherNiveaux', 'profil'])
                ->role('teacher')
                ->findOrFail($userId);

            $this->userId = $user->id;

            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = (bool) $user->status;

            $this->telephone = $user->profil?->telephone;
            $this->sexe = $user->profil?->sexe;

            $this->grade = $user->profil?->grade;
            $this->departement = $user->profil?->departement;

            $this->selectedTeacherNiveaux = $user->teacherNiveaux->pluck('id')->toArray();

            $this->activeTab = 'personal';
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

    public function deleteUser(int $userId): void
    {
        try {
            $user = User::role('teacher')->findOrFail($userId);
            $user->delete();

            $this->alert('success', 'Supprimé', [
                'toast' => true, 'position' => 'center',
                'timer' => 2000, 'showConfirmButton' => false,
                'text' => 'Enseignant supprimé.',
            ]);
        } catch (\Throwable $e) {
            Log::error('UsersTeacher deleteUser error', ['error' => $e->getMessage(), 'user_id' => $userId]);

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
            ->with(['teacherNiveaux', 'profil'])
            ->role('teacher')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhereHas('profil', function ($p) {
                          $p->where('grade', 'like', "%{$this->search}%")
                            ->orWhere('departement', 'like', "%{$this->search}%")
                            ->orWhere('telephone', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->niveau_filter, function ($query) {
                $query->whereHas('teacherNiveaux', fn ($q) => $q->where('niveaux.id', $this->niveau_filter));
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-teacher', [
            'teachers' => $teachers,
            'niveaux' => Niveau::query()->where('status', true)->get(),
            'type' => 'teacher',
        ]);
    }
}
