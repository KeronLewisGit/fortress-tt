<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Enquiry;
use App\Models\JobPosting;
use App\Models\MessageTemplate;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Nested <form> elements are invalid HTML: browsers drop the inner tag, so a Delete form placed
 * inside an edit form turns the Save button into a DELETE request. Guard every admin page.
 */
class AdminFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_admin_page_renders_nested_forms(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $other = User::factory()->create(['role' => 'recruiter']);
        $application = Application::factory()->create(['job_posting_id' => JobPosting::first()->id]);
        $enquiry = Enquiry::create(['name' => 'Test', 'email' => 't@example.com', 'message' => 'Need a quote for two officers.']);

        $pages = [
            route('admin.dashboard'), route('admin.jobs.index'), route('admin.jobs.create'), route('admin.jobs.edit', JobPosting::first()),
            route('admin.applications.index'), route('admin.applications.pipeline'), route('admin.applications.show', $application),
            route('admin.enquiries.index'), route('admin.enquiries.show', $enquiry),
            route('admin.templates.index'), route('admin.templates.edit', MessageTemplate::first()),
            route('admin.services.index'), route('admin.services.edit', Service::first()),
            route('admin.settings.edit'), route('admin.users.index'), route('admin.users.edit', $other), route('admin.profile.edit'),
        ];

        foreach ($pages as $url) {
            $html = $this->actingAs($admin)->get($url)->assertOk()->getContent();
            $this->assertNoNestedForms($html, $url);
        }
    }

    public function test_saving_an_edit_form_does_not_delete_the_record(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $template = MessageTemplate::first();
        $service = Service::first();

        $this->actingAs($admin)->put(route('admin.templates.update', $template), $template->only('name', 'subject', 'body', 'set_status', 'sort_order'))->assertRedirect();
        $this->actingAs($admin)->put(route('admin.services.update', $service), ['name' => $service->name, 'icon' => $service->icon, 'summary' => $service->summary, 'sort_order' => 1, 'is_active' => 1])->assertRedirect();

        $this->assertDatabaseHas('message_templates', ['id' => $template->id]);
        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    private function assertNoNestedForms(string $html, string $url): void
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        foreach ($dom->getElementsByTagName('form') as $form) {
            for ($p = $form->parentNode; $p; $p = $p->parentNode) {
                $this->assertNotSame('form', $p->nodeName, "Nested <form> found on {$url}");
            }
        }
    }
}
