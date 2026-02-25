<?php
/**
 * 진행중인 채용정보 리스트 페이지
 * - 입금대기중/진행중 상태의 본인 채용정보 목록
 */
if (!defined('_GNUBOARD_')) exit;

$jobs_mypage_active = 'ongoing';
$jobs_breadcrumb_current = '📋 진행중인 채용정보';
$g5['title'] = '진행중인 채용정보 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_register.php');
?>

<?php include(G5_THEME_PATH.'/jobs_ongoing_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
