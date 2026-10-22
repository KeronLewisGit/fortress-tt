<x-layouts.public :title="$job->title.' – Careers'" :description="$job->summary">
    <section class="bg-navy-950 py-16 text-white"><div class="container-x">
        <a href="{{ route('careers') }}" class="inline-flex items-center gap-1.5 text-sm text-blue-300 hover:text-white">← All vacancies</a>
        <div class="mt-6 flex flex-wrap items-center gap-2"><span class="chip-blue">{{ $job->typeLabel() }}</span>@if($job->department)<span class="chip">{{ $job->department }}</span>@endif @if($job->is_featured)<span class="chip-amber">Urgent</span>@endif</div>
        <h1 class="h1 mt-4 max-w-4xl">{{ $job->title }}</h1>
        <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-sm text-blue-100/85">
            <span class="inline-flex items-center gap-1.5"><x-icon name="map-pin" class="h-4 w-4" />{{ $job->location }}</span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="users" class="h-4 w-4" />{{ $job->openings }} {{ Str::plural('opening', $job->openings) }}</span>
            @if($job->salary_note)<span class="inline-flex items-center gap-1.5"><x-icon name="trending" class="h-4 w-4" />{{ $job->salary_note }}</span>@endif
            @if($job->closes_at)<span class="inline-flex items-center gap-1.5"><x-icon name="clock" class="h-4 w-4" />Apply by {{ $job->closes_at->format('j F Y') }}</span>@endif
            <span class="inline-flex items-center gap-1.5"><x-icon name="calendar" class="h-4 w-4" />Posted {{ $job->published_at->diffForHumans() }}</span>
        </div>
        <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('careers.apply', $job) }}" class="btn-amber">Apply now</a><a href="#details" class="btn-outline-light">Read details</a></div>
    </div></section>

    <section id="details" class="py-16"><div class="container-x grid gap-12 lg:grid-cols-3">
        <div class="space-y-10 lg:col-span-2">
            <div class="prose-fss"><p class="text-lg font-medium text-navy-900">{{ $job->summary }}</p>@foreach($job->paragraphs() as $p)<p>{{ $p }}</p>@endforeach</div>
            @if($job->responsibilities)<div><h2 class="h3 text-navy-900">Key responsibilities</h2><ul class="mt-4 space-y-2.5">@foreach($job->responsibilities as $r)<li class="flex items-start gap-3 text-steel-700"><x-icon name="arrow" class="mt-1 h-4 w-4 shrink-0 text-blue-500" />{{ $r }}</li>@endforeach</ul></div>@endif
            @if($job->requirements)<div><h2 class="h3 text-navy-900">Minimum requirements</h2><ul class="mt-4 grid gap-3 sm:grid-cols-2">@foreach($job->requirements as $r)<li class="flex items-start gap-3 rounded-lg border border-steel-200 bg-steel-50 p-3.5 text-sm text-steel-700"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />{{ $r }}</li>@endforeach</ul></div>@endif
            @if($job->benefits)<div><h2 class="h3 text-navy-900">Benefits</h2><ul class="mt-4 flex flex-wrap gap-2">@foreach($job->benefits as $b)<li class="chip-green">{{ $b }}</li>@endforeach</ul></div>@endif
            <div class="rounded-xl border border-amber-400/40 bg-amber-400/10 p-5 text-sm text-steel-700"><p class="font-semibold text-navy-900">Before you apply</p><p class="mt-1">Have your Police Certificate of Character (issued within the last 6 months), national ID @if($job->requires_licence), driver's permit @endif @if($job->requires_precept), Precept and Firearm User's Licence @endif and a CV (PDF or Word) ready. In-house background checks are conducted on all applicants.</p></div>
        </div>
        <aside class="space-y-6">
            <div class="rounded-xl bg-navy-900 p-6 text-white lg:sticky lg:top-24"><h3 class="h3">Apply for this role</h3><p class="mt-2 text-sm text-blue-100/80">Takes about 5 minutes. You'll get a reference number by email.</p><a href="{{ route('careers.apply', $job) }}" class="btn-amber mt-5 w-full">Apply now</a>
                <div class="mt-6 border-t border-white/10 pt-5 text-sm text-blue-100/80"><p class="font-semibold text-white">Questions?</p><p class="mt-1">Recruitment: <a href="mailto:{{ setting('business.recruitment_email') }}" class="text-blue-300 hover:text-white">{{ setting('business.recruitment_email') }}</a></p><p><a href="{{ tel_href(setting('business.phone')) }}" class="text-blue-300 hover:text-white">{{ setting('business.phone') }}</a></p></div>
            </div>
            @if($related->isNotEmpty())<div class="card p-6"><h3 class="h3 text-navy-900">Other vacancies</h3><ul class="mt-3 divide-y divide-steel-100">@foreach($related as $r)<li><a href="{{ route('careers.show', $r) }}" class="block py-3 hover:text-blue-500"><p class="font-semibold text-navy-900">{{ $r->title }}</p><p class="text-xs text-steel-500">{{ $r->location }} · {{ $r->typeLabel() }}</p></a></li>@endforeach</ul></div>@endif
        </aside>
    </div></section>
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'JobPosting', 'title' => $job->title, 'description' => $job->summary, 'datePosted' => $job->published_at->toDateString(), 'validThrough' => $job->closes_at?->toDateString(), 'employmentType' => strtoupper($job->employment_type), 'hiringOrganization' => ['@type' => 'Organization', 'name' => fortress('name'), 'sameAs' => url('/')], 'jobLocation' => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job->location, 'addressCountry' => 'TT']]], JSON_UNESCAPED_SLASHES) !!}</script>
</x-layouts.public>
