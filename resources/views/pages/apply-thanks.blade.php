<x-layouts.public title="Application received">
    <section class="py-24"><div class="container-x max-w-2xl text-center">
        <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-700"><x-icon name="check" class="h-8 w-8" /></span>
        <p class="eyebrow mt-6">{{ session('duplicate') ? 'Already received' : 'Application received' }}</p>
        <h1 class="h2 mt-3 text-navy-900">Thank you, {{ $application->first_name }}</h1>
        <p class="mt-4 text-lg text-steel-600">@if(session('duplicate'))We already have your application for this role on file from the last 30 days – no need to apply again.@else Your application{{ $application->job ? ' for '.$application->job->title : '' }} has been received. @if(session('mail_failed'))We could not send the confirmation email right now, so please keep your reference number safe.@else A confirmation has been sent to <strong>{{ $application->email }}</strong>.@endif @endif</p>
        <div class="card mx-auto mt-8 inline-block px-8 py-5"><p class="text-xs font-bold uppercase tracking-[0.18em] text-steel-500">Your reference</p><p class="font-display text-4xl font-extrabold text-navy-900">{{ $application->reference }}</p></div>
        <p class="mt-8 text-sm text-steel-500">Please quote this reference if you call us. Our recruiters usually make screening calls within 5 business days.</p>
        @if($application->applicant_id)
            <div class="mt-8 flex flex-wrap justify-center gap-3"><a href="{{ route('portal.applications.show', $application->reference) }}" class="btn-primary">Track this application</a><a href="{{ route('careers') }}" class="btn-outline">Browse other vacancies</a></div>
        @else
            <div class="card mx-auto mt-8 max-w-lg p-6 text-left"><p class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Track your application online</p><p class="mt-1 text-sm text-steel-600">Create a free applicant account with <strong>{{ $application->email }}</strong> to follow your progress, book your interview when invited and keep your CV updated.</p><a href="{{ route('portal.register', ['email' => $application->email]) }}" class="btn-primary mt-4 w-full">Create my applicant account</a><p class="mt-2 text-center text-xs text-steel-500">Already registered? <a href="{{ route('portal.login') }}" class="text-blue-500">Sign in</a></p></div>
            <div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ route('careers') }}" class="btn-navy">Browse other vacancies</a><a href="{{ route('home') }}" class="btn-outline">Back to home</a></div>
        @endif
    </div></section>
</x-layouts.public>
