<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Proyek - {{ $project->name }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .brand {
            font-size: 14pt;
            font-weight: bold;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        .meta-table {
            width: 100%;
            margin-top: 4px;
            border-collapse: collapse;
        }
        .meta-table td {
            font-size: 8.5pt;
            padding: 2px 0;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            width: 100px;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
        }
        .summary-grid {
            width: 100%;
            margin: 12px 0;
            border-collapse: collapse;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            width: 25%;
            vertical-align: top;
        }
        .summary-title {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-critical {
            background-color: #ffe4e6;
            color: #e11d48;
            border: 1px solid #fecdd3;
        }
        .badge-normal {
            background-color: #e0e7ff;
            color: #4338ca;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }
        /* Linimasa Gantt Chart */
        .gantt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            table-layout: fixed;
        }
        .gantt-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 2px 1px;
            text-align: center;
            font-weight: bold;
            color: #475569;
        }
        .gantt-table th.task-col {
            text-align: left;
            padding-left: 5px;
            width: 180px;
            background-color: #f1f5f9;
        }
        .gantt-table td {
            border: 1px solid #e2e8f0;
            padding: 2px 0;
            height: 18px;
            position: relative;
        }
        .gantt-table td.task-name-cell {
            padding: 2px 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
            border-right: 2px solid #cbd5e1;
        }
        .weekend-cell {
            background-color: #f8fafc;
        }
        .bar-container {
            width: 100%;
            height: 12px;
            position: relative;
        }
        .bar-span {
            position: absolute;
            top: 1px;
            height: 10px;
            border-radius: 2px;
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 6.5pt;
            line-height: 10px;
            text-align: center;
            overflow: hidden;
            font-weight: bold;
        }
        .bar-span.critical {
            background-color: #e11d48;
        }
        .legend {
            margin-top: 8px;
            font-size: 7.5pt;
            color: #475569;
        }
        .legend-item {
            display: inline-block;
            margin-right: 14px;
        }
        .legend-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            vertical-align: middle;
            margin-right: 3px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top;">
                    <div class="brand">EventPlan &bull; Project Management Report</div>
                    <div class="title">{{ $project->name }}</div>
                    @if($project->organization)
                        <div style="font-size: 8.5pt; color: #475569; font-weight: 500;">Organisasi / Instansi: {{ $project->organization }}</div>
                    @endif
                </td>
                <td style="text-align: right; vertical-align: top; width: 220px;">
                    <div style="font-size: 8pt; color: #64748b;">Tanggal Cetak: <strong>{{ now()->translatedFormat('d F Y H:i') }}</strong></div>
                    <div style="font-size: 8pt; color: #64748b;">Pemilik / Leader: <strong>{{ $project->owner->name ?? '-' }}</strong></div>
                    <div style="font-size: 8pt; color: #64748b;">Status Proyek: <strong style="text-transform: capitalize;">{{ $project->status }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Proyek & CPM Metrics -->
    <table class="summary-grid">
        <tr>
            <td class="summary-card">
                <div class="summary-title">Periode Proyek</div>
                <div class="summary-value" style="font-size: 9.5pt;">
                    {{ $project->start_date->format('d/m/Y') }} - {{ $project->deadline->format('d/m/Y') }}
                </div>
                @if(isset($filterStart) && $filterStart || isset($filterEnd) && $filterEnd)
                    <div style="font-size: 7pt; color: #4f46e5; margin-top: 2px; font-weight: bold;">
                        Filter: {{ $filterStart ? $filterStart->format('d/m/Y') : 'Awal' }} s/d {{ $filterEnd ? $filterEnd->format('d/m/Y') : 'Akhir' }}
                    </div>
                @endif
            </td>
            <td style="width: 1%;"></td>
            <td class="summary-card">
                <div class="summary-title">Estimasi Durasi (CPM)</div>
                <div class="summary-value" style="color: #4f46e5;">
                    {{ $cpm['project_duration'] ?? 0 }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">Hari Kerja</span>
                </div>
            </td>
            <td style="width: 1%;"></td>
            <td class="summary-card">
                <div class="summary-title">Tugas di Jalur Kritis</div>
                <div class="summary-value" style="color: #e11d48;">
                    {{ $cpm['critical_tasks'] ? $cpm['critical_tasks']->count() : 0 }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">dari {{ $tasks->count() }} Tugas</span>
                </div>
            </td>
            <td style="width: 1%;"></td>
            <td class="summary-card">
                <div class="summary-title">Total Anggota & Tim</div>
                <div class="summary-value" style="color: #0d9488;">
                    {{ $members->count() }} Anggota <span style="font-size: 8pt; font-weight: normal; color: #64748b;">({{ $resources->count() }} Resource)</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Ringkasan Deskripsi & Kalender -->
    @if($project->description)
        <div style="margin-bottom: 10px; font-size: 8pt; background-color: #f8fafc; border-left: 3px solid #4f46e5; padding: 6px 8px;">
            <strong>Deskripsi Proyek:</strong> {{ $project->description }}
        </div>
    @endif

    <!-- Linimasa Gantt Chart -->
    <div class="section-title">Visualisasi Linimasa (Gantt Chart Timeline)</div>

    @if($tasks->isEmpty())
        <div style="padding: 15px; text-align: center; color: #94a3b8; font-size: 8.5pt;">
            Belum ada tugas yang terdaftar dalam proyek ini.
        </div>
    @else
        @php
            // Batasi tampilan kolom timeline jika rentang hari sangat panjang agar PDF tetap rapi
            $maxRenderDays = min($totalDays, 45);
            $displayedPeriod = array_slice($period->toArray(), 0, $maxRenderDays);
        @endphp

        <table class="gantt-table">
            <thead>
                <tr>
                    <th class="task-col">Nama Tugas</th>
                    <th style="width: 45px;">Mulai</th>
                    <th style="width: 45px;">Selesai</th>
                    <th style="width: 32px;">Durasi</th>
                    <th style="width: 32px;">Float</th>
                    @foreach($displayedPeriod as $date)
                        @php
                            $isWeekend = in_array($date->dayOfWeekIso, [6, 7]);
                        @endphp
                        <th style="width: {{ round(100 / ($maxRenderDays + 15), 1) }}%; {{ $isWeekend ? 'background-color: #f1f5f9; color: #94a3b8;' : '' }}">
                            {{ $date->format('d') }}<br><span style="font-size: 5.5pt;">{{ substr($date->locale('id')->isoFormat('ddd'), 0, 2) }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $t)
                    @php
                        $isCritical = (bool) $t->is_critical;
                        $tStartIndex = max(0, $minDate->diffInDays($t->start_date));
                        $tEndIndex = min($maxRenderDays - 1, $minDate->diffInDays($t->end_date));
                    @endphp
                    <tr>
                        <td class="task-name-cell" title="{{ $t->name }}">
                            @if($isCritical)
                                <strong style="color: #e11d48;">[!]</strong>
                            @endif
                            {{ $t->name }} ({{ $t->progress_percentage }}%)
                        </td>
                        <td style="text-align: center; font-size: 6.5pt;">{{ $t->start_date ? $t->start_date->format('d/m') : '-' }}</td>
                        <td style="text-align: center; font-size: 6.5pt;">{{ $t->end_date ? $t->end_date->format('d/m') : '-' }}</td>
                        <td style="text-align: center; font-size: 6.5pt;">{{ $t->duration_days }}h</td>
                        <td style="text-align: center; font-size: 6.5pt; font-weight: bold; {{ $isCritical ? 'color: #e11d48;' : '' }}">
                            {{ $t->total_float ?? 0 }}h
                        </td>

                        @foreach($displayedPeriod as $idx => $date)
                            @php
                                $isWeekend = in_array($date->dayOfWeekIso, [6, 7]);
                                $isInTask = ($idx >= $tStartIndex && $idx <= $tEndIndex);
                            @endphp
                            <td class="{{ $isWeekend ? 'weekend-cell' : '' }}" style="text-align: center; padding: 0; vertical-align: middle;">
                                @if($isInTask)
                                    <div style="height: 10px; width: 100%; background-color: {{ $isCritical ? '#e11d48' : '#4f46e5' }};"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($totalDays > $maxRenderDays)
            <div style="font-size: 7pt; color: #64748b; margin-top: 4px; font-style: italic;">
                * Linimasa di atas menampilkan 45 hari pertama dari total {{ $totalDays }} hari rentang proyek.
            </div>
        @endif

        <div class="legend">
            <span class="legend-item"><span class="legend-box" style="background-color: #4f46e5;"></span> Tugas Normal</span>
            <span class="legend-item"><span class="legend-box" style="background-color: #e11d48;"></span> Jalur Kritis (Critical Path / Float 0)</span>
            <span class="legend-item"><span class="legend-box" style="background-color: #f1f5f9; border: 1px solid #cbd5e1;"></span> Akhir Pekan (Libur)</span>
        </div>
    @endif

    <!-- Page Break untuk Tabel Detail & Kolaborator -->
    <div class="page-break"></div>

    <!-- Header Halaman 2 -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top;">
                    <div class="brand">EventPlan &bull; Rincian Tugas & Anggota Kolaborasi</div>
                    <div class="title" style="font-size: 13pt;">{{ $project->name }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 8pt; color: #64748b;">Halaman 2: Rincian Lengkap CPM & Tim</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Rincian Tugas & Analisis CPM -->
    <div class="section-title">Rincian Tugas & Parameter Critical Path Method (CPM)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Nama Tugas</th>
                <th style="width: 60px;">Status</th>
                <th style="width: 50px;">Prioritas</th>
                <th style="width: 60px;">Mulai</th>
                <th style="width: 60px;">Selesai</th>
                <th style="width: 45px;">Durasi</th>
                <th style="width: 40px;">Progres</th>
                <th style="width: 35px;">ES</th>
                <th style="width: 35px;">EF</th>
                <th style="width: 35px;">LS</th>
                <th style="width: 35px;">LF</th>
                <th style="width: 40px;">Float</th>
                <th style="width: 65px;">Kritis?</th>
                <th>Penanggung Jawab</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $index => $t)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $t->name }}</strong>
                        @if($t->predecessors->isNotEmpty())
                            <br><span style="font-size: 6.5pt; color: #64748b;">Pre: {{ $t->predecessors->pluck('name')->implode(', ') }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $t->status === 'completed' ? 'badge-success' : 'badge-normal' }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td style="text-transform: capitalize;">{{ $t->priority }}</td>
                    <td>{{ $t->start_date ? $t->start_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $t->end_date ? $t->end_date->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">{{ $t->duration_days }}h</td>
                    <td style="text-align: center; font-weight: bold;">{{ $t->progress_percentage }}%</td>
                    <td style="text-align: center;">{{ $t->early_start ?? 0 }}</td>
                    <td style="text-align: center;">{{ $t->early_finish ?? 0 }}</td>
                    <td style="text-align: center;">{{ $t->late_start ?? 0 }}</td>
                    <td style="text-align: center;">{{ $t->late_finish ?? 0 }}</td>
                    <td style="text-align: center; font-weight: bold; {{ $t->is_critical ? 'color: #e11d48;' : '' }}">
                        {{ $t->total_float ?? 0 }}h
                    </td>
                    <td style="text-align: center;">
                        @if($t->is_critical)
                            <span class="badge badge-critical">YA (Kritis)</span>
                        @else
                            <span style="color: #64748b; font-size: 7pt;">Tidak</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $assignedNames = $t->assignments->map(fn($asg) => ($asg->resource->name ?? '-') . ' (' . $asg->allocation_percentage . '%)')->implode(', ');
                        @endphp
                        {{ $assignedNames ?: '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" style="text-align: center; color: #94a3b8; padding: 12px;">Tidak ada tugas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Susunan Tim & Anggota Kolaborasi -->
    <table style="width: 100%; margin-top: 10px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-title">Daftar Anggota Proyek (Kolaborasi)</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th style="width: 70px;">Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $m)
                            <tr>
                                <td><strong>{{ $m->user->name ?? '-' }}</strong></td>
                                <td>{{ $m->user->email ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $m->role === 'leader' ? 'badge-critical' : 'badge-normal' }}">
                                        {{ ucfirst($m->role) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8;">Belum ada anggota tim kolaborasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-title">Sumber Daya & Kapasitas (Resources)</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Sumber Daya</th>
                            <th>Role / Jabatan</th>
                            <th style="width: 60px;">Kapasitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resources as $r)
                            <tr>
                                <td><strong>{{ $r->name }}</strong></td>
                                <td>{{ $r->role ?: '-' }}</td>
                                <td style="text-align: center;">{{ $r->capacity_hours_per_day }} jam/hari</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8;">Belum ada sumber daya terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        EventPlan &bull; Sistem Manajemen Proyek & Analisis Linimasa Kritis &bull; Digenerate secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
