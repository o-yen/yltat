<?php
header('Content-Type: text/plain; charset=utf-8');
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    echo 'RegistrationController=' . (class_exists(App\Http\Controllers\RegistrationController::class) ? '1' : '0') . "\n";
    echo 'ApplicationController=' . (class_exists(App\Http\Controllers\Admin\ApplicationController::class) ? '1' : '0') . "\n";
    echo 'TalentController=' . (class_exists(App\Http\Controllers\Admin\TalentController::class) ? '1' : '0') . "\n";
    echo 'portal.register.store=' . (\Illuminate\Support\Facades\Route::has('portal.register.store') ? '1' : '0') . "\n";
    echo 'admin.talents.index=' . (\Illuminate\Support\Facades\Route::has('admin.talents.index') ? '1' : '0') . "\n";
    if (\Illuminate\Support\Facades\Route::has('portal.register.store')) {
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('portal.register.store');
        echo 'portal.register.store.action=' . ($route?->getActionName() ?? 'null') . "\n";
    }
} catch (Throwable $e) {
    echo 'ERR=' . $e->getMessage() . "\n";
}
