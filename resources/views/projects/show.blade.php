@extends('layouts.app')

@section('content')
@php
    $currentUser = auth()->user();
    $isLeader = $project->isLeader($currentUser);
@endphp

<div class="space-y-8" x-data="{ activeTab: 'tasks', showNewTaskModal: false, showNewResourceModal: false, showInviteModal: false, editTask: null }">
    <!-- Header Proyek -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
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
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mt-1">{{ $project->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Jadwal: <span class="font-semibold text-slate-700">{{ $project->start_date->format('d M Y') }}</span> s/d <span class="font-semibold text-slate-700">{{ $project->deadline->format('d M Y') }}</span>
                • Kalender: <span class="text-indigo-600 font-medium">{{ $project->workCalendar->name }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2" x-data="{ openPdfModal: false, startDate: '{{ $project->start_date->format('Y-m-d') }}', endDate: '{{ $project->deadline->format('Y-m-d') }}' }">
            <!-- Visualisasi (Tersedia bagi Leader maupun Member) -->
            <a href="{{ route('projects.gantt', $project) }}" class="px-3.5 py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition flex items-center space-x-1">
                <span>Gantt Chart</span>
            </a>
            <a href="{{ route('projects.pert', $project) }}" class="px-3.5 py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition flex items-center space-x-1">
                <span>PERT Diagram</span>
            </a>
            <a href="{{ route('projects.resources-chart', $project) }}" class="px-3.5 py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition flex items-center space-x-1">
                <span>Beban Tim</span>
            </a>
            <a href="{{ route('projects.export-tasks', $project) }}" class="px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-300 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 transition flex items-center space-x-1.5" title="Unduh CSV">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Ekspor CSV</span>
            </a>
            <button type="button" @click="openPdfModal = true" class="px-3.5 py-2 rounded-xl bg-rose-50 border border-rose-300 text-xs font-semibold text-rose-800 hover:bg-rose-100 transition flex items-center space-x-1.5" title="Filter & Ekspor Laporan PDF">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Laporan PDF</span>
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

    <!-- Tab Navigasi -->
    <div class="flex border-b border-slate-200 space-x-6 text-sm font-semibold overflow-x-auto">
        <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition flex items-center space-x-2 shrink-0">
            <span>Daftar Tugas</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $project->tasks->count() }}</span>
        </button>
        <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition flex items-center space-x-2 shrink-0">
            <span>Kolaborasi & Anggota</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-50 text-indigo-700 font-bold">{{ $project->members->count() }}</span>
        </button>
        <button @click="activeTab = 'resources'" :class="activeTab === 'resources' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition flex items-center space-x-2 shrink-0">
            <span>Tim & Sumber Daya</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $project->resources->count() }}</span>
        </button>
        @if($isLeader)
            <button @click="activeTab = 'calendar'" :class="activeTab === 'calendar' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition shrink-0">
                <span>Kalender Kerja & Hari Libur</span>
            </button>
        @endif
    </div>

    <!-- TAB 1: TUGAS (TASKS & DEPENDENCY) -->
    <div x-show="activeTab === 'tasks'" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Manajemen Tugas Proyek</h2>
                <p class="text-xs text-slate-500">Tugas, durasi hari kerja, prioritas, status, serta relasi dependensi (FS/SS).</p>
            </div>
            @if($isLeader)
                <button @click="showNewTaskModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Tugas</span>
                </button>
            @endif
        </div>

        @if($project->tasks->isEmpty())
            <div class="p-12 text-center bg-white rounded-2xl border border-dashed border-slate-300">
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
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4" x-data="{ showDepModal: false, showAssignModal: false, showProgressModal: false }">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center space-x-2">
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
                                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $t->name }}</h3>
                                @if($t->description)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $t->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-2">
                                    <span>Durasi: <strong class="text-slate-800">{{ $t->duration_days }} hari kerja</strong></span>
                                    <span>Mulai: <strong class="text-slate-800">{{ $t->start_date->format('d M Y') }}</strong></span>
                                    <span>Selesai: <strong class="text-slate-800">{{ $t->end_date->format('d M Y') }}</strong></span>
                                    <span>Float: <strong class="{{ $t->is_critical ? 'text-rose-600 font-bold' : 'text-slate-800' }}">{{ $t->total_float ?? 0 }} hari</strong></span>
                                    @if($t->is_critical)
                                        <span class="text-rose-600 font-bold">• Jalur Kritis (CPM)</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                @if($canEditProgress)
                                    <button @click="showProgressModal = true" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-semibold transition">
                                        Update Progres
                                    </button>
                                @endif

                                @if($isLeader)
                                    <a href="{{ route('projects.tasks.edit', [$project, $t]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                    <button @click="showAssignModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                        + Tugaskan ({{ $t->assignments->count() }})
                                    </button>
                                    <button @click="showDepModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                        + Dependency ({{ $t->predecessors->count() }})
                                    </button>
                                    <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $t]) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition">
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
                            <div x-show="showProgressModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-200" @click.away="showProgressModal = false">
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
                            <div x-show="showDepModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="showDepModal = false">
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
                                        <div class="grid grid-cols-2 gap-3">
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
                            <div x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
                                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="showAssignModal = false">
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
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Kolaborasi & Anggota Tim</h2>
                <p class="text-xs text-slate-500">Daftar pengguna dengan akses proyek (Leader & Member) serta undangan pending.</p>
            </div>
            @if($isLeader)
                <button @click="showInviteModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Undang Anggota</span>
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Anggota Aktif -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Anggota Proyek ({{ $project->members->count() }})</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($project->members as $member)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm">
                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $member->user->name }}</h4>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $member->isLeader() ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $member->role }}
                                        </span>
                                        @if($member->user_id === $currentUser->id)
                                            <span class="text-[10px] text-slate-400">(Anda)</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $member->user->email }}</p>
                                </div>
                            </div>

                            @if($isLeader && !$member->isLeader() && $member->user_id !== $project->owner_id)
                                <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}" onsubmit="return confirm('Keluarkan anggota ini dari proyek?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-lg font-semibold transition">
                                        Keluarkan
                                    </button>
                                </form>
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

        <!-- MODAL UNDANG ANGGOTA BARU (LEADER ONLY) -->
        @if($isLeader)
            <div x-show="showInviteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="showInviteModal = false">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-bold text-slate-900">Undang Anggota ke Proyek</h3>
                        <button @click="showInviteModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">
                        Masukkan email pengguna terdaftar. Setelah menerima undangan, mereka akan menjadi <strong>Member</strong> pada proyek ini.
                    </p>
                    <form method="POST" action="{{ route('projects.invitations.store', $project) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Pengguna *</label>
                            <input type="email" name="email" required placeholder="user@example.com" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Harus akun yang sudah terdaftar di EventPlan.</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Singkat (Opsional)</label>
                            <textarea name="message" rows="2" placeholder="Mohon bantuan untuk koordinasi teknis acara..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" @click="showInviteModal = false" class="px-4 py-2 border border-slate-300 text-xs rounded-xl font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-xl font-semibold">Kirim Undangan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
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
        <div x-show="showNewTaskModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200" @click.away="showNewTaskModal = false">
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
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                            <input type="date" name="start_date" value="{{ $project->start_date->format('Y-m-d') }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi (Hari Kerja) *</label>
                            <input type="number" name="duration_days" value="2" min="1" max="365" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
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
        <div x-show="showNewResourceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="showNewResourceModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Tambah Anggota Tim / Sumber Daya</h3>
                    <button @click="showNewResourceModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('projects.resources.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Anggota *</label>
                        <input type="text" name="name" required placeholder="Contoh: Sarah Anindya" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
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
                        <input type="email" name="email" placeholder="sarah@example.com" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
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
    @endif
</div>
@endsection
