<?php
/**
 * 메인 페이지 좌측 사이드바 (로그인, 빠른메뉴, 지역별검색, 추천업소, 고객지원)
 * - head.php, head.memo_full.php 등에서 include
 */
if (!defined('_GNUBOARD_')) exit;
?>
<aside class="left-sidebar">
  <?php include G5_THEME_PATH.'/inc/sidebar_login_widget.php'; ?>
  <div class="sidebar-widget">
    <div class="widget-title">⚡ 빠른 메뉴</div>
    <div class="widget-body">
      <div class="quick-links">
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs_register.php' : '/jobs_register.php'; ?>" class="quick-link-btn"><span class="ql-icon">📋</span>채용공고 등록</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php'; ?>" class="quick-link-btn"><span class="ql-icon">👩</span>이력서 등록</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="quick-link-btn"><span class="ql-icon">📍</span>지역별 채용</a>
        <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/sudabang.php' : '/sudabang.php'; ?>" class="quick-link-btn"><span class="ql-icon">💬</span>수다방</a>
      </div>
    </div>
  </div>
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
  <div class="sidebar-widget">
    <div class="widget-title">💎 추천업소</div>
    <div class="widget-body">
      <div class="side-ad-card">
        <div class="side-ad-banner g12">동탄스카이 아이퍼블릭<br><b style="font-size:15px">60분 TC12만원</b></div>
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
      <div class="side-ad-card">
        <div class="side-ad-banner" style="background:linear-gradient(135deg,#1A0010,#FF1B6B);font-size:18px;font-weight:900">당일<br>백만<br>UP</div>
        <div class="side-ad-info">
          <div class="side-ad-name">당일 백만원 UP 이벤트</div>
          <div class="side-ad-wage">기간 한정 특별 혜택</div>
        </div>
      </div>
    </div>
  </div>
  <?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
</aside>
