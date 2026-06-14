<?php
/**
 * 알림 & 채팅 페이지 head — 리뉴얼 3컬럼 (evealba_chat.html)
 */
if (!defined('_GNUBOARD_')) exit;

if (G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH . '/shop.head.php');
    return;
}

$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];
} else {
    $g5_head_title = implode(' | ', array_filter(array($g5['title'], $config['cf_title'])));
}
$g5['title'] = strip_tags($g5['title']);
$g5_head_title = strip_tags($g5_head_title);

$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location']) {
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
}
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/' . G5_ADMIN_DIR . '/') || $is_admin == 'super') {
    $g5['lo_url'] = '';
}
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $g5_head_title; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
<?php
$_renewal_ver = is_file(G5_THEME_PATH . '/css/evealba_renewal.css') ? filemtime(G5_THEME_PATH . '/css/evealba_renewal.css') : G5_CSS_VER;
$_pages_ver = is_file(G5_THEME_PATH . '/css/evealba_renewal_pages.css') ? filemtime(G5_THEME_PATH . '/css/evealba_renewal_pages.css') : G5_CSS_VER;
$_hub_ver = is_file(G5_THEME_PATH . '/css/evealba_chat_hub.css') ? filemtime(G5_THEME_PATH . '/css/evealba_chat_hub.css') : G5_CSS_VER;
?>
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL; ?>/evealba_renewal.css?ver=<?php echo $_renewal_ver; ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL; ?>/evealba_renewal_pages.css?ver=<?php echo $_pages_ver; ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL; ?>/evealba_chat_hub.css?ver=<?php echo $_hub_ver; ?>">
<script src="<?php echo G5_JS_URL; ?>/jquery-1.12.4.min.js"></script>
<script src="<?php echo G5_JS_URL; ?>/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php echo G5_JS_URL; ?>/common.js?ver=<?php echo G5_JS_VER; ?>"></script>
<script>
var g5_url = "<?php echo G5_URL; ?>";
var g5_bbs_url = "<?php echo G5_BBS_URL; ?>";
var g5_is_mobile = "<?php echo G5_IS_MOBILE ? '1' : ''; ?>";
</script>
<?php if (is_file(G5_THEME_PATH . '/js/evealba_renewal.js')) { ?>
<script src="<?php echo G5_THEME_URL; ?>/js/evealba_renewal.js?ver=<?php echo filemtime(G5_THEME_PATH . '/js/evealba_renewal.js'); ?>" defer></script>
<?php } ?>
</head>
<body class="eve-renewal-active chat-hub-page">
<?php
if (!defined('EVEALBA_RENEWAL_UI')) {
    define('EVEALBA_RENEWAL_UI', true);
}

include_once(G5_LIB_PATH . '/latest.lib.php');
include_once(G5_LIB_PATH . '/outlogin.lib.php');
include_once(G5_LIB_PATH . '/poll.lib.php');
include_once(G5_LIB_PATH . '/visit.lib.php');
include_once(G5_LIB_PATH . '/connect.lib.php');
include_once(G5_LIB_PATH . '/popular.lib.php');

$nav_active = 'memo';
$ev_renewal_main_class = 'feed-main chat-hub-feed';
$ev_panel_right_inc = G5_THEME_PATH . '/inc/panel_right.php';
include G5_THEME_PATH . '/inc/head_top.php';
$ev_sidebar_legacy_inc = '';
include G5_THEME_PATH . '/inc/page_layout_open.php';
