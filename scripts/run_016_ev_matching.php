<?php
/**
 * 016_create_ev_matching 수동 실행 (테이블 미존재 시)
 */
define('_RUN_MIGRATION_', true);
chdir(__DIR__ . '/..');
include 'common.php';

$sql = file_get_contents(__DIR__ . '/../migrations/016_create_ev_matching.sql');
$queries = array_filter(array_map('trim', explode(';', $sql)));
$ok = 0;
foreach ($queries as $q) {
    if ($q === '' || preg_match('/^\s*--/', $q)) continue;
    $r = sql_query($q, false);
    if ($r !== false) {
        $ok++;
        echo "[OK] " . substr($q, 0, 80) . "...\n";
    } else {
        echo "[ERR] " . substr($q, 0, 60) . "...\n";
    }
}
echo "Done. Executed: $ok queries\n";
