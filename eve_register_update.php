<?php
/**
 * 이브알바 회원가입 처리 (AJAX)
 * register_main.php 에서 FormData로 POST
 * mb_1=회원유형, mb_2=사업자번호, mb_3=상호, mb_4=대표자, mb_5=주소
 * mb_6=확인문서경로, mb_7=승인상태, mb_8=OCR데이터, mb_9=업종
 */
include_once('./_common.php');
include_once(G5_LIB_PATH.'/register.lib.php');

header('Content-Type: application/json; charset=UTF-8');

$res = array('ok' => 0, 'msg' => '');

$mb_id          = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
$mb_password    = isset($_POST['mb_password']) ? trim($_POST['mb_password']) : '';
$mb_password_re = isset($_POST['mb_password_re']) ? trim($_POST['mb_password_re']) : '';
$mb_name        = isset($_POST['mb_name']) ? trim($_POST['mb_name']) : '';
$mb_nick        = isset($_POST['mb_nick']) ? trim($_POST['mb_nick']) : '';
$mb_email       = isset($_POST['mb_email']) ? trim($_POST['mb_email']) : '';
$mb_birth       = isset($_POST['mb_birth']) ? trim($_POST['mb_birth']) : '';
$mb_sex         = isset($_POST['mb_sex']) ? trim($_POST['mb_sex']) : '';
$mb_hp          = isset($_POST['mb_hp']) ? trim($_POST['mb_hp']) : '';
$mb_sms         = isset($_POST['mb_sms']) ? trim($_POST['mb_sms']) : '0';
$mb_1           = isset($_POST['mb_1']) ? trim($_POST['mb_1']) : 'personal';
$mb_2           = isset($_POST['mb_2']) ? preg_replace('/[^0-9]/', '', trim($_POST['mb_2'])) : '';
$mb_3           = isset($_POST['mb_3']) ? trim($_POST['mb_3']) : '';
$mb_4           = isset($_POST['mb_4']) ? trim($_POST['mb_4']) : '';
$mb_5           = isset($_POST['mb_5']) ? trim($_POST['mb_5']) : '';
$mb_9           = isset($_POST['mb_9']) ? trim($_POST['mb_9']) : '';

$mb_name  = clean_xss_tags($mb_name, 1, 1);
$mb_nick  = clean_xss_tags($mb_nick, 1, 1);
$mb_hp    = clean_xss_tags($mb_hp, 1, 1);
$mb_3     = clean_xss_tags($mb_3, 1, 1);
$mb_4     = clean_xss_tags($mb_4, 1, 1);
$mb_5     = clean_xss_tags($mb_5, 1, 1);
$mb_9     = clean_xss_tags($mb_9, 1, 1);

if (!$mb_id || strlen($mb_id) < 4) {
    $res['msg'] = '아이디를 4자 이상 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if ($msg = valid_mb_id($mb_id)) {
    $res['msg'] = $msg;
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if ($msg = exist_mb_id($mb_id)) {
    $res['msg'] = '이미 사용중인 아이디입니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_password || strlen($mb_password) < 4) {
    $res['msg'] = '비밀번호를 4자 이상 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (strlen($mb_password) > 12) {
    $res['msg'] = '비밀번호는 12자 이하로 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!preg_match('/[a-zA-Z]/', $mb_password)) {
    $res['msg'] = '비밀번호에 영문자를 포함해야 합니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?`~]/', $mb_password)) {
    $res['msg'] = '비밀번호에 특수문자를 포함해야 합니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if ($mb_password !== $mb_password_re) {
    $res['msg'] = '비밀번호가 일치하지 않습니다.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_name) {
    $res['msg'] = '이름을 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_nick) {
    $res['msg'] = '닉네임을 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if ($msg = valid_mb_nick($mb_nick)) {
    $res['msg'] = $msg;
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if ($msg = exist_mb_nick($mb_nick, '')) {
    $res['msg'] = $msg;
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_email || !filter_var($mb_email, FILTER_VALIDATE_EMAIL)) {
    $res['msg'] = '올바른 이메일을 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if ($msg = exist_mb_email($mb_email, '')) {
    $res['msg'] = $msg;
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_birth) {
    $res['msg'] = '생년월일을 선택해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_hp) {
    $res['msg'] = '핸드폰 번호를 입력해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}
if (!$mb_9) {
    $res['msg'] = '업종을 선택해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

$mb_6 = '';
$mb_7 = '';
$mb_8 = '';

if ($mb_1 === 'biz') {
    if (!$mb_2 || strlen($mb_2) !== 10) {
        $res['msg'] = '사업자번호 10자리를 입력해주세요.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }
    if (!$mb_3) {
        $res['msg'] = '상호를 입력해주세요.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }
    if (!$mb_4) {
        $res['msg'] = '대표자를 입력해주세요.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }
    if (!$mb_5) {
        $res['msg'] = '주소를 입력해주세요.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    if (!isset($_FILES['biz_doc']) || $_FILES['biz_doc']['error'] !== UPLOAD_ERR_OK) {
        $res['msg'] = '확인문서를 첨부해주세요.';
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
        $res['msg'] = '허용되지 않는 파일 형식입니다. (jpg, png, gif, webp)';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $upload_dir = G5_DATA_PATH . '/member_biz_doc';
    if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);

    $filename = $mb_id . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $dest = $upload_dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $res['msg'] = '파일 업로드에 실패했습니다.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $mb_6 = 'data/member_biz_doc/' . $filename;
    $mb_7 = 'pending';

    $ocr_data = array(
        'user_input' => array(
            'biz_num' => $mb_2,
            'biz_name' => $mb_3,
            'biz_rep' => $mb_4,
            'biz_addr' => $mb_5
        ),
        'doc_file' => $mb_6,
        'submitted_at' => date('Y-m-d H:i:s')
    );
    $mb_8 = json_encode($ocr_data, JSON_UNESCAPED_UNICODE);
}

$mb_level = $config['cf_register_level'];
if ($mb_1 === 'biz') {
    $mb_level = 1;
}

$enc_pw = get_encrypt_string($mb_password);
$mb_id_esc    = sql_escape_string($mb_id);
$mb_name_esc  = sql_escape_string($mb_name);
$mb_nick_esc  = sql_escape_string($mb_nick);
$mb_email_esc = sql_escape_string($mb_email);
$mb_birth_esc = sql_escape_string($mb_birth);
$mb_sex_esc   = sql_escape_string($mb_sex);
$mb_hp_esc    = sql_escape_string($mb_hp);
$mb_1_esc     = sql_escape_string($mb_1);
$mb_2_esc     = sql_escape_string($mb_2);
$mb_3_esc     = sql_escape_string($mb_3);
$mb_4_esc     = sql_escape_string($mb_4);
$mb_5_esc     = sql_escape_string($mb_5);
$mb_6_esc     = sql_escape_string($mb_6);
$mb_7_esc     = sql_escape_string($mb_7);
$mb_8_esc     = sql_escape_string($mb_8);
$mb_9_esc     = sql_escape_string($mb_9);

$sql = "INSERT INTO {$g5['member_table']} SET
    mb_id = '{$mb_id_esc}',
    mb_password = '{$enc_pw}',
    mb_name = '{$mb_name_esc}',
    mb_nick = '{$mb_nick_esc}',
    mb_nick_date = '".G5_TIME_YMD."',
    mb_email = '{$mb_email_esc}',
    mb_birth = '{$mb_birth_esc}',
    mb_sex = '{$mb_sex_esc}',
    mb_hp = '{$mb_hp_esc}',
    mb_level = '{$mb_level}',
    mb_mailling = '0',
    mb_sms = '{$mb_sms}',
    mb_open = '0',
    mb_today_login = '".G5_TIME_YMDHIS."',
    mb_datetime = '".G5_TIME_YMDHIS."',
    mb_ip = '{$_SERVER['REMOTE_ADDR']}',
    mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
    mb_email_certify = '".G5_TIME_YMDHIS."',
    mb_1 = '{$mb_1_esc}',
    mb_2 = '{$mb_2_esc}',
    mb_3 = '{$mb_3_esc}',
    mb_4 = '{$mb_4_esc}',
    mb_5 = '{$mb_5_esc}',
    mb_6 = '{$mb_6_esc}',
    mb_7 = '{$mb_7_esc}',
    mb_8 = '{$mb_8_esc}',
    mb_9 = '{$mb_9_esc}',
    mb_10 = ''
";

$result = sql_query($sql, false);

if (!$result) {
    $res['msg'] = '회원가입 처리중 오류가 발생했습니다. 다시 시도해주세요.';
    echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
}

if ($mb_1 !== 'biz') {
    @insert_point($mb_id, $config['cf_register_point'], '회원가입 축하', '@member', $mb_id, '회원가입');
}

$res['ok'] = 1;
$res['msg'] = $mb_1 === 'biz'
    ? '기업회원 가입 신청이 완료되었습니다. 관리자 승인 후 로그인이 가능합니다.'
    : '회원가입이 완료되었습니다.';
echo json_encode($res, JSON_UNESCAPED_UNICODE);
