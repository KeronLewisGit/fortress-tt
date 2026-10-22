@props(['service', 'image' => true])
@php $img = $image ? service_image($service) : null; @endphp
<a href="{{ route('services.show', $service) }}" class="card group tilt flex h-full flex-col overflow-hidden hover:border-blue-300 hover:shadow-glow">
    @if($img)
        <div class="img-zoom relative aspect-[16/9] overflow-hidden bg-navy-900"><x-photo :name="$img" :alt="$service->name" sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw" /><div class="absolute inset-0 bg-gradient-to-t from-navy-950/70 via-transparent to-transparent"></div><span class="absolute bottom-3 left-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500 text-white shadow-lg"><x-icon :name="$service->icon" class="h-5 w-5" /></span></div>
    @endif
    <div class="flex flex-1 flex-col p-6">
        @unless($img)<span class="inline-flex h-12 w-12 items-center justify-center rounded-lg bg-navy-900 text-blue-300 transition group-hover:bg-blue-500 group-hover:text-white"><x-icon :name="$service->icon" class="h-6 w-6" /></span>@endunless
        @if($service->eyebrow)<p class="{{ $img ? '' : 'mt-5' }} text-xs font-bold uppercase tracking-[0.16em] text-steel-500">{{ $service->eyebrow }}</p>@endif
        <h3 class="h3 mt-1 text-navy-900">{{ $service->name }}</h3>
        <p class="mt-2 flex-1 text-sm text-steel-600">{{ $service->summary }}</p>
        <span class="mt-5 inline-flex items-center gap-1.5 font-display text-sm font-bold uppercase tracking-wide text-blue-500">Learn more <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
    </div>
</a>
