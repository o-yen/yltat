<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { die('POST required'); }

$file = $_GET['file'] ?? '';
$content = file_get_contents('php://input');
if (empty($content)) { die('Empty body'); }

$directMap = [
    'blade/create-user'    => [__DIR__ . '/../resources/views/admin/settings/create-user.blade.php'],
    'blade/edit-user'      => [__DIR__ . '/../resources/views/admin/settings/edit-user.blade.php'],
    'blade/guide-index'    => [__DIR__ . '/../resources/views/admin/system-guide/index.blade.php'],
    'routes/web'           => [__DIR__ . '/../routes/web.php'],
    'blade/portal-show'    => [__DIR__ . '/../resources/views/portal/show.blade.php'],
    'blade/admin-layout'   => [__DIR__ . '/../resources/views/layouts/admin.blade.php'],
    'blade/talents-edit'   => [__DIR__ . '/../resources/views/admin/talents/edit.blade.php'],
    'controllers/talent'   => [__DIR__ . '/../app/Http/Controllers/Admin/TalentController.php'],
    'public/system-overview' => [__DIR__ . '/system-overview.json.php'],
];

if (isset($directMap[$file])) {
    $targets = $directMap[$file];
} elseif (preg_match('#^(en|ms)/[a-z_]+\.php$#', $file)) {
    $targets = [
        __DIR__ . "/../lang/{$file}",
        __DIR__ . "/../resources/lang/{$file}",
    ];
} else {
    die('Invalid file: ' . htmlspecialchars($file));
}

foreach ($targets as $target) {
    $dir = dirname($target);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($target, $content);
}

echo "OK: wrote " . strlen($content) . " bytes to " . count($targets) . " location(s)";
