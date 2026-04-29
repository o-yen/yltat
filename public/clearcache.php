<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$out = [];
Artisan::call('cache:clear');  $out[] = '✓ cache:clear';
Artisan::call('view:clear');   $out[] = '✓ view:clear — ' . trim(Artisan::output());
Artisan::call('config:clear'); $out[] = '✓ config:clear';
Artisan::call('route:clear');  $out[] = '✓ route:clear';
foreach (glob(__DIR__.'/../bootstrap/cache/*.php') as $f) { unlink($f); $out[] = '✓ deleted '.basename($f); }
if (function_exists('opcache_reset')) {
    $result = @opcache_reset();
    $out[] = $result ? '✓ opcache_reset' : '• opcache_reset unavailable';
}
echo '<pre>'.implode("\n",$out).'</pre><p style="color:green;font-weight:bold">Done.</p>';
