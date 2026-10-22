<x-layouts.admin :title="$user->exists ? 'Edit user' : 'Add user'">
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-card mx-auto max-w-xl p-6 space-y-4">@csrf @if($user->exists)@method('PUT')@endif
        <div><label class="label" for="name">Name *</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required class="field"></div>
        <div><label class="label" for="email">Email *</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="field"></div>
        <div><label class="label" for="password">{{ $user->exists ? 'New password (leave blank to keep)' : 'Password *' }}</label><input id="password" type="password" name="password" minlength="8" @required(! $user->exists) autocomplete="new-password" class="field"></div>
        <div><label class="label" for="role">Role</label><select id="role" name="role" class="field" @disabled($user->exists && $user->id === auth()->id())>@foreach(\App\Models\User::ROLES as $k => $v)<option value="{{ $k }}" @selected(old('role', $user->role ?? 'recruiter') === $k)>{{ $v }}</option>@endforeach</select></div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->exists ? $user->is_active : true)) @disabled($user->exists && $user->id === auth()->id()) class="check">Active (can sign in)</label>
        <div class="flex items-center gap-2 pt-2"><button class="admin-btn-primary">Save</button><a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">Cancel</a>@if($user->exists && $user->id !== auth()->id())<span class="ml-auto"><x-admin-delete :action="route('admin.users.destroy', $user)" /></span>@endif</div>
    </form>
</x-layouts.admin>
