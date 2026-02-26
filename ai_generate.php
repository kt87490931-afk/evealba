<?php
/**
 * AI 소개글 생성 AJAX 엔드포인트 (Gemini API)
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

if (!$is_member) {
    echo json_encode(['success' => false, 'message' => '로그인 후 이용해 주세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);

if (!$inputData || !is_array($inputData)) {
    echo json_encode(['success' => false, 'message' => '입력 데이터가 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

include_once(G5_LIB_PATH.'/gemini_api.lib.php');

$formData = [
    'nickname'   => isset($inputData['nickname'])   ? trim($inputData['nickname'])   : '',
    'title'      => isset($inputData['title'])      ? trim($inputData['title'])      : '',
    'location'   => isset($inputData['location'])   ? trim($inputData['location'])   : '',
    'environment'=> isset($inputData['environment']) ? trim($inputData['environment']): '',
    'benefits'   => isset($inputData['benefits'])   ? trim($inputData['benefits'])   : '',
    'details'    => isset($inputData['details'])    ? trim($inputData['details'])    : '',
    'contact'    => isset($inputData['contact'])    ? trim($inputData['contact'])    : '',
    'sns'        => isset($inputData['sns'])        ? trim($inputData['sns'])        : '',
    'salary'     => isset($inputData['salary'])     ? trim($inputData['salary'])     : '',
    'region'     => isset($inputData['region'])     ? trim($inputData['region'])     : '',
    'jobtype'    => isset($inputData['jobtype'])    ? trim($inputData['jobtype'])    : '',
];

$role_id = isset($inputData['ai_tone']) ? $inputData['ai_tone'] : 'unnie';
if (!in_array($role_id, ['unnie', 'boss_male', 'pro'])) {
    $role_id = 'unnie';
}

$generatedText = generate_store_description_gemini($formData, $role_id);

if (strpos($generatedText, '오류') !== false || strpos($generatedText, '설정') !== false || strpos($generatedText, '대기열') !== false || strpos($generatedText, '큐 락') !== false) {
    echo json_encode(['success' => false, 'message' => $generatedText], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['success' => true, 'text' => $generatedText], JSON_UNESCAPED_UNICODE);
