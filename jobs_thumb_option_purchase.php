<?php
/**
 * 썸네일 유료 옵션 구매 (PG 미연동, 즉시 구매 처리)
 * - 남은 광고기간만큼만 구매
 * - 중복구매 방지
 * POST: jr_id, thumb_icon, thumb_motion, thumb_wave, thumb_border, thumb_gradient, coupon_id
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

$result = array('ok' => 0, 'msg' => '');

if (!$is_member) {
    $result['msg'] = '로그인 후 이용해 주세요.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
$thumb_icon   = isset($_POST['thumb_icon'])   ? trim((string)$_POST['thumb_icon'])   : '';
$thumb_motion = isset($_POST['thumb_motion']) ? trim((string)$_POST['thumb_motion']) : '';
$thumb_wave   = isset($_POST['thumb_wave'])   ? (int)$_POST['thumb_wave'] : 0;
$thumb_border = isset($_POST['thumb_border']) ? trim((string)$_POST['thumb_border']) : '';
$thumb_gradient = isset($_POST['thumb_gradient']) ? trim((string)$_POST['thumb_gradient']) : '';
$coupon_id = isset($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : 0;

if (!$jr_id) {
    $result['msg'] = '잘못된 요청입니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$mb_id_esc = sql_escape_string($member['mb_id']);
$row = sql_fetch("SELECT jr_id, mb_id, jr_end_date FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    $result['msg'] = '권한이 없거나 데이터가 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$valid_until = $row['jr_end_date'] ?? '';
if (!$valid_until) {
    $result['msg'] = '광고 종료일이 설정되지 않았습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$today = date('Y-m-d');
$remaining_days = max(0, (int)((strtotime($valid_until . ' 23:59:59') - time()) / 86400));
if ($remaining_days <= 0) {
    $result['msg'] = '광고 기간이 만료되어 구매할 수 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$tb = 'g5_jobs_thumb_option_paid';
if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false))) {
    $result['msg'] = '구매 기능을 사용할 수 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

include_once(G5_LIB_PATH . '/ev_thumb_option.lib.php');
$daily_rates = ev_thumb_get_daily_rates();

$to_insert = array();
$total_amount = 0;

if ($thumb_icon && !ev_thumb_is_option_paid($jr_id, 'badge', $thumb_icon)) {
    $to_insert[] = array('badge', $thumb_icon, $daily_rates['badge']);
}
if ($thumb_motion && !ev_thumb_is_option_paid($jr_id, 'motion', $thumb_motion)) {
    $to_insert[] = array('motion', $thumb_motion, $daily_rates['motion']);
}
if ($thumb_wave > 0 && !ev_thumb_is_option_paid($jr_id, 'wave', '1')) {
    $to_insert[] = array('wave', '1', $daily_rates['wave']);
}
if ($thumb_border && !ev_thumb_is_option_paid($jr_id, 'border', $thumb_border)) {
    $to_insert[] = array('border', $thumb_border, $daily_rates['border']);
}
if (preg_match('/^P[1-4]$/', $thumb_gradient) && !ev_thumb_is_option_paid($jr_id, 'premium_color', $thumb_gradient)) {
    $to_insert[] = array('premium_color', $thumb_gradient, $daily_rates['premium']);
}

if (empty($to_insert)) {
    $result['msg'] = '선택한 유료 옵션이 없거나 이미 구매한 옵션입니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

foreach ($to_insert as $item) {
    $total_amount += $item[2] * $remaining_days;
}

$coupon_discount = 0;
if ($coupon_id > 0 && function_exists('ev_coupon_list_available_thumb')) {
    @include_once(G5_LIB_PATH . '/ev_coupon.lib.php');
    $coupons = ev_coupon_list_available_thumb($member['mb_id'], $total_amount);
    foreach ($coupons as $c) {
        if ((int)$c['ec_id'] === $coupon_id && function_exists('ev_coupon_calc_discount')) {
            $coupon_discount = ev_coupon_calc_discount($c, $total_amount);
            break;
        }
    }
}
$final_amount = max(0, $total_amount - $coupon_discount);

$order_id = 'MANUAL_' . time() . '_' . $jr_id;

sql_query("START TRANSACTION");
try {
    $first = true;
    foreach ($to_insert as $item) {
        $key = $item[0];
        $val = $item[1];
        $rate = $item[2];
        $item_amount = (int)($rate * $remaining_days);
        $coup_disc = $first ? $coupon_discount : 0;
        $coup_id = $first ? $coupon_id : 0;
        $key_esc = sql_escape_string($key);
        $val_esc = sql_escape_string($val);
        $order_esc = sql_escape_string($order_id);

        sql_query("INSERT INTO {$tb} (jr_id, mb_id, jtp_option_key, jtp_option_value, jtp_valid_until, jtp_amount, jtp_coupon_id, jtp_coupon_discount, jtp_order_id) VALUES (
            '{$jr_id}', '{$mb_id_esc}', '{$key_esc}', '{$val_esc}', '{$valid_until}',
            " . (int)$item_amount . ", " . (int)$coup_id . ", " . (int)$coup_disc . ", '{$order_esc}'
        )");
        $first = false;
    }
    sql_query("COMMIT");
} catch (Exception $e) {
    sql_query("ROLLBACK");
    $result['msg'] = '구매 처리 중 오류가 발생했습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$result['ok'] = 1;
$result['msg'] = '구매가 되었습니다.';
echo json_encode($result, JSON_UNESCAPED_UNICODE);
