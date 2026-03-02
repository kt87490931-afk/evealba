<?php
/**
 * 1. g5_jobs_ai_content, g5_jobs_ai_queue 테이블 utf8mb4 변환
 * 2. 큐 프로세서 즉시 실행
 */
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

echo "=== DB 테이블 utf8mb4 변환 ===\n";

sql_query("ALTER TABLE g5_jobs_ai_content CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci", false);
echo "g5_jobs_ai_content -> utf8mb4 완료\n";

sql_query("ALTER TABLE g5_jobs_ai_queue CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci", false);
echo "g5_jobs_ai_queue -> utf8mb4 완료\n";

echo "\n=== 현재 큐 상태 (pending) ===\n";
$q = sql_query("SELECT id, jr_id, status, created_at FROM g5_jobs_ai_queue WHERE status = 'pending' ORDER BY id", false);
$cnt = 0;
while ($row = sql_fetch_array($q)) {
    echo "  큐ID={$row['id']} jr_id={$row['jr_id']} status={$row['status']} created={$row['created_at']}\n";
    $cnt++;
}
echo "총 {$cnt}건 pending\n";

echo "\n=== 큐 프로세서 실행 ===\n";
echo "(별도로 실행: php jobs_ai_queue_process.php --limit=3)\n";
