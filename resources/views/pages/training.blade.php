<x-layouts.public title="Officer training" description="All Fortress recruits undergo rigorous in-house training by senior officers: aptitude assessment, Level 1 and 2, Response Officer and Level 3 Cash in Transit programmes.">
    <section class="bg-navy-950 py-20 text-white"><div class="container-x"><p class="eyebrow-light">Training &amp; development</p><h1 class="h1 mt-4 max-w-4xl">Careers reap the benefits of world-class training</h1><p class="mt-6 max-w-2xl text-lg text-blue-100/85">All new recruits undergo rigorous training by senior officers in all areas of security. Staff development is a crucial part of our success – training continues at every level, for every officer.</p></div></section>
    <section class="py-20"><div class="container-x">
        <ol class="relative space-y-6 before:absolute before:left-6 before:top-6 before:h-[calc(100%-3rem)] before:w-px before:bg-steel-200 md:before:left-1/2">
            @foreach(fortress('training') as $i => $t)
                <li class="relative md:grid md:grid-cols-2 md:gap-12 {{ $i % 2 ? '' : 'md:text-right' }}">
                    <span class="absolute left-6 top-6 z-10 -ml-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-[10px] font-bold text-white md:left-1/2">{{ $i + 1 }}</span>
                    <div class="card ml-12 p-6 md:ml-0 {{ $i % 2 ? 'md:col-start-2' : '' }}">
                        <p class="eyebrow">{{ $t['level'] }}</p><h2 class="h3 mt-1 text-navy-900">{{ $t['title'] }}</h2><p class="mt-2 text-steel-600">{{ $t['body'] }}</p>
                        <ul class="mt-4 flex flex-wrap gap-2 {{ $i % 2 ? '' : 'md:justify-end' }}">@foreach($t['topics'] as $topic)<li class="chip">{{ $topic }}</li>@endforeach</ul>
                    </div>
                </li>
            @endforeach
        </ol>
    </div></section>
    <section class="bg-steel-50 py-20"><div class="container-x grid gap-10 lg:grid-cols-2 lg:items-center">
        <x-section-heading eyebrow="Start here" title="Ready to train with us?">Entry-level officers, drivers and CIT crew are recruited year-round. Check the current vacancies or submit a general application and we'll match you to the next intake.</x-section-heading>
        <div class="grid gap-4">
            @forelse($jobs as $job)<x-job-card :job="$job" />@empty<div class="card p-6"><p class="text-steel-600">No roles are posted right now. Send a general application and we'll contact you for the next intake.</p></div>@endforelse
            <a href="{{ route('careers') }}" class="btn-navy">All vacancies</a>
        </div>
    </div></section>
</x-layouts.public>
