<x-layouts.admin title="My profile">
    <form method="POST" action="{{ route('admin.profile.update') }}" class="admin-card max-w-xl p-6 space-y-4">@csrf @method('PUT')
        <div><label class="label" for="name">Name</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required class="field"></div>
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="field"></div>
        <hr class="border-steel-200">
        <p class="text-sm font-semibold text-steel-700">Change password</p>
        <div><label class="label" for="current_password">Current password</label><input id="current_password" type="password" name="current_password" autocomplete="current-password" class="field"></div>
        <div class="grid gap-4 sm:grid-cols-2"><div><label class="label" for="password">New password</label><input id="password" type="password" name="password" minlength="8" autocomplete="new-password" class="field"></div><div><label class="label" for="password_confirmation">Confirm</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="field"></div></div>
        <button class="admin-btn-primary">Save</button>
    </form>
</x-layouts.admin>
