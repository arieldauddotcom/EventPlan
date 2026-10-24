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
            return response()->json([
                'exists' => false,
                'error' => 'Unauthorized',
                'message' => 'Hanya Project Leader yang dapat memeriksa email anggota.'
            ], 403);
        }

        $email = strtolower(trim($request->query('email', '')));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'exists' => false,
                'message' => 'Format email tidak valid.'
            ]);
        }

        $targetUser = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        if (!$targetUser) {
            return response()->json([
                'exists' => false,
                'message' => 'Email ' . $email . ' belum terdaftar di sistem EventPlan.'
            ]);
        }

        $isOwner = $project->owner_id === $targetUser->id;
        $existingMember = $project->members()->where('user_id', $targetUser->id)->first();
        $pendingInvitation = $project->invitations()
            ->where(function ($q) use ($targetUser, $email) {
                $q->where('invitee_id', $targetUser->id)
                  ->orWhere('email', $email);
            })
            ->where('status', 'pending')
            ->first();

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
            'has_pending_invitation' => (bool)$pendingInvitation,
            'pending_role' => $pendingInvitation ? ($pendingInvitation->role ?? 'member') : null,
            'message' => 'Pengguna terdaftar ditemukan.'
        ]);
    }

    /**
     * Tambah/Undang anggota tim ke proyek berdasarkan email terdaftar.
     * Mengirim undangan (status: pending) dan baru aktif setelah di-approve oleh pengguna.
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
            'message' => 'nullable|string|max:500',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'role.required' => 'Peran tim wajib dipilih.',
            'role.in' => 'Peran harus berupa member atau leader.',
        ]);

        $email = strtolower(trim($validated['email']));

        // Deteksi apakah email terdaftar di sistem
        $targetUser = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        if (!$targetUser) {
            return back()->with('error', 'Email "' . $email . '" tidak terdaftar di sistem EventPlan. Calon anggota harus memiliki akun terdaftar terlebih dahulu.')
                ->with('tab', 'members')
                ->withInput();
        }

        // Cek jika pengguna mencoba menambahkan dirinya sendiri
        if ($targetUser->id === $user->id) {
            return back()->with('error', 'Anda sudah berada di dalam tim proyek ini.')
                ->with('tab', 'members')
                ->withInput();
        }

        // Cek jika pengguna sudah menjadi anggota aktif atau pemilik
        if ($project->members()->where('user_id', $targetUser->id)->exists() || $project->owner_id === $targetUser->id) {
            return back()->with('error', 'Pengguna "' . $targetUser->name . '" (' . $email . ') sudah menjadi anggota aktif di tim proyek ini.')
                ->with('tab', 'members')
                ->withInput();
        }

        // Cek jika sudah ada undangan pending untuk pengguna ini
        $existingPending = ProjectInvitation::where('project_id', $project->id)
            ->where(function ($q) use ($targetUser, $email) {
                $q->where('invitee_id', $targetUser->id)
                  ->orWhere('email', $email);
            })
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return back()->with('error', 'Undangan untuk ' . $targetUser->name . ' (' . $email . ') sudah dikirim sebelumnya dan sedang menunggu persetujuan (approve).')
                ->with('tab', 'members')
                ->withInput();
        }

        // Buat ProjectInvitation berstatus pending (harus di-approve oleh pengguna)
        ProjectInvitation::create([
            'project_id' => $project->id,
            'inviter_id' => $user->id,
            'invitee_id' => $targetUser->id,
            'email' => $email,
            'role' => $validated['role'],
            'status' => 'pending',
            'message' => $validated['message'] ?? 'Undangan bergabung ke tim proyek ' . $project->name,
        ]);

        $roleLabel = $validated['role'] === 'leader' ? 'Co-Leader' : 'Member';
        return back()->with('success', "Undangan tim berhasil dikirim ke {$targetUser->name} ({$targetUser->email}) sebagai {$roleLabel}! Proyek akan muncul di daftar proyek pengguna setelah disetujui (approve) di menu Undangan Proyek.")
            ->with('tab', 'members');
    }

    /**
     * Ubah role anggota tim proyek (mengangkat menjadi Co-Leader atau menurunkan menjadi Member).
     */
    public function updateRole(Request $request, Project $project, ProjectMember $member)
    {
        $user = Auth::user();

        if (!$project->isLeader($user)) {
            abort(403, 'Hanya Project Leader yang dapat mengubah peran anggota tim.');
        }

        if ($member->project_id !== $project->id) {
            abort(404);
        }

        // Pemilik proyek (Owner) perannya permanen dan tidak dapat diubah
        if ($member->user_id === $project->owner_id) {
            return back()->with('error', 'Peran Pemilik Proyek (Owner) adalah permanen dan tidak dapat diubah.')
                ->with('tab', 'members');
        }

        // Pengguna tidak dapat mengubah perannya sendiri
        if ($member->user_id === $user->id) {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.')
                ->with('tab', 'members');
        }

        // Co-Leader hanya dapat diubah/diturunkan oleh Pemilik Proyek (Owner) atau Administrator
        if ($member->isLeader() && $user->id !== $project->owner_id && !$user->isAdmin()) {
            return back()->with('error', 'Hanya Pemilik Proyek (Owner) yang memiliki wewenang untuk mengubah peran Co-Leader.')
                ->with('tab', 'members');
        }

        $validated = $request->validate([
            'role' => 'required|in:member,leader,co-leader',
        ], [
            'role.required' => 'Peran anggota wajib ditentukan.',
            'role.in' => 'Peran harus berupa member, co-leader, atau leader.',
        ]);

        // Standarisasi role internal: 'leader' mewakili Co-Leader untuk non-owner
        $newRole = in_array($validated['role'], ['leader', 'co-leader']) ? 'leader' : 'member';
        $oldRole = $member->role;

        if ($oldRole === $newRole) {
            $currentLabel = $newRole === 'leader' ? 'Co-Leader' : 'Member';
            return back()->with('info', "Peran {$member->user->name} sudah merupakan {$currentLabel}.")
                ->with('tab', 'members');
        }

        $member->update(['role' => $newRole]);

        // Sinkronisasi peran pada Resource proyek jika ada
        $resource = Resource::where('project_id', $project->id)
            ->where('user_id', $member->user_id)
            ->first();

        if ($resource) {
            if ($newRole === 'leader' && in_array($resource->role, ['Anggota Tim Pelaksana', 'Member', null])) {
                $resource->update(['role' => 'Project Co-Leader']);
            } elseif ($newRole === 'member' && in_array($resource->role, ['Project Co-Leader', 'Leader', 'Co-Leader'])) {
                $resource->update(['role' => 'Anggota Tim Pelaksana']);
            }
        }

        $memberName = $member->user->name ?? 'Anggota';
        $actionText = $newRole === 'leader' ? 'diangkat menjadi Co-Leader' : 'diubah menjadi Member';

        return back()->with('success', "{$memberName} berhasil {$actionText} proyek!")
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
