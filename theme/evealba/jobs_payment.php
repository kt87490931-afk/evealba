<?php
if (!defined('_GNUBOARD_')) exit;

$jobs_mypage_active = 'payment';
$jobs_breadcrumb_current = '💳 유료결제 내역';
$g5['title'] = '유료결제 내역 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_register.php');
?>
<?php include(G5_THEME_PATH.'/jobs_payment_main.php'); ?>
<?php include_once(G5_THEME_PATH.'/tail.php'); ?>
