<?php
/**
 * 어드민 - 채용정보등록 입금확인 처리
 * 입금확인 시 즉시 진행중(ongoing)으로 전환 → AI 업소설명 진행 가능
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
    alert('입금확인할 항목을 선택하세요.', './jobs_register_list.php');
}

$confirm_ok = 0;
foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_status, jr_payment_confirmed, jr_title, jr_subject_display, jr_ad_period FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) continue;
    if ($row['jr_payment_confirmed']) continue;
    if ($row['jr_status'] !== 'pending') continue;

    $display_title = ($row['jr_title'] ?? '') ?: ($row['jr_subject_display'] ?? '');
    $display_esc = sql_escape_string($display_title);
    $approved_dt = date('Y-m-d H:i:s');
    $end_date = date('Y-m-d', strtotime('+' . (int)($row['jr_ad_period'] ?? 30) . ' days'));

    sql_query("UPDATE g5_jobs_register SET jr_payment_confirmed = 1, jr_approved = 1, jr_approved_datetime = '{$approved_dt}', jr_end_date = '{$end_date}', jr_status = 'ongoing', jr_subject_display = '{$display_esc}' WHERE jr_id = '{$id}'");
    $confirm_ok++;
}

$msg = $confirm_ok ? $confirm_ok . '건 입금확인 완료. 진행중으로 전환되었습니다.' : '입금확인할 건이 없습니다.';
alert($msg, './jobs_register_list.php');
