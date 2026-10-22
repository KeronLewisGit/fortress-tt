<x-mail-layout title="Application received">
    <p style="margin:0 0 14px;">Dear {{ $application->first_name }},</p>
    <p style="margin:0 0 14px;">Thank you for applying{{ $application->job ? ' for the position of <strong>'.e($application->job->title).'</strong>' : ' to join Fortress Security Services' }}. We have received your application.</p>
    <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.15em;color:#6b7a90;">Your reference</p>
    <p style="margin:0 0 18px;font-size:28px;font-weight:800;color:#0a1a33;">{{ $application->reference }}</p>
    <p style="margin:0 0 14px;"><strong>What happens next:</strong> our recruitment team reviews applications daily and will contact you by phone or email within five business days if you are shortlisted. Please have your Police Certificate of Character, national ID and original certificates ready for interview.</p>
    <p style="margin:0 0 14px;">Questions? Reply to this email or call {{ setting('business.phone') }} and quote your reference.</p>
    <p style="margin:0;">Regards,<br>Recruitment Team<br>{{ fortress('name') }}</p>
</x-mail-layout>
