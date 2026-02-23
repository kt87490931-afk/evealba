<?php
/**
 * 이브수다방 페이지 전용 head (eve_alba_sudabang_1.html 100% 동일)
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
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$nav_active = 'sudabang';
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span>›</span>
    <span class="current">💬 이브수다방</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 (수다방용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_sudabang.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
