<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/about', '/services', '/training', '/careers', '/contact', '/privacy', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/services/'.Service::first()->slug)->assertOk()->assertSee(Service::first()->name);
        $this->get('/track')->assertRedirect();
    }

    public function test_live_job_is_visible_and_draft_is_not(): void
    {
        $live = JobPosting::live()->first();
        $this->get(route('careers.show', $live))->assertOk()->assertSee($live->title);

        $draft = JobPosting::factory()->draft()->create();
        $this->get(route('careers.show', $draft))->assertNotFound();
        $this->get('/careers')->assertDontSee($draft->title);
    }

    public function test_contact_form_stores_enquiry(): void
    {
        $this->post('/contact', ['name' => 'Test Client', 'email' => 'client@example.com', 'service' => 'Protection Officers', 'message' => 'Need two officers nightly at a plaza.'])
            ->assertRedirect(route('contact'))->assertSessionHas('status');
        $this->assertDatabaseHas('enquiries', ['email' => 'client@example.com', 'status' => 'new']);
    }
}
