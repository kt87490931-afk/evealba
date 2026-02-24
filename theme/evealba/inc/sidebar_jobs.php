<?php
/**
 * 채용정보 페이지 전용 좌측 사이드바 (eve_alba_jobs.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
<!-- 로그인 -->
<div class="sidebar-widget">
  <div class="widget-title">🌸 로그인</div>
  <div class="login-visitor">오늘 방문 <strong>24,153</strong>명</div>
  <div class="widget-body">
    <div class="login-form">
      <input type="text" placeholder="아이디">
      <input type="password" placeholder="비밀번호">
      <button>로그인</button>
    </div>
    <div class="login-links">
      <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a><span class="sep">|</span>
      <a href="<?php echo G5_BBS_URL ?>/password_lost.php">아이디 찾기</a><span class="sep">|</span>
      <a href="<?php echo G5_BBS_URL ?>/password_lost.php">비밀번호</a>
    </div>
  </div>
</div>

<!-- 지역별 채용정보 -->
<div class="sidebar-widget">
  <div class="widget-title">📍 지역별 채용정보</div>
  <div class="widget-body">
    <div class="region-grid">
      <a href="#" class="region-btn">서울</a>
      <a href="#" class="region-btn">경기</a>
      <a href="#" class="region-btn">인천</a>
      <a href="#" class="region-btn">부산</a>
      <a href="#" class="region-btn">대구</a>
      <a href="#" class="region-btn">광주</a>
      <a href="#" class="region-btn">대전</a>
      <a href="#" class="region-btn">울산</a>
      <a href="#" class="region-btn">강원</a>
      <a href="#" class="region-btn">충청</a>
      <a href="#" class="region-btn">전라</a>
      <a href="#" class="region-btn">경상</a>
    </div>
  </div>
</div>

<!-- 추천업소 배너 -->
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

<!-- 업직종별 채용정보 -->
<div class="sidebar-widget">
  <div class="widget-title">💼 업직종별 채용정보</div>
  <div class="widget-body">
    <div class="job-type-list">
      <a href="#" class="job-type-item">룸싸롱<span class="job-type-count">1,243</span></a>
      <a href="#" class="job-type-item">주점<span class="job-type-count">872</span></a>
      <a href="#" class="job-type-item">바<span class="job-type-count">548</span></a>
      <a href="#" class="job-type-item">다방<span class="job-type-count">321</span></a>
      <a href="#" class="job-type-item">마사지<span class="job-type-count">445</span></a>
      <a href="#" class="job-type-item">기타<span class="job-type-count">198</span></a>
    </div>
  </div>
</div>

<!-- 고용형태 -->
<div class="sidebar-widget">
  <div class="widget-title">📄 고용형태</div>
  <div class="widget-body">
    <div class="employ-list">
      <a href="#" class="employ-item">🌙 낮</a>
      <a href="#" class="employ-item">🌙 저녁</a>
      <a href="#" class="employ-item">🏠 숙식</a>
      <a href="#" class="employ-item">🤝 협의</a>
      <a href="#" class="employ-item">⏱ 풀타임</a>
    </div>
  </div>
</div>

<!-- 광고 링크섹션 -->
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
