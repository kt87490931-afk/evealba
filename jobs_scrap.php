<?php
/**
 * 채용정보 스크랩 토글 API (일반회원용)
 * POST: jr_id, action (add|remove)
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

$result = array('ok' => 0, 'msg' => '', 'scraped' => 0, 'count' => 0);

if (!$is_member) {
    $result['msg'] = '로그인 후 스크랩할 수 있습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : 'add';
if (!in_array($action, array('add', 'remove'))) $action = 'add';

if (!$jr_id) {
    $result['msg'] = '잘못된 요청입니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$mb_id_esc = addslashes($member['mb_id']);
$tb = 'g5_jobs_scrap';

$tb_check = sql_query("SHOW TABLES LIKE '{$tb}'", false);
if (!$tb_check || !sql_num_rows($tb_check)) {
    sql_query("CREATE TABLE IF NOT EXISTS {$tb} (
        js_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        jr_id INT UNSIGNED NOT NULL,
        mb_id VARCHAR(20) NOT NULL DEFAULT '',
        js_datetime DATETIME NOT NULL,
        UNIQUE KEY uk_jr_mb (jr_id, mb_id),
        KEY idx_mb_id (mb_id),
        KEY idx_jr_id (jr_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='채용정보 스크랩'", false);
}

$exists = sql_fetch("SELECT js_id FROM {$tb} WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}' LIMIT 1");

if ($action === 'add') {
    if ($exists) {
        $result['ok'] = 1;
        $result['msg'] = '이미 스크랩한 채용정보입니다.';
        $result['scraped'] = 1;
    } else {
        $now = date('Y-m-d H:i:s');
        sql_query("INSERT INTO {$tb} (jr_id, mb_id, js_datetime) VALUES ('{$jr_id}', '{$mb_id_esc}', '{$now}')");
        $result['ok'] = 1;
        $result['msg'] = '스크랩했습니다.';
        $result['scraped'] = 1;
    }
} else {
    if ($exists) {
        sql_query("DELETE FROM {$tb} WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
    }
    $result['ok'] = 1;
    $result['msg'] = '스크랩을 해제했습니다.';
    $result['scraped'] = 0;
}

$cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM {$tb} WHERE jr_id = '{$jr_id}'");
$result['count'] = (int)($cnt['cnt'] ?? 0);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
