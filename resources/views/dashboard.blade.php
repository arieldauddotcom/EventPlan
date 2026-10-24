@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Utama</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan analitik proyek, aktivitas jalur kritis, dan linimasa acara.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-3 py-2 sm:px-3.5 sm:py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm transition border border-slate-200 shadow-2xs">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-5 h-5 rounded-full object-cover mr-2 aspect-square" style="border-radius: 9999px;">
                @else
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                @endif
                Profil
            </a>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs sm:text-sm transition shadow-sm shadow-indigo-600/25">
                <svg class="w-4 h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Proyek
            </a>
        </div>
    </div>

    @if(auth()->user()->isAdmin() && !empty($adminStats))
        <!-- Panel Ringkasan Administrator Global -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-md border border-slate-800 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-800/80 gap-2">
                <div class="flex items-center space-x-3">
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        Admin Overview
                    </span>
                    <h2 class="text-base font-bold text-white tracking-tight">Ringkasan Sistem dan Global</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.projects.index') }}" class="text-xs font-semibold text-purple-300 hover:text-white inline-flex items-center">
                        Semua Proyek &rarr;
                    </a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center">
                        Kelola Pengguna &rarr;
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400">Total Pengguna Terdaftar</span>
                    <p class="text-2xl font-black text-white mt-1">{{ $adminStats['total_users'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $adminStats['admin_users'] }} Admin &bull; {{ $adminStats['regular_users'] }} User</p>
                </div>
                <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400">Total Proyek Global</span>
                    <p class="text-2xl font-black text-white mt-1">{{ $adminStats['global_projects'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Semua user terdaftar</p>
                </div>
                <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-amber-400">Proyek Planning Global</span>
                    <p class="text-2xl font-black text-amber-400 mt-1">{{ $adminStats['global_planning_projects'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Fase perencanaan</p>
                </div>
                <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-emerald-400">Proyek Global Aktif</span>
                    <p class="text-2xl font-black text-emerald-400 mt-1">{{ $adminStats['global_active_projects'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Sedang berjalan</p>
                </div>
                <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-purple-400">Proyek Selesai Global</span>
                    <p class="text-2xl font-black text-purple-400 mt-1">{{ $adminStats['global_completed_projects'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Berhasil diselesaikan</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Ringkasan Statistik Status Proyek -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Proyek</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalProjects }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Proyek Aktif</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $activeProjects }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-amber-600 uppercase tracking-wider">Fase Planning</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $planningProjects }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-purple-600 uppercase tracking-wider">Proyek Selesai</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $completedProjects }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Ringkasan Analitik Tugas & Jalur Kritis -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Total Tugas Terdaftar</span>
            <p class="text-xl font-bold text-slate-900 mt-0.5">{{ $totalTasks }} Tugas</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-medium text-blue-600">Sedang Dikerjakan</span>
            <p class="text-xl font-bold text-blue-700 mt-0.5">{{ $inProgressTasks }} Tugas</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-medium text-emerald-600">Tugas Tuntas</span>
            <p class="text-xl font-bold text-emerald-700 mt-0.5">{{ $completedTasks }} Tugas</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-rose-200 bg-rose-50/30 shadow-2xs">
            <span class="text-xs font-bold text-rose-600">Tugas di Jalur Kritis (CPM)</span>
            <p class="text-xl font-bold text-rose-700 mt-0.5">{{ $criticalTasksCount }} Tugas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Proyek Terbaru (2 span) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900 text-base">Proyek Terbaru</h2>
                <a href="{{ route('projects.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Lihat Semua &rarr;</a>
            </div>

            @if($recentProjects->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">Belum ada proyek</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Mulai perencanaan acara Anda dengan membuat proyek baru pertama Anda.</p>
                    <a href="{{ route('projects.create') }}" class="mt-4 inline-flex items-center px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-medium text-xs hover:bg-indigo-700 transition">
                        + Tambah Proyek Sekarang
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentProjects as $project)
                        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50/75 transition">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition text-base">
                                        {{ $project->name }}
                                    </a>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $project->status === 'planning' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $project->status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $project->status === 'draft' ? 'bg-slate-100 text-slate-700' : '' }}
                                    ">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    Organisasi: <span class="font-medium text-slate-700">{{ $project->organization ?? '-' }}</span> &bull; 
                                    Durasi: {{ $project->start_date->format('d M Y') }} - {{ $project->deadline->format('d M Y') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('projects.gantt', $project) }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-[11px] font-semibold text-slate-700 hover:bg-slate-100 transition" title="Gantt Chart">
                                    Gantt
                                </a>
                                <a href="{{ route('projects.pert', $project) }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-[11px] font-semibold text-slate-700 hover:bg-slate-100 transition" title="PERT Diagram">
                                    PERT
                                </a>
                                <a href="{{ route('projects.show', $project) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-xs hover:bg-indigo-100 transition">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Deadline Terdekat (7 Hari ke depan) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900 text-base">Deadline Mendatang</h2>
                <span class="text-[11px] bg-rose-50 text-rose-700 font-bold px-2 py-0.5 rounded-md">7 Hari Kedepan</span>
            </div>

            <div class="p-4 flex-1">
                @if($upcomingDeadlines->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center p-6 text-center text-slate-400">
                        <svg class="w-8 h-8 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs">Tidak ada tugas dengan deadline dalam 7 hari mendatang.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingDeadlines as $task)
                            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-50 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="truncate">
                                        <div class="flex items-center space-x-1.5">
                                            @if($task->is_critical)
                                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Kritis"></span>
                                            @endif
                                            <span class="text-xs font-bold text-slate-900 truncate">{{ $task->name }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            Proyek: <span class="font-medium text-slate-700">{{ $task->project->name }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-700 shrink-0">
                                        {{ $task->end_date->format('d M') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
