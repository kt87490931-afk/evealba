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
    echo "=== jr_id={$jid} ===\n";
    $row = sql_fetch("SELECT ai_data FROM g5_jobs_ai_content WHERE jr_id = {$jid} AND is_active = 1 ORDER BY version DESC LIMIT 1");
    if (!$row || empty($row['ai_data'])) {
        echo "  [없음]\n\n";
        continue;
    }
    
    $data = json_decode($row['ai_data'], true);
    if (!is_array($data)) {
        echo "  [파싱 실패] raw: " . mb_substr($row['ai_data'], 0, 200) . "\n\n";
        continue;
    }
    
    echo "  키 목록: " . implode(', ', array_keys($data)) . "\n";
    
    $intro_key = null;
    foreach (['ai_intro', 'intro'] as $k) {
        if (isset($data[$k])) { $intro_key = $k; break; }
    }
    if ($intro_key) {
        echo "  {$intro_key}: " . mb_substr($data[$intro_key], 0, 200) . "\n";
    }
    
    foreach (['ai_card1_title', 'card1_title'] as $k) {
        if (isset($data[$k])) echo "  {$k}: {$data[$k]}\n";
    }
    foreach (['ai_card2_title', 'card2_title'] as $k) {
        if (isset($data[$k])) echo "  {$k}: {$data[$k]}\n";
    }
    foreach (['ai_card3_title', 'card3_title'] as $k) {
        if (isset($data[$k])) echo "  {$k}: {$data[$k]}\n";
    }
    foreach (['ai_card4_title', 'card4_title'] as $k) {
        if (isset($data[$k])) echo "  {$k}: {$data[$k]}\n";
    }
    
    echo "\n";
}
