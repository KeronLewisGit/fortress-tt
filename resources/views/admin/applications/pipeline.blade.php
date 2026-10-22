<x-layouts.admin title="Hiring pipeline" wide>
    <x-slot:actions>
        <form class="flex gap-2"><select name="job" class="field !py-1.5 w-56 max-w-[60vw]" onchange="this.form.submit()"><option value="">All jobs</option><option value="general" @selected(request('job') === 'general')>General applications</option>@foreach($jobs as $j)<option value="{{ $j->id }}" @selected(request('job') == $j->id)>{{ $j->title }}</option>@endforeach</select><select name="assigned" class="field !py-1.5" onchange="this.form.submit()"><option value="">Any recruiter</option><option value="me" @selected(request('assigned') === 'me')>Mine</option><option value="none" @selected(request('assigned') === 'none')>Unassigned</option></select></form>
        <a href="{{ route('admin.applications.index', request()->query()) }}" class="admin-btn-secondary"><x-icon name="list" class="h-4 w-4" />List</a>
    </x-slot:actions>
    <div x-data="pipeline" class="relative">
        <div x-show="toast" x-cloak x-transition class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-lg bg-navy-900 px-4 py-2.5 text-sm font-medium text-white shadow-xl" x-text="toast"></div>
        <p class="mb-5 text-sm text-steel-500">Drag a card to move an applicant to a new stage. Hired: <a href="{{ route('admin.applications.index', ['status' => 'hired']) }}" class="font-semibold text-green-700">{{ $closedCounts['hired'] }}</a> · Rejected: <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" class="font-semibold text-steel-600">{{ $closedCounts['rejected'] }}</a></p>
        <div class="flex gap-4 overflow-x-auto pb-4">
            @foreach($columns as $stage => $apps)
                <section class="kanban-col" data-stage="{{ $stage }}" :class="over === '{{ $stage }}' ? 'drag-over' : ''" @dragover.prevent="over = '{{ $stage }}'" @dragleave.self="over = null" @drop.prevent="drop('{{ $stage }}')">
                    <header class="flex items-center justify-between px-2 py-1.5"><h2 class="flex items-center gap-2 text-sm font-bold text-navy-900"><x-stage-badge :status="$stage" :label="\App\Models\Application::STAGES[$stage]" /></h2><span class="rounded-full bg-white px-2 py-0.5 text-xs font-bold text-steel-600" data-count>{{ $apps->count() }}</span></header>
                    <div class="flex min-h-[60vh] flex-col gap-2" data-list>
                        <p class="rounded-lg border border-dashed border-steel-300 p-4 text-center text-xs text-steel-400 {{ $apps->count() ? 'hidden' : '' }}" data-empty>Drop applicants here</p>
                        @foreach($apps as $a)
                            <article id="app-{{ $a->id }}" class="kanban-card" draggable="true" data-url="{{ route('admin.applications.update', $a) }}" data-name="{{ $a->full_name }}" @dragstart="start($event, {{ $a->id }})" @dragend="end()" :class="dragging === {{ $a->id }} ? 'opacity-40' : ''">
                                <div class="flex items-start justify-between gap-2"><a href="{{ route('admin.applications.show', $a) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $a->full_name }}</a><x-stars :rating="$a->rating" class="h-3 w-3" /></div>
                                <p class="mt-0.5 text-xs text-steel-500">{{ $a->job?->title ?? 'General application' }}</p>
                                <div class="mt-2 flex flex-wrap gap-1 text-[11px]">
                                    <span class="chip !py-0">{{ $a->area }}</span><span class="chip !py-0">{{ $a->licenceLabel() }}</span>@if($a->has_police_certificate)<span class="chip-green !py-0">PCC</span>@endif @if($a->has_precept)<span class="chip-blue !py-0">Precept</span>@endif @if($a->resume_path)<span class="chip !py-0">CV</span>@endif
                                </div>
                                <div class="mt-2 flex items-center justify-between text-[11px] text-steel-500"><span>{{ $a->assignee?->name ?? 'Unassigned' }}</span><span>{{ $a->status_changed_at?->diffForHumans(short: true) }}</span></div>
                                @if($a->interview_at && $stage === 'interview')<p class="mt-1.5 rounded bg-amber-400/15 px-2 py-1 text-[11px] font-semibold text-amber-500">Interview {{ $a->interview_at->format('D j M, g:i a') }}</p>@endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
