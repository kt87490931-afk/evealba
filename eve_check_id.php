<?php
/**
 * 아이디 중복 확인 AJAX 엔드포인트
 */
include_once('./_common.php');
include_once(G5_LIB_PATH.'/register.lib.php');

header('Content-Type: application/json; charset=UTF-8');

$mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';

if (!$mb_id) {
    echo json_encode(array('available' => false, 'msg' => '아이디를 입력해주세요.'));
    exit;
}

if ($msg = empty_mb_id($mb_id)) {
    echo json_encode(array('available' => false, 'msg' => $msg));
    exit;
}

if ($msg = valid_mb_id($mb_id)) {
    echo json_encode(array('available' => false, 'msg' => $msg));
    exit;
}

if ($msg = exist_mb_id($mb_id)) {
    echo json_encode(array('available' => false, 'msg' => '이미 사용중인 아이디입니다.'));
    exit;
}

if ($msg = reserve_mb_id($mb_id)) {
    echo json_encode(array('available' => false, 'msg' => $msg));
    exit;
}

echo json_encode(array('available' => true, 'msg' => '사용 가능한 아이디입니다.'));
