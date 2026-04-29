<?php
if (!isset($_GET['t']) || $_GET['t'] !== 'YLTAT2025') { http_response_code(403); die('403'); }

// Parse .env for DB credentials
$env = [];
foreach (file(__DIR__ . '/../.env') as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, '"\'');
}

$host = $env['DB_HOST'] ?? 'localhost';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

$pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Helper: add column only if it doesn't exist
function addColumnIfMissing(PDO $pdo, string $table, string $column, string $definition): string {
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    if ($stmt->rowCount() > 0) {
        return "SKIP  {$table}.{$column} (already exists)\n";
    }
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
    return "ADD   {$table}.{$column}\n";
}

// Helper: add unique index only if it doesn't exist
function addUniqueIfMissing(PDO $pdo, string $table, string $column): string {
    $stmt = $pdo->query("SHOW INDEX FROM `{$table}` WHERE Column_name = '{$column}' AND Non_unique = 0");
    if ($stmt->rowCount() > 0) {
        return "SKIP  unique({$column}) on {$table} (already exists)\n";
    }
    $pdo->exec("ALTER TABLE `{$table}` ADD UNIQUE (`{$column}`)");
    return "ADD   unique({$column}) on {$table}\n";
}

$log = "=== migrate-talents.php ===\n";

// --- 2026_03_28_300001_align_talents_with_master_graduan ---
$log .= addColumnIfMissing($pdo, 'talents', 'id_graduan',    "VARCHAR(20) NULL AFTER `id`");
$log .= addUniqueIfMissing($pdo, 'talents', 'id_graduan');
$log .= addColumnIfMissing($pdo, 'talents', 'negeri',        "VARCHAR(100) NULL AFTER `address`");
$log .= addColumnIfMissing($pdo, 'talents', 'kelayakan',     "VARCHAR(150) NULL AFTER `gender`");

// --- 2026_03_28_000003_add_protege_fields_to_talents_table ---
$log .= addColumnIfMissing($pdo, 'talents', 'kategori',                "VARCHAR(50) NULL AFTER `status`");
$log .= addColumnIfMissing($pdo, 'talents', 'status_penyerapan_6bulan',"VARCHAR(30) NULL AFTER `kategori`");
$log .= addColumnIfMissing($pdo, 'talents', 'id_pelaksana',            "VARCHAR(20) NULL AFTER `status_penyerapan_6bulan`");
$log .= addColumnIfMissing($pdo, 'talents', 'id_syarikat_penempatan',  "VARCHAR(20) NULL AFTER `id_pelaksana`");
$log .= addColumnIfMissing($pdo, 'talents', 'jawatan',                 "VARCHAR(200) NULL AFTER `id_syarikat_penempatan`");

// --- 2026_03_28_300001 continued ---
$log .= addColumnIfMissing($pdo, 'talents', 'tarikh_mula',   "DATE NULL AFTER `jawatan`");
$log .= addColumnIfMissing($pdo, 'talents', 'tarikh_tamat',  "DATE NULL AFTER `tarikh_mula`");
$log .= addColumnIfMissing($pdo, 'talents', 'status_aktif',  "VARCHAR(30) NULL AFTER `tarikh_tamat`");

// --- 2026_03_28_400001_add_placement_fields_to_talents_table ---
$log .= addColumnIfMissing($pdo, 'talents', 'department',      "VARCHAR(200) NULL AFTER `jawatan`");
$log .= addColumnIfMissing($pdo, 'talents', 'supervisor_name', "VARCHAR(200) NULL AFTER `department`");
$log .= addColumnIfMissing($pdo, 'talents', 'supervisor_email',"VARCHAR(200) NULL AFTER `supervisor_name`");
$log .= addColumnIfMissing($pdo, 'talents', 'duration_months', "INT NULL AFTER `supervisor_email`");
$log .= addColumnIfMissing($pdo, 'talents', 'monthly_stipend', "DECIMAL(10,2) NULL AFTER `duration_months`");
$log .= addColumnIfMissing($pdo, 'talents', 'additional_cost', "DECIMAL(10,2) NULL AFTER `monthly_stipend`");
$log .= addColumnIfMissing($pdo, 'talents', 'programme_type',  "VARCHAR(100) NULL AFTER `additional_cost`");

// --- 2026_03_28_500001_add_phone_avatar_to_users_table ---
$log .= addColumnIfMissing($pdo, 'users', 'phone',  "VARCHAR(30) NULL AFTER `email`");
$log .= addColumnIfMissing($pdo, 'users', 'avatar', "VARCHAR(500) NULL AFTER `language`");

$log .= "=== DONE ===\n";
header('Content-Type: text/plain');
echo $log;
