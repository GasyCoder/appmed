<?php

use App\Livewire\Admin\Niveaux;
use App\Livewire\Admin\Parcours;

use App\Livewire\Admin\Semestres;
use App\Livewire\Teacher\Documents;
use App\Livewire\Admin\UsersStudent;
use App\Livewire\Admin\UsersTeacher;

use Illuminate\Support\Facades\Auth;
use App\Livewire\Student\HomeStudent;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\ScheduleUpload;
use App\Livewire\Teacher\DocumentEdit;
use App\Livewire\Shared\ScheduleViewer;
use App\Livewire\Admin\AuthorizedEmails;

use App\Livewire\Student\EnseignantView;
use App\Livewire\Teacher\DocumentUpload;
use App\Livewire\Student\StudentDocument;
use App\Livewire\Admin\ScheduleManagement;

use App\Livewire\Teacher\TeacherDashboard;
use App\Livewire\Shared\AnnouncementsIndex;
use App\Http\Controllers\DocumentController;

use App\Http\Controllers\ScheduleController;
use App\Livewire\Admin\AnnouncementsManager;
use App\Livewire\Programmes\ProgrammesIndex;
use App\Http\Controllers\Auth\RegisterFormController;
use App\Http\Controllers\Auth\EmailVerificationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [EmailVerificationController::class, 'index'])->name('inscription');
    Route::post('/inscription/verify', [EmailVerificationController::class, 'verifyEmailStudent'])->name('email.verify');
    Route::get('/inscription/formulaire/{token}', [RegisterFormController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/inscription/formulaire/{token}', [RegisterFormController::class, 'register'])->name('register.store');
});

Route::get('/set-password/{token}', function ($token) {
    return view('auth.set-password', ['token' => $token]);
})->name('password.set')->middleware('signed');

Route::get('/documents/public/{document}', [DocumentController::class, 'public'])
    ->name('document.public')
    ->middleware('signed');

Route::view('/faq', 'support.faq')->name('faq');
Route::view('/aide', 'support.help')->name('help');    

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return match (true) {
            Auth::user()->hasRole('admin')   => redirect()->route('adminEspace'),
            Auth::user()->hasRole('teacher') => redirect()->route('teacherEspace'),
            Auth::user()->hasRole('student') => redirect()->route('studentEspace'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');
    
    Route::get('/documents/{document}/viewer', [DocumentController::class, 'viewer'])
        ->name('document.viewer')
        ->middleware('document.access');

    Route::get('/documents/serve/{document}', [DocumentController::class, 'serve'])
        ->name('document.serve')
        ->middleware('document.access');

    Route::get('/documents/download/{document}', [DocumentController::class, 'download'])
        ->name('document.download')
        ->middleware('document.access');

    // ✅ EXTERNE: ouvrir en lecture (nouvel onglet), SANS passer par viewer blade
    Route::get('/documents/{document}/open-external', [DocumentController::class, 'openExternal'])
        ->name('document.openExternal')
        ->middleware('document.access');

    // ✅ EXTERNE: téléchargement (docx/xls/csv), SANS viewer
    Route::get('/documents/{document}/download-external', [DocumentController::class, 'downloadExternal'])
        ->name('document.downloadExternal')
        ->middleware('document.access');

    Route::get('/annonces', AnnouncementsIndex::class)
        ->name('announcements.index')
        ->middleware('role:admin|teacher|student');

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('adminEspace');
        Route::get('/etudiants', UsersStudent::class)->name('admin.students');
        Route::get('/enseignants', UsersTeacher::class)->name('admin.teachers');
        Route::get('/niveaux', Niveaux::class)->name('admin.niveau');
        Route::get('/parcours', Parcours::class)->name('admin.parcour');
        Route::get('/semestres', Semestres::class)->name('admin.semestre');
        Route::get('/emploi-du-temps', ScheduleManagement::class)->name('admin.timetable');
        Route::get('/emploi-du-temps/upload', ScheduleUpload::class)->name('admin.schedules.upload');
        Route::get('/authorized-emails', AuthorizedEmails::class)->name('admin.authorized-emails');

        Route::get('/announcements-manager', AnnouncementsManager::class)->name('admin.announcements');
    });

    Route::prefix('teacher')->middleware('role:teacher')->group(function () {
        Route::get('/dashboard', TeacherDashboard::class)->name('teacherEspace');
        Route::get('/documents', Documents::class)->name('document.teacher');
        Route::get('/documents/upload', DocumentUpload::class)->name('document.upload');
        Route::get('/documents/{document}/edit', DocumentEdit::class)->name('document.edit');
        Route::get('/emploi-du-temps', ScheduleViewer::class)->name('teacher.timetable');
    });

    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/dashboard', HomeStudent::class)->name('studentEspace');
        Route::get('/mes-cours', StudentDocument::class)->name('student.document');
        Route::get('/mes-ue', function () {
            return redirect()->route('student.document', ['view' => 'ue']);
        })->name('student.ue');
        Route::get('/mes-enseignants', EnseignantView::class)->name('student.myTeacher');
        Route::get('/emploi-du-temps', ScheduleViewer::class)->name('student.timetable');

    });

    Route::get('/nos-programmes', ProgrammesIndex::class)->name('programs');

    Route::get('/schedule/{schedule}', [ScheduleController::class, 'view'])->name('schedule.view');
    Route::get('/schedule/{schedule}/serve', [ScheduleController::class, 'serve'])->name('schedule.serve');
    Route::get('/schedule/{schedule}/download', [ScheduleController::class, 'download'])->name('schedule.download');
});