<x-portal-auth-card title="Reset your password" subtitle="Enter the email you registered with and we will send a reset link.">
    <form method="POST" action="{{ route('portal.password.email') }}" class="space-y-4">@csrf
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="field"><x-field-error name="email" /></div>
        <button class="btn-primary w-full">Email reset link</button>
        <p class="text-center text-sm"><a href="{{ route('portal.login') }}" class="font-semibold text-blue-500">← Back to sign in</a></p>
    </form>
</x-portal-auth-card>
