<?php
/**
 * 고객센터 메인 영역 - DB 연동 (공지/광고문의/FAQ)
 */
if (!defined('_GNUBOARD_')) exit;

$_bbs = (defined('G5_BBS_URL') && G5_BBS_URL) ? rtrim(G5_BBS_URL, '/') : '';
if (!$_bbs && defined('G5_URL') && G5_URL) {
  $_bbs = rtrim(G5_URL, '/') . '/' . (defined('G5_BBS_DIR') ? G5_BBS_DIR : 'bbs');
}
if (!$_bbs) $_bbs = '/bbs';
$_pfx = (defined('G5_TABLE_PREFIX') && G5_TABLE_PREFIX) ? G5_TABLE_PREFIX : 'g5_';

// URL 생성 헬퍼 (G5_BBS_URL 기반, 절대경로 보장)
$_burl = function($bo_table, $wr_id = '') use ($_bbs) {
  if (function_exists('get_pretty_url')) {
    $u = get_pretty_url($bo_table, $wr_id ?: '');
    if ($u && (strpos($u, 'http') === 0 || strpos($u, '/') === 0)) return $u;
  }
  $base = (defined('G5_BBS_URL') && G5_BBS_URL) ? rtrim(G5_BBS_URL, '/') : $_bbs;
  $u = $base . '/board.php?bo_table=' . $bo_table;
  if ($wr_id) $u .= '&wr_id=' . $wr_id;
  return $u;
};

$_notice_url = $_burl('notice');
$_ad_inq_url = $_burl('ad_inquiry');
$_ad_inq_write = $_bbs . '/write.php?bo_table=ad_inquiry';
$_faq_url = $_bbs . '/board.php?bo_table=faq';

// 공지사항 (notice)
$_notice_rows = array();
$_notice_tb = $_pfx . 'write_notice';
$chk = @sql_query("SHOW TABLES LIKE '{$_notice_tb}'", false);
if ($chk && @sql_num_rows($chk)) {
  $r = sql_fetch("SELECT COUNT(*) as c FROM {$_notice_tb} WHERE wr_is_comment = 0");
  if ($r && (int)$r['c'] > 0) {
    $_notice_res = sql_query("SELECT wr_id, wr_subject, wr_datetime FROM {$_notice_tb} WHERE wr_is_comment = 0 ORDER BY wr_num ASC, wr_id DESC LIMIT 9", false);
    if ($_notice_res) {
      while ($nr = sql_fetch_array($_notice_res)) {
        $_notice_rows[] = $nr;
      }
    }
  }
}

// 광고문의 & 일반문의 (ad_inquiry) - 부모글만
$_ad_rows = array();
$_ad_tb = $_pfx . 'write_ad_inquiry';
$chk2 = @sql_query("SHOW TABLES LIKE '{$_ad_tb}'", false);
if ($chk2 && @sql_num_rows($chk2)) {
  $_ad_res = sql_query("SELECT wr_id, wr_subject, wr_datetime, wr_comment FROM {$_ad_tb} WHERE wr_is_comment = 0 ORDER BY wr_num ASC, wr_id DESC LIMIT 9", false);
  if ($_ad_res) {
    while ($ar = sql_fetch_array($_ad_res)) {
      $_ad_rows[] = $ar;
    }
  }
}

// FAQ (게시판 faq 연동 - g5_write_faq)
$_faq_rows = array();
$_faq_write_tbl = $_pfx . 'write_faq';
$chk_faq = @sql_query("SHOW TABLES LIKE '{$_faq_write_tbl}'", false);
if ($chk_faq && @sql_num_rows($chk_faq)) {
  $_faq_res = sql_query("SELECT wr_subject AS fa_subject, wr_content AS fa_content FROM {$_faq_write_tbl} WHERE wr_is_comment = 0 ORDER BY wr_num ASC, wr_id DESC LIMIT 15", false);
  if ($_faq_res) {
    while ($fr = sql_fetch_array($_faq_res)) {
      $_faq_rows[] = $fr;
    }
  }
}
?>
    <!-- CS 히어로 배너 -->
    <div class="cs-hero">
      <div class="cs-hero-text">
        <h1>🎀 고객지원 <small style="font-size:14px;font-weight:400;color:rgba(255,255,255,.75);display:inline;font-family:'Outfit',sans-serif;">CUSTOMER CENTER</small><br>
        <small>고객님의 소리를 귀담아 듣겠습니다.<br>더 낮은 자세로 임하겠습니다.<br>여러분의 소중한 의견을 담아주세요.</small></h1>
      </div>
      <div class="cs-hero-phone">
        <span class="phone-label">📞 전화 상담</span>
        <span class="phone-num">1588-0000</span>
        <div class="phone-hours">평일 09:30~19:00 · 점심 12:00~13:30<br>*공휴일·일요일 근무하지 않습니다.</div>
        <div style="margin-top:10px;">
          <span style="background:#FEE500;color:#333;padding:5px 14px;border-radius:14px;font-size:12px;font-weight:900;display:inline-block;">💬 카카오톡 : EvéAlba</span>
        </div>
      </div>
    </div>

    <!-- 3대 진입 카드 (게시판 페이지로 직접 이동) -->
    <div class="cs-entry-grid">
      <a href="<?php echo htmlspecialchars($_notice_url, ENT_QUOTES); ?>" class="cs-entry-card">
        <div class="cs-entry-icon cei-pink">📢</div>
        <div class="cs-entry-title">NOTICE 공지사항</div>
        <div class="cs-entry-desc">사이트의 공지내용을<br>알려드립니다.</div>
        <span class="cs-entry-btn">공지사항 게시판 →</span>
      </a>
      <a href="<?php echo htmlspecialchars($_ad_inq_url, ENT_QUOTES); ?>" class="cs-entry-card">
        <div class="cs-entry-icon cei-purple">💬</div>
        <div class="cs-entry-title">Q&amp;A 문의게시판</div>
        <div class="cs-entry-desc">무엇이든 물어보세요!<br>광고문의 &amp; 일반문의</div>
        <span class="cs-entry-btn">광고문의 &amp; 일반문의 →</span>
      </a>
      <a href="<?php echo htmlspecialchars($_faq_url, ENT_QUOTES); ?>" class="cs-entry-card">
        <div class="cs-entry-icon cei-blue">❓</div>
        <div class="cs-entry-title">FAQ 자주하는 질문</div>
        <div class="cs-entry-desc">쉽게 한눈에 확인하는<br>궁금증!</div>
        <span class="cs-entry-btn">FAQ 게시판 →</span>
      </a>
    </div>

    <!-- 공지사항 + FAQ (2열) -->
    <div class="cs-grid-2">

      <!-- 공지사항 -->
      <div id="notice-section" class="cs-board-card bh-notice">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">📢</span>
            <div>
              <div class="cs-board-name">공지사항</div>
              <div class="cs-board-desc">운영팀 공지 · 이벤트 안내</div>
            </div>
          </div>
          <a href="<?php echo htmlspecialchars($_notice_url, ENT_QUOTES); ?>" class="board-more">더보기 →</a>
        </div>
        <div class="cs-post-list">
<?php if (empty($_notice_rows)) { ?>
          <div class="cs-post-item" style="justify-content:center;color:#999;font-size:13px;">등록된 공지가 없습니다</div>
<?php } else {
  foreach ($_notice_rows as $_ni => $_nr) {
    $_subj = get_text($_nr['wr_subject']);
    $_subj_short = mb_strlen($_subj, 'UTF-8') > 35 ? mb_substr($_subj, 0, 35, 'UTF-8').'…' : $_subj;
    $_date = substr($_nr['wr_datetime'], 0, 10);
    $_badge = ($_ni < 3) ? 'pb-notice' : (($_date >= date('Y-m-d', strtotime('-3 days'))) ? 'pb-new' : 'pb-hot');
    $_badge_txt = ($_ni < 3) ? '공지' : (($_date >= date('Y-m-d', strtotime('-3 days'))) ? 'NEW' : 'HOT');
    $_link = $_burl('notice', $_nr['wr_id']);
?>
          <div class="cs-post-item">
            <span class="post-badge <?php echo $_badge; ?>"><?php echo $_badge_txt; ?></span>
            <a href="<?php echo htmlspecialchars($_link, ENT_QUOTES); ?>" class="post-title"><?php echo htmlspecialchars($_subj_short, ENT_QUOTES); ?></a>
            <span class="post-meta"><?php echo $_date; ?></span>
          </div>
<?php } } ?>
        </div>
      </div>

      <!-- FAQ -->
      <div id="faq-section" class="cs-board-card">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">❓</span>
            <div>
              <div class="cs-board-name">FAQ 자주하는 질문</div>
              <div class="cs-board-desc">클릭하면 답변이 펼쳐집니다</div>
            </div>
          </div>
          <a href="<?php echo htmlspecialchars($_faq_url, ENT_QUOTES); ?>" class="board-more">더보기 →</a>
        </div>
        <div class="faq-list">
<?php if (empty($_faq_rows)) { ?>
          <div class="faq-item" style="text-align:center;color:#999;font-size:13px;">등록된 FAQ가 없습니다</div>
<?php } else {
  foreach ($_faq_rows as $_fi => $_fq) {
    $_q = get_text($_fq['fa_subject']);
    $_a = get_text($_fq['fa_content']);
    $_open = ($_fi === 0) ? ' open' : '';
?>
          <div class="faq-item<?php echo $_open; ?>">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text"><?php echo htmlspecialchars($_q, ENT_QUOTES); ?></div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer"><?php echo htmlspecialchars($_a, ENT_QUOTES); ?></div>
          </div>
<?php } } ?>
        </div>
      </div>

    </div>

    <!-- 광고문의 & 일반문의 (히어로배너와 동일 가로폭) -->
    <div class="cs-qna-full">
      <div id="qna-section" class="cs-board-card bh-qna">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">💬</span>
            <div>
              <div class="cs-board-name">광고문의 &amp; 일반문의</div>
              <div class="cs-board-desc">궁금하신 점을 남겨주세요</div>
            </div>
          </div>
          <div style="display:flex;gap:6px;">
            <a href="<?php echo htmlspecialchars($_ad_inq_url, ENT_QUOTES); ?>" class="board-more">더보기</a>
            <a href="<?php echo htmlspecialchars($_ad_inq_write, ENT_QUOTES); ?>" class="board-write-btn">✏️ 문의하기</a>
          </div>
        </div>
        <div class="cs-post-list">
<?php if (empty($_ad_rows)) { ?>
          <div class="cs-post-item" style="justify-content:center;color:#999;font-size:13px;">등록된 문의가 없습니다</div>
<?php } else {
  foreach ($_ad_rows as $_ai => $_ar) {
    $_subj = get_text($_ar['wr_subject']);
    $_subj_short = mb_strlen($_subj, 'UTF-8') > 25 ? mb_substr($_subj, 0, 25, 'UTF-8').'…' : $_subj;
    $_date = substr($_ar['wr_datetime'], 0, 10);
    $_has_reply = (int)$_ar['wr_comment'] > 0;
    $_is_new = $_date >= date('Y-m-d', strtotime('-3 days'));
    $_badge = $_is_new ? 'pb-new' : ($_has_reply ? 'pb-answer' : 'pb-wait');
    $_badge_txt = $_is_new ? 'NEW' : ($_has_reply ? '답변완료' : '대기중');
    $_link = $_burl('ad_inquiry', $_ar['wr_id']);
?>
          <div class="cs-post-item">
            <span class="post-badge <?php echo $_badge; ?>"><?php echo $_badge_txt; ?></span>
            <a href="<?php echo htmlspecialchars($_link, ENT_QUOTES); ?>" class="post-title"><?php echo htmlspecialchars($_subj_short, ENT_QUOTES); ?></a>
            <span class="post-meta"><?php echo $_date; ?></span>
          </div>
<?php } } ?>
        </div>
      </div>
    </div>
