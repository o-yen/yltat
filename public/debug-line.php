<?php
if (!isset($_GET["t"]) || $_GET["t"] !== "YLTAT2025") { die("403"); }
require __DIR__ . "/../vendor/autoload.php";
$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$path = storage_path("framework/views/6d1480962e9c2010b118a614d858ade4.php");
echo "Path: $path
";
echo "Exists: " . (file_exists($path) ? "yes" : "no") . "
";

// Find show.blade compiled file
$files = glob(storage_path("framework/views/*.php"));
echo "Total compiled views: " . count($files) . "
";
foreach ($files as $f) {
    $content = file_get_contents($f);
    if (strpos($content, "manage-placement") !== false && strpos($content, "showAssignModal") !== false) {
        echo "Found: " . basename($f) . "
";
        $lines = file($f);
        for ($i = max(0, 108); $i <= min(count($lines)-1, 118); $i++) {
            echo ($i+1) . ": " . htmlspecialchars(rtrim($lines[$i])) . "
";
        }
        break;
    }
}

