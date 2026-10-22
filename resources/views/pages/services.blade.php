<x-layouts.public title="Services" description="Protection officers, cash in transit, personal protection, CCTV & alarms, 24-hour monitoring, GPS tracking, courier, investigations, event security and valet – Fortress Security Services, Trinidad & Tobago.">
    <section class="bg-navy-950 py-20 text-white"><div class="container-x"><p class="eyebrow-light">Our services</p><h1 class="h1 mt-4 max-w-4xl">A complete security programme</h1><p class="mt-6 max-w-2xl text-lg text-blue-100/85">Manpower, logistics, electronics and intelligence from a single accountable provider – with management available 24 hours a day.</p></div></section>
    <section class="py-20"><div class="container-x grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach($services as $service)<x-service-card :service="$service" />@endforeach</div></section>
    <section class="bg-steel-50 py-16"><div class="container-x grid gap-8 md:grid-cols-3">
        @foreach([['Survey', 'A consultant visits your site, reviews risks and existing systems.'], ['Proposal', 'Written proposal with manpower, equipment and monitoring options within 48 hours.'], ['Deploy', 'Trained officers and installers mobilise; your account manager reviews performance monthly.']] as $i => [$t, $b])
            <div class="flex gap-4"><span class="font-display text-5xl font-extrabold text-blue-200">0{{ $i + 1 }}</span><div><h3 class="h3 text-navy-900">{{ $t }}</h3><p class="mt-1 text-steel-600">{{ $b }}</p></div></div>
        @endforeach
    </div></section>
</x-layouts.public>
