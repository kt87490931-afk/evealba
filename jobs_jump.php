<?php
/**
 * 수동 점프 API
 * POST: jr_id
 */
@error_reporting(0);
@ini_set('display_errors', '0');
ob_start();
include_once('./_common.php');
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

function _jump_json($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$is_member) {
    _jump_json(array('ok' => 0, 'msg' => '로그인이 필요합니다.'));
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
if (!$jr_id) {
    _jump_json(array('ok' => 0, 'msg' => '광고 ID가 필요합니다.'));
}

$mb_id_esc = addslashes($member['mb_id']);

$row = sql_fetch("SELECT jr_id, mb_id, jr_status, jr_jump_remain, jr_jump_used, jr_jump_total,
    jr_jump_datetime, jr_end_date, jr_auto_jump, jr_auto_jump_next, jr_ad_labels
    FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");

if (!$row) {
    _jump_json(array('ok' => 0, 'msg' => '존재하지 않는 광고입니다.'));
}
if ($row['mb_id'] !== $member['mb_id']) {
    _jump_json(array('ok' => 0, 'msg' => '본인의 광고만 점프할 수 있습니다.'));
}
if ($row['jr_status'] !== 'ongoing') {
    _jump_json(array('ok' => 0, 'msg' => '진행중인 광고만 점프할 수 있습니다.'));
}

$remain = (int)$row['jr_jump_remain'];
if ($remain <= 0) {
    _jump_json(array('ok' => 0, 'msg' => '잔여 점프 횟수가 없습니다. 추가 구매해 주세요.', 'remain' => 0));
}

$now = date('Y-m-d H:i:s');
$remain_before = $remain;
$remain_after = $remain - 1;
$used = (int)$row['jr_jump_used'] + 1;

sql_query("UPDATE g5_jobs_register SET
    jr_jump_remain = '{$remain_after}',
    jr_jump_used = '{$used}',
    jr_jump_datetime = '{$now}'
    WHERE jr_id = '{$jr_id}' AND jr_jump_remain > 0");

if (sql_affected_rows() === 0) {
    _jump_json(array('ok' => 0, 'msg' => '점프 처리에 실패했습니다. 다시 시도해 주세요.'));
}

sql_query("INSERT INTO g5_jobs_jump_log (jr_id, mb_id, jl_type, jl_remain_before, jl_remain_after, jl_datetime)
    VALUES ('{$jr_id}', '{$mb_id_esc}', 'manual', '{$remain_before}', '{$remain_after}', '{$now}')");

$auto_next = '';
if ((int)$row['jr_auto_jump'] === 1 && $remain_after > 0) {
    $end_date = $row['jr_end_date'];
    $days_remain = max(1, (strtotime($end_date) - strtotime(date('Y-m-d'))) / 86400);
    $mins_remain = $days_remain * 24 * 60;
    $interval = max(10, floor($mins_remain / $remain_after));
    $offset = rand(-5, 5);
    $next_ts = time() + ($interval + $offset) * 60;
    $auto_next = date('Y-m-d H:i:s', $next_ts);
    sql_query("UPDATE g5_jobs_register SET jr_auto_jump_next = '{$auto_next}' WHERE jr_id = '{$jr_id}'");
}

_jump_json(array(
    'ok' => 1,
    'msg' => '점프 완료!',
    'remain' => $remain_after,
    'used' => $used,
    'total' => (int)$row['jr_jump_total'],
    'jump_datetime' => $now,
    'auto_next' => $auto_next
));
