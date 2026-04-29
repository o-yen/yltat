<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "<pre>\n";
$dirs = ['lang' => __DIR__ . '/../lang', 'resources/lang' => __DIR__ . '/../resources/lang'];
foreach ($dirs as $label => $dir) {
    echo "--- {$label}/ ---\n";
    if (!is_dir($dir)) { echo "  NOT FOUND\n\n"; continue; }
    foreach (['en', 'ms'] as $locale) {
        $localeDir = "{$dir}/{$locale}";
        if (!is_dir($localeDir)) continue;
        foreach (glob("{$localeDir}/*.php") as $file) {
            $result = include $file;
            $ok = is_array($result) ? "OK (" . count($result) . " keys)" : "ERROR: returns " . gettype($result);
            $icon = is_array($result) ? '✓' : '✗';
            echo "  {$icon} {$locale}/" . basename($file) . ": {$ok}\n";
        }
    }
    echo "\n";
}
echo "</pre>";
