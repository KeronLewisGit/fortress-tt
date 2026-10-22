<x-layouts.public title="About us" description="Fortress Security Services Ltd began operations in 2006 as Personnel Protection Services Ltd. IFPO Corporate Member, STOW certified, registered under the Ministry of National Security.">
    <section class="bg-navy-950 py-20 text-white"><div class="container-x"><p class="eyebrow-light">About Fortress</p><h1 class="h1 mt-4 max-w-4xl">An innovative, professional security firm with demonstrated experience</h1><p class="mt-6 max-w-2xl text-lg text-blue-100/85">Founded on the growing demand for security service in an industry driven by professionalism and safety, our operating philosophy is simple: develop and provide the best security service programme for our clients.</p></div></section>

    <section class="py-20"><div class="container-x grid gap-12 lg:grid-cols-2">
        <div><p class="eyebrow">Mission</p><h2 class="h2 mt-3 text-navy-900">Innovative solutions. Superior service.</h2><p class="mt-5 text-lg text-steel-600">{{ fortress('mission') }}</p></div>
        <div><p class="eyebrow">Vision</p><h2 class="h2 mt-3 text-navy-900">The Caribbean's leading provider</h2><p class="mt-5 text-lg text-steel-600">{{ fortress('vision') }}</p></div>
    </div></section>

    <section class="bg-steel-50 py-20"><div class="container-x">
        <x-section-heading eyebrow="Our story" title="Two decades of growth" />
        <ol class="mt-12 grid gap-6 md:grid-cols-5">
            @foreach(fortress('timeline') as $t)
                <li class="relative rounded-xl border border-steel-200 bg-white p-6"><span class="font-display text-3xl font-extrabold text-blue-500">{{ $t['year'] }}</span><h3 class="h3 mt-2 text-navy-900">{{ $t['title'] }}</h3><p class="mt-2 text-sm text-steel-600">{{ $t['body'] }}</p></li>
            @endforeach
        </ol>
    </div></section>

    <section class="py-20"><div class="container-x grid gap-12 lg:grid-cols-5">
        <div class="lg:col-span-2"><x-section-heading eyebrow="Our commitments" title="What every client can expect" /></div>
        <div class="grid gap-4 lg:col-span-3">
            @foreach(fortress('commitments') as $c)<div class="card flex gap-4 p-6"><span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500 text-white"><x-icon name="check" class="h-5 w-5" /></span><div><h3 class="h3 text-navy-900">{{ $c['title'] }}</h3><p class="mt-1 text-steel-600">{{ $c['body'] }}</p></div></div>@endforeach
        </div>
    </div></section>

    <section class="bg-navy-900 py-20 text-white"><div class="container-x">
        <x-section-heading eyebrow="Credentials" title="Certified, registered, accountable" light />
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach(fortress('certifications') as $c)<div class="rounded-xl border border-white/10 bg-white/5 p-6"><x-icon name="award" class="h-8 w-8 text-amber-400" /><p class="mt-4 text-xs font-bold uppercase tracking-[0.16em] text-blue-300">{{ $c['year'] }}</p><h3 class="h3 mt-1">{{ $c['title'] }}</h3><p class="mt-2 text-sm text-blue-100/80">{{ $c['body'] }}</p></div>@endforeach
        </div>
    </div></section>

    <section class="py-20"><div class="container-x">
        <x-section-heading eyebrow="Where we are" title="North and South offices, island-wide deployment" />
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            @foreach(fortress('offices') as $o)
                <div class="card p-6"><h3 class="h3 text-navy-900">{{ $o['name'] }}</h3><p class="mt-2 text-steel-600">{{ $o['address'] }}</p><p class="mt-3 text-sm"><a href="{{ tel_href($o['phone']) }}" class="font-semibold text-blue-500">{{ $o['phone'] }}</a>@if($o['phone_alt']) · <a href="{{ tel_href($o['phone_alt']) }}" class="font-semibold text-blue-500">{{ $o['phone_alt'] }}</a>@endif @if($o['fax'])· Fax {{ $o['fax'] }}@endif</p><p class="mt-1 text-sm text-steel-500">{{ $o['hours'] }}</p><a href="{{ $o['map'] }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 hover:text-blue-500"><x-icon name="map-pin" class="h-4 w-4" />Open in Google Maps</a></div>
            @endforeach
        </div>
    </div></section>
</x-layouts.public>
