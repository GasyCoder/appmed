<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Profil;
use App\Models\Parcour;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\AuthorizedEmail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\UserAccountCreatedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserAccountCreated;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UsersStudent extends Component
{
    use WithPagination;
    use LivewireAlert;

    public $search = '';
    public $perPage = 10;

    public $showUserModal = false;

    // User fields
    public $userId;
    public $name = '';
    public $email = '';
    public $status = true;

    public $niveau_id;

    // Parcours auto (si unique)
    public $parcour_id = null;
    public $defaultParcourId = null;

    // Profil
    public $sexe;
    public $telephone;
    public $departement;
    public $ville;
    public $adresse;

    // Filters
    public $niveau_filter = '';

    public $isLoading = false;

    protected $listeners = [
        'refresh' => '$refresh',
        'deleteConfirmed' => 'deleteUser',
    ];

    public function mount()
    {
        abort_if(!Auth::user()->hasRole('admin'), 403, 'Non autorisé.');

        // Parcours auto si unique (le premier actif)
        $this->defaultParcourId = Parcour::where('status', true)->value('id');
        $this->parcour_id = $this->defaultParcourId;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'status' => 'boolean',

            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'nullable|exists:parcours,id',

            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'name.required' => 'Le nom est requis',
        'email.required' => "L'email est requis",
        'email.email' => "L'email doit être valide",
        'email.unique' => 'Cet email est déjà utilisé',
        'niveau_id.required' => 'Le niveau est requis',
    ];

    public function createStudent()
    {
        $this->validate();
        $this->isLoading = true;

        $isUpdate = !empty($this->userId);

        try {
            DB::beginTransaction();

            $parcourId = $this->parcour_id ?: $this->defaultParcourId;

            // Token + mdp temporaire uniquement à la création
            $token = Str::random(64);
            $temporaryPassword = Str::random(8);

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'status' => (bool) $this->status,
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $parcourId,
            ];

            if ($isUpdate) {
                $user = User::role('student')->findOrFail($this->userId);
                $user->update($userData);
            } else {
                $userData['password'] = Hash::make($temporaryPassword);
                $userData['email_verified_at'] = now();

                $user = User::create($userData);
                $user->assignRole('student');

                AuthorizedEmail::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'is_registered' => false,
                        'verification_token' => null,
                        'token_expires_at' => null,
                    ]
                );

                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => Hash::make($token), 'created_at' => now()]
                );

                Mail::to($user->email)->send(
                    new UserAccountCreatedMail(
                        name: $user->name,
                        email: $user->email,
                        token: $token,
                        temporaryPassword: $temporaryPassword,
                        sexe: $this->sexe,
                        appName: 'EPIRC',
                        orgName: 'Faculté de Médecine — Université de Mahajanga',
                    )
                );

            }

            $profileData = [
                'sexe' => $this->sexe,
                'telephone' => $this->telephone,
                'departement' => $this->departement,
                'ville' => $this->ville,
                'adresse' => $this->adresse,
            ];

            if ($user->profil) {
                $user->profil->update($profileData);
            } else {
                $user->profil()->create($profileData);
            }

            DB::commit();

            $this->resetForm();

            $this->alert('success', 'Succès', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2500,
                'timerProgressBar' => true,
                'text' => $isUpdate
                    ? 'Étudiant mis à jour avec succès.'
                    : 'Compte étudiant créé. Un email a été envoyé.',
                'showConfirmButton' => false,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur création étudiant:', [
                'error' => $e->getMessage(),
            ]);

            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 3000,
                'timerProgressBar' => true,
                'text' => 'Une erreur est survenue lors de l’enregistrement.',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function editStudent($userId)
    {
        try {
            $user = User::with(['niveau', 'parcour', 'profil'])
                ->role('student')
                ->findOrFail($userId);

            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = (bool) $user->status;
            $this->niveau_id = $user->niveau_id;
            $this->parcour_id = $user->parcour_id ?: $this->defaultParcourId;

            if ($user->profil) {
                $this->sexe = $user->profil->sexe;
                $this->telephone = $user->profil->telephone;
                $this->departement = $user->profil->departement;
                $this->ville = $user->profil->ville;
                $this->adresse = $user->profil->adresse;
            }

            $this->showUserModal = true;

        } catch (\Exception $e) {
            Log::error("Erreur editStudent", ['error' => $e->getMessage(), 'userId' => $userId]);

            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2500,
                'text' => 'Impossible de charger les données de cet étudiant.',
            ]);
        }
    }

    public function confirmDelete($userId)
    {
        $this->alert('warning', 'Confirmation', [
            'text' => 'Êtes-vous sûr de vouloir supprimer cet étudiant ?',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Oui, supprimer',
            'showCancelButton' => true,
            'cancelButtonText' => 'Annuler',
            'onConfirmed' => 'deleteConfirmed',
            'data' => ['userId' => $userId],
        ]);
    }

    public function deleteUser($data = null)
    {
        $userId = is_array($data) ? ($data['userId'] ?? null) : $data;

        if (!$userId) return;

        try {
            $user = User::role('student')->findOrFail($userId);
            $user->delete();

            $this->alert('success', 'Supprimé', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2000,
                'text' => 'Étudiant supprimé avec succès.',
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur deleteUser", ['error' => $e->getMessage(), 'userId' => $userId]);

            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2500,
                'text' => 'Erreur lors de la suppression.',
            ]);
        }
    }

    public function toggleUserStatus($userId)
    {
        try {
            $user = User::role('student')->findOrFail($userId);
            $user->update(['status' => !$user->status]);

        } catch (\Exception $e) {
            $this->alert('error', 'Erreur', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2000,
                'text' => 'Erreur lors de la mise à jour du statut.',
            ]);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'userId', 'name', 'email', 'status',
            'niveau_id',
            'sexe', 'telephone', 'departement', 'ville', 'adresse',
            'showUserModal',
        ]);

        // réapplique le parcours auto
        $this->parcour_id = $this->defaultParcourId;

        $this->resetValidation();
    }

    public function render()
    {
        $students = User::query()
            ->with(['niveau', 'parcour', 'profil'])
            ->role('student')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhereHas('profil', function ($p) {
                            $p->where('departement', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->niveau_filter, function ($query) {
                $query->where('niveau_id', $this->niveau_filter);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-student', [
            'students' => $students,
            'niveaux' => Niveau::where('status', true)->get(),
            'type' => 'student',
        ]);
    }
}
