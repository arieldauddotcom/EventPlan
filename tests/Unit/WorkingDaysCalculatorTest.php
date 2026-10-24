<?php

namespace Tests\Unit;

use App\Models\Holiday;
use App\Models\WorkCalendar;
use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkingDaysCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_skips_weekends_in_end_date_calculation(): void
    {
        // Kalender Senin-Jumat (1,2,3,4,5)
        $calendar = WorkCalendar::create([
            'name' => 'Kalender Standar',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $calc = new WorkingDaysCalculator($calendar);

        // Jumat, 2026-10-23
        $startFriday = Carbon::parse('2026-10-23');

        // Durasi 1 hari kerja -> selesai Jumat itu juga
        $end1 = $calc->calculateEndDate($startFriday, 1);
        $this->assertEquals('2026-10-23', $end1->format('Y-m-d'));

        // Durasi 2 hari kerja -> mulai Jumat, lewati Sabtu & Minggu -> selesai Senin 2026-10-26
        $end2 = $calc->calculateEndDate($startFriday, 2);
        $this->assertEquals('2026-10-26', $end2->format('Y-m-d'));
    }

    public function test_skips_holidays_in_end_date_calculation(): void
    {
        $calendar = WorkCalendar::create([
            'name' => 'Kalender Nasional',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        // Libur pada hari Senin 2026-10-26
        Holiday::create([
            'work_calendar_id' => $calendar->id,
            'name' => 'Libur Nasional',
            'date' => '2026-10-26',
        ]);

        $calc = new WorkingDaysCalculator($calendar);

        // Mulai Jumat 2026-10-23, durasi 2 hari kerja
        // Jumat (hari 1), Sabtu-Minggu libur akhir pekan, Senin libur nasional, Selasa (hari 2) -> 2026-10-27
        $startFriday = Carbon::parse('2026-10-23');
        $end = $calc->calculateEndDate($startFriday, 2);

        $this->assertEquals('2026-10-27', $end->format('Y-m-d'));
    }

    public function test_calculates_working_days_between_two_dates(): void
    {
        $calendar = WorkCalendar::create([
            'name' => 'Kalender Standar',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $calc = new WorkingDaysCalculator($calendar);

        // Senin 2026-10-19 sampai Jumat 2026-10-23 = 5 hari kerja
        $days = $calc->calculateWorkingDays(
            Carbon::parse('2026-10-19'),
            Carbon::parse('2026-10-23')
        );

        $this->assertEquals(5, $days);

        // Termasuk akhir pekan: Senin 2026-10-19 sampai Minggu 2026-10-25 tetap 5 hari kerja
        $daysWithWeekend = $calc->calculateWorkingDays(
            Carbon::parse('2026-10-19'),
            Carbon::parse('2026-10-25')
        );

        $this->assertEquals(5, $daysWithWeekend);
    }
}
