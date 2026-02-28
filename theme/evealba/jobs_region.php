<?php
/**
 * 지역별채용 페이지 - 채용정보와 동일 구조, 지역 필터 적용
 */
if (!defined('_JOBS_REGION_')) define('_JOBS_REGION_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '지역별채용 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_jobs.php');

$job_filters = array(
    'er_id' => isset($_GET['er_id']) ? (int)$_GET['er_id'] : 0,
    'erd_id' => isset($_GET['erd_id']) ? (int)$_GET['erd_id'] : 0,
    'ei_id' => isset($_GET['ei_id']) ? (int)$_GET['ei_id'] : 0,
    'ej_id' => isset($_GET['ej_id']) ? (int)$_GET['ej_id'] : 0,
    'ec_id' => isset($_GET['ec_id']) ? (int)$_GET['ec_id'] : 0,
    'stx' => isset($_GET['stx']) ? trim($_GET['stx']) : ''
);

$region_filter = isset($_GET['region']) ? trim($_GET['region']) : '';
?>

<?php include(G5_THEME_PATH.'/jobs_region_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
