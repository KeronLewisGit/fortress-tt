<x-layouts.portal title="Notifications">
    <div class="card">
        @forelse($notifications as $n)
            <a href="{{ $n->link ?? '#' }}" class="flex gap-4 border-b border-steel-100 px-5 py-4 last:border-b-0 hover:bg-steel-50">
                <span class="mt-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ ['status' => 'bg-blue-50 text-blue-500', 'interview' => 'bg-amber-400/15 text-amber-500', 'message' => 'bg-violet-50 text-violet-700'][$n->type] ?? 'bg-steel-100 text-steel-500' }}"><x-icon :name="['status' => 'activity', 'interview' => 'calendar', 'message' => 'mail'][$n->type] ?? 'bell'" class="h-4 w-4" /></span>
                <div class="min-w-0 flex-1"><p class="font-semibold text-navy-900">{{ $n->title }}</p>@if($n->body)<p class="mt-0.5 whitespace-pre-line text-sm text-steel-600">{{ $n->body }}</p>@endif<p class="mt-1 text-xs text-steel-500">{{ $n->created_at->format('j M Y, g:i a') }}@if($n->application?->job) · {{ $n->application->job->title }}@endif</p></div>
            </a>
        @empty<div class="px-5 py-12 text-center text-steel-600">No notifications yet.</div>@endforelse
    </div>
    <div class="mt-5">{{ $notifications->links() }}</div>
</x-layouts.portal>
