<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f6fb;padding:32px 16px;">
<tr><td align="center">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border:1px solid #dbe4f0;border-radius:20px;overflow:hidden;">
    <tr><td style="background:#1e3a5f;padding:20px 28px;">
        <div style="font-size:22px;font-weight:700;color:#fff;">{{ __('common.email_talent_welcome_brand') }}</div>
        <div style="font-size:13px;color:#c7d4e6;margin-top:4px;">{{ __('common.email_talent_welcome_tagline') }}</div>
    </td></tr>
    <tr><td style="padding:32px 28px;">
        <div style="font-size:22px;font-weight:700;color:#111827;margin-bottom:16px;">{{ __('common.email_payment_heading') }}</div>

        <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
            {{ __('common.email_payment_greeting', ['name' => $talentName]) }}
        </p>
        <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
            {{ __('common.email_payment_body', ['month' => $payment->bulan, 'year' => $payment->tahun]) }}
        </p>

        @php
            $statusColor = match($status) {
                'Selesai' => '#10b981',
                'Lewat' => '#ef4444',
                default => '#f59e0b',
            };
        @endphp

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #dbe4f0;border-radius:12px;background:#f8fbff;">
        <tr><td style="padding:16px 20px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr><td style="padding:4px 0;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_payment_period') }}</span><br><span style="font-size:15px;font-weight:600;color:#1e3a5f;">{{ $payment->bulan }} {{ $payment->tahun }}</span></td></tr>
                @if($payment->elaun_prorate)
                <tr><td style="padding:4px 0;border-top:1px solid #e2e8f0;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_payment_amount') }}</span><br><span style="font-size:18px;font-weight:700;color:#1e3a5f;">RM {{ number_format($payment->elaun_prorate, 2) }}</span></td></tr>
                @endif
                <tr><td style="padding:4px 0;border-top:1px solid #e2e8f0;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_payment_status') }}</span><br><span style="font-size:14px;font-weight:600;color:{{ $statusColor }};">{{ $status }}</span></td></tr>
                @if($payment->tarikh_bayar)
                <tr><td style="padding:4px 0;border-top:1px solid #e2e8f0;"><span style="font-size:12px;color:#6b7280;">{{ __('common.email_payment_date') }}</span><br><span style="font-size:14px;color:#374151;">{{ \Carbon\Carbon::parse($payment->tarikh_bayar)->format('d/m/Y') }}</span></td></tr>
                @endif
            </table>
        </td></tr></table>

        <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#94a3b8;">{{ __('common.email_payment_footer') }}</p>
    </td></tr>
    <tr><td style="background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;">
        <p style="margin:0;font-size:11px;color:#94a3b8;text-align:center;">{{ __('common.email_talent_welcome_footer') }}</p>
    </td></tr>
</table>
</td></tr></table>
</body></html>
