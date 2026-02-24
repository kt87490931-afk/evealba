<?php
/**
 * 이력서 등록 페이지 (eve_alba_resume.html 100% 동일)
 */
if (!defined('_RESUME_REGISTER_')) define('_RESUME_REGISTER_', true);
if (!defined('_TALENT_')) define('_TALENT_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '이력서 등록 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_resume_register.php');
?>

<?php include(G5_THEME_PATH.'/resume_register_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
