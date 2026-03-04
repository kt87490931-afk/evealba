<?php
/**
 * 이브알바 메인 영역
 * - DB 연동: ongoing 광고를 유형별로 조회하여 표시
 * - DB에 건이 없으면 기존 더미 HTML 유지
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH.'/extend/jobs_list_helper.php');
}

$_idx_udae   = function_exists('get_jobs_by_type') ? get_jobs_by_type('우대', 0) : array();
$_idx_premium = function_exists('get_jobs_by_type') ? get_jobs_by_type('프리미엄', 0) : array();
$_idx_special = function_exists('get_jobs_by_type') ? get_jobs_by_type('스페셜', 0) : array();
$_idx_urgent  = function_exists('get_jobs_by_type') ? get_jobs_by_type('급구', 5) : array();
$_idx_recomm  = function_exists('get_jobs_by_type') ? get_jobs_by_type('추천', 10) : array();
// 모바일 추천업소: PC 플로팅배너와 동일 소스 (g5_special_banner)
$_idx_recommend = array();
$_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';
$_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_sb_check = sql_query("SHOW TABLES LIKE '{$_sb_table}'");
if ($_sb_check && sql_num_rows($_sb_check) > 0) {
    $_sb_res = sql_query("SELECT jr.*
        FROM {$_sb_table} sb
        LEFT JOIN {$_jr_table} jr ON sb.sb_jr_id = jr.jr_id
        WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
        ORDER BY sb.sb_position ASC LIMIT 6");
    while ($_sb_r = sql_fetch_array($_sb_res)) {
        if (!empty($_sb_r['jr_id'])) $_idx_recommend[] = $_sb_r;
    }
}
?>
<!-- 빠른 통계 (데스크톱) -->
<div class="quick-stats">
  <div class="stat-card">
    <div class="stat-icon">💼</div>
    <div class="stat-label">오늘 채용공고</div>
    <div class="stat-value">3,427</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">📄</div>
    <div class="stat-label">등록 이력서</div>
    <div class="stat-value">12,841</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🏢</div>
    <div class="stat-label">가입 업소</div>
    <div class="stat-value">8,920</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">👩</div>
    <div class="stat-label">오늘 접속자</div>
    <div class="stat-value">24,153</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">✅</div>
    <div class="stat-label">오늘 매칭</div>
    <div class="stat-value">1,203</div>
  </div>
</div>

<!-- 빠른 메뉴 (모바일) -->
<?php $_qm_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : ''; ?>
<div class="mobile-quick-menu">
  <a href="<?php echo $_qm_base; ?>/jobs_register.php" class="mqm-btn"><span class="mqm-icon">📋</span><span class="mqm-label">채용공고 등록</span></a>
  <a href="<?php echo $_qm_base; ?>/resume_register.php" class="mqm-btn"><span class="mqm-icon">👩</span><span class="mqm-label">이력서 등록</span></a>
  <a href="<?php echo $_qm_base; ?>/jobs.php" class="mqm-btn"><span class="mqm-icon">📍</span><span class="mqm-label">지역별 채용</span></a>
  <a href="<?php echo $_qm_base; ?>/sudabang.php" class="mqm-btn"><span class="mqm-icon">💬</span><span class="mqm-label">수다방</span></a>
  <a href="javascript:void(0);" class="mqm-btn" onclick="var u='<?php echo G5_PLUGIN_URL; ?>/chat/eve_chat_frame.php';window.open(u,'eveChatPopup','width='+Math.min(420,screen.availWidth)+',height='+Math.min(720,screen.availHeight)+',scrollbars=no,resizable=yes');"><span class="mqm-icon">💬</span><span class="mqm-label">채팅</span></a>
  <a href="<?php echo G5_BBS_URL; ?>/memo.php" class="mqm-btn"><span class="mqm-icon">📩</span><span class="mqm-label">쪽지</span></a>
</div>

<!-- 공지 -->
<div class="notice-bar">
  <span class="notice-label">📢 공지</span>
  <div class="notice-text">
    <a href="#">[공지] 이브알바 신규 서비스 오픈 이벤트 안내 · 채용공고 등록 시 프리미엄 무료 업그레이드 혜택!</a>
  </div>
</div>

<!-- 모바일 전용 추천업소 (PC 플로팅배너와 동일 DB 소스, PC에서는 숨김) -->
<?php if (!function_exists('render_premium_card')) { @include_once(G5_PATH . '/extend/jobs_list_helper.php'); } ?>
<div class="mobile-recommend">
  <div class="section-header">
    <h2 class="section-title">💎 추천업소</h2>
  </div>
  <div class="mobile-recommend-grid">
<?php if (!empty($_idx_recommend) && function_exists('render_premium_card')) {
  foreach ($_idx_recommend as $_rec) { render_premium_card($_rec, 'mobile-rec-card'); }
} else { ?>
    <div class="mobile-rec-card mobile-rec-empty">
      <div class="mobile-rec-info" style="padding:20px;text-align:center;color:#999;font-size:13px;">등록된 추천업소가 없습니다.</div>
    </div>
<?php } ?>
  </div>
</div>

<!-- 우대채용정보 -->
<div class="section-wrap">
  <div class="section-header">
    <h2 class="section-title">우대채용정보</h2>
  </div>
  <div class="featured-grid">
<?php if (!empty($_idx_udae)) { foreach ($_idx_udae as $_u) { render_job_card($_u); } } else { ?>
    <div class="job-card">
      <div class="job-card-banner g1"><span>👑 강남 하이퍼블릭<br>아우라</span></div>
      <div class="hot-badge">HOT</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">경기</span>안양시 룸싸롱</div>
        <div class="job-desc">♥안양하이퍼TC16♥안양1등 이.</div>
        <div class="job-card-footer">
          <span class="job-wage">160,000원</span>
          <span class="job-badge"><span class="crown-gold">👑</span>24회 1170일</span>
        </div>
      </div>
    </div>
    <div class="job-card">
      <div class="job-card-banner g2"><span>💜 부천 하이퍼블릭<br>메쎄</span></div>
      <div class="hot-badge">HOT</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">경기</span>부천시 룸싸롱</div>
        <div class="job-desc">1등·패츠X최고조건·손님많고객.</div>
        <div class="job-card-footer">
          <span class="job-wage">150,000원</span>
          <span class="job-badge"><span class="crown-gold">👑</span>17회 1290일</span>
        </div>
      </div>
    </div>
    <div class="job-card">
      <div class="job-card-banner g3"><span>❤ 파주최고TC<br>REINA</span></div>
      <div class="hot-badge">HOT</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">경기</span>파주시 노래주점</div>
        <div class="job-desc">●●● 퍼블릭 1시간 10만원 ●●●</div>
        <div class="job-card-footer">
          <span class="job-wage">100,000원</span>
          <span class="job-badge"><span class="crown-silver">🥈</span>1회 30일</span>
        </div>
      </div>
    </div>
    <div class="job-card">
      <div class="job-card-banner g4"><span>💎 화류지옥<br>서울</span></div>
      <div class="new-badge">NEW</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">서울</span>기타</div>
        <div class="job-desc">♥최고패이♥화류지옥♥</div>
        <div class="job-card-footer">
          <span class="job-wage">500,000원</span>
          <span class="job-badge"><span class="crown-gold">👑</span>114회 3420일</span>
        </div>
      </div>
    </div>
<?php } ?>
  </div>
</div>

<!-- 프리미엄채용정보 -->
<div class="section-wrap">
  <div class="section-header">
    <h2 class="section-title">프리미엄채용정보</h2>
  </div>
<?php if (!empty($_idx_premium)) { ?>
  <div class="premium-grid">
    <?php foreach ($_idx_premium as $_p) { render_premium_card($_p); } ?>
  </div>
<?php } else { include_once dirname(__FILE__).'/inc/ads_premium.php'; } ?>
</div>

<!-- 커뮤니티 + 인재정보 -->
<?php
$_bbs = G5_BBS_URL;
$_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$_pfx = G5_TABLE_PREFIX;

$_tab_data = array(
    'best'   => array('label'=>'베스트글',     'badge'=>'<span class="comm-badge badge-best">BEST</span>', 'items'=>array()),
    'night'  => array('label'=>'밤문화이야기', 'badge'=>'<span class="comm-badge badge-night">🌙</span>', 'items'=>array()),
    'couple' => array('label'=>'단짝찾기',     'badge'=>'<span class="comm-badge badge-new">💑</span>',  'items'=>array()),
    'law'    => array('label'=>'법률상담',     'badge'=>'<span class="comm-badge badge-new">⚖️</span>', 'items'=>array()),
);

foreach (array('night','couple','law') as $_cb) {
    $_cb_ok = @sql_query("SHOW TABLES LIKE '{$_pfx}write_{$_cb}'", false);
    if (!$_cb_ok || !@sql_num_rows($_cb_ok)) continue;
    $_cb_q = sql_query("SELECT wr_id, wr_subject, wr_datetime, wr_good FROM {$_pfx}write_{$_cb} WHERE wr_is_comment = 0 ORDER BY wr_datetime DESC LIMIT 5");
    while ($_rw = sql_fetch_array($_cb_q)) {
        $subj = get_text($_rw['wr_subject'], 1);
        $subj_short = mb_strlen($subj,'UTF-8') > 35 ? mb_substr($subj,0,35,'UTF-8').'…' : $subj;
        $item = array(
            'subject' => $subj_short,
            'url' => $_bbs.'/board.php?bo_table='.$_cb.'&wr_id='.(int)$_rw['wr_id'],
            'time' => substr($_rw['wr_datetime'], 5, 11),
            'good' => (int)$_rw['wr_good'],
        );
        $_tab_data[$_cb]['items'][] = $item;
        if ($item['good'] >= 10) {
            $_tab_data['best']['items'][] = $item;
        }
    }
}
if (empty($_tab_data['best']['items'])) {
    $all = array_merge($_tab_data['night']['items'], $_tab_data['couple']['items'], $_tab_data['law']['items']);
    usort($all, function($a,$b){ return strcmp($b['time'],$a['time']); });
    $_tab_data['best']['items'] = array_slice($all, 0, 5);
}

$_talent_rows = array();
$_tl_check = @sql_query("SHOW TABLES LIKE 'g5_resume'", false);
if ($_tl_check && @sql_num_rows($_tl_check)) {
    $_tl_q = sql_query("SELECT rs_id, rs_nick, rs_age, rs_gender, rs_title, rs_salary_type, rs_salary_amt, rs_datetime FROM g5_resume WHERE rs_status='active' ORDER BY rs_datetime DESC LIMIT 6");
    while ($_tl = sql_fetch_array($_tl_q)) { $_talent_rows[] = $_tl; }
}
$_tab_keys = array_keys($_tab_data);
?>
<div class="community-resume-row">
  <div class="tab-section">
    <div class="tab-header">
<?php foreach ($_tab_keys as $_ti => $_tk) { ?>
      <button type="button" class="tab-btn<?php echo $_ti===0?' active':''; ?>" onclick="switchCommTab(this,<?php echo $_ti; ?>)"><?php echo $_tab_data[$_tk]['label']; ?></button>
<?php } ?>
    </div>
<?php foreach ($_tab_keys as $_ti => $_tk) { $_items = $_tab_data[$_tk]['items']; $_badge = $_tab_data[$_tk]['badge']; ?>
    <div class="tab-content" id="commTab<?php echo $_ti; ?>" style="<?php echo $_ti>0?'display:none;':''; ?>">
<?php if (empty($_items)) { ?>
      <div class="community-item" style="justify-content:center;color:#aaa;font-size:13px;">게시글이 없습니다</div>
<?php } else { foreach ($_items as $_ci) { ?>
      <a href="<?php echo $_ci['url']; ?>" class="community-item" style="text-decoration:none;color:inherit;">
        <?php echo $_badge; ?>
        <span class="comm-title"><?php echo $_ci['subject']; ?></span>
        <span class="comm-time"><?php echo $_ci['time']; ?></span>
      </a>
<?php } } ?>
    </div>
<?php } ?>
  </div>
  <div class="resume-table">
    <table>
      <thead>
        <tr>
          <th>이름</th>
          <th>나이/성별</th>
          <th>제목</th>
          <th>희망급여</th>
          <th>등록일</th>
        </tr>
      </thead>
      <tbody>
<?php if (empty($_talent_rows)) { ?>
        <tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">등록된 이력서가 없습니다</td></tr>
<?php } else { foreach ($_talent_rows as $_tl) {
    $_tl_name = mb_substr($_tl['rs_nick'], 0, 1, 'UTF-8') . '○○';
    $_tl_age = (int)$_tl['rs_age'];
    $_tl_gender = $_tl['rs_gender'] ? mb_substr($_tl['rs_gender'], 0, 1, 'UTF-8') : '';
    $_tl_title = mb_strlen($_tl['rs_title'], 'UTF-8') > 20 ? mb_substr($_tl['rs_title'], 0, 20, 'UTF-8').'…' : $_tl['rs_title'];
    $_tl_sal = '';
    if ((int)$_tl['rs_salary_amt'] > 0) { $_tl_sal = number_format((int)$_tl['rs_salary_amt']).'원'; $_tl_wage_cls = 'wage-fixed'; }
    else { $_tl_sal = '면접협의'; $_tl_wage_cls = 'wage-neg'; }
    $_tl_date = substr($_tl['rs_datetime'], 5, 5);
    $_tl_url = $_base_url.'/talent_view.php?rs_id='.(int)$_tl['rs_id'];
?>
        <tr onclick="location.href='<?php echo $_tl_url; ?>'" style="cursor:pointer;">
          <td class="resume-name"><?php echo htmlspecialchars($_tl_name); ?></td>
          <td><?php echo $_tl_age; ?>/<?php echo $_tl_gender; ?></td>
          <td class="resume-title"><a href="<?php echo $_tl_url; ?>"><?php echo htmlspecialchars($_tl_title); ?></a></td>
          <td><span class="wage-tag <?php echo $_tl_wage_cls; ?>"><?php echo $_tl_sal; ?></span></td>
          <td><?php echo $_tl_date; ?></td>
        </tr>
<?php } } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 스페셜채용정보 -->
<div class="section-wrap">
  <div class="section-header">
    <h2 class="section-title">스페셜채용정보</h2>
  </div>
<?php if (!empty($_idx_special)) { ?>
  <div class="special-grid">
    <?php foreach ($_idx_special as $_s) { render_premium_card($_s, 'special-card'); } ?>
  </div>
<?php } else { include_once dirname(__FILE__).'/inc/ads_special.php'; } ?>
</div>

<!-- 급구채용 + 추천채용 -->
<div class="urgency-recommend-row">
  <div>
    <div class="section-header">
      <h2 class="section-title" style="font-size:16px">급구채용</h2>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php?ad_type='.rawurlencode('급구') : '/jobs.php?ad_type='.rawurlencode('급구'); ?>" class="section-more">더보기 →</a>
    </div>
    <div class="urgency-list">
<?php if (!empty($_idx_urgent)) { foreach ($_idx_urgent as $_ug) { render_urgency_card($_ug); } } else { ?>
      <div class="urgency-card">
        <div class="urgency-name">♥화류지옥♥</div>
        <div class="urgency-area">서울</div>
        <div class="urgency-desc">♥최고패이♥화류지옥♥</div>
        <div class="urgency-wage">500,000원 <span>· 114회 3420일</span></div>
      </div>
      <div class="urgency-card">
        <div class="urgency-name">강남짬오❤이태곤대표</div>
        <div class="urgency-area">서울 강남구</div>
        <div class="urgency-desc">♥순수테이블♥ 2시간40분!</div>
        <div class="urgency-wage">면접 후 협의 <span>· 42회 1260일</span></div>
      </div>
      <div class="urgency-card">
        <div class="urgency-name">타임</div>
        <div class="urgency-area">경기 광명시 · 노래주점</div>
        <div class="urgency-desc">♥철산1등 타가게/고정/반고정.</div>
        <div class="urgency-wage">130,000원 <span>· 1회 60일</span></div>
      </div>
<?php } ?>
    </div>
  </div>
  <div>
    <div class="section-header">
      <h2 class="section-title" style="font-size:16px">추천채용</h2>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php?ad_type='.rawurlencode('추천') : '/jobs.php?ad_type='.rawurlencode('추천'); ?>" class="section-more">더보기 →</a>
    </div>
    <div class="recommend-list">
<?php if (!empty($_idx_recomm)) { foreach ($_idx_recomm as $_rc) { render_recommend_card($_rc); } } else { ?>
      <div class="recommend-card">
        <div>
          <div class="rec-name">♥파주최고TC♥ <span class="rec-area">경기 파주시</span></div>
          <div class="rec-desc">●●● 퍼블릭 1시간 10만원 ●●● 파주 최고TC</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">100,000원</div>
          <div class="rec-meta">노래주점 · 1회 30일</div>
        </div>
      </div>
      <div class="recommend-card">
        <div>
          <div class="rec-name">구구단 신제니 <span class="rec-area">서울</span></div>
          <div class="rec-desc">구구단 신제니 ♡ 정동 여자 마담 ♡ 하이퍼캠오!</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">150,000원</div>
          <div class="rec-meta">룸싸롱 · 1회 90일</div>
        </div>
      </div>
      <div class="recommend-card">
        <div>
          <div class="rec-name">동탄하퍼대표 <span class="rec-area">경기</span></div>
          <div class="rec-desc">자유복장하이퍼♥TC12♥60분♥당일지급</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">3,000,000원</div>
          <div class="rec-meta">룸싸롱 · 51회 1530일</div>
        </div>
      </div>
      <div class="recommend-card">
        <div>
          <div class="rec-name">아우라 하이퍼블릭 <span class="rec-area">경기</span></div>
          <div class="rec-desc">♥수원1번하이퍼블릭♥ 아우라 대표가 환영합.</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">면접 후 협의</div>
          <div class="rec-meta">노래주점 · 3회 540일</div>
        </div>
      </div>
<?php } ?>
    </div>
  </div>
</div>
<script>
function switchCommTab(btn, idx) {
  btn.closest('.tab-section').querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  btn.closest('.tab-section').querySelectorAll('.tab-content').forEach(function(c, i){ c.style.display = i === idx ? '' : 'none'; });
}
</script>
<script src="<?php echo G5_THEME_URL; ?>/js/lazy_anim.js?v=<?php echo G5_CSS_VER; ?>"></script>
