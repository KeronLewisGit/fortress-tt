@props(['title' => 'Admin', 'wide' => false])
<!DOCTYPE html>
<html lang="en" class="h-full bg-steel-50">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Fortress Admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <meta name="robots" content="noindex">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-steel-900" x-data="{ nav: false }">
@php
    $u = auth()->user();
    $newApps = \App\Models\Application::where('status', 'new')->count();
    $newEnq = \App\Models\Enquiry::where('status', 'new')->count();
    $menu = [
        ['Dashboard', route('admin.dashboard'), 'admin.dashboard', 'home', null],
        ['Pipeline', route('admin.applications.pipeline'), 'admin.applications.pipeline', 'columns', null],
        ['Applicants', route('admin.applications.index'), 'admin.applications.index|admin.applications.show', 'users', $newApps],
        ['Jobs', route('admin.jobs.index'), 'admin.jobs.*', 'briefcase', null],
        ['Enquiries', route('admin.enquiries.index'), 'admin.enquiries.*', 'inbox', $newEnq],
        ['Email templates', route('admin.templates.index'), 'admin.templates.*', 'template', null],
    ];
    if ($u->isAdmin()) {
        $menu[] = ['Services', route('admin.services.index'), 'admin.services.*', 'layers', null];
        $menu[] = ['Settings', route('admin.settings.edit'), 'admin.settings.*', 'settings', null];
        $menu[] = ['Users', route('admin.users.index'), 'admin.users.*', 'id', null];
    }
@endphp
<div class="flex min-h-full">
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full transform flex-col bg-navy-900 text-white transition lg:sticky lg:top-0 lg:h-screen lg:translate-x-0" :class="nav ? 'translate-x-0' : ''">
        <div class="flex h-16 items-center justify-between px-5"><a href="{{ route('admin.dashboard') }}"><x-logo class="h-9" light /></a><button class="text-white/80 lg:hidden" @click="nav = false" aria-label="Close"><x-icon name="x" class="h-5 w-5" /></button></div>
        <p class="px-5 pb-1 pt-3 text-[11px] font-bold uppercase tracking-[0.18em] text-white/40">Recruitment</p>
        <nav class="space-y-0.5 px-3">
            @foreach($menu as [$label, $url, $pattern, $icon, $badge])
                @if($label === 'Services')</nav><p class="px-5 pb-1 pt-4 text-[11px] font-bold uppercase tracking-[0.18em] text-white/40">Administration</p><nav class="space-y-0.5 px-3">@endif
                <a href="{{ $url }}" class="admin-nav-link {{ request()->routeIs(...explode('|', $pattern)) ? 'active' : '' }}"><x-icon :name="$icon" class="h-[18px] w-[18px]" />{{ $label }}@if($badge)<span class="ml-auto rounded-full bg-amber-400 px-2 py-0.5 text-[11px] font-bold text-navy-950">{{ $badge }}</span>@endif</a>
            @endforeach
        </nav>
        <div class="mt-auto border-t border-white/10 p-4 text-sm">
            <a href="{{ route('admin.profile.edit') }}" class="block font-semibold text-white hover:text-blue-300">{{ $u->name }}</a>
            <div class="mt-1 flex items-center justify-between text-xs text-white/70"><span>{{ \App\Models\User::ROLES[$u->role] ?? ucfirst($u->role) }}</span><a href="{{ route('home') }}" target="_blank" class="hover:text-white">View site ↗</a></div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">@csrf<button class="text-xs text-white/70 hover:text-white">Sign out</button></form>
        </div>
    </aside>
    <div x-show="nav" x-cloak class="fixed inset-0 z-30 bg-black/40 lg:hidden" @click="nav = false"></div>
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-steel-200 bg-white/90 px-4 backdrop-blur sm:px-6">
            <button class="rounded-lg p-2 hover:bg-steel-100 lg:hidden" @click="nav = true" aria-label="Menu"><x-icon name="menu" class="h-5 w-5" /></button>
            <h1 class="truncate font-display text-xl font-bold uppercase tracking-wide text-navy-900">{{ $title }}</h1>
            <div class="ml-auto flex items-center gap-2">{{ $actions ?? '' }}@unless(request()->routeIs('admin.jobs.create'))<a href="{{ route('admin.jobs.create') }}" class="{{ isset($actions) && trim($actions) !== '' ? 'admin-btn-secondary' : 'admin-btn-blue' }}">+ Post a job</a>@endunless</div>
        </header>
        <main class="flex-1 p-4 sm:p-6 lg:p-8 {{ $wide ? '' : 'mx-auto w-full max-w-7xl' }}">
            @if(session('status'))<div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('status') }}</div>@endif
            @if($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><p class="font-semibold">Please fix the following:</p><ul class="mt-1 list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
