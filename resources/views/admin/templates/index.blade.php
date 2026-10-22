<x-layouts.admin title="Email templates">
    <x-slot:actions><a href="{{ route('admin.templates.create') }}" class="admin-btn-blue">+ New template</a></x-slot:actions>
    <p class="mb-5 text-sm text-steel-500">Templates appear as one-click buttons on every applicant's page. Placeholders: @foreach(\App\Models\MessageTemplate::placeholders() as $p)<code class="rounded bg-steel-100 px-1">{{ $p }}</code> @endforeach</p>
    <div class="admin-card overflow-x-auto"><table class="admin-table"><thead><tr><th>Name</th><th>Subject</th><th>Moves applicant to</th><th></th></tr></thead><tbody>
        @forelse($templates as $t)<tr><td><a href="{{ route('admin.templates.edit', $t) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $t->name }}</a></td><td class="text-steel-600">{{ $t->subject }}</td><td>@if($t->set_status)<x-stage-badge :status="$t->set_status" :label="\App\Models\Application::STAGES[$t->set_status]" />@else<span class="text-steel-400">—</span>@endif</td><td class="text-right"><a href="{{ route('admin.templates.edit', $t) }}" class="admin-btn-secondary">Edit</a></td></tr>
        @empty<tr><td colspan="4" class="py-12 text-center text-steel-500">No templates.</td></tr>@endforelse
    </tbody></table></div>
</x-layouts.admin>
