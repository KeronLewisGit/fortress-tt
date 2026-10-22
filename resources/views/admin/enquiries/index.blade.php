<x-layouts.admin title="Quote enquiries">
    <div class="mb-5 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.enquiries.index') }}" class="rounded-full px-3.5 py-1.5 text-sm font-semibold {{ ! request('status') ? 'bg-navy-900 text-white' : 'bg-white text-steel-600 ring-1 ring-steel-200' }}">All <span class="opacity-60">{{ $counts->sum() }}</span></a>
        @foreach(\App\Models\Enquiry::STATUSES as $k => $label)<a href="{{ route('admin.enquiries.index', ['status' => $k]) }}" class="rounded-full px-3.5 py-1.5 text-sm font-semibold {{ request('status') === $k ? 'bg-navy-900 text-white' : 'bg-white text-steel-600 ring-1 ring-steel-200 hover:bg-steel-50' }}">{{ $label }} <span class="opacity-60">{{ $counts[$k] ?? 0 }}</span></a>@endforeach
        <form class="ml-auto"><input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, company, email" class="field w-64 !py-1.5"><input type="hidden" name="status" value="{{ request('status') }}"></form>
    </div>
    <div class="admin-card overflow-x-auto"><table class="admin-table"><thead><tr><th>Contact</th><th>Service</th><th>Message</th><th>Status</th><th>Received</th></tr></thead><tbody>
        @forelse($enquiries as $e)<tr class="{{ $e->status === 'new' ? 'bg-amber-400/5' : '' }}"><td><a href="{{ route('admin.enquiries.show', $e) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $e->name }}</a><p class="text-xs text-steel-500">{{ $e->company }}{{ $e->company && $e->phone ? ' · ' : '' }}{{ $e->phone }}</p></td><td class="text-steel-600">{{ $e->service ?: '—' }}<br><span class="text-xs text-steel-500">{{ $e->location }}</span></td><td class="max-w-md text-steel-600"><p class="line-clamp-2 text-sm">{{ $e->message }}</p></td><td><x-stage-badge :status="$e->status" :label="\App\Models\Enquiry::STATUSES[$e->status]" /></td><td class="whitespace-nowrap text-steel-500">{{ $e->created_at->format('j M, g:i a') }}</td></tr>
        @empty<tr><td colspan="5" class="py-12 text-center text-steel-500">No enquiries yet.</td></tr>@endforelse
    </tbody></table></div>
    <div class="mt-5">{{ $enquiries->links() }}</div>
</x-layouts.admin>
