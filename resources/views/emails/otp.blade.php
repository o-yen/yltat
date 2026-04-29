<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.email_otp_title') }} — {{ __('common.app_name') }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: #1E3A5F; padding: 32px 40px; text-align: center; }
        .header img { height: 48px; }
        .header h1 { color: #ffffff; font-size: 18px; margin: 16px 0 0; font-weight: 600; }
        .body { padding: 40px; }
        .greeting { font-size: 15px; color: #374151; margin-bottom: 16px; }
        .otp-box { background: #EFF6FF; border: 2px dashed #1E3A5F; border-radius: 10px; text-align: center; padding: 28px 20px; margin: 28px 0; }
        .otp-label { font-size: 13px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; }
        .otp-code { font-size: 42px; font-weight: 700; letter-spacing: 0.25em; color: #1E3A5F; font-family: 'Courier New', monospace; }
        .otp-expiry { font-size: 13px; color: #9CA3AF; margin-top: 12px; }
        .note { font-size: 13px; color: #6B7280; line-height: 1.6; background: #FEF3C7; border-left: 3px solid #F59E0B; padding: 12px 16px; border-radius: 6px; margin-top: 24px; }
        .footer { background: #F9FAFB; border-top: 1px solid #E5E7EB; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #9CA3AF; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{!! __('common.email_otp_header') !!}</h1>
        </div>

        <div class="body">
            <p class="greeting">{!! __('common.email_otp_greeting', ['name' => $user->full_name]) !!}</p>

            <p style="font-size:15px;color:#374151;line-height:1.6;">
                {{ __('common.email_otp_body') }}
            </p>

            <div class="otp-box">
                <div class="otp-label">{{ __('common.email_otp_label') }}</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">&#9201; {!! __('common.email_otp_expiry') !!}</div>
            </div>

            <div class="note">
                &#9888;&#65039; {{ __('common.email_otp_warning') }}
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {!! __('common.email_otp_footer_copy') !!}</p>
            <p style="margin-top:4px;">{{ __('common.email_otp_footer_auto') }}</p>
        </div>
    </div>
</body>
</html>
