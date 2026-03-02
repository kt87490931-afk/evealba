<?php
if (!defined('_GNUBOARD_')) exit;

$jobs_mypage_active = 'jump_shop';
$jobs_breadcrumb_current = '🔝 점프옵션 구매하기';
$g5['title'] = '점프옵션 구매하기 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_register.php');
?>
<?php include(G5_THEME_PATH.'/jobs_jump_shop_main.php'); ?>
<?php include_once(G5_THEME_PATH.'/tail.php'); ?>
