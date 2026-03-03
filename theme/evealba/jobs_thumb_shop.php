<?php
/**
 * 썸네일상점 - 테마 래퍼
 */
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '썸네일상점 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_thumb_shop.php');
?>
<?php include(G5_THEME_PATH.'/jobs_thumb_shop_main.php'); ?>
<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
