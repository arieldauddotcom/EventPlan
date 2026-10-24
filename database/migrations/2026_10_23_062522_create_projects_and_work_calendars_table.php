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
        // Work Calendars
        Schema::create('work_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Kalender Standar');
            // JSON array of working days: [1,2,3,4,5] (1 = Senin, 7 = Minggu)
            $table->json('working_days');
            $table->decimal('hours_per_day', 4, 2)->default(8.00);
            $table->timestamps();
        });

        // Holidays per calendar
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_calendar_id')->constrained('work_calendars')->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->timestamps();

            $table->unique(['work_calendar_id', 'date']);
        });

        // Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('work_calendar_id')->constrained('work_calendars')->restrictOnDelete();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('deadline');
            $table->enum('status', ['draft', 'planning', 'active', 'completed', 'archived'])->default('planning');
            $table->timestamps();
        });

        // Project Members
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['manager', 'member', 'viewer'])->default('member');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('work_calendars');
    }
};
