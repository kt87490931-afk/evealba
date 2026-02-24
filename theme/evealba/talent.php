<?php
/**
 * 인재정보 페이지 (eve_alba_talent.html 100% 동일)
 * - 검색 필터 GET 파라미터 처리
 */
if (!defined('_TALENT_')) define('_TALENT_', true);
if (!defined('_GNUBOARD_')) exit;

$g5['title'] = '인재정보 - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_talent.php');

$talent_filters = array(
    'er_id' => isset($_GET['er_id']) ? (int)$_GET['er_id'] : 0,
    'ei_id' => isset($_GET['ei_id']) ? (int)$_GET['ei_id'] : 0,
    'ej_id' => isset($_GET['ej_id']) ? (int)$_GET['ej_id'] : 0,
    'stx' => isset($_GET['stx']) ? trim($_GET['stx']) : ''
);
?>

<?php include(G5_THEME_PATH.'/talent_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
