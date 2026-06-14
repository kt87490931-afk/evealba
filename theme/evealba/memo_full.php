<?php
/**
 * 알림 & 채팅 — 리뉴얼 통합 허브
 */
if (!defined('_GNUBOARD_')) exit;

include_once(G5_LIB_PATH . '/eve_chat_dm.lib.php');

$g5['title'] = '알림 & 채팅 - ' . $config['cf_title'];
$nav_active = 'memo';

$_ch_tab = isset($_GET['tab']) ? preg_replace('/[^a-z]/', '', $_GET['tab']) : 'noti';
if (!in_array($_ch_tab, array('noti', 'chat', 'region'), true)) {
    $_ch_tab = 'noti';
}
$_ch_dm_id = isset($_GET['dm_id']) ? (int)$_GET['dm_id'] : 0;
$_ch_open_jr = isset($_GET['open_jr']) ? (int)$_GET['open_jr'] : 0;

$_ch_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_ch_is_female = eve_member_is_female_normal($member);
$_ch_is_biz = eve_member_is_biz($member);
$_ch_can_region = $_ch_is_female;
$_ch_can_dm = $_ch_is_female || $_ch_is_biz;
$_ch_unread = eve_chat_dm_unread_count($member['mb_id']);

include_once(G5_THEME_PATH . '/head.memo_full.php');
include G5_THEME_PATH . '/chat_hub_main.php';
include_once(G5_THEME_PATH . '/tail.sub.php');
