<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>{{ $title ?? fortress('name') }}</title></head>
<body style="margin:0;background:#eef2f7;font-family:Inter,Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:28px 12px;">
<table role="presentation" width="600" style="max-width:600px;width:100%;background:#fff;border-radius:12px;overflow:hidden;">
    <tr><td style="background:#0a1a33;padding:20px 28px;"><span style="font-family:'Barlow Condensed',Arial Narrow,Arial,sans-serif;font-size:22px;font-weight:800;letter-spacing:.08em;color:#fff;text-transform:uppercase;">Fortress</span> <span style="font-size:11px;letter-spacing:.25em;color:#7fb2ff;text-transform:uppercase;">Security Services</span></td></tr>
    <tr><td style="padding:28px;font-size:15px;line-height:1.6;">{{ $slot }}</td></tr>
    <tr><td style="background:#f6f8fb;padding:18px 28px;font-size:12px;color:#6b7a90;line-height:1.5;">{{ fortress('name') }} · 8 Desaine Street, St. Augustine, Trinidad &amp; Tobago · {{ setting('business.phone') }}<br>{{ fortress('motto') }}</td></tr>
</table></td></tr></table></body></html>
