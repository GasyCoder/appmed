<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Profil;
use App\Models\Parcour;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserAccountCreated;
use App\Notifications\TeacherAccountCreated;

class UsersTeacher extends Component
{
    use WithPagination;

    // Propriétés de recherche et filtrage
    public $search = '';
    public $perPage = 10;

    // Propriétés du modal
    public $showUserModal = false;

    // Propriétés du formulaire enseignant
    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $status = true;

    // Propriétés pour enseignant
    public $selectedTeacherNiveaux = [];
    public $selectedTeacherParcours = [];

    // Propriétés du profil
    public $grade;
    public $sexe;
    public $telephone;
    public $departement = null;
    public $ville;
    public $adresse;
    public $niveau_filter = '';
    public $parcour_filter = '';
    public $activeTab = 'info';
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
            // 'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'status' => 'boolean',

            // Règles pour le profil
            'grade' => 'nullable|string|max:255',
            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',

            // Règles pour enseignant
            'selectedTeacherNiveaux' => 'required|array|min:1',
            'selectedTeacherParcours' => 'required|array|min:1',
        ];
    }

    // Messages de validation personnalisés
    protected $messages = [
        'name.required' => 'Le nom est requis',
        'email.required' => 'L\'email est requis',
        'email.email' => 'L\'email doit être valide',
        'email.unique' => 'Cet email est déjà utilisé',
        // 'password.required' => 'Le mot de passe est requis',
        'selectedTeacherNiveaux.required' => 'Sélectionnez au moins un niveau d\'enseignement',
        'selectedTeacherParcours.required' => 'Sélectionnez au moins un parcours d\'enseignement',
    ];

    // Création ou mise à jour d'un enseignant
    public function createTeacher()
    {
        $this->isLoading = true;

        try {
            DB::beginTransaction();

            // Générer un token sécurisé pour la création du mot de passe
            $token = Str::random(64);

            // Générer un mot de passe temporaire lisible
            $temporaryPassword = Str::random(8);

            // Préparation des données de l'utilisateur
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
                'password' => Hash::make($temporaryPassword),
                'email_verified_at' => now()  // Email vérifié par défaut
            ];

            if ($this->userId) {
                // Mise à jour d'un utilisateur existant
                $user = User::findOrFail($this->userId);
                unset($userData['password']); // Ne pas mettre à jour le mot de passe
                unset($userData['email_verified_at']); // Ne pas mettre à jour la vérification email
                $user->update($userData);

                // Synchronisation des niveaux et parcours
                $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);
                $user->teacherParcours()->sync($this->selectedTeacherParcours);

                $message = 'Enseignant mis à jour avec succès';
            } else {
                // Création d'un nouvel utilisateur
                $user = User::create($userData);

                // Enregistrement du token pour la création du mot de passe
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'token' => Hash::make($token),
                        'created_at' => now()
                    ]
                );

                // Attribution du rôle enseignant
                $user->assignRole('teacher');

                // Synchronisation des niveaux et parcours
                $user->teacherNiveaux()->sync($this->selectedTeacherNiveaux);
                $user->teacherParcours()->sync($this->selectedTeacherParcours);

                // Envoi de l'email avec le mot de passe temporaire
                $user->notify(new UserAccountCreated($token, $temporaryPassword));

                $message = 'Compte enseignant créé avec succès. Un email a été envoyé.';
            }

            // Création ou mise à jour du profil
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

            DB::commit();

            // Réinitialisation et fermeture du modal
            $this->reset();
            $this->showUserModal = false;

            // Alert de succès avec animation
            $this->alert('success', 'Succès !', [
                'position' => 'center',
                'timer' => 2000,
                'toast' => true, // Changé en true pour un style plus compact
                'timerProgressBar' => true,
                'showConfirmButton' => false,
                'text' => $this->userId
                    ? 'Enseignant mis à jour avec succès.'
                    : 'Compte enseignant créé. Un email a été envoyé.',
                'width' => '400', // Largeur réduite
                'padding' => '1em', // Padding réduit
                'customClass' => [
                    'popup' => 'custom-alert',
                    'title' => 'text-lg font-semibold mb-2',
                    'text' => 'text-sm'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création enseignant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Alert d'erreur
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 2000,
                'toast' => true,
                'timerProgressBar' => true,
                'showConfirmButton' => true,
                'showCancelButton' => false,
                'confirmButtonText' => 'OK',
                'text' => 'Une erreur est survenue lors de la création.',
                'width' => '400',
                'padding' => '1em',
                'customClass' => [
                    'popup' => 'custom-alert',
                    'title' => 'text-lg font-semibold mb-2',
                    'text' => 'text-sm'
                ]
            ]);



        } finally {
            $this->isLoading = false;
        }
    }

    // Édition d'un enseignant
    public function editTeacher($userId)
    {
        try {
            $user = User::with(['teacherNiveaux', 'teacherParcours', 'profil'])
                       ->role('teacher')
                       ->findOrFail($userId);

            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = $user->status;
            $this->selectedTeacherNiveaux = $user->teacherNiveaux->pluck('id')->toArray();
            $this->selectedTeacherParcours = $user->teacherParcours->pluck('id')->toArray();

            // Charger les données du profil
            if ($user->profil) {
                $this->grade = $user->profil->grade;
                $this->sexe = $user->profil->sexe;
                $this->telephone = $user->profil->telephone;
                $this->departement = $user->profil->departement;
                $this->ville = $user->profil->ville;
                $this->adresse = $user->profil->adresse;
            }

            $this->showUserModal = true;

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de l\'enseignant:', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            $this->dispatch('notify', [
                'message' => 'Erreur lors du chargement de l\'enseignant',
                'type' => 'error'
            ]);
        }
    }

    // Supprimer un enseignant
    public function deleteUser($userId)
    {
        try {
            $user = User::role('teacher')->findOrFail($userId);
            $user->delete();

            $this->dispatch('notify', [
                'message' => 'Enseignant supprimé avec succès',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'enseignant:', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            $this->dispatch('notify', [
                'message' => 'Erreur lors de la suppression de l\'enseignant',
                'type' => 'error'
            ]);
        }
    }

    // Confirmer la suppression
    public function confirmDelete($userId)
    {
        $this->dispatch('showDeleteConfirmation', ['userId' => $userId]);
    }

    // Changer le statut d'un enseignant
    public function toggleUserStatus($userId)
    {
        try {
            $user = User::role('teacher')->findOrFail($userId);
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
            'userId', 'name', 'email', 'status', 'showUserModal',
            'grade', 'sexe', 'telephone', 'departement', 'ville', 'adresse',
            'selectedTeacherNiveaux', 'selectedTeacherParcours'
        ]);
        $this->resetValidation();
    }

    // Rendu du composant
    public function render()
    {
        $teachers = User::query()
            ->with(['teacherNiveaux', 'teacherParcours', 'profil'])
            ->role('teacher')
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
            ->when($this->niveau_filter, function ($query) {
                $query->whereHas('teacherNiveaux', function ($q) {
                    $q->where('niveaux.id', $this->niveau_filter);
                });
            })
            ->when($this->parcour_filter, function ($query) {
                $query->whereHas('teacherParcours', function ($q) {
                    $q->where('parcours.id', $this->parcour_filter);
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.users-teacher', [
            'teachers' => $teachers,
            'niveaux' => Niveau::where('status', true)->get(),
            'parcours' => Parcour::where('status', true)->get(),
            'type' => 'teacher'
        ]);
    }
}
