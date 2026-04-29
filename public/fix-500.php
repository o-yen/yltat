<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "<pre>\n";

// Check the errors.500 view itself — it might be crashing
try {
    $html = view('errors.500', ['exception' => new \Exception('test')])->render();
    echo "errors.500 view renders OK (" . strlen($html) . " bytes)\n";
} catch (\Throwable $e) {
    echo "errors.500 VIEW CRASHES: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Check the Authenticate middleware
try {
    $auth = app(\App\Http\Middleware\Authenticate::class);
    $request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
    
    // This should throw an auth exception since no user is logged in
    $response = $auth->handle($request, function($r) { return response('ok'); });
    echo "Auth middleware response: " . $response->getStatusCode() . "\n";
} catch (\Illuminate\Auth\AuthenticationException $e) {
    echo "Auth middleware correctly throws AuthenticationException\n";
    echo "Redirect to: " . ($e->redirectTo() ?? 'null') . "\n";
} catch (\Throwable $e) {
    echo "Auth middleware throws: " . get_class($e) . " - " . $e->getMessage() . "\n";
    echo "File: " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
}
echo "</pre>";
