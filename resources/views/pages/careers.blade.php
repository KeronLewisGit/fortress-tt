<x-layouts.public title="Careers" description="Join Fortress Security Services. Current vacancies for security officers, drivers, CIT crew and more across Trinidad & Tobago. Permanent employment, in-house training, health plan and pension.">
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <div class="absolute inset-0 grid-bg"></div><div class="absolute inset-0 glow"></div>
        <div class="container-x relative grid gap-10 py-20 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <p class="eyebrow-light" data-reveal>Careers</p><h1 class="h1 mt-4 max-w-4xl" data-reveal style="--d:80ms">We're hiring. <span class="text-amber-400">Join the Fortress team.</span></h1>
                <p class="mt-6 max-w-2xl text-lg text-blue-100/85" data-reveal style="--d:160ms">{{ setting('careers.intro') }}</p>
                <div class="mt-8 flex flex-wrap gap-3" data-reveal style="--d:240ms"><a href="#vacancies" class="btn-amber shine">View {{ $jobs->count() }} open {{ Str::plural('role', $jobs->count()) }}</a>@if($generalOpen)<a href="{{ route('careers.general') }}" class="btn-outline-light">General application</a>@endif</div>
            </div>
            <div class="lg:col-span-5" data-reveal="right" style="--d:200ms">
                <div class="grid grid-cols-5 gap-3">
                    <div class="reveal-clip img-zoom col-span-3 aspect-[3/4] overflow-hidden rounded-xl frame-corner"><x-photo name="hero-officer" alt="Fortress security officer smiling in uniform" sizes="(min-width: 1024px) 25vw, 60vw" eager /></div>
                    <div class="col-span-2 grid gap-3"><div class="reveal-clip img-zoom aspect-square overflow-hidden rounded-xl" style="--d:200ms"><x-photo name="team-event" alt="Fortress officer team" sizes="(min-width: 1024px) 15vw, 40vw" /></div><div class="reveal-clip img-zoom aspect-square overflow-hidden rounded-xl" style="--d:350ms"><x-photo name="k9" alt="Fortress K9 German shepherd" sizes="(min-width: 1024px) 15vw, 40vw" /></div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-steel-200 bg-steel-50"><div class="container-x grid gap-4 py-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach(array_slice(fortress('benefits'), 0, 4) as $b)<p class="flex items-center gap-2 text-sm font-semibold text-navy-900"><x-icon name="check" class="h-4 w-4 text-blue-500" />{{ $b }}</p>@endforeach
    </div></section>

    <section id="vacancies" class="py-20"><div class="container-x">
        <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
            <x-section-heading eyebrow="Open positions" title="Current vacancies" />
            <form method="GET" action="{{ route('careers') }}#vacancies" class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title or location" class="field col-span-2 sm:w-56">
                <select name="department" class="field sm:w-44" onchange="this.form.submit()"><option value="">All departments</option>@foreach($departments as $d)<option value="{{ $d }}" @selected(request('department') === $d)>{{ $d }}</option>@endforeach</select>
                <select name="type" class="field sm:w-40" onchange="this.form.submit()"><option value="">All types</option>@foreach(\App\Models\JobPosting::TYPES as $k => $v)<option value="{{ $k }}" @selected(request('type') === $k)>{{ $v }}</option>@endforeach</select>
                <button class="btn-navy col-span-2 !py-2.5">Filter</button>
            </form>
        </div>
        @if($jobs->isEmpty())
            <div class="card mt-10 p-10 text-center"><p class="text-lg text-steel-600">No vacancies match your filters right now.</p>@if($generalOpen)<a href="{{ route('careers.general') }}" class="btn-primary mt-5">Submit a general application</a>@endif</div>
        @else
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">@foreach($jobs as $job)<div data-reveal style="--d:{{ ($loop->index % 3) * 90 }}ms"><x-job-card :job="$job" /></div>@endforeach</div>
        @endif
    </div></section>

    <section class="bg-navy-900 py-20 text-white"><div class="container-x grid gap-12 lg:grid-cols-2">
        <div><x-section-heading eyebrow="Benefits" title="One of the industry's most complete packages" light /><ul class="mt-8 grid gap-3 sm:grid-cols-2">@foreach(fortress('benefits') as $b)<li class="flex items-start gap-2.5 text-sm"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-amber-400" />{{ $b }}</li>@endforeach</ul></div>
        <div><x-section-heading eyebrow="Requirements" title="Who we're looking for" light /><ul class="mt-8 space-y-3">@foreach(fortress('applicant_requirements') as $r)<li class="flex items-start gap-2.5 text-sm text-blue-100/85"><x-icon name="shield" class="mt-0.5 h-4 w-4 shrink-0 text-blue-300" />{{ $r }}</li>@endforeach</ul><p class="mt-6 text-xs text-blue-100/60">In-house background checks are conducted on all applicants. Unsuitable applicants will be notified.</p></div>
    </div></section>

    <section class="py-20"><div class="container-x">
        <x-section-heading eyebrow="How it works" title="From application to deployment" center />
        <ol class="mt-12 grid gap-6 md:grid-cols-5">
            @foreach([['Apply online', 'Fill the form and attach your CV. You receive a reference number by email.'], ['Screening', 'Our recruiters review your documents and call to confirm details.'], ['Interview', 'At our St. Augustine head office or Marabella branch.'], ['Assessment', 'Aptitude test, fitness check, drug screening and background checks.'], ['Training', 'Level 1 class, uniform kit and your first deployment.']] as $i => [$t, $b])
                <li class="relative rounded-xl border border-steel-200 p-6 tilt" data-reveal style="--d:{{ $i * 100 }}ms"><span class="font-display text-4xl font-extrabold text-blue-200">0{{ $i + 1 }}</span><h3 class="h3 mt-2 text-navy-900">{{ $t }}</h3><p class="mt-2 text-sm text-steel-600">{{ $b }}</p></li>
            @endforeach
        </ol>
        @if($generalOpen)<div class="mt-12 text-center"><p class="text-steel-600">Don't see the right role?</p><a href="{{ route('careers.general') }}" class="btn-primary mt-3">Submit a general application</a></div>@endif
    </div></section>
</x-layouts.public>
