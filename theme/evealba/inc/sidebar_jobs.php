<?php
/**
 * 채용정보 페이지 전용 좌측 사이드바 (eve_alba_jobs.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
<?php include G5_THEME_PATH.'/inc/sidebar_login_widget.php'; ?>

<!-- 지역별 채용정보 -->
<div class="sidebar-widget">
  <div class="widget-title">📍 지역별 채용정보</div>
  <div class="widget-body">
    <div class="region-grid">
      <?php
      $sidebar_regions = isset($ev_regions) && !empty($ev_regions) ? $ev_regions : array(
        array('er_name'=>'서울'), array('er_name'=>'경기'), array('er_name'=>'인천'), array('er_name'=>'부산'),
        array('er_name'=>'대구'), array('er_name'=>'광주'), array('er_name'=>'대전'), array('er_name'=>'울산'),
        array('er_name'=>'강원'), array('er_name'=>'경남'), array('er_name'=>'경북'), array('er_name'=>'전남'),
        array('er_name'=>'전북'), array('er_name'=>'충남'), array('er_name'=>'충북'), array('er_name'=>'세종'),
        array('er_name'=>'제주'));
      $jobs_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php';
      foreach ($sidebar_regions as $sr) {
        echo '<a href="'.htmlspecialchars($jobs_url).'" class="region-btn">'.htmlspecialchars($sr['er_name']).'</a>';
      }
      ?>
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
