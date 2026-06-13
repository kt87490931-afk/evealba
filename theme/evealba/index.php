<?php
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit;

if (!defined('EVEALBA_RENEWAL_UI')) define('EVEALBA_RENEWAL_UI', true);

if (G5_IS_MOBILE && !(defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI)) {
    include_once(G5_THEME_MOBILE_PATH.'/index.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/index.php');
    return;
}

include_once(G5_THEME_PATH.'/head.php');
?>

<?php include(G5_THEME_PATH.'/index_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
