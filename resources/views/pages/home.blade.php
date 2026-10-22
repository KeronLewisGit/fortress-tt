<x-layouts.public>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <div class="absolute inset-0 grid-bg"></div>
        <div class="absolute inset-0 glow"></div>
        <svg class="absolute -right-24 top-1/2 hidden h-[640px] w-[640px] -translate-y-1/2 text-blue-500/10 lg:block" viewBox="0 0 48 56" fill="currentColor"><path d="M24 2 44 9v17c0 13-8.5 22.5-20 28C12.5 48.5 4 39 4 26V9z"/></svg>
        <div class="container-x relative grid gap-12 py-20 lg:grid-cols-12 lg:py-28">
            <div class="lg:col-span-7">
                @if($a = setting('home.announcement'))<p class="mb-5 inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-1 text-sm font-semibold text-amber-400">{{ $a }}</p>@endif
                <p class="eyebrow-light">Trinidad &amp; Tobago · Since {{ fortress('founded') }}</p>
                <h1 class="h1 mt-4">Protection <span class="text-blue-400">beyond</span> your expectation</h1>
                <p class="mt-6 max-w-xl text-lg text-blue-100/85">{{ fortress('tagline') }} Armed and unarmed officers, cash in transit, electronic security and a Central Monitoring Station that never sleeps.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('contact') }}" class="btn-primary">Request a quote</a>
                    <a href="{{ route('services') }}" class="btn-outline-light">Explore services</a>
                </div>
                <dl class="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-4">
                    @foreach(fortress('stats') as $s)
                        <div class="border-l-2 border-blue-500 pl-4"><dt class="font-display text-3xl font-extrabold text-white">{{ $s['value'] }}</dt><dd class="mt-1 text-xs uppercase tracking-wide text-blue-100/70">{{ $s['label'] }}</dd></div>
                    @endforeach
                </dl>
            </div>
            <div class="lg:col-span-5">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <div class="flex items-center justify-between"><p class="font-display text-lg font-bold uppercase tracking-wide">Central Monitoring Station</p><span class="inline-flex items-center gap-1.5 rounded-full bg-signal/15 px-2.5 py-1 text-xs font-bold text-signal"><span class="h-1.5 w-1.5 rounded-full bg-signal"></span>LIVE</span></div>
                    <ul class="mt-5 space-y-3 text-sm text-blue-100/85">
                        @foreach([['bell', 'Alarm & panic-button monitoring 24/7/365'], ['map-pin', 'GPS fleet tracking · Fortress Global'], ['radio', 'Mobile patrol & tactical response dispatch'], ['wifi', 'Multiple backups: operates through hurricanes, blackouts & floods']] as [$i, $t])
                            <li class="flex items-start gap-3"><span class="mt-0.5 rounded-md bg-blue-500/20 p-1.5 text-blue-300"><x-icon :name="$i" class="h-4 w-4" /></span>{{ $t }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <a href="{{ tel_href(setting('business.phone')) }}" class="rounded-lg bg-white/10 p-3 text-center hover:bg-white/15"><p class="text-[11px] uppercase tracking-wide text-blue-100/60">Head office</p><p class="mt-0.5 font-display text-lg font-bold">{{ setting('business.phone') }}</p></a>
                        <a href="{{ route('track') }}" target="_blank" rel="noopener" class="rounded-lg bg-blue-500 p-3 text-center hover:bg-blue-400"><p class="text-[11px] uppercase tracking-wide text-blue-100">Customers</p><p class="mt-0.5 font-display text-lg font-bold">Track vehicle ↗</p></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust strip --}}
    <section class="border-b border-steel-200 bg-steel-50">
        <div class="container-x flex flex-wrap items-center justify-center gap-x-10 gap-y-3 py-5 text-xs font-bold uppercase tracking-[0.16em] text-steel-500">
            <span class="inline-flex items-center gap-2"><x-icon name="award" class="h-4 w-4 text-blue-500" />IFPO Corporate Member</span>
            <span class="inline-flex items-center gap-2"><x-icon name="check" class="h-4 w-4 text-blue-500" />STOW Certificate of Conformance</span>
            <span class="inline-flex items-center gap-2"><x-icon name="shield" class="h-4 w-4 text-blue-500" />Registered · Ministry of National Security</span>
            <span class="inline-flex items-center gap-2"><x-icon name="map-pin" class="h-4 w-4 text-blue-500" />St. Augustine · Marabella · Tobago</span>
        </div>
    </section>

    {{-- Services --}}
    <section class="py-20">
        <div class="container-x">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <x-section-heading eyebrow="What we do" title="Integrated security, one provider">Manpower, electronics, logistics and intelligence – designed as one programme so nothing falls through the gaps.</x-section-heading>
                <a href="{{ route('services') }}" class="btn-outline shrink-0">All services</a>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($services->where('is_featured', true)->take(4) as $service)<x-service-card :service="$service" />@endforeach
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($services->where('is_featured', false) as $service)
                    <a href="{{ route('services.show', $service) }}" class="flex items-center gap-2.5 rounded-lg border border-steel-200 px-3.5 py-3 text-sm font-semibold text-navy-900 transition hover:border-blue-300 hover:bg-blue-50"><x-icon :name="$service->icon" class="h-4.5 w-4.5 h-[18px] w-[18px] shrink-0 text-blue-500" />{{ $service->name }}</a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Fortress --}}
    <section class="bg-navy-900 py-20 text-white">
        <div class="container-x grid gap-12 lg:grid-cols-2 lg:items-center">
            <div>
                <x-section-heading eyebrow="Why Fortress" title="Trained in-house. Supervised around the clock." light>Every officer passes our aptitude assessment, Level 1 training and a three-month probation before earning a permanent post. Response teams and CIT crews go further – defensive driving, firearms law, polygraph and evasive driving.</x-section-heading>
                <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('training') }}" class="btn-primary">See the training ladder</a><a href="{{ route('about') }}" class="btn-outline-light">Our story</a></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach(fortress('commitments') as $c)
                    <div class="rounded-xl border border-white/10 bg-white/5 p-6"><h3 class="h3 text-white">{{ $c['title'] }}</h3><p class="mt-2 text-sm text-blue-100/80">{{ $c['body'] }}</p></div>
                @endforeach
                <div class="rounded-xl bg-blue-500 p-6"><p class="font-display text-5xl font-extrabold">{{ date('Y') - fortress('founded') }}+</p><p class="mt-1 text-sm font-semibold uppercase tracking-wide text-blue-100">Years protecting Trinidad &amp; Tobago</p></div>
            </div>
        </div>
    </section>

    {{-- Industries --}}
    <section class="py-20">
        <div class="container-x">
            <x-section-heading eyebrow="Who we protect" title="From the energy estate to the family home" center />
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['Industrial & energy', 'STOW-certified teams for Point Lisas and industrial estates: gatehouse, perimeter, HSE-aligned response.', 'fire'],
                    ['Banking & retail', 'Cash in transit, ATM replenishment, armed officers and CCTV for branches, malls and supermarkets.', 'truck'],
                    ['Corporate & residential', 'Front-desk sentries, access control, alarm monitoring and mobile patrols for offices and gated communities.', 'lock'],
                    ['Events & VIPs', 'Crowd management for fêtes and conferences, close protection for executives and visiting dignitaries.', 'users'],
                ] as [$t, $b, $i])
                    <div class="card p-6"><x-icon :name="$i" class="h-7 w-7 text-blue-500" /><h3 class="h3 mt-4 text-navy-900">{{ $t }}</h3><p class="mt-2 text-sm text-steel-600">{{ $b }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Careers --}}
    @if($jobs->isNotEmpty())
    <section class="border-t border-steel-200 bg-steel-50 py-20">
        <div class="container-x">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <x-section-heading eyebrow="We're hiring" title="Build a career in security">Permanent employment, in-house training, health plan, pension and profit sharing. Apply online in minutes.</x-section-heading>
                <a href="{{ route('careers') }}" class="btn-amber shrink-0">All vacancies</a>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">@foreach($jobs as $job)<x-job-card :job="$job" />@endforeach</div>
        </div>
    </section>
    @endif
</x-layouts.public>
