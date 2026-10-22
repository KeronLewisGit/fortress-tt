@props(['job'])
<a href="{{ route('careers.show', $job) }}" class="card group flex h-full flex-col p-6 transition hover:-translate-y-0.5 hover:border-blue-300">
    <div class="flex flex-wrap items-center gap-2">
        <span class="chip-blue">{{ $job->typeLabel() }}</span>
        @if($job->department)<span class="chip">{{ $job->department }}</span>@endif
        @if($job->is_featured)<span class="chip-amber">Urgent</span>@endif
        @if($job->isClosingSoon())<span class="chip border-red-200 bg-red-50 text-red-700">Closes {{ $job->closes_at->format('j M') }}</span>@endif
    </div>
    <h3 class="h3 mt-4 text-navy-900 group-hover:text-blue-500">{{ $job->title }}</h3>
    <p class="mt-1 flex items-center gap-1.5 text-sm text-steel-500"><x-icon name="map-pin" class="h-4 w-4" />{{ $job->location }}</p>
    <p class="mt-3 flex-1 text-sm text-steel-600 line-clamp-3">{{ $job->summary }}</p>
    <div class="mt-5 flex items-center justify-between text-sm">
        <span class="text-steel-500">{{ $job->openings }} {{ Str::plural('opening', $job->openings) }}</span>
        <span class="inline-flex items-center gap-1.5 font-display font-bold uppercase tracking-wide text-blue-500">View &amp; apply <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
    </div>
</a>
