<?php
/**
 * 어드민 - Migration 019 (쪽지 발송 로그)
 * g5_ev_memo_log 테이블 생성 - 수동/자동 쪽지 발송 내역 기록
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 019 - 쪽지 발송 로그';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'g5_ev_memo_log';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));

if ($exists) {
    echo '<p>g5_ev_memo_log 테이블이 이미 존재합니다.</p>';
} else {
    $sql = "CREATE TABLE IF NOT EXISTS `{$tb}` (
      `eml_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `eml_type` VARCHAR(30) NOT NULL DEFAULT '' COMMENT 'manual_bulk, join_general, join_biz, monthly_coupon',
      `eml_target` VARCHAR(30) DEFAULT NULL COMMENT 'general, biz_approved, all (수동발송용)',
      `eml_count` INT UNSIGNED NOT NULL DEFAULT 1,
      `eml_recipients` TEXT DEFAULT NULL COMMENT 'JSON array of mb_id',
      `eml_memo_preview` VARCHAR(255) DEFAULT NULL,
      `eml_datetime` DATETIME NOT NULL,
      `eml_send_mb_id` VARCHAR(20) DEFAULT NULL,
      `eml_ec_id` INT UNSIGNED DEFAULT NULL COMMENT '쿠폰ID (monthly_coupon용)'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $ok = sql_query($sql, false);
    echo '<p>'.($ok ? '<span style="color:green;">[OK]</span> g5_ev_memo_log 테이블 생성됨' : '<span style="color:red;">[FAIL]</span>').'</p>';
}

echo '<p><a href="./eve_memo_manage.php" class="btn btn_01">쪽지관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
