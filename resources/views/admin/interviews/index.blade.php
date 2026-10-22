<x-layouts.admin title="Interviews">
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="admin-card">
                <div class="flex items-center justify-between border-b border-steel-200 px-5 py-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Scheduled interviews</h2><span class="text-sm text-steel-500">{{ $scheduled->count() }} upcoming</span></div>
                <div class="overflow-x-auto"><table class="admin-table"><thead><tr><th>When</th><th>Applicant</th><th>Role</th><th>Location</th><th>Recruiter</th></tr></thead><tbody>
                    @forelse($scheduled as $a)<tr><td class="whitespace-nowrap font-semibold text-navy-900">{{ $a->interview_at->format('D j M') }}<br><span class="text-xs font-normal text-steel-500">{{ $a->interview_at->format('g:i a') }}</span></td><td><a href="{{ route('admin.applications.show', $a) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $a->full_name }}</a><p class="text-xs text-steel-500">{{ $a->phone }}</p></td><td class="text-steel-600">{{ $a->job?->title ?? 'General' }}</td><td class="text-steel-600">{{ $a->interview_location ?: '—' }}</td><td class="text-steel-600">{{ $a->assignee?->name ?? '—' }}</td></tr>
                    @empty<tr><td colspan="5" class="py-10 text-center text-steel-500">No interviews scheduled.</td></tr>@endforelse
                </tbody></table></div>
            </div>
            <div class="admin-card">
                <div class="flex items-center justify-between border-b border-steel-200 px-5 py-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Open interview slots</h2><span class="text-sm text-steel-500">Applicants at the Interview stage book these from their portal</span></div>
                <div class="overflow-x-auto"><table class="admin-table"><thead><tr><th>Slot</th><th>Location</th><th>Seats</th><th>Booked by</th><th></th></tr></thead><tbody>
                    @forelse($slots as $s)<tr><td class="whitespace-nowrap font-semibold text-navy-900">{{ $s->starts_at->format('D j M, g:i a') }}<br><span class="text-xs font-normal text-steel-500">{{ $s->duration_minutes }} min{{ $s->notes ? ' · '.$s->notes : '' }}</span></td><td class="text-steel-600">{{ $s->location }}</td><td><span class="{{ $s->seatsLeft() ? 'chip-green' : 'chip' }}">{{ $s->bookings_count }}/{{ $s->capacity }} booked</span></td><td class="text-sm">@forelse($s->bookings as $b)<a href="{{ route('admin.applications.show', $b) }}" class="block font-semibold text-navy-900 hover:text-blue-500">{{ $b->full_name }}</a>@empty<span class="text-steel-400">—</span>@endforelse</td><td class="text-right">@unless($s->bookings_count)<x-admin-delete :action="route('admin.interviews.destroy', $s)" label="Remove" confirm="Remove this interview slot?" />@endunless</td></tr>
                    @empty<tr><td colspan="5" class="py-10 text-center text-steel-500">No open slots. Add some on the right so invited applicants can self-book.</td></tr>@endforelse
                </tbody></table></div>
            </div>
        </div>
        <aside class="space-y-6">
            <form method="POST" action="{{ route('admin.interviews.store') }}" class="admin-card space-y-4 p-5">@csrf
                <h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Add interview slots</h2>
                <div class="grid grid-cols-2 gap-3"><div><label class="label" for="date">Date</label><input id="date" type="date" name="date" value="{{ old('date', today()->addDay()->toDateString()) }}" min="{{ today()->toDateString() }}" required class="field"></div><div><label class="label" for="time">First slot</label><input id="time" type="time" name="time" value="{{ old('time', '09:00') }}" required class="field"></div></div>
                <div class="grid grid-cols-2 gap-3"><div><label class="label" for="duration_minutes">Length (min)</label><input id="duration_minutes" type="number" name="duration_minutes" value="{{ old('duration_minutes', 30) }}" min="10" max="240" step="5" required class="field"></div><div><label class="label" for="repeat">How many in a row</label><input id="repeat" type="number" name="repeat" value="{{ old('repeat', 4) }}" min="1" max="12" required class="field"></div></div>
                <div><label class="label" for="capacity">Applicants per slot</label><input id="capacity" type="number" name="capacity" value="{{ old('capacity', 1) }}" min="1" max="20" required class="field"></div>
                <div><label class="label" for="location">Location</label><input id="location" name="location" value="{{ old('location', 'Head Office – 8 Desaine Street, St. Augustine') }}" required maxlength="255" class="field"></div>
                <div><label class="label" for="notes">Note (optional)</label><input id="notes" name="notes" value="{{ old('notes') }}" maxlength="255" placeholder="e.g. Bring original certificates" class="field"></div>
                <button class="admin-btn-blue w-full justify-center">Add slots</button>
                <p class="help">Back-to-back slots are created from the first time. Applicants are emailed when they book.</p>
            </form>
            <div class="admin-card p-5"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Awaiting a time</h2><p class="text-xs text-steel-500">Invited to interview, no date yet.</p>
                <ul class="mt-3 divide-y divide-steel-100">@forelse($awaiting as $a)<li class="py-2 text-sm"><a href="{{ route('admin.applications.show', $a) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $a->full_name }}</a><p class="text-xs text-steel-500">{{ $a->job?->title ?? 'General' }} · {{ $a->applicant_id ? 'has portal account' : 'no portal account – call to arrange' }}</p></li>@empty<li class="py-4 text-sm text-steel-500">Nobody waiting.</li>@endforelse</ul></div>
        </aside>
    </div>
</x-layouts.admin>
