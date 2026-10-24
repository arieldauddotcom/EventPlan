<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\VisualizationController;
use Illuminate\Support\Facades\Route;

// Public avatar storage fallback route (works without auth, ensures LAN IP / reverse-proxy renders avatars)
Route::get('/storage/avatars/{filename}', function ($filename) {
    $path = 'avatars/' . $filename;
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->where('filename', '.*');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::post('/auth/dev-login', [AuthController::class, 'devLogin'])->name('auth.dev-login');
});

// Landing page (guest sees landing page, auth user redirects to dashboard)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    });

    // Projects CRUD
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/holidays', [ProjectController::class, 'addHoliday'])->name('projects.holidays.store');
    Route::delete('/projects/{project}/holidays/{holiday}', [ProjectController::class, 'removeHoliday'])->name('projects.holidays.destroy');

    // Project Invitations & Members Collaboration
    Route::get('/invitations', [ProjectInvitationController::class, 'index'])->name('invitations.index');
    Route::post('/projects/{project}/invitations', [ProjectInvitationController::class, 'store'])->name('projects.invitations.store');
    Route::post('/invitations/{invitation}/accept', [ProjectInvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{invitation}/decline', [ProjectInvitationController::class, 'decline'])->name('invitations.decline');
    Route::delete('/projects/{project}/invitations/{invitation}', [ProjectInvitationController::class, 'destroy'])->name('projects.invitations.destroy');
    Route::delete('/projects/{project}/members/{member}', [ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');

    // Visualizations (Gantt, PERT, Resource Chart)
    Route::get('/projects/{project}/gantt', [VisualizationController::class, 'gantt'])->name('projects.gantt');
    Route::get('/projects/{project}/pert', [VisualizationController::class, 'pert'])->name('projects.pert');
    Route::get('/projects/{project}/resources-chart', [VisualizationController::class, 'resources'])->name('projects.resources-chart');
    Route::get('/projects/{project}/export-tasks', [ExportController::class, 'exportTasksCsv'])->name('projects.export-tasks');
    Route::get('/projects/{project}/export-pdf', [ExportController::class, 'exportPdf'])->name('projects.export-pdf');

    // Tasks Management
    Route::get('/projects/{project}/tasks/{task}/edit', [TaskController::class, 'edit'])->name('projects.tasks.edit');
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('/projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    Route::post('/projects/{project}/tasks/{task}/dependencies', [TaskController::class, 'addDependency'])->name('projects.tasks.dependencies.store');
    Route::delete('/projects/{project}/tasks/{task}/dependencies/{dependency}', [TaskController::class, 'removeDependency'])->name('projects.tasks.dependencies.destroy');

    // Resource Management
    Route::post('/projects/{project}/resources', [ResourceController::class, 'store'])->name('projects.resources.store');
    Route::put('/projects/{project}/resources/{resource}', [ResourceController::class, 'update'])->name('projects.resources.update');
    Route::delete('/projects/{project}/resources/{resource}', [ResourceController::class, 'destroy'])->name('projects.resources.destroy');
    Route::post('/projects/{project}/resources/{resource}/leaves', [ResourceController::class, 'addLeave'])->name('projects.resources.leaves.store');
    Route::delete('/projects/{project}/resources/{resource}/leaves/{leave}', [ResourceController::class, 'removeLeave'])->name('projects.resources.leaves.destroy');

    // Task Assignment
    Route::post('/projects/{project}/tasks/{task}/assignments', [TaskAssignmentController::class, 'store'])->name('projects.tasks.assignments.store');
    Route::delete('/projects/{project}/tasks/{task}/assignments/{assignment}', [TaskAssignmentController::class, 'destroy'])->name('projects.tasks.assignments.destroy');
});
