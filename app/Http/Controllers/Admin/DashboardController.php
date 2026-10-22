<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enquiry;
use App\Models\JobPosting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stageCounts = Application::query()->select('status', DB::raw('count(*) as c'))->groupBy('status')->pluck('c', 'status');

        $days = collect(range(13, 0))->map(fn ($d) => today()->subDays($d));
        $perDay = Application::query()->where('created_at', '>=', today()->subDays(13))
            ->get(['created_at'])->groupBy(fn ($a) => $a->created_at->toDateString())->map->count();

        return view('admin.dashboard', [
            'openJobs' => JobPosting::live()->count(),
            'draftJobs' => JobPosting::where('status', 'draft')->count(),
            'newApps' => $stageCounts['new'] ?? 0,
            'activeApps' => Application::active()->count(),
            'hiredMonth' => Application::where('status', 'hired')->where('status_changed_at', '>=', now()->startOfMonth())->count(),
            'weekApps' => Application::where('created_at', '>=', now()->subDays(7))->count(),
            'prevWeekApps' => Application::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count(),
            'newEnquiries' => Enquiry::where('status', 'new')->count(),
            'stageCounts' => $stageCounts,
            'chart' => $days->map(fn ($d) => ['label' => $d->format('D j'), 'count' => $perDay[$d->toDateString()] ?? 0])->values(),
            'recent' => Application::with('job')->latest()->take(8)->get(),
            'interviews' => Application::with('job')->whereNotNull('interview_at')->where('interview_at', '>=', now()->startOfDay())->orderBy('interview_at')->take(6)->get(),
            'jobs' => JobPosting::withCount(['applications', 'applications as new_count' => fn ($q) => $q->where('status', 'new')])->whereIn('status', ['open', 'paused'])->orderByDesc('applications_count')->take(6)->get(),
            'closingSoon' => JobPosting::live()->whereNotNull('closes_at')->where('closes_at', '<=', today()->addDays(7))->count(),
            'recentEnquiries' => Enquiry::latest()->take(4)->get(),
        ]);
    }
}
