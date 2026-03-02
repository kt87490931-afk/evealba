<?php
/**
 * 메인 페이지 좌측 사이드바 (로그인, 빠른메뉴, 지역별검색, 추천업소, 고객지원)
 * - head.php, head.memo_full.php 등에서 include
 */
if (!defined('_GNUBOARD_')) exit;
?>
<aside class="left-sidebar">
  <?php include G5_THEME_PATH.'/inc/sidebar_login_widget.php'; ?>
  <?php include G5_THEME_PATH.'/inc/sidebar_quick_menu.php'; ?>
  <div class="sidebar-widget">
    <div class="widget-title">📍 지역별 검색</div>
    <div class="widget-body">
      <div class="region-grid">
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">서울</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">경기</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">인천</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">부산</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">대구</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">광주</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">대전</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">울산</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">강원</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">충청</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">전라</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">경상</a>
      </div>
    </div>
  </div>
  <?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
</aside>
