<?php
/**
 * 채용정보 페이지 전용 head
 * - head_top (nav_active=jobs), breadcrumb, page-layout, sidebar_jobs, main-area
 */
if (!defined('_GNUBOARD_')) exit;

/* evealba 반응형 테마: 모바일에서도 동일 레이아웃 사용 (evealba.css 반응형) */
if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
if (file_exists(G5_LIB_PATH.'/ev_master.lib.php')) {
    include_once(G5_LIB_PATH.'/ev_master.lib.php');
    $ev_regions = ev_get_regions();
    $ev_region_details = ev_get_region_details();
    $ev_industries = ev_get_industries();
    $ev_jobs = ev_get_jobs();
    $ev_conveniences = ev_get_conveniences();
} else {
    $ev_regions = $ev_region_details = $ev_industries = $ev_jobs = $ev_conveniences = [];
}
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$nav_active = 'jobs';
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span>›</span>
    <span class="current">📋 채용정보 리스트 (전체)</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 (채용정보용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_jobs.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
