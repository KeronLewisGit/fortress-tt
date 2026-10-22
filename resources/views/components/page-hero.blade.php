@props(['eyebrow' => null, 'title', 'image' => null, 'alt' => '', 'position' => 'center', 'compact' => false])
<section class="relative overflow-hidden bg-navy-950 text-white">
    @if($image)
        <div class="absolute inset-0"><x-photo :name="$image" :alt="$alt" class="opacity-45" style="object-position: {{ $position }}" sizes="100vw" eager /></div>
        <div class="absolute inset-0 hero-fade"></div>
    @else
        <div class="absolute inset-0 grid-bg"></div><div class="absolute inset-0 glow"></div>
    @endif
    <div class="container-x relative {{ $compact ? 'py-14' : 'py-20 lg:py-24' }}">
        {{ $before ?? '' }}
        @if($eyebrow)<p class="eyebrow-light" data-reveal>{{ $eyebrow }}</p>@endif
        <h1 class="h1 mt-4 max-w-4xl" data-reveal style="--d:80ms">{!! $title !!}</h1>
        @if(trim($slot) !== '')<div class="mt-6 max-w-2xl text-lg text-blue-100/85" data-reveal style="--d:160ms">{{ $slot }}</div>@endif
        {{ $after ?? '' }}
    </div>
</section>
