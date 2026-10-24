<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectMember;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectInvitationController extends Controller
{
    /**
     * Tampilkan halaman daftar undangan yang diterima pengguna saat ini.
     */
    public function index()
    {
        $user = Auth::user();

        // Cari undangan berdasarkan invitee_id atau matching email
        $invitations = ProjectInvitation::with(['project.owner', 'inviter'])
            ->where(function ($query) use ($user) {
                $query->where('invitee_id', $user->id)
                    ->orWhere('email', strtolower($user->email));
            })
            ->latest()
            ->paginate(15);

        return view('invitations.index', compact('invitations'));
    }

    /**
     * Kirim undangan proyek oleh Project Leader ke email pengguna lain yang sudah terdaftar.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();

        // Pastikan pengguna adalah Project Leader
        if (!$project->isLeader($user)) {
            abort(403, 'Hanya Project Leader yang dapat mengundang anggota.');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'nullable|in:member,leader',
            'message' => 'nullable|string|max:500',
        ]);

        $inviteeEmail = strtolower(trim($validated['email']));

        // Aturan 15: Cegah self-invitation
        if ($inviteeEmail === strtolower($user->email)) {
            return back()->with('error', 'Anda tidak dapat mengundang diri Anda sendiri ke proyek.');
        }

        // Aturan 6: Pengguna harus sudah terdaftar
        $invitee = User::whereRaw('LOWER(TRIM(email)) = ?', [$inviteeEmail])->first();
        if (!$invitee) {
            return back()->with('error', 'Pengguna dengan email ' . $inviteeEmail . ' belum terdaftar di sistem. Harap minta mereka mendaftar terlebih dahulu.');
        }

        // Aturan 15: Cegah duplicate project membership
        if ($project->members()->where('user_id', $invitee->id)->exists() || $project->isOwnedBy($invitee)) {
            return back()->with('error', 'Pengguna ini sudah menjadi anggota pada proyek ini.');
        }

        // Aturan 15: Cegah duplicate pending invitations
        $existingInvitation = ProjectInvitation::where('project_id', $project->id)
            ->where('email', $inviteeEmail)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'Undangan untuk email ini sudah dikirim dan masih berstatus pending.');
        }

        $role = $validated['role'] ?? 'member';

        ProjectInvitation::create([
            'project_id' => $project->id,
            'inviter_id' => $user->id,
            'invitee_id' => $invitee->id,
            'email' => $inviteeEmail,
            'role' => $role,
            'status' => 'pending',
            'message' => $validated['message'] ?? null,
        ]);

        return back()->with('success', 'Undangan berhasil dikirim ke ' . $invitee->name . ' (' . $inviteeEmail . ').');
    }

    /**
     * Terima undangan proyek -> pengguna resmi bergabung ke proyek.
     */
    public function accept(ProjectInvitation $invitation)
    {
        $user = Auth::user();

        // Validasi kepemilikan undangan
        if ($invitation->invitee_id !== $user->id && strtolower($invitation->email) !== strtolower($user->email)) {
            abort(403, 'Anda tidak berhak mengakses undangan ini.');
        }

        if (!$invitation->isPending()) {
            return back()->with('error', 'Undangan ini sudah ' . $invitation->status . '.');
        }

        $project = $invitation->project;
        $role = in_array($invitation->role, ['leader', 'member']) ? $invitation->role : 'member';

        // Buat keanggotaan proyek setelah di-approve oleh pengguna
        if (!$project->members()->where('user_id', $user->id)->exists()) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role' => $role,
            ]);
        }

        // Sinkronisasi otomatis ke tabel Resource agar langsung dapat ditugaskan tugas
        Resource::firstOrCreate(
            [
                'project_id' => $project->id,
                'email' => $user->email,
            ],
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $role === 'leader' ? 'Project Co-Leader' : 'Anggota Tim Pelaksana',
                'cost_per_hour' => 50000,
                'max_hours_per_day' => 8,
            ]
        );

        $invitation->update([
            'invitee_id' => $user->id,
            'status' => 'accepted',
        ]);

        $roleLabel = $role === 'leader' ? 'Co-Leader' : 'Member';
        return redirect()->route('projects.show', $project)->with('success', 'Selamat bergabung! Anda telah menyetujui undangan dan resmi menjadi ' . $roleLabel . ' di proyek ' . $project->name . '.');
    }

    /**
     * Tolak undangan proyek.
     */
    public function decline(ProjectInvitation $invitation)
    {
        $user = Auth::user();

        if ($invitation->invitee_id !== $user->id && strtolower($invitation->email) !== strtolower($user->email)) {
            abort(403, 'Anda tidak berhak mengakses undangan ini.');
        }

        if (!$invitation->isPending()) {
            return back()->with('error', 'Undangan ini sudah ' . $invitation->status . '.');
        }

        $invitation->update([
            'invitee_id' => $user->id,
            'status' => 'declined',
        ]);

        return back()->with('success', 'Undangan proyek telah ditolak.');
    }

    /**
     * Batalkan undangan oleh Project Leader.
     */
    public function destroy(Project $project, ProjectInvitation $invitation)
    {
        $user = Auth::user();

        if (!$project->isLeader($user)) {
            abort(403, 'Hanya Project Leader yang dapat membatalkan undangan.');
        }

        if ($invitation->project_id !== $project->id) {
            abort(404);
        }

        $invitation->delete();

        return back()->with('success', 'Undangan berhasil dibatalkan.');
    }
}
