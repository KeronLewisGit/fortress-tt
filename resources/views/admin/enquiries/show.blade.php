<x-layouts.admin :title="'Enquiry – '.$enquiry->name">
    <x-slot:actions><a href="mailto:{{ $enquiry->email }}?subject=Re: your Fortress Security enquiry" class="admin-btn-secondary"><x-icon name="mail" class="h-4 w-4" />Reply</a>@if($enquiry->phone)<a href="{{ tel_href($enquiry->phone) }}" class="admin-btn-secondary"><x-icon name="phone" class="h-4 w-4" />Call</a>@endif</x-slot:actions>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="admin-card p-6 lg:col-span-2">
            <div class="flex flex-wrap items-center gap-2"><h2 class="font-display text-2xl font-bold text-navy-900">{{ $enquiry->name }}</h2><x-stage-badge :status="$enquiry->status" :label="\App\Models\Enquiry::STATUSES[$enquiry->status]" /></div>
            <p class="mt-1 text-sm text-steel-500">Received {{ $enquiry->created_at->format('l j F Y, g:i a') }}@if($enquiry->ip) · IP {{ $enquiry->ip }}@endif</p>
            <dl class="mt-5 grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div><dt class="text-steel-500">Company</dt><dd class="font-medium">{{ $enquiry->company ?: '—' }}</dd></div>
                <div><dt class="text-steel-500">Service</dt><dd class="font-medium">{{ $enquiry->service ?: '—' }}</dd></div>
                <div><dt class="text-steel-500">Email</dt><dd><a href="mailto:{{ $enquiry->email }}" class="font-medium text-blue-500">{{ $enquiry->email }}</a></dd></div>
                <div><dt class="text-steel-500">Phone</dt><dd class="font-medium">{{ $enquiry->phone ?: '—' }}</dd></div>
                <div><dt class="text-steel-500">Site location</dt><dd class="font-medium">{{ $enquiry->location ?: '—' }}</dd></div>
            </dl>
            <p class="mt-6 whitespace-pre-line rounded-lg bg-steel-50 p-4 text-sm text-steel-800">{{ $enquiry->message }}</p>
        </div>
        <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="admin-card p-6 space-y-4">@csrf @method('PUT')
            <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Follow-up</h3>
            <div><label class="label" for="status">Status</label><select id="status" name="status" class="field">@foreach(\App\Models\Enquiry::STATUSES as $k => $v)<option value="{{ $k }}" @selected($enquiry->status === $k)>{{ $v }}</option>@endforeach</select></div>
            <div><label class="label" for="assigned_to">Owner</label><select id="assigned_to" name="assigned_to" class="field"><option value="">Unassigned</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected($enquiry->assigned_to == $u->id)>{{ $u->name }}</option>@endforeach</select></div>
            <div><label class="label" for="notes">Internal notes</label><textarea id="notes" name="notes" rows="7" class="field text-sm" placeholder="Site survey date, quote value, next call…">{{ old('notes', $enquiry->notes) }}</textarea></div>
            <button class="admin-btn-primary w-full justify-center">Save</button>
            <div class="pt-2 text-right"><x-admin-delete :action="route('admin.enquiries.destroy', $enquiry)" /></div>
        </form>
    </div>
</x-layouts.admin>
