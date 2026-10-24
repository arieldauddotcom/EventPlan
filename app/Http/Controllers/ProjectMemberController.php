<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectMember;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
    /**
     * Cek apakah email terdaftar di sistem dan status keanggotaannya pada proyek.
     */
    public function checkUserEmail(Request $request, Project $project)
    {
        $user = Auth::user();
        if (!$project->isLeader($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $email = strtolower(trim($request->query('email', '')));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'exists' => false,
                'message' => 'Format email tidak valid.'
            ]);
        }

        $targetUser = User::where('email', $email)->first();
        if (!$targetUser) {
            return response()->json([
                'exists' => false,
                'message' => 'Email ' . $email . ' belum terdaftar di sistem EventPlan.'
            ]);
        }

        $isOwner = $project->owner_id === $targetUser->id;
        $existingMember = $project->members()->where('user_id', $targetUser->id)->first();

        return response()->json([
            'exists' => true,
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'username' => $targetUser->username,
                'email' => $targetUser->email,
                'avatar_url' => $targetUser->avatar_url,
            ],
            'is_member' => (bool)$existingMember || $isOwner,
            'is_owner' => $isOwner,
            'member_role' => $existingMember ? $existingMember->role : ($isOwner ? 'Leader (Owner)' : null),
            'message' => 'Pengguna terdaftar ditemukan.'
        ]);
    }

    /**
     * Tambah anggota tim ke proyek secara langsung berdasarkan email terdaftar.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();

        if (!$project->isLeader($user)) {
            abort(403, 'Hanya Project Leader yang dapat menambahkan anggota tim.');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'required|in:member,leader',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'role.required' => 'Peran tim wajib dipilih.',
            'role.in' => 'Peran harus berupa member atau leader.',
        ]);

        $email = strtolower(trim($validated['email']));

        // Deteksi apakah email terdaftar di sistem
        $targetUser = User::where('email', $email)->first();
        if (!$targetUser) {
            return back()->with('error', 'Email "' . $email . '" tidak terdaftar di sistem EventPlan. Anggota tim harus memiliki akun terdaftar terlebih dahulu.')
                ->with('tab', 'members')
                ->withInput();
        }

        // Cek jika pengguna mencoba menambahkan dirinya sendiri
        if ($targetUser->id === $user->id) {
            return back()->with('error', 'Anda sudah berada di dalam tim proyek ini.')
                ->with('tab', 'members')
                ->withInput();
        }

        // Cek jika pengguna sudah menjadi anggota atau pemilik
        if ($project->members()->where('user_id', $targetUser->id)->exists() || $project->owner_id === $targetUser->id) {
            return back()->with('error', 'Pengguna "' . $targetUser->name . '" (' . $email . ') sudah terdaftar di tim proyek ini.')
                ->with('tab', 'members')
                ->withInput();
        }

        // Buat ProjectMember
        $member = ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $targetUser->id,
            'role' => $validated['role'],
        ]);

        // Sinkronisasi otomatis ke tabel Resource agar langsung dapat ditugaskan tugas
        Resource::firstOrCreate(
            [
                'project_id' => $project->id,
                'email' => $targetUser->email,
            ],
            [
                'user_id' => $targetUser->id,
                'name' => $targetUser->name,
                'role' => $validated['role'] === 'leader' ? 'Project Co-Leader' : 'Anggota Tim Pelaksana',
                'cost_per_hour' => 50000,
                'max_hours_per_day' => 8,
            ]
        );

        // Jika ada invitation pending untuk email ini, tandai sebagai accepted
        ProjectInvitation::where('project_id', $project->id)
            ->where('email', $email)
            ->update([
                'invitee_id' => $targetUser->id,
                'status' => 'accepted',
            ]);

        $roleLabel = $validated['role'] === 'leader' ? 'Leader' : 'Member';
        return back()->with('success', "{$targetUser->name} ({$targetUser->email}) berhasil ditambahkan ke tim proyek sebagai {$roleLabel}!")
            ->with('tab', 'members');
    }

    /**
     * Keluarkan anggota dari tim proyek oleh Project Leader.
     */
    public function destroy(Project $project, ProjectMember $member)
    {
        $user = Auth::user();

        if (!$project->isLeader($user)) {
            abort(403, 'Hanya Project Leader yang dapat mengeluarkan anggota tim proyek.');
        }

        if ($member->project_id !== $project->id) {
            abort(404);
        }

        // Pemilik proyek (Owner) tidak dapat dikeluarkan
        if ($member->user_id === $project->owner_id) {
            return back()->with('error', 'Pemilik proyek tidak dapat dikeluarkan dari tim proyek.')
                ->with('tab', 'members');
        }

        // Co-Leader hanya dapat dikeluarkan oleh Pemilik Proyek (Owner)
        if ($member->isLeader() && $user->id !== $project->owner_id) {
            return back()->with('error', 'Hanya Pemilik Proyek yang dapat mengeluarkan Project Leader lainnya.')
                ->with('tab', 'members');
        }

        $memberName = $member->user->name ?? 'Anggota';
        $member->delete();

        return back()->with('success', $memberName . ' berhasil dikeluarkan dari tim proyek.')
            ->with('tab', 'members');
    }
}
