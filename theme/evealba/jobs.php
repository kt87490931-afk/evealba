<?php
/**
 * 채용정보 페이지 (eve_alba_jobs.html 100% 동일)
 * - 디자인만 구현, 기능 연동은 추후 진행
 */
if (!defined('_JOBS_')) define('_JOBS_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '채용정보 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs.php');
?>

<?php include(G5_THEME_PATH.'/jobs_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
