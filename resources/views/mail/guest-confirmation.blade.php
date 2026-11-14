<!doctype html>
<html lang="en"><body style="margin:0;padding:24px;background:#f6f1e8;font-family:Arial,Helvetica,sans-serif;color:#1d1a16">
<table role="presentation" width="100%" style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:24px;font-size:15px;line-height:1.6">
<tr><td>
<p style="margin:0 0 16px">Dear {{ $name ?: 'traveler' }},</p>
<p style="margin:0 0 16px">Thank you for writing to We Are Morocco. Your note is with our atelier in Marrakech, and a producer will reply within one business day, usually sooner.</p>
<p style="margin:0 0 16px">If your dates are close, write us at <a href="mailto:{{ $site['email'] ?? '' }}" style="color:#9a7b4f">{{ $site['email'] ?? '' }}</a> or WhatsApp {{ $site['phone_display'] ?? '' }}.</p>
<p style="margin:0">The We Are Morocco atelier</p>
</td></tr></table>
</body></html>
