<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\CriticalPathCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Sanitasi sel teks CSV untuk mencegah CSV Formula Injection (Formula injection mitigation)
     */
    protected function sanitizeCell(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $firstChar = substr($value, 0, 1);
        // Bila diawali simbol formula spreadsheet (=, +, -, @, tab, cr), tambahkan apostrof '
        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"])) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Ekspor daftar tugas, estimasi CPM, dan jadwal kerja ke CSV
     */
    public function exportTasksCsv(Project $project, CriticalPathCalculator $cpmCalculator): StreamedResponse
    {
        $this->authorize('view', $project);

        // Pastikan nilai CPM terbaru terkalkulasi
        $cpmCalculator->calculate($project);

        $tasks = $project->tasks()
            ->with(['predecessors', 'assignments.resource'])
            ->orderBy('start_date')
            ->get();

        $filename = 'tugas_proyek_' . str()->slug($project->name) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'ID Tugas',
            'Nama Tugas',
            'Status',
            'Prioritas',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi (Hari Kerja)',
            'Progres (%)',
            'Early Start (ES)',
            'Early Finish (EF)',
            'Late Start (LS)',
            'Late Finish (LF)',
            'Total Float (Hari)',
            'Jalur Kritis (CPM)',
            'Predecessors',
            'Penanggung Jawab / Tim',
        ];

        return response()->stream(function () use ($tasks, $columns) {
            $handle = fopen('php://output', 'w');

            // Tulis UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Tulis Header Kolom
            fputcsv($handle, $columns);

            foreach ($tasks as $task) {
                $predecessors = $task->predecessors->pluck('name')->implode(', ');
                $resources = $task->assignments->map(function ($asg) {
                    return ($asg->resource->name ?? '-') . ' (' . $asg->allocation_percentage . '%)';
                })->implode(', ');

                $row = [
                    $task->id,
                    $this->sanitizeCell($task->name),
                    ucfirst($task->status),
                    ucfirst($task->priority),
                    $task->start_date ? $task->start_date->format('Y-m-d') : '',
                    $task->end_date ? $task->end_date->format('Y-m-d') : '',
                    $task->duration_days,
                    $task->progress_percentage . '%',
                    $task->early_start ?? 0,
                    $task->early_finish ?? 0,
                    $task->late_start ?? 0,
                    $task->late_finish ?? 0,
                    $task->total_float ?? 0,
                    $task->is_critical ? 'YA (Kritis)' : 'TIDAK',
                    $this->sanitizeCell($predecessors),
                    $this->sanitizeCell($resources),
                ];

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Ekspor ringkasan proyek dan visualisasi linimasa Gantt Chart ke format PDF (Download atau Preview Stream)
     */
    public function exportPdf(Request $request, Project $project, CriticalPathCalculator $cpmCalculator)
    {
        $this->authorize('view', $project);

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $cpm = $cpmCalculator->calculate($project);

        $tasksQuery = $project->tasks()
            ->with(['predecessors', 'assignments.resource'])
            ->orderBy('start_date');

        $filterStart = $request->filled('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : null;
        $filterEnd = $request->filled('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;

        // Filter tugas berdasarkan rentang tanggal jika disediakan
        if ($filterStart) {
            $tasksQuery->where('end_date', '>=', $filterStart->format('Y-m-d'));
        }
        if ($filterEnd) {
            $tasksQuery->where('start_date', '<=', $filterEnd->format('Y-m-d'));
        }

        $tasks = $tasksQuery->get();

        $minDate = $filterStart ?: $project->start_date->copy();
        $maxDate = $filterEnd ?: $project->deadline->copy();

        if (!$filterStart || !$filterEnd) {
            foreach ($tasks as $t) {
                if (!$filterStart && $t->start_date && $t->start_date->lt($minDate)) {
                    $minDate = $t->start_date->copy();
                }
                if (!$filterEnd && $t->end_date && $t->end_date->gt($maxDate)) {
                    $maxDate = $t->end_date->copy();
                }
            }
        }

        $period = CarbonPeriod::create($minDate, $maxDate);
        $totalDays = max(1, $period->count());

        $members = $project->members()->with('user')->get();
        $resources = $project->resources()->get();

        $pdf = Pdf::loadView('projects.exports.pdf', compact(
            'project',
            'tasks',
            'cpm',
            'minDate',
            'maxDate',
            'period',
            'totalDays',
            'members',
            'resources',
            'filterStart',
            'filterEnd'
        ))->setPaper('a4', 'landscape');

        $filename = 'laporan_proyek_' . str()->slug($project->name) . '_' . now()->format('Ymd_His') . '.pdf';

        if ($request->query('preview') === '1' || $request->query('stream') === '1') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
