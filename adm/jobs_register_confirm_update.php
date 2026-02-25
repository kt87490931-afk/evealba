<?php
/**
 * 어드민 - 채용정보등록 입금확인 처리
 * 입금확인 후 승인(서류검수)을 별도 진행해야 광고 노출
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

_jr_debug('[CONFIRM] REQUEST_METHOD=' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' POST_jr_ids=' . ($_POST['jr_ids'] ?? '') . ' GET_jr_ids=' . ($_GET['jr_ids'] ?? '') . ' REQUEST_jr_id=' . ($_REQUEST['jr_id'] ?? ''));

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
_jr_debug('[CONFIRM] parsed jr_ids=' . json_encode($jr_ids));

if (empty($jr_ids)) {
    _jr_debug('[CONFIRM] empty jr_ids -> alert');
    alert('입금확인할 항목을 선택하세요.', './jobs_register_list.php');
}

$confirm_ok = 0;
foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_status, jr_payment_confirmed FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) {
        _jr_debug("[CONFIRM] jr_id={$id} row not found");
        continue;
    }
    if ($row['jr_payment_confirmed']) {
        _jr_debug("[CONFIRM] jr_id={$id} skip: already payment_confirmed");
        continue;
    }
    if ($row['jr_status'] !== 'pending') {
        _jr_debug("[CONFIRM] jr_id={$id} skip: status={$row['jr_status']} not pending");
        continue;
    }

    sql_query("UPDATE g5_jobs_register SET jr_payment_confirmed = 1 WHERE jr_id = '{$id}'");
    $confirm_ok++;
    _jr_debug("[CONFIRM] jr_id={$id} OK");
}

$msg = $confirm_ok ? $confirm_ok . '건 입금확인 완료. 서류검수 후 승인해주세요.' : '입금확인할 건이 없습니다.';
_jr_debug('[CONFIRM] result confirm_ok=' . $confirm_ok . ' msg=' . $msg);
alert($msg, './jobs_register_list.php');
