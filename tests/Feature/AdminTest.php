<?php

namespace Tests\Feature;

use App\Mail\ApplicantMessage;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\MessageTemplate;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $recruiter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->recruiter = User::factory()->create(['role' => 'recruiter']);
    }

    public function test_guests_are_redirected_and_login_works(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->post('/login', ['email' => $this->admin->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_recruiter_cannot_access_admin_only_areas(): void
    {
        $this->actingAs($this->recruiter)->get('/admin')->assertOk();
        $this->actingAs($this->recruiter)->get(route('admin.jobs.index'))->assertOk();
        $this->actingAs($this->recruiter)->get(route('admin.settings.edit'))->assertForbidden();
        $this->actingAs($this->recruiter)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($this->recruiter)->get(route('admin.services.index'))->assertForbidden();
        $this->actingAs($this->admin)->get(route('admin.settings.edit'))->assertOk();
    }

    public function test_recruiter_can_post_a_job_that_goes_live(): void
    {
        $this->actingAs($this->recruiter)->post(route('admin.jobs.store'), [
            'title' => 'K9 Officer', 'location' => 'Piarco', 'employment_type' => 'shift', 'summary' => 'Handle and care for patrol dogs.',
            'requirements' => "- Valid PCC\n• Dog handling experience", 'openings' => 2, 'status' => 'open',
        ])->assertRedirect();
        $job = JobPosting::where('title', 'K9 Officer')->first();
        $this->assertTrue($job->isLive());
        $this->assertSame(['Valid PCC', 'Dog handling experience'], $job->requirements);
        $this->get('/careers')->assertSee('K9 Officer');

        $this->actingAs($this->recruiter)->patch(route('admin.jobs.status', $job), ['status' => 'closed']);
        $this->get(route('careers.show', $job->fresh()))->assertNotFound();
    }

    public function test_pipeline_drag_updates_stage_and_logs_timeline(): void
    {
        $app = Application::factory()->create(['job_posting_id' => JobPosting::first()->id]);
        $this->actingAs($this->recruiter)->patchJson(route('admin.applications.update', $app), ['status' => 'interview'])
            ->assertOk()->assertJson(['status' => 'interview']);
        $this->assertSame('interview', $app->fresh()->status);
        $this->assertDatabaseHas('application_notes', ['application_id' => $app->id, 'type' => 'status']);
        $this->actingAs($this->recruiter)->get(route('admin.applications.pipeline'))->assertOk()->assertSee($app->full_name);
    }

    public function test_templated_email_is_sent_and_moves_stage(): void
    {
        Mail::fake();
        $app = Application::factory()->create(['job_posting_id' => JobPosting::first()->id, 'interview_at' => now()->addDays(3)]);
        $t = MessageTemplate::where('set_status', 'interview')->first();
        $this->actingAs($this->recruiter)->post(route('admin.applications.message', $app), ['subject' => $t->subject, 'body' => $t->body, 'set_status' => $t->set_status])->assertRedirect();
        Mail::assertSent(ApplicantMessage::class, fn ($m) => str_contains($m->subjectLine, $app->reference) && str_contains($m->body, $app->first_name));
        $this->assertSame('interview', $app->fresh()->status);
        $this->assertDatabaseHas('application_notes', ['application_id' => $app->id, 'type' => 'email']);
    }

    public function test_only_admin_can_delete_applicants_and_export_works(): void
    {
        $app = Application::factory()->create();
        $this->actingAs($this->recruiter)->delete(route('admin.applications.destroy', $app))->assertForbidden();
        $this->actingAs($this->admin)->get(route('admin.applications.export'))->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->actingAs($this->admin)->delete(route('admin.applications.destroy', $app))->assertRedirect(route('admin.applications.index'));
        $this->assertDatabaseMissing('applications', ['id' => $app->id]);
    }

    public function test_dashboard_and_show_pages_render(): void
    {
        $app = Application::factory()->create(['job_posting_id' => JobPosting::first()->id]);
        $this->actingAs($this->admin)->get('/admin')->assertOk();
        $this->actingAs($this->admin)->get(route('admin.applications.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.applications.show', $app))->assertOk()->assertSee($app->reference);
        $this->actingAs($this->admin)->get(route('admin.jobs.edit', JobPosting::first()))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.enquiries.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.templates.index'))->assertOk();
    }
}
