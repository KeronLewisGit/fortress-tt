@props(['title' => 'Applicant portal'])
@php $applicant = auth('applicant')->user(); $unread = $applicant?->unreadCount() ?? 0; @endphp
<x-layouts.public :title="$title" description="Fortress Security Services applicant portal – track your application, book your interview and manage your CV.">
    <section class="border-b border-steel-200 bg-navy-950 text-white">
        <div class="container-x flex flex-col gap-4 py-8 md:flex-row md:items-end md:justify-between">
            <div><p class="eyebrow-light">Applicant portal</p><h1 class="h2 mt-2">{{ $title }}</h1></div>
            @if($applicant)
            <nav class="-mx-4 flex gap-1 overflow-x-auto px-4 text-sm font-semibold sm:mx-0 sm:px-0" aria-label="Portal">
                @foreach([['Dashboard', route('portal.dashboard'), 'portal.dashboard|portal.applications.*'], ['Notifications', route('portal.notifications'), 'portal.notifications'], ['My CV & profile', route('portal.profile'), 'portal.profile*']] as [$l, $u, $p])
                    <a href="{{ $u }}" class="shrink-0 rounded-md px-3 py-2 {{ request()->routeIs(...explode('|', $p)) ? 'bg-blue-500 text-white' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">{{ $l }}@if($l === 'Notifications' && $unread)<span class="ml-1.5 rounded-full bg-amber-400 px-1.5 text-[11px] font-bold text-navy-950">{{ $unread }}</span>@endif</a>
                @endforeach
                <form method="POST" action="{{ route('portal.logout') }}" class="shrink-0">@csrf<button class="rounded-md px-3 py-2 text-blue-100/80 hover:bg-white/10 hover:text-white">Sign out</button></form>
            </nav>
            @endif
        </div>
    </section>
    <section class="bg-steel-50 py-10"><div class="container-x">
        @if($errors->any())<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><p class="font-semibold">Please check the following:</p><ul class="mt-1 list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        {{ $slot }}
    </div></section>
</x-layouts.public>
