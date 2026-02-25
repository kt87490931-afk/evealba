<?php
/**
 * 어드민 - 채용정보등록 입금확인 취소 (입금대기중으로 되돌림)
 */
$sub_menu = '910100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$jr_ids = array();
if (isset($_POST['jr_ids']) && $_POST['jr_ids'] !== '') {
    $jr_ids = array_map('intval', array_filter(explode(',', $_POST['jr_ids'])));
} elseif (isset($_GET['jr_ids']) && $_GET['jr_ids'] !== '') {
    $jr_ids = array_map('intval', array_filter(explode(',', $_GET['jr_ids'])));
} elseif (isset($_POST['chk']) && is_array($_POST['chk'])) {
    foreach ($_POST['chk'] as $v) { $id = (int)$v; if ($id) $jr_ids[] = $id; }
}
$jr_id = isset($_REQUEST['jr_id']) ? (int)$_REQUEST['jr_id'] : 0;
if ($jr_id) {
    $jr_ids = array($jr_id);
}

if (empty($jr_ids)) {
    alert('취소할 항목을 선택하세요.', './jobs_register_list.php');
}

$cancel_ok = 0;
foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_status, jr_payment_confirmed FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) continue;
    if (!$row['jr_payment_confirmed']) continue;
    if ($row['jr_status'] !== 'pending') continue;

    sql_query("UPDATE g5_jobs_register SET jr_payment_confirmed = 0 WHERE jr_id = '{$id}'");
    $cancel_ok++;
}

$msg = $cancel_ok ? $cancel_ok . '건 입금대기중으로 변경되었습니다.' : '취소할 건이 없습니다.';
alert($msg, './jobs_register_list.php');
