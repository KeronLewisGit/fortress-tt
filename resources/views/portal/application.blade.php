<x-layouts.portal :title="$application->job?->title ?? 'General application'">
    <a href="{{ route('portal.dashboard') }}" class="text-sm font-semibold text-blue-500">← All applications</a>

    @php $steps = ['sent' => 'Sent', 'review' => 'In review', 'interview' => 'Interview', 'accepted' => 'Decision']; $ps = $application->publicStatus(); $order = array_keys($steps); $idx = $ps === 'rejected' || $ps === 'withdrawn' ? 3 : array_search($ps, $order); @endphp
    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-steel-500">Reference {{ $application->reference }}</p><p class="mt-1 text-sm text-steel-600">Applied {{ $application->created_at->format('l j F Y') }} · {{ $application->job?->location ?? 'Trinidad & Tobago' }}</p></div><x-public-status :application="$application" class="!text-sm !px-3 !py-1" /></div>
        <ol class="mt-6 grid grid-cols-4 gap-2">
            @foreach($steps as $k => $label)
                @php $i = $loop->index; $done = $i < $idx; $current = $i === $idx; $final = $i === 3; @endphp
                <li class="relative">
                    <div class="h-1.5 rounded-full {{ $done || $current ? ($final && in_array($ps, ['rejected', 'withdrawn']) ? 'bg-steel-400' : ($final ? 'bg-green-600' : 'bg-blue-500')) : 'bg-steel-200' }}"></div>
                    <p class="mt-2 text-xs font-semibold {{ $done || $current ? 'text-navy-900' : 'text-steel-400' }}">{{ $final && in_array($ps, ['rejected', 'withdrawn']) ? ucfirst($ps === 'rejected' ? 'Not successful' : 'Withdrawn') : $label }}</p>
                </li>
            @endforeach
        </ol>
        <p class="mt-5 text-steel-700">
            @switch($ps)
                @case('sent') We have your application and will review it shortly. Recruiters usually make screening calls within five business days. @break
                @case('review') Your application is with our recruitment team. We may call you to confirm details or request documents. @break
                @case('interview') You have progressed to the interview stage. @if($application->interview_at) Your interview details are below. @else Choose an interview time below. @endif @break
                @case('accepted') Congratulations – your application has been accepted. Our team will contact you about onboarding, documents and your training start date. @break
                @case('rejected') Thank you for your interest. We will not be progressing this application, but we keep applications on file for six months and you are welcome to apply again. @break
                @default This application has been withdrawn.
            @endswitch
        </p>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if($application->interview_at)
                <div class="rounded-xl bg-navy-900 p-6 text-white"><p class="eyebrow-light">Your interview</p><p class="mt-2 font-display text-3xl font-bold">{{ $application->interview_at->format('l j F Y') }}</p><p class="font-display text-2xl font-bold text-blue-300">{{ $application->interview_at->format('g:i a') }}</p><p class="mt-3 text-sm text-blue-100/85"><x-icon name="map-pin" class="mr-1 inline h-4 w-4" />{{ $application->interview_location ?: 'Head Office – 8 Desaine Street, St. Augustine' }}</p>
                    <div class="mt-5 rounded-lg bg-white/10 p-4 text-sm text-blue-100/85"><p class="font-semibold text-white">Please bring</p><ul class="mt-1 list-disc pl-5"><li>Two forms of national identification</li><li>Police Certificate of Character (within 6 months)</li><li>Driver's permit, precept or firearm licence if you hold one</li><li>Original certificates and a passport-size photo</li></ul></div>
                    <p class="mt-4 text-xs text-blue-100/70">Need to reschedule? Call {{ setting('business.phone') }} or email {{ setting('business.recruitment_email') }} quoting {{ $application->reference }}.</p></div>
            @elseif($application->canBookInterview())
                <div class="card p-6"><h2 class="h3 text-navy-900">Book your interview</h2><p class="mt-1 text-sm text-steel-600">Choose a time that suits you. Interviews take about 30 minutes.</p>
                    @if($slots->isEmpty())<p class="mt-4 rounded-lg bg-steel-50 p-4 text-sm text-steel-600">No open times are published right now. We will notify you as soon as new interview times are added, or call {{ setting('business.phone') }}.</p>
                    @else
                    <form method="POST" action="{{ route('portal.applications.book', $application->reference) }}" class="mt-4">@csrf
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($slots->groupBy(fn ($s) => $s->starts_at->toDateString()) as $day => $daySlots)
                                <p class="sm:col-span-2 mt-2 text-xs font-bold uppercase tracking-[0.14em] text-steel-500">{{ $daySlots->first()->starts_at->format('l j F') }}</p>
                                @foreach($daySlots as $s)
                                    <label class="flex cursor-pointer items-center justify-between gap-3 rounded-lg border border-steel-200 p-3 text-sm has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50"><span class="flex items-center gap-3"><input type="radio" name="slot" value="{{ $s->id }}" required class="check">{{ $s->starts_at->format('g:i a') }} – {{ $s->endsAt()->format('g:i a') }}</span><span class="text-xs text-steel-500">{{ Str::limit($s->location, 28) }}</span></label>
                                @endforeach
                            @endforeach
                        </div>
                        <button class="btn-amber mt-5">Confirm interview time</button>
                    </form>
                    @endif
                </div>
            @endif

            <div class="card p-6"><h2 class="h3 text-navy-900">Progress</h2>
                <ol class="mt-4 space-y-4 border-l-2 border-steel-200 pl-5">
                    @forelse($timeline as $n)
                        <li class="relative"><span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full ring-4 ring-white {{ ['status' => 'bg-blue-500', 'interview' => 'bg-amber-400', 'email' => 'bg-violet-500'][$n->type] ?? 'bg-steel-400' }}"></span>
                            <p class="text-xs text-steel-500">{{ $n->created_at->format('j M Y, g:i a') }}</p>
                            @if($n->type === 'status') @php $to = $n->meta['to'] ?? null; @endphp<p class="text-sm font-semibold text-navy-900">{{ $to ? (\App\Models\Application::PUBLIC_STATUSES[$to][1] ?? 'Updated') : 'Updated' }}</p>
                            @elseif($n->type === 'email')<p class="text-sm font-semibold text-navy-900">✉ {{ $n->meta['subject'] ?? 'Message from Fortress Recruitment' }}</p><details class="mt-1 text-sm text-steel-600"><summary class="cursor-pointer text-xs text-blue-500">Read message</summary><p class="mt-2 whitespace-pre-line rounded bg-steel-50 p-3">{{ $n->body }}</p></details>
                            @else<p class="text-sm text-steel-800">{{ $n->body }}</p>@endif
                        </li>
                    @empty<li class="text-sm text-steel-500">No updates yet.</li>@endforelse
                </ol>
            </div>
        </div>
        <aside class="space-y-6">
            @if($application->job)<div class="card p-5"><p class="eyebrow">Position</p><h3 class="h3 mt-1 text-navy-900">{{ $application->job->title }}</h3><p class="mt-1 text-sm text-steel-500">{{ $application->job->location }} · {{ $application->job->typeLabel() }}</p>@if($application->job->isLive())<a href="{{ route('careers.show', $application->job) }}" class="mt-3 inline-block text-sm font-semibold text-blue-500">View posting →</a>@else<p class="mt-3 text-xs text-steel-500">This vacancy is no longer advertised.</p>@endif</div>@endif
            <div class="card p-5 text-sm"><p class="font-semibold text-navy-900">CV on this application</p>@if($application->resume_path)<p class="mt-1 flex items-center gap-2 text-steel-700"><x-icon name="file" class="h-4 w-4 text-blue-500" />{{ $application->resume_name }}</p>@else<p class="mt-1 text-steel-600">No CV attached.</p>@endif<p class="mt-2 text-xs text-steel-500">Upload or update your CV under <a href="{{ route('portal.profile') }}" class="text-blue-500">My CV &amp; profile</a>; it is shared with recruiters on your active applications.</p></div>
            <div class="card p-5 text-sm text-steel-600"><p class="font-semibold text-navy-900">Your details</p><p class="mt-1">{{ $application->full_name }}<br>{{ $application->email }}<br>{{ $application->phone }}<br>{{ $application->area }}</p></div>
        </aside>
    </div>
</x-layouts.portal>
