<x-layouts.admin title="Dashboard">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <a href="{{ route('admin.applications.index', ['status' => 'new']) }}" class="stat border-l-4 border-l-amber-400 hover:border-steel-300"><p class="stat-value">{{ $newApps }}</p><p class="stat-label">New applicants to review</p></a>
        <a href="{{ route('admin.applications.pipeline') }}" class="stat hover:border-steel-300"><p class="stat-value">{{ $activeApps }}</p><p class="stat-label">In pipeline</p></a>
        <div class="stat"><p class="stat-value">{{ $weekApps }} <span class="text-base font-semibold {{ $weekApps >= $prevWeekApps ? 'text-green-600' : 'text-red-600' }}">{{ $weekApps >= $prevWeekApps ? '▲' : '▼' }} {{ abs($weekApps - $prevWeekApps) }}</span></p><p class="stat-label">Applications this week vs last</p></div>
        <div class="stat"><p class="stat-value">{{ $hiredMonth }}</p><p class="stat-label">Hired this month</p></div>
        <a href="{{ route('admin.jobs.index') }}" class="stat hover:border-steel-300"><p class="stat-value">{{ $openJobs }}</p><p class="stat-label">Live vacancies @if($draftJobs)· {{ $draftJobs }} draft @endif @if($closingSoon)· <span class="text-red-600">{{ $closingSoon }} closing soon</span>@endif</p></a>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="admin-card p-5 xl:col-span-2">
            <div class="flex items-center justify-between"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Applications – last 14 days</h2><span class="text-sm text-steel-500">{{ $chart->sum('count') }} total</span></div>
            <div x-data="bars(@js($chart))" class="mt-5 flex h-40 items-stretch gap-1.5">
                <template x-for="p in points"><div class="group relative flex flex-1 flex-col items-center justify-end"><div class="w-full rounded-t bg-blue-500 transition group-hover:bg-navy-900" :style="{ height: h(p) }" :title="p.label + ': ' + p.count"></div><span class="mt-1.5 hidden text-[10px] text-steel-500 sm:block" x-text="p.label.split(' ')[1]"></span></div></template>
            </div>
        </div>
        <div class="admin-card p-5">
            <h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Pipeline</h2>
            <ul class="mt-4 space-y-2.5">
                @foreach(\App\Models\Application::STAGES as $k => $label)
                    @php $c = $stageCounts[$k] ?? 0; $tot = max(1, $stageCounts->sum()); @endphp
                    <li><a href="{{ route('admin.applications.index', ['status' => $k]) }}" class="flex items-center gap-3 text-sm hover:text-blue-500"><x-stage-badge :status="$k" :label="$label" class="w-36 justify-center" /><span class="h-2 flex-1 rounded-full bg-steel-100"><span class="block h-2 rounded-full bg-blue-500" style="width: {{ round($c / $tot * 100) }}%"></span></span><span class="w-8 text-right font-semibold">{{ $c }}</span></a></li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="admin-card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-steel-200 px-5 py-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Recent applicants</h2><a href="{{ route('admin.applications.index') }}" class="text-sm font-semibold text-blue-500">All applicants →</a></div>
            <table class="admin-table"><thead><tr><th>Applicant</th><th>Role</th><th>Stage</th><th>Applied</th></tr></thead><tbody>
                @forelse($recent as $a)<tr><td><a href="{{ route('admin.applications.show', $a) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $a->full_name }}</a><p class="text-xs text-steel-500">{{ $a->area }} · {{ $a->licenceLabel() }}</p></td><td class="text-steel-600">{{ $a->job?->title ?? 'General' }}</td><td><x-stage-badge :status="$a->status" :label="$a->stageLabel()" /></td><td class="text-steel-500">{{ $a->created_at->diffForHumans(short: true) }}</td></tr>
                @empty<tr><td colspan="4" class="py-10 text-center text-steel-500">No applications yet. Share <a href="{{ route('careers') }}" class="text-blue-500">{{ route('careers') }}</a> on Facebook to start receiving them.</td></tr>@endforelse
            </tbody></table>
        </div>
        <div class="space-y-6">
            <div class="admin-card p-5"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Upcoming interviews</h2>
                <ul class="mt-3 divide-y divide-steel-100">@forelse($interviews as $a)<li class="py-2.5"><a href="{{ route('admin.applications.show', $a) }}" class="flex items-center justify-between text-sm hover:text-blue-500"><span><span class="font-semibold text-navy-900">{{ $a->full_name }}</span><br><span class="text-xs text-steel-500">{{ $a->job?->title ?? 'General' }}</span></span><span class="text-right text-xs font-semibold text-steel-600">{{ $a->interview_at->format('D j M') }}<br>{{ $a->interview_at->format('g:i a') }}</span></a></li>@empty<li class="py-6 text-center text-sm text-steel-500">None scheduled</li>@endforelse</ul></div>
            <div class="admin-card p-5"><div class="flex items-center justify-between"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Jobs by applicants</h2><a href="{{ route('admin.jobs.index') }}" class="text-sm font-semibold text-blue-500">Manage</a></div>
                <ul class="mt-3 divide-y divide-steel-100">@forelse($jobs as $j)<li class="flex items-center justify-between py-2.5 text-sm"><a href="{{ route('admin.applications.index', ['job' => $j->id]) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $j->title }}</a><span class="text-steel-600">{{ $j->applications_count }} @if($j->new_count)<span class="ml-1 rounded-full bg-amber-400 px-1.5 text-[11px] font-bold text-navy-950">{{ $j->new_count }} new</span>@endif</span></li>@empty<li class="py-6 text-center text-sm text-steel-500">No open jobs</li>@endforelse</ul></div>
            <div class="admin-card p-5"><div class="flex items-center justify-between"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Quote enquiries</h2><a href="{{ route('admin.enquiries.index') }}" class="text-sm font-semibold text-blue-500">{{ $newEnquiries }} new</a></div>
                <ul class="mt-3 divide-y divide-steel-100">@forelse($recentEnquiries as $e)<li class="py-2.5 text-sm"><a href="{{ route('admin.enquiries.show', $e) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $e->name }}</a> <span class="text-steel-500">· {{ $e->service ?: 'General' }}</span></li>@empty<li class="py-6 text-center text-sm text-steel-500">No enquiries yet</li>@endforelse</ul></div>
        </div>
    </div>
</x-layouts.admin>
