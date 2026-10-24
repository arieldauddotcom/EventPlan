@extends('layouts.app')

@section('title', 'Gantt Chart: ' . $project->name)

@section('content')
<div class="space-y-6" x-data="{
    filterCritical: false,
    dayWidth: 52,
    zoomIn() { this.dayWidth = Math.min(120, this.dayWidth + 12); },
    zoomOut() { this.dayWidth = Math.max(34, this.dayWidth - 12); },
    resetZoom() { this.dayWidth = 52; }
}">
    <!-- Header Responsif Mobile -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs">
                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 font-semibold hover:underline">&larr; Detail Proyek</a>
                <span class="text-slate-400">•</span>
                <span class="text-slate-500 truncate max-w-[200px]">{{ $project->name }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Gantt Chart Timeline</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Visualisasi linimasa tugas panjang, progres, ketergantungan (dependencies), dan jalur kritis (CPM).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2" x-data="{ openGanttPdfModal: false, startDate: '{{ $minDate->format('Y-m-d') }}', endDate: '{{ $maxDate->format('Y-m-d') }}' }">
            <!-- Zoom Controls untuk Atur Panjang Kolom / Garis Waktu -->
            <div class="inline-flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200 text-xs">
                <button type="button" @click="zoomOut()" class="p-1.5 hover:bg-white rounded-lg text-slate-600 hover:text-slate-900 transition" title="Persempit Garis Waktu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </button>
                <button type="button" @click="resetZoom()" class="px-2 py-1 hover:bg-white rounded-lg text-slate-700 font-semibold text-[11px] transition" title="Reset Skala Normal">
                    <span x-text="dayWidth + 'px'"></span>
                </button>
                <button type="button" @click="zoomIn()" class="p-1.5 hover:bg-white rounded-lg text-slate-600 hover:text-slate-900 transition" title="Perpanjang Garis Waktu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>

            <button @click="filterCritical = !filterCritical" :class="filterCritical ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs font-semibold transition flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full" :class="filterCritical ? 'bg-white' : 'bg-rose-500'"></span>
                <span x-text="filterCritical ? 'Semua Tugas' : 'Sorot CPM'"></span>
            </button>

            <button type="button" @click="openGanttPdfModal = true" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-rose-50 border border-rose-300 text-xs font-semibold text-rose-800 hover:bg-rose-100 transition flex items-center space-x-1.5" title="Filter & Ekspor PDF">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>PDF</span>
            </button>

            <a href="{{ route('projects.pert', $project) }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                PERT &rarr;
            </a>

            <!-- Modal Filter & Ekspor PDF -->
            <div x-show="openGanttPdfModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="openGanttPdfModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200 text-left max-h-[85vh] overflow-y-auto">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            <h3 class="text-base font-bold text-slate-800">Ekspor Laporan PDF</h3>
                        </div>
                        <button @click="openGanttPdfModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <p class="text-xs text-slate-500 mt-3">
                        Tentukan rentang tanggal tugas yang hendak dicetak ke dalam laporan atau gunakan bawaan kalender.
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
                            <button type="button" @click="openGanttPdfModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
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
        </div>
    </div>

    <!-- Ringkasan CPM -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Estimasi Durasi Proyek</span>
            <p class="text-xl font-bold text-indigo-600 mt-0.5">{{ $cpm['project_duration'] }} Hari Kerja</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Tugas di Jalur Kritis</span>
            <p class="text-xl font-bold text-rose-600 mt-0.5">{{ $cpm['critical_tasks']->count() }} Tugas</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Rentang Linimasa</span>
            <p class="text-xs font-bold text-slate-800 mt-1.5">{{ $minDate->format('d M Y') }} s/d {{ $maxDate->format('d M Y') }} ({{ $totalDays }} Hari)</p>
        </div>
    </div>

    <!-- Timeline Gantt Container Panjang Horisontal -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        @if($tasks->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm font-semibold text-slate-700">Belum ada tugas untuk digambarkan pada linimasa.</p>
                <a href="{{ route('projects.show', $project) }}" class="text-xs text-indigo-600 font-bold hover:underline mt-2 inline-block">Tambah Tugas Sekarang &rarr;</a>
            </div>
        @else
            <!-- Petunjuk Scroll Mobile -->
            <div class="sm:hidden px-4 py-2 bg-indigo-50 border-b border-indigo-100 flex items-center justify-between text-[11px] text-indigo-700 font-medium">
                <span class="flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1 text-indigo-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Geser kanan &rarr; untuk melihat seluruh linimasa
                </span>
                <span class="text-[10px] text-indigo-400 font-mono" x-text="dayWidth + 'px'"></span>
            </div>

            <!-- Scrollable Timeline -->
            <div class="overflow-x-auto select-none" id="gantt-scroll-container" style="-webkit-overflow-scrolling: touch;">
                <div class="inline-block min-w-full p-6"
                     :style="'min-width: calc(340px + ' + ({{ $totalDays }} * dayWidth) + 'px);'">

                    <!-- Header Timeline Tanggal & Hari -->
                    <div class="flex border-b border-slate-200 pb-2">
                        <!-- Kolom Header Tugas Kiri Kunci -->
                        <div class="w-[340px] shrink-0 pr-4 flex items-center justify-between text-xs font-bold text-slate-600 uppercase tracking-wider">
                            <span>Daftar Tugas ({{ $tasks->count() }})</span>
                            <span class="text-[10px] text-slate-400 font-normal">Progress / Durasi</span>
                        </div>

                        <!-- Kolom Tanggal Horisontal Panjang -->
                        <div class="flex">
                            @foreach($period as $date)
                                @php
                                    $isWeekend = in_array($date->dayOfWeekIso, [6, 7]);
                                    $isToday = $date->isToday();
                                @endphp
                                <div class="text-center shrink-0 border-r border-slate-100 {{ $isToday ? 'bg-indigo-50/70 font-bold text-indigo-700' : ($isWeekend ? 'bg-slate-50 text-slate-400' : 'text-slate-600') }}"
                                     :style="'width: ' + dayWidth + 'px;'">
                                    <div class="text-[9px] uppercase tracking-tight text-slate-400">{{ substr($date->locale('id')->isoFormat('MMM'), 0, 3) }}</div>
                                    <div class="text-xs font-bold {{ $isToday ? 'text-indigo-600' : '' }}">{{ $date->format('d') }}</div>
                                    <div class="text-[9px] {{ $isWeekend ? 'text-slate-400 font-semibold' : 'text-slate-400' }}">{{ substr($date->locale('id')->isoFormat('ddd'), 0, 1) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Area Linimasa Tugas & Garis Konektor Dependencies -->
                    <div class="relative mt-2" id="gantt-tasks-wrapper">
                        <!-- SVG Layer untuk Garis Hubung Dependencies Predecessor -> Successor -->
                        <svg class="absolute inset-0 pointer-events-none z-10 w-full h-full" id="gantt-svg-canvas">
                            <defs>
                                <marker id="arrow-critical" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                    <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#e11d48" />
                                </marker>
                                <marker id="arrow-normal" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                    <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#64748b" />
                                </marker>
                            </defs>
                            <g id="gantt-dependency-lines"></g>
                        </svg>

                        <!-- Baris-Baris Tugas -->
                        <div class="space-y-3 pt-2">
                            @foreach($tasks as $t)
                                @php
                                    $startOffset = $minDate->diffInDays($t->start_date);
                                    $durationDays = max(1, $t->start_date->diffInDays($t->end_date) + 1);
                                    $isCritical = $t->is_critical;
                                    $predecessorIds = $t->predecessors->pluck('id')->toArray();
                                @endphp

                                <div class="flex items-center hover:bg-slate-50/70 rounded-xl transition py-1.5 group/row"
                                     x-show="!filterCritical || {{ $isCritical ? 'true' : 'false' }}"
                                     data-task-id="{{ $t->id }}"
                                     data-predecessors="{{ json_encode($predecessorIds) }}"
                                     data-is-critical="{{ $isCritical ? '1' : '0' }}">

                                    <!-- Kolom Info Tugas Sisi Kiri -->
                                    <div class="w-[340px] shrink-0 pr-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                                            @if($isCritical)
                                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 ring-2 ring-rose-200" title="Jalur Kritis (Float 0)"></span>
                                            @else
                                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 shrink-0"></span>
                                            @endif
                                            @if($project->isLeader(auth()->user()))
                                                <a href="{{ route('projects.tasks.edit', [$project, $t]) }}" class="text-xs font-semibold text-slate-800 hover:text-indigo-600 truncate transition" title="Klik untuk edit tugas: {{ $t->name }}">
                                                    {{ $t->name }}
                                                </a>
                                            @else
                                                <span class="text-xs font-semibold text-slate-800 truncate" title="{{ $t->name }}">
                                                    {{ $t->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2 shrink-0 text-[10px]">
                                            @if($project->isLeader(auth()->user()))
                                                <a href="{{ route('projects.tasks.edit', [$project, $t]) }}" class="text-slate-400 hover:text-indigo-600 transition p-1" title="Edit Tugas">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                            @endif
                                            <span class="font-bold {{ $t->progress_percentage == 100 ? 'text-emerald-600' : 'text-slate-600' }}">
                                                {{ $t->progress_percentage }}%
                                            </span>
                                            <span class="text-slate-400">|</span>
                                            <span class="text-slate-500 font-mono">{{ $t->duration_days }}h</span>
                                        </div>
                                    </div>

                                    <!-- Area Garis Waktu Panjang (Grid Kolom + Bar Task) -->
                                    <div class="relative shrink-0 flex items-center h-10"
                                         :style="'width: calc(' + ({{ $totalDays }} * dayWidth) + 'px);'">

                                        <!-- Background Garis Vertikal per Hari -->
                                        <div class="absolute inset-0 flex pointer-events-none">
                                            @foreach($period as $d)
                                                <div class="h-full border-r border-slate-100 shrink-0 {{ in_array($d->dayOfWeekIso, [6, 7]) ? 'bg-slate-50/60' : '' }}"
                                                     :style="'width: ' + dayWidth + 'px;'"></div>
                                            @endforeach
                                        </div>

                                        <!-- Bar Linimasa Gantt -->
                                        <div class="gantt-task-bar absolute h-7 rounded-xl shadow-xs flex items-center px-3 text-[11px] font-semibold text-white overflow-hidden transition-all duration-150 group z-20 cursor-pointer
                                            {{ $isCritical ? 'bg-rose-600 ring-2 ring-rose-200/90' : 'bg-indigo-600 ring-2 ring-indigo-200/70' }}"
                                             id="task-bar-{{ $t->id }}"
                                             :style="'left: calc(' + ({{ $startOffset }} * dayWidth) + 'px); width: calc(' + ({{ $durationDays }} * dayWidth) + 'px); min-width: 42px;'">

                                            <!-- Progress Shading -->
                                            <div class="absolute inset-y-0 left-0 bg-black/25 pointer-events-none rounded-l-xl"
                                                 style="width: {{ $t->progress_percentage }}%;"></div>

                                            <span class="relative z-10 truncate drop-shadow-xs">
                                                {{ $t->name }} ({{ $t->progress_percentage }}%)
                                            </span>

                                            <!-- Penanda Kritis di Ujung Bar -->
                                            @if($isCritical)
                                                <span class="relative z-10 ml-auto pl-1 text-[9px] font-black uppercase tracking-wider text-rose-200 shrink-0">KRITIS</span>
                                            @endif

                                            <!-- Tooltip Rincian Tugas -->
                                            <div class="absolute hidden group-hover:flex flex-col bottom-full left-1/2 -translate-x-1/2 mb-2 p-2.5 bg-slate-900/95 text-white text-[10px] rounded-xl shadow-xl whitespace-nowrap z-50 pointer-events-none backdrop-blur-xs">
                                                <span class="font-bold text-xs">{{ $t->name }}</span>
                                                <span class="text-slate-300 mt-0.5">{{ $t->start_date->format('d M') }} — {{ $t->end_date->format('d M Y') }} ({{ $durationDays }} hari)</span>
                                                <span class="text-slate-300">Durasi Kerja: {{ $t->duration_days }} hari • Float: {{ $t->total_float ?? 0 }} hari</span>
                                                @if($isCritical)
                                                    <span class="text-rose-300 font-bold mt-0.5">Jalur Kritis (Critical Path)</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Label Indikator Status & Float di Kanan Bar jika Bar Sempit -->
                                        <div class="absolute text-[10px] text-slate-400 font-medium pl-2 pointer-events-none flex items-center space-x-1.5"
                                             :style="'left: calc(' + (({{ $startOffset }} + {{ $durationDays }}) * dayWidth) + 'px);'">
                                            @if($isCritical)
                                                <span class="text-[9px] font-bold text-rose-600 uppercase tracking-tight">Kritis</span>
                                            @elseif($t->total_float !== null)
                                                <span class="text-[9px] text-slate-400">Float {{ $t->total_float }}h</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legenda Penjelasan Garis & Jalur Kritis -->
            <div class="bg-slate-50 p-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-600">
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <span class="w-3.5 h-3.5 rounded bg-indigo-600"></span>
                        <span class="font-medium text-slate-700">Tugas Normal</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-3.5 h-3.5 rounded bg-rose-600 ring-2 ring-rose-200"></span>
                        <span class="font-medium text-rose-700">Tugas Jalur Kritis (Float 0)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-6 h-0.5 bg-rose-500 inline-block"></span>
                        <span class="font-medium text-rose-600">Garis Dependensi Kritis</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-6 h-0.5 bg-slate-400 inline-block"></span>
                        <span>Garis Dependensi Normal</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-3.5 h-3.5 rounded bg-slate-200"></span>
                        <span>Akhir Pekan</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 italic">
                    Gunakan tombol zoom (+ / -) di atas untuk mengatur kerapatan & panjang garis waktu.
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const svgCanvas = document.getElementById('gantt-svg-canvas');
        const linesGroup = document.getElementById('gantt-dependency-lines');
        const tasksWrapper = document.getElementById('gantt-tasks-wrapper');

        function drawDependencyLines() {
            if (!linesGroup || !tasksWrapper) return;
            linesGroup.innerHTML = '';

            const wrapperRect = tasksWrapper.getBoundingClientRect();
            const taskRows = document.querySelectorAll('[data-task-id]');
            const barPositions = {};

            // Catat posisi tiap bar
            taskRows.forEach(row => {
                const taskId = row.getAttribute('data-task-id');
                const bar = document.getElementById('task-bar-' + taskId);
                if (bar && row.style.display !== 'none' && !row.hasAttribute('hidden')) {
                    const rect = bar.getBoundingClientRect();
                    barPositions[taskId] = {
                        left: rect.left - wrapperRect.left,
                        right: rect.right - wrapperRect.left,
                        top: rect.top - wrapperRect.top,
                        bottom: rect.bottom - wrapperRect.top,
                        centerY: (rect.top + rect.bottom) / 2 - wrapperRect.top,
                        isCritical: row.getAttribute('data-is-critical') === '1'
                    };
                }
            });

            // Gambar garis ortogonal berpanah dari predecessor -> successor
            taskRows.forEach(row => {
                const succId = row.getAttribute('data-task-id');
                const predsJson = row.getAttribute('data-predecessors');
                if (!predsJson) return;

                const preds = JSON.parse(predsJson);
                const succPos = barPositions[succId];
                if (!succPos) return;

                preds.forEach(predId => {
                    const predPos = barPositions[predId];
                    if (!predPos) return;

                    const isCritRel = predPos.isCritical && succPos.isCritical;
                    const strokeColor = isCritRel ? '#e11d48' : '#94a3b8';
                    const strokeWidth = isCritRel ? '2' : '1.5';
                    const marker = isCritRel ? 'url(#arrow-critical)' : 'url(#arrow-normal)';

                    // Garis ortogonal: Ujung kanan predPos -> belok vertikal -> ujung kiri succPos
                    const startX = predPos.right;
                    const startY = predPos.centerY;
                    const targetX = succPos.left - 4;
                    const targetY = succPos.centerY;

                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    let d = '';

                    if (targetX > startX + 16) {
                        const midX = startX + 12;
                        d = `M ${startX} ${startY} L ${midX} ${startY} L ${midX} ${targetY} L ${targetX} ${targetY}`;
                    } else {
                        const midY = startY + (targetY > startY ? 16 : -16);
                        const loopX = Math.min(startX, targetX) - 14;
                        d = `M ${startX} ${startY} L ${startX + 10} ${startY} L ${startX + 10} ${midY} L ${loopX} ${midY} L ${loopX} ${targetY} L ${targetX} ${targetY}`;
                    }

                    path.setAttribute('d', d);
                    path.setAttribute('fill', 'none');
                    path.setAttribute('stroke', strokeColor);
                    path.setAttribute('stroke-width', strokeWidth);
                    path.setAttribute('marker-end', marker);
                    if (isCritRel) {
                        path.setAttribute('stroke-dasharray', '4 2');
                    }
                    linesGroup.appendChild(path);
                });
            });
        }

        // Gambar saat load, resize, dan bila skala waktu diganti
        setTimeout(drawDependencyLines, 100);
        window.addEventListener('resize', drawDependencyLines);

        const observer = new MutationObserver(function() {
            setTimeout(drawDependencyLines, 50);
        });
        if (tasksWrapper) {
            observer.observe(tasksWrapper, { attributes: true, subtree: true, childList: true });
        }
    });
</script>
@endsection
