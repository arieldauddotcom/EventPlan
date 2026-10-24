@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Pengguna & Sistem</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar seluruh akun terdaftar, hak akses, dan metrik keseluruhan proyek sistem.</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:space-x-2.5 sm:gap-0">
            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs sm:text-sm transition border border-indigo-200 shadow-2xs">
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Lihat Semua Proyek</span>
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-indigo-200 text-indigo-800 font-bold">
                    {{ $stats['total_projects'] }}
                </span>
            </a>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs sm:text-sm transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Proyek
            </a>
        </div>
    </div>

    <!-- Panel Kartu Ringkasan Statistik Sistem -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Kartu Total Proyek -->
        <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 rounded-2xl p-5 text-white shadow-md shadow-indigo-900/20 border border-indigo-500/40 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-100">Total Portofolio Proyek</span>
                    <span class="p-2 rounded-xl bg-indigo-800/60 text-indigo-200 border border-indigo-400/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-3xl font-black text-white tracking-tight">{{ $stats['total_projects'] }}</p>
                    <span class="text-xs text-indigo-200 font-medium">proyek terdaftar</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-1.5 text-[11px]">
                    <span class="px-2 py-0.5 rounded-md bg-indigo-800/60 text-indigo-100 border border-indigo-400/30 font-semibold">
                        {{ $stats['active_projects'] }} Aktif
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-200 border border-amber-400/30 font-semibold">
                        {{ $stats['planning_projects'] }} Planning
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-purple-900/60 text-purple-200 border border-purple-400/30 font-semibold">
                        {{ $stats['completed_projects'] }} Selesai
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-indigo-500/30 flex items-center justify-between text-xs">
                <a href="{{ route('admin.projects.index') }}" class="text-indigo-200 hover:text-white font-semibold inline-flex items-center transition group">
                    <span>Lihat Rincian Semua Proyek</span>
                    <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Kartu Total Pengguna -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pengguna Terdaftar</span>
                    <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['total_users'] }}</p>
                    <span class="text-xs text-slate-500">akun pengguna</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-1.5 text-[11px]">
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-semibold border border-purple-200">
                        {{ $stats['admin_users'] }} Admin
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium">
                        {{ $stats['regular_users'] }} User Biasa
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                Memiliki akses autentikasi internal & Google OAuth.
            </div>
        </div>

        <!-- Kartu Tugas CPM Global -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs flex flex-col justify-between sm:col-span-2 lg:col-span-1">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Tugas CPM Linimasa</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['total_tasks'] }}</p>
                    <span class="text-xs text-slate-500">kegiatan terdata</span>
                </div>
                <p class="mt-3 text-xs text-slate-500 leading-relaxed">
                    Terdistribusi di seluruh proyek acara dengan kalkulasi jalur kritis (CPM).
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Tersinkronisasi otomatis</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-2.5 sm:gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, username, atau email..." class="w-full px-3.5 py-2 sm:py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div class="flex items-center space-x-2">
                <select name="role" class="w-full sm:w-36 px-3.5 py-2 sm:py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $role === 'user' ? 'selected' : '' }}>User</option>
                </select>
                <button type="submit" class="px-5 py-2 sm:py-2.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition shrink-0">
                    Cari
                </button>
                @if($search || $role)
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 sm:py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200 transition shrink-0 flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="sm:hidden px-4 py-2 bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            <span>Geser tabel ke kanan untuk melihat rincian akun</span>
        </div>
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Proyek Dibuat</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($u->avatar_url)
                                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-full object-cover shrink-0 aspect-square border border-slate-200" style="border-radius: 9999px;">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0 aspect-square border border-indigo-100" style="border-radius: 9999px;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $u->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-600">
                                {{ '@' . ($u->username ?? '-') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ strtoupper($u->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                @if($u->owned_projects_count > 0)
                                    <a href="{{ route('admin.projects.index', ['owner_id' => $u->id]) }}"
                                       title="Lihat seluruh proyek yang dibuat oleh {{ $u->name }}"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold transition group">
                                        <span>{{ $u->owned_projects_count }} proyek</span>
                                        <svg class="w-3 h-3 text-indigo-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">0 proyek</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Edit
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-sm">
                                Tidak ada data pengguna yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
