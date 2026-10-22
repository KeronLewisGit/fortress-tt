<x-mail-layout title="New applicant">
    <p style="margin:0 0 14px;font-size:12px;text-transform:uppercase;letter-spacing:.15em;color:#1f6fe5;">New application</p>
    <h1 style="margin:0 0 6px;font-size:22px;color:#0a1a33;">{{ $application->full_name }}</h1>
    <p style="margin:0 0 18px;color:#6b7a90;">{{ $application->job?->title ?? 'General application' }} · {{ $application->reference }}</p>
    <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.7;">
        <tr><td style="color:#6b7a90;padding-right:16px;">Phone</td><td>{{ $application->phone }}</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">Email</td><td>{{ $application->email }}</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">Area</td><td>{{ $application->area }}</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">Age</td><td>{{ $application->age() ?? '—' }}</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">Permit</td><td>{{ $application->licenceLabel() }}</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">Experience</td><td>{{ $application->years_experience }} yrs</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">Police cert.</td><td>{{ $application->has_police_certificate ? 'Yes' : 'No' }} · Precept: {{ $application->has_precept ? 'Yes' : 'No' }} · FUL: {{ $application->has_firearm_licence ? 'Yes' : 'No' }}</td></tr>
        <tr><td style="color:#6b7a90;padding-right:16px;">CV</td><td>{{ $application->resume_name ?? 'Not attached' }}</td></tr>
    </table>
    @if($application->cover_letter)<p style="margin:18px 0 0;padding:14px;background:#f6f8fb;border-radius:8px;white-space:pre-line;">{{ $application->cover_letter }}</p>@endif
    <p style="margin:22px 0 0;"><a href="{{ route('admin.applications.show', $application) }}" style="display:inline-block;background:#1f6fe5;color:#fff;text-decoration:none;padding:11px 18px;border-radius:6px;font-weight:700;">Open in recruitment portal</a></p>
</x-mail-layout>
