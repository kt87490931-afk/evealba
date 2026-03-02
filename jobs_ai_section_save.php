<?php
/**
 * AI 소개글 섹션별 인라인 수정 저장 (AJAX)
 * POST: jr_id, section_key, value
 * ai_* 키 → g5_jobs_ai_content 테이블, desc_* 키 → jr_data (사용자 입력)
 */
include_once('./_common.php');
include_once(G5_LIB_PATH . '/jobs_ai_content.lib.php');

header('Content-Type: application/json; charset=utf-8');

$result = array('ok' => 0, 'msg' => '', 'value' => '');

if (!$is_member) {
    $result['msg'] = '로그인 후 이용해 주세요.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$ai_keys = array(
    'ai_intro', 'ai_location', 'ai_env', 'ai_benefit', 'ai_wrapup', 'ai_content',
    'ai_welfare', 'ai_qualify', 'ai_extra', 'ai_mbti_comment',
    'ai_card1_title', 'ai_card1_desc', 'ai_card2_title', 'ai_card2_desc',
    'ai_card3_title', 'ai_card3_desc', 'ai_card4_title', 'ai_card4_desc'
);
$jr_data_keys = array('desc_qualify', 'desc_extra');
$allowed_keys = array_merge($ai_keys, $jr_data_keys);

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
$section_key = isset($_POST['section_key']) ? trim($_POST['section_key']) : '';
$value = isset($_POST['value']) ? $_POST['value'] : '';

if (!$jr_id || !in_array($section_key, $allowed_keys)) {
    $result['msg'] = '잘못된 요청입니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$value = clean_xss_tags($value);
$value = preg_replace('/\r\n|\r/', "\n", $value);

$mb_id_esc = sql_escape_string($member['mb_id']);
$row = sql_fetch("SELECT jr_id FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    $result['msg'] = '권한이 없거나 데이터가 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

if (in_array($section_key, $ai_keys)) {
    $ok = aic_update_field($jr_id, $section_key, $value);
    if (!$ok) {
        $result['msg'] = 'AI 콘텐츠가 없습니다. 먼저 AI 생성이 완료되어야 합니다.';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    $row2 = sql_fetch("SELECT jr_data FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
    $jr_data = $row2['jr_data'] ? json_decode($row2['jr_data'], true) : array();
    if (!is_array($jr_data)) $jr_data = array();
    $jr_data[$section_key] = $value;
    $jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
    sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}' WHERE jr_id = '{$jr_id}'");
}

$result['ok'] = 1;
$result['msg'] = '저장되었습니다.';
$result['value'] = $value;
echo json_encode($result, JSON_UNESCAPED_UNICODE);
