<x-layouts.public title="Services" description="Protection officers, cash in transit, personal protection, CCTV & alarms, 24-hour monitoring, GPS tracking, courier, investigations, event security and valet – Fortress Security Services, Trinidad & Tobago.">
    <x-page-hero eyebrow="Our services" title="A complete security programme" image="control-room" alt="Fortress operator monitoring CCTV feeds in the Central Monitoring Station" position="center 35%">Manpower, logistics, electronics and intelligence from a single accountable provider – with management available 24 hours a day.</x-page-hero>
    <section class="py-20"><div class="container-x grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach($services as $service)<div data-reveal style="--d:{{ ($loop->index % 3) * 90 }}ms"><x-service-card :service="$service" /></div>@endforeach</div></section>
    <section class="bg-steel-50 py-16"><div class="container-x grid gap-8 md:grid-cols-3">
        @foreach([['Survey', 'A consultant visits your site, reviews risks and existing systems.'], ['Proposal', 'Written proposal with manpower, equipment and monitoring options within 48 hours.'], ['Deploy', 'Trained officers and installers mobilise; your account manager reviews performance monthly.']] as $i => [$t, $b])
            <div class="flex gap-4" data-reveal style="--d:{{ $i * 120 }}ms"><span class="font-display text-5xl font-extrabold text-blue-200">0{{ $i + 1 }}</span><div><h3 class="h3 text-navy-900">{{ $t }}</h3><p class="mt-1 text-steel-600">{{ $b }}</p></div></div>
        @endforeach
    </div></section>
</x-layouts.public>
