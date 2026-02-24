<?php
/**
 * 채용공고 등록 페이지 (eve_alba_job_register.html 100% 동일)
 * - 디자인만 구현, 이브알바 포인트 연동은 추후 진행
 */
if (!defined('_JOBS_REGISTER_')) define('_JOBS_REGISTER_', true);
if (!defined('_JOBS_')) define('_JOBS_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '채용정보 등록 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs_register.php');
?>

<?php include(G5_THEME_PATH.'/jobs_register_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
