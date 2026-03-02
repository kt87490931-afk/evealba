<?php
/**
 * 인재정보 페이지 전용 좌측 사이드바 (eve_alba_talent.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
<?php include G5_THEME_PATH.'/inc/sidebar_login_widget.php'; ?>
<?php include G5_THEME_PATH.'/inc/sidebar_quick_menu.php'; ?>

<!-- 지역별 인재정보 -->
<div class="sidebar-widget">
  <div class="widget-title">📍 지역별 인재정보</div>
  <div class="widget-body">
    <div class="region-grid">
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">서울</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">경기</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">인천</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">부산</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">대구</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">광주</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">대전</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">울산</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">강원</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">충청</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">전라</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="region-btn">경상</a>
    </div>
  </div>
</div>

<!-- 업직종별 인재정보 -->
<div class="sidebar-widget">
  <div class="widget-title">💼 업직종별 인재정보</div>
  <div class="widget-body">
    <div class="job-type-list">
      <a href="#" class="job-type-item">룸싸롱<span class="job-type-count">2,258</span></a>
      <a href="#" class="job-type-item">노래주점<span class="job-type-count">1,985</span></a>
      <a href="#" class="job-type-item">마사지<span class="job-type-count">2,917</span></a>
      <a href="#" class="job-type-item">기타<span class="job-type-count">9,888</span></a>
    </div>
  </div>
</div>

<!-- 광고 섹션 -->
<div class="sidebar-widget">
  <div class="widget-title">📢 광고 섹션</div>
  <div class="widget-body">
    <div class="side-section-links">
      <a href="#" class="side-section-link">▶ 우대등록채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 프리미엄채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 스페셜채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 급구채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 추천채용정보<span class="badge-ad">광고신청</span></a>
    </div>
  </div>
</div>

<?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
