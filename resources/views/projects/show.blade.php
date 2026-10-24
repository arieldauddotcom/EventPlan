@extends('layouts.app')

@section('content')
@php
    $currentUser = auth()->user();
    $isLeader = $project->isLeader($currentUser);
@endphp

<div class="space-y-8" x-data="{ activeTab: '{{ session('tab', 'tasks') }}', showNewTaskModal: false, showNewResourceModal: false, showAddTeamModal: false, editTask: null }">
    <!-- Header Proyek Responsif Mobile -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                    @if($project->status === 'active') bg-emerald-100 text-emerald-700
                    @elseif($project->status === 'planning') bg-indigo-100 text-indigo-700
                    @elseif($project->status === 'completed') bg-blue-100 text-blue-700
                    @else bg-slate-100 text-slate-700 @endif">
                    {{ ucfirst($project->status) }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $isLeader ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                    Peran: {{ $isLeader ? 'Leader' : 'Member' }}
                </span>
                @if($project->organization)
                    <span class="text-xs text-slate-500 font-medium">• {{ $project->organization }}</span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 tracking-tight mt-1.5">{{ $project->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                Jadwal: <span class="font-semibold text-slate-700">{{ $project->start_date->format('d M Y') }}</span> s/d <span class="font-semibold text-slate-700">{{ $project->deadline->format('d M Y') }}</span>
                • Kalender: <span class="text-indigo-600 font-medium">{{ $project->workCalendar->name }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2" x-data="{ openPdfModal: false, startDate: '{{ $project->start_date->format('Y-m-d') }}', endDate: '{{ $project->deadline->format('Y-m-d') }}' }">
            <!-- Visualisasi (Tersedia bagi Leader maupun Member) -->
            <a href="{{ route('projects.gantt', $project) }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition flex items-center space-x-1">
                <span>Gantt Chart</span>
            </a>
            <a href="{{ route('projects.pert', $project) }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition flex items-center space-x-1">
                <span>PERT Diagram</span>
            </a>
            <a href="{{ route('projects.resources-chart', $project) }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition flex items-center space-x-1">
                <span>Beban Tim</span>
            </a>
            <a href="{{ route('projects.export-tasks', $project) }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-emerald-50 border border-emerald-300 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 transition flex items-center space-x-1" title="Unduh CSV">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>CSV</span>
            </a>
            <button type="button" @click="openPdfModal = true" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-rose-50 border border-rose-300 text-xs font-semibold text-rose-800 hover:bg-rose-100 transition flex items-center space-x-1" title="Filter & Ekspor Laporan PDF">
                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>PDF</span>
            </button>

            <!-- Modal Filter & Ekspor PDF -->
            <div x-show="openPdfModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="openPdfModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            <h3 class="text-base font-bold text-slate-800">Ekspor Laporan PDF</h3>
                        </div>
                        <button @click="openPdfModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <p class="text-xs text-slate-500 mt-3">
                        Pilih rentang tanggal linimasa yang ingin dicantumkan dalam laporan PDF atau biarkan default untuk seluruh rentang proyek.
                    </p>

                    <form method="GET" action="{{ route('projects.export-pdf', $project) }}" class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" x-model="startDate" class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500 p-2.5 border">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" x-model="endDate" class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500 p-2.5 border">
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-end space-x-2">
                            <button type="button" @click="openPdfModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" name="preview" value="1" formtarget="_blank" class="px-4 py-2 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl transition flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Preview di Tab</span>
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh PDF</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($isLeader)
                <button type="button" @click="activeTab = 'members'; showAddTeamModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Tim</span>
                </button>
                <a href="{{ route('projects.edit', $project) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Edit Proyek
                </a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Hapus proyek ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold hover:bg-rose-100 transition">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Tab Navigasi Responsif Mobile -->
    <div class="border-b border-slate-200 -mx-3 px-3 sm:mx-0 sm:px-0">
        <div class="flex space-x-3 sm:space-x-6 text-xs sm:text-sm font-semibold overflow-x-auto whitespace-nowrap pb-1">
            <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition flex items-center space-x-1.5 shrink-0 py-1">
                <span>Daftar Tugas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $project->tasks->count() }}</span>
            </button>
            <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition flex items-center space-x-1.5 shrink-0 py-1">
                <span>Tim & Kolaborasi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-50 text-indigo-700 font-bold">{{ $project->members->count() }}</span>
            </button>
            <button @click="activeTab = 'resources'" :class="activeTab === 'resources' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition flex items-center space-x-1.5 shrink-0 py-1">
                <span>Tim & Sumber Daya</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $project->resources->count() }}</span>
            </button>
            @if($isLeader)
                <button @click="activeTab = 'calendar'" :class="activeTab === 'calendar' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition shrink-0 py-1">
                    <span>Kalender Kerja & Hari Libur</span>
                </button>
            @endif
        </div>
    </div>

    <!-- TAB 1: TUGAS (TASKS & DEPENDENCY) -->
    <div x-show="activeTab === 'tasks'" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Manajemen Tugas Proyek</h2>
                <p class="text-xs text-slate-500">Tugas, durasi hari kerja, prioritas, status, serta relasi dependensi (FS/SS).</p>
            </div>
            @if($isLeader)
                <button @click="showNewTaskModal = true" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center justify-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Tugas</span>
                </button>
            @endif
        </div>

        @if($project->tasks->isEmpty())
            <div class="p-8 sm:p-12 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Tugas Terdaftar</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Mulai rencanakan aktivitas acara dengan membuat tugas pertama Anda.</p>
                @if($isLeader)
                    <button @click="showNewTaskModal = true" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">Buat Tugas Sekarang</button>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach($project->tasks as $t)
                    @php
                        $isAssignedToCurrentUser = $t->assignments->some(function($asg) use ($currentUser) {
                            return strtolower($asg->resource->email ?? '') === strtolower($currentUser->email);
                        });
                        $canEditProgress = $isLeader || $isAssignedToCurrentUser;
                    @endphp
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4" x-data="{ showDepModal: false, showAssignModal: false, showProgressModal: false }">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        @if($t->priority === 'urgent') bg-rose-100 text-rose-700
                                        @elseif($t->priority === 'high') bg-amber-100 text-amber-700
                                        @elseif($t->priority === 'medium') bg-indigo-100 text-indigo-700
                                        @else bg-slate-100 text-slate-600 @endif">
                                        {{ $t->priority }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium
                                        @if($t->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($t->status === 'in_progress') bg-blue-50 text-blue-700 border border-blue-200
                                        @else bg-slate-50 text-slate-600 border border-slate-200 @endif">
                                        {{ str_replace('_', ' ', ucfirst($t->status)) }}
                                    </span>
                                    <span class="text-xs text-slate-400">• {{ $t->progress_percentage }}% selesai</span>
                                    @if($isAssignedToCurrentUser)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            Ditugaskan ke Anda
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 mt-1">{{ $t->name }}</h3>
                                @if($t->description)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $t->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] sm:text-xs text-slate-500 mt-2">
                                    <span>Durasi: <strong class="text-slate-800">{{ $t->duration_days }} hari kerja</strong></span>
                                    <span>Mulai: <strong class="text-slate-800">{{ $t->start_date->format('d M Y') }}</strong></span>
                                    <span>Selesai: <strong class="text-slate-800">{{ $t->end_date->format('d M Y') }}</strong></span>
                                    <span>Float: <strong class="{{ $t->is_critical ? 'text-rose-600 font-bold' : 'text-slate-800' }}">{{ $t->total_float ?? 0 }} hari</strong></span>
                                    @if($t->is_critical)
                                        <span class="text-rose-600 font-bold">• Jalur Kritis (CPM)</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                                @if($canEditProgress)
                                    <button @click="showProgressModal = true" class="px-2.5 py-1.5 sm:px-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-semibold transition">
                                        Update Progres
                                    </button>
                                @endif

                                @if($isLeader)
                                    <a href="{{ route('projects.tasks.edit', [$project, $t]) }}" class="px-2.5 py-1.5 sm:px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                    <button @click="showAssignModal = true" class="px-2.5 py-1.5 sm:px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                        + Tugaskan ({{ $t->assignments->count() }})
                                    </button>
                                    <button @click="showDepModal = true" class="px-2.5 py-1.5 sm:px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                        + Dependency ({{ $t->predecessors->count() }})
                                    </button>
                                    <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $t]) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Tugas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Info Predecessors & Assignments -->
                        <div class="pt-3 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="font-semibold text-slate-600 block mb-1">Predecessor (Tugas Syarat):</span>
                                @if($t->predecessors->isEmpty())
                                    <span class="text-slate-400 italic">Tidak ada predecessor (bisa langsung dimulai).</span>
                                @else
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($t->predecessors as $p)
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                <span>{{ $p->name }} ({{ $p->pivot->type }}{{ $p->pivot->lag_days > 0 ? '+'.$p->pivot->lag_days.'h' : '' }})</span>
                                                @if($isLeader)
                                                    <form method="POST" action="{{ route('projects.tasks.dependencies.destroy', [$project, $t, $p->pivot->id]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-indigo-400 hover:text-indigo-900 ml-1">&times;</button>
                                                    </form>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div>
                                <span class="font-semibold text-slate-600 block mb-1">Petugas Ditugaskan:</span>
                                @if($t->assignments->isEmpty())
                                    <span class="text-slate-400 italic">Belum ada tim yang ditugaskan.</span>
                                @else
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($t->assignments as $asg)
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span>{{ $asg->resource->name }} ({{ $asg->allocation_percentage }}%)</span>
                                                @if($isLeader)
                                                    <form method="POST" action="{{ route('projects.tasks.assignments.destroy', [$project, $t, $asg]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-emerald-400 hover:text-emerald-900 ml-1">&times;</button>
                                                    </form>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- MODAL UPDATE PROGRES TUGAS (LEADER & MEMBER) -->
                        @if($canEditProgress)
                            <div x-show="showProgressModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-sm w-full p-5 sm:p-6 shadow-xl border border-slate-200 max-h-[85vh] overflow-y-auto" @click.away="showProgressModal = false">
                                    <h4 class="text-sm font-bold text-slate-900 mb-1">Update Progres Tugas</h4>
                                    <p class="text-xs text-slate-500 mb-4">{{ $t->name }}</p>
                                    <form method="POST" action="{{ route('projects.tasks.update', [$project, $t]) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                             <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pengerjaan</label>
                                            <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                                <option value="todo" {{ $t->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                                <option value="in_progress" {{ $t->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="review" {{ $t->status === 'review' ? 'selected' : '' }}>Review</option>
                                                <option value="completed" {{ $t->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Persentase Selesai (%)</label>
                                            <input type="number" name="progress_percentage" value="{{ $t->progress_percentage }}" min="0" max="100" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                        </div>
                                        <div class="flex justify-end space-x-2 pt-2">
                                            <button type="button" @click="showProgressModal = false" class="px-3 py-1.5 border border-slate-300 text-xs rounded-xl font-semibold">Batal</button>
                                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-xl font-semibold">Simpan Progres</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- MODAL TAMBAH DEPENDENCY (LEADER ONLY) -->
                        @if($isLeader)
                            <div x-show="showDepModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 max-h-[85vh] overflow-y-auto" @click.away="showDepModal = false">
                                    <h4 class="text-sm font-bold text-slate-900 mb-2">Tambah Predecessor untuk "{{ $t->name }}"</h4>
                                    <form method="POST" action="{{ route('projects.tasks.dependencies.store', [$project, $t]) }}" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Tugas Syarat (Predecessor)</label>
                                            <select name="predecessor_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                                @foreach($project->tasks->where('id', '!=', $t->id) as $otherTask)
                                                    <option value="{{ $otherTask->id }}">{{ $otherTask->name }} (Selesai: {{ $otherTask->end_date->format('d M') }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Relasi</label>
                                                <select name="type" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                                    <option value="FS">Finish-to-Start (FS)</option>
                                                    <option value="SS">Start-to-Start (SS)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Lag (Hari)</label>
                                                <input type="number" name="lag_days" value="0" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                            </div>
                                        </div>
                                        <div class="flex justify-end space-x-2 pt-2">
                                            <button type="button" @click="showDepModal = false" class="px-3 py-1.5 border border-slate-300 text-xs rounded-xl font-semibold">Batal</button>
                                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-xl font-semibold">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- MODAL TAMBAH ASSIGNMENT (LEADER ONLY) -->
                            <div x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 max-h-[85vh] overflow-y-auto" @click.away="showAssignModal = false">
                                    <h4 class="text-sm font-bold text-slate-900 mb-2">Tugaskan Anggota ke "{{ $t->name }}"</h4>
                                    <form method="POST" action="{{ route('projects.tasks.assignments.store', [$project, $t]) }}" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Anggota Tim</label>
                                            <select name="resource_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                                @foreach($project->resources as $res)
                                                    <option value="{{ $res->id }}">{{ $res->name }} ({{ $res->role }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alokasi Beban Kerja (%)</label>
                                            <input type="number" name="allocation_percentage" value="100" min="1" max="100" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                            <span class="text-[10px] text-slate-400">Contoh: 100% untuk fokus penuh, atau 50% paruh waktu.</span>
                                        </div>
                                        <div class="flex justify-end space-x-2 pt-2">
                                            <button type="button" @click="showAssignModal = false" class="px-3 py-1.5 border border-slate-300 text-xs rounded-xl font-semibold">Batal</button>
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-xl font-semibold">Simpan Penugasan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 2: KOLABORASI & ANGGOTA PROYEK (MEMBERS & INVITATIONS) -->
    <div x-show="activeTab === 'members'" class="space-y-6" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Kolaborasi & Tim Proyek</h2>
                <p class="text-xs text-slate-500">Daftar pengguna dengan akses proyek (Leader & Member) serta status undangan tim.</p>
            </div>
            @if($isLeader)
                <div class="flex items-center space-x-2">
                    <button type="button" @click="showAddTeamModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>+ Tambah Tim</span>
                    </button>
                    <button type="button" @click="showAddTeamModal = true" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5" title="Kirim email undangan">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Undang via Email</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Anggota Aktif -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Anggota Tim Aktif ({{ $project->members->count() }})</h3>
                        <p class="text-[11px] text-slate-400">Pengguna yang memiliki akun dan akses kerja pada proyek ini.</p>
                    </div>
                    @if($isLeader)
                        <button type="button" @click="showAddTeamModal = true" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center space-x-1">
                            <span>+ Tambah Anggota</span>
                        </button>
                    @endif
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($project->members as $member)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/80 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm shadow-2xs shrink-0 aspect-square">
                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center flex-wrap gap-1.5">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $member->user->name }}</h4>
                                        @if($member->user_id === $project->owner_id)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                                Leader (Owner)
                                            </span>
                                        @elseif($member->isLeader())
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-100 text-purple-700 border border-purple-200">
                                                Co-Leader
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                                Member
                                            </span>
                                        @endif
                                        @if($member->user_id === $currentUser->id)
                                            <span class="text-[10px] text-slate-400 font-medium">(Anda)</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $member->user->email }}</p>
                                </div>
                            </div>

                            @if($isLeader && $member->user_id !== $currentUser->id && $member->user_id !== $project->owner_id)
                                <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                                    {{-- Tombol Angkat / Turunkan Peran --}}
                                    @if($member->role === 'member')
                                        <form method="POST" action="{{ route('projects.members.update-role', [$project, $member]) }}" onsubmit="return confirm('Angkat {{ addslashes($member->user->name) }} menjadi Co-Leader? Pengguna ini akan memiliki hak untuk memimpin dan mengelola proyek.')" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="role" value="leader">
                                            <button type="submit" class="px-2.5 py-1.5 text-xs text-indigo-700 hover:text-white bg-indigo-50 hover:bg-indigo-600 border border-indigo-200 hover:border-indigo-600 rounded-xl font-semibold transition flex items-center space-x-1 shadow-2xs cursor-pointer" title="Angkat menjadi Co-Leader">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"/></svg>
                                                <span>Jadikan Co-Leader</span>
                                            </button>
                                        </form>
                                    @elseif($member->isLeader() && ($currentUser->id === $project->owner_id || $currentUser->isAdmin()))
                                        <form method="POST" action="{{ route('projects.members.update-role', [$project, $member]) }}" onsubmit="return confirm('Turunkan {{ addslashes($member->user->name) }} menjadi Member biasa? Hak manajerial proyek akan dinonaktifkan.')" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="role" value="member">
                                            <button type="submit" class="px-2.5 py-1.5 text-xs text-slate-700 hover:text-white bg-slate-100 hover:bg-slate-600 border border-slate-200 hover:border-slate-600 rounded-xl font-semibold transition flex items-center space-x-1 shadow-2xs cursor-pointer" title="Ubah kembali menjadi Member">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/></svg>
                                                <span>Jadikan Member</span>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tombol Hapus Anggota --}}
                                    @if(!$member->isLeader() || $project->owner_id === $currentUser->id || $currentUser->isAdmin())
                                        <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan {{ addslashes($member->user->name) }} dari tim proyek?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 text-xs text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-xl font-semibold transition flex items-center space-x-1 shadow-2xs cursor-pointer" title="Hapus dari Tim">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kolom Undangan Terkirim (Leader Only View) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Status Undangan ({{ $project->invitations->count() }})</h3>
                </div>
                @if($project->invitations->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-400 italic">
                        Belum ada riwayat undangan yang dikirimkan.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                        @foreach($project->invitations as $inv)
                            <div class="p-3.5 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-800 line-clamp-1">{{ $inv->email }}</span>
                                    @if($inv->isPending())
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                    @elseif($inv->isAccepted())
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Diterima</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">Ditolak</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>{{ $inv->created_at->diffForHumans() }}</span>
                                    @if($isLeader && $inv->isPending())
                                        <form method="POST" action="{{ route('projects.invitations.destroy', [$project, $inv]) }}" onsubmit="return confirm('Batalkan undangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:underline">Batalkan</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>


    </div>

    <!-- TAB 3: SUMBER DAYA & TIM (RESOURCES) -->
    <div x-show="activeTab === 'resources'" class="space-y-6" style="display: none;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Tim & Sumber Daya</h2>
                <p class="text-xs text-slate-500">Anggota tim pelaksana, peran, tarif honor, dan jadwal cuti / ketersediaan.</p>
            </div>
            @if($isLeader)
                <button @click="showNewResourceModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Anggota</span>
                </button>
            @endif
        </div>

        @if($project->resources->isEmpty())
            <div class="p-12 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Anggota Tim</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Tambahkan anggota tim atau staf penanggung jawab kegiatan.</p>
                @if($isLeader)
                    <button @click="showNewResourceModal = true" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">Tambah Anggota Sekarang</button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($project->resources as $res)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4" x-data="{ showLeaveModal: false }">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">{{ $res->role }}</span>
                                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $res->name }}</h3>
                                @if($res->email)
                                    <p class="text-xs text-slate-500">{{ $res->email }}</p>
                                @endif
                            </div>
                            @if($isLeader)
                                <form method="POST" action="{{ route('projects.resources.destroy', [$project, $res]) }}" onsubmit="return confirm('Hapus anggota ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="text-xs space-y-1 text-slate-600">
                            <div class="flex justify-between">
                                <span>Max jam kerja:</span>
                                <strong class="text-slate-800">{{ $res->max_hours_per_day }} jam/hari</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Tarif / jam:</span>
                                <strong class="text-slate-800">Rp {{ number_format($res->cost_per_hour, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Tugas aktif:</span>
                                <strong class="text-indigo-600">{{ $res->assignments->count() }} tugas</strong>
                            </div>
                        </div>

                        <!-- Cuti / Leaves -->
                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-700">Jadwal Cuti ({{ $res->leaves->count() }})</span>
                                @if($isLeader)
                                    <button @click="showLeaveModal = true" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-semibold">+ Tambah</button>
                                @endif
                            </div>
                            @if($res->leaves->isEmpty())
                                <span class="text-[11px] text-slate-400 italic">Siap bertugas penuh waktu.</span>
                            @else
                                <div class="space-y-1">
                                    @foreach($res->leaves as $lv)
                                        <div class="flex items-center justify-between text-[11px] bg-slate-50 px-2 py-1 rounded border border-slate-200">
                                            <span>{{ $lv->start_date->format('d M') }} - {{ $lv->end_date->format('d M') }} ({{ $lv->reason ?: 'Cuti' }})</span>
                                            @if($isLeader)
                                                <form method="POST" action="{{ route('projects.resources.leaves.destroy', [$project, $res, $lv]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-400 hover:text-rose-600">&times;</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Modal Cuti (Leader Only) -->
                        @if($isLeader)
                            <div x-show="showLeaveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-200" @click.away="showLeaveModal = false">
                                    <h4 class="text-sm font-bold text-slate-900 mb-2">Jadwal Cuti {{ $res->name }}</h4>
                                    <form method="POST" action="{{ route('projects.resources.leaves.store', [$project, $res]) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai Cuti</label>
                                            <input type="date" name="start_date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Akhir Cuti</label>
                                            <input type="date" name="end_date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Keterangan</label>
                                            <input type="text" name="reason" placeholder="Misal: Sakit, Izin kuliah" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                                        </div>
                                        <div class="flex justify-end space-x-2 pt-2">
                                            <button type="button" @click="showLeaveModal = false" class="px-3 py-1.5 border border-slate-300 text-xs rounded-xl font-semibold">Batal</button>
                                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-xl font-semibold">Simpan Cuti</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 4: KALENDER KERJA & HARI LIBUR (LEADER ONLY) -->
    @if($isLeader)
        <div x-show="activeTab === 'calendar'" class="space-y-6" style="display: none;">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Pengaturan Kalender Proyek</h2>
                    <p class="text-xs text-slate-500">Jadwal hari kerja mingguan dan hari libur spesifik untuk perhitungan durasi CPM.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hari Kerja Efektif</h4>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                                $workingDays = $project->workCalendar->working_days ?? [1,2,3,4,5];
                            @endphp
                            @foreach($dayNames as $num => $name)
                                <span class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ in_array($num, $workingDays) ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                                    {{ $name }} {{ in_array($num, $workingDays) ? '✓' : '' }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Standar: {{ $project->workCalendar->hours_per_day }} jam kerja per hari.</p>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hari Libur Khusus / Nasional</h4>
                        <form method="POST" action="{{ route('projects.holidays.store', $project) }}" class="flex gap-2 mb-4">
                            @csrf
                            <input type="text" name="name" placeholder="Nama hari libur..." required class="flex-1 px-3 py-2 border border-slate-300 rounded-xl text-xs">
                            <input type="date" name="date" required class="px-3 py-2 border border-slate-300 rounded-xl text-xs">
                            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">Tambah</button>
                        </form>

                        <div class="space-y-1.5 max-h-60 overflow-y-auto">
                            @forelse($project->workCalendar->holidays as $h)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                    <div>
                                        <span class="font-semibold text-slate-800">{{ $h->name }}</span>
                                        <span class="text-slate-500 ml-2">({{ \Carbon\Carbon::parse($h->date)->format('d M Y') }})</span>
                                    </div>
                                    <form method="POST" action="{{ route('projects.holidays.destroy', [$project, $h]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-600 transition">&times;</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Belum ada hari libur khusus yang ditambahkan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL TAMBAH TUGAS (LEADER ONLY) -->
    @if($isLeader)
        <div x-show="showNewTaskModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
            <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-slate-200 max-h-[85vh] overflow-y-auto" @click.away="showNewTaskModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Tambah Tugas Baru</h3>
                    <button @click="showNewTaskModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tugas / Aktivitas *</label>
                        <input type="text" name="name" required placeholder="Contoh: Booking Venue Acara" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                            <input type="date" name="start_date" value="{{ $project->start_date->format('Y-m-d') }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi (Hari Kerja) *</label>
                            <input type="number" name="duration_days" value="2" min="1" max="365" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Prioritas *</label>
                            <select name="priority" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                                <option value="low">Low (Rendah)</option>
                                <option value="medium" selected>Medium (Sedang)</option>
                                <option value="high">High (Tinggi)</option>
                                <option value="urgent">Urgent (Mendesak)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                            <select name="status" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                                <option value="todo" selected>To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Tugas</label>
                        <textarea name="description" rows="2" placeholder="Detail teknis tugas..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="showNewTaskModal = false" class="px-4 py-2 border border-slate-300 text-xs rounded-xl font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-xl font-semibold">Simpan Tugas</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL TAMBAH RESOURCE (LEADER ONLY) -->
        <div x-show="showNewResourceModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 max-h-[85vh] overflow-y-auto" @click.away="showNewResourceModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Tambah Anggota Tim / Sumber Daya</h3>
                    <button @click="showNewResourceModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('projects.resources.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Anggota *</label>
                        <input type="text" name="name" required placeholder="Contoh: Surya Pratama" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Peran / Jabatan *</label>
                            <input type="text" name="role" required placeholder="Contoh: Koordinator Acara" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Kontak / WA</label>
                            <input type="text" name="phone" placeholder="08123456789" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" placeholder="surya@arieldaud.com" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif / Jam (Rp)</label>
                            <input type="number" name="cost_per_hour" value="50000" min="0" step="1000" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Max Jam Kerja / Hari</label>
                            <input type="number" name="max_hours_per_day" value="8" min="1" max="24" step="0.5" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="showNewResourceModal = false" class="px-4 py-2 border border-slate-300 text-xs rounded-xl font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-xl font-semibold">Simpan Anggota</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL TAMBAH TIM PROYEK (LEADER ONLY) DENGAN DETEKSI REAL-TIME EMAIL -->
        <div x-show="showAddTeamModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200"
                 @click.away="closeModal()"
                 x-data="{
                    email: '',
                    role: 'member',
                    checking: false,
                    submitting: false,
                    status: null,
                    userFound: null,
                    message: '',
                    timer: null,
                    lastCheckedEmail: '',
                    checkEmail() {
                        const cleanEmail = this.email.trim().toLowerCase();
                        if (!cleanEmail || !cleanEmail.includes('@') || !cleanEmail.includes('.')) {
                            this.status = null;
                            this.userFound = null;
                            this.message = '';
                            this.checking = false;
                            this.lastCheckedEmail = '';
                            return;
                        }
                        if (cleanEmail === this.lastCheckedEmail && this.status !== null) {
                            return;
                        }
                        this.checking = true;
                        this.status = null;
                        clearTimeout(this.timer);
                        this.timer = setTimeout(() => {
                            fetch('{{ route('projects.members.check-email', $project, false) }}?email=' + encodeURIComponent(cleanEmail), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
                            .then(result => {
                                this.checking = false;
                                this.lastCheckedEmail = cleanEmail;
                                const data = result.data;
                                if (!result.ok) {
                                    this.status = 'error';
                                    this.message = (data && data.message) ? data.message : 'Gagal memeriksa email (Error ' + result.status + ').';
                                    this.userFound = null;
                                    return;
                                }
                                if (!data || !data.exists) {
                                    this.status = 'not_found';
                                    this.message = (data && data.message) ? data.message : 'Email tidak terdaftar di sistem EventPlan.';
                                    this.userFound = null;
                                } else if (data.is_owner || (data.user && data.user.id === {{ $currentUser->id }})) {
                                    this.status = 'is_self';
                                    this.message = 'Email ini adalah akun Anda sendiri.';
                                    this.userFound = data.user;
                                } else if (data.is_member) {
                                    this.status = 'already_member';
                                    this.message = 'Pengguna sudah menjadi anggota aktif tim proyek ini (' + (data.member_role || 'Anggota') + ').';
                                    this.userFound = data.user;
                                } else if (data.has_pending_invitation) {
                                    this.status = 'already_member';
                                    this.message = 'Pengguna ini sudah memiliki undangan aktif yang sedang menunggu persetujuan (approve).';
                                    this.userFound = data.user;
                                } else {
                                    this.status = 'available';
                                    this.message = 'Akun terdaftar ditemukan!';
                                    this.userFound = data.user;
                                }
                            })
                            .catch(err => {
                                this.checking = false;
                                this.status = 'error';
                                this.message = 'Gagal terhubung ke server untuk memeriksa email.';
                                this.userFound = null;
                            });
                        }, 250);
                    },
                    closeModal() {
                        this.email = '';
                        this.role = 'member';
                        this.status = null;
                        this.userFound = null;
                        this.message = '';
                        this.checking = false;
                        this.submitting = false;
                        this.lastCheckedEmail = '';
                        showAddTeamModal = false;
                    }
                 }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Undang Tim ke Proyek</h3>
                            <p class="text-xs text-slate-500">Kirim undangan ke pengguna terdaftar. Proyek akan aktif setelah di-approve oleh pengguna.</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 rounded-lg hover:bg-slate-100 transition">&times;</button>
                </div>

                <form method="POST" action="{{ route('projects.members.store', $project) }}" @submit="submitting = true" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alamat Email Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="email"
                                   name="email"
                                   x-model="email"
                                   @input="checkEmail()"
                                   required
                                   autocomplete="off"
                                   placeholder="Masukkan email yang terdaftar..."
                                   class="w-full px-3.5 py-2.5 pr-10 border rounded-xl text-xs transition focus:outline-none"
                                   :class="{
                                       'border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100': status === null && !checking,
                                       'border-amber-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100': checking,
                                       'border-emerald-500 ring-2 ring-emerald-100': status === 'available',
                                       'border-rose-400 ring-2 ring-rose-100': status === 'not_found' || status === 'error',
                                       'border-amber-400 ring-2 ring-amber-100': status === 'already_member' || status === 'is_self'
                                   }">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <template x-if="checking">
                                    <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!checking && status === 'available'">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="!checking && (status === 'not_found' || status === 'error')">
                                    <svg class="h-4 w-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </template>
                                <template x-if="!checking && (status === 'already_member' || status === 'is_self')">
                                    <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </template>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Sistem otomatis mendeteksi apakah email terdaftar di sistem EventPlan.</p>
                    </div>

                    <!-- Deteksi: Loading / Memeriksa -->
                    <div x-show="checking" class="p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl flex items-center space-x-2 text-xs text-indigo-700">
                        <svg class="animate-spin h-3.5 w-3.5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memeriksa ketersediaan email di database...</span>
                    </div>

                    <!-- Deteksi: TIDAK TERDAFTAR -->
                    <div x-show="!checking && status === 'not_found'" class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl space-y-1">
                        <div class="flex items-center space-x-2 text-rose-800 font-semibold text-xs">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Email Belum Terdaftar</span>
                        </div>
                        <p class="text-[11px] text-rose-700 leading-relaxed" x-text="message"></p>
                        <p class="text-[11px] text-slate-500 pt-1 border-t border-rose-100 mt-1">
                            Calon anggota harus membuat akun terlebih dahulu sebelum dapat dimasukkan ke tim proyek.
                        </p>
                    </div>

                    <!-- Deteksi: ERROR SISTEM / SERVER -->
                    <div x-show="!checking && status === 'error'" class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl space-y-1">
                        <div class="flex items-center space-x-2 text-rose-800 font-semibold text-xs">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terjadi Kendala</span>
                        </div>
                        <p class="text-[11px] text-rose-700 leading-relaxed" x-text="message"></p>
                    </div>

                    <!-- Deteksi: SUDAH JADI ANGGOTA ATAU AKUN SENDIRI -->
                    <div x-show="!checking && (status === 'already_member' || status === 'is_self')" class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1">
                        <div class="flex items-center space-x-2 text-amber-800 font-semibold text-xs">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="status === 'is_self' ? 'Akun Anda Sendiri' : 'Sudah Bergabung'"></span>
                        </div>
                        <p class="text-[11px] text-amber-700 leading-relaxed" x-text="message"></p>
                    </div>

                    <!-- Deteksi: AKUN DITEMUKAN & TERSEDIA -->
                    <div x-show="!checking && status === 'available' && userFound" class="p-3.5 bg-emerald-50/80 border border-emerald-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center space-x-1">
                                <svg class="w-3 h-3 text-emerald-600 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Akun Terdaftar</span>
                            </span>
                            <span class="text-[11px] text-emerald-700 font-medium">Siap ditambahkan</span>
                        </div>
                        <div class="flex items-center space-x-3 pt-1">
                            <div class="w-10 h-10 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0" x-text="userFound ? userFound.name.substring(0, 2).toUpperCase() : 'EP'">
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-slate-900 truncate" x-text="userFound ? userFound.name : ''"></h4>
                                <p class="text-[11px] text-slate-500 truncate" x-text="userFound ? (userFound.username ? '@' + userFound.username + ' • ' + userFound.email : userFound.email) : ''"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Pemilihan Peran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Peran dalam Tim <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer border rounded-xl p-3 flex flex-col space-y-1 transition"
                                   :class="role === 'member' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-500' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900">Member</span>
                                    <input type="radio" name="role" value="member" x-model="role" class="text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <span class="text-[10px] text-slate-500 leading-tight">Mengerjakan tugas dan melihat linimasa proyek.</span>
                            </label>
                            <label class="cursor-pointer border rounded-xl p-3 flex flex-col space-y-1 transition"
                                   :class="role === 'leader' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-500' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900">Co-Leader</span>
                                    <input type="radio" name="role" value="leader" x-model="role" class="text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <span class="text-[10px] text-slate-500 leading-tight">Dapat mengelola tugas, anggota tim, dan jadwal.</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border border-slate-300 text-xs rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="status !== 'available' || checking || submitting"
                                class="px-5 py-2 text-xs rounded-xl font-semibold text-white transition flex items-center space-x-1.5"
                                :class="status === 'available' && !checking && !submitting ? 'bg-indigo-600 hover:bg-indigo-700 shadow-sm cursor-pointer' : 'bg-slate-300 cursor-not-allowed opacity-60'">
                            <template x-if="submitting">
                                <svg class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </template>
                            <template x-if="!submitting">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </template>
                            <span x-text="submitting ? 'Mengirim Undangan...' : 'Kirim Undangan Tim'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
