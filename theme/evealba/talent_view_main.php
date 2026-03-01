<?php
/**
 * 인재정보 상세보기 — AI매칭 프리뷰 카드 (읽기전용)
 * $rs_row, $rs_data 는 talent_view.php 에서 준비됨
 */
if (!defined('_GNUBOARD_')) exit;

$d = $rs_data;
$r = $rs_row;

function _tv($arr, $key, $default='') {
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : $default;
}
function _tv_chip($val, $color='pink') {
    if (!$val || $val === '—' || $val === '-') return '<span class="aip-chip aip-chip-gray">—</span>';
    return '<span class="aip-chip aip-chip-'.$color.'">'.htmlspecialchars($val).'</span>';
}

$title       = htmlspecialchars($r['rs_title']);
$nick        = htmlspecialchars($r['rs_nick']);
$gender      = htmlspecialchars($r['rs_gender']);
$age         = (int)$r['rs_age'];
$job1        = htmlspecialchars($r['rs_job1']);
$job2        = htmlspecialchars($r['rs_job2']);
$region      = htmlspecialchars($r['rs_region']);
$region_detail = htmlspecialchars(_tv($r, 'rs_region_detail'));
$work_region = htmlspecialchars($r['rs_work_region']);
$work_region_detail = htmlspecialchars(_tv($d, 'work_region_detail'));
$salary_type = htmlspecialchars($r['rs_salary_type']);
$salary_amt  = (int)$r['rs_salary_amt'];
$photo_url   = $r['rs_photo'] ? htmlspecialchars($r['rs_photo']) : '';
$intro       = htmlspecialchars(_tv($d, 'intro'));
$contact     = htmlspecialchars(_tv($d, 'contact'));
$phone       = htmlspecialchars(_tv($d, 'phone'));
$sns_type    = htmlspecialchars(_tv($d, 'sns_type'));
$sns_id      = htmlspecialchars(_tv($d, 'sns_id'));
$height      = htmlspecialchars(_tv($d, 'height'));
$weight      = htmlspecialchars(_tv($d, 'weight'));
$size        = htmlspecialchars(_tv($d, 'size'));
$edu         = htmlspecialchars(_tv($d, 'edu'));
$work_type   = htmlspecialchars(_tv($d, 'work_type'));
$work_time_type  = htmlspecialchars(_tv($d, 'work_time_type'));
$work_time_start = htmlspecialchars(_tv($d, 'work_time_start'));
$work_time_end   = htmlspecialchars(_tv($d, 'work_time_end'));
$work_days       = htmlspecialchars(_tv($d, 'work_days'));
$work_region_extra = htmlspecialchars(_tv($d, 'work_region_extra'));
$career_type = htmlspecialchars(_tv($d, 'career_type'));
$careers     = @json_decode(_tv($d, 'careers', '[]'), true);
$amenities   = @json_decode(_tv($d, 'amenities', '[]'), true);
$keywords    = @json_decode(_tv($d, 'keywords', '[]'), true);
$mbti        = htmlspecialchars(_tv($d, 'mbti'));

if (!is_array($careers))  $careers  = array();
if (!is_array($amenities)) $amenities = array();
if (!is_array($keywords))  $keywords  = array();

$is_owner = ($is_member && $member['mb_id'] === $r['mb_id']);
$talent_list_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php';
$resume_edit_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php';
$rs_date = $r['rs_datetime'] ? substr($r['rs_datetime'], 0, 10) : '';
?>

<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/css/resume_register.css?v=<?php echo @filemtime(G5_THEME_PATH.'/css/resume_register.css'); ?>">

<div style="max-width:800px;margin:0 auto 30px;">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <div style="display:flex;align-items:center;gap:10px;">
      <h2 style="font-size:20px;font-weight:800;color:#333;margin:0;">👑 인재정보 상세보기</h2>
      <span style="font-size:13px;color:#999;">등록일: <?php echo $rs_date; ?></span>
    </div>
    <div style="display:flex;gap:7px;">
<?php if ($is_owner) { ?>
      <a href="<?php echo $resume_edit_url; ?>?rs_id=<?php echo (int)$r['rs_id']; ?>" style="display:inline-flex;align-items:center;gap:4px;padding:8px 16px;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;border-radius:20px;font-size:13px;font-weight:700;text-decoration:none;">✏️ 수정</a>
<?php } ?>
      <a href="<?php echo $talent_list_url; ?>" style="display:inline-flex;align-items:center;gap:4px;padding:8px 14px;background:#f5f5f5;color:#666;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;">📋 목록</a>
    </div>
  </div>

  <div class="ai-preview-card" style="border:none;box-shadow:0 2px 16px rgba(0,0,0,.08);">
    <div class="ai-preview-header" style="cursor:default;">
      <div class="ai-preview-header-left">
        <div class="ai-preview-avatar">👩</div>
        <div>
          <div class="ai-preview-title">AI매칭에 보여지는 이력서</div>
          <div class="ai-preview-subtitle"><?php echo $nick; ?> · <?php echo $gender; ?> · <?php echo $age; ?>세</div>
        </div>
      </div>
    </div>
    <div class="ai-preview-body" style="display:block;">
      <div class="aip-row">
        <div class="aip-label">📄 이력서 제목</div>
        <div class="aip-value"><strong><?php echo $title; ?></strong></div>
      </div>

      <div class="aip-row aip-row-photo">
        <div class="aip-label">📷 사진 · 자기소개</div>
        <div class="aip-value aip-photo-area">
          <div class="aip-photo-box">
<?php if ($photo_url) { ?>
            <img src="<?php echo $photo_url; ?>" alt="프로필 사진" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
<?php } else { ?>
            <div class="aip-photo-empty"><span style="font-size:28px;opacity:.3;">👤</span><span class="aip-empty" style="font-size:11px;margin-top:4px;">사진 없음</span></div>
<?php } ?>
          </div>
          <div class="aip-intro"><?php echo $intro ? nl2br($intro) : '<span class="aip-empty">—</span>'; ?></div>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">👩 닉네임 · 연락방법</div>
        <div class="aip-value">
          <?php echo _tv_chip($nick, 'pink'); ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($contact ?: '미입력', 'blue'); ?>
<?php if ($phone) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($phone, 'gray'); ?>
<?php } ?>
<?php if ($sns_id) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($sns_type.': '.$sns_id, 'blue'); ?>
<?php } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">💰 희망급여 · 신장/체중 · 사이즈</div>
        <div class="aip-value">
          <?php
          $salary_str = $salary_type;
          if ($salary_amt > 0) $salary_str .= ' '.number_format($salary_amt).'원';
          echo _tv_chip($salary_str ?: '협의', 'pink');
          ?>
<?php if ($height || $weight) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip(($height?$height.'cm':'').($weight?'/'.$weight.'kg':''), 'blue'); ?>
<?php } ?>
<?php if ($size) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($size, 'blue'); ?>
<?php } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">🏠 거주지역 · 학력</div>
        <div class="aip-value">
          <?php echo _tv_chip(($region ?: '').($region_detail ? ' '.$region_detail : '') ?: '미입력', 'pink'); ?>
<?php if ($edu) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($edu, 'blue'); ?>
<?php } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">💼 희망분야</div>
        <div class="aip-value">
          <?php echo _tv_chip($job1 ?: '미선택', 'pink'); ?>
<?php if ($job2) { ?>
          <span class="aip-sep">›</span>
          <?php echo _tv_chip($job2, 'blue'); ?>
<?php } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">📍 업무가능지역</div>
        <div class="aip-value">
          <?php echo _tv_chip($work_region ?: '미선택', 'pink'); ?>
<?php if ($work_region_detail) { ?>
          <?php echo _tv_chip($work_region_detail, 'blue'); ?>
<?php } ?>
<?php if ($work_region_extra) { ?>
          <span class="aip-sep">·</span>
<?php   foreach (explode(',', $work_region_extra) as $ex) { echo _tv_chip(trim($ex), 'blue'); } ?>
<?php } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">⏰ 근무조건</div>
        <div class="aip-value">
          <?php echo _tv_chip($work_type ?: '미선택', 'pink'); ?>
<?php if ($work_time_type) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($work_time_type, 'blue'); ?>
<?php } ?>
<?php if ($work_time_start && $work_time_end) { ?>
          <?php echo _tv_chip($work_time_start.'~'.$work_time_end, 'gray'); ?>
<?php } ?>
<?php if ($work_days) { ?>
          <span class="aip-sep">·</span>
          <?php echo _tv_chip($work_days, 'blue'); ?>
<?php } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">📚 경력사항</div>
        <div class="aip-value">
          <?php echo _tv_chip($career_type ?: '미선택', 'pink'); ?>
<?php if (!empty($careers)) { ?>
          <div style="margin-top:8px;">
<?php   foreach ($careers as $c) {
          $c_name = isset($c['name']) ? htmlspecialchars($c['name']) : '';
          $c_type = isset($c['type']) ? htmlspecialchars($c['type']) : '';
          $c_period = isset($c['period']) ? htmlspecialchars($c['period']) : '';
          $c_pay = isset($c['pay']) ? htmlspecialchars($c['pay']) : '';
          if ($c_name || $c_type) {
?>
            <div style="font-size:13px;color:#555;margin:3px 0;"><?php echo $c_name; ?> (<?php echo $c_type; ?>) · <?php echo $c_period; ?><?php echo $c_pay ? ' · '.$c_pay.'원' : ''; ?></div>
<?php     }
        } ?>
          </div>
<?php } ?>
        </div>
      </div>

      <div class="aip-row aip-row-tall">
        <div class="aip-label">✅ 희망하는 편의사항</div>
        <div class="aip-value">
<?php if (empty($amenities)) { ?>
          <span class="aip-empty">선택된 편의사항이 없습니다</span>
<?php } else { foreach ($amenities as $am) { echo _tv_chip($am, 'blue').' '; } } ?>
        </div>
      </div>

      <div class="aip-row aip-row-tall">
        <div class="aip-label">🏷️ 키워드</div>
        <div class="aip-value">
<?php if (empty($keywords)) { ?>
          <span class="aip-empty">선택된 키워드가 없습니다</span>
<?php } else { foreach ($keywords as $kw) { echo _tv_chip($kw, 'pink').' '; } } ?>
        </div>
      </div>

      <div class="aip-row">
        <div class="aip-label">🧠 MBTI</div>
        <div class="aip-value"><?php echo _tv_chip($mbti ?: '미선택', 'pink'); ?></div>
      </div>

      <div class="aip-footer">
        <div class="aip-footer-icon">🤖</div>
        <div class="aip-footer-text">위 정보는 <strong>AI 근접 매칭</strong> 시 기업회원(업소)에게 노출됩니다. 민감한 개인정보(전화번호 등)는 선택한 공개 방식에 따라 처리됩니다.</div>
      </div>
    </div>
  </div>

  <div style="text-align:center;margin-top:20px;">
    <a href="<?php echo $talent_list_url; ?>" style="display:inline-block;padding:12px 30px;background:#f5f5f5;color:#666;border-radius:25px;font-size:14px;font-weight:600;text-decoration:none;">📋 목록으로</a>
  </div>

</div>
