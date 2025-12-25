<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementsIndex extends Component
{
    public $items;

    public function mount(): void
    {
        $u = Auth::user();

        $this->items = Announcement::query()
            ->active()
            ->forUser($u)
            ->latest('created_at')
            ->get();
    }

    public function render()
    {
        // ✅ Layout: student => layouts.student, sinon => layouts.app
        $layout = Auth::user()?->hasRole('student') ? 'layouts.student' : 'layouts.app';

        return view('livewire.shared.announcements-index');
    }
}
