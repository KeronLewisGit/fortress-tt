<?php

namespace Tests\Feature;

use App\Mail\ApplicantUpdate;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\InterviewSlot;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\ApplicantResetPassword;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicantPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Mail::fake();
    }

    public function test_registration_claims_existing_applications_by_email(): void
    {
        $job = JobPosting::live()->first();
        $existing = Application::factory()->create(['email' => 'kerry@example.com', 'job_posting_id' => $job->id]);
        $other = Application::factory()->create(['email' => 'someone@example.com']);

        $this->post(route('portal.register'), ['first_name' => 'Kerry', 'last_name' => 'Joseph', 'email' => 'Kerry@Example.com', 'phone' => '868-555-0100', 'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1'])
            ->assertRedirect(route('portal.dashboard'));
        $applicant = Applicant::where('email', 'kerry@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($applicant, 'applicant');
        $this->assertSame($applicant->id, $existing->fresh()->applicant_id);
        $this->assertNull($other->fresh()->applicant_id);

        $this->get(route('portal.dashboard'))->assertOk()->assertSee($job->title)->assertSee($existing->reference)->assertSee('Application sent');
        $this->post(route('portal.register'), ['first_name' => 'Dup', 'last_name' => 'X', 'email' => 'kerry@example.com', 'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1'])->assertRedirect(route('portal.dashboard')); // guest middleware
    }

    public function test_login_logout_and_guard_separation(): void
    {
        $applicant = Applicant::factory()->create(['email' => 'a@example.com']);
        $this->get(route('portal.dashboard'))->assertRedirect(route('portal.login'));
        $this->post(route('portal.login'), ['email' => 'a@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post(route('portal.login'), ['email' => 'A@example.com', 'password' => 'password'])->assertRedirect(route('portal.dashboard'));
        $this->assertAuthenticatedAs($applicant, 'applicant');
        $this->assertGuest('web');

        // An applicant is not staff.
        $this->get('/admin')->assertRedirect('/login');
        $this->post(route('portal.logout'))->assertRedirect('/');
        $this->assertGuest('applicant');

        // Staff are not applicants.
        $staff = User::factory()->create(['role' => 'admin']);
        $this->actingAs($staff, 'web')->get(route('portal.dashboard'))->assertRedirect(route('portal.login'));
    }

    public function test_applicant_only_sees_their_own_applications(): void
    {
        $me = Applicant::factory()->create();
        $mine = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email]);
        $theirs = Application::factory()->create();
        $this->actingAs($me, 'applicant')->get(route('portal.applications.show', $mine->reference))->assertOk()->assertSee($mine->reference);
        $this->actingAs($me, 'applicant')->get(route('portal.applications.show', $theirs->reference))->assertNotFound();
        $this->actingAs($me, 'applicant')->post(route('portal.applications.book', $theirs->reference), ['slot' => 1])->assertNotFound();
    }

    public function test_status_changes_notify_the_applicant_by_email_and_in_portal(): void
    {
        $me = Applicant::factory()->create();
        $app = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'job_posting_id' => JobPosting::first()->id]);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['status' => 'screening']);
        Mail::assertSent(ApplicantUpdate::class, fn ($m) => $m->hasTo($me->email) && str_contains($m->title, 'reviewed'));
        $this->assertDatabaseHas('applicant_notifications', ['applicant_id' => $me->id, 'application_id' => $app->id, 'type' => 'status']);

        // Screening → assessment is the same public status: no second notification.
        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['status' => 'assessment']);
        $this->assertSame(1, $me->notifications()->count());

        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['status' => 'rejected']);
        $this->assertSame(2, $me->notifications()->count());
        $this->assertSame(1, $me->unreadCount() ? 1 : 0);
        $this->actingAs($me, 'applicant')->get(route('portal.notifications'))->assertOk()->assertSee('Update on your application');
        $this->assertSame(0, $me->fresh()->unreadCount(), 'visiting the notifications page marks them read');
        $this->actingAs($me, 'applicant')->get(route('portal.applications.show', $app->reference))->assertSee('Not successful');
    }

    public function test_admin_can_set_interview_date_and_applicant_sees_it(): void
    {
        $me = Applicant::factory()->create();
        $app = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'status' => 'interview']);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $this->actingAs($recruiter)->patch(route('admin.applications.update', $app), ['interview_at' => '2027-03-04T10:30', 'interview_location' => 'Marabella branch'])->assertRedirect();
        Mail::assertSent(ApplicantUpdate::class, fn ($m) => str_contains($m->title, 'scheduled'));
        $this->assertDatabaseHas('applicant_notifications', ['applicant_id' => $me->id, 'type' => 'interview']);
        $this->actingAs($me, 'applicant')->get(route('portal.applications.show', $app->reference))->assertOk()->assertSee('Thursday 4 March 2027')->assertSee('10:30 am')->assertSee('Marabella branch')->assertDontSee('Book your interview');
    }

    public function test_applicant_can_book_an_open_slot_when_invited(): void
    {
        $me = Applicant::factory()->create();
        $app = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'status' => 'interview', 'job_posting_id' => JobPosting::first()->id]);
        $notInvited = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'status' => 'new']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.interviews.store'), ['date' => today()->addDays(3)->toDateString(), 'time' => '09:00', 'duration_minutes' => 30, 'location' => 'Head Office', 'capacity' => 1, 'repeat' => 3])->assertRedirect();
        $this->assertSame(3, InterviewSlot::count());
        $slot = InterviewSlot::orderBy('starts_at')->first();
        $this->assertSame('09:00', $slot->starts_at->format('H:i'));
        $this->assertSame('10:00', InterviewSlot::orderByDesc('starts_at')->first()->starts_at->format('H:i'));

        $page = $this->actingAs($me, 'applicant')->get(route('portal.applications.show', $app->reference));
        $page->assertOk()->assertSee('Book your interview')->assertSee('9:00 am');

        $this->actingAs($me, 'applicant')->post(route('portal.applications.book', $app->reference), ['slot' => $slot->id])->assertRedirect(route('portal.applications.show', $app->reference));
        $app->refresh();
        $this->assertTrue($app->interview_at->equalTo($slot->starts_at));
        $this->assertSame($slot->id, $app->interview_slot_id);
        $this->assertSame('Head Office', $app->interview_location);
        Mail::assertSent(ApplicantUpdate::class, fn ($m) => $m->title === 'Interview booked');
        $this->assertDatabaseHas('application_notes', ['application_id' => $app->id, 'type' => 'interview']);

        // Slot is now full; another applicant cannot take it, and the first applicant cannot rebook.
        $other = Applicant::factory()->create();
        $otherApp = Application::factory()->create(['applicant_id' => $other->id, 'email' => $other->email, 'status' => 'interview']);
        $this->actingAs($other, 'applicant')->post(route('portal.applications.book', $otherApp->reference), ['slot' => $slot->id])->assertSessionHasErrors('slot');
        $this->actingAs($me, 'applicant')->post(route('portal.applications.book', $app->reference), ['slot' => InterviewSlot::orderByDesc('starts_at')->first()->id])->assertForbidden();
        $this->actingAs($me, 'applicant')->post(route('portal.applications.book', $notInvited->reference), ['slot' => InterviewSlot::orderByDesc('starts_at')->first()->id])->assertForbidden();

        // Admin sees the booking and cannot delete a booked slot.
        $this->actingAs($admin)->get(route('admin.interviews.index'))->assertOk()->assertSee($app->full_name)->assertSee('1/1 booked');
        $this->actingAs($admin)->delete(route('admin.interviews.destroy', $slot))->assertSessionHasErrors('slot');
        $this->actingAs($admin)->delete(route('admin.interviews.destroy', InterviewSlot::orderByDesc('starts_at')->first()))->assertRedirect();
        $this->assertSame(2, InterviewSlot::count());
    }

    public function test_resume_upload_replace_download_and_attachment_to_active_applications(): void
    {
        Storage::fake('local');
        $me = Applicant::factory()->create();
        $active = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'status' => 'screening', 'resume_path' => null]);
        $closed = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'status' => 'rejected', 'resume_path' => null]);

        $this->actingAs($me, 'applicant')->post(route('portal.resume.store'), ['resume' => UploadedFile::fake()->create('my-cv.pdf', 50, 'application/pdf')])->assertRedirect();
        $me->refresh();
        Storage::disk('local')->assertExists($me->resume_path);
        $this->assertSame('my-cv.pdf', $me->resume_name);
        $this->assertSame($me->resume_path, $active->fresh()->resume_path, 'active applications without a CV get the new one');
        $this->assertNull($closed->fresh()->resume_path);

        $old = $me->resume_path;
        $this->actingAs($me, 'applicant')->post(route('portal.resume.store'), ['resume' => UploadedFile::fake()->create('cv-v2.docx', 50, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')])->assertRedirect();
        Storage::disk('local')->assertMissing($old);
        $this->assertSame('cv-v2.docx', $me->fresh()->resume_name);
        $this->actingAs($me, 'applicant')->get(route('portal.resume.download'))->assertOk();
        $this->actingAs($me, 'applicant')->post(route('portal.resume.store'), ['resume' => UploadedFile::fake()->create('virus.exe', 10)])->assertSessionHasErrors('resume');

        $this->actingAs($me, 'applicant')->delete(route('portal.resume.destroy'))->assertRedirect();
        $this->assertNull($me->fresh()->resume_path);
        $this->actingAs($me, 'applicant')->get(route('portal.resume.download'))->assertNotFound();
    }

    public function test_logged_in_applicant_application_is_linked_and_uses_profile_cv(): void
    {
        Storage::fake('local');
        $me = Applicant::factory()->create(['email' => 'me@example.com', 'resume_path' => 'resumes/applicants/x.pdf', 'resume_name' => 'profile.pdf']);
        $job = JobPosting::live()->first();
        $this->actingAs($me, 'applicant')->get(route('careers.apply', $job))->assertOk()->assertSee('Signed in as me@example.com');
        $this->actingAs($me, 'applicant')->post(route('careers.apply.store', $job), [
            'first_name' => 'Me', 'last_name' => 'Too', 'email' => 'me@example.com', 'phone' => '868-1', 'area' => 'Arima', 'date_of_birth' => now()->subYears(25)->toDateString(),
            'nationality' => 'Trinidad & Tobago', 'licence_class' => 'none', 'years_experience' => 1, 'consent' => 1,
        ])->assertRedirect();
        $app = Application::where('email', 'me@example.com')->firstOrFail();
        $this->assertSame($me->id, $app->applicant_id);
        $this->assertSame('resumes/applicants/x.pdf', $app->resume_path);
        $this->assertDatabaseHas('applicant_notifications', ['applicant_id' => $me->id, 'type' => 'system']);
        $this->get(route('careers.thanks', $app->reference))->assertSee('Track this application');

        // A guest applying with a registered email is linked too, and the thank-you page invites unregistered ones to sign up.
        $this->post(route('portal.logout'));
        $this->post(route('careers.apply.store', $job), ['first_name' => 'G', 'last_name' => 'Uest', 'email' => 'new@example.com', 'phone' => '868-2', 'area' => 'Arima', 'date_of_birth' => now()->subYears(25)->toDateString(), 'nationality' => 'TT', 'licence_class' => 'none', 'years_experience' => 0, 'consent' => 1]);
        $guestApp = Application::where('email', 'new@example.com')->firstOrFail();
        $this->get(route('careers.thanks', $guestApp->reference))->assertSee('Create my applicant account');
    }

    public function test_profile_update_and_password_reset_flow(): void
    {
        Notification::fake();
        $me = Applicant::factory()->create(['email' => 'reset@example.com']);
        $this->actingAs($me, 'applicant')->put(route('portal.profile.update'), ['first_name' => 'New', 'last_name' => 'Name', 'phone' => '868-9', 'current_password' => 'wrong', 'password' => 'another-pass-1', 'password_confirmation' => 'another-pass-1'])->assertSessionHasErrors('current_password');
        $this->actingAs($me, 'applicant')->put(route('portal.profile.update'), ['first_name' => 'New', 'last_name' => 'Name', 'phone' => '868-9'])->assertSessionHasNoErrors();
        $this->assertSame('New Name', $me->fresh()->full_name);

        $this->post(route('portal.logout'));
        $this->post(route('portal.password.email'), ['email' => 'reset@example.com'])->assertSessionHas('status');
        Notification::assertSentTo($me, ApplicantResetPassword::class, function ($n) use ($me) {
            $this->post(route('portal.password.update'), ['token' => $n->token, 'email' => $me->email, 'password' => 'brand-new-pass', 'password_confirmation' => 'brand-new-pass'])->assertRedirect(route('portal.login'));

            return true;
        });
        $this->post(route('portal.login'), ['email' => 'reset@example.com', 'password' => 'brand-new-pass'])->assertRedirect(route('portal.dashboard'));
    }

    public function test_admin_pages_show_portal_information(): void
    {
        $me = Applicant::factory()->create(['resume_path' => 'resumes/applicants/a.pdf', 'resume_name' => 'latest.pdf']);
        $app = Application::factory()->create(['applicant_id' => $me->id, 'email' => $me->email, 'status' => 'interview']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.applications.show', $app))->assertOk()->assertSee('Applicant portal')->assertSee('latest.pdf')->assertSee('self-book');
        $this->actingAs($admin)->get(route('admin.interviews.index'))->assertOk()->assertSee($app->full_name)->assertSee('has portal account');
        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
