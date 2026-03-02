<?php
/**
 * 어드민 — AI 콘텐츠 액션 처리 (재생성, 재시도, 버전 활성화)
 */
$sub_menu = '910200';
require_once './_common.php';
include_once(G5_LIB_PATH . '/jobs_ai_content.lib.php');

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$act = isset($_GET['act']) ? trim($_GET['act']) : (isset($_POST['act']) ? trim($_POST['act']) : '');
$jr_id = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : (isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0);

if ($act === 'activate') {
    $version = isset($_GET['version']) ? (int)$_GET['version'] : 0;
    if (!$jr_id || !$version) {
        alert('잘못된 요청입니다.', './jobs_ai_content_list.php');
    }
    aic_activate_version($jr_id, $version);
    alert("jr_id={$jr_id} v{$version} 활성화 완료.", './jobs_ai_content_list.php?jr_id=' . $jr_id);
}

if ($act === 'regenerate') {
    if (!$jr_id) {
        alert('jr_id가 필요합니다.', './jobs_ai_content_list.php');
    }
    $tbq = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
    if (!$tbq || !sql_num_rows($tbq)) {
        alert('g5_jobs_ai_queue 테이블이 없습니다.', './jobs_ai_content_list.php');
    }
    $q_check = sql_fetch("SELECT id FROM g5_jobs_ai_queue WHERE jr_id = '{$jr_id}' AND status IN ('pending','processing') LIMIT 1");
    if ($q_check) {
        alert('이미 대기열에 있습니다.', './jobs_ai_content_list.php?jr_id=' . $jr_id);
    }
    sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, created_at) VALUES ('{$jr_id}', 'pending', '".G5_TIME_YMDHIS."')");

    $base = dirname(__DIR__);
    $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
    @pclose(popen(sprintf('cd %s && %s jobs_ai_queue_process.php --limit=1 > /dev/null 2>&1 &', escapeshellarg($base), escapeshellarg($php)), 'r'));
    alert('재생성 요청이 등록되었습니다. 잠시 후 새로고침하세요.', './jobs_ai_content_list.php?jr_id=' . $jr_id);
}

if ($act === 'retry') {
    if (!$jr_id) {
        alert('jr_id가 필요합니다.', './jobs_ai_content_list.php');
    }
    sql_query("UPDATE g5_jobs_ai_queue SET status = 'pending', retry_count = 0, error_msg = '' WHERE jr_id = '{$jr_id}' AND status IN ('failed','processing')");

    $base = dirname(__DIR__);
    $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
    @pclose(popen(sprintf('cd %s && %s jobs_ai_queue_process.php --limit=1 > /dev/null 2>&1 &', escapeshellarg($base), escapeshellarg($php)), 'r'));
    alert('재시도 요청이 등록되었습니다.', './jobs_ai_content_list.php');
}

if ($act === 'retry_all') {
    $cnt = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status IN ('failed','processing')")['c'];
    if ($cnt > 0) {
        sql_query("UPDATE g5_jobs_ai_queue SET status = 'pending', retry_count = 0, error_msg = '' WHERE status IN ('failed','processing')");
        $base = dirname(__DIR__);
        $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
        @pclose(popen(sprintf('cd %s && %s jobs_ai_queue_process.php --limit=%d > /dev/null 2>&1 &', escapeshellarg($base), escapeshellarg($php), min($cnt, 5)), 'r'));
    }
    alert("{$cnt}건 재시도 요청이 등록되었습니다.", './jobs_ai_content_list.php');
}

if ($act === 'reset_stuck') {
    $stuck_cnt = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status = 'processing'")['c'];
    if ($stuck_cnt > 0) {
        sql_query("UPDATE g5_jobs_ai_queue SET status = 'pending', retry_count = 0, error_msg = 'admin manual reset' WHERE status = 'processing'");
    }
    alert("{$stuck_cnt}건의 고착 항목을 초기화했습니다.", './jobs_ai_content_list.php');
}

if ($act === 'test_api_key') {
    $ext_dir = dirname(__DIR__) . '/extend';
    if (file_exists($ext_dir . '/gemini_config.php')) {
        include $ext_dir . '/gemini_config.php';
    }
    $api_key = isset($gemini_api_key) ? trim($gemini_api_key) : '';
    $model = isset($gemini_model) ? $gemini_model : 'gemini-3-flash-preview';

    if (empty($api_key)) {
        alert('API 키가 설정되지 않았습니다.', './jobs_ai_content_list.php');
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    $payload = json_encode(['contents' => [['parts' => [['text' => 'Hello test. Reply with OK only.']]]]]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . $api_key]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $resp = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err = curl_error($ch);
    curl_close($ch);

    $dec = json_decode($resp, true);
    $key_masked = substr($api_key, 0, 10) . '...' . substr($api_key, -4);

    if ($curl_err) {
        alert("API 테스트 실패 (cURL 오류)\n키: {$key_masked}\n모델: {$model}\n에러: {$curl_err}", './jobs_ai_content_list.php');
    } elseif ($http_code === 200 && isset($dec['candidates'])) {
        $reply = isset($dec['candidates'][0]['content']['parts'][0]['text']) ? trim($dec['candidates'][0]['content']['parts'][0]['text']) : '(응답 있음)';
        alert("API 테스트 성공!\n키: {$key_masked}\n모델: {$model}\nHTTP: {$http_code}\n응답: {$reply}", './jobs_ai_content_list.php');
    } else {
        $err_msg = isset($dec['error']['message']) ? $dec['error']['message'] : '알 수 없는 오류';
        alert("API 테스트 실패\n키: {$key_masked}\n모델: {$model}\nHTTP: {$http_code}\n에러: {$err_msg}", './jobs_ai_content_list.php');
    }
}

alert('알 수 없는 요청입니다.', './jobs_ai_content_list.php');
