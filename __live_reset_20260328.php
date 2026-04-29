<?php
header('Content-Type: text/plain; charset=utf-8');
$base = __DIR__;
require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$commands = ['optimize:clear', 'view:clear', 'route:clear', 'config:clear', 'cache:clear'];
foreach ($commands as $command) {
    echo "== $command ==\n";
    try {
        $kernel->call($command);
        echo $kernel->output() . "\n";
    } catch (Throwable $e) {
        echo 'ERR: ' . $e->getMessage() . "\n";
    }
}
if (function_exists('opcache_reset')) {
    echo 'opcache_reset=' . (opcache_reset() ? '1' : '0') . "\n";
} else {
    echo "opcache_reset=na\n";
}
