<?php
/**
 * 어드민 - 쿠폰 삭제
 */
$sub_menu = '910940';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$ec_id = isset($_GET['ec_id']) ? (int)$_GET['ec_id'] : 0;
if (!$ec_id) alert('잘못된 요청입니다.', './eve_coupon_list.php');

$tb = 'g5_ev_coupon';
$tb_issue = 'g5_ev_coupon_issue';
$tb_use = 'g5_ev_coupon_use';

$row = sql_fetch("SELECT ec_id FROM {$tb} WHERE ec_id = '{$ec_id}'");
if (!$row) alert('쿠폰을 찾을 수 없습니다.', './eve_coupon_list.php');

sql_query("DELETE FROM {$tb_use} WHERE ec_id = '{$ec_id}'", false);
sql_query("DELETE FROM {$tb_issue} WHERE ec_id = '{$ec_id}'", false);
sql_query("DELETE FROM {$tb} WHERE ec_id = '{$ec_id}'", false);

alert('삭제되었습니다.', './eve_coupon_list.php');
