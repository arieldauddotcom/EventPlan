<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class WorkCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'working_days',
        'hours_per_day',
    ];

    protected $casts = [
        'working_days' => 'array',
        'hours_per_day' => 'decimal:2',
    ];

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Cek apakah tanggal tertentu adalah hari kerja
     */
    public function isWorkingDay(Carbon $date): bool
    {
        $dayOfWeek = $date->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
        $workingDays = $this->working_days ?? [1, 2, 3, 4, 5];

        if (!in_array($dayOfWeek, $workingDays)) {
            return false;
        }

        // Cek hari libur
        $isHoliday = $this->holidays()->whereDate('date', $date->toDateString())->exists();
        return !$isHoliday;
    }

    /**
     * Hitung tanggal selesai dari tanggal mulai dan durasi (hari kerja)
     */
    public function calculateEndDate(Carbon $startDate, int $durationDays): Carbon
    {
        if ($durationDays <= 1) {
            $current = $startDate->copy();
            while (!$this->isWorkingDay($current)) {
                $current->addDay();
            }
            return $current;
        }

        $current = $startDate->copy();
        while (!$this->isWorkingDay($current)) {
            $current->addDay();
        }

        $daysCounted = 1;
        while ($daysCounted < $durationDays) {
            $current->addDay();
            if ($this->isWorkingDay($current)) {
                $daysCounted++;
            }
        }

        return $current;
    }
}
