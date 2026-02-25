<?php
/**
 * 어드민 - 채용정보등록 입금확인 처리
 * 입금확인 후 승인(서류검수)을 별도 진행해야 광고 노출
 */
$sub_menu = '300830';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$jr_ids = isset($_POST['chk']) && is_array($_POST['chk']) ? $_POST['chk'] : array();
$jr_id = isset($_REQUEST['jr_id']) ? (int)$_REQUEST['jr_id'] : 0;

if ($jr_id) {
    $jr_ids = array($jr_id);
}

if (empty($jr_ids)) {
    alert('입금확인할 항목을 선택하세요.', './jobs_register_list.php');
}

$confirm_ok = 0;
foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_status, jr_payment_confirmed FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) continue;
    if ($row['jr_payment_confirmed']) continue; // 이미 입금확인
    if ($row['jr_status'] !== 'pending') continue;

    sql_query("UPDATE g5_jobs_register SET jr_payment_confirmed = 1 WHERE jr_id = '{$id}'");
    $confirm_ok++;
}

$msg = $confirm_ok ? $confirm_ok . '건 입금확인 완료. 서류검수 후 승인해주세요.' : '입금확인할 건이 없습니다.';
alert($msg, './jobs_register_list.php');
