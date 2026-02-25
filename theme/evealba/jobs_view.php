<?php
if (!defined('_GNUBOARD_')) exit;

$jobs_mypage_active = 'ongoing';
$jobs_breadcrumb_current = '채용정보 상세';
$g5['title'] = '채용정보 상세 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_register.php');
?>

<?php include(G5_THEME_PATH.'/jobs_view_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
