<?php
/**
 * 어드민 - Migration 018 (매월1일 쿠폰 발급 쪽지 설정)
 * g5_ev_memo_config에 em_monthly_coupon_memo 컬럼 추가
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 018 - 매월1일 쿠폰 쪽지 설정';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'g5_ev_memo_config';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));

if (!$exists) {
    echo '<p style="color:#c00;">g5_ev_memo_config 테이블이 없습니다. <a href="./run_migration_016.php">마이그레이션 016</a>을 먼저 실행하세요.</p>';
} else {
    $cols = array();
    $cr = sql_query("SHOW COLUMNS FROM {$tb}", false);
    if ($cr) while ($r = sql_fetch_array($cr)) $cols[] = $r['Field'];
    if (in_array('em_monthly_coupon_memo', $cols)) {
        echo '<p>em_monthly_coupon_memo 컬럼이 이미 존재합니다.</p>';
    } else {
        $sql = "ALTER TABLE `{$tb}` ADD COLUMN `em_monthly_coupon_memo` TEXT DEFAULT NULL COMMENT '매월 1일 쿠폰 발급 시 쪽지 내용' AFTER `em_join_memo_biz`";
        $ok = sql_query($sql, false);
        echo '<p>'.($ok ? '<span style="color:green;">[OK]</span> em_monthly_coupon_memo 컬럼 추가됨' : '<span style="color:red;">[FAIL]</span>').'</p>';
    }
}

echo '<p><a href="./eve_memo_manage.php" class="btn btn_01">쪽지관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
