<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Enquiry;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Local demo data only: php artisan db:seed --class=DemoSeeder */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DemoSeeder is for local development only and was skipped.');

            return;
        }

        User::firstOrCreate(['email' => 'admin@fortress.test'], ['name' => 'Fortress Admin', 'password' => 'password', 'role' => 'admin']);
        $recruiter = User::firstOrCreate(['email' => 'hr@fortress.test'], ['name' => 'Recruitment Officer', 'password' => 'password', 'role' => 'recruiter']);

        $jobs = JobPosting::all();
        $stages = ['new', 'new', 'new', 'screening', 'screening', 'interview', 'assessment', 'offer', 'hired', 'rejected'];
        foreach (range(1, 28) as $i) {
            $job = $jobs->random();
            $a = Application::factory()->create([
                'job_posting_id' => $job->id,
                'status' => $stages[array_rand($stages)],
                'assigned_to' => $i % 3 === 0 ? $recruiter->id : null,
                'rating' => $i % 4 === 0 ? rand(2, 5) : null,
                'created_at' => now()->subDays(rand(0, 40))->subHours(rand(0, 23)),
            ]);
            $a->notes()->create(['type' => 'system', 'body' => 'Application received via website', 'created_at' => $a->created_at]);
            if ($a->status === 'interview') {
                $a->update(['interview_at' => now()->addDays(rand(1, 10))->setTime(10, 0)]);
            }
        }
        Enquiry::create(['name' => 'Marissa Lee', 'company' => 'Westside Plaza', 'email' => 'marissa@example.com', 'phone' => '868-620-1234', 'service' => 'Protection Officers', 'location' => 'Westmoorings', 'message' => 'We need two baton officers for a retail plaza, 7pm-7am nightly. Please quote.']);
        Enquiry::create(['name' => 'Devon Ramkissoon', 'email' => 'devon@example.com', 'phone' => '868-355-9876', 'service' => 'CCTV, Access Control & Alarms', 'location' => 'Chaguanas', 'message' => '8-camera system for a hardware store with monitoring. What packages do you offer?', 'status' => 'contacted']);
    }
}
