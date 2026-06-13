<?php
if (!defined('_GNUBOARD_')) exit;

if (G5_IS_MOBILE && !(defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI)) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

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
?>

<?php if(defined('_INDEX_') && !(defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI)) { include G5_BBS_PATH.'/newwin.inc.php'; } ?>

<?php include G5_THEME_PATH.'/inc/head_top.php'; ?>

<!-- PAGE LAYOUT -->
<?php $ev_sidebar_legacy_inc = G5_THEME_PATH.'/inc/sidebar_main.php'; include G5_THEME_PATH.'/inc/page_layout_open.php'; ?>
