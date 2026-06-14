<?php
/**
 * 이브수다방 페이지 전용 head — 리뉴얼 3컬럼
 */
if (!defined('_GNUBOARD_')) exit;

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

if (!defined('EVEALBA_RENEWAL_UI')) define('EVEALBA_RENEWAL_UI', true);

$nav_active = 'sudabang';
$ev_panel_right_inc = G5_THEME_PATH.'/inc/panel_right_sudabang.php';
include G5_THEME_PATH.'/inc/head_top.php';
$ev_sidebar_legacy_inc = '';
include G5_THEME_PATH.'/inc/page_layout_open.php';
