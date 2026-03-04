<?php
/**
 * 이브알바 쪽지 공통 함수
 * 전체 쪽지, 회원가입 자동 쪽지, 쿠폰 발송 시 쪽지 등 공통 사용
 */
if (!defined('_GNUBOARD_')) exit;

/**
 * 쪽지 발송 (관리자 → 회원)
 * @param string $recv_mb_id 수신자 mb_id
 * @param string $content 쪽지 내용 (65536자 제한)
 * @param string $send_mb_id 발신자 mb_id (비어있으면 관리자)
 * @return bool 성공 여부
 */
function ev_send_memo($recv_mb_id, $content, $send_mb_id = '')
{
    global $g5, $config;

    $recv_mb_id = substr(preg_replace("/[^a-zA-Z0-9_]*/", "", $recv_mb_id), 0, 20);
    if (!$recv_mb_id) return false;

    if (!$send_mb_id) {
        $admin_row = sql_fetch("SELECT mb_id FROM {$g5['member_table']} WHERE mb_level = 10 ORDER BY mb_no ASC LIMIT 1");
        $send_mb_id = isset($admin_row['mb_id']) ? $admin_row['mb_id'] : (isset($config['cf_admin']) ? $config['cf_admin'] : 'admin');
    }
    $send_mb_id = substr(preg_replace("/[^a-zA-Z0-9_]*/", "", $send_mb_id), 0, 20);

    $content = preg_replace("#[\\\\]+$#", "", substr(trim($content), 0, 65536));
    $me_memo = sql_escape_string($content);
    $now = G5_TIME_YMDHIS;
    $send_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    // recv INSERT
    $sql = "INSERT INTO {$g5['memo_table']} (me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo, me_read_datetime, me_type, me_send_ip)
            VALUES ('{$recv_mb_id}', '{$send_mb_id}', '{$now}', '{$me_memo}', '0000-00-00 00:00:00', 'recv', '{$send_ip}')";
    sql_query($sql);

    $me_id = sql_insert_id();
    if (!$me_id) return false;

    // send INSERT
    $sql2 = "INSERT INTO {$g5['memo_table']} (me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo, me_read_datetime, me_send_id, me_type, me_send_ip)
             VALUES ('{$recv_mb_id}', '{$send_mb_id}', '{$now}', '{$me_memo}', '0000-00-00 00:00:00', '{$me_id}', 'send', '{$send_ip}')";
    sql_query($sql2);

    // mb_memo_call, mb_memo_cnt 업데이트
    if (function_exists('get_memo_not_read')) {
        $cnt = get_memo_not_read($recv_mb_id);
        sql_query("UPDATE {$g5['member_table']} SET mb_memo_call = '{$send_mb_id}', mb_memo_cnt = '{$cnt}' WHERE mb_id = '{$recv_mb_id}'");
    }

    return true;
}
