<?php
/**
 * 어드민 - 채용정보등록 승인 처리 (입금확인 후 서류검수 완료 시)
 * 승인 시 광고 노출, 광고기간은 승인일부터 시작
 */
$sub_menu = '910100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$jr_ids = isset($_POST['chk']) && is_array($_POST['chk']) ? $_POST['chk'] : array();
$jr_id = isset($_REQUEST['jr_id']) ? (int)$_REQUEST['jr_id'] : 0;

// 단건 승인 (GET/LINK)
if ($jr_id) {
    $jr_ids = array($jr_id);
}

if (empty($jr_ids)) {
    alert('승인할 항목을 선택하세요.', './jobs_register_list.php');
}

$approve_ok = 0;
$approve_fail = array();

foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_payment_confirmed, jr_approved, jr_title, jr_ai_title, jr_subject_display, jr_ad_period, jr_approved_datetime FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) continue;

    if ($row['jr_approved']) {
        $approve_fail[] = $id . ': 이미 승인됨';
        continue;
    }
    if (!$row['jr_payment_confirmed']) {
        $approve_fail[] = $id . ': 입금확인 후 승인 가능';
        continue;
    }

    $display_title = (!empty($row['jr_ai_title'])) ? $row['jr_ai_title'] : $row['jr_title'];
    $display_title = $display_title ?: $row['jr_subject_display'];
    $display_esc = sql_escape_string($display_title);

    $approved_dt = date('Y-m-d H:i:s');
    $end_date = date('Y-m-d', strtotime('+' . (int)$row['jr_ad_period'] . ' days'));

    sql_query("UPDATE g5_jobs_register SET jr_approved = 1, jr_approved_datetime = '{$approved_dt}', jr_end_date = '{$end_date}', jr_status = 'ongoing', jr_subject_display = '{$display_esc}' WHERE jr_id = '{$id}'");
    $approve_ok++;
}

$msg = $approve_ok ? $approve_ok . '건 승인 완료. 광고가 노출됩니다.' : '승인된 건이 없습니다.';
if (!empty($approve_fail)) {
    $msg .= ' (' . implode(', ', $approve_fail) . ')';
}
alert($msg, './jobs_register_list.php');
