@props(['eyebrow' => null, 'title', 'light' => false, 'center' => false])
<div {{ $attributes->merge(['class' => 'max-w-3xl '.($center ? 'mx-auto text-center' : '')]) }}>
    @if($eyebrow)<p class="{{ $light ? 'eyebrow-light' : 'eyebrow' }}">{{ $eyebrow }}</p>@endif
    <h2 class="h2 mt-3 {{ $light ? 'text-white' : 'text-navy-900' }}">{{ $title }}</h2>
    @if(trim($slot) !== '')<div class="mt-4 text-lg {{ $light ? 'text-blue-100/80' : 'text-steel-600' }}">{{ $slot }}</div>@endif
</div>
