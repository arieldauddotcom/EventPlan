@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{
    zoom: 1,
    panX: 0,
    panY: 0,
    isDragging: false,
    startX: 0,
    startY: 0,
    startPan(e) {
        this.isDragging = true;
        this.startX = e.clientX - this.panX;
        this.startY = e.clientY - this.panY;
    },
    onPan(e) {
        if (!this.isDragging) return;
        this.panX = e.clientX - this.startX;
        this.panY = e.clientY - this.startY;
    },
    endPan() {
        this.isDragging = false;
    },
    zoomIn() { this.zoom = Math.min(2, this.zoom + 0.15); },
    zoomOut() { this.zoom = Math.max(0.5, this.zoom - 0.15); },
    resetView() { this.zoom = 1; this.panX = 0; this.panY = 0; }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs">
                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 font-semibold hover:underline">&larr; Kembali ke Detail Proyek</a>
                <span class="text-slate-400">•</span>
                <span class="text-slate-500">{{ $project->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">PERT / Network Diagram</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Representasi graf simpul aktivitas (Activity on Node) dengan jalur kritis (Critical Path Method) bertanda merah.
            </p>
        </div>

        <!-- Tombol Kontrol Zoom & Navigasi -->
        <div class="flex items-center space-x-2">
            <button @click="zoomIn()" class="p-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold" title="Perbesar (+)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
            <button @click="zoomOut()" class="p-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold" title="Perkecil (-)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
            </button>
            <button @click="resetView()" class="px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                Reset
            </button>
            <a href="{{ route('projects.gantt', $project) }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                Gantt Chart &rarr;
            </a>
        </div>
    </div>

    <!-- Ringkasan Jalur Kritis -->
    <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        <div>
            <span class="font-bold text-rose-800 uppercase tracking-wider block">Jalur Kritis (Critical Path) Teridentifikasi:</span>
            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                @forelse($cpm['critical_tasks'] as $ct)
                    <span class="px-2.5 py-1 rounded-md bg-white text-rose-700 font-bold border border-rose-200 shadow-2xs">{{ $ct->name }}</span>
                    @if(!$loop->last)
                        <span class="text-rose-400 font-bold">&rarr;</span>
                    @endif
                @empty
                    <span class="text-slate-500 italic">Belum ada tugas atau ketergantungan tugas yang dihitung.</span>
                @endforelse
            </div>
        </div>
        <div class="text-right">
            <span class="text-slate-500">Total Durasi Acara:</span>
            <strong class="text-rose-700 text-sm ml-1">{{ $cpm['project_duration'] }} Hari Kerja</strong>
        </div>
    </div>

    <!-- Canvas SVG PERT Diagram -->
    <div class="bg-slate-900 rounded-2xl p-4 shadow-inner overflow-hidden relative cursor-grab active:cursor-grabbing h-[600px] border border-slate-800"
         @mousedown="startPan($event)"
         @mousemove="onPan($event)"
         @mouseup="endPan()"
         @mouseleave="endPan()">

        <div class="absolute top-4 left-4 z-10 bg-slate-800/80 backdrop-blur-xs text-white text-[11px] px-3 py-1.5 rounded-xl border border-slate-700 pointer-events-none">
            Gunakan drag/mouse untuk geser (pan), zoom in/out via tombol atas.
        </div>

        @if($tasks->isEmpty())
            <div class="h-full flex items-center justify-center text-slate-400 text-sm">
                Belum ada aktivitas untuk digambarkan pada diagram jaringan.
            </div>
        @else
            @php
                // Hitung posisi node secara dinamis berdasarkan Early Start (ES)
                $nodeWidth = 180;
                $nodeHeight = 90;
                $layerSpacingX = 260;
                $nodeSpacingY = 130;

                // Kelompokkan task per level Early Start (leveling)
                $levels = [];
                foreach ($tasks as $t) {
                    $lvl = $t->early_start ?? 0;
                    $levels[$lvl][] = $t;
                }
                ksort($levels);

                $positions = [];
                $xIndex = 0;
                $maxY = 0;

                foreach ($levels as $lvl => $levelTasks) {
                    $yIndex = 0;
                    foreach ($levelTasks as $t) {
                        $posX = 50 + ($xIndex * $layerSpacingX);
                        $posY = 50 + ($yIndex * $nodeSpacingY);
                        $positions[$t->id] = ['x' => $posX, 'y' => $posY];
                        if ($posY > $maxY) $maxY = $posY;
                        $yIndex++;
                    }
                    $xIndex++;
                }

                $svgWidth = max(1200, 100 + ($xIndex * $layerSpacingX));
                $svgHeight = max(600, $maxY + 200);
            @endphp

            <svg :style="`transform: translate(${panX}px, ${panY}px) scale(${zoom}); transform-origin: 0 0; transition: transform 0.05s ease-out;`"
                 width="{{ $svgWidth }}" height="{{ $svgHeight }}" class="select-none">

                <defs>
                    <!-- Marker Panah Normal -->
                    <marker id="arrow-normal" viewBox="0 0 10 10" refX="10" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#64748b"/>
                    </marker>
                    <!-- Marker Panah Kritis -->
                    <marker id="arrow-critical" viewBox="0 0 10 10" refX="10" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#f43f5e"/>
                    </marker>
                </defs>

                <!-- Garis Ketergantungan (Panah Predecessor -> Successor) -->
                @foreach($tasks as $t)
                    @foreach($t->predecessors as $pred)
                        @if(isset($positions[$pred->id]) && isset($positions[$t->id]))
                            @php
                                $fromX = $positions[$pred->id]['x'] + $nodeWidth;
                                $fromY = $positions[$pred->id]['y'] + ($nodeHeight / 2);
                                $toX = $positions[$t->id]['x'];
                                $toY = $positions[$t->id]['y'] + ($nodeHeight / 2);
                                $isEdgeCritical = $pred->is_critical && $t->is_critical;
                            @endphp

                            <path d="M {{ $fromX }} {{ $fromY }} C {{ $fromX + 40 }} {{ $fromY }}, {{ $toX - 40 }} {{ $toY }}, {{ $toX }} {{ $toY }}"
                                  fill="none"
                                  stroke="{{ $isEdgeCritical ? '#f43f5e' : '#64748b' }}"
                                  stroke-width="{{ $isEdgeCritical ? 3 : 1.5 }}"
                                  stroke-dasharray="{{ $isEdgeCritical ? 'none' : '4,2' }}"
                                  marker-end="url({{ $isEdgeCritical ? '#arrow-critical' : '#arrow-normal' }})"/>
                        @endif
                    @endforeach
                @endforeach

                <!-- Node Kotak Aktivitas (AON - Activity On Node) -->
                @foreach($tasks as $t)
                    @php
                        $pos = $positions[$t->id];
                        $isCritical = $t->is_critical;
                    @endphp

                    <g transform="translate({{ $pos['x'] }}, {{ $pos['y'] }})" class="cursor-pointer">
                        <!-- Kotak Card Node -->
                        <rect width="{{ $nodeWidth }}" height="{{ $nodeHeight }}" rx="12"
                              fill="{{ $isCritical ? '#1e1b4b' : '#1e293b' }}"
                              stroke="{{ $isCritical ? '#f43f5e' : '#334155' }}"
                              stroke-width="{{ $isCritical ? 2.5 : 1.5 }}"
                              filter="drop-shadow(0 4px 6px rgba(0,0,0,0.3))"/>

                        <!-- Baris Atas: ES | Durasi | EF -->
                        <rect x="0" y="0" width="{{ $nodeWidth }}" height="24" rx="12" fill="{{ $isCritical ? '#4c0519' : '#0f172a' }}" />
                        <line x1="60" y1="0" x2="60" y2="24" stroke="{{ $isCritical ? '#f43f5e' : '#334155' }}" stroke-width="1"/>
                        <line x1="120" y1="0" x2="120" y2="24" stroke="{{ $isCritical ? '#f43f5e' : '#334155' }}" stroke-width="1"/>

                        <text x="30" y="16" fill="#94a3b8" font-size="10" font-weight="bold" text-anchor="middle">ES: {{ $t->early_start }}</text>
                        <text x="90" y="16" fill="#f8fafc" font-size="10" font-weight="bold" text-anchor="middle">{{ $t->duration_days }}h</text>
                        <text x="150" y="16" fill="#94a3b8" font-size="10" font-weight="bold" text-anchor="middle">EF: {{ $t->early_finish }}</text>

                        <!-- Baris Tengah: Nama Aktivitas -->
                        <text x="{{ $nodeWidth / 2 }}" y="50" fill="{{ $isCritical ? '#fda4af' : '#e2e8f0' }}" font-size="11" font-weight="bold" text-anchor="middle">
                            {{ Str::limit($t->name, 20) }}
                        </text>

                        <!-- Baris Bawah: LS | Float | LF -->
                        <line x1="0" y1="66" x2="{{ $nodeWidth }}" y2="66" stroke="{{ $isCritical ? '#f43f5e' : '#334155' }}" stroke-width="1"/>
                        <line x1="60" y1="66" x2="60" y2="{{ $nodeHeight }}" stroke="{{ $isCritical ? '#f43f5e' : '#334155' }}" stroke-width="1"/>
                        <line x1="120" y1="66" x2="120" y2="{{ $nodeHeight }}" stroke="{{ $isCritical ? '#f43f5e' : '#334155' }}" stroke-width="1"/>

                        <text x="30" y="81" fill="#94a3b8" font-size="10" text-anchor="middle">LS: {{ $t->late_start }}</text>
                        <text x="90" y="81" fill="{{ $isCritical ? '#fb7185' : '#38bdf8' }}" font-size="10" font-weight="bold" text-anchor="middle">TF: {{ $t->total_float }}</text>
                        <text x="150" y="81" fill="#94a3b8" font-size="10" text-anchor="middle">LF: {{ $t->late_finish }}</text>
                    </g>
                @endforeach
            </svg>
        @endif
    </div>

    <!-- Legenda Simbol Node PERT -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded bg-rose-500"></span>
                <span class="font-semibold text-slate-700">Simpul Kritis (Total Float = 0)</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded bg-slate-700"></span>
                <span class="text-slate-600">Simpul Normal (Memiliki Kelonggaran Waktu)</span>
            </div>
        </div>
        <div class="text-slate-400">
            <strong>Keterangan node:</strong> [ES = Early Start | Durasi | EF = Early Finish] &bull; [LS = Late Start | TF = Total Float | LF = Late Finish]
        </div>
    </div>
</div>
@endsection
