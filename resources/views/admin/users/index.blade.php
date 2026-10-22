<x-layouts.admin title="Users">
    <x-slot:actions><a href="{{ route('admin.users.create') }}" class="admin-btn-blue">+ Add user</a></x-slot:actions>
    <div class="admin-card overflow-x-auto"><table class="admin-table"><thead><tr><th>Name</th><th>Role</th><th>Assigned applicants</th><th>Last sign-in</th><th>Status</th><th></th></tr></thead><tbody>
        @foreach($users as $u)<tr><td><a href="{{ route('admin.users.edit', $u) }}" class="font-semibold text-navy-900 hover:text-blue-500">{{ $u->name }}</a><p class="text-xs text-steel-500">{{ $u->email }}</p></td><td>{{ \App\Models\User::ROLES[$u->role] ?? $u->role }}</td><td>{{ $u->assigned_applications_count }}</td><td class="text-steel-500">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td><td>@if($u->is_active)<span class="chip-green">Active</span>@else<span class="chip">Disabled</span>@endif</td><td class="text-right"><a href="{{ route('admin.users.edit', $u) }}" class="admin-btn-secondary">Edit</a></td></tr>@endforeach
    </tbody></table></div>
    <p class="mt-4 text-sm text-steel-500"><strong>Recruiters</strong> manage jobs, applicants, email templates and enquiries. <strong>Administrators</strong> can also edit services, settings and users, and delete applicants.</p>
</x-layouts.admin>
