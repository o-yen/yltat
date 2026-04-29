<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }

$results = [];

// 1. Clear compiled Blade views
$viewsPath = __DIR__ . '/../storage/framework/views';
$cleared = 0;
if (is_dir($viewsPath)) {
    foreach (glob($viewsPath . '/*.php') as $file) {
        unlink($file);
        $cleared++;
    }
}
$results[] = "Blade views cleared: {$cleared} files";

// 2. Clear config cache
$configCache = __DIR__ . '/../bootstrap/cache/config.php';
if (file_exists($configCache)) { unlink($configCache); $results[] = "Config cache cleared"; }
else { $results[] = "Config cache: not present"; }

// 3. Clear route cache
$routeCache = __DIR__ . '/../bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) { unlink($routeCache); $results[] = "Route cache cleared"; }
else { $results[] = "Route cache: not present"; }

// 4. Clear events cache
$eventsCache = __DIR__ . '/../bootstrap/cache/events.php';
if (file_exists($eventsCache)) { unlink($eventsCache); $results[] = "Events cache cleared"; }
else { $results[] = "Events cache: not present"; }

// 5. Check last Laravel log entry
$logFile = __DIR__ . '/../storage/logs/laravel.log';
$lastError = '';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $relevant = array_filter($lines, fn($l) => str_contains($l, '.ERROR') || str_contains($l, 'production.ERROR'));
    if ($relevant) {
        $last = end($relevant);
        $lastError = trim($last);
    }
}

header('Content-Type: text/plain');
echo "=== clear-cache.php ===\n";
foreach ($results as $r) echo $r . "\n";
echo "\n--- Last ERROR log entry ---\n";
echo $lastError ?: "(none found)";
echo "\n=== DONE ===\n";
