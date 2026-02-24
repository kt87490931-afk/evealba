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
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/cs.php' : '/cs.php'; ?>">고객센터</a>
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
      <form method="get" action="<?php echo ($nav_active==='used') ? ((defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/used.php' : '/used.php') : (G5_BBS_URL.'/search.php'); ?>">
        <input type="hidden" name="sfl" value="wr_subject||wr_content">
        <input type="hidden" name="sop" value="and">
        <input type="text" name="stx" placeholder="<?php echo ($nav_active==='cs') ? '궁금하신 내용을 검색하세요' : (($nav_active==='used') ? '중고거래 상품을 검색하세요' : '업소명, 지역명으로 검색하세요'); ?>">
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
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php'; ?>" class="nav-item<?php echo ($nav_active==='talent') ? ' active' : ''; ?>"><span class="nav-icon">👑</span>인재정보</a>
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/sudabang.php' : '/sudabang.php'; ?>" class="nav-item<?php echo ($nav_active==='sudabang') ? ' active' : ''; ?>"><span class="nav-icon">💬</span>이브수다방</a>
    <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=used" class="nav-item<?php echo ($nav_active==='used') ? ' active' : ''; ?>"><span class="nav-icon">🏪</span>중고거래</a>
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/cs.php' : '/cs.php'; ?>" class="nav-item<?php echo ($nav_active==='cs') ? ' active' : ''; ?>"><span class="nav-icon">🎀</span>고객센터</a>
  </div>
</nav>

<!-- TICKER -->
<div class="ticker-wrap">
  <span class="ticker-label"><?php echo ($nav_active==='talent') ? '🌸 신규' : (($nav_active==='sudabang') ? '💬 HOT' : (($nav_active==='used') ? '🛍️ NEW' : (($nav_active==='cs') ? '🎀 고객센터' : '🔥 급구'))); ?></span>
  <div class="ticker-track">
    <div class="ticker-inner">
<?php if ($nav_active==='talent') { ?>
      <span><b>마○○</b> 여 26세 · 룸싸롱 · 강남 구해요 N</span>
      <span><b>짜○○</b> 여 27세 · 서울 경기 인천 쉬어 야간 구해요 N</span>
      <span><b>넬○○</b> 여 33세 · 160 66kg 일종 둘론 일자리 구합니다 N</span>
      <span><b>수○○</b> 여 22세 · 일구해요 N</span>
      <span><b>cnjzi○○</b> 여 27세 · 20대 77 여자 일 구해요 N</span>
      <span><b>마○○</b> 여 26세 · 룸싸롱 · 강남 구해요 N</span>
      <span><b>짜○○</b> 여 27세 · 서울 경기 인천 쉬어 야간 구해요 N</span>
      <span><b>수○○</b> 여 22세 · 일구해요 N</span>
      <span><b>cnjzi○○</b> 여 27세 · 20대 77 여자 일 구해요 N</span>
<?php } elseif ($nav_active==='cs') { ?>
      <span><b>[공지]</b> 2026 설연휴 휴무 안내</span>
      <span><b>[FAQ]</b> 개명 했을 경우 어떻게 해야할까요?</span>
      <span><b>[문의]</b> 광고문의 · 답변완료</span>
      <span><b>[디자인]</b> 상세이미지 수정 모청드립니다</span>
      <span><b>[공지]</b> 2026 설연휴 휴무 안내</span>
      <span><b>[FAQ]</b> 개명 했을 경우 어떻게 해야할까요?</span>
      <span><b>[문의]</b> 광고문의 · 답변완료</span>
      <span><b>[디자인]</b> 상세이미지 수정 모청드립니다</span>
<?php } elseif ($nav_active==='sudabang') { ?>
      <span><b>[베스트]</b> 3부 강한 하퍼 어디예요 💬24</span>
      <span><b>[밤문화]</b> 하퍼 담당분들은 잘 안... 💬17</span>
      <span><b>[단짝찾기]</b> 현재 회원님의 헝볼로 같이 일할 단짝찾기 💬8</span>
      <span><b>[법률자문]</b> 마이킹 관련 · 비밀글 💬3</span>
      <span><b>[중고거래]</b> 전략 분리리 세로패딩 · 비밀글 💬5</span>
      <span><b>[베스트]</b> 3부 강한 하퍼 어디예요 💬24</span>
      <span><b>[밤문화]</b> 하퍼 담당분들은 잘 안... 💬17</span>
      <span><b>[단짝찾기]</b> 현재 회원님의 헝볼로 같이 일할 단짝찾기 💬8</span>
<?php } elseif ($nav_active==='used') { ?>
      <span><b>웃저렴하게팔아용</b> - 의류 · 방금</span>
      <span><b>유엘핀 수입의류 판매</b> [2] - 의류 · 방금</span>
      <span><b>정뤌 불가리 세르펜티 투보가스 시계</b> - 시계 · 방금</span>
      <span><b>라쉘 로쎔제이 수입의류 홀복 미시착 새상품</b> - 의류 · 방금</span>
      <span><b>루이비통 7.5 미우미우 7.5 버버리 8.5</b> - 신발 · 방금</span>
      <span><b>베이스 메이크업 화장품 브러쉬세트</b> - 화장품 · 방금</span>
      <span><b>웃저렴하게팔아용</b> - 의류 · 방금</span>
      <span><b>유엘핀 수입의류 판매</b> - 의류 · 방금</span>
<?php } else { ?>
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
<?php } ?>
    </div>
  </div>
</div>
