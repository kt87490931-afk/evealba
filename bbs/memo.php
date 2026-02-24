<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('회원만 이용하실 수 있습니다.');

define('G5_MEMO_POPUP', true);
set_session('ss_memo_delete_token', $token = uniqid(time()));

$kind = isset($_GET['kind']) ? clean_xss_tags($_GET['kind'], 0, 1) : 'recv';

if ($kind == 'recv')
    $unkind = 'send';
else if ($kind == 'send')
    $unkind = 'recv';
else if ($kind == 'unread') {
    $unkind = 'send';
    $kind = 'recv'; // unread는 recv의 미열람만
} else {
    alert("kind 변수 값이 올바르지 않습니다.");
}

$g5['title'] = '내 쪽지함';
include_once(G5_PATH.'/head.sub.php');

$memo_recv_count = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_recv_mb_id = '{$member['mb_id']}' AND me_type='recv'")['cnt'];
$memo_unread_count = function_exists('get_memo_not_read') ? get_memo_not_read($member['mb_id']) : 0;
$memo_send_count = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_send_mb_id = '{$member['mb_id']}' AND me_type='send'")['cnt'];
$member_type = (isset($member['mb_2']) && (strpos($member['mb_2'], 'biz') !== false || $member['mb_2'] === '기업')) ? '기업회원' : '일반회원';
$memo_current_tab = isset($_GET['kind']) && $_GET['kind'] === 'unread' ? 'unread' : (isset($_GET['kind']) ? $_GET['kind'] : 'recv');

if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

run_event('memo_list', $kind, $unkind, $page);

$unread_where = ($memo_current_tab === 'unread') ? " AND me_read_datetime LIKE '0%' " : "";
$sql = " select count(*) as cnt from {$g5['memo_table']} where me_{$kind}_mb_id = '{$member['mb_id']}' and me_type = '$kind' $unread_where ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$total_page  = ceil($total_count / $config['cf_page_rows']);  // 전체 페이지 계산
$from_record = ((int) $page - 1) * $config['cf_page_rows']; // 시작 열을 구함

if ($kind == 'recv')
{
    $kind_title = ($memo_current_tab === 'unread') ? '미열람' : '받은';
    $recv_img = 'on';
    $send_img = 'off';
}
else
{
    $kind_title = '보낸';
    $recv_img = 'off';
    $send_img = 'on';
}
$qstr = isset($qstr) ? $qstr : '';

$list = array();

$sql = " select a.*, b.mb_id, b.mb_nick, b.mb_email, b.mb_homepage
            from {$g5['memo_table']} a
            left join {$g5['member_table']} b on (a.me_{$unkind}_mb_id = b.mb_id)
            where a.me_{$kind}_mb_id = '{$member['mb_id']}' and a.me_type = '$kind' $unread_where
            order by a.me_id desc limit $from_record, {$config['cf_page_rows']} ";

$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;

    $mb_id = $row["me_{$unkind}_mb_id"];

    if ($row['mb_nick'])
        $mb_nick = $row['mb_nick'];
    else
        $mb_nick = '정보없음';

    $name = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

    if (substr($row['me_read_datetime'],0,1) == 0)
        $read_datetime = '아직 읽지 않음';
    else
        $read_datetime = substr($row['me_read_datetime'],2,14);

    $send_datetime = substr($row['me_send_datetime'],2,14);

    $list[$i]['mb_id'] = $mb_id;
    $list[$i]['name'] = $name;
    $list[$i]['send_datetime'] = $send_datetime;
    $list[$i]['read_datetime'] = $read_datetime;
    $list[$i]['view_href'] = './memo_view.php?me_id='.$row['me_id'].'&amp;kind='.$kind.'&amp;page='.$page;
    $list[$i]['del_href'] = './memo_delete.php?me_id='.$row['me_id'].'&amp;token='.$token.'&amp;kind='.$kind;
}

$list_kind_param = ($memo_current_tab === 'unread') ? 'unread' : $kind;
$write_pages = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "./memo.php?kind=$list_kind_param".$qstr."&amp;page=");

// 이브알바: 테마 사용 시 쪽지는 항상 테마 스킨 사용 (cf_member_skin이 basic이면 루트 스킨이 로드되므로 강제)
if (defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/skin/member/basic/memo.skin.php')) {
    $member_skin_path = G5_THEME_PATH.'/skin/member/basic';
    $member_skin_url  = G5_THEME_URL.'/skin/member/basic';
}
include_once($member_skin_path.'/memo.skin.php');

include_once(G5_PATH.'/tail.sub.php');