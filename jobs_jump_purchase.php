<?php
/**
 * 점프 추가 구매 API
 * POST: jr_id, package (200/450/700/1200/2000)
 */
@error_reporting(0);
@ini_set('display_errors', '0');
ob_start();
include_once('./_common.php');
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

function _jpp_json($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$is_member) {
    _jpp_json(array('ok' => 0, 'msg' => '로그인이 필요합니다.'));
}

$packages = array(
    200  => 10000,
    450  => 20000,
    700  => 30000,
    1200 => 50000,
    2000 => 80000,
);

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
$pkg = isset($_POST['package']) ? (int)$_POST['package'] : 0;

if (!$jr_id) {
    _jpp_json(array('ok' => 0, 'msg' => '광고 ID가 필요합니다.'));
}
if (!isset($packages[$pkg])) {
    _jpp_json(array('ok' => 0, 'msg' => '유효하지 않은 패키지입니다.'));
}

$mb_id_esc = addslashes($member['mb_id']);
$row = sql_fetch("SELECT jr_id, mb_id, jr_status, jr_jump_remain, jr_jump_total, jr_end_date, jr_auto_jump
    FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");

if (!$row || $row['mb_id'] !== $member['mb_id']) {
    _jpp_json(array('ok' => 0, 'msg' => '본인의 광고만 구매할 수 있습니다.'));
}
if ($row['jr_status'] !== 'ongoing') {
    _jpp_json(array('ok' => 0, 'msg' => '진행중인 광고만 구매할 수 있습니다.'));
}

$amount = $packages[$pkg];
$now = date('Y-m-d H:i:s');

sql_query("INSERT INTO g5_jobs_jump_purchase (jr_id, mb_id, jp_count, jp_amount, jp_status, jp_datetime)
    VALUES ('{$jr_id}', '{$mb_id_esc}', '{$pkg}', '{$amount}', 'pending', '{$now}')");

$jp_id = sql_insert_id();

_jpp_json(array(
    'ok' => 1,
    'msg' => '점프 ' . number_format($pkg) . '회 구매 신청이 접수되었습니다. 입금 확인 후 지급됩니다.',
    'jp_id' => (int)$jp_id,
    'count' => $pkg,
    'amount' => $amount
));
