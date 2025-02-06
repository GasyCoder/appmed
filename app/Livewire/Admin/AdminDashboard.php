<?php
namespace App\Livewire\Admin;

use App\Models\Niveau;
use App\Models\User;
use App\Models\Parcour;
use Livewire\Component;
use App\Models\Category;
use App\Models\Document;
use Livewire\WithPagination;

class AdminDashboard extends Component
{
    use WithPagination;

    protected $stats;
    protected $recentDocuments;

    public function mount()
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        // Statistiques générales
        $this->stats = [
            'users_count' => User::count(),
            'teachers_count' => User::role('teacher')->count(),
            'students_count' => User::role('student')->count(),

            'parcours_count' => Parcour::count(),
            'niveau_count' => Niveau::count(),

            'documents_count' => Document::count(),
            'pending_documents' => Document::where('is_actif', false)->count(),
        ];

        // Documents récents
        $this->recentDocuments = Document::with('uploader')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'stats' => $this->stats,
            'recentDocuments' => $this->recentDocuments
        ]);
    }
}
