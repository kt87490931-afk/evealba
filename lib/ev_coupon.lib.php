<?php
/**
 * 이브알바 쿠폰 시스템 (기업/일반 회원용)
 */
if (!defined('_GNUBOARD_')) exit;

function ev_coupon_get_member_target($mb_id) {
    global $g5;
    if (!$mb_id) return '';
    $tbl = isset($g5['member_table']) ? $g5['member_table'] : 'g5_member';
    $mb = sql_fetch("SELECT mb_1 FROM {$tbl} WHERE mb_id = '".addslashes($mb_id)."'");
    if (!$mb) return '';
    $m1 = isset($mb['mb_1']) ? $mb['mb_1'] : '';
    return ($m1 === 'biz' || $m1 === 'business') ? 'biz' : 'personal';
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

/**
 * "지금" 발급: 저장 시 즉시 대상 회원에게 쿠폰 발급 (테스트용)
 * @param int $ec_id 쿠폰 ID
 * @param bool $include_pending true이면 승인대기(mb_7=pending/'') 회원도 포함 (테스트용)
 * @return string 발급 결과 메시지 (예: "즉시 발급 3명")
 */
function _ev_coupon_issue_now($ec_id, $include_pending = false) {
    global $g5;
    $tb = 'g5_ev_coupon';
    $tb_issue = 'g5_ev_coupon_issue';
    $ec_id = (int)$ec_id;
    if (!$ec_id) return '';

    $member_table = isset($g5['member_table']) ? $g5['member_table'] : 'g5_member';
    if (!$member_table) return '';

    $cols_check = array();
    $cr = sql_query("SHOW COLUMNS FROM {$tb}", false);
    if ($cr) while ($r = sql_fetch_array($cr)) $cols_check[] = $r['Field'];
    $select_memo = in_array('ec_memo_send', $cols_check);

    $sel = "ec_issue_from, ec_issue_to, ec_issue_limit_per_member, ec_use_limit, ec_issue_target_scope, ec_issue_target_mb_id";
    if ($select_memo) $sel .= ", ec_name, ec_memo_send";
    $ec_row = sql_fetch("SELECT {$sel} FROM {$tb} WHERE ec_id = '{$ec_id}'");
    if (!$ec_row) return '';

    $today = date('Y-m-d');
    if (!empty($ec_row['ec_issue_from']) && $today < $ec_row['ec_issue_from']) return '';
    if (!empty($ec_row['ec_issue_to']) && $today > $ec_row['ec_issue_to']) return '';

    $limit_per = (int)(isset($ec_row['ec_issue_limit_per_member']) ? $ec_row['ec_issue_limit_per_member'] : 0);
    $use_limit = (int)(isset($ec_row['ec_use_limit']) ? $ec_row['ec_use_limit'] : 0);
    $scope = isset($ec_row['ec_issue_target_scope']) ? $ec_row['ec_issue_target_scope'] : 'all';
    $target_mb = trim(isset($ec_row['ec_issue_target_mb_id']) ? $ec_row['ec_issue_target_mb_id'] : '');

    $mb_ids = array();
    $mb7_cond = $include_pending ? " AND (mb_7 = 'approved' OR mb_7 = 'pending' OR mb_7 = '' OR mb_7 IS NULL)" : " AND mb_7 = 'approved'";
    $mb1_cond = "(mb_1 = 'biz' OR mb_1 = 'business')";
    if ($scope === 'individual' && $target_mb) {
        $target_esc = sql_escape_string($target_mb);
        $m = sql_fetch("SELECT mb_id FROM {$member_table} WHERE mb_id = '{$target_esc}' AND {$mb1_cond}{$mb7_cond}");
        if ($m) $mb_ids = array($m['mb_id']);
    } else {
        $r = sql_query("SELECT mb_id FROM {$member_table} WHERE {$mb1_cond}{$mb7_cond}");
        if ($r) while ($row = sql_fetch_array($r)) $mb_ids[] = $row['mb_id'];
    }

    $issued = 0;
    $ec_name = isset($ec_row['ec_name']) ? $ec_row['ec_name'] : '';
    foreach ($mb_ids as $mb_id) {
        $mb_esc = sql_escape_string($mb_id);
        if ($use_limit > 0) {
            $total = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}'");
            if ($total && isset($total['c']) && (int)$total['c'] >= $use_limit) break;
        }
        if ($limit_per > 0) {
            $cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '{$mb_esc}'");
            if ($cnt && isset($cnt['c']) && (int)$cnt['c'] >= $limit_per) continue;
        }
        $ex = sql_fetch("SELECT eci_id FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '{$mb_esc}' LIMIT 1");
        if ($ex) continue;
        sql_query("INSERT INTO {$tb_issue} (ec_id, mb_id) VALUES ('{$ec_id}', '{$mb_esc}')", false);
        $issued++;
        if ($select_memo && !empty($ec_row['ec_memo_send']) && function_exists('ev_send_memo')) {
            $memo_content = '쿠폰이 도착하였습니다. ' . (function_exists('get_text') ? get_text($ec_name) : $ec_name);
            ev_send_memo($mb_id, $memo_content, '');
        }
    }
    return $issued > 0 ? "즉시 발급 {$issued}명" : '';
}
