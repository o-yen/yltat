<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "<pre>\n";

// Simulate an actual HTTP request through the full stack
$request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
$request->setLaravelSession(app('session.store'));

// Login as user 1
$user = \App\Models\User::first();
auth()->login($user);
$request->setUserResolver(fn() => $user);

try {
    // Run through middleware manually
    $router = app('router');
    $route = $router->getRoutes()->match($request);
    $request->setRouteResolver(fn() => $route);
    
    // Get the middleware
    $middleware = $router->gatherRouteMiddleware($route);
    echo "Route: " . $route->uri() . "\n";
    echo "Middleware: " . implode(', ', $middleware) . "\n\n";
    
    // Try dispatching
    $response = $router->dispatch($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() >= 400) {
        $content = $response->getContent();
        if (preg_match('/<div class="code">(.*?)<\/div>/s', $content, $m)) {
            echo "Error: " . trim(strip_tags($m[1])) . "\n";
        }
    }
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
echo "</pre>";
