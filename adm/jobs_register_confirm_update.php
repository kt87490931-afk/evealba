<?php
/**
 * 어드민 - 채용정보등록 입금확인 처리 (pending → ongoing)
 */
$sub_menu = '300830';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$jr_id = isset($_REQUEST['jr_id']) ? (int)$_REQUEST['jr_id'] : 0;
if (!$jr_id) {
    alert('잘못된 요청입니다.', './jobs_register_list.php');
}

$row = sql_fetch("SELECT jr_id, jr_status, jr_title, jr_ai_title, jr_subject_display FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
if (!$row) {
    alert('해당 채용정보가 없습니다.', './jobs_register_list.php');
}
if ($row['jr_status'] !== 'pending') {
    alert('입금대기중인 건만 입금확인할 수 있습니다.', './jobs_register_list.php');
}

// 진행중으로 변경, 표시 제목: AI제목 있으면 사용, 없으면 jr_title 사용
$display_title = (!empty($row['jr_ai_title'])) ? $row['jr_ai_title'] : $row['jr_title'];
$display_title = $display_title ?: $row['jr_subject_display'];
$display_esc = sql_escape_string($display_title);

sql_query("UPDATE g5_jobs_register SET jr_status = 'ongoing', jr_subject_display = '{$display_esc}' WHERE jr_id = '{$jr_id}'");
alert('입금확인 완료. 광고가 개재됩니다.', './jobs_register_list.php');
