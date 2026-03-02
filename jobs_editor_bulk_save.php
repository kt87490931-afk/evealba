<?php
/**
 * 편의사항/키워드/MBTI 일괄 저장 (AJAX)
 * POST: jr_id, amenity[] (배열), keyword[] (배열), mbti_prefer[] (배열)
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

$result = array('ok' => 0, 'msg' => '');

if (!$is_member) {
    $result['msg'] = '로그인 후 이용해 주세요.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
if (!$jr_id) {
    $result['msg'] = '잘못된 요청입니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$mb_id_esc = sql_escape_string($member['mb_id']);
$row = sql_fetch("SELECT jr_id, jr_data FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    $result['msg'] = '권한이 없거나 데이터가 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
if (!is_array($jr_data)) {
    $jr_data = array();
}

if (isset($_POST['amenity']) && is_array($_POST['amenity'])) {
    $arr = array_map(function ($v) {
        return clean_xss_tags(trim((string)$v));
    }, $_POST['amenity']);
    $jr_data['amenity'] = array_values(array_filter($arr, function ($v) { return $v !== ''; }));
}

if (isset($_POST['keyword']) && is_array($_POST['keyword'])) {
    $arr = array_map(function ($v) {
        return clean_xss_tags(trim((string)$v));
    }, $_POST['keyword']);
    $jr_data['keyword'] = array_values(array_filter($arr, function ($v) { return $v !== ''; }));
}

if (isset($_POST['mbti_prefer']) && is_array($_POST['mbti_prefer'])) {
    $arr = array_map(function ($v) {
        return clean_xss_tags(trim((string)$v));
    }, $_POST['mbti_prefer']);
    $jr_data['mbti_prefer'] = array_values(array_filter($arr, function ($v) { return $v !== ''; }));
}

$jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}' WHERE jr_id = '{$jr_id}'");

$result['ok'] = 1;
$result['msg'] = '저장되었습니다.';
echo json_encode($result, JSON_UNESCAPED_UNICODE);
