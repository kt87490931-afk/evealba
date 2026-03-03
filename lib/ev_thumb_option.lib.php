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

/** 구매한 유료 옵션 목록(유효기간 포함) - 내가 구매한 아이템 표시용 */
function ev_thumb_get_paid_options_with_dates($jr_id) {
    $tb = 'g5_jobs_thumb_option_paid';
    if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false))) return array();
    $today = date('Y-m-d');
    $res = sql_query("SELECT jtp_option_key, jtp_option_value, jtp_valid_until, jtp_amount FROM {$tb} WHERE jr_id = ".(int)$jr_id." AND jtp_valid_until >= '{$today}' ORDER BY jtp_option_key, jtp_valid_until DESC");
    if (!$res) return array();
    $list = array();
    $opt_labels = array(
        'badge' => '뱃지', 'motion' => '제목모션', 'wave' => '컬러웨이브',
        'border' => '테두리', 'premium_color' => '유료컬러'
    );
    $val_labels = array(
        'beginner'=>'초보환영','room'=>'원룸제공','luxury'=>'고급시설','black'=>'블랙관리',
        'phone'=>'폰비지급','size'=>'사이즈X','set'=>'세트환영','pickup'=>'픽업가능','member'=>'1회원제','kkongbi'=>'꽁비지급',
        'shimmer'=>'글씨확대','soft-blink'=>'소프트블링크','glow'=>'글로우','bounce'=>'바운스',
        'gold'=>'골드테두리','pink'=>'핫핑크','charcoal'=>'차콜','royalblue'=>'로얄블루','royalpurple'=>'로얄퍼플',
        'P1'=>'메탈릭골드','P2'=>'메탈릭실버','P3'=>'카본','P4'=>'오로라','1'=>'컬러웨이브'
    );
    while ($row = sql_fetch_array($res)) {
        $key = $row['jtp_option_key'];
        $val = $row['jtp_option_value'];
        $label = ($opt_labels[$key] ?? $key) . ' - ' . ($val_labels[$val] ?? $val);
        $list[] = array('key'=>$key,'value'=>$val,'valid_until'=>$row['jtp_valid_until'],'amount'=>$row['jtp_amount'],'label'=>$label);
    }
    return $list;
}
