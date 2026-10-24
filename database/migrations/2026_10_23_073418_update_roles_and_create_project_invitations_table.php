<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah global role pada users: admin atau user (default: user)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            }
        });

        // Set default admin untuk user pertama/admin jika ada
        DB::table('users')->whereIn('email', ['admin@eventplan.local', 'admin@arieldaud.com'])->update(['role' => 'admin']);

        // 2. Sesuaikan tabel project_members: role per proyek (leader / member)
        Schema::table('project_members', function (Blueprint $table) {
            // Ubah tipe kolom role menjadi enum('leader', 'member')
            $table->string('role', 20)->default('member')->change();
        });

        // Normalkan data lama di project_members
        DB::table('project_members')->whereIn('role', ['manager', 'leader'])->update(['role' => 'leader']);
        DB::table('project_members')->whereNotIn('role', ['leader', 'member'])->update(['role' => 'member']);

        // Pastikan setiap project memiliki relasi leader di project_members jika belum ada
        $projects = DB::table('projects')->get();
        foreach ($projects as $p) {
            $exists = DB::table('project_members')->where('project_id', $p->id)->where('user_id', $p->owner_id)->first();
            if ($exists) {
                DB::table('project_members')->where('id', $exists->id)->update(['role' => 'leader']);
            } else {
                DB::table('project_members')->insert([
                    'project_id' => $p->id,
                    'user_id' => $p->owner_id,
                    'role' => 'leader',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Buat tabel project_invitations
        Schema::create('project_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invitee_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('email');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'email', 'status']);
            $table->index(['invitee_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_invitations');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
