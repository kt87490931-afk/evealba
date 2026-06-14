<?php
/**
 * 회원가입 페이지 (evealba_register.html 100% 동일)
 */
if (!defined('_EVE_REGISTER_')) define('_EVE_REGISTER_', true);
if (!defined('_GNUBOARD_')) exit;
if (!defined('EVEALBA_RENEWAL_UI')) define('EVEALBA_RENEWAL_UI', true);

$g5['title'] = '회원가입 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_register.php');
?>

<?php include(G5_THEME_PATH.'/register_main_renewal.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
