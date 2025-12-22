<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuthorizedEmail;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AuthorizedEmails extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $showModal = false;
    public $showBulkModal = false;
    
    public $email = '';
    public $bulkEmails = '';
    public $statusFilter = '';

    protected $rules = [
        'email' => 'required|email|unique:authorized_emails,email',
    ];

    protected $messages = [
        'email.required' => 'L\'email est requis',
        'email.email' => 'L\'email doit être valide',
        'email.unique' => 'Cet email est déjà autorisé',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addEmail()
    {
        $this->validate();

        try {
            AuthorizedEmail::create([
                'email' => strtolower(trim($this->email)),
            ]);

            $this->reset(['email', 'showModal']);
            
            $this->alert('success', 'Succès !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Email ajouté à la liste autorisée',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur ajout email autorisé: ' . $e->getMessage());
            
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Erreur lors de l\'ajout de l\'email',
            ]);
        }
    }

    public function addBulkEmails()
    {
        $this->validate([
            'bulkEmails' => 'required|string',
        ]);

        try {
            $emailsList = array_filter(
                array_map('trim', explode("\n", $this->bulkEmails))
            );

            $added = 0;
            $skipped = 0;

            foreach ($emailsList as $email) {
                $email = strtolower(trim($email));
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                if (AuthorizedEmail::where('email', $email)->exists()) {
                    $skipped++;
                    continue;
                }

                AuthorizedEmail::create(['email' => $email]);
                $added++;
            }

            $this->reset(['bulkEmails', 'showBulkModal']);
            
            $message = "{$added} email(s) ajouté(s)";
            if ($skipped > 0) {
                $message .= ", {$skipped} déjà existant(s)";
            }

            $this->alert('success', 'Succès !', [
                'position' => 'top-end',
                'timer' => 4000,
                'toast' => true,
                'text' => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur ajout emails en masse: ' . $e->getMessage());
            
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Erreur lors de l\'ajout des emails',
            ]);
        }
    }

    // ✅ NOUVELLE MÉTHODE - Basculer le statut d'inscription
    public function toggleRegistrationStatus($id)
    {
        try {
            $authorizedEmail = AuthorizedEmail::findOrFail($id);
            
            $newStatus = !$authorizedEmail->is_registered;
            $authorizedEmail->update(['is_registered' => $newStatus]);
            
            $statusText = $newStatus ? 'inscrit' : 'en attente';
            
            $this->alert('success', 'Statut modifié !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => "Email marqué comme {$statusText}",
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur modification statut: ' . $e->getMessage());
            
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Erreur lors de la modification du statut',
            ]);
        }
    }

    public function deleteEmail($id)
    {
        try {
            $authorizedEmail = AuthorizedEmail::findOrFail($id);
            $authorizedEmail->delete();
            
            $this->alert('success', 'Supprimé !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Email retiré de la liste',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression email autorisé: ' . $e->getMessage());
            
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Erreur lors de la suppression',
            ]);
        }
    }

    public function render()
    {
        $authorizedEmails = AuthorizedEmail::query()
            ->when($this->search, function($query) {
                $query->where('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== '', function($query) {
                $query->where('is_registered', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => AuthorizedEmail::count(),
            'registered' => AuthorizedEmail::where('is_registered', true)->count(),
            'pending' => AuthorizedEmail::where('is_registered', false)->count(),
        ];

        return view('livewire.admin.authorized-emails', [
            'authorizedEmails' => $authorizedEmails,
            'stats' => $stats,
        ]);
    }
}