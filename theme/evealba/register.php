<?php
/**
 * 회원가입 페이지 (eve_alba_register.html 100% 동일, footer-info 제외)
 * Step1 약관동의 → Step2 회원정보입력 → Step3 가입완료
 */
if (!defined('_EVE_REGISTER_')) define('_EVE_REGISTER_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '회원가입 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_register.php');
?>

<?php include(G5_THEME_PATH.'/register_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
