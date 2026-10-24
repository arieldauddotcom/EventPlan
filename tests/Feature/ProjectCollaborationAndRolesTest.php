<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectMember;
use App\Models\Resource;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Models\WorkCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCollaborationAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected User $member;
    protected User $externalUser;
    protected User $admin;
    protected Project $project;
    protected WorkCalendar $calendar;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles global: admin dan user
        $this->admin = User::factory()->create([
            'email' => 'admin@eventplan.local',
            'role' => 'admin',
        ]);

        $this->leader = User::factory()->create([
            'name' => 'Leader User',
            'email' => 'leader@example.com',
            'role' => 'user',
        ]);

        $this->member = User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'role' => 'user',
        ]);

        $this->externalUser = User::factory()->create([
            'name' => 'External User',
            'email' => 'external@example.com',
            'role' => 'user',
        ]);

        $this->calendar = WorkCalendar::create([
            'name' => 'Kalender Standar',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        // Leader membuat proyek (otomatis jadi Leader)
        $this->project = Project::create([
            'owner_id' => $this->leader->id,
            'work_calendar_id' => $this->calendar->id,
            'name' => 'Konser Musik Nusantara',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-20',
            'status' => 'active',
        ]);

        ProjectMember::create([
            'project_id' => $this->project->id,
            'user_id' => $this->leader->id,
            'role' => 'leader',
        ]);
    }

    public function test_user_can_create_project_and_automatically_becomes_leader(): void
    {
        $response = $this->actingAs($this->member)->post('/projects', [
            'name' => 'Proyek Baru Member',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-10',
            'status' => 'planning',
        ]);

        $response->assertRedirect();
        $newProject = Project::where('name', 'Proyek Baru Member')->first();
        $this->assertNotNull($newProject);
        $this->assertEquals($this->member->id, $newProject->owner_id);

        // Cek bahwa member otomatis menjadi leader di proyek barunya
        $this->assertDatabaseHas('project_members', [
            'project_id' => $newProject->id,
            'user_id' => $this->member->id,
            'role' => 'leader',
        ]);
        $this->assertTrue($newProject->isLeader($this->member));
    }

    public function test_leader_can_invite_registered_user_by_email(): void
    {
        $response = $this->actingAs($this->leader)->post("/projects/{$this->project->id}/invitations", [
            'email' => 'member@example.com',
            'message' => 'Silakan gabung tim!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('project_invitations', [
            'project_id' => $this->project->id,
            'inviter_id' => $this->leader->id,
            'invitee_id' => $this->member->id,
            'email' => 'member@example.com',
            'status' => 'pending',
            'message' => 'Silakan gabung tim!',
        ]);
    }

    public function test_cannot_invite_unregistered_email(): void
    {
        $response = $this->actingAs($this->leader)->post("/projects/{$this->project->id}/invitations", [
            'email' => 'unknown_person@example.com',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('project_invitations', [
            'email' => 'unknown_person@example.com',
        ]);
    }

    public function test_prevents_self_invitation(): void
    {
        $response = $this->actingAs($this->leader)->post("/projects/{$this->project->id}/invitations", [
            'email' => 'leader@example.com',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('project_invitations', [
            'email' => 'leader@example.com',
        ]);
    }

    public function test_prevents_duplicate_invitations(): void
    {
        ProjectInvitation::create([
            'project_id' => $this->project->id,
            'inviter_id' => $this->leader->id,
            'invitee_id' => $this->member->id,
            'email' => 'member@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->leader)->post("/projects/{$this->project->id}/invitations", [
            'email' => 'member@example.com',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, ProjectInvitation::where('email', 'member@example.com')->count());
    }

    public function test_invitee_can_accept_invitation_and_becomes_project_member(): void
    {
        $invitation = ProjectInvitation::create([
            'project_id' => $this->project->id,
            'inviter_id' => $this->leader->id,
            'invitee_id' => $this->member->id,
            'email' => 'member@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->member)->post("/invitations/{$invitation->id}/accept");
        $response->assertRedirect("/projects/{$this->project->id}");

        $this->assertEquals('accepted', $invitation->fresh()->status);
        $this->assertDatabaseHas('project_members', [
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);
        $this->assertTrue($this->project->isMember($this->member));
    }

    public function test_invitee_can_decline_invitation(): void
    {
        $invitation = ProjectInvitation::create([
            'project_id' => $this->project->id,
            'inviter_id' => $this->leader->id,
            'invitee_id' => $this->member->id,
            'email' => 'member@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->member)->post("/invitations/{$invitation->id}/decline");
        $response->assertSessionHas('success');

        $this->assertEquals('declined', $invitation->fresh()->status);
        $this->assertDatabaseMissing('project_members', [
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_leader_can_cancel_invitation_and_remove_member(): void
    {
        // Cancel pending invitation
        $invitation = ProjectInvitation::create([
            'project_id' => $this->project->id,
            'inviter_id' => $this->leader->id,
            'invitee_id' => $this->externalUser->id,
            'email' => 'external@example.com',
            'status' => 'pending',
        ]);

        $delInvResponse = $this->actingAs($this->leader)->delete("/projects/{$this->project->id}/invitations/{$invitation->id}");
        $delInvResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('project_invitations', ['id' => $invitation->id]);

        // Remove active member
        $memberRecord = ProjectMember::create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);

        $delMemberResponse = $this->actingAs($this->leader)->delete("/projects/{$this->project->id}/members/{$memberRecord->id}");
        $delMemberResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('project_members', ['id' => $memberRecord->id]);
    }

    public function test_member_can_view_project_and_visualizations_but_not_edit_project_settings(): void
    {
        ProjectMember::create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);

        // Member can view project & charts
        $this->actingAs($this->member)->get("/projects/{$this->project->id}")->assertStatus(200);
        $this->actingAs($this->member)->get("/projects/{$this->project->id}/gantt")->assertStatus(200);
        $this->actingAs($this->member)->get("/projects/{$this->project->id}/pert")->assertStatus(200);
        $this->actingAs($this->member)->get("/projects/{$this->project->id}/resources-chart")->assertStatus(200);

        // Member cannot edit project settings (Forbidden 403)
        $this->actingAs($this->member)->get("/projects/{$this->project->id}/edit")->assertStatus(403);
        $this->actingAs($this->member)->put("/projects/{$this->project->id}", [
            'name' => 'Nama Baru Diubah Member',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-20',
            'status' => 'active',
            'work_calendar_id' => $this->calendar->id,
        ])->assertStatus(403);

        // Member cannot add holiday
        $this->actingAs($this->member)->post("/projects/{$this->project->id}/holidays", [
            'name' => 'Libur Member',
            'date' => '2026-11-15',
        ])->assertStatus(403);
    }

    public function test_member_can_only_update_progress_of_assigned_task(): void
    {
        ProjectMember::create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);

        $resourceMember = Resource::create([
            'project_id' => $this->project->id,
            'name' => 'Member Resource',
            'email' => 'member@example.com',
            'role' => 'Staff Lapangan',
        ]);

        $taskAssigned = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Tugas Khusus Member',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-04',
            'duration_days' => 3,
            'priority' => 'high',
            'status' => 'todo',
            'progress_percentage' => 0,
        ]);

        TaskAssignment::create([
            'task_id' => $taskAssigned->id,
            'resource_id' => $resourceMember->id,
            'allocation_percentage' => 100,
        ]);

        $taskNotAssigned = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Tugas Orang Lain',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-04',
            'duration_days' => 3,
            'priority' => 'low',
            'status' => 'todo',
            'progress_percentage' => 0,
        ]);

        // Member boleh mengupdate progres tugas yang di-assign padanya
        $responseAssigned = $this->actingAs($this->member)->put("/projects/{$this->project->id}/tasks/{$taskAssigned->id}", [
            'status' => 'in_progress',
            'progress_percentage' => 75,
        ]);
        $responseAssigned->assertSessionHas('success');
        $this->assertEquals(75, $taskAssigned->fresh()->progress_percentage);
        $this->assertEquals('in_progress', $taskAssigned->fresh()->status);

        // Member dilarang mengupdate tugas yang bukan miliknya (403)
        $responseNotAssigned = $this->actingAs($this->member)->put("/projects/{$this->project->id}/tasks/{$taskNotAssigned->id}", [
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);
        $responseNotAssigned->assertStatus(403);
    }

    public function test_external_user_cannot_access_unrelated_project(): void
    {
        $response = $this->actingAs($this->externalUser)->get("/projects/{$this->project->id}");
        $response->assertStatus(403);
    }
}
