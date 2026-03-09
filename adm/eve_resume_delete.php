<?php
/**
 * 어드민 - 이력서 삭제 (작성 취소)
 * 삭제 시 추천인 이력서 카운터 자동 차감 (DB 조회로 반영)
 */
$sub_menu = '910970';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();
$rs_id = isset($_GET['rs_id']) ? (int)$_GET['rs_id'] : 0;
if ($rs_id <= 0) {
    alert('잘못된 요청입니다.', './eve_referral_manage.php');
}

$tb = 'g5_resume';
$row = sql_fetch("SELECT rs_id, mb_id FROM {$tb} WHERE rs_id = '{$rs_id}'");
if (!$row) {
    alert('해당 이력서가 없습니다.', './eve_referral_manage.php');
}

sql_query("DELETE FROM {$tb} WHERE rs_id = '{$rs_id}'");

alert('이력서가 삭제되었습니다. (추천인 이력서 카운터 자동 차감)', './eve_referral_manage.php');
