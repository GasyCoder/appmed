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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UsersStudent extends Component
{
    use WithPagination;

    // Propriétés de recherche et filtrage
    public $search = '';
    public $perPage = 10;

    // Propriétés du modal
    public $showUserModal = false;

    // Propriétés du formulaire étudiant
    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $status = true;
    public $niveau_id;
    public $parcour_id;

    // Propriétés du profil
    public $sexe;
    public $telephone;
    public $departement;
    public $ville;
    public $adresse;
    public $niveau_filter = '';
    public $parcour_filter = '';
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
            'status' => 'boolean',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',

            // Règles pour le profil
            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
        ];
    }

    // Messages de validation personnalisés
    protected $messages = [
        'name.required' => 'Le nom est requis',
        'email.required' => 'L\'email est requis',
        'email.email' => 'L\'email doit être valide',
        'email.unique' => 'Cet email est déjà utilisé',
        'password.required' => 'Le mot de passe est requis',
        'niveau_id.required' => 'Le niveau est requis',
        'parcour_id.required' => 'Le parcours est requis',
    ];

    // Création ou mise à jour d'un étudiant
    public function createStudent()
    {
        $this->isLoading = true;
        $validatedData = $this->validate();

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
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

            // Assignation du rôle étudiant
            $user->assignRole('student');

            DB::commit();

            $this->reset();
            $this->dispatch('notify', [
                'message' => $this->userId ? 'Étudiant mis à jour avec succès' : 'Étudiant créé avec succès',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création/mise à jour de l\'étudiant:', [
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

    // Édition d'un étudiant
    public function editStudent($userId)
    {
        try {
            $user = User::with(['niveau', 'parcour', 'profil'])
                       ->role('student')
                       ->findOrFail($userId);

            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = $user->status;
            $this->niveau_id = $user->niveau_id;
            $this->parcour_id = $user->parcour_id;

            // Charger les données du profil
            if ($user->profil) {
                $this->sexe = $user->profil->sexe;
                $this->telephone = $user->profil->telephone;
                $this->departement = $user->profil->departement;
                $this->ville = $user->profil->ville;
                $this->adresse = $user->profil->adresse;
            }

            $this->showUserModal = true;

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de l\'étudiant:', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            $this->dispatch('notify', [
                'message' => 'Erreur lors du chargement de l\'étudiant',
                'type' => 'error'
            ]);
        }
    }

    // Supprimer un étudiant
    public function deleteUser($userId)
    {
        try {
            $user = User::role('student')->findOrFail($userId);
            $user->delete();

            $this->dispatch('notify', [
                'message' => 'Étudiant supprimé avec succès',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'étudiant:', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            $this->dispatch('notify', [
                'message' => 'Erreur lors de la suppression de l\'étudiant',
                'type' => 'error'
            ]);
        }
    }

    // Confirmer la suppression
    public function confirmDelete($userId)
    {
        $this->dispatch('showDeleteConfirmation', ['userId' => $userId]);
    }

    // Changer le statut d'un étudiant
    public function toggleUserStatus($userId)
    {
        try {
            $user = User::role('student')->findOrFail($userId);
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
            'userId', 'name', 'email', 'password',
            'niveau_id', 'parcour_id', 'status', 'showUserModal',
            'sexe', 'telephone', 'departement', 'ville', 'adresse'
        ]);
        $this->resetValidation();
    }

    // Rendu du composant
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
            ->when($this->parcour_filter, function ($query) {
                $query->where('parcour_id', $this->parcour_filter);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-student', [
            'students' => $students,
            'niveaux' => Niveau::where('status', true)->get(),
            'parcours' => Parcour::where('status', true)->get(),
            'type' => 'student'
        ]);
    }
}
