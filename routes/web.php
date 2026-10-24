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

// SEO: Dynamic Robots.txt & Sitemap.xml
Route::get('/robots.txt', function () {
    $content = implode("\n", [
        'User-agent: *',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /dashboard',
        'Disallow: /projects',
        'Disallow: /invitations',
        'Disallow: /profile',
        'Disallow: /admin',
        'Disallow: /auth/',
        '',
        'Allow: /',
        'Allow: /images/',
        'Allow: /build/',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ]);
    return response($content, 200)->header('Content-Type', 'text/plain');
});

Route::get('/sitemap.xml', function () {
    $now = now()->toAtomString();
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $xml .= '  <url>';
    $xml .= '    <loc>' . url('/') . '</loc>';
    $xml .= '    <lastmod>' . $now . '</lastmod>';
    $xml .= '    <changefreq>weekly</changefreq>';
    $xml .= '    <priority>1.0</priority>';
    $xml .= '  </url>';
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
});

// Public avatar storage fallback route (works without auth, ensures LAN IP / reverse-proxy renders avatars)
Route::get('/storage/avatars/{filename}', function ($filename) {
    $path = 'avatars/' . $filename;
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->where('filename', '.*');

// Google OAuth Routes (Dapat diakses guest untuk login atau user terautentikasi untuk menautkan akun)
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
    Route::post('/auth/dev-login', [AuthController::class, 'devLogin'])->name('auth.dev-login');
});

// Landing page (dapat diakses baik oleh tamu maupun pengguna yang sudah login)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile & OAuth Unlinking
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/auth/google', [AuthController::class, 'unlinkGoogle'])->name('auth.google.unlink');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::get('/projects', [AdminUserController::class, 'projects'])->name('projects.index');
        Route::delete('/projects/{project}', [AdminUserController::class, 'destroyProject'])->name('projects.destroy');
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
    Route::get('/projects/{project}/check-user-email', [ProjectMemberController::class, 'checkUserEmail'])->name('projects.members.check-email');
    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::patch('/projects/{project}/members/{member}/role', [ProjectMemberController::class, 'updateRole'])->name('projects.members.update-role');
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
