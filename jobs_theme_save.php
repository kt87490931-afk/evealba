<?php
include_once('./_common.php');
if (!defined('_GNUBOARD_')) exit;

header('Content-Type: application/json; charset=UTF-8');

if (!$is_member) {
    echo json_encode(array('success' => false, 'msg' => '로그인이 필요합니다.'));
    exit;
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
$theme = isset($_POST['theme']) ? trim($_POST['theme']) : 'pink';

if (!$jr_id || !in_array($theme, array('pink', 'black', 'blue'))) {
    echo json_encode(array('success' => false, 'msg' => '잘못된 요청입니다.'));
    exit;
}

$mb_id_esc = sql_escape_string($member['mb_id']);
$row = sql_fetch("SELECT jr_id, mb_id, jr_data FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");

if (!$row) {
    echo json_encode(array('success' => false, 'msg' => '권한이 없습니다.'));
    exit;
}

$data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
if (!is_array($data)) $data = array();

$data['theme'] = $theme;
$data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
$data_esc = sql_escape_string($data_json);

sql_query("UPDATE g5_jobs_register SET jr_data = '{$data_esc}' WHERE jr_id = '{$jr_id}'");

echo json_encode(array('success' => true, 'msg' => '테마가 저장되었습니다.', 'theme' => $theme));
