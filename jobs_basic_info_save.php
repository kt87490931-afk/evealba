<?php
/**
 * 채용정보 기본정보 인라인 수정 저장 (AJAX)
 * POST: jr_id, job_nickname, job_company, job_contact, job_kakao, job_line, job_telegram,
 *       job_salary_type, job_salary_amt, job_work_region_1, job_work_region_detail_1, job_job1, job_job2
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

$result = array('ok' => 0, 'msg' => '');

if (isset($_GET['debug'])) {
    $raw = file_get_contents('php://input');
    echo json_encode(array(
        'post_keys' => array_keys($_POST),
        'post_data' => $_POST,
        'raw_length' => strlen($raw),
        'raw_preview' => substr($raw, 0, 500),
        'content_type' => isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'none',
        'request_method' => $_SERVER['REQUEST_METHOD']
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

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

$allowed_keys = array(
    'job_nickname', 'job_company', 'job_contact', 'job_kakao', 'job_line', 'job_telegram',
    'job_salary_type', 'job_salary_amt',
    'job_work_region_1', 'job_work_region_detail_1',
    'job_work_region_2', 'job_work_region_detail_2',
    'job_work_region_3', 'job_work_region_detail_3',
    'job_job1', 'job_job2', 'job_title',
    'thumb_gradient', 'thumb_title', 'thumb_text',
    'thumb_icon', 'thumb_motion', 'thumb_wave',
    'thumb_text_color',
    'thumb_border'
);

$updates = array();
foreach ($allowed_keys as $k) {
    if (isset($_POST[$k])) {
        $updates[$k] = clean_xss_tags(trim((string)$_POST[$k]));
    }
}
if (empty($updates)) {
    $result['msg'] = '수정할 항목이 없습니다.';
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
foreach ($updates as $k => $v) {
    $jr_data[$k] = $v;
}
$jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}' WHERE jr_id = '{$jr_id}'");

$result['ok'] = 1;
$result['msg'] = '저장되었습니다.';
echo json_encode($result, JSON_UNESCAPED_UNICODE);
