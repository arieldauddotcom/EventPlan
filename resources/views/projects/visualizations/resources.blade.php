@extends('layouts.app')

@section('title', 'Beban Tim: ' . $project->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs">
                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 font-semibold hover:underline">&larr; Kembali ke Detail Proyek</a>
                <span class="text-slate-400">•</span>
                <span class="text-slate-500 truncate max-w-[180px] sm:max-w-none">{{ $project->name }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Resource Workload & Capacity</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Visualisasi pembebanan kerja harian sumber daya tim, deteksi bentrok cuti, dan over-allocation (>100%).
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('projects.gantt', $project) }}" class="flex-1 sm:flex-initial text-center px-3 py-2 rounded-xl border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold">
                Gantt Chart
            </a>
            <a href="{{ route('projects.pert', $project) }}" class="flex-1 sm:flex-initial text-center px-3 py-2 rounded-xl border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold">
                PERT Diagram
            </a>
        </div>
    </div>

    <!-- Ringkasan Status Resource -->
    @php
        $totalOverAllocated = 0;
        foreach($resourceWorkloads as $rw) {
            foreach($rw['daily'] as $d) {
                if ($d['is_overallocated']) {
                    $totalOverAllocated++;
                    break;
                }
            }
        }
    @endphp

    @if($totalOverAllocated > 0)
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between text-xs text-rose-800">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span><strong>Peringatan Over-Allocation:</strong> Ada {{ $totalOverAllocated }} anggota tim yang memiliki total beban kerja di atas 100% pada hari tertentu.</span>
            </div>
        </div>
    @endif

    <!-- Heatmap & Bar Chart Resource Timeline -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Matriks Beban Kerja Harian (% Kapasitas)</h2>
            <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-[11px] text-slate-500">
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-slate-100 border border-slate-200"></span>
                    <span>Kosong (0%)</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-emerald-100 text-emerald-800"></span>
                    <span>Normal (&le; 100%)</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-rose-500"></span>
                    <span>Over-allocated (> 100%)</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-amber-200"></span>
                    <span>Cuti/Libur</span>
                </div>
            </div>
        </div>

        <div class="sm:hidden px-4 py-2 bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            <span>Geser tabel ke kanan untuk melihat jadwal harian</span>
        </div>

        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            @if(empty($resourceWorkloads))
                <div class="p-12 text-center text-slate-400 text-xs">
                    Belum ada sumber daya tim yang ditambahkan ke proyek ini.
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] text-slate-500">
                            <th class="p-3 sticky left-0 bg-slate-50 z-20 min-w-[150px] sm:min-w-[200px] border-r border-slate-200">Anggota Tim / Peran</th>
                            @foreach($days as $day)
                                <th class="p-2 text-center min-w-[42px] font-medium {{ $day->isWeekend() ? 'bg-slate-100/60 text-slate-400' : '' }}">
                                    <div class="font-bold text-slate-700">{{ $day->format('d') }}</div>
                                    <div class="text-[9px] uppercase">{{ $day->format('D') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($resourceWorkloads as $rw)
                            @php
                                $res = $rw['resource'];
                                $daily = $rw['daily'];
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Info Resource (Sticky Column) -->
                                <td class="p-3 sticky left-0 bg-white hover:bg-slate-50 z-10 border-r border-slate-200">
                                    <div class="font-semibold text-slate-800">{{ $res->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $res->role ?? 'Anggota Tim' }}</div>
                                </td>

                                <!-- Grid Hari -->
                                @foreach($days as $day)
                                    @php
                                        $dStr = $day->format('Y-m-d');
                                        $cell = $daily[$dStr] ?? ['is_leave' => false, 'total_load' => 0, 'is_overallocated' => false, 'active_tasks' => []];
                                        $isWeekend = $day->isWeekend();
                                    @endphp

                                    <td class="p-1 text-center border-r border-slate-100 {{ $isWeekend ? 'bg-slate-50/50' : '' }}">
                                        @if($cell['is_leave'])
                                            <div class="w-full h-8 rounded-lg bg-amber-100 border border-amber-300 text-amber-800 font-bold flex items-center justify-center text-[10px]" title="Sedang Cuti">
                                                Cuti
                                            </div>
                                        @elseif($cell['total_load'] > 0)
                                            <div class="w-full h-8 rounded-lg flex flex-col items-center justify-center font-bold text-[10px] {{ $cell['is_overallocated'] ? 'bg-rose-500 text-white shadow-xs' : 'bg-emerald-100 text-emerald-800 border border-emerald-300' }}"
                                                 title="{{ collect($cell['active_tasks'])->map(fn($t) => $t['task'].' ('.$t['allocation'].'%)')->implode(', ') }}">
                                                {{ $cell['total_load'] }}%
                                            </div>
                                        @else
                                            <div class="w-full h-8 rounded-lg text-slate-300 flex items-center justify-center text-[10px]">
                                                -
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
