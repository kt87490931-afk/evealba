<?php
/**
 * 이브수다방 페이지 (eve_alba_sudabang_1.html 100% 동일)
 */
if (!defined('_SUDABANG_')) define('_SUDABANG_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '이브수다방 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_sudabang.php');
?>

<?php include(G5_THEME_PATH.'/sudabang_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
