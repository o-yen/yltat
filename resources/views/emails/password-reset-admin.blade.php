<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.email_password_reset_subject') }}</title>
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
                            <div style="font-size:22px;line-height:1.3;font-weight:700;color:#111827;margin-bottom:16px;">{{ __('common.email_password_reset_heading') }}</div>

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_password_reset_greeting', ['name' => $user->full_name]) }}
                            </p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_password_reset_body') }}
                            </p>

                            {{-- Credentials box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #dbe4f0;border-radius:12px;background:#f8fbff;">
                                <tr>
                                    <td style="padding:20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding:6px 0;">
                                                    <span style="font-size:12px;color:#6b7280;">{{ __('common.email_talent_welcome_email_label') }}</span><br>
                                                    <span style="font-size:15px;font-weight:600;color:#1e3a5f;">{{ $user->email }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;border-top:1px solid #e2e8f0;">
                                                    <span style="font-size:12px;color:#6b7280;">{{ __('common.email_password_reset_new_password') }}</span><br>
                                                    <span style="font-size:18px;font-weight:700;color:#1e3a5f;font-family:Consolas,monospace;letter-spacing:2px;">{{ $temporaryPassword }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">
                                {{ __('common.email_password_reset_change_prompt') }}
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
                                {{ __('common.email_password_reset_warning') }}
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
