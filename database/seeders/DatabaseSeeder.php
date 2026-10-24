<?php

namespace Database\Seeders;

use App\Models\Holiday;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectMember;
use App\Models\Resource;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskDependency;
use App\Models\User;
use App\Models\WorkCalendar;
use App\Services\CriticalPathCalculator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed sampel data komprehensif: pengguna, proyek ragam skala, kalender kerja, tugas bertingkat, ketergantungan (dependencies), jalur kritis (CPM), dan kolaborasi anggota tim.
     */
    public function run(): void
    {
        $cpmCalculator = app(CriticalPathCalculator::class);

        // -------------------------------------------------------------
        // 1. Akun Pengguna Global (Role: admin & user)
        // -------------------------------------------------------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@eventplan.local'],
            [
                'name' => 'Administrator EventPlan',
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $ariel = User::updateOrCreate(
            ['email' => 'kontak@arieldaud.com'],
            [
                'name' => 'Mohamad Nazril R. Daud',
                'username' => 'planner',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $sarah = User::updateOrCreate(
            ['email' => 'sarah@gemilang.com'],
            [
                'name' => 'Sarah Anindya',
                'username' => 'sarah',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $budi = User::updateOrCreate(
            ['email' => 'budi@gemilang.com'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budisantoso',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $doni = User::updateOrCreate(
            ['email' => 'doni@gemilang.com'],
            [
                'name' => 'Doni Kusuma',
                'username' => 'donikusuma',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $maya = User::updateOrCreate(
            ['email' => 'maya@gemilang.com'],
            [
                'name' => 'Maya Putri',
                'username' => 'mayaputri',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // -------------------------------------------------------------
        // 2. Kalender Kerja Acara & Hari Libur Nasional
        // -------------------------------------------------------------
        $calendarStandard = WorkCalendar::firstOrCreate(
            ['name' => 'Kalender Standar Event (Senin - Jumat)'],
            [
                'working_days' => [1, 2, 3, 4, 5],
                'hours_per_day' => 8.00,
            ]
        );

        $calendarIntensive = WorkCalendar::firstOrCreate(
            ['name' => 'Kalender Produksi Cepat (Senin - Sabtu)'],
            [
                'working_days' => [1, 2, 3, 4, 5, 6],
                'hours_per_day' => 9.00,
            ]
        );

        Holiday::firstOrCreate(
            ['work_calendar_id' => $calendarStandard->id, 'date' => '2026-11-10'],
            ['name' => 'Hari Pahlawan Nasional']
        );
        Holiday::firstOrCreate(
            ['work_calendar_id' => $calendarStandard->id, 'date' => '2026-12-25'],
            ['name' => 'Hari Raya Natal']
        );

        // -------------------------------------------------------------
        // 3. PROYEK 1: Jakarta Music & Art Festival 2026
        // Leader: Mohamad Nazril R. Daud | Member: Sarah, Doni | Undangan Pending: Budi
        // Karakter: Kompleks multi-path, jalur kritis artis & panggung
        // -------------------------------------------------------------
        $p1 = Project::updateOrCreate(
            ['name' => 'Jakarta Music & Art Festival 2026'],
            [
                'owner_id' => $ariel->id,
                'work_calendar_id' => $calendarStandard->id,
                'organization' => 'Kreasi Gemilang Nusantara',
                'description' => 'Festival musik akbar dan pameran instalasi seni nusantara dengan estimasi 15.000 penonton di Stadion Madya Senayan.',
                'start_date' => '2026-11-02',
                'deadline' => '2026-11-30',
                'status' => 'active',
            ]
        );

        ProjectMember::updateOrCreate(['project_id' => $p1->id, 'user_id' => $ariel->id], ['role' => 'leader']);
        ProjectMember::updateOrCreate(['project_id' => $p1->id, 'user_id' => $sarah->id], ['role' => 'member']);
        ProjectMember::updateOrCreate(['project_id' => $p1->id, 'user_id' => $doni->id], ['role' => 'member']);

        ProjectInvitation::updateOrCreate(
            ['project_id' => $p1->id, 'email' => 'budi@gemilang.com'],
            [
                'inviter_id' => $ariel->id,
                'invitee_id' => $budi->id,
                'status' => 'pending',
                'message' => 'Halo Budi, mohon bergabung sebagai koordinator panggung dan logistik utama!',
            ]
        );

        // Resources P1
        $r1Budi = Resource::updateOrCreate(
            ['project_id' => $p1->id, 'email' => 'budi@gemilang.com'],
            ['name' => 'Budi Santoso', 'role' => 'Project Director', 'cost_per_hour' => 150000, 'max_hours_per_day' => 8]
        );
        $r1Sarah = Resource::updateOrCreate(
            ['project_id' => $p1->id, 'email' => 'sarah@gemilang.com'],
            ['name' => 'Sarah Anindya', 'role' => 'Koordinator Venue & Legal', 'cost_per_hour' => 100000, 'max_hours_per_day' => 8]
        );
        $r1Doni = Resource::updateOrCreate(
            ['project_id' => $p1->id, 'email' => 'doni@gemilang.com'],
            ['name' => 'Doni Kusuma', 'role' => 'Lead Audio & Lighting Engineer', 'cost_per_hour' => 125000, 'max_hours_per_day' => 8]
        );
        $r1Maya = Resource::updateOrCreate(
            ['project_id' => $p1->id, 'email' => 'maya@gemilang.com'],
            ['name' => 'Maya Putri', 'role' => 'Head of Brand & Ticket Sales', 'cost_per_hour' => 95000, 'max_hours_per_day' => 8]
        );

        $r1Doni->leaves()->firstOrCreate(
            ['start_date' => '2026-11-12', 'end_date' => '2026-11-12'],
            ['reason' => 'Sertifikasi Kalibrasi Audio']
        );

        // Tugas P1
        $t1_1 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Perizinan Tempat & Keamanan Polda'],
            ['start_date' => '2026-11-02', 'end_date' => '2026-11-05', 'duration_days' => 4, 'priority' => 'urgent', 'status' => 'completed', 'progress_percentage' => 100, 'sort_order' => 1]
        );
        $t1_2 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Penandatanganan Kontrak Artis Headliner'],
            ['start_date' => '2026-11-06', 'end_date' => '2026-11-11', 'duration_days' => 3, 'priority' => 'urgent', 'status' => 'in_progress', 'progress_percentage' => 75, 'sort_order' => 2]
        );
        $t1_3 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Peluncuran Website & Tiket Early Bird'],
            ['start_date' => '2026-11-06', 'end_date' => '2026-11-09', 'duration_days' => 2, 'priority' => 'high', 'status' => 'completed', 'progress_percentage' => 100, 'sort_order' => 3]
        );
        $t1_4 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Instalasi Panggung Rigging & Sound System'],
            ['start_date' => '2026-11-12', 'end_date' => '2026-11-17', 'duration_days' => 4, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 4]
        );
        $t1_5 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Setup Booth Tenant F&B dan Merchandise'],
            ['start_date' => '2026-11-12', 'end_date' => '2026-11-13', 'duration_days' => 2, 'priority' => 'medium', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 5]
        );
        $t1_6 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Gladi Bersih & Technical Rehearsal'],
            ['start_date' => '2026-11-18', 'end_date' => '2026-11-19', 'duration_days' => 2, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 6]
        );
        $t1_7 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Showday: Pelaksanaan Acara Festival'],
            ['start_date' => '2026-11-20', 'end_date' => '2026-11-20', 'duration_days' => 1, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 7]
        );
        $t1_8 = Task::updateOrCreate(
            ['project_id' => $p1->id, 'name' => 'Bongkar Muat Rigging & Laporan Evaluasi'],
            ['start_date' => '2026-11-23', 'end_date' => '2026-11-24', 'duration_days' => 2, 'priority' => 'low', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 8]
        );

        // Dependencies P1
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_1->id, 'successor_id' => $t1_2->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_1->id, 'successor_id' => $t1_3->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_2->id, 'successor_id' => $t1_4->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_1->id, 'successor_id' => $t1_5->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_4->id, 'successor_id' => $t1_6->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_5->id, 'successor_id' => $t1_6->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_6->id, 'successor_id' => $t1_7->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t1_7->id, 'successor_id' => $t1_8->id], ['type' => 'FS']);

        // Assignments P1
        TaskAssignment::firstOrCreate(['task_id' => $t1_1->id, 'resource_id' => $r1Sarah->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_2->id, 'resource_id' => $r1Budi->id], ['allocation_percentage' => 70]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_3->id, 'resource_id' => $r1Maya->id], ['allocation_percentage' => 90]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_4->id, 'resource_id' => $r1Doni->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_5->id, 'resource_id' => $r1Sarah->id], ['allocation_percentage' => 60]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_6->id, 'resource_id' => $r1Doni->id], ['allocation_percentage' => 80]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_6->id, 'resource_id' => $r1Budi->id], ['allocation_percentage' => 50]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_7->id, 'resource_id' => $r1Budi->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_7->id, 'resource_id' => $r1Doni->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_7->id, 'resource_id' => $r1Sarah->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t1_8->id, 'resource_id' => $r1Doni->id], ['allocation_percentage' => 80]);

        $cpmCalculator->calculate($p1);

        // -------------------------------------------------------------
        // 4. PROYEK 2: Indonesia Tech Summit & Exhibition 2026
        // Leader: Budi Santoso | Member: Mohamad Nazril R. Daud, Maya Putri
        // Karakter: Konferensi teknologi hybrid, paralel webinar & expo booth
        // -------------------------------------------------------------
        $p2 = Project::updateOrCreate(
            ['name' => 'Indonesia Tech Summit & Exhibition 2026'],
            [
                'owner_id' => $budi->id,
                'work_calendar_id' => $calendarStandard->id,
                'organization' => 'Asosiasi Startup & AI Nasional',
                'description' => 'Konferensi teknologi bergengsi menghadirkan 50 pembicara global, live demo AI, dan 100 stan startup.',
                'start_date' => '2026-12-01',
                'deadline' => '2026-12-24',
                'status' => 'active',
            ]
        );

        ProjectMember::updateOrCreate(['project_id' => $p2->id, 'user_id' => $budi->id], ['role' => 'leader']);
        ProjectMember::updateOrCreate(['project_id' => $p2->id, 'user_id' => $ariel->id], ['role' => 'member']);
        ProjectMember::updateOrCreate(['project_id' => $p2->id, 'user_id' => $maya->id], ['role' => 'member']);

        $r2Ariel = Resource::updateOrCreate(
            ['project_id' => $p2->id, 'email' => 'kontak@arieldaud.com'],
            ['name' => 'Mohamad Nazril R. Daud', 'role' => 'Curator of Content & Keynote Speakers', 'cost_per_hour' => 140000, 'max_hours_per_day' => 8]
        );
        $r2Maya = Resource::updateOrCreate(
            ['project_id' => $p2->id, 'email' => 'maya@gemilang.com'],
            ['name' => 'Maya Putri', 'role' => 'Sponsorship & Exhibitor Manager', 'cost_per_hour' => 110000, 'max_hours_per_day' => 8]
        );
        $r2Budi = Resource::updateOrCreate(
            ['project_id' => $p2->id, 'email' => 'budi@gemilang.com'],
            ['name' => 'Budi Santoso', 'role' => 'Event Director & Live Stream Producer', 'cost_per_hour' => 160000, 'max_hours_per_day' => 8]
        );

        $t2_1 = Task::updateOrCreate(
            ['project_id' => $p2->id, 'name' => 'Kurasi Pembicara Utama & Konfirmasi Jadwal'],
            ['start_date' => '2026-12-01', 'end_date' => '2026-12-04', 'duration_days' => 4, 'priority' => 'urgent', 'status' => 'in_progress', 'progress_percentage' => 80, 'sort_order' => 1]
        );
        $t2_2 = Task::updateOrCreate(
            ['project_id' => $p2->id, 'name' => 'Akuisisi Sponsor & Booth Pameran'],
            ['start_date' => '2026-12-01', 'end_date' => '2026-12-08', 'duration_days' => 6, 'priority' => 'high', 'status' => 'in_progress', 'progress_percentage' => 50, 'sort_order' => 2]
        );
        $t2_3 = Task::updateOrCreate(
            ['project_id' => $p2->id, 'name' => 'Pembangunan Platform Streaming & Registrasi'],
            ['start_date' => '2026-12-07', 'end_date' => '2026-12-11', 'duration_days' => 5, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 3]
        );
        $t2_4 = Task::updateOrCreate(
            ['project_id' => $p2->id, 'name' => 'Distribusi Materi Slide & Briefing Moderator'],
            ['start_date' => '2026-12-14', 'end_date' => '2026-12-16', 'duration_days' => 3, 'priority' => 'medium', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 4]
        );
        $t2_5 = Task::updateOrCreate(
            ['project_id' => $p2->id, 'name' => 'Dry Run Live Broadcasting & Audio Multi-Room'],
            ['start_date' => '2026-12-17', 'end_date' => '2026-12-18', 'duration_days' => 2, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 5]
        );
        $t2_6 = Task::updateOrCreate(
            ['project_id' => $p2->id, 'name' => 'Hari Pelaksanaan Summit (Day 1 - 2)'],
            ['start_date' => '2026-12-21', 'end_date' => '2026-12-22', 'duration_days' => 2, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 6]
        );

        TaskDependency::firstOrCreate(['predecessor_id' => $t2_1->id, 'successor_id' => $t2_4->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t2_1->id, 'successor_id' => $t2_3->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t2_3->id, 'successor_id' => $t2_5->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t2_4->id, 'successor_id' => $t2_5->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t2_5->id, 'successor_id' => $t2_6->id], ['type' => 'FS']);

        TaskAssignment::firstOrCreate(['task_id' => $t2_1->id, 'resource_id' => $r2Ariel->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_2->id, 'resource_id' => $r2Maya->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_3->id, 'resource_id' => $r2Budi->id], ['allocation_percentage' => 80]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_4->id, 'resource_id' => $r2Ariel->id], ['allocation_percentage' => 70]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_5->id, 'resource_id' => $r2Budi->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_6->id, 'resource_id' => $r2Ariel->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_6->id, 'resource_id' => $r2Budi->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t2_6->id, 'resource_id' => $r2Maya->id], ['allocation_percentage' => 100]);

        $cpmCalculator->calculate($p2);

        // -------------------------------------------------------------
        // 5. PROYEK 3: Pameran Otomotif & Elektrifikasi Ramah Lingkungan
        // Leader: Mohamad Nazril R. Daud | Member: Sarah Anindya
        // Karakter: Proyek skala menengah, status planning
        // -------------------------------------------------------------
        $p3 = Project::updateOrCreate(
            ['name' => 'Pameran Otomotif & Elektrifikasi Ramah Lingkungan'],
            [
                'owner_id' => $ariel->id,
                'work_calendar_id' => $calendarIntensive->id,
                'organization' => 'EcoMobility Federation',
                'description' => 'Eksibisi kendaraan listrik dan konversi energi terbarukan di ICE BSD Hall 1-3.',
                'start_date' => '2027-01-05',
                'deadline' => '2027-01-25',
                'status' => 'planning',
            ]
        );

        ProjectMember::updateOrCreate(['project_id' => $p3->id, 'user_id' => $ariel->id], ['role' => 'leader']);
        ProjectMember::updateOrCreate(['project_id' => $p3->id, 'user_id' => $sarah->id], ['role' => 'member']);

        $r3Sarah = Resource::updateOrCreate(
            ['project_id' => $p3->id, 'email' => 'sarah@gemilang.com'],
            ['name' => 'Sarah Anindya', 'role' => 'Head of Venue & Safety Regulations', 'cost_per_hour' => 110000, 'max_hours_per_day' => 9]
        );
        $r3Ariel = Resource::updateOrCreate(
            ['project_id' => $p3->id, 'email' => 'kontak@arieldaud.com'],
            ['name' => 'Mohamad Nazril R. Daud', 'role' => 'Program Manager', 'cost_per_hour' => 150000, 'max_hours_per_day' => 9]
        );

        $t3_1 = Task::updateOrCreate(
            ['project_id' => $p3->id, 'name' => 'Sewa Hall ICE BSD & Dokumen Sertifikasi Baterai'],
            ['start_date' => '2027-01-05', 'end_date' => '2027-01-09', 'duration_days' => 5, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 1]
        );
        $t3_2 = Task::updateOrCreate(
            ['project_id' => $p3->id, 'name' => 'Desain Floorplan & Distribusi Jalur Charging EV'],
            ['start_date' => '2027-01-11', 'end_date' => '2027-01-15', 'duration_days' => 5, 'priority' => 'high', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 2]
        );
        $t3_3 = Task::updateOrCreate(
            ['project_id' => $p3->id, 'name' => 'Loading Kendaraan & Inspeksi Kesiapan Pemadam Api'],
            ['start_date' => '2027-01-16', 'end_date' => '2027-01-19', 'duration_days' => 3, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 3]
        );

        TaskDependency::firstOrCreate(['predecessor_id' => $t3_1->id, 'successor_id' => $t3_2->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t3_2->id, 'successor_id' => $t3_3->id], ['type' => 'FS']);

        TaskAssignment::firstOrCreate(['task_id' => $t3_1->id, 'resource_id' => $r3Sarah->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t3_2->id, 'resource_id' => $r3Ariel->id], ['allocation_percentage' => 80]);
        TaskAssignment::firstOrCreate(['task_id' => $t3_3->id, 'resource_id' => $r3Sarah->id], ['allocation_percentage' => 100]);

        $cpmCalculator->calculate($p3);

        // -------------------------------------------------------------
        // 6. PROYEK 4: Galang Dana & Konser Amal Peduli Bencana
        // Leader: Sarah Anindya | Member: Maya Putri | Mohamad Nazril R. Daud diundang
        // Karakter: Acara sosial non-profit, linimasa singkat
        // -------------------------------------------------------------
        $p4 = Project::updateOrCreate(
            ['name' => 'Galang Dana & Konser Amal Peduli Bencana'],
            [
                'owner_id' => $sarah->id,
                'work_calendar_id' => $calendarStandard->id,
                'organization' => 'Yayasan Sahabat Kemanusiaan',
                'description' => 'Malam penggalangan donasi dan lelang karya seni untuk pemulihan korban bencana alam.',
                'start_date' => '2026-11-25',
                'deadline' => '2026-12-10',
                'status' => 'active',
            ]
        );

        ProjectMember::updateOrCreate(['project_id' => $p4->id, 'user_id' => $sarah->id], ['role' => 'leader']);
        ProjectMember::updateOrCreate(['project_id' => $p4->id, 'user_id' => $maya->id], ['role' => 'member']);

        // Undangan pending ke Mohamad Nazril R. Daud
        ProjectInvitation::updateOrCreate(
            ['project_id' => $p4->id, 'email' => 'kontak@arieldaud.com'],
            [
                'inviter_id' => $sarah->id,
                'invitee_id' => $ariel->id,
                'status' => 'pending',
                'message' => 'Hai Ariel! Butuh masukan teknis kamu untuk rundown konser amal ini.',
            ]
        );

        $r4Sarah = Resource::updateOrCreate(
            ['project_id' => $p4->id, 'email' => 'sarah@gemilang.com'],
            ['name' => 'Sarah Anindya', 'role' => 'Event Producer', 'cost_per_hour' => 0, 'max_hours_per_day' => 8]
        );
        $r4Maya = Resource::updateOrCreate(
            ['project_id' => $p4->id, 'email' => 'maya@gemilang.com'],
            ['name' => 'Maya Putri', 'role' => 'Koordinator Donasi & Lelang', 'cost_per_hour' => 0, 'max_hours_per_day' => 8]
        );

        $t4_1 = Task::updateOrCreate(
            ['project_id' => $p4->id, 'name' => 'Pengumpulan Barang Lelang Karya Seni'],
            ['start_date' => '2026-11-25', 'end_date' => '2026-11-27', 'duration_days' => 3, 'priority' => 'high', 'status' => 'completed', 'progress_percentage' => 100, 'sort_order' => 1]
        );
        $t4_2 = Task::updateOrCreate(
            ['project_id' => $p4->id, 'name' => 'Broadcast Undangan VIP & Donatur Utama'],
            ['start_date' => '2026-11-30', 'end_date' => '2026-12-02', 'duration_days' => 3, 'priority' => 'urgent', 'status' => 'in_progress', 'progress_percentage' => 50, 'sort_order' => 2]
        );
        $t4_3 = Task::updateOrCreate(
            ['project_id' => $p4->id, 'name' => 'Malam Acara Lelang & Penyerahan Donasi'],
            ['start_date' => '2026-12-03', 'end_date' => '2026-12-03', 'duration_days' => 1, 'priority' => 'urgent', 'status' => 'todo', 'progress_percentage' => 0, 'sort_order' => 3]
        );

        TaskDependency::firstOrCreate(['predecessor_id' => $t4_1->id, 'successor_id' => $t4_2->id], ['type' => 'FS']);
        TaskDependency::firstOrCreate(['predecessor_id' => $t4_2->id, 'successor_id' => $t4_3->id], ['type' => 'FS']);

        TaskAssignment::firstOrCreate(['task_id' => $t4_1->id, 'resource_id' => $r4Maya->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t4_2->id, 'resource_id' => $r4Sarah->id], ['allocation_percentage' => 80]);
        TaskAssignment::firstOrCreate(['task_id' => $t4_3->id, 'resource_id' => $r4Sarah->id], ['allocation_percentage' => 100]);
        TaskAssignment::firstOrCreate(['task_id' => $t4_3->id, 'resource_id' => $r4Maya->id], ['allocation_percentage' => 100]);

        $cpmCalculator->calculate($p4);
    }
}
