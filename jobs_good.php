<?php
/**
 * 채용공고 추천(좋아요) AJAX 처리
 * - 1개 광고당, 1 아이디당, 하루 10번
 * - 본인 글도 추천 가능
 */
@error_reporting(0);
@ini_set('display_errors', '0');
ob_start();

include_once('./_common.php');

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

function jg_json($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;

if (!$is_member) {
    jg_json(array('ok' => 0, 'msg' => '회원만 추천할 수 있습니다.'));
}
if (!$jr_id) {
    jg_json(array('ok' => 0, 'msg' => '잘못된 요청입니다.'));
}

$mb_id_esc = addslashes($member['mb_id']);

$jr = sql_fetch("SELECT jr_id, jr_good FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND jr_status = 'ongoing'");
if (!$jr) {
    jg_json(array('ok' => 0, 'msg' => '존재하지 않거나 노출 중이 아닌 광고입니다.'));
}

$good_table = 'g5_jobs_good';
$tb_check = @sql_query("SHOW TABLES LIKE '{$good_table}'", false);
if (!$tb_check || !@sql_num_rows($tb_check)) {
    @sql_query("CREATE TABLE IF NOT EXISTS {$good_table} (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        jr_id INT UNSIGNED NOT NULL,
        mb_id VARCHAR(20) NOT NULL,
        jg_date DATE NOT NULL,
        jg_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
        UNIQUE KEY uk_jr_mb_date (jr_id, mb_id, jg_date),
        KEY idx_jr_id (jr_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
}

$col_check = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_good'", false);
if (!$col_check || !@sql_num_rows($col_check)) {
    @sql_query("ALTER TABLE g5_jobs_register ADD COLUMN jr_good INT UNSIGNED NOT NULL DEFAULT 0", false);
    $jr['jr_good'] = 0;
}

$today = date('Y-m-d');
$existing = sql_fetch("SELECT jg_count FROM {$good_table} WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}' AND jg_date = '{$today}'");

$daily_limit = 10;
$used = $existing ? (int)$existing['jg_count'] : 0;

if ($used >= $daily_limit) {
    jg_json(array(
        'ok' => 0,
        'msg' => '오늘 추천 횟수를 모두 사용했습니다 ('.$daily_limit.'/'.$daily_limit.')',
        'total' => (int)$jr['jr_good'],
        'used' => $used,
        'limit' => $daily_limit
    ));
}

if ($existing) {
    sql_query("UPDATE {$good_table} SET jg_count = jg_count + 1 WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}' AND jg_date = '{$today}'");
} else {
    sql_query("INSERT INTO {$good_table} (jr_id, mb_id, jg_date, jg_count) VALUES ('{$jr_id}', '{$mb_id_esc}', '{$today}', 1)");
}

sql_query("UPDATE g5_jobs_register SET jr_good = jr_good + 1 WHERE jr_id = '{$jr_id}'");

$updated = sql_fetch("SELECT jr_good FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
$new_total = $updated ? (int)$updated['jr_good'] : (int)$jr['jr_good'] + 1;

jg_json(array(
    'ok' => 1,
    'msg' => '추천되었습니다!',
    'total' => $new_total,
    'used' => $used + 1,
    'limit' => $daily_limit
));
