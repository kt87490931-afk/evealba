<?php
/**
 * 채용 상세 — 리뉴얼 UI (evealba_job_detail.html)
 * jobs_view_main.php 변수 설정 후 include
 */
if (!defined('_GNUBOARD_')) exit;

@include_once(G5_PATH . '/extend/jobs_list_helper.php');

$_jvr_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_jvr_jobs_url = $_jvr_base ? $_jvr_base . '/jobs.php' : '/jobs.php';
$_jvr_login_url = G5_BBS_URL . '/login.php';
$_jvr_memo_url = $_jvr_base ? $_jvr_base . '/memo_full.php' : '/memo_full.php';

if (!isset($amenity_arr)) {
    $amenity_arr = is_array($data['amenity'] ?? null)
        ? array_map('trim', $data['amenity'])
        : (trim($amenity ?? '') ? array_map('trim', explode(',', $amenity)) : array());
}
if (!isset($mbti_arr)) {
    $mbti_arr = is_array($data['mbti_prefer'] ?? null)
        ? array_map('trim', $data['mbti_prefer'])
        : (trim($mbti ?? '') ? array_map('trim', explode(',', $mbti)) : array());
}
if (!isset($keyword_arr)) {
    $keyword_arr = is_array($data['keyword'] ?? null)
        ? array_map('trim', $data['keyword'])
        : (trim($keyword ?? '') ? array_map('trim', explode(',', $keyword)) : array());
}

$_jvr_display = $nick ?: $comp ?: '업소';
$_jvr_page_title = $title ?: ($_jvr_display . ' 채용');
$_jvr_job1 = isset($job1) ? trim($job1) : (isset($data['job_job1']) ? trim($data['job_job1']) : '');
$_jvr_reg1_id = isset($reg1_id) ? trim($reg1_id) : (isset($data['job_work_region_1']) ? trim($data['job_work_region_1']) : '');
$_jvr_reg1_name = '';
if ($_jvr_reg1_id && isset($_reg_name_map[(int)$_jvr_reg1_id])) {
    $_jvr_reg1_name = $_reg_name_map[(int)$_jvr_reg1_id];
} elseif ($_jvr_reg1_id && function_exists('_jlh_region_name')) {
    $_jvr_reg1_name = _jlh_region_name($_jvr_reg1_id);
}

$_jvr_ad_labels = isset($row['jr_ad_labels']) ? trim($row['jr_ad_labels']) : '';
$_jvr_grade = function_exists('_jlh_grade_info') ? _jlh_grade_info($_jvr_ad_labels) : array('label' => '', 'badge_class' => '');
$_jvr_grade_icons = array(
    'VIP' => '👑', '우대' => '⭐', '프리미엄' => '💎', '스페셜' => '✨', '급구' => '🔥', '추천' => '💖',
);
$_jvr_grade_icon = isset($_jvr_grade_icons[$_jvr_grade['label']]) ? $_jvr_grade_icons[$_jvr_grade['label']] : '💎';

$_jvr_sal_amt_num = (int)preg_replace('/[^0-9]/', '', (string)$salary_amt);
$_jvr_sal_display = function_exists('_jlh_format_salary_mockup')
    ? _jlh_format_salary_mockup($salary_type, $_jvr_sal_amt_num)
    : ($salary_disp ?: '협의');

$_jvr_sal_sub_parts = array();
foreach ($amenity_arr as $_jvr_am) {
    if (in_array($_jvr_am, array('당일지급', '선불가능', '만근비지원', '출퇴근지원'), true)) {
        $_jvr_sal_sub_parts[] = $_jvr_am;
    }
}
$_jvr_sal_sub = implode(' · ', array_slice($_jvr_sal_sub_parts, 0, 3));

$_jvr_scraped = false;
$_jvr_scrap_count = 0;
$_jvr_tb_scrap = @sql_query("SHOW TABLES LIKE 'g5_jobs_scrap'", false);
if ($_jvr_tb_scrap && @sql_num_rows($_jvr_tb_scrap)) {
    $_jvr_sc = @sql_fetch("SELECT COUNT(*) AS cnt FROM g5_jobs_scrap WHERE jr_id = '" . (int)$jr_id . "'");
    $_jvr_scrap_count = (int)($_jvr_sc['cnt'] ?? 0);
    if ($is_member && !empty($member['mb_id'])) {
        $_jvr_chk = @sql_fetch("SELECT 1 FROM g5_jobs_scrap WHERE jr_id = '" . (int)$jr_id . "' AND mb_id = '" . addslashes($member['mb_id']) . "' LIMIT 1");
        $_jvr_scraped = (bool)$_jvr_chk;
    }
}

$_jvr_images = isset($data['jr_images']) && is_array($data['jr_images']) ? $data['jr_images'] : array();
$_jvr_img_slots = array_pad(array_slice($_jvr_images, 0, 5), 5, array('url' => '', 'caption' => ''));

$_jvr_prev = @sql_fetch("SELECT jr_id, jr_title, jr_nickname, jr_company, jr_data FROM g5_jobs_register WHERE jr_status = 'ongoing' AND jr_id < '" . (int)$jr_id . "' ORDER BY jr_id DESC LIMIT 1", false);
$_jvr_next = @sql_fetch("SELECT jr_id, jr_title, jr_nickname, jr_company, jr_data FROM g5_jobs_register WHERE jr_status = 'ongoing' AND jr_id > '" . (int)$jr_id . "' ORDER BY jr_id ASC LIMIT 1", false);

$_jvr_mbti_all = array(
    'ENFJ', 'ENFP', 'ESFJ', 'ESFP', 'ENTJ', 'ENTP', 'ESTJ', 'ESTP',
    'INFJ', 'INFP', 'ISFJ', 'ISFP', 'INTJ', 'INTP', 'ISTJ', 'ISTP',
);
$_jvr_mbti_active = array_flip(array_map('strtoupper', array_filter($mbti_arr)));

$_jvr_contact_disp = '로그인 후 확인';
$_jvr_contact_href = '';
$_jvr_contact_tel = '';
if ($is_member && !empty($contact)) {
    $_jvr_contact_disp = htmlspecialchars($contact);
    $_jvr_contact_tel = preg_replace('/[^0-9+]/', '', $contact);
    if ($_jvr_contact_tel) {
        $_jvr_contact_href = 'tel:' . $_jvr_contact_tel;
    }
}

$_jvr_scrap_url = isset($jobs_scrap_url) ? $jobs_scrap_url : ($_jvr_base ? $_jvr_base . '/jobs_scrap.php' : '/jobs_scrap.php');
?>
<div class="breadcrumb">
  <a href="<?php echo G5_URL; ?>">🏠 메인</a>
  <span class="bc-sep">›</span>
  <a href="<?php echo htmlspecialchars($_jvr_jobs_url); ?>">채용정보</a>
<?php if ($_jvr_reg1_name) { ?>
  <span class="bc-sep">›</span>
  <a href="<?php echo htmlspecialchars($_jvr_jobs_url . '?er_id=' . (int)$_jvr_reg1_id); ?>"><?php echo htmlspecialchars($_jvr_reg1_name); ?></a>
<?php } ?>
  <span class="bc-sep">›</span>
  <span class="bc-cur"><?php echo htmlspecialchars(mb_substr($_jvr_display, 0, 24, 'UTF-8')); ?></span>
</div>

<div class="img-stack" id="imgStack">
<?php for ($_jvr_si = 0; $_jvr_si < 5; $_jvr_si++) {
    $_jvr_img = $_jvr_img_slots[$_jvr_si];
    $_jvr_img_url = isset($_jvr_img['url']) ? trim($_jvr_img['url']) : '';
    $_jvr_slot_num = $_jvr_si + 1;
    if ($_jvr_img_url) { ?>
  <div class="img-slot">
    <img src="<?php echo htmlspecialchars($_jvr_img_url); ?>" alt="업소 이미지 <?php echo $_jvr_slot_num; ?>">
    <span class="img-num"><?php echo $_jvr_slot_num; ?> / 5</span>
  </div>
<?php } else { ?>
  <div class="img-slot empty">
    <span class="empty-icon">📷</span>
    <span>이미지 <?php echo $_jvr_slot_num; ?></span>
  </div>
<?php }
} ?>
</div>

<div class="job-info-wrap">

  <div class="job-header">
    <div class="job-header-left">
<?php if (!empty($_jvr_grade['label'])) { ?>
      <div class="job-grade <?php echo htmlspecialchars($_jvr_grade['badge_class']); ?>"><?php echo $_jvr_grade_icon; ?> <?php echo htmlspecialchars($_jvr_grade['label']); ?></div>
<?php } ?>
      <h1 class="job-title"><?php echo htmlspecialchars($_jvr_page_title); ?></h1>
      <div class="job-shop">
        <span class="shop-name"><?php echo htmlspecialchars($_jvr_display); ?></span>
<?php if ($region) { ?>
        <span>·</span>
        <span><?php echo htmlspecialchars($region); ?></span>
<?php }
      if ($jobtype) {
          $_jvr_jt_parts = array_map('trim', explode('/', $jobtype));
          foreach ($_jvr_jt_parts as $_jvr_jtp) {
              if (!$_jvr_jtp) continue; ?>
        <span>·</span>
        <span><?php echo htmlspecialchars($_jvr_jtp); ?></span>
<?php     }
      } ?>
      </div>
    </div>
    <button type="button" class="btn-like<?php echo $_jvr_scraped ? ' liked' : ''; ?>" id="btn-job-like" onclick="doJobScrap(<?php echo (int)$jr_id; ?>)" title="찜하기" aria-pressed="<?php echo $_jvr_scraped ? 'true' : 'false'; ?>"><?php echo $_jvr_scraped ? '❤️' : '🤍'; ?></button>
  </div>

  <div class="job-chips">
<?php if ($_jvr_sal_display) { ?>
    <span class="chip highlight">💰 <?php echo htmlspecialchars($_jvr_sal_display); ?></span>
<?php }
if ($region) { ?>
    <span class="chip">📍 <?php echo htmlspecialchars(mb_substr($region, 0, 20, 'UTF-8')); ?></span>
<?php }
foreach (array_slice(array_unique(array_merge($keyword_arr, $amenity_arr)), 0, 8) as $_jvr_chip) {
    if (!$_jvr_chip) continue; ?>
    <span class="chip"><?php echo htmlspecialchars($_jvr_chip); ?></span>
<?php } ?>
  </div>

  <div class="salary-row">
    <div>
      <div class="s-label">급여조건</div>
      <div class="s-value"><?php echo htmlspecialchars($_jvr_sal_display); ?></div>
<?php if ($_jvr_sal_sub) { ?>
      <div class="s-sub"><?php echo htmlspecialchars($_jvr_sal_sub); ?></div>
<?php } ?>
    </div>
  </div>

  <div class="divider-thick"></div>

  <div style="padding-top:20px;" class="ai-section">

    <div class="ai-section-label">
      <span class="ai-tag">✨ AI</span>
      업소 소개
    </div>

<?php if (!empty($ai_intro)) { ?>
    <div class="ai-body">
      <p><?php echo nl2br(htmlspecialchars($ai_intro)); ?></p>
    </div>
    <div class="divider"></div>
<?php } ?>

<?php if (!empty($ai_location)) { ?>
    <div class="ai-section-block">
      <div class="ai-section-title">📍 업소 위치</div>
      <div class="ai-body">
        <p><?php echo nl2br(htmlspecialchars($ai_location)); ?></p>
      </div>
    </div>
    <div class="divider"></div>
<?php } ?>

<?php if (!empty($ai_env)) { ?>
    <div class="ai-section-block">
      <div class="ai-section-title">🏢 근무환경</div>
      <div class="ai-body">
        <p><?php echo nl2br(htmlspecialchars($ai_env)); ?></p>
      </div>
    </div>
    <div class="divider"></div>
<?php } ?>

<?php if (!empty($amenity_arr)) { ?>
    <div class="ai-section-block">
      <div class="ai-section-title">✅ 편의사항</div>
      <div class="benefit-tags">
<?php foreach ($amenity_arr as $_jvr_ben) {
    if (!$_jvr_ben) continue; ?>
        <span class="benefit-tag"><?php echo htmlspecialchars($_jvr_ben); ?></span>
<?php } ?>
      </div>
    </div>
    <div class="divider"></div>
<?php } ?>

    <div class="ai-section-block">
      <div class="ai-section-title">🧠 선호 MBTI</div>
      <div class="mbti-tags">
<?php foreach ($_jvr_mbti_all as $_jvr_mt) {
    $_jvr_mt_active = isset($_jvr_mbti_active[strtoupper($_jvr_mt)]); ?>
        <span class="mbti-tag<?php echo $_jvr_mt_active ? ' active' : ''; ?>"><?php echo $_jvr_mt; ?></span>
<?php } ?>
      </div>
<?php if (!empty($ai_mbti_comment_val)) { ?>
      <div class="ai-body" style="margin-top:12px;">
        <p><?php echo nl2br(htmlspecialchars($ai_mbti_comment_val)); ?></p>
      </div>
<?php } ?>
    </div>

    <div class="divider"></div>

<?php if (!empty($ai_welfare)) { ?>
    <div class="ai-section-block">
      <div class="ai-section-title">🎁 지원 혜택 및 복리후생</div>
      <div class="ai-body">
        <p><?php echo nl2br(htmlspecialchars($ai_welfare)); ?></p>
      </div>
    </div>
    <div class="divider"></div>
<?php } ?>

    <div class="ai-section-block">
      <div class="ai-section-title">📋 기본 정보</div>
      <table class="info-table">
        <tr><th>업소명</th><td><?php echo htmlspecialchars($_jvr_display); ?></td></tr>
        <tr><th>위치</th><td><?php echo htmlspecialchars($region ?: '—'); ?></td></tr>
        <tr><th>업종</th><td><?php echo htmlspecialchars($jobtype ?: '—'); ?></td></tr>
        <tr><th>급여</th><td><?php echo htmlspecialchars($_jvr_sal_display . ($_jvr_sal_sub ? ' (' . $_jvr_sal_sub . ')' : '')); ?></td></tr>
        <tr><th>근무형태</th><td><?php echo htmlspecialchars(isset($employ_type) ? $employ_type : '상시'); ?></td></tr>
        <tr><th>연락처</th><td><?php echo $_jvr_contact_disp; ?></td></tr>
      </table>
    </div>

    <div class="divider"></div>

    <div class="prev-next">
<?php
$_jvr_pn_title = function ($pn_row) {
    if (!$pn_row) return '';
    $pn_jd = is_string($pn_row['jr_data']) ? json_decode($pn_row['jr_data'], true) : (array)$pn_row['jr_data'];
    $pn_title = trim($pn_row['jr_title'] ?? '');
    if (!$pn_title && !empty($pn_jd['job_title'])) $pn_title = trim($pn_jd['job_title']);
    if (!$pn_title) $pn_title = trim($pn_row['jr_nickname'] ?: ($pn_row['jr_company'] ?: '채용정보'));
    return $pn_title;
};
$_jvr_prev_link = '';
$_jvr_next_link = '';
if ($_jvr_prev) {
    $_jvr_prev_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_jvr_prev) : $_jvr_base . '/jobs_view.php?jr_id=' . (int)$_jvr_prev['jr_id'];
}
if ($_jvr_next) {
    $_jvr_next_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_jvr_next) : $_jvr_base . '/jobs_view.php?jr_id=' . (int)$_jvr_next['jr_id'];
}
?>
<?php if ($_jvr_prev_link) { ?>
      <a class="prev-next-btn" href="<?php echo htmlspecialchars($_jvr_prev_link); ?>">
        <span class="arrow">‹</span>
        <div>
          <span class="pn-label">이전 공고</span>
          <span class="pn-title"><?php echo htmlspecialchars(mb_substr($_jvr_pn_title($_jvr_prev), 0, 30, 'UTF-8')); ?></span>
        </div>
      </a>
<?php } else { ?>
      <span class="prev-next-btn" style="opacity:.45;cursor:default;">
        <span class="arrow">‹</span>
        <div>
          <span class="pn-label">이전 공고</span>
          <span class="pn-title">없음</span>
        </div>
      </span>
<?php }
if ($_jvr_next_link) { ?>
      <a class="prev-next-btn next" href="<?php echo htmlspecialchars($_jvr_next_link); ?>">
        <div>
          <span class="pn-label">다음 공고</span>
          <span class="pn-title"><?php echo htmlspecialchars(mb_substr($_jvr_pn_title($_jvr_next), 0, 30, 'UTF-8')); ?></span>
        </div>
        <span class="arrow">›</span>
      </a>
<?php } else { ?>
      <span class="prev-next-btn next" style="opacity:.45;cursor:default;">
        <div>
          <span class="pn-label">다음 공고</span>
          <span class="pn-title">없음</span>
        </div>
        <span class="arrow">›</span>
      </span>
<?php } ?>
    </div>

  </div>
</div>

<div class="cta-spacer"></div>

<div class="cta-fixed">
  <button type="button" class="btn-contact chat" onclick="if(typeof toggleEveChat==='function'){toggleEveChat();return false;}location.href='<?php echo htmlspecialchars($_jvr_memo_url, ENT_QUOTES); ?>';">💬 1:1 채팅</button>
<?php if ($is_member && $_jvr_contact_href) { ?>
  <a class="btn-contact" href="<?php echo htmlspecialchars($_jvr_contact_href); ?>">📞 연락하기</a>
<?php } elseif ($is_member && !empty($contact)) { ?>
  <span class="btn-contact" style="cursor:default;">📞 <?php echo htmlspecialchars($contact); ?></span>
<?php } else { ?>
  <a class="btn-contact" href="<?php echo htmlspecialchars($_jvr_login_url); ?>">📞 로그인 후 확인</a>
<?php } ?>
  <button type="button" class="btn-like<?php echo $_jvr_scraped ? ' liked' : ''; ?>" id="btn-job-scrap-cta" onclick="doJobScrap(<?php echo (int)$jr_id; ?>)" title="찜하기" aria-pressed="<?php echo $_jvr_scraped ? 'true' : 'false'; ?>"><?php echo $_jvr_scraped ? '❤️' : '🤍'; ?></button>
</div>

<?php include G5_THEME_PATH . '/inc/renewal_footer_in_main.php'; ?>

<script>
(function(){
  window.toggleLike = function(btn) {
    if (!btn) return;
    var liked = btn.classList.toggle('liked');
    btn.textContent = liked ? '❤️' : '🤍';
    btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
  };

  window.doJobScrap = function(jid) {
    var hdr = document.getElementById('btn-job-like');
    var cta = document.getElementById('btn-job-scrap-cta');
    var btn = hdr || cta;
    if (!btn) return;
    <?php if (!$is_member) { ?>
    alert('로그인 후 스크랩할 수 있습니다.');
    location.href = '<?php echo addslashes($_jvr_login_url); ?>';
    return;
    <?php } ?>
    if (hdr) hdr.disabled = true;
    if (cta) cta.disabled = true;
    var fd = new FormData();
    fd.append('jr_id', jid);
    fd.append('action', btn.classList.contains('liked') ? 'remove' : 'add');
    fetch('<?php echo addslashes($_jvr_scrap_url); ?>', { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (hdr) hdr.disabled = false;
        if (cta) cta.disabled = false;
        if (res.ok) {
          var scraped = !!res.scraped;
          [hdr, cta].forEach(function(el) {
            if (!el) return;
            if (scraped) {
              el.classList.add('liked');
              el.textContent = '❤️';
              el.setAttribute('aria-pressed', 'true');
            } else {
              el.classList.remove('liked');
              el.textContent = '🤍';
              el.setAttribute('aria-pressed', 'false');
            }
          });
          if (res.msg) alert(res.msg);
        } else {
          alert(res.msg || '스크랩 처리에 실패했습니다.');
        }
      })
      .catch(function() {
        if (hdr) hdr.disabled = false;
        if (cta) cta.disabled = false;
        alert('스크랩 처리 중 오류가 발생했습니다.');
      });
  };
})();
</script>
