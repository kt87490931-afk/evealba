<?php
/**
 * 채용정보 스크랩 삭제 (선택삭제)
 * POST: js_id[] (스크랩 ID 배열) 또는 GET: js_id (단건)
 */
include_once('./_common.php');

if (!$is_member) {
    alert('로그인 후 이용하실 수 있습니다.');
}

$return_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/').'/jobs_scrap_list.php' : '/jobs_scrap_list.php';
$mb_id_esc = addslashes($member['mb_id']);
$tb = 'g5_jobs_scrap';

$tb_check = sql_query("SHOW TABLES LIKE '{$tb}'", false);
if (!$tb_check || !sql_num_rows($tb_check)) {
    goto_url($return_url);
}

$deleted = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['js_id']) && is_array($_POST['js_id'])) {
    foreach ($_POST['js_id'] as $js_id) {
        $js_id = (int)$js_id;
        if ($js_id > 0) {
            sql_query("DELETE FROM {$tb} WHERE js_id = '{$js_id}' AND mb_id = '{$mb_id_esc}'");
            if (sql_affected_rows() > 0) $deleted++;
        }
    }
} elseif (isset($_GET['js_id'])) {
    $js_id = (int)$_GET['js_id'];
    if ($js_id > 0) {
        sql_query("DELETE FROM {$tb} WHERE js_id = '{$js_id}' AND mb_id = '{$mb_id_esc}'");
        if (sql_affected_rows() > 0) $deleted++;
    }
}

goto_url($return_url.($deleted ? '?deleted='.$deleted : ''));
