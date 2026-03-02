<?php
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

$ids = [2, 3, 4];
foreach ($ids as $jid) {
    echo str_repeat('=', 60) . "\n";
    echo "jr_id={$jid} - 활성 AI 콘텐츠\n";
    echo str_repeat('=', 60) . "\n";
    
    $row = sql_fetch("SELECT id, jr_id, version, is_active, ai_data, created_at FROM g5_jobs_ai_content WHERE jr_id = {$jid} AND is_active = 1 ORDER BY version DESC LIMIT 1");
    if (!$row || empty($row['id'])) {
        echo "  [없음]\n\n";
        continue;
    }
    
    echo "  ID={$row['id']} v{$row['version']} active={$row['is_active']} created={$row['created_at']}\n\n";
    
    $data = json_decode($row['ai_data'], true);
    if (!is_array($data)) {
        echo "  [ai_data JSON 파싱 실패]\n\n";
        continue;
    }
    
    $keys = ['ai_intro', 'ai_card1_title', 'ai_card1_desc', 'ai_card2_title', 'ai_card3_title', 'ai_card4_title', 'ai_location', 'ai_env', 'ai_welfare', 'ai_qualify', 'ai_extra', 'ai_mbti_comment'];
    foreach ($keys as $k) {
        $val = isset($data[$k]) ? $data[$k] : '[없음]';
        $preview = mb_substr($val, 0, 120);
        echo "  {$k}: {$preview}\n";
    }
    echo "\n";
}
