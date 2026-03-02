<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/scripts/run_migration_011.php';

$base = dirname(__DIR__);
include_once($base . '/_common.php');

echo "=== Migration 011: Jump Columns ===\n";

$cols = array(
    'jr_jump_remain' => 'INT UNSIGNED NOT NULL DEFAULT 0',
    'jr_jump_used' => 'INT UNSIGNED NOT NULL DEFAULT 0',
    'jr_jump_total' => 'INT UNSIGNED NOT NULL DEFAULT 0',
    'jr_jump_datetime' => 'DATETIME DEFAULT NULL',
    'jr_auto_jump' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0',
    'jr_auto_jump_next' => 'DATETIME DEFAULT NULL',
);
foreach ($cols as $col => $def) {
    $chk = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE '{$col}'", false);
    if ($chk && @sql_num_rows($chk)) {
        echo "Column {$col}: already exists\n";
    } else {
        $r = @sql_query("ALTER TABLE g5_jobs_register ADD COLUMN {$col} {$def}", false);
        echo "ADD {$col}: " . ($r !== false ? 'OK' : 'FAIL') . "\n";
    }
}

$r2 = @sql_query("CREATE TABLE IF NOT EXISTS g5_jobs_jump_log (
    jl_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jr_id INT UNSIGNED NOT NULL,
    mb_id VARCHAR(20) NOT NULL DEFAULT '',
    jl_type ENUM('manual','auto') NOT NULL DEFAULT 'manual',
    jl_remain_before INT UNSIGNED NOT NULL DEFAULT 0,
    jl_remain_after INT UNSIGNED NOT NULL DEFAULT 0,
    jl_datetime DATETIME NOT NULL,
    KEY idx_jr_id (jr_id),
    KEY idx_datetime (jl_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
echo "CREATE jump_log: " . ($r2 !== false ? 'OK' : 'FAIL') . "\n";

$r3 = @sql_query("CREATE TABLE IF NOT EXISTS g5_jobs_jump_purchase (
    jp_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jr_id INT UNSIGNED NOT NULL,
    mb_id VARCHAR(20) NOT NULL DEFAULT '',
    jp_count INT UNSIGNED NOT NULL DEFAULT 0,
    jp_amount INT UNSIGNED NOT NULL DEFAULT 0,
    jp_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    jp_datetime DATETIME NOT NULL,
    jp_confirmed_datetime DATETIME DEFAULT NULL,
    KEY idx_jr_id (jr_id),
    KEY idx_mb_id (mb_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
echo "CREATE jump_purchase: " . ($r3 !== false ? 'OK' : 'FAIL') . "\n";

$check = sql_fetch("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_jump_remain'");
echo "Verify jr_jump_remain: " . ($check ? 'EXISTS' : 'MISSING') . "\n";

$check2 = sql_query("SHOW TABLES LIKE 'g5_jobs_jump_log'", false);
echo "Verify g5_jobs_jump_log: " . ($check2 && sql_num_rows($check2) ? 'EXISTS' : 'MISSING') . "\n";

$check3 = sql_query("SHOW TABLES LIKE 'g5_jobs_jump_purchase'", false);
echo "Verify g5_jobs_jump_purchase: " . ($check3 && sql_num_rows($check3) ? 'EXISTS' : 'MISSING') . "\n";

echo "\n=== DONE ===\n";
