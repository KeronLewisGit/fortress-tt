@props(['title' => 'Applicant portal'])
@php $applicant = auth('applicant')->user(); $unread = $applicant?->unreadCount() ?? 0; @endphp
<x-layouts.public :title="$title" description="Fortress Security Services applicant portal – track your application, book your interview and manage your CV.">
    <section class="bg-navy-950 text-white">
        <div class="container-x">
            <div class="flex flex-col gap-4 pb-6 pt-8 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="eyebrow-light">Applicant portal</p><h1 class="h2 mt-2">{{ $title }}</h1></div>
                @if($applicant)
                <div class="flex items-center gap-3 text-sm text-blue-100/80">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500 font-bold text-white">{{ $applicant->initials() }}</span>
                    <div class="leading-tight"><p class="font-semibold text-white">{{ $applicant->full_name }}</p><p class="text-xs">{{ $applicant->email }}</p></div>
                </div>
                @endif
            </div>
            @if($applicant)
            <nav class="-mx-4 flex gap-1 overflow-x-auto px-4 sm:mx-0 sm:px-0" aria-label="Portal">
                @foreach([['Dashboard', route('portal.dashboard'), 'portal.dashboard|portal.applications.*', 'home'], ['Notifications', route('portal.notifications'), 'portal.notifications', 'bell'], ['My CV & profile', route('portal.profile'), 'portal.profile*', 'id']] as [$l, $u, $p, $i])
                    @php $active = request()->routeIs(...explode('|', $p)); @endphp
                    <a href="{{ $u }}" class="inline-flex shrink-0 items-center gap-2 border-b-2 px-3 py-3 text-sm font-semibold transition {{ $active ? 'border-blue-400 text-white' : 'border-transparent text-blue-100/70 hover:border-white/30 hover:text-white' }}"><x-icon :name="$i" class="h-4 w-4" />{{ $l }}@if($l === 'Notifications' && $unread)<span class="rounded-full bg-amber-400 px-1.5 text-[11px] font-bold text-navy-950">{{ $unread }}</span>@endif</a>
                @endforeach
                <form method="POST" action="{{ route('portal.logout') }}" class="ml-auto shrink-0">@csrf<button class="inline-flex items-center gap-2 border-b-2 border-transparent px-3 py-3 text-sm font-semibold text-blue-100/70 transition hover:text-white"><x-icon name="external" class="h-4 w-4" />Sign out</button></form>
            </nav>
            @endif
        </div>
    </section>
    <section class="bg-steel-50 py-10 lg:py-12"><div class="container-x">
        @if($errors->any())<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><p class="font-semibold">Please check the following:</p><ul class="mt-1 list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        {{ $slot }}
    </div></section>
</x-layouts.public>
