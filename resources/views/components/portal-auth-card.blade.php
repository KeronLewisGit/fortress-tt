@props(['title', 'subtitle' => null])
<x-layouts.public :title="$title">
    <section class="relative overflow-hidden bg-navy-950 py-16 text-white"><div class="absolute inset-0 grid-bg"></div><div class="absolute inset-0 glow"></div>
        <div class="container-x relative grid gap-10 lg:grid-cols-2 lg:items-center">
            <div><p class="eyebrow-light">Applicant portal</p><h1 class="h1 mt-4">{{ $title }}</h1>@if($subtitle)<p class="mt-5 max-w-lg text-lg text-blue-100/85">{{ $subtitle }}</p>@endif
                <ul class="mt-8 space-y-3 text-sm text-blue-100/85">@foreach(['Track every application from sent to decision', 'Book your interview time when invited', 'Keep your CV up to date for recruiters', 'Email and in-portal notifications at each step'] as $t)<li class="flex items-start gap-2.5"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-amber-400" />{{ $t }}</li>@endforeach</ul></div>
            <div class="card p-6 text-steel-800 sm:p-8">{{ $slot }}</div>
        </div>
    </section>
</x-layouts.public>
