<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Area kerja privat aplikasi EventPlan untuk manajemen tugas, alokasi sumber daya, dan analisis linimasa acara.">
    <title>{{ $title ?? 'EventPlan — Perencanaan & Manajemen Acara' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-slate-800 antialiased font-sans flex flex-col md:flex-row min-h-screen" x-data="{ mobileMenuOpen: false }">

    <!-- Mobile Header -->
    <header class="md:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="flex items-center space-x-2.5">
            <img src="{{ asset('images/logo-rounded.png') }}" alt="EventPlan Logo" class="w-8 h-8 rounded-lg shadow-sm object-cover">
            <span class="font-bold text-lg text-slate-900 tracking-tight">EventPlan</span>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" aria-label="Buka Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </header>

    <!-- Sidebar Desktop -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 flex-shrink-0 border-r border-slate-800">
        <div class="flex items-center px-6 py-5 border-b border-slate-800 space-x-3">
            <img src="{{ asset('images/logo-rounded.png') }}" alt="EventPlan Logo" class="w-10 h-10 rounded-xl shadow-md object-cover ring-1 ring-white/10">
            <div>
                <h1 class="font-bold text-base text-white tracking-tight leading-tight">EventPlan</h1>
                <p class="text-xs text-indigo-400 font-medium">Project & Resource</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400 {{ request()->routeIs('dashboard') ? '!text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('projects.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->routeIs('projects.*') ? 'bg-indigo-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400 {{ request()->routeIs('projects.*') ? '!text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Daftar Proyek
            </a>

            <a href="{{ route('invitations.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg transition {{ request()->routeIs('invitations.*') ? 'bg-indigo-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-slate-400 {{ request()->routeIs('invitations.*') ? '!text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Undangan Proyek
                </div>
                @php
                    $pendingCount = \App\Models\ProjectInvitation::where(function($q) {
                        $q->where('invitee_id', auth()->id())
                          ->orWhere('email', strtolower(auth()->user()->email));
                    })->where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-rose-500 text-white">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Menu Pengaturan Profil di Sidebar -->
            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400 {{ request()->routeIs('profile.*') ? '!text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Pengaturan Profil
            </a>

            @if(auth()->user()->isAdmin())
                <div class="pt-4 pb-1">
                    <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Administrasi</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 text-slate-400 {{ request()->routeIs('admin.users.*') ? '!text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Kelola Pengguna
                </a>
            @endif
        </nav>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center justify-between">
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 overflow-hidden group hover:opacity-90 transition flex-1 min-w-0 mr-2" title="Edit Profil">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover shrink-0 aspect-square border border-slate-700" style="border-radius: 9999px;">
                    @else
                        <div class="w-9 h-9 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold flex items-center justify-center text-sm shrink-0 aspect-square border border-indigo-500/30" style="border-radius: 9999px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="truncate">
                        <p class="text-sm font-semibold text-white group-hover:text-indigo-300 transition truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile Drawer -->
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 md:hidden flex" style="display: none;">
        <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="relative w-4/5 max-w-xs bg-slate-900 text-slate-300 flex flex-col h-full p-4 z-10">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo-rounded.png') }}" alt="EventPlan Logo" class="w-8 h-8 rounded-lg shadow-sm object-cover">
                    <span class="font-bold text-lg text-white">EventPlan</span>
                </div>
                <button @click="mobileMenuOpen = false" class="text-slate-400 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">Dashboard</a>
                <a href="{{ route('projects.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">Daftar Proyek</a>
                <a href="{{ route('invitations.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('invitations.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">Undangan Proyek</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">Kelola Pengguna</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">Profil Saya</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-slate-800">
                @csrf
                <button type="submit" class="w-full text-left text-rose-400 hover:text-rose-300 text-sm font-medium">Keluar</button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        <!-- Toast Alerts -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm shadow-xs">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex-1 px-4 sm:px-6 lg:px-8 py-4">
            @yield('content')
        </div>
    </main>

</body>
</html>
