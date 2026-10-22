<x-layouts.public title="Application received">
    <section class="py-24"><div class="container-x max-w-2xl text-center">
        <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-700"><x-icon name="check" class="h-8 w-8" /></span>
        <p class="eyebrow mt-6">{{ session('duplicate') ? 'Already received' : 'Application received' }}</p>
        <h1 class="h2 mt-3 text-navy-900">Thank you, {{ $application->first_name }}</h1>
        <p class="mt-4 text-lg text-steel-600">@if(session('duplicate'))We already have your application for this role on file from the last 30 days – no need to apply again.@else Your application{{ $application->job ? ' for '.$application->job->title : '' }} has been received. A confirmation has been sent to <strong>{{ $application->email }}</strong>.@endif</p>
        <div class="card mx-auto mt-8 inline-block px-8 py-5"><p class="text-xs font-bold uppercase tracking-[0.18em] text-steel-500">Your reference</p><p class="font-display text-4xl font-extrabold text-navy-900">{{ $application->reference }}</p></div>
        <p class="mt-8 text-sm text-steel-500">Please quote this reference if you call us. Our recruiters usually make screening calls within 5 business days.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3"><a href="{{ route('careers') }}" class="btn-navy">Browse other vacancies</a><a href="{{ route('home') }}" class="btn-outline">Back to home</a></div>
    </div></section>
</x-layouts.public>
