<?php
/**
 * 자동 점프 ON/OFF 토글 API
 * POST: jr_id, auto_jump (1=ON, 0=OFF)
 */
@error_reporting(0);
@ini_set('display_errors', '0');
ob_start();
include_once('./_common.php');
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

function _atj_json($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$is_member) {
    _atj_json(array('ok' => 0, 'msg' => '로그인이 필요합니다.'));
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
$auto_jump = isset($_POST['auto_jump']) ? (int)$_POST['auto_jump'] : 0;

if (!$jr_id) {
    _atj_json(array('ok' => 0, 'msg' => '광고 ID가 필요합니다.'));
}

$mb_id_esc = addslashes($member['mb_id']);
$row = sql_fetch("SELECT jr_id, mb_id, jr_status, jr_jump_remain, jr_end_date
    FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");

if (!$row || $row['mb_id'] !== $member['mb_id']) {
    _atj_json(array('ok' => 0, 'msg' => '본인의 광고만 설정할 수 있습니다.'));
}
if ($row['jr_status'] !== 'ongoing') {
    _atj_json(array('ok' => 0, 'msg' => '진행중인 광고만 설정할 수 있습니다.'));
}

$remain = (int)$row['jr_jump_remain'];

if ($auto_jump && $remain <= 0) {
    _atj_json(array('ok' => 0, 'msg' => '잔여 점프 횟수가 없어 자동 점프를 활성화할 수 없습니다.'));
}

$auto_next = 'NULL';
$auto_next_display = '';

if ($auto_jump && $remain > 0) {
    $end_date = $row['jr_end_date'];
    $days_remain = max(1, (strtotime($end_date) - strtotime(date('Y-m-d'))) / 86400);
    $mins_remain = $days_remain * 24 * 60;
    $interval = max(10, floor($mins_remain / $remain));
    $offset = rand(-5, 5);
    $next_ts = time() + ($interval + $offset) * 60;
    $auto_next_val = date('Y-m-d H:i:s', $next_ts);
    $auto_next = "'{$auto_next_val}'";
    $auto_next_display = $auto_next_val;

    sql_query("UPDATE g5_jobs_register SET jr_auto_jump = 1, jr_auto_jump_next = {$auto_next} WHERE jr_id = '{$jr_id}'");
    $msg = '자동 점프가 활성화되었습니다. (간격: 약 ' . $interval . '분)';
} else {
    sql_query("UPDATE g5_jobs_register SET jr_auto_jump = 0, jr_auto_jump_next = NULL WHERE jr_id = '{$jr_id}'");
    $msg = '자동 점프가 비활성화되었습니다.';
}

_atj_json(array(
    'ok' => 1,
    'msg' => $msg,
    'auto_jump' => $auto_jump ? 1 : 0,
    'auto_next' => $auto_next_display,
    'remain' => $remain
));
