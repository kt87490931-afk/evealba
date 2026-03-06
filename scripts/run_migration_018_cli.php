<?php
/**
 * CLI - em_monthly_coupon_memo 컬럼 추가
 * php scripts/run_migration_018_cli.php
 */
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
chdir(dirname(dirname(__FILE__)));
define('_RUN_MIGRATION_', true);
include_once 'common.php';

$tb = 'g5_ev_memo_config';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
if (!$exists) {
    echo "g5_ev_memo_config 테이블 없음. run_migration_016 먼저 실행.\n";
    exit(1);
}
$cr = sql_query("SHOW COLUMNS FROM {$tb}", false);
$cols = array();
while ($r = sql_fetch_array($cr)) $cols[] = $r['Field'];
if (in_array('em_monthly_coupon_memo', $cols)) {
    echo "em_monthly_coupon_memo 컬럼 이미 존재.\n";
    exit(0);
}
$ok = sql_query("ALTER TABLE `{$tb}` ADD COLUMN `em_monthly_coupon_memo` TEXT DEFAULT NULL COMMENT '매월 1일 쿠폰 발급 시 쪽지 내용' AFTER `em_join_memo_biz`", false);
echo $ok ? "em_monthly_coupon_memo 컬럼 추가 완료.\n" : "실패.\n";
exit($ok ? 0 : 1);
