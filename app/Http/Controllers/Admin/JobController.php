<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobPosting::withCount(['applications', 'applications as new_count' => fn ($q) => $q->where('status', 'new')])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->orderByRaw("case status when 'open' then 0 when 'draft' then 1 when 'paused' then 2 else 3 end")
            ->latest('updated_at')->paginate(20)->withQueryString();

        return view('admin.jobs.index', ['jobs' => $jobs, 'counts' => JobPosting::query()->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status')]);
    }

    public function create()
    {
        return view('admin.jobs.form', ['job' => new JobPosting(['benefits' => fortress('benefits'), 'requirements' => fortress('applicant_requirements'), 'location' => 'Trinidad & Tobago', 'openings' => 1])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = unique_slug(JobPosting::class, $data['title']);
        $data['created_by'] = $request->user()->id;
        if ($data['status'] === 'open' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        $job = JobPosting::create($data);

        return redirect()->route('admin.jobs.edit', $job)->with('status', 'Job posting created.'.($job->isLive() ? ' It is now live on the careers page.' : ''));
    }

    public function edit(JobPosting $job)
    {
        $job->loadCount('applications');

        return view('admin.jobs.form', ['job' => $job]);
    }

    public function update(Request $request, JobPosting $job)
    {
        $data = $this->validated($request);
        if (empty($data['published_at'])) {
            // Blank = keep what the job already has; publish now if it is being opened for the first time.
            $data['published_at'] = $job->published_at ?? ($data['status'] === 'open' ? now() : null);
        }
        if ($request->boolean('regenerate_slug')) {
            $data['slug'] = unique_slug(JobPosting::class, $data['title'], $job->id);
        }
        $job->update($data);

        return redirect()->route('admin.jobs.edit', $job)->with('status', 'Job posting saved.');
    }

    public function status(Request $request, JobPosting $job)
    {
        $status = $request->validate(['status' => ['required', Rule::in(array_keys(JobPosting::STATUSES))]])['status'];
        $job->update(['status' => $status, 'published_at' => $status === 'open' ? ($job->published_at ?? now()) : $job->published_at]);

        return back()->with('status', "“{$job->title}” is now ".strtolower(JobPosting::STATUSES[$status]).'.');
    }

    public function duplicate(JobPosting $job)
    {
        $copy = $job->replicate(['views', 'published_at']);
        $copy->title = $job->title.' (copy)';
        $copy->slug = unique_slug(JobPosting::class, $copy->title);
        $copy->status = 'draft';
        $copy->published_at = null;
        $copy->created_by = auth()->id();
        $copy->save();

        return redirect()->route('admin.jobs.edit', $copy)->with('status', 'Job duplicated as a draft.');
    }

    public function destroy(JobPosting $job)
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('status', 'Job posting deleted. Its applications were kept as general applications.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:80'],
            'location' => ['required', 'string', 'max:120'],
            'employment_type' => ['required', Rule::in(array_keys(JobPosting::TYPES))],
            'summary' => ['required', 'string', 'max:600'],
            'description' => ['nullable', 'string', 'max:10000'],
            'responsibilities' => ['nullable', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'benefits' => ['nullable', 'string', 'max:5000'],
            'salary_note' => ['nullable', 'string', 'max:120'],
            'openings' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', Rule::in(array_keys(JobPosting::STATUSES))],
            'is_featured' => ['nullable', 'boolean'],
            'requires_licence' => ['nullable', 'boolean'],
            'requires_precept' => ['nullable', 'boolean'],
            'closes_at' => ['nullable', 'date', Rule::when($request->input('status') === 'open', ['after_or_equal:today'])],
            'published_at' => ['nullable', 'date'],
        ]);
        foreach (['responsibilities', 'requirements', 'benefits'] as $k) {
            $data[$k] = lines_to_array($data[$k] ?? null);
        }
        foreach (['is_featured', 'requires_licence', 'requires_precept'] as $k) {
            $data[$k] = $request->boolean($k);
        }

        return $data;
    }
}
