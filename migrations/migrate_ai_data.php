<?php
/**
 * 기존 jr_data 내 ai_* 데이터를 g5_jobs_ai_content 테이블로 이전
 * 사용법: php migrations/migrate_ai_data.php [--dry-run]
 * 주의: run_migration.php로 010 테이블 생성 후 실행할 것
 */
$dryRun = in_array('--dry-run', $argv ?? []);

define('_GNUBOARD_', true);
if (file_exists(dirname(__DIR__) . '/common.php')) {
    include_once dirname(__DIR__) . '/common.php';
} else {
    die("common.php를 찾을 수 없습니다.\n");
}

$tb = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_content'", false);
if (!$tb || !sql_num_rows($tb)) {
    die("g5_jobs_ai_content 테이블이 없습니다. run_migration.php를 먼저 실행하세요.\n");
}

$ai_keys = array(
    'ai_intro','ai_card1_title','ai_card1_desc','ai_card2_title','ai_card2_desc',
    'ai_card3_title','ai_card3_desc','ai_card4_title','ai_card4_desc',
    'ai_location','ai_env','ai_welfare','ai_qualify','ai_extra','ai_mbti_comment',
    'ai_benefit','ai_wrapup','ai_content'
);

$result = sql_query("SELECT jr_id, mb_id, jr_data FROM g5_jobs_register WHERE jr_data IS NOT NULL AND jr_data != '' AND jr_data != '{}'");
$migrated = 0;
$skipped = 0;
$total = 0;

while ($row = sql_fetch_array($result)) {
    $total++;
    $jr_id = (int)$row['jr_id'];
    $mb_id = $row['mb_id'];
    $jr_data = json_decode($row['jr_data'], true);
    if (!is_array($jr_data)) { $skipped++; continue; }

    $existing = sql_fetch("SELECT id FROM g5_jobs_ai_content WHERE jr_id = '{$jr_id}' LIMIT 1");
    if ($existing) {
        echo "[SKIP] jr_id={$jr_id} — 이미 마이그레이션됨\n";
        $skipped++;
        continue;
    }

    $ai_data = array();
    $has_ai = false;
    foreach ($ai_keys as $k) {
        if (isset($jr_data[$k]) && trim($jr_data[$k]) !== '') {
            $ai_data[$k] = trim($jr_data[$k]);
            $has_ai = true;
        }
    }
    if (!$has_ai) {
        $skipped++;
        continue;
    }

    $ai_tone = isset($jr_data['ai_tone']) ? trim($jr_data['ai_tone']) : 'unnie';
    if (!in_array($ai_tone, array('unnie', 'boss_male', 'pro'))) $ai_tone = 'unnie';

    if ($dryRun) {
        echo "[DRY-RUN] jr_id={$jr_id} mb_id={$mb_id} keys=" . implode(',', array_keys($ai_data)) . "\n";
        $migrated++;
        continue;
    }

    $mb_id_esc = sql_escape_string($mb_id);
    $tone_esc = sql_escape_string($ai_tone);
    $json_esc = sql_escape_string(json_encode($ai_data, JSON_UNESCAPED_UNICODE));
    $now = date('Y-m-d H:i:s');

    sql_query("INSERT INTO g5_jobs_ai_content (jr_id, mb_id, version, ai_tone, ai_data, is_active, created_at, duration_ms)
               VALUES ('{$jr_id}', '{$mb_id_esc}', 1, '{$tone_esc}', '{$json_esc}', 1, '{$now}', 0)");

    echo "[OK] jr_id={$jr_id} mb_id={$mb_id} keys=" . count($ai_data) . "\n";
    $migrated++;
}

echo "\n완료. 전체: {$total} / 마이그레이션: {$migrated} / 스킵: {$skipped}\n";
if ($dryRun) echo "(--dry-run 모드: 실제 저장 안 됨)\n";
echo "※ jr_data의 ai_* 키는 안전을 위해 삭제하지 않았습니다.\n";
