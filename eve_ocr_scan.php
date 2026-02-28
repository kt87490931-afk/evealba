<?php
/**
 * Gemini Vision OCR - 사업자등록증 자동 인식 AJAX 엔드포인트
 * POST: biz_doc (file) → Gemini Vision API → JSON
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=UTF-8');

$res = array('ok' => 0, 'msg' => '');

if (!isset($_FILES['biz_doc']) || $_FILES['biz_doc']['error'] !== UPLOAD_ERR_OK) {
    $res['msg'] = '문서 파일이 전송되지 않았습니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$file = $_FILES['biz_doc'];
$max_size = 10 * 1024 * 1024;
if ($file['size'] > $max_size) {
    $res['msg'] = '파일 크기가 10MB를 초과합니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
if (!in_array($ext, $allowed_ext)) {
    $res['msg'] = '허용되지 않는 파일 형식입니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$mime_map = array('jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp');
$mime_type = isset($mime_map[$ext]) ? $mime_map[$ext] : 'image/jpeg';

$image_data = file_get_contents($file['tmp_name']);
if (!$image_data) {
    $res['msg'] = '파일을 읽을 수 없습니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
$base64_image = base64_encode($image_data);

@include_once(G5_EXTEND_PATH . '/gemini_config.php');
if (empty($gemini_api_key)) {
    $res['msg'] = 'AI 서비스 설정 오류 (API 키 없음)';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$prompt = <<<PROMPT
이 이미지는 한국의 사업자등록증, 직업소개사업등록증, 또는 영업허가증입니다.
다음 4가지 정보를 정확히 추출하여 JSON 형식으로만 응답해주세요.
다른 설명 없이 JSON만 반환하세요.

{
  "biz_num": "사업자등록번호 (숫자만, 하이픈 제거)",
  "biz_name": "상호 (사업장 이름)",
  "biz_rep": "대표자 성명",
  "biz_addr": "사업장 소재지 (전체 주소)"
}

주의사항:
- 사업자등록번호는 하이픈(-) 없이 숫자 10자리만 추출
- 상호는 정확한 상호명만 추출 (예: "(주)이브알바" 처럼)
- 대표자는 성명만 추출
- 주소는 문서에 기재된 전체 주소를 추출
- 정보를 찾을 수 없는 경우 해당 필드를 빈 문자열("")로 설정
PROMPT;

$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $gemini_model . ':generateContent?key=' . $gemini_api_key;

$request_body = array(
    'contents' => array(
        array(
            'parts' => array(
                array('text' => $prompt),
                array('inline_data' => array(
                    'mime_type' => $mime_type,
                    'data' => $base64_image
                ))
            )
        )
    ),
    'generationConfig' => array(
        'temperature' => 0.1,
        'topP' => 0.8,
        'maxOutputTokens' => 1024
    )
);

$ch = curl_init($api_url);
curl_setopt_array($ch, array(
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($request_body),
    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    $res['msg'] = 'AI 서비스 연결 실패: ' . $curl_error;
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

if ($http_code !== 200) {
    $res['msg'] = 'AI 서비스 오류 (HTTP ' . $http_code . ')';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$data = json_decode($response, true);
if (!$data || !isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    $res['msg'] = 'AI 응답을 처리할 수 없습니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$ai_text = $data['candidates'][0]['content']['parts'][0]['text'];

$json_text = $ai_text;
if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $ai_text, $m)) {
    $json_text = trim($m[1]);
} else {
    $json_text = trim($ai_text);
}

$ocr_result = json_decode($json_text, true);
if (!$ocr_result || !is_array($ocr_result)) {
    $res['msg'] = 'AI 인식 결과를 파싱할 수 없습니다. 직접 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$biz_num = isset($ocr_result['biz_num']) ? preg_replace('/[^0-9]/', '', $ocr_result['biz_num']) : '';
$biz_name = isset($ocr_result['biz_name']) ? trim($ocr_result['biz_name']) : '';
$biz_rep = isset($ocr_result['biz_rep']) ? trim($ocr_result['biz_rep']) : '';
$biz_addr = isset($ocr_result['biz_addr']) ? trim($ocr_result['biz_addr']) : '';

$res['ok'] = 1;
$res['msg'] = 'AI 자동인식 완료';
$res['biz_num'] = $biz_num;
$res['biz_name'] = $biz_name;
$res['biz_rep'] = $biz_rep;
$res['biz_addr'] = $biz_addr;
echo json_encode($res, JSON_UNESCAPED_UNICODE);
