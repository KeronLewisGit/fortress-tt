<x-layouts.public :title="$service->name" :description="$service->summary">
    @php $img = service_image($service); @endphp
    <section class="relative overflow-hidden bg-navy-950 text-white">
        @if($img)<div class="absolute inset-0"><x-photo :name="$img" :alt="$service->name" class="opacity-35" sizes="100vw" eager /></div><div class="absolute inset-0 hero-fade"></div>@else<div class="absolute inset-0 grid-bg"></div><div class="absolute inset-0 glow"></div>@endif
        <div class="container-x relative grid gap-10 py-20 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <a href="{{ route('services') }}" class="inline-flex items-center gap-1.5 text-sm text-blue-300 hover:text-white">← All services</a>
                <div class="mt-6 flex items-start gap-5"><span class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-blue-500 sm:inline-flex" data-reveal="scale"><x-icon :name="$service->icon" class="h-8 w-8" /></span><div>@if($service->eyebrow)<p class="eyebrow-light" data-reveal>{{ $service->eyebrow }}</p>@endif<h1 class="h1 mt-2" data-reveal style="--d:80ms">{{ $service->name }}</h1><p class="mt-5 max-w-2xl text-lg text-blue-100/85" data-reveal style="--d:160ms">{{ $service->summary }}</p></div></div>
            </div>
            @if($img)<div class="lg:col-span-5" data-reveal="right" style="--d:200ms"><div class="reveal-clip img-zoom frame-corner aspect-[4/3] overflow-hidden rounded-xl shadow-2xl"><x-photo :name="$img" :alt="$service->name" sizes="(min-width: 1024px) 40vw, 100vw" eager /></div></div>@endif
        </div>
    </section>
    <section class="py-20"><div class="container-x grid gap-12 lg:grid-cols-3">
        <div class="prose-fss lg:col-span-2">
            @foreach($service->paragraphs() as $p)<p class="text-lg">{{ $p }}</p>@endforeach
            @if($service->features)
                <h2 class="h3 mt-10 text-navy-900">What's included</h2>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">@foreach($service->features as $f)<li class="flex items-start gap-3 rounded-lg border border-steel-200 p-3.5 text-sm" data-reveal style="--d:{{ $loop->index * 50 }}ms"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />{{ $f }}</li>@endforeach</ul>
            @endif
        </div>
        <aside class="space-y-6">
            <div class="rounded-xl bg-navy-900 p-6 text-white"><h3 class="h3">Request a proposal</h3><p class="mt-2 text-sm text-blue-100/80">Site survey and written proposal within 48 hours.</p><a href="{{ route('contact', ['service' => $service->name]) }}" class="btn-primary mt-5 w-full">Get a quote</a><a href="{{ tel_href(setting('business.phone')) }}" class="btn-outline-light mt-2 w-full">{{ setting('business.phone') }}</a></div>
            @if($service->ideal_for)<div class="card p-6"><h3 class="h3 text-navy-900">Ideal for</h3><ul class="mt-3 space-y-2 text-sm text-steel-600">@foreach($service->ideal_for as $i)<li class="flex gap-2"><span class="text-blue-500">•</span>{{ $i }}</li>@endforeach</ul></div>@endif
            <div class="card p-6"><h3 class="h3 text-navy-900">Related services</h3><ul class="mt-3 divide-y divide-steel-100">@foreach($others as $o)<li><a href="{{ route('services.show', $o) }}" class="flex items-center gap-3 py-2.5 text-sm font-semibold text-navy-900 hover:text-blue-500"><x-icon :name="$o->icon" class="h-4 w-4 text-blue-500" />{{ $o->name }}</a></li>@endforeach</ul></div>
        </aside>
    </div></section>
</x-layouts.public>
