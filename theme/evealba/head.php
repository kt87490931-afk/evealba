<?php
if (!defined('_GNUBOARD_')) exit;

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>

<?php if(defined('_INDEX_')) { include G5_BBS_PATH.'/newwin.inc.php'; } ?>

<!-- TOP BAR -->
<div class="top-bar">
  <div class="top-bar-left">
    <span>🌸 이브알바에 오신 것을 환영합니다!</span>
    고객센터: 1588-0000 (평일 09:00~18:00)
  </div>
  <div>
    <a href="<?php echo G5_BBS_URL ?>/login.php">로그인</a>
    <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a>
    <?php if ($is_admin) { ?><a href="<?php echo G5_ADMIN_URL ?>">관리자</a><?php } ?>
    <a href="#">고객센터</a>
  </div>
</div>

<!-- HEADER -->
<header>
  <div class="header-inner">
    <a href="<?php echo G5_URL ?>" class="logo">
      <span class="logo-eve">eve</span>
      <span class="logo-dot"></span>
      <span class="logo-alba">알바</span>
    </a>
    <div class="search-box">
      <form method="get" action="<?php echo G5_BBS_URL ?>/search.php">
        <input type="hidden" name="sfl" value="wr_subject||wr_content">
        <input type="hidden" name="sop" value="and">
        <input type="text" name="stx" placeholder="업소명, 지역명으로 검색하세요">
        <button type="submit">🔍</button>
      </form>
    </div>
    <div class="header-actions">
      <div class="kakao-btn">카카오톡<br><b>EvéAlba</b></div>
      <a href="#">채용공고 등록</a>
      <a href="<?php echo G5_BBS_URL ?>/register.php" class="btn-register">이력서 등록</a>
    </div>
  </div>
</header>

<!-- NAV -->
<nav>
  <div class="nav-scroll">
    <a href="#" class="nav-item"><span class="nav-icon">📋</span>채용정보</a>
    <a href="#" class="nav-item"><span class="nav-icon">📍</span>지역별채용</a>
    <a href="#" class="nav-item"><span class="nav-icon">👑</span>인재정보</a>
    <a href="#" class="nav-item"><span class="nav-icon">💬</span>이브수다방</a>
    <a href="#" class="nav-item"><span class="nav-icon">🏪</span>중고거래</a>
    <a href="#" class="nav-item"><span class="nav-icon">🎀</span>고객센터</a>
  </div>
</nav>

<!-- TICKER -->
<div class="ticker-wrap">
  <span class="ticker-label">🔥 급구</span>
  <div class="ticker-track">
    <div class="ticker-inner">
      <span><b>[강남] 클럽마샤</b> 일급 150만원 · 밀빵OK · 당일면접</span>
      <span><b>[홍대] 하이퍼블릭 이브</b> 시급 15만원 · 초보환영</span>
      <span><b>[신사] 퍼블릭라운지</b> 룸당 10만원 · 즉시출근</span>
      <span><b>[이태원] 이브VIP</b> 하루 100만원 보장</span>
      <span><b>[압구정] 헤라클럽</b> 시급 20만원 · 2시간 40만원</span>
      <span><b>[강남] 클럽마샤</b> 일급 150만원 · 밀빵OK · 당일면접</span>
      <span><b>[홍대] 하이퍼블릭 이브</b> 시급 15만원 · 초보환영</span>
      <span><b>[신사] 퍼블릭라운지</b> 룸당 10만원 · 즉시출근</span>
      <span><b>[이태원] 이브VIP</b> 하루 100만원 보장</span>
      <span><b>[압구정] 헤라클럽</b> 시급 20만원 · 2시간 40만원</span>
    </div>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 -->
  <aside class="left-sidebar">
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
    <div class="sidebar-widget">
      <div class="widget-title">⚡ 빠른 메뉴</div>
      <div class="widget-body">
        <div class="quick-links">
          <a href="#" class="quick-link-btn"><span class="ql-icon">📋</span>채용공고 등록</a>
          <a href="#" class="quick-link-btn"><span class="ql-icon">👩</span>이력서 등록</a>
          <a href="#" class="quick-link-btn"><span class="ql-icon">📍</span>지역별 채용</a>
          <a href="#" class="quick-link-btn"><span class="ql-icon">💬</span>수다방</a>
        </div>
      </div>
    </div>
    <div class="sidebar-widget">
      <div class="widget-title">📍 지역별 검색</div>
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
  </aside>

  <!-- 메인 영역 (index.php에서 채움) -->
  <div class="main-area">
