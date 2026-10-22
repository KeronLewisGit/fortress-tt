<x-layouts.portal title="My CV & profile">
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="card p-6 sm:p-8"><h2 class="h3 text-navy-900">My CV / résumé</h2><p class="mt-1 text-sm text-steel-600">PDF or Word, up to 5 MB. Your latest CV is shared with recruiters on your active applications.</p>
            @if($applicant->resume_path)<div class="mt-4 flex flex-wrap items-center gap-3 rounded-lg border border-steel-200 p-3.5 text-sm"><x-icon name="file" class="h-5 w-5 text-blue-500" /><span class="min-w-0 flex-1 truncate font-medium">{{ $applicant->resume_name }}</span><span class="text-xs text-steel-500">Updated {{ $applicant->resume_updated_at?->format('j M Y') }}</span><a href="{{ route('portal.resume.download') }}" class="font-semibold text-blue-500">Download</a></div>@endif
            <form method="POST" action="{{ route('portal.resume.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3">@csrf
                <input type="file" name="resume" accept=".pdf,.doc,.docx" required class="field file:mr-3 file:rounded file:border-0 file:bg-navy-900 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white"><x-field-error name="resume" />
                <button class="btn-primary">{{ $applicant->resume_path ? 'Replace CV' : 'Upload CV' }}</button>
            </form>
            @if($applicant->resume_path)<form method="POST" action="{{ route('portal.resume.destroy') }}" class="mt-3" onsubmit="return confirm('Remove your CV from your profile? CVs already attached to applications are kept for recruiters.')">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-600">Remove CV</button></form>@endif
        </div>
        <form method="POST" action="{{ route('portal.profile.update') }}" class="card space-y-4 p-6 sm:p-8">@csrf @method('PUT')
            <h2 class="h3 text-navy-900">Profile</h2>
            <div class="grid gap-4 sm:grid-cols-2"><div><label class="label" for="first_name">First name</label><input id="first_name" name="first_name" value="{{ old('first_name', $applicant->first_name) }}" required class="field"></div><div><label class="label" for="last_name">Last name</label><input id="last_name" name="last_name" value="{{ old('last_name', $applicant->last_name) }}" required class="field"></div></div>
            <div><label class="label">Email</label><input value="{{ $applicant->email }}" disabled class="field bg-steel-50 text-steel-500"><p class="help">Your email links your applications and cannot be changed here. Contact recruitment if it needs updating.</p></div>
            <div><label class="label" for="phone">Phone / WhatsApp</label><input id="phone" type="tel" name="phone" value="{{ old('phone', $applicant->phone) }}" class="field"></div>
            <hr class="border-steel-200"><p class="text-sm font-semibold text-steel-700">Change password (optional)</p>
            <div><label class="label" for="current_password">Current password</label><input id="current_password" type="password" name="current_password" autocomplete="current-password" class="field"></div>
            <div class="grid gap-4 sm:grid-cols-2"><div><label class="label" for="password">New password</label><input id="password" type="password" name="password" minlength="8" autocomplete="new-password" class="field"></div><div><label class="label" for="password_confirmation">Confirm</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="field"></div></div>
            <button class="btn-primary">Save profile</button>
        </form>
    </div>
</x-layouts.portal>
