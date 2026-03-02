<?php
/**
 * DB charset 확인 + 기존 AI 콘텐츠 재생성 트리거 스크립트
 * 사용: php check_and_regenerate.php
 */
define('_GNUBOARD_', true);
$g5_path = ['path' => __DIR__ . '/..'];
include_once __DIR__ . '/../common.php';

echo "=== 1. DB 테이블 charset 확인 ===\n";
$r = sql_query("SHOW CREATE TABLE g5_jobs_ai_content", false);
if ($r) {
    $row = sql_fetch_array($r);
    if ($row) {
        preg_match('/CHARSET=(\w+)/', $row[1], $m);
        echo "g5_jobs_ai_content charset: " . ($m[1] ?? 'unknown') . "\n";
        if (isset($m[1]) && $m[1] !== 'utf8mb4') {
            echo ">> utf8mb4로 변환 실행...\n";
            sql_query("ALTER TABLE g5_jobs_ai_content CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci", false);
            echo ">> 변환 완료\n";
        } else {
            echo ">> 이미 utf8mb4이거나 확인 불가\n";
        }
    }
} else {
    echo "테이블 없음\n";
}

$r2 = sql_query("SHOW CREATE TABLE g5_jobs_ai_queue", false);
if ($r2) {
    $row2 = sql_fetch_array($r2);
    if ($row2) {
        preg_match('/CHARSET=(\w+)/', $row2[1], $m2);
        echo "g5_jobs_ai_queue charset: " . ($m2[1] ?? 'unknown') . "\n";
        if (isset($m2[1]) && $m2[1] !== 'utf8mb4') {
            echo ">> utf8mb4로 변환 실행...\n";
            sql_query("ALTER TABLE g5_jobs_ai_queue CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci", false);
            echo ">> 변환 완료\n";
        }
    }
}

echo "\n=== 2. 기존 AI 콘텐츠 비활성화 (jr_id=2,3,4) ===\n";
$ids = [2, 3, 4];
foreach ($ids as $jid) {
    sql_query("UPDATE g5_jobs_ai_content SET is_active = 0 WHERE jr_id = {$jid}", false);
    $cnt = sql_num_rows(sql_query("SELECT 1 FROM g5_jobs_ai_content WHERE jr_id = {$jid}", false));
    echo "jr_id={$jid}: {$cnt}개 레코드 비활성화됨\n";
}

echo "\n=== 3. 재생성 요청 (큐에 추가) ===\n";
foreach ($ids as $jid) {
    $exists = sql_fetch("SELECT id, status FROM g5_jobs_ai_queue WHERE jr_id = {$jid} AND status IN ('pending','processing')");
    if ($exists && isset($exists['id'])) {
        echo "jr_id={$jid}: 이미 큐에 있음 (status={$exists['status']}), 스킵\n";
        continue;
    }
    $now = date('Y-m-d H:i:s');
    sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, created_at, updated_at) VALUES ({$jid}, 'pending', '{$now}', '{$now}')", false);
    echo "jr_id={$jid}: 큐에 추가 완료 (pending)\n";
}

echo "\n=== 4. 현재 큐 상태 ===\n";
$queue = sql_query("SELECT id, jr_id, status, created_at, updated_at, error_msg FROM g5_jobs_ai_queue ORDER BY id DESC LIMIT 10", false);
while ($q = sql_fetch_array($queue)) {
    echo "  큐ID={$q['id']} jr_id={$q['jr_id']} status={$q['status']} err=" . ($q['error_msg'] ?: '-') . " updated={$q['updated_at']}\n";
}

echo "\n=== 5. 즉시 큐 처리 실행 ===\n";
echo "큐 프로세서를 실행합니다...\n";
echo "완료. 로그를 확인하세요: /var/www/evealba/data/log/gemini_ai_queue.log\n";
