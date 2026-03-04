<?php
/**
 * 어드민 - Migration 014 (쿠폰 발급유형·자동트리거·발급대상)
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 014 실행';
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
    array('ec_issue_type', "ADD COLUMN `ec_issue_type` varchar(20) NOT NULL DEFAULT 'manual' COMMENT 'manual=수동, auto=자동' AFTER `ec_is_active`"),
    array('ec_auto_trigger', "ADD COLUMN `ec_auto_trigger` varchar(30) DEFAULT NULL COMMENT 'on_approval=가입인증후, monthly_1st=매월1일' AFTER `ec_issue_type`"),
    array('ec_issue_target_scope', "ADD COLUMN `ec_issue_target_scope` varchar(20) NOT NULL DEFAULT 'all' COMMENT 'all=전체, individual=개인' AFTER `ec_auto_trigger`"),
    array('ec_issue_target_mb_id', "ADD COLUMN `ec_issue_target_mb_id` varchar(20) DEFAULT NULL COMMENT '개인 발급 시 대상 회원ID' AFTER `ec_issue_target_scope`")
);

echo '<div class="local_desc01 local_desc"><p>g5_ev_coupon에 발급유형·자동트리거·발급대상 컬럼을 추가합니다.</p></div>';
echo '<div class="tbl_head01 tbl_wrap"><table><tbody>';

foreach ($adds as $a) {
    $col = $a[0];
    $sql = "ALTER TABLE `{$tb}` " . $a[1];
    if (in_array($col, $cols)) {
        echo '<tr><td><span style="color:gray;">[SKIP]</span></td><td>'.$col.' 이미 존재함</td></tr>';
    } else {
        $r = @sql_query($sql, false);
        $ok = ($r !== false);
        echo '<tr><td>'.($ok ? '<span style="color:green;">[OK]</span>' : '<span style="color:red;">[FAIL]</span>').'</td><td>'.$col.'</td></tr>';
        if ($ok) $cols[] = $col;
    }
}

if (in_array('ec_issue_type', $cols)) {
    sql_query("UPDATE `{$tb}` SET ec_issue_type = 'auto', ec_auto_trigger = 'on_approval' WHERE ec_name IN ('줄광고3달무료', '채용공고30%할인')", false);
    $n = sql_affected_rows();
    echo '<tr><td><span style="color:green;">[OK]</span></td><td>기존 쿠폰 마이그레이션 '.$n.'건 (줄광고3달무료, 채용공고30%할인)</td></tr>';
}

echo '</tbody></table></div>';
echo '<p><a href="./eve_coupon_list.php" class="btn btn_01">쿠폰 관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
