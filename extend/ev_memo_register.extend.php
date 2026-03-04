<?php
/**
 * 회원가입 시 자동 쪽지 발송
 * register_form_update_after 훅 사용
 */
if (!defined('_GNUBOARD_')) exit;

include_once(G5_LIB_PATH . '/ev_memo.lib.php');

add_event('register_form_update_after', 'ev_memo_on_register_after', 10, 2);

function ev_memo_on_register_after($mb_id, $w)
{
    if ($w !== '') {
        return;
    }

    global $g5;

    $tb = 'g5_ev_memo_config';
    $exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
    if (!$exists) {
        return;
    }

    $cfg = sql_fetch("SELECT em_join_memo_on, em_join_memo_general, em_join_memo_biz FROM {$tb} WHERE emc_id = 1");
    if (!$cfg || empty($cfg['em_join_memo_on'])) {
        return;
    }

    $mb = sql_fetch("SELECT mb_id, mb_1 FROM {$g5['member_table']} WHERE mb_id = '" . sql_escape_string($mb_id) . "'");
    if (!$mb) {
        return;
    }

    $content = ($mb['mb_1'] === 'biz')
        ? (trim($cfg['em_join_memo_biz'] ?? '') ?: '이브알바에 기업회원으로 가입해 주셔서 감사합니다. 승인 후 서비스를 이용하실 수 있습니다.')
        : (trim($cfg['em_join_memo_general'] ?? '') ?: '이브알바에 가입해 주셔서 감사합니다.');

    ev_send_memo($mb_id, $content, '');
}
