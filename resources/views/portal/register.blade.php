<x-portal-auth-card title="Create your applicant account" subtitle="Use the same email you applied with and we will link your existing applications automatically.">
    <form method="POST" action="{{ route('portal.register') }}" class="space-y-4">@csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="label" for="first_name">First name</label><input id="first_name" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" class="field"><x-field-error name="first_name" /></div>
            <div><label class="label" for="last_name">Last name</label><input id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" class="field"><x-field-error name="last_name" /></div>
        </div>
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" class="field"><x-field-error name="email" /></div>
        <div><label class="label" for="phone">Phone / WhatsApp</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="868-" class="field"></div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="label" for="password">Password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="field"><x-field-error name="password" /></div>
            <div><label class="label" for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field"></div>
        </div>
        <button class="btn-amber w-full">Create account</button>
        <p class="text-center text-sm text-steel-600">Already registered? <a href="{{ route('portal.login') }}" class="font-semibold text-blue-500">Sign in</a></p>
    </form>
</x-portal-auth-card>
