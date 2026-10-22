<x-layouts.admin :title="$application->full_name">
    <x-slot:actions>
        @if($application->resume_path)<a href="{{ route('admin.applications.resume', $application) }}" class="admin-btn-secondary"><x-icon name="download" class="h-4 w-4" />CV</a>@endif
        <a href="{{ tel_href($application->phone) }}" class="admin-btn-secondary"><x-icon name="phone" class="h-4 w-4" />Call</a>
        @if($wa = whatsapp_href($application->phone, 'Good day '.$application->first_name.', this is Fortress Security Services regarding your application '.$application->reference.'.'))<a href="{{ $wa }}" target="_blank" class="admin-btn-secondary"><x-icon name="whatsapp" class="h-4 w-4" />WhatsApp</a>@endif
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            {{-- Header card --}}
            <div class="admin-card p-6">
                <div class="flex flex-wrap items-start gap-5">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-navy-900 text-xl font-bold text-white">{{ $application->initials() }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2"><h2 class="font-display text-2xl font-bold text-navy-900">{{ $application->full_name }}</h2><x-stage-badge :status="$application->status" :label="$application->stageLabel()" /><x-stars :rating="$application->rating" /></div>
                        <p class="mt-1 text-sm text-steel-500">{{ $application->reference }} · Applied {{ $application->created_at->format('j M Y, g:i a') }} ({{ $application->created_at->diffForHumans() }}) · Source: {{ \App\Models\Application::SOURCES[$application->source] ?? $application->source }}</p>
                        <dl class="mt-4 grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                            <div><dt class="text-steel-500">Email</dt><dd><a href="mailto:{{ $application->email }}" class="font-medium text-blue-500">{{ $application->email }}</a></dd></div>
                            <div><dt class="text-steel-500">Phone</dt><dd class="font-medium">{{ $application->phone }}</dd></div>
                            <div><dt class="text-steel-500">Area / address</dt><dd class="font-medium">{{ $application->area }}@if($application->address)<br><span class="text-steel-600">{{ $application->address }}</span>@endif</dd></div>
                            <div><dt class="text-steel-500">Date of birth</dt><dd class="font-medium">{{ $application->date_of_birth?->format('j M Y') ?? '—' }}@if($application->age()) ({{ $application->age() }} yrs)@endif · {{ $application->nationality }}</dd></div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Qualifications --}}
            <div class="admin-card p-6">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Qualifications &amp; experience</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    @foreach([['Driver\'s permit', $application->licenceLabel(), $application->licence_class && $application->licence_class !== 'none'], ['Police certificate', $application->has_police_certificate ? 'Yes – within 6 months' : 'Not provided', $application->has_police_certificate], ['Precept', $application->has_precept ? 'Holder' : 'No', $application->has_precept], ['Firearm licence', $application->has_firearm_licence ? 'Holder' : 'No', $application->has_firearm_licence], ['Experience', $application->years_experience.' '.Str::plural('year', $application->years_experience), $application->years_experience > 0], ['Education', $application->education ?: '—', (bool) $application->education]] as [$l, $v, $ok])
                        <div class="rounded-lg border p-3.5 {{ $ok ? 'border-green-200 bg-green-50/50' : 'border-steel-200' }}"><p class="text-xs text-steel-500">{{ $l }}</p><p class="mt-0.5 text-sm font-semibold text-steel-800">{{ $v }}</p></div>
                    @endforeach
                </div>
                @if($application->current_employer)<p class="mt-4 text-sm"><span class="text-steel-500">Current / last employer:</span> <span class="font-medium">{{ $application->current_employer }}</span></p>@endif
                @if($application->cover_letter)<div class="mt-5"><p class="text-xs font-bold uppercase tracking-wide text-steel-500">Cover note</p><p class="mt-2 whitespace-pre-line rounded-lg bg-steel-50 p-4 text-sm text-steel-700">{{ $application->cover_letter }}</p></div>@endif
                <div class="mt-5 flex items-center gap-3 rounded-lg border border-steel-200 p-3.5 text-sm"><x-icon name="file" class="h-5 w-5 text-blue-500" />@if($application->resume_path)<span class="font-medium">{{ $application->resume_name }}</span><a href="{{ route('admin.applications.resume', $application) }}" class="ml-auto font-semibold text-blue-500">Download CV</a>@else<span class="text-steel-500">No CV attached</span>@endif</div>
            </div>

            {{-- Send email --}}
            <div class="admin-card p-6" x-data="composer(@js($templates->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'subject' => $t->subject, 'body' => $t->body, 'set_status' => $t->set_status])), @js(['subject' => old('subject', ''), 'body' => old('body', ''), 'set_status' => old('set_status', ''), 'open' => $errors->has('body') || $errors->has('subject') || old('subject') !== null]))">
                <div class="flex items-center justify-between"><h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Email applicant</h3><a href="{{ route('admin.templates.index') }}" class="text-xs font-semibold text-blue-500">Manage templates</a></div>
                <form method="POST" action="{{ route('admin.applications.message', $application) }}" class="mt-4 space-y-4">@csrf
                    <div class="flex flex-wrap gap-2">@foreach($templates as $t)<button type="button" @click="pick({{ $t->id }}); open = true" class="rounded-full border border-steel-200 bg-white px-3 py-1 text-xs font-semibold text-steel-700 hover:border-blue-300 hover:text-blue-500">{{ $t->name }}</button>@endforeach<button type="button" @click="open = !open" class="rounded-full border border-dashed border-steel-300 px-3 py-1 text-xs font-semibold text-steel-500">Blank email</button></div>
                    <div x-show="open" x-cloak class="space-y-3">
                        <input name="subject" x-model="subject" placeholder="Subject" required class="field">
                        <textarea name="body" x-model="body" rows="10" required class="field text-sm"></textarea>
                        <div class="flex flex-wrap items-center gap-3"><select name="set_status" x-model="setStatus" class="field w-auto"><option value="">Keep current stage</option>@foreach(\App\Models\Application::STAGES as $k => $v)<option value="{{ $k }}">Move to {{ $v }} after sending</option>@endforeach</select><button class="admin-btn-blue"><x-icon name="mail" class="h-4 w-4" />Send to {{ $application->email }}</button></div>
                        <p class="help">Placeholders {{ implode(' ', \App\Models\MessageTemplate::placeholders()) }} are replaced before sending. Set the interview date in the side panel first if the template uses it.</p>
                    </div>
                </form>
            </div>

            {{-- Timeline --}}
            <div class="admin-card p-6">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Notes &amp; activity</h3>
                <form method="POST" action="{{ route('admin.applications.notes.store', $application) }}" class="mt-4 flex gap-2">@csrf<textarea name="body" rows="2" required placeholder="Add a note – screening call outcome, documents seen, references…" class="field text-sm"></textarea><button class="admin-btn-primary self-end">Add</button></form>
                <ol class="mt-6 space-y-4 border-l-2 border-steel-200 pl-5">
                    @forelse($application->notes as $n)
                        <li class="relative"><span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full ring-4 ring-white {{ ['note' => 'bg-navy-900', 'status' => 'bg-blue-500', 'email' => 'bg-violet-500', 'interview' => 'bg-amber-400', 'system' => 'bg-steel-300'][$n->type] ?? 'bg-steel-300' }}"></span>
                            <p class="text-xs text-steel-500"><span class="font-semibold text-steel-700">{{ $n->user?->name ?? 'System' }}</span> · {{ ucfirst($n->type) }} · {{ $n->created_at->format('j M Y, g:i a') }}</p>
                            @if($n->type === 'email')<p class="mt-1 text-sm font-semibold text-steel-800">✉ {{ $n->meta['subject'] ?? 'Email sent' }}</p><details class="mt-1 text-sm text-steel-600"><summary class="cursor-pointer text-xs text-blue-500">Show message</summary><p class="mt-2 whitespace-pre-line rounded bg-steel-50 p-3">{{ $n->body }}</p></details>
                            @else<p class="mt-1 whitespace-pre-line text-sm text-steel-800">{{ $n->body }}</p>@endif
                        </li>
                    @empty<li class="text-sm text-steel-500">No activity yet.</li>@endforelse
                </ol>
            </div>
        </div>

        <aside class="space-y-6">
            <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="admin-card p-6 space-y-4">@csrf @method('PATCH')
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Manage</h3>
                <div><label class="label" for="status">Stage</label><select id="status" name="status" class="field">@foreach(\App\Models\Application::STAGES as $k => $v)<option value="{{ $k }}" @selected($application->status === $k)>{{ $v }}</option>@endforeach</select></div>
                <div><label class="label" for="comment">Reason / comment <span class="font-normal text-steel-500">(added to timeline on stage change)</span></label><input id="comment" name="comment" maxlength="2000" class="field" placeholder="e.g. Passed screening call"></div>
                <div><label class="label" for="rating">Rating</label><select id="rating" name="rating" class="field"><option value="0">Not rated</option>@for($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected($application->rating == $i)>{{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }}</option>@endfor</select></div>
                <div><label class="label" for="assigned_to">Recruiter</label><select id="assigned_to" name="assigned_to" class="field"><option value="">Unassigned</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected($application->assigned_to == $u->id)>{{ $u->name }}</option>@endforeach</select></div>
                <div><label class="label" for="interview_at">Interview date &amp; time</label><input id="interview_at" type="datetime-local" name="interview_at" value="{{ $application->interview_at?->format('Y-m-d\TH:i') }}" class="field"></div>
                <div><label class="label" for="job_posting_id">Role</label><select id="job_posting_id" name="job_posting_id" class="field"><option value="">General application</option>@foreach($jobs as $j)<option value="{{ $j->id }}" @selected($application->job_posting_id == $j->id)>{{ $j->title }}</option>@endforeach</select></div>
                <button class="admin-btn-primary w-full justify-center">Save</button>
            </form>
            <div class="admin-card p-6 text-sm">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Quick stage</h3>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    @foreach(['screening' => 'admin-btn-secondary', 'interview' => 'admin-btn-secondary', 'assessment' => 'admin-btn-secondary', 'offer' => 'admin-btn-secondary', 'hired' => 'admin-btn bg-green-600 text-white hover:bg-green-700', 'rejected' => 'admin-btn-danger ring-1 ring-steel-200'] as $s => $cls)
                        <form method="POST" action="{{ route('admin.applications.update', $application) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $s }}"><button class="{{ $cls }} w-full justify-center" @disabled($application->status === $s)>{{ \App\Models\Application::STAGES[$s] }}</button></form>
                    @endforeach
                </div>
            </div>
            @if($others->isNotEmpty())<div class="admin-card p-6 text-sm"><h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Other applications</h3><ul class="mt-3 divide-y divide-steel-100">@foreach($others as $o)<li class="py-2"><a href="{{ route('admin.applications.show', $o) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $o->job?->title ?? 'General' }}</a><p class="text-xs text-steel-500">{{ $o->created_at->format('j M Y') }} · {{ $o->stageLabel() }}</p></li>@endforeach</ul></div>@endif
            <div class="admin-card p-6 text-xs text-steel-500"><p>Consent given: {{ $application->consent ? 'Yes' : 'No' }} · IP {{ $application->ip ?? '—' }}</p><p class="mt-1">Stage last changed {{ $application->status_changed_at?->diffForHumans() }}</p>
                @if(auth()->user()->isAdmin())<div class="mt-4"><x-admin-delete :action="route('admin.applications.destroy', $application)" label="Delete application" confirm="Permanently delete this applicant and their CV? This cannot be undone." /></div>@endif</div>
        </aside>
    </div>
</x-layouts.admin>
