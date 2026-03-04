<?php
/**
 * 어드민 - Migration 015 (줄광고무료 기간 선택)
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 015 실행';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'g5_ev_coupon';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
if (!$exists) {
    echo '<p style="color:#c00;">g5_ev_coupon 테이블이 없습니다. <a href="./run_migration_012.php">마이그레이션 012</a>를 먼저 실행하세요.</p>';
    require_once G5_ADMIN_PATH.'/admin.tail.php';
    exit;
}

$cols = array();
$res = sql_query("SHOW COLUMNS FROM {$tb}", false);
while ($r = sql_fetch_array($res)) $cols[] = $r['Field'];

if (in_array('ec_line_ad_days', $cols)) {
    echo '<p>ec_line_ad_days 컬럼이 이미 존재합니다.</p>';
} else {
    $sql = "ALTER TABLE `{$tb}` ADD COLUMN `ec_line_ad_days` int NOT NULL DEFAULT 0 COMMENT '줄광고무료일 때 무료 기간(일): 30,60,90 등' AFTER `ec_type`";
    $ok = @sql_query($sql, false);
    echo '<p>'.($ok ? '<span style="color:green;">[OK]</span> ec_line_ad_days 컬럼 추가됨' : '<span style="color:red;">[FAIL]</span>').'</p>';
    if ($ok) {
        sql_query("UPDATE `{$tb}` SET ec_line_ad_days = 90 WHERE ec_type = 'line_ad_free' AND ec_line_ad_days = 0", false);
        echo '<p><span style="color:green;">[OK]</span> 기존 줄광고무료 쿠폰 90일로 설정</p>';
    }
}

echo '<p><a href="./eve_coupon_list.php" class="btn btn_01">쿠폰 관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
