<?php
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

echo "=== 실패한 큐 항목 리셋 ===\n";
sql_query("UPDATE g5_jobs_ai_queue SET status = 'pending', retry_count = 0, error_msg = '' WHERE jr_id IN (2,3,4) AND status = 'failed'", false);
echo "failed -> pending 리셋 완료\n";

echo "\n=== 현재 큐 상태 ===\n";
$q = sql_query("SELECT id, jr_id, status, error_msg FROM g5_jobs_ai_queue WHERE jr_id IN (2,3,4) ORDER BY id DESC", false);
while ($row = sql_fetch_array($q)) {
    echo "  ID={$row['id']} jr_id={$row['jr_id']} status={$row['status']} err=" . ($row['error_msg'] ?: '-') . "\n";
}

echo "\n완료. 크론이 자동 처리합니다.\n";
