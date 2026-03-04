<?php
/**
 * 채용등록 - 사용 가능 쿠폰 조회 (AJAX)
 * GET: line_amount, box_amount
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=UTF-8');

if (!isset($member['mb_id']) || !$member['mb_id']) {
    echo json_encode(array());
    exit;
}

$line = (int)($_GET['line_amount'] ?? 0);
$box = (int)($_GET['box_amount'] ?? 0);

$list = array();
if (file_exists(G5_LIB_PATH.'/ev_coupon.lib.php')) {
    include_once G5_LIB_PATH.'/ev_coupon.lib.php';
    if (function_exists('ev_coupon_list_available_ad')) {
        $list = ev_coupon_list_available_ad($member['mb_id'], $line, $box);
    }
}

echo json_encode($list);
