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
$jobs_basic_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_basic_info_save.php' : '/jobs_basic_info_save.php';

$status = $row['jr_status'];
$payment_ok = !empty($row['jr_payment_confirmed']);
$status_label = ($status === 'ongoing') ? '진행중' : ($payment_ok ? '입금확인' : '입금대기중');
$status_class = ($status === 'ongoing') ? 'ongoing' : ($payment_ok ? 'payment-ok' : 'payment-wait');

// 입금대기중: 상세 열람 차단 (URL 직접 접근 포함)
if ($status === 'pending' && !$payment_ok) {
    echo '<script>alert("입금확인 후 이용 가능합니다."); location.href="'.addslashes($jobs_ongoing_url).'";</script>';
    return;
}

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
$can_edit = ($status === 'ongoing' || $payment_ok);
$title_employ = $title ? $title . ' · ' . $employ_type : $employ_type;
$amenity_arr = is_array($data['amenity'] ?? null) ? array_map('trim', $data['amenity']) : (trim($amenity ?? '') ? explode(',', $amenity) : array());
?>
<?php
$sns_kakao = !empty($data['job_kakao']) ? trim($data['job_kakao']) : '';
$sns_line = !empty($data['job_line']) ? trim($data['job_line']) : '';
$sns_telegram = !empty($data['job_telegram']) ? trim($data['job_telegram']) : '';
$banner_comp = $nick ?: $comp ?: '—';
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<article id="bo_v" class="ev-view-wrap jobs-view-wrap jobs-ad-post-wrap" style="width:100%;max-width:680px;margin:0 auto;">
  <!-- eve_alba_ad_post 스타일 폼 -->
  <div class="jobs-ad-post" style="font-family:'Malgun Gothic','맑은 고딕',Apple SD Gothic Neo,sans-serif;color:#222;line-height:1.6;">

    <!-- 상단 배너 -->
    <div class="ad-banner" style="background:linear-gradient(135deg,#2D0020 0%,#FF1B6B 55%,#FF6BA8 100%);border-radius:16px 16px 0 0;padding:28px 30px 22px;position:relative;overflow:hidden;">
      <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;"></div>
      <div style="position:absolute;bottom:-20px;right:60px;width:80px;height:80px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
      <div style="display:inline-block;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:#fff;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;letter-spacing:.5px;margin-bottom:10px;">🏮 <?php echo htmlspecialchars($jobtype ?: '업종'); ?></div>
      <div style="font-size:26px;font-weight:900;color:#fff;letter-spacing:-0.5px;line-height:1.2;margin-bottom:6px;">🌸 <?php echo htmlspecialchars($banner_comp); ?></div>
      <div style="font-size:14px;color:rgba(255,255,255,.85);font-weight:500;"><?php echo htmlspecialchars($title ?: $row['jr_subject_display']); ?></div>
      <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
        <?php if ($region) { ?><span style="background:rgba(0,0,0,.25);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:12px;">📍 <?php echo htmlspecialchars($region); ?></span><?php } ?>
        <?php if ($salary_disp) { ?><span style="background:rgba(255,215,0,.25);color:#FFD700;font-size:11px;font-weight:700;padding:4px 10px;border-radius:12px;">💰 <?php echo htmlspecialchars($salary_disp); ?></span><?php } ?>
        <?php if ($amenity) { $a1 = explode(',', $amenity); $a1 = array_slice(array_map('trim', $a1), 0, 2); foreach ($a1 as $a) { if ($a) { ?><span style="background:rgba(255,255,255,.15);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:12px;">✅ <?php echo htmlspecialchars($a); ?></span><?php } } } ?>
      </div>
    </div>

    <!-- 기본 정보 테이블 -->
    <div class="ad-basic-info jobs-basic-info-block" data-jr-id="<?php echo (int)$jr_id; ?>" style="background:#fff;border:1.5px solid #fce8f0;border-top:none;padding:0;">
      <div style="background:linear-gradient(90deg,#fff0f6,#fff8fb);padding:10px 20px;border-bottom:1.5px solid #fce8f0;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;font-weight:900;color:#FF1B6B;letter-spacing:.3px;">📋 기본 정보</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit btn-edit-basic" style="padding:4px 12px;background:linear-gradient(135deg,var(--ev-orange, #FF6B35),var(--ev-hot-pink, #FF1B6B));color:#fff;border:none;border-radius:14px;font-size:11px;font-weight:700;cursor:pointer;">✏️ 수정</button><?php } ?>
      </div>
      <table style="width:100%;border-collapse:collapse;">
        <tr><td style="width:110px;padding:11px 14px 11px 20px;background:#fdf5f8;border-bottom:1px solid #fce8f0;font-size:12px;font-weight:700;color:#888;">🏷️ 업소명</td><td style="padding:11px 18px;border-bottom:1px solid #fce8f0;font-size:13px;font-weight:700;color:#222;"><?php echo htmlspecialchars($banner_comp); ?></td></tr>
        <tr><td style="width:110px;padding:11px 14px 11px 20px;background:#fdf5f8;border-bottom:1px solid #fce8f0;font-size:12px;font-weight:700;color:#888;">📞 연락처</td><td style="padding:11px 18px;border-bottom:1px solid #fce8f0;font-size:13px;font-weight:700;color:#FF1B6B;"><?php echo htmlspecialchars($contact ?: '—'); ?></td></tr>
        <tr><td style="width:110px;padding:11px 14px 11px 20px;background:#fdf5f8;border-bottom:1px solid #fce8f0;font-size:12px;font-weight:700;color:#888;">💬 SNS</td><td style="padding:11px 18px;border-bottom:1px solid #fce8f0;font-size:13px;color:#333;">
          <?php if ($sns_kakao) { ?><span style="display:inline-block;background:#FEE500;color:#333;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;margin-right:5px;">카카오 <?php echo htmlspecialchars($sns_kakao); ?></span><?php } ?>
          <?php if ($sns_line) { ?><span style="display:inline-block;background:#00B300;color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;margin-right:5px;">라인 <?php echo htmlspecialchars($sns_line); ?></span><?php } ?>
          <?php if ($sns_telegram) { ?><span style="display:inline-block;background:#2AABEE;color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;">텔레그램 <?php echo htmlspecialchars($sns_telegram); ?></span><?php } ?>
          <?php if (!$sns_kakao && !$sns_line && !$sns_telegram) { ?>—<?php } ?>
        </td></tr>
        <tr><td style="width:110px;padding:11px 14px 11px 20px;background:#fdf5f8;border-bottom:1px solid #fce8f0;font-size:12px;font-weight:700;color:#888;">💰 급여조건</td><td style="padding:11px 18px;border-bottom:1px solid #fce8f0;"><?php if ($salary_disp || $amenity) { ?><span style="display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-size:12px;font-weight:900;padding:4px 14px;border-radius:20px;"><?php echo htmlspecialchars($salary_disp ?: '급여협의'); if ($amenity) { echo ' · ' . htmlspecialchars(cut_str($amenity, 24)); } ?></span><?php } else { ?>—<?php } ?></td></tr>
        <tr><td style="width:110px;padding:11px 14px 11px 20px;background:#fdf5f8;border-bottom:1px solid #fce8f0;font-size:12px;font-weight:700;color:#888;">📍 근무지역</td><td style="padding:11px 18px;border-bottom:1px solid #fce8f0;font-size:13px;color:#333;"><?php echo htmlspecialchars($region ?: '—'); ?></td></tr>
        <tr><td style="width:110px;padding:11px 14px 11px 20px;background:#fdf5f8;font-size:12px;font-weight:700;color:#888;">🏮 업종/직종</td><td style="padding:11px 18px;font-size:13px;color:#333;">
          <?php if ($jobtype) { $jparts = array_filter(explode('/', str_replace(' / ', '/', $jobtype))); foreach ($jparts as $jp) { $jp = trim($jp); if ($jp) { ?><span style="display:inline-block;background:#FCE4EC;color:#C62828;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;margin-right:5px;"><?php echo htmlspecialchars($jp); ?></span><?php } } } else { ?>—<?php } ?>
        </td></tr>
      </table>
    </div>

    <?php if ($show_ai) { ?>
    <?php if ($has_sections) {
      $ai_intro_block = array('key' => 'ai_intro', 'label' => '인사말', 'val' => $ai_intro);
      if (!empty($ai_intro_block['val'])) {
    ?>
    <div class="ad-intro jobs-ai-section" data-section="ai_intro" data-jr-id="<?php echo (int)$jr_id; ?>" style="background:#fff;border:1.5px solid #fce8f0;border-top:none;padding:22px 24px;">
      <div class="jobs-ai-view-wrap"><div style="border-left:3px solid #FF1B6B;padding-left:14px;"><div class="viewContent" style="font-size:13px;color:#444;line-height:1.85;"><?php echo nl2br(htmlspecialchars($ai_intro)); ?></div></div></div>
      <div class="jobs-ai-edit-wrap" style="display:none;"><textarea class="jobs-ai-edit-ta" rows="6"><?php echo htmlspecialchars($ai_intro); ?></textarea><div class="jobs-ai-edit-actions"><button type="button" class="btn-save-ai">저장</button><button type="button" class="btn-cancel-ai">취소</button></div></div>
      <?php if ($can_edit) { ?><div class="jobs-ai-reply-actions" style="margin-top:12px;"><button type="button" class="btn-edit btn-edit-ai">✏️ 수정</button></div><?php } ?>
    </div>
    <?php }
    <?php }
      $ai_detail_sections = array(
        array('key' => 'ai_location', 'label' => '📍 업소 위치', 'val' => $ai_location),
        array('key' => 'ai_env', 'label' => '🏢 근무환경', 'val' => $ai_env),
        array('key' => 'ai_benefit', 'label' => '💰 지원 혜택 및 급여', 'val' => $ai_benefit),
      );
      foreach ($ai_detail_sections as $sec) {
        if (empty($sec['val'])) continue;
    ?>
    <div class="ad-detail-section jobs-ai-section" data-section="<?php echo htmlspecialchars($sec['key']); ?>" data-jr-id="<?php echo (int)$jr_id; ?>" style="background:#fff;border:1.5px solid #fce8f0;border-top:none;padding:20px 24px;">
      <div style="margin-bottom:8px;"><span style="background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-size:10px;font-weight:900;padding:3px 9px;border-radius:10px;"><?php echo htmlspecialchars($sec['label']); ?></span></div>
      <div class="jobs-ai-view-wrap"><div style="background:#fdf5f8;border-radius:10px;padding:13px 16px;font-size:12.5px;color:#444;line-height:1.85;border-left:3px solid #FF6BA8;"><div class="viewContent"><?php echo nl2br(htmlspecialchars($sec['val'])); ?></div></div></div>
      <div class="jobs-ai-edit-wrap" style="display:none;"><textarea class="jobs-ai-edit-ta" rows="6"><?php echo htmlspecialchars($sec['val']); ?></textarea><div class="jobs-ai-edit-actions"><button type="button" class="btn-save-ai">저장</button><button type="button" class="btn-cancel-ai">취소</button></div></div>
      <?php if ($can_edit) { ?><div class="jobs-ai-reply-actions" style="margin-top:12px;"><button type="button" class="btn-edit btn-edit-ai">✏️ 수정</button></div><?php } ?>
    </div>
    <?php }
      if (!empty($ai_wrapup)) {
    ?>
    <div class="ad-wrapup jobs-ai-section" data-section="ai_wrapup" data-jr-id="<?php echo (int)$jr_id; ?>" style="background:linear-gradient(135deg,#fff0f6,#fce8f2);border:1.5px solid #ffd6e7;border-top:none;padding:18px 24px;">
      <div style="font-size:12px;font-weight:900;color:#FF1B6B;margin-bottom:10px;">🎀 언니 사장의 약속</div>
      <div class="jobs-ai-view-wrap"><div class="viewContent" style="font-size:12.5px;color:#555;line-height:1.9;"><?php echo nl2br(htmlspecialchars($ai_wrapup)); ?></div></div>
      <div class="jobs-ai-edit-wrap" style="display:none;"><textarea class="jobs-ai-edit-ta" rows="6"><?php echo htmlspecialchars($ai_wrapup); ?></textarea><div class="jobs-ai-edit-actions"><button type="button" class="btn-save-ai">저장</button><button type="button" class="btn-cancel-ai">취소</button></div></div>
      <?php if ($can_edit) { ?><div class="jobs-ai-reply-actions" style="margin-top:12px;"><button type="button" class="btn-edit btn-edit-ai">✏️ 수정</button></div><?php } ?>
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
          <?php if ($can_edit) { ?><div class="jobs-ai-reply-actions"><button type="button" class="btn-edit btn-edit-ai">✏️ 수정</button></div><?php } ?>
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

    <!-- 연락처 CTA -->
    <div class="ad-cta" style="background:linear-gradient(135deg,#2D0020,#FF1B6B);border-radius:0 0 16px 16px;padding:22px 24px;text-align:center;">
      <div style="font-size:13px;font-weight:900;color:#fff;margin-bottom:4px;">💌 지금 바로 연락주세요! 기다리고 있을게요~</div>
      <div style="font-size:11px;color:rgba(255,255,255,.75);margin-bottom:16px;">자다가 깨서 연락 주셔도 괜찮아요! 🌙 24시간 열려 있어요</div>
      <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;margin-bottom:12px;">
        <?php if ($sns_kakao) { ?><a href="https://open.kakao.com/o/s/<?php echo htmlspecialchars($sns_kakao); ?>" target="_blank" rel="noopener" style="display:inline-block;background:#FEE500;color:#333;font-size:12px;font-weight:900;padding:10px 20px;border-radius:12px;text-decoration:none;">💬 카카오 <?php echo htmlspecialchars($sns_kakao); ?></a><?php } ?>
        <?php if ($sns_line) { ?><span style="display:inline-block;background:#00B300;color:#fff;font-size:12px;font-weight:900;padding:10px 20px;border-radius:12px;">💚 라인 <?php echo htmlspecialchars($sns_line); ?></span><?php } ?>
        <?php if ($sns_telegram) { ?><span style="display:inline-block;background:#2AABEE;color:#fff;font-size:12px;font-weight:900;padding:10px 20px;border-radius:12px;">✈️ 텔레그램 <?php echo htmlspecialchars($sns_telegram); ?></span><?php } ?>
      </div>
      <?php if ($contact) { ?><div style="background:rgba(255,255,255,.15);border-radius:10px;padding:10px 16px;display:inline-block;"><a href="tel:<?php echo preg_replace('/[^0-9+]/','',$contact); ?>" style="font-size:15px;font-weight:900;color:#fff;letter-spacing:.5px;text-decoration:none;">📞 <?php echo htmlspecialchars($contact); ?></a></div><?php } ?>
      <div style="margin-top:14px;font-size:10px;color:rgba(255,255,255,.4);">🌸 이브알바 EVE ALBA — <?php echo htmlspecialchars($banner_comp); ?></div>
    </div>
  </div>

    <!-- 기본정보 수정 모달 -->
    <?php if ($can_edit) { ?>
    <div id="basicInfoModal" class="jobs-basic-modal" style="display:none;position:fixed;inset:0;z-index:9999;flex-direction:row;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.5);">
      <div class="jobs-basic-modal-content" style="background:#fff;border-radius:14px;max-width:480px;width:100%;max-height:90vh;overflow:auto;box-shadow:0 10px 40px rgba(0,0,0,.2);">
        <div style="padding:14px 20px;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;display:flex;align-items:center;justify-content:space-between;">
          <strong style="font-size:15px;">📋 기본 정보 수정</strong>
          <button type="button" class="btn-modal-close" style="background:none;border:none;color:#fff;font-size:24px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">업소명</label><input type="text" id="bi_nickname" class="bi-input" value="<?php echo htmlspecialchars($nick ?: ''); ?>" placeholder="닉네임/업소명" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">연락처</label><input type="text" id="bi_contact" class="bi-input" value="<?php echo htmlspecialchars($contact); ?>" placeholder="010-0000-0000" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">카카오 ID</label><input type="text" id="bi_kakao" class="bi-input" value="<?php echo htmlspecialchars($sns_kakao); ?>" placeholder="" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">라인 ID</label><input type="text" id="bi_line" class="bi-input" value="<?php echo htmlspecialchars($sns_line); ?>" placeholder="" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">텔레그램 ID</label><input type="text" id="bi_telegram" class="bi-input" value="<?php echo htmlspecialchars($sns_telegram); ?>" placeholder="" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">급여조건</label>
            <div style="display:flex;gap:8px;align-items:center;margin-top:4px;">
              <select id="bi_salary_type" style="padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;min-width:100px;">
                <option value="급여협의"<?php echo ($salary_type==='급여협의')?' selected':''; ?>>급여협의</option>
                <option value="시급"<?php echo ($salary_type==='시급')?' selected':''; ?>>시급</option>
                <option value="일급"<?php echo ($salary_type==='일급')?' selected':''; ?>>일급</option>
                <option value="주급"<?php echo ($salary_type==='주급')?' selected':''; ?>>주급</option>
                <option value="월급"<?php echo ($salary_type==='월급')?' selected':''; ?>>월급</option>
              </select>
              <input type="text" id="bi_salary_amt" class="bi-input" value="<?php echo htmlspecialchars($salary_amt); ?>" placeholder="금액" style="flex:1;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;box-sizing:border-box;"><span style="font-size:12px;color:#888;">원</span>
            </div>
          </div>
          <div style="margin-bottom:12px;"><label style="font-size:12px;font-weight:700;color:#888;">근무지역</label><input type="text" id="bi_region" class="bi-input" value="<?php echo htmlspecialchars($region); ?>" placeholder="" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="margin-bottom:16px;"><label style="font-size:12px;font-weight:700;color:#888;">업종/직종</label><input type="text" id="bi_jobtype" class="bi-input" value="<?php echo htmlspecialchars($jobtype); ?>" placeholder="예: 카페 / 베이커리" style="width:100%;padding:10px 14px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;margin-top:4px;box-sizing:border-box;"></div>
          <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" class="btn-basic-cancel" style="padding:10px 20px;background:#e8e8e8;color:#555;border:none;border-radius:18px;font-size:13px;font-weight:700;cursor:pointer;">취소</button>
            <button type="button" class="btn-basic-save" style="padding:10px 24px;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;border:none;border-radius:18px;font-size:13px;font-weight:700;cursor:pointer;">저장</button>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>

    <div class="view-notices" style="margin:0 0 16px;width:100%;">
      <p>* 커뮤니티 정책과 맞지 않는 게시물의 경우 블라인드 또는 삭제될 수 있습니다.</p>
    </div>
    <div class="view-actions" style="margin:0 0 16px;width:100%;">
      <a href="<?php echo $jobs_ongoing_url; ?>" class="btn-action btn-list2">📋 목록으로</a>
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

  var basicModal = document.getElementById('basicInfoModal');
  var basicSaveUrl = <?php echo json_encode($jobs_basic_save_url ?? ''); ?>;
  var basicJrId = <?php echo (int)$jr_id; ?>;
  if (basicModal && basicSaveUrl && basicJrId) {
    var btnEditBasic = document.querySelector('.btn-edit-basic');
    var btnModalClose = basicModal.querySelector('.btn-modal-close');
    var btnBasicCancel = basicModal.querySelector('.btn-basic-cancel');
    var btnBasicSave = basicModal.querySelector('.btn-basic-save');
    function openBasicModal(){ basicModal.style.display='flex'; }
    function closeBasicModal(){ basicModal.style.display='none'; }
    if (btnEditBasic) btnEditBasic.onclick = openBasicModal;
    if (btnModalClose) btnModalClose.onclick = closeBasicModal;
    if (btnBasicCancel) btnBasicCancel.onclick = closeBasicModal;
    if (btnBasicSave) btnBasicSave.onclick = function(){
      var regionVal = (document.getElementById('bi_region')||{}).value || '';
      var regionParts = regionVal.trim().split(/\s+/, 2);
      var jobtypeVal = (document.getElementById('bi_jobtype')||{}).value || '';
      var jobParts = jobtypeVal.split(/\/|\/\/| \/ /).map(function(s){ return s.trim(); }).filter(Boolean);
      var fd = new FormData();
      fd.append('jr_id', basicJrId);
      fd.append('job_nickname', (document.getElementById('bi_nickname')||{}).value || '');
      fd.append('job_contact', (document.getElementById('bi_contact')||{}).value || '');
      fd.append('job_kakao', (document.getElementById('bi_kakao')||{}).value || '');
      fd.append('job_line', (document.getElementById('bi_line')||{}).value || '');
      fd.append('job_telegram', (document.getElementById('bi_telegram')||{}).value || '');
      fd.append('job_salary_type', (document.getElementById('bi_salary_type')||{}).value || '');
      fd.append('job_salary_amt', (document.getElementById('bi_salary_amt')||{}).value || '');
      fd.append('job_work_region_1', regionParts[0] || '');
      fd.append('job_work_region_detail_1', regionParts[1] || '');
      fd.append('job_job1', jobParts[0] || '');
      fd.append('job_job2', jobParts[1] || '');
      btnBasicSave.disabled = true;
      fetch(basicSaveUrl, { method:'POST', body:fd, credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(res){
          btnBasicSave.disabled = false;
          if (res.ok){ closeBasicModal(); location.reload(); }
          else { alert(res.msg || '저장에 실패했습니다.'); }
        })
        .catch(function(){ btnBasicSave.disabled = false; alert('저장 중 오류가 발생했습니다.'); });
    };
  }
})();
</script>
