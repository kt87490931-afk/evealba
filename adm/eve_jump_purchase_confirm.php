<?php
/**
 * 어드민 - 점프 추가 구매 입금확인 처리
 * 잔여 횟수에 추가하고, 자동 점프 간격 재계산
 */
$sub_menu = '910910';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$jp_id = isset($_POST['jp_id']) ? (int)$_POST['jp_id'] : 0;
$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;

if (!$jp_id || !$jr_id) {
    alert('잘못된 요청입니다.', './eve_jump_health.php');
}

$purchase = sql_fetch("SELECT * FROM g5_jobs_jump_purchase WHERE jp_id = '{$jp_id}' AND jp_status = 'pending'");
if (!$purchase) {
    alert('이미 처리되었거나 존재하지 않는 구매입니다.', './eve_jump_health.php');
}

$count = (int)$purchase['jp_count'];
$now = date('Y-m-d H:i:s');

sql_query("UPDATE g5_jobs_jump_purchase SET jp_status = 'confirmed', jp_confirmed_datetime = '{$now}' WHERE jp_id = '{$jp_id}'");

sql_query("UPDATE g5_jobs_register SET
    jr_jump_total = jr_jump_total + {$count},
    jr_jump_remain = jr_jump_remain + {$count}
    WHERE jr_id = '{$jr_id}'");

$row = sql_fetch("SELECT jr_auto_jump, jr_jump_remain, jr_end_date FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
if ($row && (int)$row['jr_auto_jump'] === 1 && (int)$row['jr_jump_remain'] > 0) {
    $end_date = $row['jr_end_date'];
    $today = date('Y-m-d');
    $days_left = max(1, (strtotime($end_date) - strtotime($today)) / 86400);
    $mins_left = $days_left * 24 * 60;
    $remain = (int)$row['jr_jump_remain'];
    $interval = max(10, floor($mins_left / $remain));
    $offset = rand(-5, 5);
    $next_ts = time() + ($interval + $offset) * 60;
    $auto_next = date('Y-m-d H:i:s', $next_ts);
    sql_query("UPDATE g5_jobs_register SET jr_auto_jump_next = '{$auto_next}' WHERE jr_id = '{$jr_id}'");
}

alert('점프 ' . number_format($count) . '회가 지급되었습니다. (자동 간격 재계산 완료)', './eve_jump_health.php');
