<?php
/**
 * 인재정보 페이지 (eve_alba_talent.html 100% 동일)
 */
if (!defined('_TALENT_')) define('_TALENT_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '인재정보 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_talent.php');
?>

<?php include(G5_THEME_PATH.'/talent_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
