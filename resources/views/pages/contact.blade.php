<x-layouts.public title="Contact & quotes" description="Request a security quote from Fortress Security Services. Head office 8 Desaine Street, St. Augustine (868) 223-2223 · Marabella branch (868) 223-9666.">
    <x-page-hero eyebrow="Contact us" title="Request a quote or site survey" image="consultation" alt="Fortress security consultant meeting a client" position="center 30%">Tell us about your premises or event and a security consultant will respond within one business day. For emergencies, call the head office – management is available 24/7.</x-page-hero>
    <section class="py-16"><div class="container-x grid gap-12 lg:grid-cols-5">
        <form method="POST" action="{{ route('contact.store') }}" class="card space-y-5 p-6 sm:p-8 lg:col-span-3" novalidate>
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label class="label" for="name">Your name *</label><input id="name" name="name" value="{{ old('name') }}" required class="field"><x-field-error name="name" /></div>
                <div><label class="label" for="company">Company / organisation</label><input id="company" name="company" value="{{ old('company') }}" class="field"></div>
                <div><label class="label" for="email">Email *</label><input id="email" type="email" name="email" value="{{ old('email') }}" required class="field"><x-field-error name="email" /></div>
                <div><label class="label" for="phone">Phone</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" class="field"></div>
                <div><label class="label" for="service">Service required</label><select id="service" name="service" class="field"><option value="">Select a service</option>@foreach(fortress('contact_services') as $s)<option value="{{ $s }}" @selected(old('service', request('service')) === $s)>{{ $s }}</option>@endforeach</select></div>
                <div><label class="label" for="location">Site location</label><input id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Chaguanas" class="field"></div>
            </div>
            <div><label class="label" for="message">How can we help? *</label><textarea id="message" name="message" rows="6" required class="field" placeholder="Number of officers, hours of cover, cameras needed, event date…">{{ old('message') }}</textarea><x-field-error name="message" /></div>
            <div class="flex flex-wrap items-center gap-4"><button class="btn-primary">Send request</button><p class="text-xs text-steel-500">We never share your details. See our <a href="{{ route('privacy') }}" class="underline">privacy policy</a>.</p></div>
        </form>
        <aside class="space-y-6 lg:col-span-2">
            @foreach(fortress('offices') as $o)
                <div class="card p-6"><h2 class="h3 text-navy-900">{{ $o['name'] }}</h2><p class="mt-2 text-steel-600">{{ $o['address'] }}</p>
                    <ul class="mt-4 space-y-2 text-sm"><li class="flex items-center gap-2"><x-icon name="phone" class="h-4 w-4 text-blue-500" /><a href="{{ tel_href($o['phone']) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $o['phone'] }}</a>@if($o['phone_alt']) <span class="text-steel-400">/</span> <a href="{{ tel_href($o['phone_alt']) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $o['phone_alt'] }}</a>@endif</li>@if($o['fax'])<li class="flex items-center gap-2 text-steel-600"><x-icon name="file" class="h-4 w-4 text-blue-500" />Fax {{ $o['fax'] }}</li>@endif<li class="flex items-center gap-2 text-steel-600"><x-icon name="clock" class="h-4 w-4 text-blue-500" />{{ $o['hours'] }}</li></ul>
                    <a href="{{ $o['map'] }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-blue-500 hover:text-navy-900"><x-icon name="map-pin" class="h-4 w-4" />Directions</a></div>
            @endforeach
            <div class="rounded-xl bg-navy-900 p-6 text-white"><h2 class="h3">Email</h2><ul class="mt-3 space-y-1.5 text-sm"><li>General: <a href="mailto:{{ setting('business.email') }}" class="text-blue-300 hover:text-white">{{ setting('business.email') }}</a></li><li>Recruitment: <a href="mailto:{{ setting('business.recruitment_email') }}" class="text-blue-300 hover:text-white">{{ setting('business.recruitment_email') }}</a></li></ul>@if($wa = whatsapp_href(setting('business.whatsapp')))<a href="{{ $wa }}" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:bg-[#1ebe5b] mt-5 w-full"><x-icon name="whatsapp" class="h-5 w-5" />WhatsApp us</a>@endif<p class="mt-5 text-xs text-blue-100/70">Monitoring centre staffed 24 hours a day, 7 days a week, 365 days a year.</p></div>
        </aside>
    </div></section>
</x-layouts.public>
