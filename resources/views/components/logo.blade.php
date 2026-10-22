@props(['light' => false, 'compact' => false])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 48 56" class="h-full w-auto shrink-0" aria-hidden="true">
        <defs><linearGradient id="fss-g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3b8bff"/><stop offset="1" stop-color="#1f6fe5"/></linearGradient></defs>
        <path d="M24 2 44 9v17c0 13-8.5 22.5-20 28C12.5 48.5 4 39 4 26V9z" fill="url(#fss-g)"/>
        <path d="M24 7 39 12.5V26c0 10.5-6.6 18.3-15 23C15.6 44.3 9 36.5 9 26V12.5z" fill="#0a1a33"/>
        <path d="m24 14 2.6 5.6 6.1.7-4.5 4.2 1.2 6L24 27.5l-5.4 3 1.2-6-4.5-4.2 6.1-.7z" fill="#fff"/>
        <text x="24" y="43" text-anchor="middle" font-family="Barlow Condensed, Inter, sans-serif" font-weight="800" font-size="11" fill="#fff" letter-spacing="1">FSS</text>
    </svg>
    @unless($compact)
    <span class="flex flex-col leading-none">
        <span class="font-display text-[22px] font-extrabold uppercase tracking-[0.08em] {{ $light ? 'text-white' : 'text-navy-900' }}">Fortress</span>
        <span class="font-display text-[11px] font-bold uppercase tracking-[0.28em] {{ $light ? 'text-blue-300' : 'text-blue-500' }}">Security Services</span>
    </span>
    @endunless
</span>
