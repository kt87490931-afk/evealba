<?php
/**
 * 어드민 - Migration 020 (추천인 mb_recommend 컬럼)
 * g5_member에 mb_recommend 없으면 추가 (닉네임→mb_id 매핑 저장용)
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 020 - 추천인';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = $g5['member_table'];
$cols = array();
$r = @sql_query("SHOW COLUMNS FROM {$tb}", false);
if ($r) {
    while ($row = sql_fetch_array($r)) {
        $cols[] = $row['Field'];
    }
}

if (in_array('mb_recommend', $cols)) {
    echo '<p><span style="color:green;">[OK]</span> mb_recommend 컬럼이 이미 존재합니다.</p>';
} else {
    $ok = @sql_query("ALTER TABLE {$tb} ADD COLUMN mb_recommend VARCHAR(20) NOT NULL DEFAULT '' COMMENT '추천인 mb_id' AFTER mb_nick_date", false);
    echo '<p>'.($ok ? '<span style="color:green;">[OK]</span> mb_recommend 컬럼 추가됨' : '<span style="color:red;">[FAIL]</span>').'</p>';
}

echo '<p><a href="./eve_referral_manage.php" class="btn btn_01">추천인·이력서 관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
