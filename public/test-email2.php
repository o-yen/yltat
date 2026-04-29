<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre>\n";
$to = 'cruzvebration35@gmail.com';

// Allowance Payment
try {
    $payment = \App\Models\KewanganElaun::first();
    if ($payment) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\AllowancePaymentMail($payment, 'Test Graduate', 'Selesai'));
        echo "✓ AllowancePaymentMail sent\n";
    } else {
        echo "• No payment records found\n";
    }
} catch (\Throwable $e) {
    echo "✗ AllowancePaymentMail FAILED: " . $e->getMessage() . "\n";
    echo "  Line: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

sleep(2);

// Critical Issue
try {
    $issue = \App\Models\IsuRisiko::first();
    if ($issue) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\CriticalIssueMail($issue));
        echo "✓ CriticalIssueMail sent\n";
    } else {
        echo "• No issue records found\n";
    }
} catch (\Throwable $e) {
    echo "✗ CriticalIssueMail FAILED: " . $e->getMessage() . "\n";
    echo "  Line: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "</pre>";
