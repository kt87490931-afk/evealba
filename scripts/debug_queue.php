<?php
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

echo "=== 큐 테이블에 jr_id=2,3,4 추가 ===\n";
$now = date('Y-m-d H:i:s');
$ids = [2, 3, 4];
foreach ($ids as $jid) {
    $chk = sql_fetch("SELECT id FROM g5_jobs_ai_queue WHERE jr_id = {$jid} AND status IN ('pending','processing')");
    if ($chk && !empty($chk['id'])) {
        echo "jr_id={$jid}: 이미 큐에 있음 (ID={$chk['id']}), 스킵\n";
        continue;
    }
    sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, retry_count, error_msg, created_at) VALUES ({$jid}, 'pending', 0, '', '{$now}')", false);
    echo "jr_id={$jid}: pending으로 추가 완료\n";
}

echo "\n=== 큐 전체 확인 ===\n";
$r = sql_query("SELECT id, jr_id, status, error_msg, created_at, processed_at FROM g5_jobs_ai_queue ORDER BY id DESC LIMIT 10", false);
while ($row = sql_fetch_array($r)) {
    $err = $row['error_msg'] ? substr($row['error_msg'], 0, 60) : '-';
    echo "  ID={$row['id']} jr_id={$row['jr_id']} status={$row['status']} err={$err}\n";
}

echo "\n=== AI 콘텐츠 비활성화 상태 확인 ===\n";
$r2 = sql_query("SELECT jr_id, COUNT(*) as cnt, SUM(is_active) as active_cnt FROM g5_jobs_ai_content GROUP BY jr_id", false);
while ($row = sql_fetch_array($r2)) {
    echo "  jr_id={$row['jr_id']}: 총{$row['cnt']}건, 활성{$row['active_cnt']}건\n";
}

echo "\n완료. 이제 큐 프로세서를 실행하세요.\n";
