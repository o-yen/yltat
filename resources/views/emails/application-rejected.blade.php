<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.email_rejected_subject') }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f6fb;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #dbe4f0;border-radius:20px;overflow:hidden;">
                    <tr>
                        <td style="background:#1e3a5f;padding:20px 28px;">
                            <div style="font-size:22px;line-height:1.2;font-weight:700;color:#ffffff;">{{ __('common.email_talent_welcome_brand') }}</div>
                            <div style="font-size:13px;line-height:1.5;color:#c7d4e6;margin-top:4px;">{{ __('common.email_talent_welcome_tagline') }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px;">
                            <div style="font-size:22px;line-height:1.3;font-weight:700;color:#111827;margin-bottom:16px;">{{ __('common.email_rejected_heading') }}</div>

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_rejected_greeting', ['name' => $talent->full_name]) }}
                            </p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_rejected_body') }}
                            </p>

                            {{-- Reason box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #fecaca;border-radius:12px;background:#fef2f2;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">{{ __('common.email_rejected_reason_label') }}</div>
                                        <div style="font-size:14px;color:#991b1b;line-height:1.6;">{{ $reason }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_rejected_contact') }}
                            </p>

                            <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#94a3b8;">
                                {{ __('common.email_rejected_footer') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;">
                            <p style="margin:0;font-size:11px;color:#94a3b8;text-align:center;">
                                {{ __('common.email_talent_welcome_footer') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
