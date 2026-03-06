<?php
/**
 * CLI - g5_memo, g5_ev_memo_config 테이블 utf8mb4 변환 (이모지 정상 표시)
 * 사용: php scripts/run_memo_utf8mb4_cli.php
 */
$base = dirname(dirname(__FILE__));
chdir($base);
$_SERVER['REQUEST_METHOD'] = 'GET';
include_once $base . '/common.php';
if (!function_exists('sql_query')) { echo "DB 연결 실패\n"; exit(1); }

$tables = array('g5_memo', 'g5_ev_memo_config');
$ok = true;
foreach ($tables as $tb) {
    $exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
    if (!$exists) {
        echo "{$tb}: 테이블 없음 - 스킵\n";
        continue;
    }
    $r = sql_fetch("SELECT TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$tb}'");
    $coll = $r['TABLE_COLLATION'] ?? '';
    if (strpos($coll, 'utf8mb4') === 0) {
        echo "{$tb}: 이미 utf8mb4\n";
        continue;
    }
    $sql = "ALTER TABLE `{$tb}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $res = sql_query($sql, false);
    if ($res) {
        echo "OK: {$tb} -> utf8mb4\n";
    } else {
        echo "FAIL: {$tb}\n";
        $ok = false;
    }
}
exit($ok ? 0 : 1);
