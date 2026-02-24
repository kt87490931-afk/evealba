<?php
/**
 * 채용공고 등록 페이지 전용 head
 * - head_top (nav_active=jobs), breadcrumb, page-layout, sidebar_jobs_register, main-area
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

$nav_active = 'jobs';
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span class="sep">›</span>
    <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/register_form.php'); ?>">회원정보</a>
    <span class="sep">›</span>
    <span class="current">📝 채용정보 등록</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 (채용공고 등록용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_jobs_register.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
