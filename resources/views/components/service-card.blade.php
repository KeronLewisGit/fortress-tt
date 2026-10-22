@props(['service'])
<a href="{{ route('services.show', $service) }}" class="card group flex h-full flex-col p-6 transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-glow">
    <span class="inline-flex h-12 w-12 items-center justify-center rounded-lg bg-navy-900 text-blue-300 transition group-hover:bg-blue-500 group-hover:text-white"><x-icon :name="$service->icon" class="h-6 w-6" /></span>
    @if($service->eyebrow)<p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-steel-500">{{ $service->eyebrow }}</p>@endif
    <h3 class="h3 mt-1 text-navy-900">{{ $service->name }}</h3>
    <p class="mt-2 flex-1 text-sm text-steel-600">{{ $service->summary }}</p>
    <span class="mt-5 inline-flex items-center gap-1.5 font-display text-sm font-bold uppercase tracking-wide text-blue-500">Learn more <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
</a>
