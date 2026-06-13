<?php
/**
 * Readdy형 슬림 헤더 (renewal 전용)
 * PC: 상단 크롬 없음 — 좌측 사이드바만
 * 모바일: 로고 + 검색 아이콘
 */
if (!defined('_GNUBOARD_')) exit;
$_ht_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
?>
<?php if (G5_IS_MOBILE) { ?>
<header class="renewal-header-mobile">
  <a href="<?php echo G5_URL; ?>" class="renewal-mobile-logo"><em>eve</em>·알바</a>
  <a href="<?php echo $_ht_base; ?>/jobs.php" class="renewal-mobile-search" aria-label="검색">🔍</a>
</header>
<?php } ?>
