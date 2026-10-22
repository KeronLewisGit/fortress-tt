<?php

namespace Tests\Feature;

use App\Mail\ApplicationReceived;
use App\Mail\NewApplicationAlert;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\Setting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function payload(array $over = []): array
    {
        return array_merge([
            'first_name' => 'Kerry', 'last_name' => 'Joseph', 'email' => 'KERRY@example.com', 'phone' => '868-555-0100',
            'area' => 'Tunapuna', 'date_of_birth' => now()->subYears(30)->toDateString(), 'nationality' => 'Trinidad & Tobago',
            'licence_class' => '3', 'years_experience' => 4, 'has_police_certificate' => 1, 'consent' => 1, 'source' => 'facebook',
        ], $over);
    }

    public function test_applicant_can_apply_for_a_live_job_with_cv(): void
    {
        Mail::fake();
        Storage::fake('local');
        $job = JobPosting::live()->first();

        $response = $this->post(route('careers.apply.store', $job), $this->payload(['resume' => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf')]));

        $app = Application::first();
        $response->assertRedirect(route('careers.thanks', $app->reference));
        $this->assertSame('kerry@example.com', $app->email);
        $this->assertSame($job->id, $app->job_posting_id);
        $this->assertTrue($app->has_police_certificate);
        Storage::disk('local')->assertExists($app->resume_path);
        $this->assertDatabaseHas('application_notes', ['application_id' => $app->id, 'type' => 'system']);
        Mail::assertSent(ApplicationReceived::class);
        Mail::assertSent(NewApplicationAlert::class);
        $this->get(route('careers.thanks', $app->reference))->assertOk()->assertSee($app->reference);
    }

    public function test_duplicate_application_within_30_days_is_not_stored_twice(): void
    {
        Mail::fake();
        $job = JobPosting::live()->first();
        $this->post(route('careers.apply.store', $job), $this->payload());
        $this->post(route('careers.apply.store', $job), $this->payload())->assertSessionHas('duplicate');
        $this->assertSame(1, Application::count());
    }

    public function test_validation_rejects_minors_and_missing_consent(): void
    {
        $job = JobPosting::live()->first();
        $this->post(route('careers.apply.store', $job), $this->payload(['date_of_birth' => now()->subYears(16)->toDateString(), 'consent' => null]))
            ->assertSessionHasErrors(['date_of_birth', 'consent']);
        $this->assertSame(0, Application::count());
    }

    public function test_general_application_can_be_disabled(): void
    {
        $this->get(route('careers.general'))->assertOk();
        Setting::set('careers.general_applications', '0');
        $this->get(route('careers.general'))->assertNotFound();
    }
}
