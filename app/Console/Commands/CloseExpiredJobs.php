<?php

namespace App\Console\Commands;

use App\Models\JobPosting;
use Illuminate\Console\Command;

class CloseExpiredJobs extends Command
{
    protected $signature = 'jobs:close-expired';

    protected $description = 'Close open job postings whose closing date has passed';

    public function handle(): int
    {
        $n = JobPosting::where('status', 'open')->whereNotNull('closes_at')->where('closes_at', '<', today()->toDateString())->update(['status' => 'closed']);
        $this->info("Closed $n expired job postings");

        return self::SUCCESS;
    }
}
