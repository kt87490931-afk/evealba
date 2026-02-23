<?php
/**
 * 공통 상단: top-bar, header, nav, ticker
 * $nav_active = 'jobs' 시 채용정보에 active
 */
if (!defined('_GNUBOARD_')) exit;
$nav_active = isset($nav_active) ? $nav_active : '';
?>
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
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="nav-item<?php echo ($nav_active==='jobs') ? ' active' : ''; ?>"><span class="nav-icon">📋</span>채용정보</a>
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
