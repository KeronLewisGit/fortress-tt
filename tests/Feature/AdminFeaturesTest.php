<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Enquiry;
use App\Models\JobPosting;
use App\Models\MessageTemplate;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** End-to-end coverage of every admin action: jobs, applicants, enquiries, templates, services, settings, users, profile. */
class AdminFeaturesTest extends TestCase
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

    // ---------- Jobs ----------

    public function test_job_status_toggle_publish_pause_and_duplicate(): void
    {
        $job = JobPosting::factory()->draft()->create(['title' => 'Draft Role']);
        $this->actingAs($this->recruiter)->patch(route('admin.jobs.status', $job), ['status' => 'open'])->assertRedirect();
        $job->refresh();
        $this->assertSame('open', $job->status);
        $this->assertNotNull($job->published_at, 'publishing a draft sets published_at');
        $this->assertTrue($job->isLive());

        $this->actingAs($this->recruiter)->patch(route('admin.jobs.status', $job), ['status' => 'paused']);
        $this->assertSame('paused', $job->fresh()->status);
        $this->assertFalse($job->fresh()->isLive());
        $this->get(route('careers.show', $job))->assertNotFound();

        $this->actingAs($this->recruiter)->patch(route('admin.jobs.status', $job), ['status' => 'bogus'])->assertSessionHasErrors('status');

        $this->actingAs($this->recruiter)->post(route('admin.jobs.duplicate', $job))->assertRedirect();
        $copy = JobPosting::where('title', 'Draft Role (copy)')->first();
        $this->assertNotNull($copy);
        $this->assertSame('draft', $copy->status);
        $this->assertNull($copy->published_at);
        $this->assertSame(0, $copy->views);
        $this->assertNotSame($job->slug, $copy->slug);
    }

    public function test_deleting_a_job_keeps_its_applications_as_general(): void
    {
        $job = JobPosting::first();
        $app = Application::factory()->create(['job_posting_id' => $job->id]);
        $this->actingAs($this->recruiter)->delete(route('admin.jobs.destroy', $job))->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseMissing('job_postings', ['id' => $job->id]);
        $this->assertNull($app->fresh()->job_posting_id);
        $this->actingAs($this->admin)->get(route('admin.applications.show', $app))->assertOk()->assertSee('General application');
    }

    public function test_job_update_regenerates_slug_only_when_asked_and_validates_fields(): void
    {
        $job = JobPosting::first();
        $payload = ['title' => 'Renamed Role', 'location' => $job->location, 'employment_type' => 'full_time', 'summary' => 'Summary', 'openings' => 2, 'status' => 'open'];
        $this->actingAs($this->admin)->put(route('admin.jobs.update', $job), $payload)->assertRedirect();
        $this->assertSame($job->slug, $job->fresh()->slug);
        $this->actingAs($this->admin)->put(route('admin.jobs.update', $job), array_merge($payload, ['regenerate_slug' => 1]))->assertRedirect();
        $this->assertSame('renamed-role', $job->fresh()->slug);

        $this->actingAs($this->admin)->put(route('admin.jobs.update', $job->fresh()), array_merge($payload, ['employment_type' => 'gig', 'openings' => 0]))
            ->assertSessionHasErrors(['employment_type', 'openings']);
    }

    // ---------- Applicants ----------

    public function test_applicant_filters_narrow_the_list(): void
    {
        $job = JobPosting::first();
        $a = Application::factory()->create(['job_posting_id' => $job->id, 'first_name' => 'Zed', 'last_name' => 'Alpha', 'licence_class' => '5', 'has_police_certificate' => true, 'has_precept' => true, 'rating' => 5, 'assigned_to' => $this->recruiter->id, 'area' => 'Couva']);
        $b = Application::factory()->create(['job_posting_id' => null, 'first_name' => 'Yan', 'last_name' => 'Beta', 'licence_class' => 'none', 'has_police_certificate' => false, 'rating' => null, 'assigned_to' => null, 'area' => 'Arima']);

        $as = fn () => $this->actingAs($this->recruiter);
        $as()->get(route('admin.applications.index', ['job' => 'general']))->assertSee('Yan Beta')->assertDontSee('Zed Alpha');
        $as()->get(route('admin.applications.index', ['job' => $job->id]))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['assigned' => 'me']))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['assigned' => 'none']))->assertSee('Yan Beta')->assertDontSee('Zed Alpha');
        $as()->get(route('admin.applications.index', ['licence' => '5']))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['police' => 1, 'precept' => 1]))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['rating' => 4]))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['q' => 'Couva']))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['q' => 'Zed Alpha']))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
        $as()->get(route('admin.applications.index', ['q' => $b->reference]))->assertSee('Yan Beta')->assertDontSee('Zed Alpha');
        $as()->get(route('admin.applications.pipeline', ['job' => $job->id]))->assertSee('Zed Alpha')->assertDontSee('Yan Beta');
    }

    public function test_applicant_can_be_rated_assigned_moved_and_annotated(): void
    {
        $app = Application::factory()->create();
        $job = JobPosting::first();
        $this->actingAs($this->recruiter)->patch(route('admin.applications.update', $app), [
            'rating' => 3, 'assigned_to' => $this->recruiter->id, 'job_posting_id' => $job->id, 'status' => 'screening', 'comment' => 'Called, documents OK',
        ])->assertRedirect();
        $app->refresh();
        $this->assertSame(3, $app->rating);
        $this->assertSame($this->recruiter->id, $app->assigned_to);
        $this->assertSame($job->id, $app->job_posting_id);
        $this->assertSame('screening', $app->status);
        $this->assertDatabaseHas('application_notes', ['application_id' => $app->id, 'type' => 'status']);

        $this->actingAs($this->recruiter)->patch(route('admin.applications.update', $app), ['rating' => 0, 'assigned_to' => ''])->assertRedirect();
        $this->assertNull($app->fresh()->rating);
        $this->assertNull($app->fresh()->assigned_to);

        $this->actingAs($this->recruiter)->post(route('admin.applications.notes.store', $app), ['body' => 'References checked'])->assertRedirect();
        $this->assertDatabaseHas('application_notes', ['application_id' => $app->id, 'type' => 'note', 'body' => 'References checked', 'user_id' => $this->recruiter->id]);
        $this->actingAs($this->recruiter)->post(route('admin.applications.notes.store', $app), ['body' => ''])->assertSessionHasErrors('body');

        $this->actingAs($this->recruiter)->patch(route('admin.applications.update', $app), ['status' => 'not-a-stage'])->assertSessionHasErrors('status');
    }

    public function test_resume_download_streams_the_stored_file(): void
    {
        Storage::fake('local');
        $path = UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')->store('resumes', 'local');
        $app = Application::factory()->create(['resume_path' => $path, 'resume_name' => 'Kerry CV.pdf']);
        $response = $this->actingAs($this->recruiter)->get(route('admin.applications.resume', $app));
        $response->assertOk();
        $this->assertStringContainsString('Kerry CV.pdf', $response->headers->get('content-disposition'));
        $this->post('/logout');
        $this->get(route('admin.applications.resume', $app))->assertRedirect('/login');
    }

    // ---------- Enquiries ----------

    public function test_enquiries_can_be_filtered_updated_and_deleted(): void
    {
        $e = Enquiry::create(['name' => 'Marissa Lee', 'company' => 'Westside Plaza', 'email' => 'm@example.com', 'message' => 'Need two officers nightly.']);
        $this->actingAs($this->recruiter)->get(route('admin.enquiries.index', ['status' => 'new']))->assertOk()->assertSee('Marissa Lee');
        $this->actingAs($this->recruiter)->get(route('admin.enquiries.index', ['status' => 'won']))->assertOk()->assertDontSee('Marissa Lee');
        $this->actingAs($this->recruiter)->get(route('admin.enquiries.index', ['q' => 'Westside']))->assertSee('Marissa Lee');

        $this->actingAs($this->recruiter)->put(route('admin.enquiries.update', $e), ['status' => 'quoted', 'notes' => 'Quote sent', 'assigned_to' => $this->recruiter->id])->assertRedirect();
        $e->refresh();
        $this->assertSame('quoted', $e->status);
        $this->assertSame('Quote sent', $e->notes);
        $this->assertSame($this->recruiter->id, $e->assigned_to);
        $this->actingAs($this->recruiter)->put(route('admin.enquiries.update', $e), ['status' => 'nope'])->assertSessionHasErrors('status');

        $this->actingAs($this->recruiter)->delete(route('admin.enquiries.destroy', $e))->assertRedirect(route('admin.enquiries.index'));
        $this->assertDatabaseMissing('enquiries', ['id' => $e->id]);
    }

    // ---------- Templates ----------

    public function test_templates_crud(): void
    {
        $this->actingAs($this->recruiter)->post(route('admin.templates.store'), ['name' => 'Reminder', 'subject' => 'Hi {first_name}', 'body' => 'See you on {interview_date}', 'set_status' => 'interview', 'sort_order' => 9])->assertRedirect();
        $t = MessageTemplate::where('name', 'Reminder')->firstOrFail();
        $this->actingAs($this->recruiter)->get(route('admin.templates.edit', $t))->assertOk()->assertSee('Reminder');
        $this->actingAs($this->recruiter)->put(route('admin.templates.update', $t), ['name' => 'Reminder 2', 'subject' => 'S', 'body' => 'B', 'set_status' => '', 'sort_order' => 1])->assertRedirect();
        $this->assertSame('Reminder 2', $t->fresh()->name);
        $this->assertNull($t->fresh()->set_status);
        $this->actingAs($this->recruiter)->put(route('admin.templates.update', $t), ['name' => 'X', 'subject' => 'S', 'body' => 'B', 'set_status' => 'wrong', 'sort_order' => 1])->assertSessionHasErrors('set_status');
        $this->actingAs($this->recruiter)->delete(route('admin.templates.destroy', $t))->assertRedirect(route('admin.templates.index'));
        $this->assertDatabaseMissing('message_templates', ['id' => $t->id]);
    }

    // ---------- Services (admin only) ----------

    public function test_services_crud_and_visibility_on_site(): void
    {
        $this->actingAs($this->recruiter)->post(route('admin.services.store'), ['name' => 'X'])->assertForbidden();

        $this->actingAs($this->admin)->post(route('admin.services.store'), ['name' => 'Drone Surveillance', 'icon' => 'eye', 'summary' => 'Aerial patrols.', 'features' => "Thermal\n- Night", 'sort_order' => 5, 'is_active' => 1])->assertRedirect();
        $s = Service::where('name', 'Drone Surveillance')->firstOrFail();
        $this->assertSame('drone-surveillance', $s->slug);
        $this->assertSame(['Thermal', 'Night'], $s->features);
        $this->get('/services/drone-surveillance')->assertOk();

        $this->actingAs($this->admin)->put(route('admin.services.update', $s), ['name' => 'Drone Surveillance', 'icon' => 'eye', 'summary' => 'Aerial patrols.', 'sort_order' => 5])->assertRedirect();
        $this->assertFalse($s->fresh()->is_active, 'unchecked checkbox hides the service');
        $this->get('/services/drone-surveillance')->assertNotFound();
        $this->get('/services')->assertDontSee('Drone Surveillance');

        $this->actingAs($this->admin)->put(route('admin.services.update', $s), ['name' => 'Drone', 'icon' => 'not-an-icon', 'summary' => 'x', 'sort_order' => 1])->assertSessionHasErrors('icon');
        $this->actingAs($this->admin)->delete(route('admin.services.destroy', $s))->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $s->id]);
    }

    // ---------- Settings ----------

    public function test_settings_checkboxes_are_coerced_and_affect_the_site(): void
    {
        $base = ['business' => ['name' => 'Fortress'], 'careers' => ['intro' => 'Join us']];
        $this->actingAs($this->admin)->put(route('admin.settings.update'), ['settings' => $base])->assertRedirect();
        $this->assertSame('0', Setting::get('careers.general_applications', 'missing'));
        $this->assertSame('0', Setting::get('careers.auto_reply', 'missing'));
        $this->get('/careers/apply')->assertNotFound();

        $this->actingAs($this->admin)->put(route('admin.settings.update'), ['settings' => array_replace_recursive($base, ['careers' => ['general_applications' => '1', 'auto_reply' => '1']])])->assertRedirect();
        $this->assertSame('1', Setting::get('careers.general_applications'));
        $this->get('/careers/apply')->assertOk();

        $this->actingAs($this->admin)->put(route('admin.settings.update'), ['settings' => ['business' => ['name' => '', 'facebook' => 'not a url']]])->assertSessionHasErrors(['settings.business.name', 'settings.business.facebook']);
    }

    // ---------- Users & profile ----------

    public function test_user_management_rules(): void
    {
        $this->actingAs($this->admin)->post(route('admin.users.store'), ['name' => 'New Recruiter', 'email' => 'NEW@Fortress.tt', 'password' => 'password123', 'role' => 'recruiter'])->assertRedirect(route('admin.users.index'));
        $u = User::where('email', 'new@fortress.tt')->firstOrFail();
        $this->assertTrue($u->is_active, 'new users are active by default');
        $this->post('/logout');
        $this->post('/login', ['email' => 'new@fortress.tt', 'password' => 'password123'])->assertRedirect('/admin');
        $this->post('/logout');

        // Update without password keeps the old one; deactivating blocks login.
        $this->actingAs($this->admin)->put(route('admin.users.update', $u), ['name' => 'Renamed', 'email' => 'new@fortress.tt', 'role' => 'recruiter'])->assertRedirect();
        $this->assertFalse($u->fresh()->is_active);
        $this->post('/logout');
        $this->post('/login', ['email' => 'new@fortress.tt', 'password' => 'password123'])->assertSessionHasErrors('email');

        // Duplicate email rejected; self cannot be demoted or deleted.
        $this->actingAs($this->admin)->post(route('admin.users.store'), ['name' => 'Dup', 'email' => 'new@fortress.tt', 'password' => 'password123', 'role' => 'recruiter'])->assertSessionHasErrors('email');
        $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), ['name' => 'Me', 'email' => $this->admin->email, 'role' => 'recruiter', 'is_active' => 0])->assertRedirect();
        $this->assertSame('admin', $this->admin->fresh()->role);
        $this->assertTrue($this->admin->fresh()->is_active);
        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin))->assertForbidden();
        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $u))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $u->id]);
    }

    public function test_deactivated_user_is_signed_out_on_next_request(): void
    {
        $this->actingAs($this->recruiter)->get('/admin')->assertOk();
        $this->recruiter->forceFill(['is_active' => false])->save();
        $this->actingAs($this->recruiter)->get('/admin')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_profile_update_requires_current_password_to_change_password(): void
    {
        $this->actingAs($this->admin)->put(route('admin.profile.update'), ['name' => 'Admin Two', 'email' => $this->admin->email])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('Admin Two', $this->admin->fresh()->name);

        $this->actingAs($this->admin)->put(route('admin.profile.update'), ['name' => 'Admin Two', 'email' => $this->admin->email, 'current_password' => 'wrong', 'password' => 'newpassword9', 'password_confirmation' => 'newpassword9'])
            ->assertSessionHasErrors('current_password');
        $this->actingAs($this->admin)->put(route('admin.profile.update'), ['name' => 'Admin Two', 'email' => $this->admin->email, 'current_password' => 'password', 'password' => 'newpassword9', 'password_confirmation' => 'newpassword9'])
            ->assertSessionHasNoErrors();
        $this->post('/logout');
        $this->post('/login', ['email' => $this->admin->email, 'password' => 'newpassword9'])->assertRedirect('/admin');
    }

    public function test_logout_and_guest_redirects(): void
    {
        $this->actingAs($this->admin)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        foreach (['admin.dashboard', 'admin.jobs.index', 'admin.applications.pipeline', 'admin.settings.edit'] as $r) {
            $this->get(route($r))->assertRedirect('/login');
        }
        $this->actingAs($this->admin)->get('/login')->assertRedirect('/admin');
    }

    public function test_close_expired_jobs_command(): void
    {
        $expired = JobPosting::factory()->create(['closes_at' => today()->subDay()]);
        $open = JobPosting::factory()->create(['closes_at' => today()->addDay()]);
        $this->artisan('jobs:close-expired')->assertSuccessful();
        $this->assertSame('closed', $expired->fresh()->status);
        $this->assertSame('open', $open->fresh()->status);
    }
}
