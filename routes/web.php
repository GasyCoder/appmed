<?php

use App\Models\Document;
use App\Livewire\Admin\Niveaux;
use App\Livewire\Admin\Parcours;
use App\Livewire\Admin\Semestres;
use App\Livewire\Teacher\Documents;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Teacher\DocumentEdit;
use App\Livewire\Admin\UsersManagement;
use App\Livewire\Student\EnseignantView;
use App\Livewire\Teacher\DocumentUpload;
use App\Livewire\Student\StudentDocument;
use App\Livewire\Teacher\TeacherDashboard;
use App\Livewire\Admin\DocumentsManagement;

/*
|--------------------------------------------------------------------------
| Redirections de base
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');
Route::redirect('/register', '/login');

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Route de redirection du dashboard selon le rôle
    Route::get('/dashboard', function () {
        return match (true) {
            auth()->user()->hasRole('admin') => redirect()->route('adminEspace'),
            auth()->user()->hasRole('teacher') => redirect()->route('teacherEspace'),
            auth()->user()->hasRole('student') => redirect()->route('studentEspace'),
            default => redirect()->route('login')
        };
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Routes Administrateur
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
    ->middleware('role:admin')
    ->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('adminEspace');
        Route::get('/users', UsersManagement::class)->name('admin.users');
        Route::get('/documents', DocumentsManagement::class)->name('admin.documents');
        Route::get('/niveaux', Niveaux::class)->name('admin.niveau');
        Route::get('/parcours', Parcours::class)->name('admin.parcour');
        Route::get('/semestres', Semestres::class)->name('admin.semestre');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Enseignant
    |--------------------------------------------------------------------------
    */
    Route::prefix('teacher')
        ->middleware('role:teacher')
        ->group(function () {
            Route::get('/dashboard', TeacherDashboard::class)->name('teacherEspace');
            Route::get('/documents', Documents::class)->name('document.teacher');
            Route::get('/documents/upload', DocumentUpload::class)->name('document.upload');
            Route::get('/documents/{document}/edit', DocumentEdit::class)->name('document.edit');
        });

    /*
    |--------------------------------------------------------------------------
    | Routes Étudiant
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')
        ->middleware('role:student')
        ->group(function () {
            Route::get('/dashboard', StudentDocument::class)->name('studentEspace');
            Route::get('/mes-enseignants', EnseignantView::class)->name('student.myTeacher');
        });


    // Route commune pour l'incrémentation des vues des documents
    Route::post('/document/{document}/increment-view', function(Document $document) {
        try {
            $document->incrementViewCount();
            return response()->json([
                'success' => true,
                'viewCount' => $document->view_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('document.increment-view');
});
