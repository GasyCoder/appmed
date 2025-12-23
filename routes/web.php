<?php

use App\Models\Document;
use App\Livewire\Admin\Niveaux;
use App\Livewire\Admin\Parcours;
use App\Livewire\Admin\Semestres;
use App\Livewire\Pages\ComingSoon;
use App\Livewire\Teacher\Documents;
use App\Livewire\Admin\UsersStudent;
use App\Livewire\Admin\UsersTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\ScheduleUpload;
use App\Livewire\Teacher\DocumentEdit;
use App\Http\Controllers\PdfController;
use App\Livewire\Shared\ScheduleViewer;
use App\Livewire\Admin\AuthorizedEmails;
use App\Livewire\Student\EnseignantView;
use App\Livewire\Teacher\DocumentUpload;
use App\Livewire\Student\ScheduleStudent;
use App\Livewire\Student\StudentDocument;
use App\Livewire\Teacher\ScheduleTeacher;
use App\Livewire\Admin\ScheduleManagement;
use App\Livewire\Student\DashboardStudent;
use App\Livewire\Teacher\TeacherDashboard;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ScheduleController;
use App\Livewire\Programmes\ProgrammesIndex;
use App\Http\Controllers\Auth\RegisterFormController;
use App\Http\Controllers\Auth\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| Redirections de base
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');
Route::redirect('/register', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [EmailVerificationController::class, 'index'])
        ->name('inscription');

    Route::post('/inscription/verify', [EmailVerificationController::class, 'verifyEmailStudent'])
        ->name('email.verify');

    Route::get('/inscription/formulaire/{token}', [RegisterFormController::class, 'showRegistrationForm'])
        ->name('register.form');

    Route::post('/inscription/formulaire/{token}', [RegisterFormController::class, 'register'])
        ->name('register.store');
});

Route::get('/set-password/{token}', function ($token) {
    return view('auth.set-password', ['token' => $token]);
})->name('password.set')->middleware('signed');

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Route de redirection du dashboard selon le rôle
    Route::get('/dashboard', function () {
        return match (true) {
            Auth::user()->hasRole('admin') => redirect()->route('adminEspace'),
            Auth::user()->hasRole('teacher') => redirect()->route('teacherEspace'),
            Auth::user()->hasRole('student') => redirect()->route('studentEspace'),
            default => redirect()->route('login')
        };
    })->name('dashboard');

    Route::get('/view-pdf/{filename}', [PdfController::class, 'viewerPdf'])->name('pdf.viewer');
    Route::get('/pdf-content/{filename}', [PdfController::class, 'show'])->name('pdf.content');
    Route::get('/pdf/download/{filename}', [PdfController::class, 'download'])->name('pdf.download');
    Route::get('/pdf/serve/{filename}', [PdfController::class, 'serve'])->name('pdf.serve');
    Route::get('/pdf/viewer/{filename}', [PdfController::class, 'viewerPpt'])->name('pdf.viewerppt');

    Route::get('/documents/serve/{document}', [DocumentController::class, 'serve'])->name('document.serve');

    Route::view('/faq', 'support.faq')->name('faq');
    Route::view('/aide', 'support.help')->name('help');
    /*
    |--------------------------------------------------------------------------
    | Routes Administrateur
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/dashboard', AdminDashboard::class)->name('adminEspace');
            Route::get('/etudiants', UsersStudent::class)->name('admin.students');
            Route::get('/enseignants', UsersTeacher::class)->name('admin.teachers');
            Route::get('/niveaux', Niveaux::class)->name('admin.niveau');
            Route::get('/parcours', Parcours::class)->name('admin.parcour');
            Route::get('/semestres', Semestres::class)->name('admin.semestre');

            // ✅ NOUVELLES ROUTES EMPLOI DU TEMPS ADMIN
            Route::get('/emploi-du-temps', ScheduleManagement::class)->name('admin.timetable');
            Route::get('/emploi-du-temps/upload', ScheduleUpload::class)->name('admin.schedules.upload');

            Route::get('/authorized-emails', AuthorizedEmails::class)
            ->name('admin.authorized-emails');
        });

    /*
    |--------------------------------------------------------------------------
    | Routes Enseignant
    |--------------------------------------------------------------------------
    */
    Route::prefix('teacher')
        ->middleware(['role:teacher', 'document.access'])
        ->group(function () {
            Route::get('/dashboard', TeacherDashboard::class)->name('teacherEspace');
            Route::get('/documents', Documents::class)->name('document.teacher');
            Route::get('/documents/upload', DocumentUpload::class)->name('document.upload');
            Route::get('/documents/{document}/edit', DocumentEdit::class)->name('document.edit');

            // ROUTE EMPLOI DU TEMPS TEACHER
            Route::get('/emploi-du-temps', ScheduleViewer::class)->name('teacher.timetable');

            Route::get('/scolarites', ComingSoon::class)->name('teacher.scolarites');
        });

    /*
    |--------------------------------------------------------------------------
    | Routes Étudiant
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')
        ->middleware('role:student')
        ->group(function () {
            Route::get('/dashboard', DashboardStudent::class)->name('studentEspace');
            Route::get('/mes-cours', StudentDocument::class)->name('student.document');
            Route::get('/mes-enseignants', EnseignantView::class)->name('student.myTeacher');

            // ✅ NOUVELLE ROUTE EMPLOI DU TEMPS STUDENT
            Route::get('/emploi-du-temps', ScheduleViewer::class)->name('student.timetable');

            Route::get('/scolarites', ComingSoon::class)->name('student.scolarites');
        });

    Route::get('/nos-programmes', ProgrammesIndex::class)->name('programs');

    // ✅ NOUVELLES ROUTES COMMUNES POUR LES EMPLOIS DU TEMPS
    Route::get('/schedule/{schedule}', [ScheduleController::class, 'view'])->name('schedule.view');
    Route::get('/schedule/{schedule}/serve', [ScheduleController::class, 'serve'])->name('schedule.serve');
    Route::get('/schedule/{schedule}/download', [ScheduleController::class, 'download'])->name('schedule.download');

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