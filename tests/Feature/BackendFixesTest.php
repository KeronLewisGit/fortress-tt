<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackendFixesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_updating_a_job_with_a_blank_publish_date_keeps_it_live(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = JobPosting::live()->first();
        $this->assertNotNull($job->published_at);

        $this->actingAs($admin)->put(route('admin.jobs.update', $job), [
            'title' => $job->title, 'location' => $job->location, 'employment_type' => $job->employment_type, 'summary' => $job->summary,
            'openings' => 2, 'status' => 'open', 'published_at' => '', 'closes_at' => today()->addMonth()->toDateString(),
        ])->assertRedirect();

        $this->assertTrue($job->fresh()->isLive());
        $this->assertNotNull($job->fresh()->published_at);
    }

    public function test_closing_date_in_the_past_is_rejected_and_expired_jobs_close(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = JobPosting::first();
        $this->actingAs($admin)->put(route('admin.jobs.update', $job), [
            'title' => $job->title, 'location' => $job->location, 'employment_type' => $job->employment_type, 'summary' => $job->summary,
            'openings' => 1, 'status' => 'open', 'closes_at' => today()->subDay()->toDateString(),
        ])->assertSessionHasErrors('closes_at');

        $job->forceFill(['closes_at' => today()->subDay()])->save();
        $this->assertFalse($job->fresh()->isLive());
        $this->artisan('jobs:close-expired')->assertSuccessful();
        $this->assertSame('closed', $job->fresh()->status);
    }

    public function test_applicant_search_matches_full_name_without_db_specific_sql(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Application::factory()->create(['first_name' => 'Kerry-Ann', 'last_name' => 'Joseph']);
        Application::factory()->create(['first_name' => 'Devon', 'last_name' => 'Ramkissoon']);

        $this->actingAs($admin)->get(route('admin.applications.index', ['q' => 'kerry joseph']))->assertOk()->assertSee('Kerry-Ann')->assertDontSee('Ramkissoon');
        $this->actingAs($admin)->get(route('admin.applications.index', ['q' => 'ramkis']))->assertOk()->assertSee('Ramkissoon')->assertDontSee('Kerry-Ann');
    }

    public function test_resume_name_is_sanitised_and_wrong_extension_rejected(): void
    {
        Mail::fake();
        Storage::fake('local');
        $job = JobPosting::live()->first();
        $base = ['first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com', 'phone' => '868-555-0000', 'area' => 'Arima',
            'date_of_birth' => now()->subYears(25)->toDateString(), 'nationality' => 'TT', 'licence_class' => 'none', 'years_experience' => 1, 'consent' => 1];

        $this->post(route('careers.apply.store', $job), $base + ['resume' => UploadedFile::fake()->create('cv.exe', 10, 'application/pdf')])
            ->assertSessionHasErrors('resume');

        $this->post(route('careers.apply.store', $job), $base + ['resume' => UploadedFile::fake()->create('..\\..\\my cv.pdf', 10, 'application/pdf')])
            ->assertSessionHasNoErrors();
        $this->assertSame('my cv.pdf', Application::first()->resume_name);
    }

    public function test_failed_confirmation_email_is_recorded_on_the_timeline(): void
    {
        Storage::fake('local');
        Mail::shouldReceive('to')->andReturnUsing(function () {
            $m = \Mockery::mock();
            $m->shouldReceive('send')->andThrow(new \RuntimeException('SMTP down'));

            return $m;
        });
        $job = JobPosting::live()->first();
        $this->post(route('careers.apply.store', $job), ['first_name' => 'A', 'last_name' => 'B', 'email' => 'fail@example.com', 'phone' => '868-555-0000', 'area' => 'Arima',
            'date_of_birth' => now()->subYears(25)->toDateString(), 'nationality' => 'TT', 'licence_class' => 'none', 'years_experience' => 1, 'consent' => 1])
            ->assertRedirect()->assertSessionHas('mail_failed', true);

        $this->assertDatabaseHas('application_notes', ['application_id' => Application::first()->id, 'type' => 'system']);
        $this->assertStringContainsString('SMTP down', Application::first()->notes()->where('body', 'like', 'Confirmation email%')->first()->body);
    }

    public function test_csv_export_starts_with_utf8_bom(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Application::factory()->create(['first_name' => 'José']);
        $csv = $this->actingAs($admin)->get(route('admin.applications.export'))->assertOk()->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('José', $csv);
    }

    public function test_admin_seeder_creates_admin_from_config_and_skips_without_it(): void
    {
        config(['fortress.admin' => ['name' => 'Administrator', 'email' => null, 'password' => null]]);
        $this->seed(AdminSeeder::class);
        $this->assertSame(0, User::count());

        config(['fortress.admin' => ['name' => 'Boss', 'email' => 'Boss@Fortress.tt', 'password' => 'supersecret1']]);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('users', ['email' => 'boss@fortress.tt', 'role' => 'admin', 'name' => 'Boss']);
        $this->post('/login', ['email' => 'boss@fortress.tt', 'password' => 'supersecret1'])->assertRedirect('/admin');
    }

    public function test_timezone_setting_is_read_from_env(): void
    {
        $this->assertSame(env('APP_TIMEZONE', 'UTC'), config('app.timezone'));
    }

    public function test_settings_without_explicit_rules_are_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('admin.settings.update'), ['settings' => [
            'business' => ['name' => 'Fortress Security Services Ltd', 'whatsapp' => ' 868-555-1234 ', 'phone' => '(868) 223-2223', 'address' => '8 Desaine Street'],
            'careers' => ['general_applications' => '1'],
        ]])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('868-555-1234', setting('business.whatsapp'));
        $this->assertSame('8 Desaine Street', setting('business.address'));
        $this->assertSame('1', setting('careers.general_applications'));
        $this->assertSame('0', setting('careers.auto_reply'));
    }

    public function test_interview_note_is_only_written_when_the_date_changes(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);
        $app = Application::factory()->create();
        $when = now()->addDays(3)->setTime(10, 0)->format('Y-m-d\TH:i');

        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['interview_at' => $when, 'rating' => 3]);
        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['interview_at' => $when, 'rating' => 4]);
        $this->assertSame(1, $app->notes()->where('type', 'interview')->count());

        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['interview_at' => '', 'rating' => 4]);
        $this->assertSame(2, $app->notes()->where('type', 'interview')->count());
        $this->assertNull($app->fresh()->interview_at);
    }

    public function test_stage_change_with_comment_keeps_the_transition(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);
        $app = Application::factory()->create();
        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['status' => 'screening', 'comment' => 'Passed screening call']);
        $note = $app->notes()->where('type', 'status')->first();
        $this->assertSame('Moved from New to Screening — Passed screening call', $note->body);
        $this->assertSame(['from' => 'new', 'to' => 'screening'], $note->meta);
    }

    public function test_open_job_with_past_closing_date_is_rejected_but_drafts_can_keep_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $base = ['title' => 'Gate Officer', 'location' => 'Couva', 'employment_type' => 'shift', 'summary' => 'Gatehouse duty.', 'openings' => 1];

        $this->actingAs($admin)->post(route('admin.jobs.store'), $base + ['status' => 'open', 'closes_at' => today()->subDays(3)->toDateString()])
            ->assertSessionHasErrors('closes_at');
        $this->actingAs($admin)->post(route('admin.jobs.store'), $base + ['status' => 'draft', 'closes_at' => today()->subDays(3)->toDateString()])
            ->assertSessionHasNoErrors();

        $job = JobPosting::where('title', 'Gate Officer')->first();
        $this->assertFalse($job->isExpired());
        $job->forceFill(['status' => 'open', 'published_at' => now()])->save();
        $this->assertTrue($job->fresh()->isExpired());
        $this->assertSame('expired', $job->fresh()->displayStatus());
        $this->assertFalse($job->fresh()->isLive());
    }
}
