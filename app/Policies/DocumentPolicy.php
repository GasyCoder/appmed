<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['teacher', 'student']);
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole('teacher')) {
            return (int) $document->uploaded_by === (int) $user->id;
        }

        // étudiant : on réutilise ta logique existante
        return $document->canAccess($user) && (bool) $document->is_actif;
    }

    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('teacher');
    }


    public function update(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasRole('teacher') && (int) $document->uploaded_by === (int) $user->id;
    }

    public function delete(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    public function toggleStatus(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    public function toggleArchive(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }
}
