<?php
/**
 * 리뉴얼 3컬럼 / 기존 2컬럼 레이아웃 시작
 * $ev_sidebar_legacy_inc — renewal OFF 시 include할 사이드바 경로
 */
if (!defined('_GNUBOARD_')) exit;

$_ev_renewal_layout = defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI;
$_ev_pl_extra = isset($ev_page_layout_class) ? trim($ev_page_layout_class) : '';
?>
<div class="page-layout<?php echo $_ev_renewal_layout ? ' page-layout-renewal' : ''; ?><?php echo $_ev_pl_extra ? ' '.htmlspecialchars($_ev_pl_extra) : ''; ?>">
<?php if ($_ev_renewal_layout) { ?>
  <aside class="sidebar-left-renewal">
    <?php include G5_THEME_PATH.'/inc/sidebar_nav_renewal.php'; ?>
  </aside>
  <div class="renewal-center-wrap">
  <div class="main-area renewal-main-area">
<?php } elseif (!empty($ev_sidebar_legacy_inc) && is_file($ev_sidebar_legacy_inc)) { ?>
  <?php include $ev_sidebar_legacy_inc; ?>
  <div class="main-area">
<?php } else { ?>
  <div class="main-area">
<?php } ?>
<?php
$_ev_skip_hero = (defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI && (defined('_INDEX_') || (isset($nav_active) && $nav_active === 'jobs')));
if (empty($ev_skip_ads_banner) && !$_ev_skip_hero) {
    include G5_THEME_PATH.'/inc/ads_main_banner.php';
}
?>
