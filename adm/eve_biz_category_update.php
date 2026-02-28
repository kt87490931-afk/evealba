<?php
/**
 * 어드민 - 허용 업태/종목 CRUD (AJAX)
 */
$sub_menu = '910400';
require_once './_common.php';

header('Content-Type: application/json; charset=UTF-8');

$res = array('ok' => 0, 'msg' => '');

auth_check_menu($auth, $sub_menu, 'w');

$tb = 'g5_eve_biz_category';
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($action === 'add') {
    $cat_type = isset($_POST['cat_type']) ? trim($_POST['cat_type']) : '';
    $cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';

    if (!in_array($cat_type, array('type', 'item'))) {
        $res['msg'] = '잘못된 카테고리 유형';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }
    if (!$cat_name) {
        $res['msg'] = '이름을 입력해주세요.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $name_esc = sql_escape_string($cat_name);
    $exists = sql_fetch("SELECT cat_id FROM `{$tb}` WHERE cat_type='{$cat_type}' AND cat_name='{$name_esc}'");
    if ($exists) {
        $res['msg'] = '이미 등록된 항목입니다.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    sql_query("INSERT INTO `{$tb}` SET cat_type='{$cat_type}', cat_name='{$name_esc}', cat_enabled=1, cat_datetime='".G5_TIME_YMDHIS."'");
    $res['ok'] = 1;
    $res['msg'] = '추가 완료';
    $res['cat_id'] = sql_insert_id();

} elseif ($action === 'toggle') {
    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
    if (!$cat_id) {
        $res['msg'] = '잘못된 요청';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $row = sql_fetch("SELECT cat_id, cat_enabled FROM `{$tb}` WHERE cat_id={$cat_id}");
    if (!$row) {
        $res['msg'] = '항목을 찾을 수 없습니다.';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    $new_val = $row['cat_enabled'] ? 0 : 1;
    sql_query("UPDATE `{$tb}` SET cat_enabled={$new_val} WHERE cat_id={$cat_id}");
    $res['ok'] = 1;
    $res['enabled'] = $new_val;
    $res['msg'] = $new_val ? '활성화됨' : '비활성화됨';

} elseif ($action === 'delete') {
    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
    if (!$cat_id) {
        $res['msg'] = '잘못된 요청';
        echo json_encode($res, JSON_UNESCAPED_UNICODE); exit;
    }

    sql_query("DELETE FROM `{$tb}` WHERE cat_id={$cat_id}");
    $res['ok'] = 1;
    $res['msg'] = '삭제 완료';

} else {
    $res['msg'] = '잘못된 액션';
}

echo json_encode($res, JSON_UNESCAPED_UNICODE);
