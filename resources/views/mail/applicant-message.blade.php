<x-mail-layout :title="$subjectLine">
    <div style="white-space:pre-line;">{{ $body }}</div>
    <p style="margin:22px 0 0;font-size:12px;color:#6b7a90;">Application reference: {{ $application->reference }}</p>
</x-mail-layout>
