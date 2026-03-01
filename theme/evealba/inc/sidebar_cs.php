<?php
/**
 * 고객센터 페이지 전용 좌측 사이드바 (eve_alba_cs.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
<?php include G5_THEME_PATH.'/inc/sidebar_login_widget.php'; ?>
<?php include G5_THEME_PATH.'/inc/sidebar_quick_menu.php'; ?>

<!-- 고객센터 메뉴 -->
<div class="sidebar-widget">
  <div class="widget-title">🎀 고객센터</div>
  <div class="widget-body">
    <div class="side-cs-list">
      <a href="#notice-section" class="side-cs-item active"><span>📢</span> 공지사항</a>
      <a href="#qna-section" class="side-cs-item"><span>💬</span> Q&amp;A 문의게시판</a>
      <a href="#faq-section" class="side-cs-item"><span>❓</span> FAQ 자주하는 질문</a>
      <a href="#" class="side-cs-item"><span>🎨</span> 디자인 문의</a>
    </div>
  </div>
</div>

<!-- 지역별 채용정보 -->
<div class="sidebar-widget">
  <div class="widget-title">📍 지역별 채용정보</div>
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

<!-- 추천업소 배너 -->
<div class="sidebar-widget">
  <div class="widget-title">💎 추천업소</div>
  <div class="widget-body">
    <div class="side-ad-card">
      <div class="side-ad-banner g12">동탄스카이 아이퍼블릭<br><b style="font-size:14px">60분 TC12만원</b></div>
      <div class="side-ad-info">
        <div class="side-ad-name">동탄스카이 아이퍼블릭</div>
        <div class="side-ad-wage">자유복장 · TC12만원</div>
      </div>
    </div>
    <div class="side-ad-card">
      <div class="side-ad-banner g1">일프로 &amp; 텐카페<br><b>300만 보상</b></div>
      <div class="side-ad-info">
        <div class="side-ad-name">일프로 · 텐카페</div>
        <div class="side-ad-wage">300만원 보장</div>
      </div>
    </div>
  </div>
</div>

<?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
