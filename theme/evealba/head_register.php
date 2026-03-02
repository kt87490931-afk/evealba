<?php
/**
 * 회원가입 페이지 전용 head
 * - head_top, breadcrumb, page-layout (단일컬럼, 사이드바 없음)
 */
if (!defined('_GNUBOARD_')) exit;

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$nav_active = '';
$g5['title'] = '회원가입 - '.$config['cf_title'];
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner" id="breadcrumb">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span class="sep">›</span>
    <span class="current">📝 회원가입</span>
  </div>
</div>

<!-- PAGE LAYOUT (단일 컬럼, 사이드바 없음) -->
<div class="page-layout layout-register">
  <div class="main-area">
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>
