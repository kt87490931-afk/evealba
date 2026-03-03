<?php
/**
 * Migration 012: 쿠폰·썸네일옵션결제 테이블 생성
 */
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/scripts/run_migration_012.php';
$base = dirname(__DIR__);
include_once($base . '/_common.php');

echo "=== Migration 012: ev_coupon, thumb_option_paid ===\n";

$sql = file_get_contents($base . '/migrations/012_create_ev_coupon_and_thumb_paid.sql');
$queries = array_filter(array_map('trim', explode(';', $sql)));
foreach ($queries as $q) {
    if ($q === '' || preg_match('/^\s*--/', $q)) continue;
    $r = @sql_query($q, false);
    echo ($r !== false ? '[OK]' : '[FAIL]') . " " . substr(str_replace("\n", " ", $q), 0, 60) . "...\n";
}

$tables = array('g5_ev_coupon', 'g5_ev_coupon_issue', 'g5_ev_coupon_use', 'g5_jobs_thumb_option_paid');
foreach ($tables as $t) {
    $chk = sql_query("SHOW TABLES LIKE '{$t}'", false);
    echo "Verify {$t}: " . ($chk && sql_num_rows($chk) ? 'EXISTS' : 'MISSING') . "\n";
}
echo "\n=== DONE ===\n";
