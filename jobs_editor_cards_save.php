<?php
/**
 * 포인트 카드 4개 일괄 저장 (AJAX)
 * POST: jr_id, ai_card1_title, ai_card1_desc ~ ai_card4_title, ai_card4_desc
 * → g5_jobs_ai_content 테이블의 활성 레코드 업데이트
 */
include_once('./_common.php');
include_once(G5_LIB_PATH . '/jobs_ai_content.lib.php');

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
$row = sql_fetch("SELECT jr_id FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    $result['msg'] = '권한이 없거나 데이터가 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$updates = array(
    'ai_card1_title', 'ai_card1_desc', 'ai_card2_title', 'ai_card2_desc',
    'ai_card3_title', 'ai_card3_desc', 'ai_card4_title', 'ai_card4_desc'
);

$fields = array();
foreach ($updates as $k) {
    if (isset($_POST[$k])) {
        $v = clean_xss_tags(trim((string)$_POST[$k]));
        $v = preg_replace('/\r\n|\r/', "\n", $v);
        $fields[$k] = $v;
    }
}

if (empty($fields)) {
    $result['msg'] = '저장할 데이터가 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$ok = aic_update_fields($jr_id, $fields);
if (!$ok) {
    $result['msg'] = 'AI 콘텐츠가 없습니다. 먼저 AI 생성이 완료되어야 합니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$result['ok'] = 1;
$result['msg'] = '저장되었습니다.';
echo json_encode($result, JSON_UNESCAPED_UNICODE);
