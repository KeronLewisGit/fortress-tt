<x-layouts.portal :title="'Welcome, '.$applicant->first_name">

    @if($needsAction->isNotEmpty())
        <div class="mb-6 rounded-xl border border-amber-400/50 bg-amber-400/10 p-5">
            <p class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Action needed: book your interview</p>
            <ul class="mt-2 space-y-1 text-sm text-steel-700">@foreach($needsAction as $a)<li>You have been invited to interview for <strong>{{ $a->job?->title ?? 'a position' }}</strong>. <a href="{{ route('portal.applications.show', $a->reference) }}" class="font-semibold text-blue-500">Choose a time →</a></li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-steel-200 px-5 py-4"><h2 class="h3 text-navy-900">My applications</h2><a href="{{ route('careers') }}" class="text-sm font-semibold text-blue-500">Browse vacancies →</a></div>
                @forelse($applications as $a)
                    <a href="{{ route('portal.applications.show', $a->reference) }}" class="flex flex-col gap-3 border-b border-steel-100 px-5 py-4 transition hover:bg-steel-50 sm:flex-row sm:items-center sm:justify-between last:border-b-0">
                        <div class="min-w-0"><p class="truncate font-semibold text-navy-900">{{ $a->job?->title ?? 'General application' }}</p><p class="text-xs text-steel-500">{{ $a->reference }} · Applied {{ $a->created_at->format('j M Y') }}@if($a->interview_at) · Interview {{ $a->interview_at->format('D j M, g:i a') }}@endif</p></div>
                        <div class="flex items-center gap-3"><x-public-status :application="$a" />@if($a->canBookInterview())<span class="chip-amber">Book interview</span>@endif<x-icon name="arrow" class="h-4 w-4 text-steel-400" /></div>
                    </a>
                @empty
                    <div class="px-5 py-12 text-center text-steel-600"><p>No applications linked to <strong>{{ $applicant->email }}</strong> yet.</p><a href="{{ route('careers') }}" class="btn-primary mt-4">See open roles</a></div>
                @endforelse
            </div>

            @if($jobs->isNotEmpty())
            <div><h2 class="h3 text-navy-900">Other roles you may like</h2><div class="mt-4 grid gap-4 md:grid-cols-3">@foreach($jobs as $job)<x-job-card :job="$job" />@endforeach</div></div>
            @endif
        </div>

        <aside class="space-y-6">
            @if($upcoming->isNotEmpty())
            <div class="rounded-xl bg-navy-900 p-5 text-white"><p class="eyebrow-light">Upcoming interview</p>
                @foreach($upcoming->take(2) as $a)<div class="mt-3"><p class="font-display text-2xl font-bold">{{ $a->interview_at->format('D j M · g:i a') }}</p><p class="text-sm text-blue-100/80">{{ $a->job?->title ?? 'General application' }}</p><p class="mt-1 text-xs text-blue-100/70">{{ $a->interview_location ?: 'Head Office – 8 Desaine Street, St. Augustine' }}</p></div>@endforeach
            </div>
            @endif
            <div class="card p-5">
                <div class="flex items-center justify-between"><h3 class="h3 text-navy-900">My CV</h3><a href="{{ route('portal.profile') }}" class="text-sm font-semibold text-blue-500">Manage</a></div>
                @if($applicant->resume_path)<p class="mt-2 flex items-center gap-2 text-sm text-steel-700"><x-icon name="file" class="h-5 w-5 text-blue-500" /><span class="truncate">{{ $applicant->resume_name }}</span></p><p class="mt-1 text-xs text-steel-500">Updated {{ $applicant->resume_updated_at?->diffForHumans() }}</p>
                @else<p class="mt-2 text-sm text-steel-600">No CV on file. Upload one so recruiters can review it with every application.</p><a href="{{ route('portal.profile') }}" class="btn-outline mt-3 w-full !py-2">Upload CV</a>@endif
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between"><h3 class="h3 text-navy-900">Notifications</h3><a href="{{ route('portal.notifications') }}" class="text-sm font-semibold text-blue-500">{{ $unread ? $unread.' unread' : 'All' }}</a></div>
                <ul class="mt-3 divide-y divide-steel-100">@forelse($notifications as $n)<li class="py-2.5"><a href="{{ $n->link ?? route('portal.notifications') }}" class="block"><p class="text-sm font-semibold text-navy-900 {{ $n->read_at ? '' : 'flex items-center gap-2' }}">@unless($n->read_at)<span class="h-2 w-2 rounded-full bg-amber-400"></span>@endunless{{ $n->title }}</p><p class="text-xs text-steel-500">{{ $n->created_at->diffForHumans() }}</p></a></li>@empty<li class="py-4 text-sm text-steel-500">Nothing yet – we will notify you here and by email at every step.</li>@endforelse</ul>
            </div>
            <div class="card p-5 text-sm text-steel-600"><p class="font-semibold text-navy-900">Need help?</p><p class="mt-1">Recruitment: <a href="mailto:{{ setting('business.recruitment_email') }}" class="text-blue-500">{{ setting('business.recruitment_email') }}</a><br><a href="{{ tel_href(setting('business.phone')) }}" class="text-blue-500">{{ setting('business.phone') }}</a></p></div>
        </aside>
    </div>
</x-layouts.portal>
