<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') — PROTEGE RTW</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .page { padding: 20mm 15mm; }
        .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 18px; color: #1E3A5F; }
        .header .meta { text-align: right; font-size: 10px; color: #666; }
        .logo { font-weight: bold; color: #1E3A5F; font-size: 14px; }
        h2 { font-size: 14px; color: #1E3A5F; margin: 15px 0 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        h3 { font-size: 12px; color: #444; margin: 10px 0 5px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 15px; font-size: 10px; }
        th { background: #1E3A5F; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9fafb; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 10px 0 15px; }
        .kpi-card { border: 1px solid #ddd; border-radius: 6px; padding: 10px; text-align: center; }
        .kpi-card .value { font-size: 20px; font-weight: bold; color: #1E3A5F; }
        .kpi-card .label { font-size: 9px; color: #666; margin-top: 3px; }
        .kpi-card.green { border-left: 3px solid #10B981; }
        .kpi-card.red { border-left: 3px solid #EF4444; }
        .kpi-card.yellow { border-left: 3px solid #F59E0B; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-green { background: #D1FAE5; color: #065F46; }
        .badge-red { background: #FEE2E2; color: #991B1B; }
        .badge-yellow { background: #FEF3C7; color: #92400E; }
        .badge-blue { background: #DBEAFE; color: #1E40AF; }
        .badge-gray { background: #F3F4F6; color: #374151; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; display: flex; justify-content: space-between; }
        .no-print { margin: 10px; }
        @media print {
            .no-print { display: none !important; }
            .page { padding: 10mm; }
            body { font-size: 10px; }
        }
        @page { size: A4; margin: 10mm; }
    </style>
</head>
<body>
    <div class="no-print" style="background: #1E3A5F; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;">
        <span>Report Preview — Use <strong>Ctrl+P</strong> or <strong>Cmd+P</strong> to save as PDF</span>
        <button onclick="window.print()" style="background: white; color: #1E3A5F; border: none; padding: 6px 16px; border-radius: 4px; font-weight: bold; cursor: pointer;">Print / Save PDF</button>
    </div>
    <div class="page">
        <div class="header">
            <div>
                <div class="logo">YAYASAN LTAT — PROTEGE RTW</div>
                <h1>@yield('title')</h1>
            </div>
            <div class="meta">
                <div>{{ now()->translatedFormat('d F Y') }}</div>
                <div>{{ __('protege.dash_summary') }}</div>
            </div>
        </div>

        @yield('content')

        <div class="footer">
            <span>&copy; {{ date('Y') }} Protege MINDEF — YLTAT Talent Monitoring System</span>
            <span>Generated: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</body>
</html>
