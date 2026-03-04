<?php
/**
 * 어드민 - Migration 013 (쿠폰 발급 기간 컬럼)
 * g5_ev_coupon에 ec_issue_from, ec_issue_to, ec_issue_limit_per_member 추가
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 013 실행';
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

$adds = array(
    array('ec_issue_from', "ADD COLUMN `ec_issue_from` date DEFAULT NULL COMMENT '발급 가능 시작일' AFTER `ec_valid_to`"),
    array('ec_issue_to', "ADD COLUMN `ec_issue_to` date DEFAULT NULL COMMENT '발급 가능 종료일' AFTER `ec_issue_from`"),
    array('ec_issue_limit_per_member', "ADD COLUMN `ec_issue_limit_per_member` int NOT NULL DEFAULT 0 COMMENT '1인당 최대 발급 횟수, 0=무제한' AFTER `ec_use_count`")
);

echo '<div class="local_desc01 local_desc"><p>g5_ev_coupon에 발급 기간·제한 컬럼을 추가합니다.</p></div>';
echo '<div class="tbl_head01 tbl_wrap"><table><tbody>';

foreach ($adds as $a) {
    $col = $a[0];
    $sql = "ALTER TABLE `{$tb}` " . $a[1];
    $ok = true;
    if (in_array($col, $cols)) {
        echo '<tr><td><span style="color:gray;">[SKIP]</span></td><td>' . htmlspecialchars($col) . ' 이미 존재함</td></tr>';
    } else {
        $r = @sql_query($sql, false);
        $ok = ($r !== false);
        echo '<tr><td>' . ($ok ? '<span style="color:green;">[OK]</span>' : '<span style="color:red;">[FAIL]</span>') . '</td><td>' . htmlspecialchars($col) . '</td></tr>';
        if ($ok) $cols[] = $col;
    }
}
echo '</tbody></table></div>';
echo '<p><a href="./eve_coupon_list.php" class="btn btn_01">쿠폰 관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
