<?php
/**
 * AI 미생성 건을 큐에 수동 등록
 * 입금확인됐으나 AI가 없는 jr_id를 g5_jobs_ai_queue에 추가
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
}
if (empty($jr_ids)) {
    alert('AI 큐에 등록할 항목을 선택하세요.', './jobs_register_list.php');
}

$tbq = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
if (!sql_num_rows($tbq)) {
    alert('g5_jobs_ai_queue 테이블이 없습니다.', './jobs_register_list.php');
}

$added = 0;
foreach ($jr_ids as $jr_id) {
    if (!$jr_id) continue;
    $row = sql_fetch("SELECT jr_id, jr_payment_confirmed, jr_data FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
    if (!$row || !$row['jr_payment_confirmed']) continue;

    $jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
    $has_ai = !empty($jr_data['ai_content']) || !empty($jr_data['ai_intro']);
    if ($has_ai) continue;

    $q_check = sql_fetch("SELECT id FROM g5_jobs_ai_queue WHERE jr_id = '{$jr_id}' AND status IN ('pending','processing') LIMIT 1", false);
    if ($q_check) continue;

    sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, created_at) VALUES ('{$jr_id}', 'pending', '".G5_TIME_YMDHIS."')");
    $added++;
}

$msg = $added ? "{$added}건 AI 큐에 등록되었습니다. 크론이 처리합니다." : "등록할 건이 없습니다. (입금확인됐고 AI 미생성인 건만 등록됩니다)";
alert($msg, './jobs_register_list.php');
