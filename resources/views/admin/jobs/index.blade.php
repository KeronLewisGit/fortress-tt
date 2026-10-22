<x-layouts.admin title="Job postings">
    <div class="mb-5 flex flex-wrap items-center gap-2">
        @foreach(['' => 'All', 'open' => 'Open', 'draft' => 'Draft', 'paused' => 'Paused', 'closed' => 'Closed'] as $k => $label)
            <a href="{{ route('admin.jobs.index', array_filter(['status' => $k, 'q' => request('q')])) }}" class="rounded-full px-3.5 py-1.5 text-sm font-semibold {{ request('status', '') === $k ? 'bg-navy-900 text-white' : 'bg-white text-steel-600 ring-1 ring-steel-200 hover:bg-steel-50' }}">{{ $label }} <span class="opacity-60">{{ $k === '' ? $counts->sum() : ($counts[$k] ?? 0) }}</span></a>
        @endforeach
        <form class="ml-auto"><input type="search" name="q" value="{{ request('q') }}" placeholder="Search titles" class="field w-56 !py-1.5"><input type="hidden" name="status" value="{{ request('status') }}"></form>
    </div>
    <div class="admin-card overflow-x-auto">
        <table class="admin-table"><thead><tr><th>Title</th><th>Location</th><th>Status</th><th>Applicants</th><th>Closes</th><th>Views</th><th></th></tr></thead><tbody>
        @forelse($jobs as $job)
            <tr>
                <td><a href="{{ route('admin.jobs.edit', $job) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $job->title }}</a><p class="text-xs text-steel-500">{{ $job->department }} · {{ $job->typeLabel() }} @if($job->is_featured)· <span class="text-amber-500">Urgent</span>@endif</p></td>
                <td class="text-steel-600">{{ $job->location }}</td>
                <td><x-stage-badge :status="$job->status" :label="\App\Models\JobPosting::STATUSES[$job->status]" /></td>
                <td><a href="{{ route('admin.applications.index', ['job' => $job->id]) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $job->applications_count }}</a>@if($job->new_count)<span class="ml-1 rounded-full bg-amber-400 px-1.5 text-[11px] font-bold text-navy-950">{{ $job->new_count }} new</span>@endif</td>
                <td class="text-steel-600">{{ $job->closes_at?->format('j M Y') ?? '—' }}</td>
                <td class="text-steel-600">{{ $job->views }}</td>
                <td class="text-right whitespace-nowrap">
                    @if($job->isLive())<a href="{{ route('careers.show', $job) }}" target="_blank" class="admin-btn-secondary !px-2.5" title="View on site"><x-icon name="external" class="h-4 w-4" /></a>@endif
                    <form method="POST" action="{{ route('admin.jobs.status', $job) }}" class="inline">@csrf @method('PATCH')
                        @if($job->status === 'open')<button name="status" value="paused" class="admin-btn-secondary">Pause</button><button name="status" value="closed" class="admin-btn-secondary">Close</button>
                        @else<button name="status" value="open" class="admin-btn-blue">Publish</button>@endif
                    </form>
                    <form method="POST" action="{{ route('admin.jobs.duplicate', $job) }}" class="inline">@csrf<button class="admin-btn-secondary">Duplicate</button></form>
                </td>
            </tr>
        @empty<tr><td colspan="7" class="py-12 text-center text-steel-500">No job postings yet. <a href="{{ route('admin.jobs.create') }}" class="font-semibold text-blue-500">Post your first job</a>.</td></tr>@endforelse
        </tbody></table>
    </div>
    <div class="mt-4">{{ $jobs->links() }}</div>
</x-layouts.admin>
