<?php
/**
 * 어드민 - 채용정보등록 승인 처리 (입금확인 후 서류검수 완료 시)
 * 승인 시 광고 노출, 광고기간은 승인일부터 시작
 */
$sub_menu = '910100';
require_once './_common.php';

// 디버그 로그 헬퍼 (원인 파악 후 제거 예정)
$g5_jr_debug_log = (defined('G5_DATA_PATH') ? G5_DATA_PATH : (dirname(__FILE__) . '/../data')) . '/log/jobs_register_debug.log';
@mkdir(dirname($g5_jr_debug_log), 0755, true);
function _jr_debug($msg, $logfile = null) {
    global $g5_jr_debug_log;
    $file = $logfile ?: $g5_jr_debug_log;
    @file_put_contents($file, date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND | LOCK_EX);
}

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

_jr_debug('[APPROVE] REQUEST_METHOD=' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' POST_jr_ids=' . ($_POST['jr_ids'] ?? '') . ' GET_jr_ids=' . ($_GET['jr_ids'] ?? '') . ' REQUEST_jr_id=' . ($_REQUEST['jr_id'] ?? ''));

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
_jr_debug('[APPROVE] parsed jr_ids=' . json_encode($jr_ids));

if (empty($jr_ids)) {
    _jr_debug('[APPROVE] empty jr_ids -> alert');
    alert('승인할 항목을 선택하세요.', './jobs_register_list.php');
}

$approve_ok = 0;
$approve_fail = array();

foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_payment_confirmed, jr_approved, jr_title, jr_ai_title, jr_subject_display, jr_ad_period, jr_approved_datetime FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) {
        _jr_debug("[APPROVE] jr_id={$id} row not found");
        continue;
    }
    _jr_debug("[APPROVE] jr_id={$id} row: payment=" . (int)($row['jr_payment_confirmed'] ?? 0) . " approved=" . (int)($row['jr_approved'] ?? 0));

    if ($row['jr_approved']) {
        $approve_fail[] = $id . ': 이미 승인됨';
        _jr_debug("[APPROVE] jr_id={$id} skip: already approved");
        continue;
    }
    if (!$row['jr_payment_confirmed']) {
        $approve_fail[] = $id . ': 입금확인 후 승인 가능';
        _jr_debug("[APPROVE] jr_id={$id} skip: payment not confirmed");
        continue;
    }

    $display_title = (!empty($row['jr_ai_title'])) ? $row['jr_ai_title'] : $row['jr_title'];
    $display_title = $display_title ?: $row['jr_subject_display'];
    $display_esc = sql_escape_string($display_title);

    $approved_dt = date('Y-m-d H:i:s');
    $end_date = date('Y-m-d', strtotime('+' . (int)$row['jr_ad_period'] . ' days'));

    sql_query("UPDATE g5_jobs_register SET jr_approved = 1, jr_approved_datetime = '{$approved_dt}', jr_end_date = '{$end_date}', jr_status = 'ongoing', jr_subject_display = '{$display_esc}' WHERE jr_id = '{$id}'");
    $approve_ok++;
    _jr_debug("[APPROVE] jr_id={$id} OK");
}

$msg = $approve_ok ? $approve_ok . '건 승인 완료. 광고가 노출됩니다.' : '승인된 건이 없습니다.';
if (!empty($approve_fail)) {
    $msg .= ' (' . implode(', ', $approve_fail) . ')';
}
_jr_debug('[APPROVE] result approve_ok=' . $approve_ok . ' msg=' . $msg);
alert($msg, './jobs_register_list.php');
