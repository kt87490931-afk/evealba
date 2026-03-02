<?php
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

echo "=== jr_id=2,3 재생성 ===\n";
$ids = [2, 3];
$now = date('Y-m-d H:i:s');
foreach ($ids as $jid) {
    sql_query("UPDATE g5_jobs_ai_content SET is_active = 0 WHERE jr_id = {$jid}", false);
    echo "jr_id={$jid}: AI 콘텐츠 비활성화\n";
    
    $exists = sql_fetch("SELECT id FROM g5_jobs_ai_queue WHERE jr_id = {$jid} AND status IN ('pending','processing')");
    if ($exists && !empty($exists['id'])) {
        echo "jr_id={$jid}: 이미 큐에 있음, 스킵\n";
        continue;
    }
    sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, retry_count, error_msg, created_at) VALUES ({$jid}, 'pending', 0, '', '{$now}')", false);
    echo "jr_id={$jid}: 큐에 추가\n";
}

echo "\n=== 현재 큐 (pending) ===\n";
$q = sql_query("SELECT id, jr_id, status FROM g5_jobs_ai_queue WHERE status = 'pending'", false);
while ($row = sql_fetch_array($q)) {
    echo "  ID={$row['id']} jr_id={$row['jr_id']}\n";
}
echo "완료\n";
