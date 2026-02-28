<?php
/**
 * 어드민 - 기업회원 승인/반려 처리 (AJAX)
 * POST: action (approve/reject/rescan), mb_id, reason, token
 */
$sub_menu = '910300';
require_once './_common.php';

header('Content-Type: application/json; charset=UTF-8');

$res = array('ok' => 0, 'msg' => '');

auth_check_menu($auth, $sub_menu, 'w');

$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

if (!$mb_id) {
    $res['msg'] = '회원 아이디가 없습니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$mb_id_esc = sql_escape_string($mb_id);
$mb = sql_fetch("SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$mb_id_esc}' AND mb_1 = 'biz'");

if (!$mb) {
    $res['msg'] = '기업회원 정보를 찾을 수 없습니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

if ($action === 'approve') {
    $normal_level = $config['cf_register_level'] ?: 2;
    sql_query("UPDATE {$g5['member_table']} SET
        mb_7 = 'approved',
        mb_level = '{$normal_level}',
        mb_memo = CONCAT(mb_memo, '\n[".G5_TIME_YMDHIS."] 기업회원 승인 by {$member['mb_id']}')
        WHERE mb_id = '{$mb_id_esc}'
    ");

    @insert_point($mb_id, $config['cf_register_point'], '기업회원 가입 승인', '@member', $mb_id, '기업회원승인');

    $res['ok'] = 1;
    $res['msg'] = $mb_id . ' 기업회원이 승인되었습니다.';

} elseif ($action === 'reject') {
    $reason_esc = sql_escape_string($reason);
    sql_query("UPDATE {$g5['member_table']} SET
        mb_7 = 'rejected',
        mb_memo = CONCAT(mb_memo, '\n[".G5_TIME_YMDHIS."] 기업회원 반려 by {$member['mb_id']} 사유: {$reason_esc}')
        WHERE mb_id = '{$mb_id_esc}'
    ");

    $res['ok'] = 1;
    $res['msg'] = $mb_id . ' 기업회원이 반려되었습니다.';

} elseif ($action === 'rescan') {
    if (empty($mb['mb_6'])) {
        $res['msg'] = '첨부문서가 없습니다.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $doc_path = G5_PATH . '/' . $mb['mb_6'];
    if (!file_exists($doc_path)) {
        $res['msg'] = '문서 파일을 찾을 수 없습니다.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    @include_once(G5_EXTEND_PATH . '/gemini_config.php');
    if (empty($gemini_api_key)) {
        $res['msg'] = 'Gemini API 키가 설정되지 않았습니다.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $ext = strtolower(pathinfo($doc_path, PATHINFO_EXTENSION));
    $mime_map = array('jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp');
    $mime_type = isset($mime_map[$ext]) ? $mime_map[$ext] : 'image/jpeg';

    $image_data = file_get_contents($doc_path);
    $base64_image = base64_encode($image_data);

    $prompt = "이 이미지는 한국의 사업자등록증, 직업소개사업등록증, 또는 영업허가증입니다.\n"
        . "다음 4가지 정보를 정확히 추출하여 JSON 형식으로만 응답해주세요.\n"
        . "{\"biz_num\": \"사업자등록번호(숫자만)\", \"biz_name\": \"상호\", \"biz_rep\": \"대표자\", \"biz_addr\": \"주소\"}";

    $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $gemini_model . ':generateContent?key=' . $gemini_api_key;
    $request_body = array(
        'contents' => array(
            array('parts' => array(
                array('text' => $prompt),
                array('inline_data' => array('mime_type' => $mime_type, 'data' => $base64_image))
            ))
        ),
        'generationConfig' => array('temperature' => 0.1, 'topP' => 0.8, 'maxOutputTokens' => 1024)
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
    curl_close($ch);

    if ($http_code !== 200 || !$response) {
        $res['msg'] = 'Gemini API 호출 실패 (HTTP ' . $http_code . ')';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $data = json_decode($response, true);
    $ai_text = isset($data['candidates'][0]['content']['parts'][0]['text']) ? $data['candidates'][0]['content']['parts'][0]['text'] : '';

    $json_text = $ai_text;
    if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $ai_text, $m)) {
        $json_text = trim($m[1]);
    }

    $ocr_result = json_decode(trim($json_text), true);
    if (!$ocr_result) {
        $res['msg'] = 'AI 응답 파싱 실패';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $existing_data = $mb['mb_8'] ? json_decode($mb['mb_8'], true) : array();
    if (!is_array($existing_data)) $existing_data = array();

    $existing_data['ocr_result'] = array(
        'biz_num' => isset($ocr_result['biz_num']) ? preg_replace('/[^0-9]/', '', $ocr_result['biz_num']) : '',
        'biz_name' => isset($ocr_result['biz_name']) ? trim($ocr_result['biz_name']) : '',
        'biz_rep' => isset($ocr_result['biz_rep']) ? trim($ocr_result['biz_rep']) : '',
        'biz_addr' => isset($ocr_result['biz_addr']) ? trim($ocr_result['biz_addr']) : ''
    );
    $existing_data['ocr_scanned_at'] = date('Y-m-d H:i:s');

    $data_esc = sql_escape_string(json_encode($existing_data, JSON_UNESCAPED_UNICODE));
    sql_query("UPDATE {$g5['member_table']} SET mb_8 = '{$data_esc}' WHERE mb_id = '{$mb_id_esc}'");

    $res['ok'] = 1;
    $res['msg'] = 'AI 재스캔 완료';
    $res['ocr_result'] = $existing_data['ocr_result'];

} else {
    $res['msg'] = '잘못된 액션입니다.';
}

echo json_encode($res, JSON_UNESCAPED_UNICODE);
