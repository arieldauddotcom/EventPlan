<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use Carbon\Carbon;
use Exception;

class DependencyValidator
{
    /**
     * Memeriksa apakah menambahkan predecessorId -> successorId akan menimbulkan siklus (Cycle / Loop)
     * Menggunakan DFS (Depth First Search)
     */
    public function createsCycle(int $predecessorId, int $successorId): bool
    {
        if ($predecessorId === $successorId) {
            return true;
        }

        // Ambil semua dependencies di database
        $deps = TaskDependency::select('predecessor_id', 'successor_id')->get();

        // Buat graf adjacency list
        $adj = [];
        foreach ($deps as $d) {
            $adj[$d->predecessor_id][] = $d->successor_id;
        }

        // Tambahkan calon relasi baru
        $adj[$predecessorId][] = $successorId;

        // Cek apakah ada lintasan dari successorId kembali ke predecessorId
        $visited = [];
        $queue = [$successorId];

        while (!empty($queue)) {
            $curr = array_shift($queue);
            if ($curr === $predecessorId) {
                return true; // Siklus terdeteksi!
            }

            if (!isset($visited[$curr])) {
                $visited[$curr] = true;
                if (!empty($adj[$curr])) {
                    foreach ($adj[$curr] as $next) {
                        if (!isset($visited[$next])) {
                            $queue[] = $next;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Hitung tanggal mulai minimum suatu tugas berdasarkan semua predecessor-nya
     */
    public function calculateEarliestStart(Task $task, WorkingDaysCalculator $calc): Carbon
    {
        $task->loadMissing('predecessors');
        $minStart = Carbon::parse($task->start_date);

        foreach ($task->predecessors as $pred) {
            $depType = $pred->pivot->type ?? 'FS';
            $lagDays = (int) ($pred->pivot->lag_days ?? 0);

            if ($depType === 'FS') {
                // Selesai predecessor + 1 hari kerja (atau + lag)
                $predEnd = Carbon::parse($pred->end_date);
                // Tambah 1 hari kerja setelah selesai
                $candidateStart = $calc->addWorkingDays($predEnd, 1 + $lagDays);
            } else { // 'SS'
                $predStart = Carbon::parse($pred->start_date);
                $candidateStart = $calc->addWorkingDays($predStart, $lagDays);
            }

            if ($candidateStart->gt($minStart)) {
                $minStart = $candidateStart;
            }
        }

        return $minStart;
    }
}
