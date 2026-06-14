<?php
/**
 * 리뉴얼 3컬럼 / 기존 2컬럼 레이아웃 시작
 * $ev_sidebar_legacy_inc — renewal OFF 시 include할 사이드바 경로
 */
if (!defined('_GNUBOARD_')) exit;

$_ev_renewal_layout = defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI;
$_ev_pl_extra = isset($ev_page_layout_class) ? trim($ev_page_layout_class) : '';
$_ev_main_class = isset($ev_renewal_main_class) ? trim($ev_renewal_main_class) : 'feed-main';
if (!preg_match('/^[a-z0-9_-]+$/i', $_ev_main_class)) {
    $_ev_main_class = 'feed-main';
}
?>
<?php if ($_ev_renewal_layout) { ?>
<div class="app-wrap<?php echo $_ev_pl_extra ? ' ' . htmlspecialchars($_ev_pl_extra) : ''; ?>">
  <aside class="sidebar">
    <?php include G5_THEME_PATH . '/inc/sidebar_nav_renewal.php'; ?>
  </aside>
  <main class="<?php echo htmlspecialchars($_ev_main_class); ?>">
<?php } else { ?>
<div class="page-layout<?php echo $_ev_pl_extra ? ' ' . htmlspecialchars($_ev_pl_extra) : ''; ?>">
<?php if (!empty($ev_sidebar_legacy_inc) && is_file($ev_sidebar_legacy_inc)) { ?>
  <?php include $ev_sidebar_legacy_inc; ?>
  <div class="main-area">
<?php } else { ?>
  <div class="main-area">
<?php } ?>
<?php } ?>
<?php
if (!$_ev_renewal_layout && empty($ev_skip_ads_banner)) {
    include G5_THEME_PATH . '/inc/ads_main_banner.php';
}
?>
