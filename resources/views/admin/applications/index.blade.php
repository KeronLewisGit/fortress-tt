<x-layouts.admin title="Applicants" wide>
    <x-slot:actions><a href="{{ route('admin.applications.pipeline', request()->query()) }}" class="admin-btn-secondary"><x-icon name="columns" class="h-4 w-4" />Pipeline</a><a href="{{ route('admin.applications.export', request()->query()) }}" class="admin-btn-secondary"><x-icon name="download" class="h-4 w-4" />Export CSV</a></x-slot:actions>
    <div class="mb-5 flex flex-wrap gap-2">
        <a href="{{ route('admin.applications.index', request()->except('status', 'page')) }}" class="rounded-full px-3.5 py-1.5 text-sm font-semibold {{ ! request('status') ? 'bg-navy-900 text-white' : 'bg-white text-steel-600 ring-1 ring-steel-200' }}">All <span class="opacity-60">{{ $stageCounts->sum() }}</span></a>
        @foreach(\App\Models\Application::STAGES as $k => $label)<a href="{{ route('admin.applications.index', array_merge(request()->except('page'), ['status' => $k])) }}" class="rounded-full px-3.5 py-1.5 text-sm font-semibold {{ request('status') === $k ? 'bg-navy-900 text-white' : 'bg-white text-steel-600 ring-1 ring-steel-200 hover:bg-steel-50' }}">{{ $label }} <span class="opacity-60">{{ $stageCounts[$k] ?? 0 }}</span></a>@endforeach
    </div>
    <form class="admin-card mb-5 grid grid-cols-1 gap-3 p-4 md:grid-cols-6">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, email, phone, reference, area" class="field md:col-span-2">
        <select name="job" class="field"><option value="">All jobs</option><option value="general" @selected(request('job') === 'general')>General applications</option>@foreach($jobs as $j)<option value="{{ $j->id }}" @selected(request('job') == $j->id)>{{ $j->title }}{{ $j->status !== 'open' ? ' ('.$j->status.')' : '' }}</option>@endforeach</select>
        <select name="assigned" class="field"><option value="">Any recruiter</option><option value="me" @selected(request('assigned') === 'me')>Assigned to me</option><option value="none" @selected(request('assigned') === 'none')>Unassigned</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected(request('assigned') == $u->id)>{{ $u->name }}</option>@endforeach</select>
        <select name="licence" class="field"><option value="">Any permit</option>@foreach(\App\Models\Application::LICENCES as $k => $v)<option value="{{ $k }}" @selected(request('licence') === $k)>{{ $v }}</option>@endforeach</select>
        <div class="flex items-center gap-3 text-sm"><label class="flex items-center gap-1.5"><input type="checkbox" name="police" value="1" @checked(request('police')) class="check">Police cert.</label><label class="flex items-center gap-1.5"><input type="checkbox" name="precept" value="1" @checked(request('precept')) class="check">Precept</label></div>
        <div class="flex gap-2 md:col-span-6"><select name="rating" class="field w-40"><option value="">Any rating</option>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }}★ and up</option>@endfor</select><button class="admin-btn-primary">Filter</button><a href="{{ route('admin.applications.index') }}" class="admin-btn-secondary">Reset</a><span class="ml-auto self-center text-sm text-steel-500">{{ $applications->total() }} {{ Str::plural('result', $applications->total()) }}</span></div>
    </form>
    <div class="admin-card overflow-x-auto">
        <table class="admin-table"><thead><tr><th>Applicant</th><th>Role</th><th>Profile</th><th>Stage</th><th>Rating</th><th>Recruiter</th><th>Applied</th></tr></thead><tbody>
        @forelse($applications as $a)
            <tr class="{{ $a->status === 'new' ? 'bg-amber-400/5' : '' }}">
                <td><a href="{{ route('admin.applications.show', $a) }}" class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy-900 text-xs font-bold text-white">{{ $a->initials() }}</span><span><span class="font-semibold text-navy-900 hover:text-blue-500">{{ $a->full_name }}</span><br><span class="text-xs text-steel-500">{{ $a->reference }} · {{ $a->phone }}</span></span></a></td>
                <td class="text-steel-600">{{ $a->job?->title ?? 'General' }}</td>
                <td class="text-xs text-steel-600"><span class="font-semibold text-steel-800">{{ $a->area }}</span>@if($a->age()) · {{ $a->age() }} yrs @endif<br>{{ $a->licenceLabel() }} · {{ $a->years_experience }} yrs exp.@if($a->has_police_certificate) · <span class="text-green-700">PCC</span>@endif @if($a->has_precept)· <span class="text-blue-500">Precept</span>@endif @if($a->resume_path)· CV @endif</td>
                <td><x-stage-badge :status="$a->status" :label="$a->stageLabel()" />@if($a->interview_at && $a->status === 'interview')<p class="mt-1 text-xs text-steel-500">{{ $a->interview_at->format('j M, g:i a') }}</p>@endif</td>
                <td><x-stars :rating="$a->rating" /></td>
                <td class="text-steel-600">{{ $a->assignee?->name ?? '—' }}</td>
                <td class="text-steel-500 whitespace-nowrap">{{ $a->created_at->format('j M') }}<br><span class="text-xs">{{ $a->created_at->diffForHumans(short: true) }}</span></td>
            </tr>
        @empty<tr><td colspan="7" class="py-12 text-center text-steel-500">No applicants match these filters.</td></tr>@endforelse
        </tbody></table>
    </div>
    <div class="mt-5">{{ $applications->links() }}</div>
</x-layouts.admin>
