<x-portal-auth-card title="Choose a new password">
    <form method="POST" action="{{ route('portal.password.update') }}" class="space-y-4">@csrf<input type="hidden" name="token" value="{{ $token }}">
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" required class="field"><x-field-error name="email" /></div>
        <div><label class="label" for="password">New password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="field"><x-field-error name="password" /></div>
        <div><label class="label" for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field"></div>
        <button class="btn-primary w-full">Reset password</button>
    </form>
</x-portal-auth-card>
