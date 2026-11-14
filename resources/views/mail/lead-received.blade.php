<!doctype html>
<html lang="en"><body style="margin:0;padding:24px;background:#f6f1e8;font-family:Arial,Helvetica,sans-serif;color:#1d1a16">
<table role="presentation" width="100%" style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;padding:24px">
<tr><td>
<p style="margin:0 0 4px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#9a7b4f">New {{ strtolower($label) }}</p>
<h1 style="margin:0 0 20px;font-size:20px">{{ $lead->name }}</h1>
<table role="presentation" width="100%" cellpadding="6" style="border-collapse:collapse;font-size:14px">
@foreach($fields as $name => $value)
<tr><td style="vertical-align:top;width:140px;color:#6b6257;border-top:1px solid #eee">{{ $name }}</td><td style="border-top:1px solid #eee;white-space:pre-wrap">{{ $value }}</td></tr>
@endforeach
</table>
<p style="margin:20px 0 0;font-size:14px">Reply to this email to answer {{ $lead->name }} directly, or <a href="{{ $adminUrl }}" style="color:#9a7b4f">open it in the admin</a>.</p>
</td></tr></table>
</body></html>
