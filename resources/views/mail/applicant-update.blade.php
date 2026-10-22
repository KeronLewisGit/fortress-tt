<x-mail-layout :title="$title">
    <p style="margin:0 0 14px;">Dear {{ $application->first_name }},</p>
    <h1 style="margin:0 0 12px;font-size:20px;color:#0a1a33;">{{ $title }}</h1>
    <p style="margin:0 0 16px;white-space:pre-line;">{{ $body }}</p>
    <p style="margin:0 0 18px;"><a href="{{ $application->applicant_id ? route('portal.applications.show', $application->reference) : route('portal.register', ['email' => $application->email]) }}" style="display:inline-block;background:#1f6fe5;color:#fff;text-decoration:none;padding:11px 18px;border-radius:6px;font-weight:700;">{{ $application->applicant_id ? 'Open your applicant portal' : 'Track this application online' }}</a></p>
    <p style="margin:0;font-size:12px;color:#6b7a90;">Reference {{ $application->reference }} · Questions? Reply to this email or call {{ setting('business.phone') }}.</p>
</x-mail-layout>
