<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error — SPB-YLTAT</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 2.5rem 2rem; max-width: 480px; width: 100%; text-align: center; }
        .icon { width: 56px; height: 56px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; }
        .icon svg { width: 28px; height: 28px; color: #dc2626; }
        h1 { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: .5rem; }
        p { font-size: .9rem; color: #6b7280; line-height: 1.6; margin-bottom: 1.5rem; }
        .code { font-family: monospace; font-size: .8rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: .75rem 1rem; color: #374151; text-align: left; word-break: break-all; margin-bottom: 1.5rem; }
        .actions { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
        a { display: inline-block; padding: .55rem 1.25rem; border-radius: 8px; font-size: .875rem; font-weight: 500; text-decoration: none; }
        .btn-primary { background: #1E3A5F; color: #fff; }
        .btn-primary:hover { background: #152c47; }
        .btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .btn-secondary:hover { background: #e5e7eb; }
        .badge { display: inline-block; font-size: .7rem; font-weight: 600; color: #6b7280; background: #f3f4f6; border-radius: 4px; padding: .2rem .5rem; margin-bottom: 1rem; letter-spacing: .05em; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <span class="badge">ERROR 500</span>
        <h1>Something went wrong</h1>
        <p>An unexpected error occurred. The issue has been noted. Please try again or go back to the dashboard.</p>

        @if(isset($exception) && config('app.debug'))
        <div class="code">
            <strong>{{ get_class($exception) }}</strong><br>
            {{ $exception->getMessage() }}<br>
            <small style="color:#9ca3af">{{ $exception->getFile() }}:{{ $exception->getLine() }}</small>
        </div>
        @endif

        <div class="actions">
            <a href="javascript:history.back()" class="btn-secondary">Go Back</a>
            <a href="/admin/dashboard" class="btn-primary">Dashboard</a>
        </div>
    </div>
</body>
</html>
