<?php if (!defined('_GNUBOARD_')) exit;

$jr_id = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0;
if (!$jr_id || !$is_member) {
    echo '<script>alert("잘못된 접근입니다."); history.back();</script>';
    return;
}

$jr_table = 'g5_jobs_register';
$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!sql_num_rows($tb_check)) {
    echo '<script>alert("데이터를 찾을 수 없습니다."); history.back();</script>';
    return;
}

$mb_id_esc = addslashes($member['mb_id']);
$row = sql_fetch("SELECT * FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    echo '<script>alert("권한이 없거나 데이터가 없습니다."); history.back();</script>';
    return;
}

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_ongoing_url = $jobs_base_url ? $jobs_base_url.'/jobs_ongoing.php' : '/jobs_ongoing.php';
$jobs_ai_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_ai_section_save.php' : '/jobs_ai_section_save.php';

$status = $row['jr_status'];
$payment_ok = !empty($row['jr_payment_confirmed']);
$status_label = ($status === 'ongoing') ? '진행중' : ($payment_ok ? '입금확인' : '입금대기중');
$status_class = ($status === 'ongoing') ? 'ongoing' : ($payment_ok ? 'payment-ok' : 'payment-wait');

$data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
$nick = isset($data['job_nickname']) ? trim($data['job_nickname']) : $row['jr_nickname'];
$comp = isset($data['job_company']) ? trim($data['job_company']) : $row['jr_company'];
$title = isset($data['job_title']) ? trim($data['job_title']) : $row['jr_title'];
$contact = isset($data['job_contact']) ? trim($data['job_contact']) : '';
$employ_type = isset($data['employ-type']) ? trim($data['employ-type']) : '고용';
$salary_type = isset($data['job_salary_type']) ? trim($data['job_salary_type']) : '';
$salary_amt = isset($data['job_salary_amt']) ? trim($data['job_salary_amt']) : '';
$salary_disp = $salary_type ? (($salary_type === '급여협의') ? '급여협의' : $salary_type . ($salary_amt ? ' ' . number_format((int)preg_replace('/[^0-9]/','',$salary_amt)) . '원' : '')) : '';
$region = '';
if (!empty($data['job_work_region_1'])) {
    $r1 = isset($data['job_work_region_1']) ? trim($data['job_work_region_1']) : '';
    $d1 = isset($data['job_work_region_detail_1']) ? trim($data['job_work_region_detail_1']) : '';
    $region = $r1 . ($d1 ? ' ' . $d1 : '');
}
$job1 = isset($data['job_job1']) ? trim($data['job_job1']) : '';
$job2 = isset($data['job_job2']) ? trim($data['job_job2']) : '';
$jobtype = ($job1 !== '' || $job2 !== '') ? trim(implode(' / ', array_filter(array($job1, $job2)))) : '';
$amenity = isset($data['amenity']) && is_array($data['amenity']) ? implode(', ', array_map('trim', $data['amenity'])) : (isset($data['amenity']) ? trim($data['amenity']) : '');
$keyword = isset($data['keyword']) && is_array($data['keyword']) ? implode(', ', array_map('trim', $data['keyword'])) : (isset($data['keyword']) ? trim($data['keyword']) : '');
$mbti = isset($data['mbti_prefer']) && is_array($data['mbti_prefer']) ? implode(', ', array_map('trim', $data['mbti_prefer'])) : '';
$sns_parts = array();
if (!empty($data['job_kakao'])) $sns_parts[] = '카카오: '.$data['job_kakao'];
if (!empty($data['job_line'])) $sns_parts[] = '라인: '.$data['job_line'];
if (!empty($data['job_telegram'])) $sns_parts[] = '텔레그램: '.$data['job_telegram'];
$sns_disp = implode(', ', $sns_parts);
$desc_location = isset($data['desc_location']) ? trim($data['desc_location']) : '';
$desc_env = isset($data['desc_env']) ? trim($data['desc_env']) : '';
$desc_benefit = isset($data['desc_benefit']) ? trim($data['desc_benefit']) : '';
$desc_qualify = isset($data['desc_qualify']) ? trim($data['desc_qualify']) : '';
$desc_extra = isset($data['desc_extra']) ? trim($data['desc_extra']) : '';
$ai_summary = isset($data['ai_content']) ? trim($data['ai_content']) : '';
$ai_intro = isset($data['ai_intro']) ? trim($data['ai_intro']) : '';
$ai_location = isset($data['ai_location']) ? trim($data['ai_location']) : '';
$ai_env = isset($data['ai_env']) ? trim($data['ai_env']) : '';
$ai_benefit = isset($data['ai_benefit']) ? trim($data['ai_benefit']) : '';
$ai_wrapup = isset($data['ai_wrapup']) ? trim($data['ai_wrapup']) : '';
$has_sections = !empty($ai_intro) || !empty($ai_location) || !empty($ai_env) || !empty($ai_benefit) || !empty($ai_wrapup);
$show_ai = ($status === 'ongoing' || $payment_ok) && ($ai_summary || $has_sections);
$title_employ = $title ? $title . ' · ' . $employ_type : $employ_type;
$amenity_arr = is_array($data['amenity'] ?? null) ? array_map('trim', $data['amenity']) : (trim($amenity ?? '') ? explode(',', $amenity) : array());
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<article id="bo_v" class="ev-view-wrap jobs-view-wrap" style="width:100%">
  <div class="view-wrap">
    <div class="view-head">
      <span class="view-head-icon">📄</span>
      <span class="view-head-title"><?php echo htmlspecialchars($row['jr_subject_display']); ?></span>
      <span class="view-head-sub"><?php echo $status_label; ?></span>
    </div>
    <div class="view-meta">
      <div class="view-meta-title-row">
        <span class="status-badge status-<?php echo $status_class; ?>"><?php echo $status_label; ?></span>
        <span class="vm-title"><?php echo htmlspecialchars($row['jr_subject_display']); ?></span>
      </div>
      <div class="view-meta-info">
        <div class="vm-info-item">
          <span class="vm-info-icon">✍️</span>
          <span>작성인</span>
          <span class="vm-info-val pink"><?php echo htmlspecialchars($nick ?: $comp ?: '-'); ?></span>
        </div>
        <div class="vm-info-item">
          <span class="vm-info-icon">📅</span>
          <span>등록일</span>
          <span class="vm-info-val"><?php echo date('Y-m-d', strtotime($row['jr_datetime'])); ?></span>
        </div>
      </div>
    </div>

    <!-- AI소개글 종합정리 -->
    <div class="ai-preview-card jobs-ai-preview jobs-register-page" id="jobs-ai-summary-card" style="margin:0 0 16px;width:100%;">
      <div class="ai-preview-header">
        <div class="ai-preview-header-left">
          <div class="ai-preview-avatar">🏢</div>
          <div>
            <div class="ai-preview-title">AI소개글 종합정리</div>
            <div class="ai-preview-subtitle">등록된 내용입니다</div>
          </div>
        </div>
        <div class="ai-preview-header-right">
          <span class="ai-preview-badge">AI 소개글 생성에 활용됩니다</span>
        </div>
      </div>
      <div class="ai-preview-body" id="jobsAiPreviewBody">
        <div class="aip-row"><div class="aip-label">🏢 닉네임 · 상호</div><div class="aip-value"><?php echo htmlspecialchars($nick ?: $comp ?: '—'); ?></div></div>
        <div class="aip-row"><div class="aip-label">📞 연락처</div><div class="aip-value"><?php echo htmlspecialchars($contact ?: '—'); ?></div></div>
        <div class="aip-row"><div class="aip-label">💬 SNS</div><div class="aip-value"><?php echo $sns_disp ? htmlspecialchars($sns_disp) : '—'; ?></div></div>
        <div class="aip-row"><div class="aip-label">📋 채용제목 · 고용형태</div><div class="aip-value"><?php echo htmlspecialchars($title_employ ?: '—'); ?></div></div>
        <div class="aip-row"><div class="aip-label">💰 급여조건</div><div class="aip-value"><?php echo htmlspecialchars($salary_disp ?: '—'); ?></div></div>
        <div class="aip-row"><div class="aip-label">📍 근무지역</div><div class="aip-value"><?php echo htmlspecialchars($region ?: '—'); ?></div></div>
        <div class="aip-row"><div class="aip-label">💼 업종/직종</div><div class="aip-value"><?php echo htmlspecialchars($jobtype ?: '—'); ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">✅ 편의사항</div><div class="aip-value"><?php echo $amenity ? htmlspecialchars($amenity) : '<span class="aip-empty">선택된 편의사항이 없습니다</span>'; ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">🏷️ 키워드</div><div class="aip-value"><?php echo $keyword ? htmlspecialchars($keyword) : '<span class="aip-empty">선택된 키워드가 없습니다</span>'; ?></div></div>
        <div class="aip-row"><div class="aip-label">🧠 선호 MBTI</div><div class="aip-value"><?php echo htmlspecialchars($mbti ?: '—'); ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">📍 업소 위치 및 업소 소개</div><div class="aip-value"><?php echo $desc_location ? nl2br(htmlspecialchars($desc_location)) : '—'; ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">🏭 근무환경</div><div class="aip-value"><?php echo $desc_env ? nl2br(htmlspecialchars($desc_env)) : '—'; ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">🎁 지원 혜택 및 복리후생</div><div class="aip-value"><?php echo $desc_benefit ? nl2br(htmlspecialchars($desc_benefit)) : '—'; ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">📋 지원 자격 및 우대사항</div><div class="aip-value"><?php echo $desc_qualify ? nl2br(htmlspecialchars($desc_qualify)) : '—'; ?></div></div>
        <div class="aip-row aip-row-tall"><div class="aip-label">📝 추가 상세설명</div><div class="aip-value"><?php echo $desc_extra ? nl2br(htmlspecialchars($desc_extra)) : '—'; ?></div></div>
        <div class="aip-footer">
          <div class="aip-footer-icon">🤖</div>
          <div class="aip-footer-text">위 정보를 기준으로 <strong>AI</strong>가 업소소개글을 자동 작성합니다.</div>
        </div>
      </div>
    </div>

    <?php if ($show_ai) { ?>
    <?php if ($has_sections) {
      $ai_sections = array(
        array('key' => 'ai_intro', 'label' => '인사말', 'val' => $ai_intro),
        array('key' => 'ai_location', 'label' => '업소 위치', 'val' => $ai_location),
        array('key' => 'ai_env', 'label' => '근무환경', 'val' => $ai_env),
        array('key' => 'ai_benefit', 'label' => '혜택·복리후생', 'val' => $ai_benefit),
        array('key' => 'ai_wrapup', 'label' => '언니의 약속', 'val' => $ai_wrapup),
      );
      foreach ($ai_sections as $sec) {
        if (empty($sec['val'])) continue;
    ?>
    <div class="jobs-ai-reply-block jobs-ai-section" data-section="<?php echo htmlspecialchars($sec['key']); ?>" data-jr-id="<?php echo (int)$jr_id; ?>">
      <div class="jobs-ai-reply-head">
        <span class="jobs-ai-reply-badge"><?php echo htmlspecialchars($sec['label']); ?></span>
      </div>
      <div class="jobs-ai-reply-body">
        <div class="jobs-ai-view-wrap">
          <div class="viewContent"><?php echo nl2br(htmlspecialchars($sec['val'])); ?></div>
          <div class="jobs-ai-reply-actions">
            <button type="button" class="btn-edit btn-edit-ai">✏️ 수정</button>
          </div>
        </div>
        <div class="jobs-ai-edit-wrap" style="display:none;">
          <textarea class="jobs-ai-edit-ta" rows="6"><?php echo htmlspecialchars($sec['val']); ?></textarea>
          <div class="jobs-ai-edit-actions">
            <button type="button" class="btn-save-ai">저장</button>
            <button type="button" class="btn-cancel-ai">취소</button>
          </div>
        </div>
      </div>
    </div>
    <?php }
    } elseif ($ai_summary) { ?>
    <div class="jobs-ai-reply-block jobs-ai-section" data-section="ai_content" data-jr-id="<?php echo (int)$jr_id; ?>">
      <div class="jobs-ai-reply-head">
        <span class="jobs-ai-reply-badge">↳ 답글</span>
      </div>
      <div class="jobs-ai-reply-body">
        <div class="jobs-ai-view-wrap">
          <div class="viewContent"><?php echo nl2br(htmlspecialchars($ai_summary)); ?></div>
          <div class="jobs-ai-reply-actions">
            <button type="button" class="btn-edit btn-edit-ai">✏️ 수정</button>
          </div>
        </div>
        <div class="jobs-ai-edit-wrap" style="display:none;">
          <textarea class="jobs-ai-edit-ta" rows="6"><?php echo htmlspecialchars($ai_summary); ?></textarea>
          <div class="jobs-ai-edit-actions">
            <button type="button" class="btn-save-ai">저장</button>
            <button type="button" class="btn-cancel-ai">취소</button>
          </div>
        </div>
      </div>
    </div>
    <?php } } ?>

    <div class="view-notices" style="margin:0 0 16px;width:100%;">
      <p>* 커뮤니티 정책과 맞지 않는 게시물의 경우 블라인드 또는 삭제될 수 있습니다.</p>
    </div>
    <div class="view-actions" style="margin:0 0 16px;width:100%;">
      <a href="<?php echo $jobs_ongoing_url; ?>" class="btn-action btn-list2">📋 목록으로</a>
    </div>
  </div>
</article>
<script>
(function(){
  var saveUrl = <?php echo json_encode($jobs_ai_save_url); ?>;
  document.querySelectorAll('.jobs-ai-section').forEach(function(block){
    var viewWrap = block.querySelector('.jobs-ai-view-wrap');
    var editWrap = block.querySelector('.jobs-ai-edit-wrap');
    var ta = block.querySelector('.jobs-ai-edit-ta');
    var btnEdit = block.querySelector('.btn-edit-ai');
    var btnSave = block.querySelector('.btn-save-ai');
    var btnCancel = block.querySelector('.btn-cancel-ai');
    var viewContent = block.querySelector('.viewContent');
    if (!viewWrap || !editWrap || !ta || !btnEdit || !btnSave || !btnCancel || !viewContent) return;
    var jrId = block.getAttribute('data-jr-id');
    var sectionKey = block.getAttribute('data-section');
    if (!jrId || !sectionKey) return;
    function esc(s){ var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
    function showView(){ viewWrap.style.display=''; editWrap.style.display='none'; }
    function showEdit(){ viewWrap.style.display='none'; editWrap.style.display=''; ta.value = viewContent.textContent || ''; ta.focus(); }
    btnEdit.onclick = function(){ showEdit(); };
    btnCancel.onclick = function(){ ta.value = viewContent.textContent || ''; showView(); };
    btnSave.onclick = function(){
      var v = ta.value;
      btnSave.disabled = true;
      var fd = new FormData();
      fd.append('jr_id', jrId);
      fd.append('section_key', sectionKey);
      fd.append('value', v);
      fetch(saveUrl, { method:'POST', body:fd, credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(res){
          btnSave.disabled = false;
          if (res.ok){
            viewContent.innerHTML = esc(res.value||v).replace(/\n/g,'<br>');
            showView();
            if (typeof alert === 'function') alert('저장되었습니다.');
          } else {
            alert(res.msg || '저장에 실패했습니다.');
          }
        })
        .catch(function(){
          btnSave.disabled = false;
          alert('저장 중 오류가 발생했습니다.');
        });
    };
  });
})();
</script>
