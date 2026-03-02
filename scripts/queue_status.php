<?php
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

echo "=== 전체 큐 (최근 20건) ===\n";
$q = sql_query("SELECT id, jr_id, status, error_msg, created_at, updated_at FROM g5_jobs_ai_queue ORDER BY id DESC LIMIT 20", false);
while ($row = sql_fetch_array($q)) {
    $err = $row['error_msg'] ? substr($row['error_msg'], 0, 60) : '-';
    echo "  ID={$row['id']} jr_id={$row['jr_id']} status={$row['status']} err={$err} updated={$row['updated_at']}\n";
}

echo "\n=== AI 콘텐츠 (최근 20건) ===\n";
$q2 = sql_query("SELECT id, jr_id, version, is_active, created_at, LEFT(ai_data, 100) as preview FROM g5_jobs_ai_content ORDER BY id DESC LIMIT 20", false);
while ($row = sql_fetch_array($q2)) {
    echo "  ID={$row['id']} jr_id={$row['jr_id']} v{$row['version']} active={$row['is_active']} created={$row['created_at']}\n";
    echo "    preview: " . str_replace("\n", " ", $row['preview']) . "\n";
}

echo "\n=== 큐에 jr_id=2,3,4 재추가 ===\n";
$ids = [2, 3, 4];
$now = date('Y-m-d H:i:s');
foreach ($ids as $jid) {
    $exists = sql_fetch("SELECT id FROM g5_jobs_ai_queue WHERE jr_id = {$jid} AND status IN ('pending','processing')");
    if ($exists && $exists['id']) {
        echo "jr_id={$jid}: 이미 큐에 있음, 스킵\n";
        continue;
    }
    sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, created_at, updated_at) VALUES ({$jid}, 'pending', '{$now}', '{$now}')", false);
    echo "jr_id={$jid}: pending으로 추가\n";
}

echo "\n=== 확인: pending 큐 ===\n";
$q3 = sql_query("SELECT id, jr_id, status FROM g5_jobs_ai_queue WHERE status = 'pending' ORDER BY id", false);
while ($row = sql_fetch_array($q3)) {
    echo "  큐ID={$row['id']} jr_id={$row['jr_id']}\n";
}
echo "완료\n";
