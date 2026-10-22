@props(['title' => null, 'description' => null, 'dark' => false])
@php
    $siteName = setting('business.name', fortress('name'));
    $openJobs = \App\Models\JobPosting::live()->count();
    $services = \App\Models\Service::active()->get(['name', 'slug']);
    $phone = setting('business.phone', '(868) 223-2223');
    $nav = [
        ['Home', route('home'), 'home'],
        ['About', route('about'), 'about'],
        ['Services', route('services'), 'services*'],
        ['Training', route('training'), 'training'],
        ['Careers', route('careers'), 'careers*'],
        ['Contact', route('contact'), 'contact'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ $siteName }}</title>
    <meta name="description" content="{{ $description ?? 'Fortress Security Services Ltd – armed and unarmed protection officers, cash in transit, CCTV and alarms, 24-hour monitoring, GPS tracking and investigations across Trinidad & Tobago since 2006.' }}">
    <meta property="og:title" content="{{ $title ?? $siteName }}"><meta property="og:description" content="{{ $description ?? fortress('tagline') }}"><meta property="og:type" content="website"><meta property="og:url" content="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="canonical" href="{{ url()->current() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'SecurityService', 'name' => $siteName, 'url' => url('/'),
        'telephone' => '+1-868-223-2223', 'email' => setting('business.email'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => '8 Desaine Street', 'addressLocality' => 'St. Augustine', 'addressCountry' => 'TT'],
        'sameAs' => array_values(array_filter([setting('business.facebook'), setting('business.linkedin'), setting('business.x')])),
        'foundingDate' => '2006', 'slogan' => fortress('motto'),
    ], JSON_UNESCAPED_SLASHES) !!}</script>
</head>
<body class="flex min-h-full flex-col" x-data="{ nav: false }">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded focus:bg-white focus:px-3 focus:py-2">Skip to content</a>

    {{-- Top bar --}}
    <div class="bg-navy-950 text-xs text-blue-100/80">
        <div class="container-x flex h-9 items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <span class="hidden items-center gap-1.5 sm:inline-flex"><span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-signal opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-signal"></span></span>Monitoring centre live · 24/7/365</span>
                <a href="{{ tel_href($phone) }}" class="inline-flex items-center gap-1.5 font-semibold text-white hover:text-blue-300"><x-icon name="phone" class="h-3.5 w-3.5" />{{ $phone }}</a>
            </div>
            <div class="flex items-center gap-4">
                @if($openJobs)<a href="{{ route('careers') }}" class="hidden items-center gap-1.5 text-amber-400 hover:text-amber-500 md:inline-flex"><x-icon name="briefcase" class="h-3.5 w-3.5" />We're hiring – {{ $openJobs }} open {{ Str::plural('role', $openJobs) }}</a>@endif
                <a href="{{ route('track') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-white"><x-icon name="map-pin" class="h-3.5 w-3.5" />Track your vehicle</a>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <header class="sticky top-0 z-50 border-b border-steel-200 bg-white/95 backdrop-blur">
        <div class="container-x flex h-[72px] items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="h-11" aria-label="{{ $siteName }} – home"><x-logo class="h-11" /></a>
            <nav class="hidden items-center gap-7 lg:flex" aria-label="Main">
                @foreach($nav as [$label, $url, $pattern])
                    <a href="{{ $url }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}">{{ $label }}@if($label === 'Careers' && $openJobs)<span class="ml-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-400 px-1.5 text-[11px] text-navy-950">{{ $openJobs }}</span>@endif</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('contact') }}" class="btn-primary hidden !py-2.5 sm:inline-flex">Request a quote</a>
                <button class="rounded-md p-2 text-navy-900 hover:bg-steel-100 lg:hidden" @click="nav = true" aria-label="Open menu"><x-icon name="menu" class="h-6 w-6" /></button>
            </div>
        </div>
        {{-- Mobile drawer --}}
        <div x-show="nav" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-navy-950/70" @click="nav = false"></div>
            <div class="absolute inset-y-0 right-0 flex w-80 max-w-full flex-col bg-navy-900 p-6 text-white" x-transition:enter="transition transform duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="flex items-center justify-between"><x-logo class="h-10" light /><button @click="nav = false" class="rounded-md p-2 hover:bg-white/10" aria-label="Close menu"><x-icon name="x" class="h-6 w-6" /></button></div>
                <nav class="mt-8 flex flex-col gap-1">
                    @foreach($nav as [$label, $url, $pattern])
                        <a href="{{ $url }}" class="rounded-lg px-3 py-3 font-display text-xl font-bold uppercase tracking-wide {{ request()->routeIs($pattern) ? 'bg-blue-500' : 'hover:bg-white/10' }}">{{ $label }}</a>
                    @endforeach
                </nav>
                <div class="mt-auto space-y-3">
                    <a href="{{ route('contact') }}" class="btn-primary w-full">Request a quote</a>
                    <a href="{{ tel_href($phone) }}" class="btn-outline-light w-full">Call {{ $phone }}</a>
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="flex-1">
        @if(session('status'))
            <div class="container-x mt-6"><div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800" role="status">{{ session('status') }}</div></div>
        @endif
        {{ $slot }}
    </main>

    {{-- CTA band --}}
    @unless(request()->routeIs('contact', 'careers.apply', 'careers.general'))
    <section class="relative overflow-hidden bg-blue-500 text-white">
        <div class="container-x flex flex-col items-start justify-between gap-6 py-12 md:flex-row md:items-center">
            <div><p class="eyebrow-light !text-blue-100">Protection beyond your expectation</p><h2 class="h2 mt-2">Talk to a security consultant today</h2><p class="mt-2 max-w-xl text-blue-50">Site survey and written proposal within 48 hours. Management available 24/7.</p></div>
            <div class="flex flex-wrap gap-3"><a href="{{ route('contact') }}" class="btn bg-white text-navy-900 hover:bg-blue-50">Request a quote</a><a href="{{ tel_href($phone) }}" class="btn-outline-light">{{ $phone }}</a></div>
        </div>
    </section>
    @endunless

    <footer class="bg-navy-950 text-blue-100/80">
        <div class="container-x grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <x-logo class="h-12" light />
                <p class="mt-5 text-sm leading-relaxed">{{ fortress('tagline') }}</p>
                <p class="mt-3 font-display text-sm font-bold uppercase tracking-[0.2em] text-blue-300">{{ fortress('values_line') }}</p>
                <div class="mt-5 flex gap-2">
                    @if($fb = setting('business.facebook'))<a href="{{ $fb }}" target="_blank" rel="noopener" class="rounded-md bg-white/10 p-2 hover:bg-blue-500 hover:text-white" aria-label="Facebook"><x-icon name="facebook" class="h-5 w-5" /></a>@endif
                    @if($li = setting('business.linkedin'))<a href="{{ $li }}" target="_blank" rel="noopener" class="rounded-md bg-white/10 p-2 hover:bg-blue-500 hover:text-white" aria-label="LinkedIn"><x-icon name="linkedin" class="h-5 w-5" /></a>@endif
                    @if($x = setting('business.x'))<a href="{{ $x }}" target="_blank" rel="noopener" class="rounded-md bg-white/10 p-2 hover:bg-blue-500 hover:text-white" aria-label="X (Twitter)"><x-icon name="twitter" class="h-5 w-5" /></a>@endif
                </div>
            </div>
            <div>
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Services</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach($services as $s)<li><a href="{{ route('services.show', $s) }}" class="hover:text-white">{{ $s->name }}</a></li>@endforeach
                </ul>
            </div>
            <div>
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Company</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white">About Fortress</a></li>
                    <li><a href="{{ route('training') }}" class="hover:text-white">Officer training</a></li>
                    <li><a href="{{ route('careers') }}" class="hover:text-white">Careers &amp; vacancies</a></li>
                    <li><a href="{{ route('track') }}" target="_blank" rel="noopener" class="hover:text-white">Track your vehicle ↗</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">Request a quote</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy policy</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Offices</h3>
                <ul class="mt-4 space-y-4 text-sm">
                    @foreach(fortress('offices') as $o)
                    <li><p class="font-semibold text-white">{{ $o['name'] }}</p><p>{{ $o['address'] }}</p><p><a href="{{ tel_href($o['phone']) }}" class="hover:text-white">{{ $o['phone'] }}</a>@if($o['phone_alt']) / <a href="{{ tel_href($o['phone_alt']) }}" class="hover:text-white">{{ substr($o['phone_alt'], -4) }}</a>@endif</p></li>
                    @endforeach
                    <li><a href="mailto:{{ setting('business.email') }}" class="hover:text-white">{{ setting('business.email') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-x flex flex-col items-start justify-between gap-2 py-5 text-xs sm:flex-row sm:items-center">
                <p>© {{ date('Y') }} {{ $siteName }}. All rights reserved. Registered under the Ministry of National Security since {{ fortress('registered_since') }}.</p>
                <p>IFPO Corporate Member · STOW Certified</p>
            </div>
        </div>
    </footer>
</body>
</html>
