<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivitySubmissionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PushTokenController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

// Service Worker do Firebase — precisa estar na raiz para ter scope em todo o site
Route::get('/firebase-messaging-sw.js', function () {
    $path = public_path('firebase-messaging-sw.js');
    return response(file_get_contents($path), 200)
        ->header('Content-Type', 'application/javascript; charset=utf-8')
        ->header('Service-Worker-Allowed', '/');
});

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rota pública para compartilhamento no WhatsApp (sem autenticação)
Route::get('/activities/{activity}/share', [ActivityController::class, 'sharePreview'])->name('activities.share');

// Rota pública — Sobre o Site
Route::get('/sobre', function () {
    return view('sobre');
})->name('sobre');

// Autenticado
Route::middleware('auth')->group(function () {
    // Alterar senha obrigatório
    Route::get('/password/change', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/password/change', [ChangePasswordController::class, 'update'])->name('password.change.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Leads (admin e coordenador)
    Route::middleware('role:administrador,coordenador')->group(function () {
        Route::get('/leads', [AdminController::class, 'leads'])->name('leads.index');
    });

    // Missões
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');

    // Confirmar inscrição / RSVP (apenas role standard)
    Route::post('/activities/{activity}/rsvp', [ActivityController::class, 'confirmRsvp'])->name('activities.rsvp');
    Route::delete('/activities/{activity}/rsvp', [ActivityController::class, 'cancelRsvp'])->name('activities.rsvp.cancel');

    // Presença via QR Code (qualquer autenticado)
    Route::get('/activities/{activity}/presenca/{token}', [ActivityController::class, 'confirmPresence'])->name('activities.confirmPresence');

    // Submissões de tarefas manuais
    Route::post('/activities/{activity}/submissions', [ActivitySubmissionController::class, 'store'])->name('activities.submissions.store');

    // Ranking
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');

    // Atividades (criar/editar - só coordenador/admin)
    Route::middleware('role:coordenador,administrador')->group(function () {
        Route::get('/activities/create/new', [ActivityController::class, 'create'])->name('activities.create');
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::get('/activities/{activity}/qrcode', [ActivityController::class, 'qrcode'])->name('activities.qrcode');

        // Review activity submissions (coordinator + admin)
        Route::get('/admin/activity-submissions', [\App\Http\Controllers\ActivitySubmissionController::class, 'index'])->name('admin.activity_submissions.index');
        Route::get('/admin/activity-submissions/{submission}', [\App\Http\Controllers\ActivitySubmissionController::class, 'show'])->name('admin.activity_submissions.show');
        Route::post('/admin/activity-submissions/{submission}/approve', [\App\Http\Controllers\ActivitySubmissionController::class, 'approve'])->name('admin.activity_submissions.approve');
        Route::post('/admin/activity-submissions/{submission}/reject', [\App\Http\Controllers\ActivitySubmissionController::class, 'reject'])->name('admin.activity_submissions.reject');
    });

    // Admin (só administrador)
    Route::middleware('role:administrador')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::post('/ranking/reset', [RankingController::class, 'reset'])->name('ranking.reset');
        Route::post('/admin/push/send', [PushTokenController::class, 'sendManual'])->name('admin.push.send');
    });

    // Push notifications
    Route::post('/salvar-token', [PushTokenController::class, 'store'])->name('push.token');
    Route::post('/check-token', [PushTokenController::class, 'check'])->name('push.check');

    // Completar cadastro (perfil expandido)
    Route::get('/profile/complete', [\App\Http\Controllers\ExpandedProfileController::class, 'edit'])->name('profile.complete');
    Route::post('/profile/complete', [\App\Http\Controllers\ExpandedProfileController::class, 'update'])->name('profile.complete.update');
});