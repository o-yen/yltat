<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.email_approved_subject') }}</title>
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
                            {{-- Success icon --}}
                            <div style="text-align:center;margin-bottom:24px;">
                                <div style="display:inline-block;width:64px;height:64px;border-radius:50%;background:#dcfce7;line-height:64px;text-align:center;">
                                    <span style="font-size:32px;">&#10003;</span>
                                </div>
                            </div>

                            <div style="font-size:22px;line-height:1.3;font-weight:700;color:#111827;margin-bottom:16px;text-align:center;">{{ __('common.email_approved_heading') }}</div>

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_approved_greeting', ['name' => $talent->full_name]) }}
                            </p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_approved_body') }}
                            </p>

                            {{-- Login credentials box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #dbe4f0;border-radius:16px;background:#f8fbff;">
                                <tr>
                                    <td style="padding:20px 22px;">
                                        <div style="font-size:12px;line-height:1.4;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:8px;">{{ __('common.email_approved_login_label') }}</div>
                                        <div style="font-size:14px;line-height:1.7;color:#334155;"><strong>{{ __('common.email_talent_welcome_email_label') }}:</strong> {{ $talent->email }}</div>
                                        <div style="font-size:14px;line-height:1.7;color:#334155;"><strong>{{ __('common.email_talent_welcome_password_label') }}:</strong></div>
                                        <div style="margin-top:8px;display:inline-block;padding:12px 14px;border-radius:12px;background:#0f172a;color:#ffffff;font-family:'Courier New',monospace;font-size:18px;letter-spacing:0.06em;">
                                            {{ $temporaryPassword }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Talent code --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 22px;border:1px solid #dbe4f0;border-radius:12px;background:#f0fdf4;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">{{ __('common.email_approved_code_label') }}</div>
                                        <div style="font-size:20px;font-weight:700;color:#1e3a5f;letter-spacing:1px;">{{ $talent->talent_code }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_approved_next_steps') }}
                            </p>

                            {{-- CTA button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('login') }}" style="display:inline-block;background:#1e3a5f;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:14px 36px;border-radius:12px;">
                                            {{ __('common.email_talent_welcome_cta') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#94a3b8;">
                                {{ __('common.email_approved_footer') }}
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
