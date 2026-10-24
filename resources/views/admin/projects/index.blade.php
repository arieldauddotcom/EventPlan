@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kelola Pengguna
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs text-slate-500 font-semibold">Administrasi Proyek</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Daftar Seluruh Proyek (Global)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau seluruh portofolio proyek acara yang dibuat oleh semua pengguna di sistem.</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:space-x-2.5 sm:gap-0">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm transition border border-slate-200 shadow-2xs">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola Pengguna
            </a>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs sm:text-sm transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Proyek Baru
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik Total Proyek -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Proyek -->
        <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 rounded-2xl p-4 text-white shadow-md shadow-indigo-900/20 border border-indigo-500/40 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-bold text-indigo-100 uppercase tracking-wider">Total Proyek</span>
                    <span class="p-1 rounded-lg bg-indigo-800/60 text-indigo-200 border border-indigo-400/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline space-x-2 mt-1">
                    <p class="text-3xl font-black text-white tracking-tight">{{ $stats['total_projects'] }}</p>
                    <span class="text-xs text-indigo-200 font-medium">global</span>
                </div>
                <p class="text-[11px] text-indigo-200 mt-1 font-medium">Seluruh portofolio sistem</p>
            </div>
        </div>

        <!-- Proyek Aktif -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Proyek Aktif</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <p class="text-3xl font-black text-emerald-600">{{ $stats['active_projects'] }}</p>
                <span class="text-xs text-slate-400">berjalan</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Dalam tahap eksekusi linimasa</p>
        </div>

        <!-- Proyek Perencanaan -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Perencanaan</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <p class="text-3xl font-black text-amber-600">{{ $stats['planning_projects'] }}</p>
                <span class="text-xs text-slate-400">planning</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Penjadwalan & CPM aktif</p>
        </div>

        <!-- Proyek Selesai -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Selesai</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <p class="text-3xl font-black text-purple-600">{{ $stats['completed_projects'] }}</p>
                <span class="text-xs text-slate-400">tuntas</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Acara selesai dilaksanakan</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- Search -->
            <div class="lg:col-span-5">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari nama proyek, organisasi, atau nama pemilik..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400">
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-3">
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Status Proyek</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>🟢 Aktif (Active)</option>
                    <option value="planning" {{ $status === 'planning' ? 'selected' : '' }}>🟡 Perencanaan (Planning)</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>🟣 Selesai (Completed)</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>⚪ Draft</option>
                </select>
            </div>

            <!-- Owner Filter -->
            <div class="lg:col-span-3">
                <select name="owner_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Pemilik (Owner)</option>
                    @foreach($usersList as $u)
                        <option value="{{ $u->id }}" {{ (string)$ownerId === (string)$u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ '@' . $u->username }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="lg:col-span-1 flex space-x-1.5">
                <button type="submit" class="w-full px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition flex items-center justify-center">
                    Cari
                </button>
                @if($search || $status || $ownerId)
                    <a href="{{ route('admin.projects.index') }}" title="Reset Filter" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition flex items-center justify-center">
                        ✕
                    </a>
                @endif
            </div>
        </form>

        @if($selectedOwner)
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-indigo-700 bg-indigo-50/60 px-3 py-2 rounded-xl">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">Filter Aktif:</span>
                    <span>Menampilkan proyek yang dibuat oleh <strong>{{ $selectedOwner->name }}</strong> ({{ $selectedOwner->email }})</span>
                </div>
                <a href="{{ route('admin.projects.index', array_filter(['search' => $search, 'status' => $status])) }}" class="font-bold underline hover:text-indigo-900">
                    Hapus Filter Pemilik
                </a>
            </div>
        @endif
    </div>

    <!-- Tabel Seluruh Proyek -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="sm:hidden px-4 py-2 bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            <span>Geser tabel ke kanan untuk melihat rincian proyek</span>
        </div>
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Proyek & Organisasi</th>
                        <th class="px-6 py-4">Pemilik (Leader)</th>
                        <th class="px-6 py-4">Kalender Kerja</th>
                        <th class="px-6 py-4">Tugas & Tim</th>
                        <th class="px-6 py-4">Linimasa & Deadline</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Nama Proyek -->
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    <a href="{{ route('projects.show', $project) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block truncate">
                                        {{ $project->name }}
                                    </a>
                                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                                        {{ $project->organization ?? 'Tanpa organisasi' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Pemilik -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2.5">
                                    @if($project->owner?->avatar_url)
                                        <img src="{{ $project->owner->avatar_url }}" alt="{{ $project->owner->name }}" class="w-7 h-7 rounded-full object-cover shrink-0 aspect-square border border-slate-200" style="border-radius: 9999px;">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0 aspect-square border border-indigo-100" style="border-radius: 9999px;">
                                            {{ strtoupper(substr($project->owner?->name ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="truncate max-w-[140px]">
                                        <a href="{{ route('admin.projects.index', ['owner_id' => $project->owner_id]) }}" title="Filter proyek milik user ini" class="font-semibold text-xs text-slate-800 hover:text-indigo-600 block truncate">
                                            {{ $project->owner?->name ?? 'Akun Terhapus' }}
                                        </a>
                                        <p class="text-[11px] text-slate-400 font-mono truncate">
                                            {{ '@' . ($project->owner?->username ?? '-') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kalender Kerja -->
                            <td class="px-6 py-4 text-xs">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $project->workCalendar?->name ?? 'Default (5 Hari)' }}
                                </span>
                            </td>

                            <!-- Tugas & Anggota -->
                            <td class="px-6 py-4 text-xs">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-semibold" title="Total Tugas CPM">
                                        <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        {{ $project->tasks_count }} Tugas
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700" title="Total Anggota Kolaborasi">
                                        <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $project->members_count }}
                                    </span>
                                </div>
                            </td>

                            <!-- Tanggal Linimasa -->
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div class="space-y-0.5">
                                    <p class="font-medium text-slate-800">
                                        {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
                                        &rarr;
                                        {{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}
                                    </p>
                                    @if($project->deadline)
                                        @if($project->deadline->isPast() && $project->status !== 'completed')
                                            <p class="text-[10px] text-rose-500 font-bold">Melewati Deadline</p>
                                        @else
                                            <p class="text-[10px] text-slate-400">{{ $project->deadline->diffForHumans() }}</p>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($project->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @elseif($project->status === 'planning')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500"></span>
                                        Planning
                                    </span>
                                @elseif($project->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-purple-500"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('projects.show', $project) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Buka Detail Proyek">
                                        Detail
                                    </a>
                                    <a href="{{ route('projects.gantt', $project) }}" class="p-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition" title="Visualisasi Gantt Chart">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7"/></svg>
                                    </a>
                                    <a href="{{ route('projects.pert', $project) }}" class="p-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition" title="Diagram Jaringan PERT">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek \'{{ $project->name }}\'? Seluruh tugas dan linimasa akan dihapus.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus Proyek">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <p class="font-semibold text-slate-700">Tidak ada proyek yang ditemukan</p>
                                    <p class="text-xs text-slate-400">Tidak ada proyek yang cocok dengan filter atau kata kunci pencarian yang dimasukkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
