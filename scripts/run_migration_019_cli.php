<?php
/**
 * CLI - Migration 019 (g5_ev_memo_log 테이블)
 * 사용: php scripts/run_migration_019_cli.php
 */
$base = dirname(dirname(__FILE__));
chdir($base);
$_SERVER['REQUEST_METHOD'] = 'GET';
include_once $base . '/common.php';
if (!function_exists('sql_query')) { echo "DB 연결 실패\n"; exit(1); }

$tb = 'g5_ev_memo_log';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
if ($exists) {
    echo "g5_ev_memo_log 이미 존재합니다.\n";
    exit(0);
}
$sql = "CREATE TABLE IF NOT EXISTS `{$tb}` (
  `eml_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `eml_type` VARCHAR(30) NOT NULL DEFAULT '',
  `eml_target` VARCHAR(30) DEFAULT NULL,
  `eml_count` INT UNSIGNED NOT NULL DEFAULT 1,
  `eml_recipients` TEXT DEFAULT NULL,
  `eml_memo_preview` VARCHAR(255) DEFAULT NULL,
  `eml_datetime` DATETIME NOT NULL,
  `eml_send_mb_id` VARCHAR(20) DEFAULT NULL,
  `eml_ec_id` INT UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$ok = sql_query($sql, false);
echo $ok ? "OK: g5_ev_memo_log 생성됨\n" : "FAIL\n";
exit($ok ? 0 : 1);
