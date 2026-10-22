<x-layouts.public :title="($job ? 'Apply – '.$job->title : 'General application')" description="Apply online to join Fortress Security Services Ltd.">
    <section class="bg-navy-950 py-14 text-white"><div class="container-x">
        <a href="{{ $job ? route('careers.show', $job) : route('careers') }}" class="inline-flex items-center gap-1.5 text-sm text-blue-300 hover:text-white">← Back</a>
        <p class="eyebrow-light mt-6">{{ $job ? 'Application' : 'General application' }}</p>
        <h1 class="h1 mt-3">{{ $job?->title ?? 'Tell us about yourself' }}</h1>
        <p class="mt-4 max-w-2xl text-blue-100/85">{{ $job ? $job->location.' · '.$job->typeLabel() : 'No matching vacancy? Submit your details and we will contact you for the next intake that fits your profile.' }}</p>
    </div></section>

    <section class="py-14"><div class="container-x grid gap-10 lg:grid-cols-3">
        <form method="POST" action="{{ $job ? route('careers.apply.store', $job) : route('careers.general.store') }}" enctype="multipart/form-data" class="space-y-10 lg:col-span-2" novalidate>
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

            @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><p class="font-semibold">Please check the highlighted fields.</p></div>@endif

            <fieldset class="card p-6 sm:p-8"><legend class="sr-only">Personal details</legend>
                <h2 class="h3 text-navy-900">1. Personal details</h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div><label class="label" for="first_name">First name *</label><input id="first_name" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" class="field"><x-field-error name="first_name" /></div>
                    <div><label class="label" for="last_name">Last name *</label><input id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" class="field"><x-field-error name="last_name" /></div>
                    <div><label class="label" for="email">Email *</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="field"><x-field-error name="email" /></div>
                    <div><label class="label" for="phone">Phone / WhatsApp *</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="868-" class="field"><x-field-error name="phone" /></div>
                    <div><label class="label" for="date_of_birth">Date of birth *</label><input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required max="{{ today()->subYears(18)->toDateString() }}" class="field"><p class="help">Applicants must be 18 or older.</p><x-field-error name="date_of_birth" /></div>
                    <div><label class="label" for="nationality">Nationality *</label><input id="nationality" name="nationality" value="{{ old('nationality', 'Trinidad & Tobago') }}" required class="field"><x-field-error name="nationality" /></div>
                    <div class="sm:col-span-2"><label class="label" for="address">Street address</label><input id="address" name="address" value="{{ old('address') }}" autocomplete="street-address" class="field"><x-field-error name="address" /></div>
                    <div class="sm:col-span-2"><label class="label" for="area">Town / area *</label><input id="area" name="area" value="{{ old('area') }}" required placeholder="e.g. Tunapuna, Couva, Scarborough" class="field"><p class="help">Helps us match you to posts near home.</p><x-field-error name="area" /></div>
                </div>
            </fieldset>

            <fieldset class="card p-6 sm:p-8"><legend class="sr-only">Qualifications</legend>
                <h2 class="h3 text-navy-900">2. Qualifications &amp; experience</h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div><label class="label" for="licence_class">Driver's permit *</label><select id="licence_class" name="licence_class" required class="field">@foreach(\App\Models\Application::LICENCES as $k => $v)<option value="{{ $k }}" @selected(old('licence_class', 'none') === $k)>{{ $v }}</option>@endforeach</select><x-field-error name="licence_class" /></div>
                    <div><label class="label" for="years_experience">Years of security experience *</label><input id="years_experience" type="number" min="0" max="50" name="years_experience" value="{{ old('years_experience', 0) }}" required class="field"><x-field-error name="years_experience" /></div>
                    <div><label class="label" for="education">Highest education</label><input id="education" name="education" value="{{ old('education') }}" placeholder="e.g. School Leaving Certificate, CXC (5 passes)" class="field"></div>
                    <div><label class="label" for="current_employer">Current / most recent employer</label><input id="current_employer" name="current_employer" value="{{ old('current_employer') }}" class="field"></div>
                    <div class="sm:col-span-2 grid gap-3 sm:grid-cols-3">
                        <label class="flex items-start gap-3 rounded-lg border border-steel-200 p-3.5 text-sm"><input type="checkbox" name="has_police_certificate" value="1" @checked(old('has_police_certificate')) class="check mt-0.5"><span><span class="font-semibold text-navy-900">Police Certificate</span><br><span class="text-steel-500">Issued within last 6 months</span></span></label>
                        <label class="flex items-start gap-3 rounded-lg border border-steel-200 p-3.5 text-sm"><input type="checkbox" name="has_precept" value="1" @checked(old('has_precept')) class="check mt-0.5"><span><span class="font-semibold text-navy-900">Precept holder</span><br><span class="text-steel-500">Valid security precept</span></span></label>
                        <label class="flex items-start gap-3 rounded-lg border border-steel-200 p-3.5 text-sm"><input type="checkbox" name="has_firearm_licence" value="1" @checked(old('has_firearm_licence')) class="check mt-0.5"><span><span class="font-semibold text-navy-900">Firearm User's Licence</span><br><span class="text-steel-500">FUL / FUEC</span></span></label>
                    </div>
                </div>
            </fieldset>

            <fieldset class="card p-6 sm:p-8"><legend class="sr-only">Documents</legend>
                <h2 class="h3 text-navy-900">3. CV &amp; cover note</h2>
                <div class="mt-6 grid gap-5">
                    <div><label class="label" for="resume">Upload CV / résumé</label><input id="resume" type="file" name="resume" accept=".pdf,.doc,.docx" class="field file:mr-3 file:rounded file:border-0 file:bg-navy-900 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white"><p class="help">PDF or Word, up to 5 MB. Optional but recommended.</p><x-field-error name="resume" /></div>
                    <div><label class="label" for="cover_letter">Why do you want to work with Fortress?</label><textarea id="cover_letter" name="cover_letter" rows="5" class="field" placeholder="Tell us about your experience, availability and the area you can work in.">{{ old('cover_letter') }}</textarea><x-field-error name="cover_letter" /></div>
                    <div><label class="label" for="source">How did you hear about us?</label><select id="source" name="source" class="field">@foreach(\App\Models\Application::SOURCES as $k => $v)<option value="{{ $k }}" @selected(old('source', 'website') === $k)>{{ $v }}</option>@endforeach</select></div>
                    <label class="flex items-start gap-3 text-sm text-steel-700"><input type="checkbox" name="consent" value="1" required @checked(old('consent')) class="check mt-0.5"><span>I confirm the information provided is accurate and I consent to Fortress Security Services Ltd processing my data, including in-house background checks, for recruitment purposes as described in the <a href="{{ route('privacy') }}" class="font-semibold text-blue-500" target="_blank">privacy policy</a>. *</span></label><x-field-error name="consent" />
                </div>
            </fieldset>

            <div class="flex flex-wrap items-center gap-4"><button class="btn-amber">Submit application</button><p class="text-sm text-steel-500">You'll receive a confirmation email with your reference number.</p></div>
        </form>

        <aside class="space-y-6">
            @if($job)<div class="card p-6"><p class="eyebrow">Applying for</p><h3 class="h3 mt-1 text-navy-900">{{ $job->title }}</h3><p class="mt-1 text-sm text-steel-500">{{ $job->location }} · {{ $job->typeLabel() }}</p>@if($job->requirements)<ul class="mt-4 space-y-2 text-sm text-steel-600">@foreach(array_slice($job->requirements, 0, 6) as $r)<li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />{{ $r }}</li>@endforeach</ul>@endif</div>@endif
            <div class="rounded-xl bg-steel-50 p-6 text-sm text-steel-600"><p class="font-semibold text-navy-900">What happens next</p><ol class="mt-3 list-decimal space-y-1.5 pl-4"><li>Confirmation email with your reference</li><li>Recruiter screening call within 5 business days</li><li>Interview at St. Augustine or Marabella</li><li>Aptitude test, fitness and background checks</li></ol></div>
        </aside>
    </div></section>
</x-layouts.public>
