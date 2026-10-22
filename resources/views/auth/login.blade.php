<!DOCTYPE html>
<html lang="en" class="h-full"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Staff sign in · Fortress Security Services</title><meta name="robots" content="noindex"><link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="flex min-h-full items-center justify-center bg-navy-950 p-6 grid-bg">
<div class="w-full max-w-sm">
    <div class="mb-8 flex justify-center"><x-logo class="h-14" light /></div>
    <form method="POST" action="{{ route('login') }}" class="rounded-2xl bg-white p-8 shadow-2xl">
        @csrf
        <h1 class="font-display text-2xl font-bold uppercase tracking-wide text-navy-900">Staff sign in</h1>
        <p class="mt-1 text-sm text-steel-500">Recruitment &amp; administration portal</p>
        @if($errors->any())<div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <div class="mt-6"><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field"></div>
        <div class="mt-4"><label class="label" for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password" class="field"></div>
        <label class="mt-4 flex items-center gap-2 text-sm text-steel-600"><input type="checkbox" name="remember" class="check">Keep me signed in</label>
        <button class="btn-primary mt-6 w-full">Sign in</button>
        <p class="mt-6 text-center text-xs text-steel-500"><a href="{{ route('home') }}" class="hover:text-navy-900">← Back to website</a></p>
    </form>
</div>
</body></html>
