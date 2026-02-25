<?php
if (!defined('_GNUBOARD_')) exit;

$jobs_mypage_active = 'ended';
$jobs_breadcrumb_current = '📁 마감된 채용정보';
$g5['title'] = '마감된 채용정보 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_register.php');
?>
<?php include(G5_THEME_PATH.'/jobs_ended_main.php'); ?>
<?php include_once(G5_THEME_PATH.'/tail.php'); ?>
