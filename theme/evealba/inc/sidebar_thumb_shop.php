<?php
/**
 * 썸네일상점 좌측 사이드바
 */
if (!defined('_GNUBOARD_')) exit;
$_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$_jobs_base = $_base;
?>
<aside class="left-sidebar-inner">
  <div class="widget-title">🖼️ 썸네일상점</div>
  <div class="widget-body">
    <p style="font-size:13px;color:#666;line-height:1.6;">채용광고 썸네일을 꾸미고<br>유료 옵션을 구매하세요.</p>
    <a href="<?php echo $_jobs_base; ?>/jobs_ongoing.php" class="side-section-link">📋 진행중인 채용정보</a>
  </div>
</aside>
