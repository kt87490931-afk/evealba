<?php
/**
 * 이브알바 쿠폰 시스템 (기업/일반 회원용)
 */
if (!defined('_GNUBOARD_')) exit;

function ev_coupon_get_member_target($mb_id) {
    if (!$mb_id) return '';
    $mb = sql_fetch("SELECT mb_1 FROM g5_member WHERE mb_id = '".addslashes($mb_id)."'");
    if (!$mb) return '';
    return (isset($mb['mb_1']) && $mb['mb_1'] === 'biz') ? 'biz' : 'personal';
}

function ev_coupon_list_available_thumb($mb_id, $amount) {
    $target = ev_coupon_get_member_target($mb_id);
    if ($target !== 'biz') return array();
    $today = date('Y-m-d');
    $tb = 'g5_ev_coupon';
    $tb_issue = 'g5_ev_coupon_issue';
    if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false))) return array();
    $mb_esc = addslashes($mb_id);
    $sql = "SELECT c.ec_id, c.ec_code, c.ec_name, c.ec_discount_type, c.ec_discount_value, c.ec_min_amount, c.ec_max_discount
        FROM {$tb} c
        INNER JOIN {$tb_issue} i ON c.ec_id = i.ec_id AND i.mb_id = '{$mb_esc}' AND i.eci_used = 0
        WHERE c.ec_target = 'biz' AND c.ec_type IN ('thumb','ad') AND c.ec_is_active = 1
        AND (c.ec_valid_from IS NULL OR c.ec_valid_from <= '{$today}')
        AND (c.ec_valid_to IS NULL OR c.ec_valid_to >= '{$today}')
        AND (c.ec_min_amount = 0 OR c.ec_min_amount <= ".(int)$amount.")
        ORDER BY c.ec_discount_value DESC";
    $res = sql_query($sql, false);
    if (!$res) return array();
    $list = array();
    while ($row = sql_fetch_array($res)) $list[] = $row;
    return $list;
}

function ev_coupon_calc_discount($ec, $amount) {
    if ($ec['ec_discount_type'] === 'percent') {
        $disc = (int)($amount * $ec['ec_discount_value'] / 100);
        if (isset($ec['ec_max_discount']) && $ec['ec_max_discount'] > 0 && $disc > $ec['ec_max_discount']) $disc = $ec['ec_max_discount'];
    } else {
        $disc = (int)$ec['ec_discount_value'];
    }
    return min($disc, $amount);
}

/**
 * 채용공고 결제용 사용가능 쿠폰 (줄광고/박스광고 금액 분리)
 * line_amount: 줄광고 금액 (할인 미적용)
 * box_amount: 박스광고 금액 (채용공고 할인 적용)
 * line_ad_free: 줄광고 90일(170000) 포함 시 적용
 */
function ev_coupon_list_available_ad($mb_id, $line_amount, $box_amount) {
    $target = ev_coupon_get_member_target($mb_id);
    if ($target !== 'biz') return array();
    $today = date('Y-m-d');
    $tb = 'g5_ev_coupon';
    $tb_issue = 'g5_ev_coupon_issue';
    if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false))) return array();
    $mb_esc = addslashes($mb_id);
    $sql = "SELECT c.ec_id, c.ec_name, c.ec_type, c.ec_discount_type, c.ec_discount_value, c.ec_min_amount, c.ec_max_discount, c.ec_line_ad_days
        FROM {$tb} c
        INNER JOIN {$tb_issue} i ON c.ec_id = i.ec_id AND i.mb_id = '{$mb_esc}' AND i.eci_used = 0
        WHERE c.ec_target = 'biz' AND c.ec_type IN ('ad','line_ad_free') AND c.ec_is_active = 1
        AND (c.ec_valid_from IS NULL OR c.ec_valid_from <= '{$today}')
        AND (c.ec_valid_to IS NULL OR c.ec_valid_to >= '{$today}')
        ORDER BY c.ec_type, c.ec_discount_value DESC";
    $res = sql_query($sql, false);
    if (!$res) return array();
    $list = array();
    $line_ad_price = array(30 => 70000, 60 => 125000, 90 => 170000);
    while ($row = sql_fetch_array($res)) {
        if ($row['ec_type'] === 'line_ad_free') {
            /* 줄광고 쿠폰은 항상 목록에 포함 (선택 전에도 노출). 할인 적용 여부는 결제 시 line_amount로 검증 */
            $list[] = $row;
        } else {
            if ($box_amount >= (int)($row['ec_min_amount'] ?? 0)) $list[] = $row;
        }
    }
    return $list;
}

/**
 * 채용공고 할인금액 계산 (줄광고 제외, 박스광고만)
 */
function ev_coupon_calc_ad_discount($ec, $line_amount, $box_amount) {
    if ($ec['ec_type'] === 'line_ad_free') {
        $line_ad_price = array(30 => 70000, 60 => 125000, 90 => 170000);
        $req = isset($ec['ec_line_ad_days']) && isset($line_ad_price[(int)$ec['ec_line_ad_days']]) ? $line_ad_price[(int)$ec['ec_line_ad_days']] : 170000;
        $disc = isset($line_ad_price[(int)($ec['ec_line_ad_days'] ?? 0)]) ? $line_ad_price[(int)$ec['ec_line_ad_days']] : 170000;
        return ($line_amount >= $req) ? $disc : 0;
    }
    return ev_coupon_calc_discount($ec, $box_amount);
}
