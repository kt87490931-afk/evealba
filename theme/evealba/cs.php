<?php
/**
 * 고객센터 페이지 (eve_alba_cs.html 100% 동일)
 */
if (!defined('_CS_')) define('_CS_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '고객센터 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_cs.php');
?>

<?php include(G5_THEME_PATH.'/cs_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
