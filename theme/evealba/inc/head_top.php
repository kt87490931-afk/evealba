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
    <?php if ($is_member) { ?>
    <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/register_form.php'); ?>">마이페이지</a>
    <a href="<?php echo G5_BBS_URL; ?>/logout.php">로그아웃</a>
    <?php } else { ?>
    <a href="<?php echo G5_BBS_URL ?>/login.php">로그인</a>
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/eve_register.php' : '/eve_register.php'; ?>">회원가입</a>
    <?php } ?>
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
        <input type="text" name="stx" placeholder="<?php echo ($nav_active==='cs') ? '궁금하신 내용을 검색하세요' : (($nav_active==='used') ? '중고거래 상품을 검색하세요' : '업소명, 지역명으로 검색'); ?>">
        <button type="submit">🔍</button>
      </form>
    </div>
    <button type="button" class="hamburger-btn" onclick="document.getElementById('mobileSlideMenu').classList.add('open');document.body.style.overflow='hidden';" aria-label="메뉴 열기">
      <span></span><span></span><span></span>
    </button>
    <div class="header-actions">
      <div class="kakao-btn">카카오톡<br><b>EvéAlba</b></div>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs_register.php' : '/jobs_register.php'; ?>">채용공고 등록</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php'; ?>" class="btn-register">이력서 등록</a>
    </div>
  </div>
</header>

<!-- NAV -->
<nav>
  <div class="nav-scroll">
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="nav-item<?php echo ($nav_active==='jobs') ? ' active' : ''; ?>"><span class="nav-icon">📋</span>채용정보</a>
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="nav-item"><span class="nav-icon">📍</span>지역별채용</a>
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
<?php } elseif ($nav_active==='cs') {
  $_ct_items = array();
  $_ct_pfx = (defined('G5_TABLE_PREFIX') && G5_TABLE_PREFIX) ? G5_TABLE_PREFIX : 'g5_';
  $_ct_bbs = (defined('G5_BBS_URL') && G5_BBS_URL) ? rtrim(G5_BBS_URL,'/') : '';
  if (function_exists('get_pretty_url')) {
    $_ct_burl = function($bt,$id='') { return get_pretty_url($bt,$id); };
  } else {
    $_ct_burl = function($bt,$id='') { return (defined('G5_BBS_URL')?G5_BBS_URL:'/bbs').'/board.php?bo_table='.$bt.($id?'&wr_id='.$id:''); };
  }
  $chk = @sql_query("SHOW TABLES LIKE '{$_ct_pfx}write_notice'", false);
  if ($chk && @sql_num_rows($chk)) {
    $res = sql_query("SELECT wr_id, wr_subject FROM {$_ct_pfx}write_notice WHERE wr_is_comment=0 ORDER BY wr_num ASC LIMIT 3", false);
    if ($res) while ($r=sql_fetch_array($res)) {
      $_ct_items[] = array('badge'=>'[공지]','txt'=>get_text($r['wr_subject']),'url'=>$_ct_burl('notice',$r['wr_id']));
    }
  }
  $chk2 = @sql_query("SHOW TABLES LIKE '{$_ct_pfx}write_ad_inquiry'", false);
  if ($chk2 && @sql_num_rows($chk2)) {
    $res2 = sql_query("SELECT wr_id, wr_subject, wr_comment FROM {$_ct_pfx}write_ad_inquiry WHERE wr_is_comment=0 ORDER BY wr_id DESC LIMIT 3", false);
    if ($res2) while ($r2=sql_fetch_array($res2)) {
      $_ct_items[] = array('badge'=>'[문의]','txt'=>get_text($r2['wr_subject']).($r2['wr_comment']>0?' · 답변완료':''),'url'=>$_ct_burl('ad_inquiry',$r2['wr_id']));
    }
  }
  $_ct_faq_write = $_ct_pfx . 'write_faq';
  $chk3 = @sql_query("SHOW TABLES LIKE '{$_ct_faq_write}'", false);
  if ($chk3 && @sql_num_rows($chk3)) {
    $res3 = sql_query("SELECT wr_id, wr_subject FROM {$_ct_faq_write} WHERE wr_is_comment=0 ORDER BY wr_num ASC, wr_id DESC LIMIT 3", false);
    if ($res3) while ($r3=sql_fetch_array($res3)) {
      $_ct_items[] = array('badge'=>'[FAQ]','txt'=>get_text($r3['wr_subject']),'url'=>$_ct_bbs.'/board.php?bo_table=faq&wr_id='.(int)$r3['wr_id']);
    }
  }
  if (empty($_ct_items)) {
    $_ct_items[] = array('badge'=>'[공지]','txt'=>'등록된 게시글이 없습니다','url'=>'#');
  }
  $_ct_double = array_merge($_ct_items, $_ct_items);
  foreach ($_ct_double as $_ci) {
    echo '<span><a href="'.htmlspecialchars($_ci['url'],ENT_QUOTES).'" style="color:inherit;text-decoration:none;"><b>'.htmlspecialchars($_ci['badge'],ENT_QUOTES).'</b> '.htmlspecialchars(mb_strlen($_ci['txt'],'UTF-8')>28?mb_substr($_ci['txt'],0,28,'UTF-8').'…':$_ci['txt'],ENT_QUOTES).'</a></span>';
  }
} elseif ($nav_active==='sudabang') { ?>
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
<?php } else {
  $_ticker_urgent = array();
  if (function_exists('get_jobs_by_type')) {
      $_ticker_urgent = get_jobs_by_type('급구', 30);
  }
  if (!empty($_ticker_urgent)) {
      $_ticker_spans = '';
      foreach ($_ticker_urgent as $_tu) {
          $_tu_data = is_string($_tu['jr_data']) ? @json_decode($_tu['jr_data'], true) : (array)$_tu['jr_data'];
          $_tu_region = '';
          if (!empty($_tu_data['desc_location'])) {
              $_tu_region = trim(explode(' ', trim($_tu_data['desc_location']))[0]);
          }
          $_tu_name = htmlspecialchars($_tu['jr_nickname'] ?: $_tu['jr_company']);
          $_tu_promo = '';
          if (!empty($_tu_data['desc_promo'])) {
              $_tu_promo = htmlspecialchars(mb_substr($_tu_data['desc_promo'], 0, 25, 'UTF-8'));
          } elseif (!empty($_tu['jr_title'])) {
              $_tu_promo = htmlspecialchars(mb_substr($_tu['jr_title'], 0, 25, 'UTF-8'));
          }
          $_tu_text = '<span><b>[' . htmlspecialchars($_tu_region ?: '전국') . '] ' . $_tu_name . '</b>';
          if ($_tu_promo) $_tu_text .= ' ' . $_tu_promo;
          $_tu_text .= '</span>';
          $_ticker_spans .= $_tu_text;
      }
      echo $_ticker_spans;
      echo $_ticker_spans;
      $_ticker_cnt = count($_ticker_urgent);
      $_ticker_dur = max(30, $_ticker_cnt * 3);
      echo '<style>.ticker-inner{animation-duration:'.$_ticker_dur.'s !important;}</style>';
  } else { ?>
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
<?php } } ?>
    </div>
  </div>
</div>

<!-- MOBILE SLIDE MENU -->
<?php
$_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$_is_biz = ($is_member && isset($member['mb_1']) && $member['mb_1'] === 'biz');
$_jobs_base = $_base;
?>
<div id="mobileSlideMenu" class="mobile-slide-menu">
  <div class="msm-overlay" onclick="document.getElementById('mobileSlideMenu').classList.remove('open');document.body.style.overflow='';"></div>
  <div class="msm-panel">
    <div class="msm-header">
      <span class="msm-title">전체 메뉴</span>
      <button type="button" class="msm-close" onclick="document.getElementById('mobileSlideMenu').classList.remove('open');document.body.style.overflow='';" aria-label="닫기">✕</button>
    </div>
    <div class="msm-body">

<?php if ($is_member) { ?>
      <?php if ($is_admin || $_is_biz) { ?>
      <div class="msm-section">
        <div class="msm-section-title">👑 채용정보 MY PAGE</div>
        <a href="<?php echo $_jobs_base; ?>/jobs_register.php" class="msm-link">📝 채용정보등록</a>
        <a href="<?php echo $_jobs_base; ?>/jobs_ongoing.php" class="msm-link">📋 진행중인 채용정보</a>
        <a href="<?php echo $_jobs_base; ?>/jobs_ended.php" class="msm-link">📁 마감된 채용정보</a>
        <a href="<?php echo $_jobs_base; ?>/jobs_jump_shop.php" class="msm-link">🔝 점프옵션 구매하기</a>
        <a href="<?php echo $_jobs_base; ?>/jobs_payment_history.php" class="msm-link">💳 유료결제 내역</a>
      </div>
      <?php } ?>

      <?php if ($is_admin || !$_is_biz) {
        $_mob_resume_url = '#';
        $_mob_rs = @sql_fetch("SELECT rs_id FROM g5_resume WHERE mb_id = '".addslashes($member['mb_id'])."' AND rs_status = 'active' LIMIT 1");
        if ($_mob_rs) $_mob_resume_url = $_jobs_base.'/talent_view.php?rs_id='.(int)$_mob_rs['rs_id'];
      ?>
      <div class="msm-section">
        <div class="msm-section-title">👩 인재정보 MY PAGE</div>
        <a href="<?php echo $_jobs_base; ?>/resume_register.php" class="msm-link">📄 이력서 리스트</a>
        <a href="#" class="msm-link">📋 채용정보 스크랩</a>
        <a href="#" class="msm-link">👤 맞춤구인정보</a>
        <a href="<?php echo $_mob_resume_url; ?>" class="msm-link">⚙️ 이력서 수정</a>
        <a href="#" class="msm-link">📝 내가 작성한 게시글</a>
        <a href="#" class="msm-link">💬 내가 작성한 댓글</a>
        <a href="#" class="msm-link">⭐ 즐겨찾기한 게시글</a>
      </div>
      <?php } ?>
<?php } else { ?>
      <div class="msm-section">
        <div class="msm-login-box">
          <p>로그인 후 이용 가능합니다.</p>
          <a href="<?php echo G5_BBS_URL; ?>/login.php" class="msm-btn-login">로그인</a>
          <a href="<?php echo $_base; ?>/eve_register.php" class="msm-btn-signup">회원가입</a>
        </div>
      </div>
<?php } ?>

      <div class="msm-section">
        <div class="msm-section-title">📌 메인 메뉴</div>
        <a href="<?php echo $_jobs_base; ?>/jobs.php" class="msm-link">📋 채용정보</a>
        <a href="<?php echo $_jobs_base; ?>/jobs.php" class="msm-link">📍 지역별채용</a>
        <a href="<?php echo $_jobs_base; ?>/talent.php" class="msm-link">👑 인재정보</a>
        <a href="<?php echo $_jobs_base; ?>/sudabang.php" class="msm-link">💬 이브수다방</a>
        <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=used" class="msm-link">🏪 중고거래</a>
        <a href="<?php echo $_jobs_base; ?>/cs.php" class="msm-link">🎀 고객센터</a>
      </div>

      <div class="msm-section">
        <div class="msm-section-title">💬 커뮤니티</div>
        <a href="javascript:void(0);" onclick="if(typeof openChatWidget==='function')openChatWidget();document.getElementById('mobileSlideMenu').classList.remove('open');document.body.style.overflow='';" class="msm-link">💬 채팅</a>
        <a href="<?php echo $_base; ?>/memo_full.php" class="msm-link">📩 쪽지</a>
      </div>

<?php if ($is_member) { ?>
      <div class="msm-section">
        <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/register_form.php'); ?>" class="msm-link">⚙️ 회원정보 수정</a>
        <?php if ($is_admin) { ?><a href="<?php echo G5_ADMIN_URL; ?>" class="msm-link">🔧 관리자 페이지</a><?php } ?>
        <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="msm-link msm-logout">🔐 로그아웃</a>
      </div>
<?php } ?>

    </div>
  </div>
</div>
