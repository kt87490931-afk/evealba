<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_guest) {
    alert_close('회원만 이용하실 수 있습니다.');
}

define('G5_IS_MEMO_PAGE', true);

$admin_row = sql_fetch("SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_level = 10 ORDER BY mb_no ASC LIMIT 1");
$admin_mb_id = isset($admin_row['mb_id']) ? $admin_row['mb_id'] : (isset($config['cf_admin']) ? $config['cf_admin'] : 'admin');

$content = "";
$me_recv_mb_id = $admin_mb_id;
$me_id = isset($_REQUEST['me_id']) ? clean_xss_tags($_REQUEST['me_id'], 1, 1) : '';

if ($me_recv_mb_id)
{
    $mb = get_member($me_recv_mb_id);
    if (!(isset($mb['mb_id']) && $mb['mb_id']))
        alert_close('운영자 정보를 찾을 수 없습니다.');

    // 이브알바: 운영자에게만 쪽지 발송, mb_open 검사 생략

    // 4.00.15 답장 인용
    $row = sql_fetch(" select me_memo from {$g5['memo_table']} where me_id = '{$me_id}' and (me_recv_mb_id = '{$member['mb_id']}' or me_send_mb_id = '{$member['mb_id']}') ");
    if (isset($row['me_memo']) && $row['me_memo'])
    {
        $content = "\n\n\n".' >'
                         ."\n".' >'
                         ."\n".' >'.str_replace("\n", "\n> ", get_text($row['me_memo'], 0))
                         ."\n".' >'
                         .' >';

    }
}

$memo_recv_count = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_recv_mb_id = '{$member['mb_id']}' AND me_type='recv'")['cnt'];
$memo_unread_count = function_exists('get_memo_not_read') ? get_memo_not_read($member['mb_id']) : 0;
$memo_send_count = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_send_mb_id = '{$member['mb_id']}' AND me_type='send'")['cnt'];
$member_type = (isset($member['mb_2']) && (strpos($member['mb_2'], 'biz') !== false || $member['mb_2'] === '기업')) ? '기업회원' : '일반회원';
$memo_current_tab = 'form';

$g5['title'] = '쪽지 보내기';
$g5_head_title = $g5['title'] . ' | ' . $config['cf_title'];
include_once(G5_PATH.'/head.sub.php');

// 이브알바: 테마 사용 시 쪽지는 항상 테마 스킨 사용
if (defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/skin/member/basic/memo_form.skin.php')) {
    $member_skin_path = G5_THEME_PATH.'/skin/member/basic';
    $member_skin_url  = G5_THEME_URL.'/skin/member/basic';
}
$memo_action_url = G5_HTTPS_BBS_URL."/memo_form_update.php";
include_once($member_skin_path.'/memo_form.skin.php');

include_once(G5_PATH.'/tail.sub.php');