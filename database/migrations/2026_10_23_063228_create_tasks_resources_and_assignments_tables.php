<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Resources (Anggota Tim / Sumber Daya)
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->default('Anggota'); // e.g. Koordinator, Desainer, Logistik
            $table->decimal('cost_per_hour', 12, 2)->default(0.00);
            $table->decimal('max_hours_per_day', 4, 1)->default(8.0);
            $table->timestamps();

            $table->index(['project_id', 'email']);
        });

        // 2. Resource Leaves (Jadwal Cuti / Ketidakhadiran)
        Schema::create('resource_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['resource_id', 'start_date', 'end_date']);
        });

        // 3. Tasks (Tugas dan Subtugas)
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days')->default(1); // Durasi hari kerja
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['todo', 'in_progress', 'review', 'completed'])->default('todo');
            $table->tinyInteger('progress_percentage')->default(0); // 0 - 100
            $table->integer('sort_order')->default(0);

            // CPM fields untuk caching hasil kalkulasi
            $table->integer('early_start')->default(0);
            $table->integer('early_finish')->default(0);
            $table->integer('late_start')->nullable();
            $table->integer('late_finish')->nullable();
            $table->integer('total_float')->nullable();
            $table->boolean('is_critical')->default(false);

            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'parent_id']);
            $table->index(['project_id', 'is_critical']);
        });

        // 4. Task Dependencies (Relasi Ketergantungan Predecessor -> Successor)
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predecessor_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('successor_id')->constrained('tasks')->cascadeOnDelete();
            $table->enum('type', ['FS', 'SS'])->default('FS'); // Finish-to-Start, Start-to-Start
            $table->integer('lag_days')->default(0); // Positive or zero lag
            $table->timestamps();

            $table->unique(['predecessor_id', 'successor_id'], 'task_dep_unique');
        });

        // 5. Task Assignments (Penugasan Sumber Daya ke Tugas)
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->tinyInteger('allocation_percentage')->default(100); // 1 - 100%
            $table->timestamps();

            $table->unique(['task_id', 'resource_id'], 'task_res_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('resource_leaves');
        Schema::dropIfExists('resources');
    }
};
