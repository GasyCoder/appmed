<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Profil;
use App\Models\Parcour;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UsersManagement extends Component
{
    use WithPagination;

    // Propriétés de recherche et filtrage
    public $search = '';
    public $role = '';
    public $perPage = 10;

    // Propriétés du modal
    public $showUserModal = false;

    // Propriétés du formulaire utilisateur
    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedRole = '';
    public $status = true;

    // Propriétés pour étudiant
    public $niveau_id;
    public $parcour_id;

    // Propriétés pour enseignant
    public $selectedTeacherNiveaux = [];
    public $selectedTeacherParcours = [];

    // Propriétés du profil
    public $grade;
    public $sexe;
    public $telephone;
    public $departement;
    public $ville;
    public $adresse;

    // État du chargement
    public $isLoading = false;

    // Écouteurs d'événements
    protected $listeners = [
        'deleteConfirmed' => 'deleteUser',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        abort_if(!auth()->user()->hasRole('admin'), 403, 'Non autorisé.');
    }

    // Règles de validation
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'selectedRole' => 'required|in:teacher,student',
            'status' => 'boolean',

            // Règles pour le profil
            'grade' => 'nullable|string|max:255',
            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',

            // Règles conditionnelles pour étudiant
            'niveau_id' => Rule::when($this->selectedRole === 'student', 'required|exists:niveaux,id'),
            'parcour_id' => Rule::when($this->selectedRole === 'student', 'required|exists:parcours,id'),

            // Règles conditionnelles pour enseignant
            'selectedTeacherNiveaux' => Rule::when($this->selectedRole === 'teacher', 'required|array|min:1'),
            'selectedTeacherParcours' => Rule::when($this->selectedRole === 'teacher', 'required|array|min:1'),
        ];
    }

    // Messages de validation personnalisés
    protected $messages = [
        'name.required' => 'Le nom est requis',
        'email.required' => 'L\'email est requis',
        'email.email' => 'L\'email doit être valide',
        'email.unique' => 'Cet email est déjà utilisé',
        'password.required' => 'Le mot de passe est requis',
        'selectedRole.required' => 'Le rôle est requis',
        'niveau_id.required' => 'Le niveau est requis pour un étudiant',
        'parcour_id.required' => 'Le parcours est requis pour un étudiant',
        'selectedTeacherNiveaux.required' => 'Sélectionnez au moins un niveau d\'enseignement',
        'selectedTeacherParcours.required' => 'Sélectionnez au moins un parcours d\'enseignement',
    ];

    // Création ou mise à jour d'un utilisateur
    public function createUser()
    {
        $this->isLoading = true;
        $validatedData = $this->validate();

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
                'niveau_id' => $this->selectedRole === 'student' ? $this->niveau_id : null,
                'parcour_id' => $this->selectedRole === 'student' ? $this->parcour_id : null,
            ];

            if (!$this->userId || $this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->update($userData);
            } else {
                $user = User::create($userData);
            }

            // Gestion du profil
            $profileData = [
                'grade' => $this->grade,
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

            // Gestion des rôles et relations
            $user->syncRoles([$this->selectedRole]);

            if ($this->selectedRole === 'teacher') {
                $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);
                $user->teacherParcours()->sync($this->selectedTeacherParcours);
            }

            DB::commit();

            $this->reset();
            $this->dispatch('notify', [
                'message' => $this->userId ? 'Utilisateur mis à jour avec succès' : 'Utilisateur créé avec succès',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création/mise à jour de l\'utilisateur:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'message' => 'Une erreur est survenue',
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    // Édition d'un utilisateur
    public function editUser($userId)
    {
        try {
            $user = User::with(['roles', 'teacherNiveaux', 'teacherParcours', 'profil'])
                       ->findOrFail($userId);

            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = $user->status;
            $this->niveau_id = $user->niveau_id;
            $this->parcour_id = $user->parcour_id;
            $this->selectedRole = $user->roles->first()?->name;

            // Charger les données du profil
            if ($user->profil) {
                $this->grade = $user->profil->grade;
                $this->sexe = $user->profil->sexe;
                $this->telephone = $user->profil->telephone;
                $this->departement = $user->profil->departement;
                $this->ville = $user->profil->ville;
                $this->adresse = $user->profil->adresse;
            }

            if ($user->hasRole('teacher')) {
                $this->selectedTeacherNiveaux = $user->teacherNiveaux->pluck('id')->toArray();
                $this->selectedTeacherParcours = $user->teacherParcours->pluck('id')->toArray();
            }

            $this->showUserModal = true;

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de l\'utilisateur:', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            $this->dispatch('notify', [
                'message' => 'Erreur lors du chargement de l\'utilisateur',
                'type' => 'error'
            ]);
        }
    }

    // Supprimer un utilisateur
    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();

            $this->dispatch('notify', [
                'message' => 'Utilisateur supprimé avec succès',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'utilisateur:', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            $this->dispatch('notify', [
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
                'type' => 'error'
            ]);
        }
    }

    // Confirmer la suppression
    public function confirmDelete($userId)
    {
        $this->dispatch('showDeleteConfirmation', ['userId' => $userId]);
    }

    // Changer le statut d'un utilisateur
    public function toggleUserStatus($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->update(['status' => !$user->status]);

            $this->dispatch('notify', [
                'message' => 'Statut mis à jour avec succès',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de la mise à jour du statut',
                'type' => 'error'
            ]);
        }
    }

    // Réinitialiser le formulaire
    public function resetForm()
    {
        $this->reset([
            'userId', 'name', 'email', 'password', 'selectedRole',
            'niveau_id', 'parcour_id', 'status', 'showUserModal',
            'grade', 'sexe', 'telephone', 'departement', 'ville', 'adresse',
            'selectedTeacherNiveaux', 'selectedTeacherParcours'
        ]);
        $this->resetValidation();
    }

    // Mise à jour du rôle sélectionné
    public function updatedSelectedRole()
    {
        if ($this->selectedRole !== 'student') {
            $this->reset(['niveau_id', 'parcour_id']);
        }
        if ($this->selectedRole !== 'teacher') {
            $this->reset(['selectedTeacherNiveaux', 'selectedTeacherParcours']);
        }
    }

    // Rendu du composant
    public function render()
    {
        $users = User::query()
            ->with(['roles', 'niveau', 'parcour', 'profil', 'teacherNiveaux', 'teacherParcours'])
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['teacher', 'student']);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhereHas('profil', function ($p) {
                          $p->where('grade', 'like', "%{$this->search}%")
                            ->orWhere('departement', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->role, function ($query) {
                $query->role($this->role);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-management', [
            'users' => $users,
            'niveaux' => Niveau::where('status', true)->get(),
            'parcours' => Parcour::where('status', true)->get(),
            'roles' => Role::whereIn('name', ['teacher', 'student'])->get() // Ajout des rôles
        ]);
    }
}
