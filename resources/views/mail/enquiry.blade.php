<x-mail-layout title="Website enquiry">
    <p style="margin:0 0 14px;font-size:12px;text-transform:uppercase;letter-spacing:.15em;color:#1f6fe5;">Website enquiry</p>
    <h1 style="margin:0 0 6px;font-size:22px;color:#0a1a33;">{{ $enquiry->name }}@if($enquiry->company) · {{ $enquiry->company }}@endif</h1>
    <p style="margin:0 0 18px;color:#6b7a90;">{{ $enquiry->service ?: 'General' }}@if($enquiry->location) · {{ $enquiry->location }}@endif</p>
    <p style="margin:0 0 6px;">Email: <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></p>
    @if($enquiry->phone)<p style="margin:0 0 6px;">Phone: {{ $enquiry->phone }}</p>@endif
    <p style="margin:18px 0 0;padding:14px;background:#f6f8fb;border-radius:8px;white-space:pre-line;">{{ $enquiry->message }}</p>
    <p style="margin:22px 0 0;"><a href="{{ route('admin.enquiries.show', $enquiry) }}" style="display:inline-block;background:#1f6fe5;color:#fff;text-decoration:none;padding:11px 18px;border-radius:6px;font-weight:700;">Open enquiry</a></p>
</x-mail-layout>
