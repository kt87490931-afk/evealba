<?php
/**
 * 어드민 - Migration 021 (mb_recommend 다중 추천인 지원)
 * mb_recommend VARCHAR(20) → VARCHAR(500) 확장 (쉼표 구분 다중 mb_id 저장)
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 021 - 추천인 다중 입력';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = $g5['member_table'];
$r = @sql_query("SHOW COLUMNS FROM {$tb} LIKE 'mb_recommend'", false);
$row = $r ? sql_fetch_array($r) : null;

if (!$row) {
    echo '<p><span style="color:orange;">[SKIP]</span> mb_recommend 컬럼이 없습니다.</p>';
} else {
    $type = $row['Type'];
    if (stripos($type, 'varchar(500)') !== false || stripos($type, 'varchar(255)') !== false) {
        echo '<p><span style="color:green;">[OK]</span> mb_recommend 이미 확장되어 있습니다. (현재: ' . htmlspecialchars($type) . ')</p>';
    } else {
        $ok = @sql_query("ALTER TABLE {$tb} MODIFY COLUMN mb_recommend VARCHAR(500) NOT NULL DEFAULT '' COMMENT '추천인 mb_id (쉼표 구분 다중)'", false);
        echo '<p>'.($ok ? '<span style="color:green;">[OK]</span> mb_recommend VARCHAR(500) 확장 완료' : '<span style="color:red;">[FAIL]</span>').'</p>';
    }
}

echo '<p><a href="./member_form.php" class="btn btn_01">회원 수정으로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
