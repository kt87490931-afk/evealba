<?php
/**
 * 썸네일 옵션 가격 및 결제여부
 */
if (!defined('_GNUBOARD_')) exit;

$GLOBALS['_ev_thumb_daily_rates'] = array('premium'=>1667,'badge'=>1000,'motion'=>1000,'wave'=>1667,'border'=>1000);

function ev_thumb_get_daily_rates() {
    return $GLOBALS['_ev_thumb_daily_rates'];
}

function ev_thumb_get_paid_options($jr_id) {
    $tb = 'g5_jobs_thumb_option_paid';
    if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false))) return array();
    $today = date('Y-m-d');
    $res = sql_query("SELECT jtp_option_key, jtp_option_value FROM {$tb} WHERE jr_id = ".(int)$jr_id." AND jtp_valid_until >= '{$today}'");
    if (!$res) return array();
    $paid = array();
    while ($row = sql_fetch_array($res)) {
        $key = $row['jtp_option_key'];
        if (!isset($paid[$key])) $paid[$key] = array();
        $paid[$key][] = $row['jtp_option_value'];
    }
    return $paid;
}

function ev_thumb_is_option_paid($jr_id, $option_key, $option_value) {
    $paid = ev_thumb_get_paid_options($jr_id);
    if (empty($paid[$option_key])) return false;
    return in_array($option_value, $paid[$option_key], true);
}
