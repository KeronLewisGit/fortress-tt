<x-layouts.public>
    {{-- Hero: looping control-room footage + kinetic headline + radar mograph --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <video data-hero class="hero-video" autoplay muted loop playsinline preload="metadata" poster="{{ img('hero-poster') }}" aria-hidden="true">
            <source src="{{ asset('assets/video/hero-loop.webm') }}" type="video/webm"><source src="{{ asset('assets/video/hero-loop.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 hero-fade"></div>
        <div class="absolute inset-0 grid-bg opacity-70"></div>
        <div class="absolute inset-0 scanline pointer-events-none"></div>
        <div class="container-x relative grid gap-12 py-20 lg:grid-cols-12 lg:items-center lg:py-28">
            <div class="lg:col-span-7" x-data="rotator(['Protection', 'Prevention', 'Response', 'Vigilance'])">
                @if($a = setting('home.announcement'))<p class="mb-5 inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-1 text-sm font-semibold text-amber-400" data-reveal>{{ $a }}</p>@endif
                <p class="eyebrow-light" data-reveal>Trinidad &amp; Tobago · Since {{ fortress('founded') }}</p>
                <h1 class="h1 mt-4" data-reveal style="--d:80ms">
                    <span class="kinetic text-blue-400">
                        <template x-for="(w, k) in words" :key="w"><span :class="{ 'is-active': i === k, 'is-leaving': leaving === k }" x-text="w"></span></template>
                        <span class="invisible" aria-hidden="true">Prevention</span>
                    </span><br>beyond your expectation
                </h1>
                <p class="mt-6 max-w-xl text-lg text-blue-100/85" data-reveal style="--d:160ms">{{ fortress('tagline') }} Armed and unarmed officers, cash in transit, electronic security and a Central Monitoring Station that never sleeps.</p>
                <div class="mt-8 flex flex-wrap gap-3" data-reveal style="--d:240ms">
                    <a href="{{ route('contact') }}" class="btn-primary shine">Request a quote</a>
                    <a href="{{ route('services') }}" class="btn-outline-light">Explore services</a>
                </div>
                <dl class="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-4">
                    @foreach(fortress('stats') as $k => $s)
                        <div class="border-l-2 border-blue-500 pl-4" data-reveal style="--d:{{ 300 + $k * 80 }}ms"><dt class="font-display text-3xl font-extrabold text-white">{{ $s['value'] }}</dt><dd class="mt-1 text-xs uppercase tracking-wide text-blue-100/70">{{ $s['label'] }}</dd></div>
                    @endforeach
                </dl>
            </div>
            <div class="lg:col-span-5" data-reveal="right" style="--d:200ms">
                <div class="relative rounded-2xl border border-white/10 bg-navy-950/60 p-6 backdrop-blur-md">
                    <div class="flex items-center justify-between"><p class="font-display text-lg font-bold uppercase tracking-wide">Central Monitoring Station</p><span class="inline-flex items-center gap-2 rounded-full bg-signal/15 px-2.5 py-1 text-xs font-bold text-signal"><span class="live-dot"></span>LIVE</span></div>
                    <div class="mt-4 grid grid-cols-5 items-center gap-4">
                        <x-radar class="col-span-2 w-full max-w-[180px]" />
                        <ul class="col-span-3 space-y-2.5 text-sm text-blue-100/85">
                            @foreach([['bell', 'Alarm & panic-button monitoring 24/7/365'], ['map-pin', 'GPS fleet tracking · Fortress Global'], ['radio', 'Patrol & tactical response dispatch'], ['wifi', 'Redundant power & comms – storm-proof']] as $k => [$i, $t])
                                <li class="flex items-start gap-2.5" data-reveal style="--d:{{ 400 + $k * 90 }}ms"><span class="mt-0.5 rounded-md bg-blue-500/20 p-1.5 text-blue-300"><x-icon :name="$i" class="h-4 w-4" /></span>{{ $t }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <a href="{{ tel_href(setting('business.phone')) }}" class="rounded-lg bg-white/10 p-3 text-center transition hover:bg-white/15"><p class="text-[11px] uppercase tracking-wide text-blue-100/60">Head office</p><p class="mt-0.5 font-display text-lg font-bold">{{ setting('business.phone') }}</p></a>
                        <a href="{{ route('track') }}" target="_blank" rel="noopener" class="shine rounded-lg bg-blue-500 p-3 text-center transition hover:bg-blue-400"><p class="text-[11px] uppercase tracking-wide text-blue-100">Customers</p><p class="mt-0.5 font-display text-lg font-bold">Track vehicle ↗</p></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust marquee --}}
    <section class="border-b border-steel-200 bg-steel-50 py-4" aria-label="Credentials">
        @php $trust = [['award', 'IFPO Corporate Member since 2014'], ['check', 'STOW Certificate of Conformance'], ['shield', 'Registered · Ministry of National Security'], ['clock', 'Central Monitoring Station 24/7/365'], ['map-pin', 'St. Augustine · Marabella · Tobago'], ['truck', 'Cash in Transit · Armoured fleet'], ['users', 'In-house trained officers'], ['radio', 'Mobile patrol & tactical response']]; @endphp
        <div class="marquee">
            @for($r = 0; $r < 2; $r++)
            <div class="marquee-track text-xs font-bold uppercase tracking-[0.16em] text-steel-500" @if($r) aria-hidden="true" @endif>
                @foreach($trust as [$i, $t])<span class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap"><x-icon :name="$i" class="h-4 w-4 text-blue-500" />{{ $t }}</span>@endforeach
            </div>
            @endfor
        </div>
    </section>

    {{-- Services --}}
    <section class="py-20">
        <div class="container-x">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end" data-reveal>
                <x-section-heading eyebrow="What we do" title="Integrated security, one provider">Manpower, electronics, logistics and intelligence – designed as one programme so nothing falls through the gaps.</x-section-heading>
                <a href="{{ route('services') }}" class="btn-outline shrink-0">All services</a>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($services->where('is_featured', true)->take(4) as $k => $service)<div data-reveal style="--d:{{ $loop->index * 90 }}ms"><x-service-card :service="$service" /></div>@endforeach
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($services->where('is_featured', false) as $service)
                    <a href="{{ route('services.show', $service) }}" class="flex items-center gap-2.5 rounded-lg border border-steel-200 px-3.5 py-3 text-sm font-semibold text-navy-900 transition hover:border-blue-300 hover:bg-blue-50" data-reveal style="--d:{{ $loop->index * 60 }}ms"><x-icon :name="$service->icon" class="h-[18px] w-[18px] shrink-0 text-blue-500" />{{ $service->name }}</a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Fortress – photo + commitments --}}
    <section class="relative overflow-hidden bg-navy-900 py-20 text-white">
        <div class="container-x grid gap-12 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-5" data-reveal="left">
                <x-section-heading eyebrow="Why Fortress" title="Trained in-house. Supervised around the clock." light>Every officer passes our aptitude assessment, Level 1 training and a three-month probation before earning a permanent post. Response teams and CIT crews go further – defensive driving, firearms law, polygraph and evasive driving.</x-section-heading>
                <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('training') }}" class="btn-primary shine">See the training ladder</a><a href="{{ route('about') }}" class="btn-outline-light">Our story</a></div>
                <div class="mt-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-8">
                    <div x-data="counter({{ date('Y') - fortress('founded') }})"><p class="font-display text-4xl font-extrabold text-blue-300"><span x-text="shown">{{ date('Y') - fortress('founded') }}</span>+</p><p class="mt-1 text-xs uppercase tracking-wide text-blue-100/70">Years in service</p></div>
                    <div x-data="counter(5)"><p class="font-display text-4xl font-extrabold text-blue-300"><span x-text="shown">5</span></p><p class="mt-1 text-xs uppercase tracking-wide text-blue-100/70">Training levels</p></div>
                    <div x-data="counter(365)"><p class="font-display text-4xl font-extrabold text-blue-300"><span x-text="shown">365</span></p><p class="mt-1 text-xs uppercase tracking-wide text-blue-100/70">Days monitored</p></div>
                </div>
            </div>
            <div class="lg:col-span-7">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="reveal-clip img-zoom frame-corner relative aspect-[4/5] overflow-hidden rounded-xl sm:row-span-2"><x-photo name="team-event" alt="Fortress protection officers standing together in uniform" sizes="(min-width: 1024px) 30vw, 50vw" /><div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/90 to-transparent p-5"><p class="font-display text-lg font-bold uppercase">One team. One standard.</p><p class="text-xs text-blue-100/80">Protection officers, drivers and response crews trained by senior Fortress officers.</p></div></div>
                    @foreach(fortress('commitments') as $c)
                        <div class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10" data-reveal style="--d:{{ 120 + $loop->index * 100 }}ms"><span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-500/20 text-blue-300"><x-icon name="check" class="h-4 w-4" /></span><h3 class="h3 mt-3 text-white">{{ $c['title'] }}</h3><p class="mt-2 text-sm text-blue-100/80">{{ $c['body'] }}</p></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Industries – photo tiles --}}
    <section class="py-20">
        <div class="container-x">
            <x-section-heading eyebrow="Who we protect" title="From the energy estate to the family home" center data-reveal />
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['Industrial & energy', 'STOW-certified teams for Point Lisas and industrial estates: gatehouse, perimeter, HSE-aligned response.', 'industrial-team', route('services.show', 'protection-officers')],
                    ['Banking & retail', 'Cash in transit, ATM replenishment, armed officers and CCTV for branches, malls and supermarkets.', 'cit-truck', route('services.show', 'cash-in-transit')],
                    ['Corporate & residential', 'Front-desk sentries, access control, alarm monitoring and mobile patrols for offices and gated communities.', 'control-room', route('services.show', 'alarm-monitoring')],
                    ['Events & VIPs', 'Crowd management for fêtes and conferences, close protection for executives and visiting dignitaries.', 'event-gate', route('services.show', 'event-security')],
                ] as [$t, $b, $im, $href])
                    <a href="{{ $href }}" class="group img-zoom relative flex aspect-[3/4] flex-col justify-end overflow-hidden rounded-xl bg-navy-900 text-white" data-reveal="scale" style="--d:{{ $loop->index * 90 }}ms">
                        <x-photo :name="$im" :alt="$t" class="absolute inset-0 opacity-80 transition group-hover:opacity-90" sizes="(min-width: 1024px) 25vw, 50vw" />
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-transparent"></div>
                        <div class="relative p-6"><h3 class="h3">{{ $t }}</h3><p class="mt-2 max-h-0 overflow-hidden text-sm text-blue-100/85 opacity-0 transition-all duration-500 group-hover:max-h-40 group-hover:opacity-100">{{ $b }}</p><span class="mt-3 inline-flex items-center gap-1.5 font-display text-sm font-bold uppercase tracking-wide text-blue-300">Learn more <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" /></span></div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Careers --}}
    @if($jobs->isNotEmpty())
    <section class="relative overflow-hidden border-t border-steel-200 bg-steel-50 py-20">
        <div class="container-x grid gap-10 lg:grid-cols-12 lg:items-start">
            <div class="lg:col-span-4" data-reveal="left">
                <div class="reveal-clip img-zoom relative aspect-[4/5] overflow-hidden rounded-xl"><x-photo name="hero-officer" alt="Smiling Fortress security officer in light-blue uniform" sizes="(min-width: 1024px) 30vw, 100vw" /><div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-navy-950"><span class="live-dot !bg-navy-950"></span>Now hiring</div></div>
            </div>
            <div class="lg:col-span-8">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end" data-reveal>
                    <x-section-heading eyebrow="We're hiring" title="Build a career in security">Permanent employment, in-house training, health plan, pension and profit sharing. Apply online in minutes.</x-section-heading>
                    <a href="{{ route('careers') }}" class="btn-amber shine shrink-0">All vacancies</a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">@foreach($jobs as $job)<div data-reveal style="--d:{{ $loop->index * 90 }}ms"><x-job-card :job="$job" /></div>@endforeach</div>
            </div>
        </div>
    </section>
    @endif
</x-layouts.public>
