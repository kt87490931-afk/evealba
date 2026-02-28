<?php
/**
 * 어드민 - 채용정보등록 입금확인 처리
 * 입금확인 시 jr_payment_confirmed=1 설정 (회원 진행중 페이지에 입금확인 표시)
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
$queued = 0;

// g5_jobs_ai_queue 테이블 없으면 생성
$tbq = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
if (!sql_num_rows($tbq)) {
    $create_sql = "CREATE TABLE IF NOT EXISTS `g5_jobs_ai_queue` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `jr_id` int unsigned NOT NULL,
      `status` varchar(20) NOT NULL DEFAULT 'pending',
      `retry_count` int NOT NULL DEFAULT 0,
      `error_msg` varchar(500) NOT NULL DEFAULT '',
      `created_at` datetime NOT NULL,
      `processed_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `status` (`status`),
      KEY `jr_id` (`jr_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    sql_query($create_sql);
}

foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_status, jr_payment_confirmed, jr_data, jr_subject_display, jr_nickname, jr_company, jr_title, jr_ad_period FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) continue;
    if ($row['jr_payment_confirmed']) continue;
    if ($row['jr_status'] !== 'pending') continue;

    $ad_period = (int)$row['jr_ad_period'] ?: 30;
    $end_date = date('Y-m-d', strtotime("+{$ad_period} days"));
    sql_query("UPDATE g5_jobs_register SET 
        jr_payment_confirmed = 1, 
        jr_approved = 1, 
        jr_status = 'ongoing',
        jr_approved_datetime = '".G5_TIME_YMDHIS."',
        jr_end_date = '{$end_date}'
        WHERE jr_id = '{$id}'");
    $confirm_ok++;

    // 입금확인 시 AI 소개글 생성 대기열에 등록
    if (!function_exists('aic_get_active')) {
        @include_once(G5_LIB_PATH . '/jobs_ai_content.lib.php');
    }
    $has_ai = function_exists('aic_get_active') ? (bool)aic_get_active($id) : false;
    if (!$has_ai) {
        $jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
        $has_ai = !empty($jr_data['ai_content']) || !empty($jr_data['ai_intro']);
    }
    if (!$has_ai) {
        $q_check = sql_fetch("SELECT id FROM g5_jobs_ai_queue WHERE jr_id = '{$id}' AND status IN ('pending','processing') LIMIT 1", false);
        if (!$q_check) {
            sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, created_at) VALUES ('{$id}', 'pending', '".G5_TIME_YMDHIS."')");
            $queued++;
        }
    }
}

// 큐 등록 건이 있으면 즉시 AI 생성 워커 실행 (백그라운드)
if ($queued > 0) {
    $base = dirname(__DIR__);
    $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
    @pclose(popen(sprintf('cd %s && %s jobs_ai_queue_process.php --limit=%d > /dev/null 2>&1 &', escapeshellarg($base), escapeshellarg($php), min($queued, 5)), 'r'));
}

$msg = $confirm_ok ? $confirm_ok . '건 입금확인 완료.' . ($queued ? ' AI 소개글 생성이 시작되었습니다.' : '') : '입금확인할 건이 없습니다.';
alert($msg, './jobs_register_list.php');
