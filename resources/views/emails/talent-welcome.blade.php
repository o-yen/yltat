<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.email_talent_welcome_title') }}</title>
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
                            {{-- Confirmation icon --}}
                            <div style="text-align:center;margin-bottom:24px;">
                                <div style="display:inline-block;width:64px;height:64px;border-radius:50%;background:#dbeafe;line-height:64px;text-align:center;">
                                    <span style="font-size:32px;">&#9993;</span>
                                </div>
                            </div>

                            <div style="font-size:24px;line-height:1.3;font-weight:700;color:#111827;margin-bottom:16px;text-align:center;">{{ __('common.email_talent_welcome_heading') }}</div>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_talent_welcome_greeting', ['name' => $talent->full_name]) }}
                            </p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_talent_welcome_body') }}
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #dbe4f0;border-radius:16px;background:#f8fbff;">
                                <tr>
                                    <td style="padding:20px 22px;">
                                        <div style="font-size:12px;line-height:1.4;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:8px;">{{ __('common.email_talent_welcome_ref_label') }}</div>
                                        <div style="font-size:20px;font-weight:700;color:#1e3a5f;letter-spacing:1px;">{{ $talent->talent_code }}</div>
                                        <div style="margin-top:12px;font-size:13px;color:#64748b;">{{ __('common.email_talent_welcome_review_info') }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 12px;font-size:14px;line-height:1.7;color:#475569;">
                                {{ __('common.email_talent_welcome_keep_ref') }}
                            </p>

                            <p style="margin:16px 0 0;font-size:13px;line-height:1.7;color:#64748b;">
                                {{ __('common.email_talent_welcome_why') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;line-height:1.7;color:#64748b;">
                            {{ __('common.email_talent_welcome_footer') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
