<?php
/**
 * 좌측 사이드바 — 지역별 검색 + 고객지원 (renewal용)
 */
if (!defined('_GNUBOARD_')) exit;
$_rg_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_regions = array('서울', '경기', '인천', '부산', '대구', '광주', '대전', '울산', '강원', '충청', '전라', '경상');
?>
<div class="sidebar-region-renewal sidebar-widget">
  <div class="widget-title">🗺 지역별 검색</div>
  <div class="widget-body">
    <div class="region-grid">
<?php foreach ($_regions as $_rg) { ?>
      <a href="<?php echo $_rg_base; ?>/jobs.php?stx=<?php echo urlencode($_rg); ?>" class="region-btn"><?php echo $_rg; ?></a>
<?php } ?>
    </div>
  </div>
</div>
<?php include G5_THEME_PATH . '/inc/sidebar_cs_widget.php'; ?>
