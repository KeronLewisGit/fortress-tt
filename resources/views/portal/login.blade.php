<x-portal-auth-card title="Applicant sign in" subtitle="Check where your application stands, book your interview and manage your CV.">
    <form method="POST" action="{{ route('portal.login') }}" class="space-y-4">@csrf
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field"><x-field-error name="email" /></div>
        <div><label class="label" for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password" class="field"></div>
        <div class="flex items-center justify-between text-sm"><label class="flex items-center gap-2 text-steel-600"><input type="checkbox" name="remember" class="check">Keep me signed in</label><a href="{{ route('portal.password.request') }}" class="font-semibold text-blue-500">Forgot password?</a></div>
        <button class="btn-primary w-full">Sign in</button>
        <p class="text-center text-sm text-steel-600">New here? <a href="{{ route('portal.register') }}" class="font-semibold text-blue-500">Create your applicant account</a></p>
        <p class="text-center text-xs text-steel-500">Fortress staff sign in <a href="{{ route('login') }}" class="underline">here</a>.</p>
    </form>
</x-portal-auth-card>
