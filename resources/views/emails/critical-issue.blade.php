<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f6fb;padding:32px 16px;">
<tr><td align="center">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border:1px solid #dbe4f0;border-radius:20px;overflow:hidden;">
    <tr><td style="background:#dc2626;padding:20px 28px;">
        <div style="font-size:22px;font-weight:700;color:#fff;">{{ __('common.email_critical_alert') }}</div>
        <div style="font-size:13px;color:#fecaca;margin-top:4px;">{{ __('common.email_talent_welcome_tagline') }}</div>
    </td></tr>
    <tr><td style="padding:32px 28px;">
        <div style="font-size:22px;font-weight:700;color:#111827;margin-bottom:16px;">{{ __('common.email_critical_heading') }}</div>

        <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
            {{ __('common.email_critical_body') }}
        </p>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #fecaca;border-radius:12px;background:#fef2f2;">
        <tr><td style="padding:16px 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr><td style="padding:4px 0;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_critical_id') }}</span><br><span style="font-size:15px;font-weight:700;color:#dc2626;">{{ $issue->id_isu }}</span></td></tr>
                <tr><td style="padding:4px 0;border-top:1px solid #fecaca;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_critical_category') }}</span><br><span style="font-size:14px;color:#374151;">{{ $issue->kategori_isu }}</span></td></tr>
                <tr><td style="padding:4px 0;border-top:1px solid #fecaca;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_critical_risk') }}</span><br><span style="font-size:14px;font-weight:700;color:#dc2626;">{{ $issue->tahap_risiko }}</span></td></tr>
                <tr><td style="padding:4px 0;border-top:1px solid #fecaca;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_critical_description') }}</span><br><span style="font-size:14px;color:#374151;">{{ $issue->butiran_isu }}</span></td></tr>
                @if($issue->pic)
                <tr><td style="padding:4px 0;border-top:1px solid #fecaca;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_critical_pic') }}</span><br><span style="font-size:14px;color:#374151;">{{ $issue->pic }}</span></td></tr>
                @endif
            </table>
        </td></tr></table>

        <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">{{ __('common.email_critical_action') }}</p>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0;">
        <tr><td align="center">
            <a href="{{ route('login') }}" style="display:inline-block;background:#dc2626;color:#fff;text-decoration:none;font-size:15px;font-weight:600;padding:14px 36px;border-radius:12px;">{{ __('common.email_critical_cta') }}</a>
        </td></tr></table>
    </td></tr>
    <tr><td style="background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;">
        <p style="margin:0;font-size:11px;color:#94a3b8;text-align:center;">{{ __('common.email_talent_welcome_footer') }}</p>
    </td></tr>
</table>
</td></tr></table>
</body></html>
