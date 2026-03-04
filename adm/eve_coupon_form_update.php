<?php
/**
 * 어드민 - 쿠폰 생성/수정 처리
 * ec_code 자동생성 (EV+ec_id)
 */
$sub_menu = '910940';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert('잘못된 요청입니다.', './eve_coupon_list.php');
}

check_admin_token();

$w = isset($_POST['w']) ? preg_replace('/[^a-z]/', '', $_POST['w']) : '';
$ec_id = isset($_POST['ec_id']) ? (int)$_POST['ec_id'] : 0;

$ec_name = isset($_POST['ec_name']) ? trim(strip_tags($_POST['ec_name'])) : '';
$ec_target = isset($_POST['ec_target']) && in_array($_POST['ec_target'], array('biz','personal')) ? $_POST['ec_target'] : 'biz';
$ec_type = isset($_POST['ec_type']) ? preg_replace('/[^a-z_]/', '', $_POST['ec_type']) : 'ad';
if (!in_array($ec_type, array('thumb','ad','line_ad_free','gift'))) $ec_type = 'ad';

$ec_discount_type = isset($_POST['ec_discount_type']) && $_POST['ec_discount_type'] === 'amount' ? 'amount' : 'percent';
$ec_discount_value = isset($_POST['ec_discount_value']) ? (int)$_POST['ec_discount_value'] : 0;
$ec_min_amount = isset($_POST['ec_min_amount']) ? (int)$_POST['ec_min_amount'] : 0;
$ec_max_discount = isset($_POST['ec_max_discount']) ? (int)$_POST['ec_max_discount'] : 0;

$ec_valid_from = isset($_POST['ec_valid_from']) ? preg_replace('/[^0-9\-]/', '', $_POST['ec_valid_from']) : '';
$ec_valid_to = isset($_POST['ec_valid_to']) ? preg_replace('/[^0-9\-]/', '', $_POST['ec_valid_to']) : '';
$ec_issue_from = isset($_POST['ec_issue_from']) ? preg_replace('/[^0-9\-]/', '', $_POST['ec_issue_from']) : '';
$ec_issue_to = isset($_POST['ec_issue_to']) ? preg_replace('/[^0-9\-]/', '', $_POST['ec_issue_to']) : '';
$ec_use_limit = isset($_POST['ec_use_limit']) ? (int)$_POST['ec_use_limit'] : 0;
$ec_issue_limit_per_member = isset($_POST['ec_issue_limit_per_member']) ? (int)$_POST['ec_issue_limit_per_member'] : 0;
$ec_is_active = isset($_POST['ec_is_active']) ? (int)$_POST['ec_is_active'] : 1;

$ec_issue_type = isset($_POST['ec_issue_type']) && $_POST['ec_issue_type'] === 'auto' ? 'auto' : 'manual';
$ec_auto_trigger = ($ec_issue_type === 'auto' && isset($_POST['ec_auto_trigger']) && in_array($_POST['ec_auto_trigger'], array('on_approval','monthly_1st'))) ? $_POST['ec_auto_trigger'] : null;
$ec_issue_target_scope = isset($_POST['ec_issue_target_scope']) && $_POST['ec_issue_target_scope'] === 'individual' ? 'individual' : 'all';
$ec_issue_target_mb_id = ($ec_issue_target_scope === 'individual' && isset($_POST['ec_issue_target_mb_id'])) ? trim(preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['ec_issue_target_mb_id'])) : '';

if (!$ec_name) {
    alert('쿠폰명을 입력하세요.', './eve_coupon_form.php?w='.$w.'&ec_id='.$ec_id);
}

$tb = 'g5_ev_coupon';
$now = G5_TIME_YMDHIS;

$n = sql_escape_string($ec_name);
$ec_target_esc = sql_escape_string($ec_target);
$ec_type_esc = sql_escape_string($ec_type);
$vf = $ec_valid_from ? "'".sql_escape_string($ec_valid_from)."'" : 'NULL';
$vt = $ec_valid_to ? "'".sql_escape_string($ec_valid_to)."'" : 'NULL';

$set_ext = '';
$check_cols = array();
$cr = sql_query("SHOW COLUMNS FROM {$tb}", false);
while ($r = sql_fetch_array($cr)) $check_cols[] = $r['Field'];
if (in_array('ec_issue_from', $check_cols)) {
    $if = $ec_issue_from ? "'".sql_escape_string($ec_issue_from)."'" : 'NULL';
    $it = $ec_issue_to ? "'".sql_escape_string($ec_issue_to)."'" : 'NULL';
    $set_ext = ", ec_issue_from = {$if}, ec_issue_to = {$it}, ec_issue_limit_per_member = ".(int)$ec_issue_limit_per_member;
}
$at_esc = $ec_auto_trigger ? "'".sql_escape_string($ec_auto_trigger)."'" : 'NULL';
$mbid_esc = $ec_issue_target_mb_id ? "'".sql_escape_string($ec_issue_target_mb_id)."'" : 'NULL';
if (in_array('ec_issue_type', $check_cols)) {
    $set_ext .= ", ec_issue_type = '".sql_escape_string($ec_issue_type)."', ec_auto_trigger = {$at_esc}, ec_issue_target_scope = '".sql_escape_string($ec_issue_target_scope)."', ec_issue_target_mb_id = {$mbid_esc}";
}

if ($w === 'u' && $ec_id) {
    $row = sql_fetch("SELECT ec_id FROM {$tb} WHERE ec_id = '{$ec_id}'");
    if (!$row) alert('쿠폰을 찾을 수 없습니다.', './eve_coupon_list.php');

    $set = "ec_name = '{$n}', ec_target = '{$ec_target_esc}', ec_type = '{$ec_type_esc}', ec_discount_type = '{$ec_discount_type}', ec_discount_value = ".(int)$ec_discount_value.", ec_min_amount = ".(int)$ec_min_amount.", ec_max_discount = ".(int)$ec_max_discount.", ec_valid_from = {$vf}, ec_valid_to = {$vt}, ec_use_limit = ".(int)$ec_use_limit.", ec_is_active = ".(int)$ec_is_active;
    if (in_array('ec_issue_from', $check_cols)) {
        $if = $ec_issue_from ? "'".sql_escape_string($ec_issue_from)."'" : 'NULL';
        $it = $ec_issue_to ? "'".sql_escape_string($ec_issue_to)."'" : 'NULL';
        $set .= ", ec_issue_from = {$if}, ec_issue_to = {$it}, ec_issue_limit_per_member = ".(int)$ec_issue_limit_per_member;
    }
    if (in_array('ec_issue_type', $check_cols)) {
        $set .= ", ec_issue_type = '".sql_escape_string($ec_issue_type)."', ec_auto_trigger = {$at_esc}, ec_issue_target_scope = '".sql_escape_string($ec_issue_target_scope)."', ec_issue_target_mb_id = {$mbid_esc}";
    }
    sql_query("UPDATE {$tb} SET {$set} WHERE ec_id = '{$ec_id}'");
    alert('수정되었습니다.', './eve_coupon_list.php');
} else {
    $ec_code = 'EV' . time() . rand(100, 999);
    sql_query("INSERT INTO {$tb} SET
        ec_code = '".sql_escape_string($ec_code)."',
        ec_name = '{$n}',
        ec_target = '{$ec_target_esc}',
        ec_type = '{$ec_type_esc}',
        ec_discount_type = '{$ec_discount_type}',
        ec_discount_value = ".(int)$ec_discount_value.",
        ec_min_amount = ".(int)$ec_min_amount.",
        ec_max_discount = ".(int)$ec_max_discount.",
        ec_valid_from = {$vf},
        ec_valid_to = {$vt},
        ec_use_limit = ".(int)$ec_use_limit."
        {$set_ext}
    ", false);

    $new_id = sql_insert_id();
    if ($new_id) {
        $ec_code = 'EV' . $new_id;
        sql_query("UPDATE {$tb} SET ec_code = '".sql_escape_string($ec_code)."' WHERE ec_id = '{$new_id}'", false);
    }
    alert('등록되었습니다.', './eve_coupon_list.php');
}
