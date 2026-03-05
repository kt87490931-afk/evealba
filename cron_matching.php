<?php
/**
 * 매칭시스템 Cron
 * 매일 실행: 기업회원·이브회원 매칭 + 쪽지 발송
 * 사용: php cron_matching.php
 * 또는 crontab: 0 6 * * * cd /var/www/evealba && php cron_matching.php
 */
define('_RUN_CRON_', true);
$start = microtime(true);

$common_path = __DIR__ . '/common.php';
if (!file_exists($common_path)) {
    echo "common.php not found\n";
    exit(1);
}
include_once($common_path);

if (!function_exists('sql_query')) {
    echo "DB not available\n";
    exit(1);
}

$log_dir = defined('G5_DATA_PATH') ? G5_DATA_PATH . '/log' : __DIR__ . '/data/log';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/matching_' . date('Ymd') . '.log';

function _matching_log($msg, $log_file) {
    $line = date('Y-m-d H:i:s') . ' ' . $msg . "\n";
    if ($log_file && is_writable(dirname($log_file))) {
        @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
    }
    echo $line;
}

if (!function_exists('ev_matching_run')) {
    if (is_file(__DIR__ . '/lib/ev_matching.lib.php')) {
        include_once(__DIR__ . '/lib/ev_matching.lib.php');
    }
    if (!function_exists('ev_matching_run')) {
        _matching_log("ERROR: ev_matching.lib.php not loaded", $log_file);
        exit(1);
    }
}

if (!function_exists('ev_send_memo') && is_file(__DIR__ . '/lib/ev_memo.lib.php')) {
    include_once(__DIR__ . '/lib/ev_memo.lib.php');
}

$result = ev_matching_run();
$duration_ms = (int)((microtime(true) - $start) * 1000);

$diag = isset($result['diag']) ? $result['diag'] : array();
$diag['duration_ms'] = $duration_ms;
$diag_str = json_encode($diag, JSON_UNESCAPED_UNICODE);

_matching_log("RESULT ok=" . ($result['ok'] ?? 0) . " msg=" . ($result['msg'] ?? '') . " diag=" . $diag_str, $log_file);

echo "Matching done. ok=" . ($result['ok'] ?? 0) . " pairs=" . (isset($diag['pairs']) ? $diag['pairs'] : 0) . " duration_ms={$duration_ms}\n";
