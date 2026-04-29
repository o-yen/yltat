<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre>\n=== Email Test Suite ===\n\n";

$to = 'cruzvebration35@gmail.com';

// 1. Application Approved
try {
    $talent = \App\Models\Talent::first();
    if ($talent) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\ApplicationApprovedMail($talent));
        echo "✓ 1/5 ApplicationApprovedMail sent to {$to}\n";
    }
} catch (\Throwable $e) {
    echo "✗ 1/5 ApplicationApprovedMail FAILED: " . $e->getMessage() . "\n";
}

// 2. Application Rejected
try {
    $talent = \App\Models\Talent::first();
    if ($talent) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\ApplicationRejectedMail($talent, 'Kelayakan akademik tidak mencukupi.'));
        echo "✓ 2/5 ApplicationRejectedMail sent\n";
    }
} catch (\Throwable $e) {
    echo "✗ 2/5 ApplicationRejectedMail FAILED: " . $e->getMessage() . "\n";
}

// 3. Password Reset
try {
    $user = \App\Models\User::first();
    if ($user) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\PasswordResetByAdminMail($user, 'ABCDefgh45!'));
        echo "✓ 3/5 PasswordResetByAdminMail sent\n";
    }
} catch (\Throwable $e) {
    echo "✗ 3/5 PasswordResetByAdminMail FAILED: " . $e->getMessage() . "\n";
}

// 4. Allowance Payment
try {
    $payment = \App\Models\KewanganElaun::first();
    if ($payment) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\AllowancePaymentMail($payment, 'Test Graduate', 'Selesai'));
        echo "✓ 4/5 AllowancePaymentMail sent\n";
    } else {
        echo "• 4/5 AllowancePaymentMail SKIPPED (no payment records)\n";
    }
} catch (\Throwable $e) {
    echo "✗ 4/5 AllowancePaymentMail FAILED: " . $e->getMessage() . "\n";
}

// 5. Critical Issue
try {
    $issue = \App\Models\IsuRisiko::first();
    if ($issue) {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\CriticalIssueMail($issue));
        echo "✓ 5/5 CriticalIssueMail sent\n";
    } else {
        echo "• 5/5 CriticalIssueMail SKIPPED (no issue records)\n";
    }
} catch (\Throwable $e) {
    echo "✗ 5/5 CriticalIssueMail FAILED: " . $e->getMessage() . "\n";
}

echo "\n✓ Done. Check {$to} inbox.\n</pre>";
