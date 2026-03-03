<?php
/**
 * 썸네일상점 메인 - jobs_view 편집과 동일한 썸네일 생성 UI
 * - 왼쪽: MY PAGE (head에서 sidebar_jobs_register)
 * - 히어로배너: head에서 ads_main_banner
 * - 썸네일생성: 컬러·뱃지·모션·테두리 등 (jobs_view와 동일)
 * - 우측 플로팅: tail.php의 추천업소
 */
if (!defined('_GNUBOARD_')) exit;

$jobs_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$thumb_shop_url = $jobs_base . '/jobs_thumb_shop.php';
$basic_save_url = $jobs_base . '/jobs_basic_info_save.php';

if (!$is_member) {
    echo '<div class="thumb-shop-guest" style="padding:40px 20px;text-align:center;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">';
    echo '<h2 style="margin:0 0 12px;font-size:20px;color:#333;">🛒 썸네일상점</h2>';
    echo '<p style="margin:0 0 20px;color:#666;line-height:1.6;">채용광고 썸네일을 꾸미고 유료 옵션을 구매하려면<br>로그인 후 이용해 주세요.</p>';
    echo '<a href="'.G5_BBS_URL.'/login.php?url='.urlencode($thumb_shop_url).'" style="display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;font-weight:700;">로그인</a>';
    echo '</div>';
    return;
}

$_is_biz = false;
if ($member['mb_id']) {
    $__mb = sql_fetch("SELECT mb_1 FROM g5_member WHERE mb_id = '".addslashes($member['mb_id'])."'");
    $_is_biz = isset($__mb['mb_1']) && $__mb['mb_1'] === 'biz';
}
if (!$_is_biz) {
    echo '<div class="thumb-shop-personal" style="padding:40px 20px;text-align:center;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">';
    echo '<h2 style="margin:0 0 12px;font-size:20px;color:#333;">🛒 썸네일상점</h2>';
    echo '<p style="margin:0 0 20px;color:#666;line-height:1.6;">썸네일 옵션은 기업회원 전용 서비스입니다.</p>';
    echo '<a href="'.$jobs_base.'/jobs.php'" style="display:inline-block;padding:12px 24px;background:#444;color:#fff;border-radius:8px;text-decoration:none;">채용정보로 이동</a>';
    echo '</div>';
    return;
}

$mb_esc = addslashes($member['mb_id']);
$ongoing_list = array();
if (sql_num_rows(sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false))) {
    $res = sql_query("SELECT jr_id, jr_subject_display, jr_end_date, jr_status, jr_payment_confirmed FROM g5_jobs_register WHERE mb_id = '{$mb_esc}' AND jr_status IN ('pending','ongoing') AND (jr_payment_confirmed = 1 OR jr_status = 'ongoing') ORDER BY jr_id DESC LIMIT 50");
    if ($res) while ($r = sql_fetch_array($res)) $ongoing_list[] = $r;
}

$jr_id = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0;
// 진행중인 광고가 있으면 jr_id 없을 때 첫 번째 자동 선택 (썸네일생성 즉시 표시)
if (!$jr_id && !empty($ongoing_list)) {
    $jr_id = (int)$ongoing_list[0]['jr_id'];
}
$row = null;
$data = array();
$thumb_gradient = $thumb_title = $thumb_text = $thumb_icon = $thumb_motion = '';
$thumb_wave = 0;
$thumb_text_color = 'rgb(255,255,255)';
$thumb_border = '';

if ($jr_id) {
    $row = sql_fetch("SELECT * FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_esc}'");
    if ($row) {
        $data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
        if (!is_array($data)) $data = array();
        $thumb_gradient = isset($data['thumb_gradient']) ? trim($data['thumb_gradient']) : '';
        $thumb_title = isset($data['thumb_title']) ? trim($data['thumb_title']) : '';
        $thumb_text = isset($data['thumb_text']) ? trim($data['thumb_text']) : '';
        $thumb_icon = isset($data['thumb_icon']) ? trim($data['thumb_icon']) : '';
        $thumb_motion = isset($data['thumb_motion']) ? trim($data['thumb_motion']) : '';
        $thumb_wave = isset($data['thumb_wave']) ? (int)$data['thumb_wave'] : 0;
        $thumb_text_color = isset($data['thumb_text_color']) ? trim($data['thumb_text_color']) : 'rgb(255,255,255)';
        $thumb_border = isset($data['thumb_border']) ? trim($data['thumb_border']) : '';
    } else {
        $row = null;
        $jr_id = 0;
    }
}

$_opt_end_date = $row ? ($row['jr_end_date'] ?? '') : '';
$_opt_remaining_days = 0;
if ($_opt_end_date) {
    $_opt_remaining_days = max(0, (int)((strtotime($_opt_end_date . ' 23:59:59') - time()) / 86400));
}
$_opt_daily_rates = array('premium'=>1667,'badge'=>1000,'motion'=>1000,'wave'=>1667,'border'=>1000);

if (!isset($ev_regions)) $ev_regions = array();
if (!isset($ev_region_details)) $ev_region_details = array();
$_reg_name_map = array();
foreach ($ev_regions as $_r) $_reg_name_map[$_r['er_id']] = $_r['er_name'];
$_regd_name_map = array();
foreach ($ev_region_details as $_rd) $_regd_name_map[$_rd['erd_id']] = $_rd['erd_name'];

$reg1_id = $row ? (isset($data['job_work_region_1']) ? trim($data['job_work_region_1']) : '') : '';
$reg1_detail_id = $row ? (isset($data['job_work_region_detail_1']) ? trim($data['job_work_region_detail_1']) : '') : '';
$job1 = $row ? (isset($data['job_job1']) ? trim($data['job_job1']) : '') : '';
$nick = $row ? ($row['jr_nickname'] ?: (isset($data['job_nickname']) ? trim($data['job_nickname']) : '')) : '';
$comp = $row ? ($row['jr_company'] ?: (isset($data['job_company']) ? trim($data['job_company']) : '')) : '';
$biz_title = $row ? (isset($data['job_title']) && trim($data['job_title']) !== '' ? trim($data['job_title']) : ($row['jr_title'] ?: ($row['jr_subject_display'] ?? ''))) : '';
$salary_disp = '';
if ($row && isset($data['job_salary_type'])) {
    $st = trim($data['job_salary_type']);
    $sa = isset($data['job_salary_amt']) ? preg_replace('/[^0-9]/','',$data['job_salary_amt']) : '';
    $salary_disp = ($st === '급여협의') ? '급여협의' : ($st ? $st . ($sa ? ' ' . number_format((int)$sa) . '원' : '') : '');
}
?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/jobs_view_editor.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/jobs_view_editor.css'); ?>">
<style>
.thumb-gen-wrap{max-width:958px;margin:0 auto 12px;background:#fff;border:1.5px solid var(--border,#fce8f0);border-radius:16px;overflow:hidden;font-family:'Noto Sans KR',sans-serif}
.thumb-gen-wrap .tg-section-header{background:linear-gradient(90deg,var(--th-section-hd1,#fff0f6),var(--th-section-hd2,#fff8fb));padding:11px 20px;border-bottom:1.5px solid var(--border,#fce8f0);display:flex;align-items:center;justify-content:space-between}
.thumb-gen-wrap .tg-section-label{font-size:12px;font-weight:900;color:var(--pink,#FF1B6B);letter-spacing:.3px}
.thumb-gen-wrap .tg-save-btn{padding:5px 18px;border:none;border-radius:8px;background:linear-gradient(135deg,var(--orange,#FF6B35),var(--pink,#FF1B6B));color:#fff;font-size:12px;font-weight:900;cursor:pointer;transition:opacity .2s;box-shadow:0 3px 12px rgba(255,27,107,.3)}
.thumb-gen-wrap .tg-save-btn:hover{opacity:.9}
.thumb-gen-wrap .tg-save-btn:disabled{opacity:.5;cursor:not-allowed}
.thumb-body{display:grid;grid-template-columns:1fr 300px;gap:0}
.thumb-controls{padding:20px 22px;border-right:1.5px solid var(--border,#fce8f0)}
.thumb-preview-col{padding:20px 18px;background:linear-gradient(180deg,var(--th-section-hd1,#fff0f6),var(--th-section-hd2,#fff8fb));display:flex;flex-direction:column;align-items:center;gap:12px}
.thumb-preview-label{font-size:11px;font-weight:900;color:var(--pink,#FF1B6B);letter-spacing:.3px;align-self:flex-start}
.ctrl-row{margin-bottom:16px}
.ctrl-label{font-size:11px;font-weight:900;color:#666;margin-bottom:7px}
.ctrl-input{width:100%;padding:9px 12px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;font-family:inherit;outline:none}
.ctrl-input:focus{border-color:var(--pink,#FF1B6B)}
.ctrl-charcount{font-size:10px;color:#bbb;text-align:right;margin-top:3px}
.color-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:6px;margin-bottom:16px}
.color-swatch{width:100%;aspect-ratio:1;border-radius:8px;cursor:pointer;border:2.5px solid transparent;transition:all .18s;position:relative}
.color-swatch:hover{transform:scale(1.12)}
.color-swatch.selected{border-color:#222;box-shadow:0 0 0 2px #fff,0 0 0 4px #222}
.color-swatch-num{position:absolute;bottom:1px;right:2px;font-size:8px;font-weight:700;color:rgba(255,255,255,.8)}
.premium-color-wrap{margin-bottom:16px}
.premium-title{font-size:11px;font-weight:900;color:#666;margin-bottom:7px}
.carbon-bg{background:linear-gradient(160deg,rgba(45,45,55,.45) 0%,transparent 40%,rgba(55,55,65,.3) 100%),url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Crect width='8' height='8' fill='%230d0d12'/%3E%3Crect width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='6' width='2' height='2' fill='%2318181f'/%3E%3Crect y='6' width='2' height='2' fill='%2318181f'/%3E%3C/svg%3E") repeat!important;background-size:100% 100%,8px 8px!important}
.txt-color-opts{display:flex;gap:8px}
.txt-color-btn{display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;border:2px solid #eee;font-size:12px;font-weight:700;cursor:pointer;background:#f5f5f5;color:#555}
.txt-color-btn.selected{border-color:var(--pink,#FF1B6B);background:#fff0f6;color:var(--pink,#FF1B6B)}
.badge-opts,.motion-opts{display:flex;flex-wrap:wrap;gap:6px}
.badge-opt,.motion-btn{padding:5px 11px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid #eee;background:#f9f9f9;color:#666}
.badge-opt.selected,.motion-btn.selected{background:#FF1B6B;color:#fff;border-color:#FF1B6B}
.badge-opt-none{border-style:dashed}
.wave-toggle{display:flex;align-items:center;gap:8px;cursor:pointer}
.wave-toggle input{accent-color:#FF1B6B;width:15px;height:15px}
.border-opts{display:flex;gap:8px;flex-wrap:wrap}
.border-btn{width:36px;height:36px;border-radius:8px;cursor:pointer;border:2px solid #eee;transition:all .18s}
.border-btn.selected{box-shadow:0 0 0 2px #fff,0 0 0 4px #FF1B6B}
.border-btn-none{border:2px dashed #ddd;font-size:10px;color:#bbb}
#tg-pv-card.job-card{cursor:default;border-radius:12px!important;overflow:hidden}
#tg-pv-card .job-card-banner{height:auto;aspect-ratio:16/9;padding:16px}
.pv-icon-badge{position:absolute;top:7px;right:7px;font-size:10px;font-weight:900;padding:2px 7px;border-radius:9px;z-index:10;color:#fff}
.tg-option-price{margin-top:6px;padding:6px 10px;background:#fff8fb;border:1px solid #f0e0e8;border-radius:8px;font-size:12px}
.tg-total-wrap{margin-top:14px;background:linear-gradient(135deg,#2D0020,#5C0040);border-radius:12px;padding:14px 16px;color:#fff}
.tg-total-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.tg-total-header .tth-label{font-size:13px;font-weight:700}
.tg-total-header .tth-amount{font-size:18px;font-weight:900;color:#FFD700}
.tg-total-items{border-top:1px solid rgba(255,255,255,.15);padding-top:8px}
.tg-total-items .tti-row{display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:3px 0}
.tg-total-items .tti-empty{font-size:11px;color:rgba(255,255,255,.5)}
@keyframes motion-pulse-scale{0%,100%{transform:scale(1)}50%{transform:scale(1.25)}}
@keyframes motion-soft-blink{0%,100%{opacity:1}50%{opacity:.3}}
@keyframes motion-glow-pulse{0%,100%{text-shadow:none}50%{text-shadow:0 0 10px #fff,0 0 25px #fff}}
@keyframes motion-bounce{0%,100%{transform:translateY(0)}25%{transform:translateY(-10px)}50%{transform:translateY(0)}}
@keyframes wave-diag{0%{background-position:0% 0%}50%{background-position:100% 100%}100%{background-position:0% 0%}}
.pv-motion-shimmer{animation:motion-pulse-scale 1.4s ease-in-out infinite!important}
.pv-motion-soft-blink{animation:motion-soft-blink 1.8s ease-in-out infinite!important}
.pv-motion-glow{animation:motion-glow-pulse 2s ease-in-out infinite!important}
.pv-motion-bounce{animation:motion-bounce 1.2s ease infinite!important}
.pv-wave-active{animation:wave-diag 4s ease-in-out infinite!important;background-size:400% 400%!important}
@media(max-width:768px){.thumb-body{grid-template-columns:1fr}.thumb-controls{border-right:none;border-bottom:1.5px solid #fce8f0}.thumb-preview-col{order:-1}.color-grid{grid-template-columns:repeat(5,1fr)}}
</style>

<?php if (empty($ongoing_list)) { ?>
<div style="max-width:958px;margin:0 auto;padding:40px 20px;text-align:center;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
  <p style="margin:0 0 16px;color:#888;">진행중인 채용광고가 없습니다.</p>
  <a href="<?php echo $jobs_base; ?>/jobs_register.php" style="display:inline-block;padding:10px 20px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;">채용공고 등록하기</a>
</div>
<?php
define('_THUMB_SHOP_FLOATS_DONE_', true);
include_once(G5_THEME_PATH . '/inc/float_banners.php');
} elseif (!$jr_id) { ?>
<div style="max-width:958px;margin:0 auto;padding:24px;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
  <h2 style="margin:0 0 8px;font-size:22px;color:#333;">🛒 썸네일상점</h2>
  <p style="margin:0 0 24px;color:#666;font-size:14px;">채용광고 썸네일을 꾸미고 유료 옵션을 구매하세요.</p>
  <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;">적용할 채용광고 선택</label>
  <select id="ts-jr-id" style="padding:10px 12px;border:1px solid #ddd;border-radius:8px;min-width:280px;font-size:14px;" onchange="var v=this.value;if(v)location.href='<?php echo $thumb_shop_url; ?>?jr_id='+v;">
    <option value="">선택하세요</option>
    <?php
    foreach ($ongoing_list as $o) {
      $end = $o['jr_end_date'] ? date('Y-m-d', strtotime($o['jr_end_date'])) : '';
      $label = $o['jr_subject_display'] ?: ('#'.$o['jr_id']);
      echo '<option value="'.(int)$o['jr_id'].'">#'.$o['jr_id'].' '.htmlspecialchars($label).($end ? ' (종료 '.$end.')' : '').'</option>';
    } ?>
  </select>
</div>
<?php
define('_THUMB_SHOP_FLOATS_DONE_', true);
include_once(G5_THEME_PATH . '/inc/float_banners.php');
} else {
  $saved_grad = $thumb_gradient ?: '1';
  $gradients = array(
    1=>'linear-gradient(135deg,rgb(255,65,108),rgb(255,75,43))',2=>'linear-gradient(135deg,rgb(255,94,98),rgb(255,195,113))',
    3=>'linear-gradient(135deg,rgb(238,9,121),rgb(255,106,0))',4=>'linear-gradient(135deg,rgb(74,0,224),rgb(142,45,226))',
    5=>'linear-gradient(135deg,rgb(67,233,123),rgb(56,249,215))',6=>'linear-gradient(135deg,rgb(29,209,161),rgb(9,132,227))',
    7=>'linear-gradient(135deg,rgb(196,113,237),rgb(246,79,89))',8=>'linear-gradient(135deg,rgb(36,198,220),rgb(81,74,157))',
    9=>'linear-gradient(135deg,rgb(0,210,255),rgb(58,123,213))',10=>'linear-gradient(135deg,rgb(236,64,122),rgb(240,98,146))',
    11=>'linear-gradient(135deg,rgb(118,75,162),rgb(102,126,234))',12=>'linear-gradient(135deg,rgb(72,85,99),rgb(41,50,60))',
    13=>'linear-gradient(135deg,rgb(30,60,114),rgb(42,82,152))',14=>'linear-gradient(135deg,rgb(255,243,176),rgb(170,218,255))',
    15=>'linear-gradient(135deg,rgb(249,83,198),rgb(255,107,157))',16=>'linear-gradient(135deg,rgb(255,0,110),rgb(131,56,236))',
    17=>'linear-gradient(135deg,rgb(67,206,162),rgb(24,90,157))',18=>'linear-gradient(135deg,rgb(19,78,94),rgb(113,178,128))',
    19=>'linear-gradient(135deg,rgb(255,153,102),rgb(255,94,98))',20=>'linear-gradient(135deg,rgb(86,171,47),rgb(168,224,99))',
  );
  $icons = array(
    ''=>array('label'=>'없음','bg'=>'#ccc'),'beginner'=>array('label'=>'💖 초보환영','bg'=>'#FF1B6B'),
    'room'=>array('label'=>'🏡 원룸제공','bg'=>'#FF6B35'),'luxury'=>array('label'=>'💎 고급시설','bg'=>'#8B00FF'),
    'black'=>array('label'=>'📋 블랙 관리','bg'=>'#333'),'phone'=>array('label'=>'📱 폰비지급','bg'=>'#0077B6'),
    'size'=>array('label'=>'👗 사이즈X','bg'=>'#E91E63'),'set'=>array('label'=>'🎀 세트환영','bg'=>'#FF9800'),
    'pickup'=>array('label'=>'🚗 픽업가능','bg'=>'#4CAF50'),'member'=>array('label'=>'🙋 1회원제운영','bg'=>'#7B1FA2'),
    'kkongbi'=>array('label'=>'💰 꽁비지급','bg'=>'#00897B'),
  );
  $r1_disp = isset($_reg_name_map[(int)$reg1_id]) ? $_reg_name_map[(int)$reg1_id] : ($reg1_id ?: '지역');
  $d1_disp = isset($_regd_name_map[(int)$reg1_detail_id]) ? $_regd_name_map[(int)$reg1_detail_id] : ($reg1_detail_id ?: '상세지역');
  $pv_title = $thumb_title ?: $nick ?: $comp ?: '업소명';
  $pv_text = $thumb_text ?: '';
  $pv_desc = $biz_title ?: '광고제목';
  $all_grads = $gradients;
  $all_grads['P1']='linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)';
  $all_grads['P2']='linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)';
  $all_grads['P3']='linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)';
  $all_grads['P4']='linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)';
  $pv_grad = isset($all_grads[$saved_grad]) ? $all_grads[$saved_grad] : $gradients[1];
  $pv_banner_style = $thumb_wave ? 'background:'.$pv_grad.';background-size:400% 400%' : 'background:'.$pv_grad;
  ?>
<div class="thumb-gen-wrap" id="thumb-gen-section">
  <div class="tg-section-header">
    <span class="tg-section-label">🎨 썸네일 생성</span>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="<?php echo $thumb_shop_url; ?>" style="font-size:12px;color:#888;">← 다른 광고 선택</a>
      <button type="button" class="tg-save-btn" id="tg-save-btn" onclick="saveThumb()">💾 저장</button>
    </div>
  </div>
  <div class="thumb-body">
    <div class="thumb-controls">
      <div class="ctrl-row">
        <div class="ctrl-label">🎨 컬러 선택 <span style="color:#bbb;">(무료 20종)</span></div>
        <div class="color-grid" id="tg-color-grid">
          <?php foreach ($gradients as $num=>$grad) {
            $sel = ((string)$num===(string)$saved_grad)?' selected':'';
            echo '<div class="color-swatch'.$sel.'" data-grad="'.$num.'" style="background:'.$grad.'" onclick="selectGrad(this)"><span class="color-swatch-num">'.$num.'</span></div>';
          } ?>
        </div>
      </div>
      <div class="ctrl-row">
        <div class="premium-title">유료 컬러 (4종)</div>
        <div class="color-grid" id="tg-premium-grid">
          <?php foreach (array(array('P1','메탈릭골드','linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)'),array('P2','메탈릭실버','linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)'),array('P3','카본','linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)'),array('P4','오로라','linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)')) as $pc) {
            $sel = ($saved_grad===$pc[0])?' selected':'';
            $cls = ($pc[0]==='P3')?' carbon-bg':'';
            echo '<div class="color-swatch'.$sel.$cls.'" data-grad="'.$pc[0].'" style="background:'.$pc[2].'" onclick="selectGrad(this)"><span class="color-swatch-num">'.$pc[0].'</span></div>';
          } ?>
        </div>
        <div class="tg-option-price" id="tg-premium-price" style="display:none"></div>
      </div>
      <div class="ctrl-row">
        <div class="ctrl-label">✏️ 썸네일 제목</div>
        <input type="text" class="ctrl-input" id="tg-title" maxlength="20" placeholder="업소명" value="<?php echo htmlspecialchars($pv_title, ENT_QUOTES); ?>" oninput="updatePreview();countChar(this,'tg-title-cnt',20)">
        <div class="ctrl-charcount"><span id="tg-title-cnt"><?php echo mb_strlen($pv_title, 'UTF-8'); ?></span>/20</div>
      </div>
      <div class="ctrl-row">
        <div class="ctrl-label">💬 홍보 문구</div>
        <input type="text" class="ctrl-input" id="tg-text" maxlength="60" placeholder="예) 시급 15,000원 · 초보환영" value="<?php echo htmlspecialchars($pv_text, ENT_QUOTES); ?>" oninput="updatePreview();countChar(this,'tg-text-cnt',60)">
        <div class="ctrl-charcount"><span id="tg-text-cnt"><?php echo mb_strlen($pv_text, 'UTF-8'); ?></span>/60</div>
      </div>
      <div class="ctrl-row">
        <div class="ctrl-label">🖊️ 텍스트 컬러</div>
        <div class="txt-color-opts" id="tg-textcolor-grid">
          <button type="button" class="txt-color-btn<?php echo $thumb_text_color==='rgb(255,255,255)'?' selected':''; ?>" data-tcolor="rgb(255,255,255)" onclick="selectTextColor(this)"><span style="width:14px;height:14px;border-radius:50%;background:#fff;border:1.5px solid #ddd;display:inline-block"></span> 흰색</button>
          <button type="button" class="txt-color-btn<?php echo $thumb_text_color==='rgb(68,68,68)'?' selected':''; ?>" data-tcolor="rgb(68,68,68)" onclick="selectTextColor(this)"><span style="width:14px;height:14px;border-radius:50%;background:#333;display:inline-block"></span> 다크</button>
        </div>
      </div>
      <div class="ctrl-row">
        <div class="ctrl-label">🏷️ 뱃지</div>
        <div class="badge-opts" id="tg-icon-grid">
          <?php foreach ($icons as $k=>$ic) {
            $sel = ($thumb_icon===$k)?' selected':'';
            if($k==='') echo '<button type="button" class="badge-opt badge-opt-none'.$sel.'" data-icon="" data-icon-bg="" data-icon-label="" onclick="selectIcon(this)">없음</button>';
            else echo '<button type="button" class="badge-opt'.$sel.'" data-icon="'.$k.'" data-icon-bg="'.$ic['bg'].'" data-icon-label="'.htmlspecialchars($ic['label'], ENT_QUOTES).'" onclick="selectIcon(this)">'.$ic['label'].'</button>';
          } ?>
        </div>
        <div class="tg-option-price" id="tg-badge-price" style="<?php echo $thumb_icon?'':'display:none'; ?>"></div>
      </div>
      <div class="ctrl-row">
        <div class="ctrl-label">✨ 제목 모션</div>
        <div class="motion-opts" id="tg-motion-grid">
          <?php foreach (array(''=>'없음','shimmer'=>'🌸 글씨 확대','soft-blink'=>'💫 소프트 블링크','glow'=>'💡 글로우','bounce'=>'🔔 바운스') as $k=>$l) {
            $sel = ($thumb_motion===$k)?' selected':'';
            echo '<button type="button" class="motion-btn'.$sel.'" data-motion="'.$k.'" onclick="selectMotion(this)">'.$l.'</button>';
          } ?>
        </div>
        <div class="tg-option-price" id="tg-motion-price" style="<?php echo $thumb_motion?'':'display:none'; ?>"></div>
      </div>
      <div class="ctrl-row">
        <div class="ctrl-label">🌊 컬러 웨이브</div>
        <label class="wave-toggle">
          <input type="checkbox" id="tg-wave-chk" <?php echo $thumb_wave?'checked':''; ?> onchange="toggleWave(this.checked)">
          <span class="wave-toggle-label">배경 웨이브 효과</span>
        </label>
        <div class="tg-option-price" id="tg-wave-price" style="<?php echo $thumb_wave?'':'display:none'; ?>"></div>
      </div>
      <div class="ctrl-row" style="margin-bottom:0">
        <div class="ctrl-label">🖼️ 테두리</div>
        <div class="border-opts" id="tg-border-grid">
          <button type="button" class="border-btn border-btn-none<?php echo !$thumb_border?' selected':''; ?>" data-border="" onclick="selectBorder(this)">없음</button>
          <button type="button" class="border-btn<?php echo $thumb_border==='gold'?' selected':''; ?>" data-border="gold" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#FFD700,#FFA500)"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='pink'?' selected':''; ?>" data-border="pink" onclick="selectBorder(this)" style="background:#FF1B6B"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='charcoal'?' selected':''; ?>" data-border="charcoal" onclick="selectBorder(this)" style="background:#2c2c2c"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='royalblue'?' selected':''; ?>" data-border="royalblue" onclick="selectBorder(this)" style="background:#4169E1"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='royalpurple'?' selected':''; ?>" data-border="royalpurple" onclick="selectBorder(this)" style="background:#7B2FBE"></button>
        </div>
        <div class="tg-option-price" id="tg-border-price" style="<?php echo $thumb_border?'':'display:none'; ?>"></div>
      </div>
    </div>
    <div class="thumb-preview-col">
      <div class="thumb-preview-label">👁️ 미리보기</div>
      <div class="job-card" id="tg-pv-card" style="width:100%">
        <div class="job-card-banner<?php echo $thumb_wave?' pv-wave-active':''; ?><?php echo ($saved_grad==='P3'&&!$thumb_wave)?' carbon-bg':''; ?>" id="tg-pv-banner" style="<?php echo $pv_banner_style; ?>">
          <span id="tpc-title" class="<?php echo $thumb_motion?'pv-motion-'.htmlspecialchars($thumb_motion):''; ?>" style="color:<?php echo htmlspecialchars($thumb_text_color); ?>"><?php echo htmlspecialchars($pv_title, ENT_QUOTES); ?><span class="tpc-sub" id="tpc-text"><?php echo htmlspecialchars($pv_text, ENT_QUOTES); ?></span></span>
        </div>
        <?php if($thumb_icon&&isset($icons[$thumb_icon])){ ?>
        <div class="pv-icon-badge" id="tg-pv-icon" style="background:<?php echo $icons[$thumb_icon]['bg']; ?>"><?php echo $icons[$thumb_icon]['label']; ?></div>
        <?php }else{ ?>
        <div class="pv-icon-badge" id="tg-pv-icon" style="display:none"></div>
        <?php } ?>
        <div class="job-card-body">
          <div class="job-card-location"><span class="job-loc-badge"><?php echo htmlspecialchars($r1_disp); ?></span> <span><?php echo htmlspecialchars($d1_disp.' '.$job1); ?></span></div>
          <div class="job-desc"><?php echo htmlspecialchars($pv_desc); ?></div>
          <div class="job-card-footer"><span class="job-wage"><?php echo htmlspecialchars($salary_disp?:'급여조건'); ?></span></div>
        </div>
      </div>
      <div style="font-size:10px;color:#aaa;">💡 메인, 채용정보 페이지에 표시됩니다</div>
      <div style="width:100%;background:#2a1525;border-radius:10px;padding:8px 12px;font-size:11px;color:#ddd;">
        📆 광고 종료: <b style="color:#FFD700"><?php echo $_opt_end_date?:'미정'; ?></b> | 잔여 <b style="color:#FF1B6B"><?php echo $_opt_remaining_days; ?>일</b>
      </div>
      <div class="tg-total-wrap" id="tg-total-wrap" style="width:100%">
        <div class="tg-total-header">
          <span class="tth-label">🛒 총 옵션 비용</span>
          <span class="tth-amount" id="tg-total-amount">0 원</span>
        </div>
        <div class="tg-total-items" id="tg-total-items"><div class="tti-empty">선택된 유료 옵션이 없습니다</div></div>
      </div>
      <button type="button" class="tg-save-btn" onclick="saveThumb()" style="width:100%;padding:11px;">💾 저장</button>
      <a href="<?php echo $jobs_base.'/jobs_view.php?jr_id='.$jr_id.'&mode=edit'; ?>" style="display:block;text-align:center;margin-top:8px;font-size:12px;color:#888;">채용광고 수정하러 가기 →</a>
    </div>
  </div>
</div>
<?php
// 썸네일생성 섹션 플로팅배너 (추천업소 + CTA)
define('_THUMB_SHOP_FLOATS_DONE_', true);
include_once(G5_THEME_PATH . '/inc/float_banners.php');
?>
<script>
(function(){
  var jrId = <?php echo (int)$jr_id; ?>;
  var basicSaveUrl = '<?php echo addslashes($basic_save_url); ?>';
  var _thumbGrads = <?php echo json_encode($all_grads, JSON_UNESCAPED_UNICODE); ?>;
  var _thumbSelected = '<?php echo addslashes($saved_grad?:'1'); ?>';
  var _thumbIcon = '<?php echo addslashes($thumb_icon); ?>';
  var _thumbMotion = '<?php echo addslashes($thumb_motion); ?>';
  var _thumbWave = <?php echo $thumb_wave?'true':'false'; ?>;
  var _thumbTextColor = '<?php echo addslashes($thumb_text_color); ?>';
  var _thumbBorder = '<?php echo addslashes($thumb_border); ?>';
  var _optRemainingDays = <?php echo (int)$_opt_remaining_days; ?>;
  var _optDailyRates = {premium:1667,badge:1000,motion:1000,wave:1667,border:1000};
  var borders = {gold:'#FFD700',pink:'#FF1B6B',charcoal:'#3a3a3a',royalblue:'#4169E1',royalpurple:'#7B2FBE'};

  function _applyBannerBg(){
    var b = document.getElementById('tg-pv-banner');
    if(!b || !_thumbGrads[_thumbSelected]) return;
    var g = _thumbGrads[_thumbSelected];
    b.classList.remove('carbon-bg');
    if(_thumbWave){ b.style.background = g; b.style.backgroundSize = '400% 400%'; b.classList.add('pv-wave-active'); }
    else{ b.style.background = g; b.style.backgroundSize = ''; b.classList.remove('pv-wave-active'); if(_thumbSelected==='P3') b.classList.add('carbon-bg'); }
  }
  function _applyBorder(){
    var c = document.getElementById('tg-pv-card');
    if(!c) return;
    if(borders[_thumbBorder]) c.style.boxShadow = 'inset 0 0 0 2px '+borders[_thumbBorder]+', 0 0 0 2px '+borders[_thumbBorder];
    else c.style.boxShadow = '';
  }
  function _optPriceHtml(type,label,days){
    if(days<=0) return '';
    var cost = days * _optDailyRates[type];
    return '<span style="font-size:11px;color:#888;">잔여 '+days+'일 × '+_optDailyRates[type].toLocaleString()+'원 = </span><b style="color:#FF1B6B">'+cost.toLocaleString()+'원</b>';
  }
  function _updateOptPrice(id,type,label,visible){
    var el = document.getElementById(id);
    if(!el) return;
    el.style.display = visible ? '' : 'none';
    if(visible) el.innerHTML = _optPriceHtml(type,label,_optRemainingDays);
  }
  function calcThumbTotal(){
    var items=[], total=0, days=_optRemainingDays;
    var motionNames={shimmer:'글씨 확대', 'soft-blink':'소프트 블링크', glow:'글로우', bounce:'바운스'};
    var borderNames={gold:'골드',pink:'핫핑크',charcoal:'차콜',royalblue:'로얄블루',royalpurple:'로얄퍼플'};
    if(_thumbIcon&&days>0){ var c=days*_optDailyRates.badge; items.push({n:'뱃지 ('+days+'일)',p:c}); total+=c; }
    if(_thumbMotion&&days>0){ var c=days*_optDailyRates.motion; items.push({n:(motionNames[_thumbMotion]||'모션')+' ('+days+'일)',p:c}); total+=c; }
    if(_thumbWave&&days>0){ var c=days*_optDailyRates.wave; items.push({n:'웨이브 ('+days+'일)',p:c}); total+=c; }
    if(_thumbBorder&&days>0){ var c=days*_optDailyRates.border; items.push({n:(borderNames[_thumbBorder]||'테두리')+' ('+days+'일)',p:c}); total+=c; }
    if(_thumbSelected&&String(_thumbSelected).charAt(0)==='P'&&days>0){ var c=days*_optDailyRates.premium; items.push({n:'프리미엄 컬러 ('+days+'일)',p:c}); total+=c; }
    var amt=document.getElementById('tg-total-amount');
    if(amt) amt.textContent = total.toLocaleString('ko-KR')+' 원';
    var list=document.getElementById('tg-total-items');
    if(list) list.innerHTML = items.length ? items.map(function(i){return '<div class="tti-row"><span class="tti-name">'+i.n+'</span><span class="tti-price">'+i.p.toLocaleString()+'원</span></div>'; }).join('') : '<div class="tti-empty">선택된 유료 옵션이 없습니다</div>';
  }

  window.selectGrad = function(btn){
    document.querySelectorAll('.color-swatch').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbSelected = btn.getAttribute('data-grad');
    _applyBannerBg();
    var isPrem = _thumbSelected&&_thumbSelected.charAt(0)==='P';
    _updateOptPrice('tg-premium-price','premium','프리미엄',isPrem);
    calcThumbTotal();
  };
  window.updatePreview = function(){
    var t=document.getElementById('tg-title'), x=document.getElementById('tg-text'), pt=document.getElementById('tpc-title'), px=document.getElementById('tpc-text');
    if(pt) pt.childNodes[0].textContent = (t&&t.value)||'업소명';
    if(px) px.textContent = (x&&x.value)||'';
  };
  window.countChar = function(el,spanId,max){
    var sp=document.getElementById(spanId);
    if(sp) sp.textContent = (el&&el.value) ? Array.from(el.value).length : 0;
  };
  window.selectTextColor = function(btn){
    document.querySelectorAll('#tg-textcolor-grid .txt-color-btn').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbTextColor = btn.getAttribute('data-tcolor')||'rgb(255,255,255)';
    var pt=document.getElementById('tpc-title');
    if(pt) pt.style.color = _thumbTextColor;
  };
  window.selectIcon = function(btn){
    document.querySelectorAll('#tg-icon-grid .badge-opt').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbIcon = btn.getAttribute('data-icon')||'';
    var pv=document.getElementById('tg-pv-icon');
    if(pv){ if(_thumbIcon){ pv.style.display=''; pv.style.background=btn.getAttribute('data-icon-bg'); pv.textContent=btn.getAttribute('data-icon-label'); } else pv.style.display='none'; }
    _updateOptPrice('tg-badge-price','badge','뱃지',!!_thumbIcon);
    calcThumbTotal();
  };
  window.selectMotion = function(btn){
    document.querySelectorAll('#tg-motion-grid .motion-btn').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbMotion = btn.getAttribute('data-motion')||'';
    var pt=document.getElementById('tpc-title');
    if(pt) pt.className = _thumbMotion ? 'pv-motion-'+_thumbMotion : '';
    _updateOptPrice('tg-motion-price','motion','모션',!!_thumbMotion);
    calcThumbTotal();
  };
  window.toggleWave = function(checked){
    _thumbWave = checked;
    _applyBannerBg();
    _updateOptPrice('tg-wave-price','wave','웨이브',checked);
    calcThumbTotal();
  };
  window.selectBorder = function(btn){
    document.querySelectorAll('#tg-border-grid .border-btn').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbBorder = btn.getAttribute('data-border')||'';
    _applyBorder();
    _updateOptPrice('tg-border-price','border','테두리',!!_thumbBorder);
    calcThumbTotal();
  };
  window.saveThumb = function(){
    var btn = document.getElementById('tg-save-btn');
    if(btn) btn.disabled = true;
    var fd = new FormData();
    fd.append('jr_id', jrId);
    fd.append('thumb_gradient', _thumbSelected||'1');
    fd.append('thumb_title', (document.getElementById('tg-title')||{}).value||'');
    fd.append('thumb_text', (document.getElementById('tg-text')||{}).value||'');
    fd.append('thumb_icon', _thumbIcon||'');
    fd.append('thumb_motion', _thumbMotion||'');
    fd.append('thumb_wave', _thumbWave?'1':'0');
    fd.append('thumb_text_color', _thumbTextColor||'rgb(255,255,255)');
    fd.append('thumb_border', _thumbBorder||'');
    fetch(basicSaveUrl, {method:'POST', body:fd, credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(res){
      if(btn) btn.disabled = false;
      alert(res.ok ? '저장되었습니다.' : (res.msg||'저장에 실패했습니다.'));
    })
    .catch(function(){ if(btn) btn.disabled = false; alert('저장 중 오류가 발생했습니다.'); });
  };

  _applyBorder();
  if(_thumbIcon) _updateOptPrice('tg-badge-price','badge','뱃지',true);
  if(_thumbMotion) _updateOptPrice('tg-motion-price','motion','모션',true);
  if(_thumbWave) _updateOptPrice('tg-wave-price','wave','웨이브',true);
  if(_thumbBorder) _updateOptPrice('tg-border-price','border','테두리',true);
  if(_thumbSelected&&String(_thumbSelected).charAt(0)==='P') _updateOptPrice('tg-premium-price','premium','프리미엄',true);
  calcThumbTotal();
})();
</script>
<?php } ?>
