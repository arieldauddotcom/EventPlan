<?php

namespace App\Services;

use App\Models\WorkCalendar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WorkingDaysCalculator
{
    protected array $workingDays;
    protected array $holidayDates = [];

    public function __construct(WorkCalendar $calendar)
    {
        // 1 = Monday, ..., 7 = Sunday
        $this->workingDays = $calendar->working_days ?? [1, 2, 3, 4, 5];

        // Format: 'Y-m-d'
        $this->holidayDates = $calendar->holidays()
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Cek apakah sebuah tanggal adalah hari kerja
     */
    public function isWorkingDay(Carbon $date): bool
    {
        $dayOfWeek = $date->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        if (!in_array($dayOfWeek, $this->workingDays)) {
            return false;
        }

        if (in_array($date->format('Y-m-d'), $this->holidayDates)) {
            return false;
        }

        return true;
    }

    /**
     * Dapatkan hari kerja berikutnya jika tanggal saat ini bukan hari kerja
     */
    public function getNextWorkingDay(Carbon $date): Carbon
    {
        $current = $date->copy();
        while (!$this->isWorkingDay($current)) {
            $current->addDay();
        }
        return $current;
    }

    /**
     * Hitung tanggal selesai dari tanggal mulai dan durasi (dalam hari kerja)
     * Contoh: durasi 1 hari mulai Senin -> selesai Senin.
     * Durasi 2 hari mulai Jumat (Sabtu-Minggu libur) -> selesai Senin.
     */
    public function calculateEndDate(Carbon $startDate, int $durationDays): Carbon
    {
        if ($durationDays < 1) {
            $durationDays = 1;
        }

        $current = $this->getNextWorkingDay($startDate);
        $counted = 1;

        while ($counted < $durationDays) {
            $current->addDay();
            if ($this->isWorkingDay($current)) {
                $counted++;
            }
        }

        return $current;
    }

    /**
     * Hitung jumlah hari kerja antara 2 tanggal (inklusif)
     */
    public function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        if ($startDate->gt($endDate)) {
            return 0;
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $count = 0;

        foreach ($period as $date) {
            if ($this->isWorkingDay($date)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Tambahkan n hari kerja dari suatu tanggal
     */
    public function addWorkingDays(Carbon $date, int $days): Carbon
    {
        $current = $date->copy();
        $added = 0;

        while ($added < $days) {
            $current->addDay();
            if ($this->isWorkingDay($current)) {
                $added++;
            }
        }

        return $current;
    }
}
