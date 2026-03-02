<?php
/**
 * 어드민 - 히어로배너 생성/편집 (썸네일 에디터 포함)
 */
$sub_menu = '910920';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$token = get_session('ss_admin_token') ?: get_admin_token();
$sb_table = 'g5_special_banner';
$jr_table = 'g5_jobs_register';

$sb_id = isset($_GET['sb_id']) ? (int)$_GET['sb_id'] : 0;
$is_edit = false;
$sb = array();
$data = array();

if ($sb_id) {
    $sb = sql_fetch("SELECT * FROM {$sb_table} WHERE sb_id = {$sb_id} AND sb_type = 'hero'");
    if (!$sb) {
        alert('해당 히어로배너를 찾을 수 없습니다.', './eve_special_banner.php');
        exit;
    }
    $is_edit = true;
    $data = $sb['sb_data'] ? json_decode($sb['sb_data'], true) : array();
    if (!is_array($data)) $data = array();
}

$thumb_gradient   = isset($data['thumb_gradient']) ? trim($data['thumb_gradient']) : '';
$thumb_title      = isset($data['thumb_title']) ? trim($data['thumb_title']) : '';
$thumb_text       = isset($data['thumb_text']) ? trim($data['thumb_text']) : '';
$thumb_icon       = isset($data['thumb_icon']) ? trim($data['thumb_icon']) : '';
$thumb_motion     = isset($data['thumb_motion']) ? trim($data['thumb_motion']) : '';
$thumb_wave       = isset($data['thumb_wave']) ? (int)$data['thumb_wave'] : 0;
$thumb_text_color = isset($data['thumb_text_color']) ? trim($data['thumb_text_color']) : 'rgb(255,255,255)';
$thumb_border     = isset($data['thumb_border']) ? trim($data['thumb_border']) : '';

$sb_jr_id   = $is_edit ? (int)$sb['sb_jr_id'] : 0;
$sb_link    = $is_edit ? ($sb['sb_link'] ?? '') : '';
$sb_memo    = $is_edit ? ($sb['sb_memo'] ?? '') : '';
$sb_position = $is_edit ? (int)$sb['sb_position'] : 0;

$g5['title'] = $is_edit ? '히어로배너 편집' : '히어로배너 생성';
require_once './admin.head.php';

$gradients = array(
    1  => 'linear-gradient(135deg,rgb(255,65,108),rgb(255,75,43))',
    2  => 'linear-gradient(135deg,rgb(255,94,98),rgb(255,195,113))',
    3  => 'linear-gradient(135deg,rgb(238,9,121),rgb(255,106,0))',
    4  => 'linear-gradient(135deg,rgb(74,0,224),rgb(142,45,226))',
    5  => 'linear-gradient(135deg,rgb(67,233,123),rgb(56,249,215))',
    6  => 'linear-gradient(135deg,rgb(29,209,161),rgb(9,132,227))',
    7  => 'linear-gradient(135deg,rgb(196,113,237),rgb(246,79,89))',
    8  => 'linear-gradient(135deg,rgb(36,198,220),rgb(81,74,157))',
    9  => 'linear-gradient(135deg,rgb(0,210,255),rgb(58,123,213))',
    10 => 'linear-gradient(135deg,rgb(236,64,122),rgb(240,98,146))',
    11 => 'linear-gradient(135deg,rgb(118,75,162),rgb(102,126,234))',
    12 => 'linear-gradient(135deg,rgb(72,85,99),rgb(41,50,60))',
    13 => 'linear-gradient(135deg,rgb(30,60,114),rgb(42,82,152))',
    14 => 'linear-gradient(135deg,rgb(255,243,176),rgb(170,218,255))',
    15 => 'linear-gradient(135deg,rgb(249,83,198),rgb(255,107,157))',
    16 => 'linear-gradient(135deg,rgb(255,0,110),rgb(131,56,236))',
    17 => 'linear-gradient(135deg,rgb(67,206,162),rgb(24,90,157))',
    18 => 'linear-gradient(135deg,rgb(19,78,94),rgb(113,178,128))',
    19 => 'linear-gradient(135deg,rgb(255,153,102),rgb(255,94,98))',
    20 => 'linear-gradient(135deg,rgb(86,171,47),rgb(168,224,99))',
);
$saved_grad = $thumb_gradient ?: '1';
$icons = array(
    '' => array('label' => '없음', 'bg' => '#ccc'),
    'beginner' => array('label' => '💖 초보환영', 'bg' => '#FF1B6B'),
    'room' => array('label' => '🏡 원룸제공', 'bg' => '#FF6B35'),
    'luxury' => array('label' => '💎 고급시설', 'bg' => '#8B00FF'),
    'black' => array('label' => '📋 블랙 관리', 'bg' => '#333'),
    'phone' => array('label' => '📱 폰비지급', 'bg' => '#0077B6'),
    'size' => array('label' => '👗 사이즈X', 'bg' => '#E91E63'),
    'set' => array('label' => '🎀 세트환영', 'bg' => '#FF9800'),
    'pickup' => array('label' => '🚗 픽업가능', 'bg' => '#4CAF50'),
    'member' => array('label' => '🙋 1회원제운영', 'bg' => '#7B1FA2'),
    'kkongbi' => array('label' => '💰 꽁비지급', 'bg' => '#00897B'),
);
$motions = array(
    '' => '없음',
    'shimmer' => '🌸 글씨 확대',
    'soft-blink' => '💫 소프트 블링크',
    'glow' => '💡 글로우 글씨',
    'bounce' => '🔔 바운스',
);
$all_grads = $gradients;
$all_grads['P1'] = 'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)';
$all_grads['P2'] = 'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)';
$all_grads['P3'] = 'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)';
$all_grads['P4'] = 'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)';

$pv_grad = isset($all_grads[$saved_grad]) ? $all_grads[$saved_grad] : $gradients[1];
$pv_banner_style = '';
if ($thumb_wave) {
    preg_match_all('/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/', $pv_grad, $pv_m);
    if (!empty($pv_m[0]) && count($pv_m[0]) >= 2) {
        $c1 = $pv_m[0][0]; $c2 = $pv_m[0][1]; $c3 = isset($pv_m[0][2]) ? $pv_m[0][2] : $c1;
        $pv_banner_style = 'background:linear-gradient(135deg,'.$c1.','.$c2.','.$c3.','.$c1.','.$c2.');background-size:400% 400%';
    } else {
        $pv_banner_style = 'background:'.$pv_grad.';background-size:400% 400%';
    }
} else {
    $pv_banner_style = 'background:'.$pv_grad;
}
?>

<style>
:root{--pink:#FF1B6B;--orange:#FF6B35;--border:#fce8f0;--gold:#FFD700}
.hero-form-wrap{max-width:960px;margin:0 auto}
.hero-form-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.hero-form-header h2{margin:0;font-size:18px;font-weight:900;color:#333}
.hero-form-back{padding:6px 14px;background:#eee;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;color:#555}
.hero-form-back:hover{background:#ddd;color:#333}

.hero-meta-section{background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:18px 22px;margin-bottom:16px}
.hero-meta-section h3{margin:0 0 14px;font-size:14px;font-weight:800;color:#555}
.meta-row{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.meta-row:last-child{margin-bottom:0}
.meta-label{width:100px;font-size:12px;font-weight:700;color:#666;flex-shrink:0}
.meta-input{flex:1;padding:8px 12px;border:1.5px solid #f0e0e8;border-radius:8px;font-size:13px;outline:none;transition:border-color .2s}
.meta-input:focus{border-color:var(--pink)}
.meta-input-sm{width:80px;flex:initial}
.meta-help{font-size:10px;color:#aaa;margin-top:2px}
.jr-search-wrap{display:flex;gap:6px;flex:1}
.jr-search-wrap input{flex:1}
.jr-search-btn{padding:8px 14px;background:#6366f1;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer}
.jr-search-result{margin-top:6px;font-size:12px;color:#333;padding:6px 10px;background:#f8f0ff;border-radius:6px;display:none}

/* 썸네일 에디터 (jobs_view_main.php와 동일) */
.thumb-gen-wrap{max-width:960px;margin:0 auto 12px;background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;font-family:'Noto Sans KR',sans-serif}
.thumb-gen-wrap .tg-section-header{background:linear-gradient(90deg,#fff0f6,#fff8fb);padding:11px 20px;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.thumb-gen-wrap .tg-section-label{font-size:12px;font-weight:900;color:var(--pink);letter-spacing:.3px}
.thumb-gen-wrap .tg-save-btn{padding:5px 18px;border:none;border-radius:8px;background:linear-gradient(135deg,var(--orange),var(--pink));color:#fff;font-size:12px;font-weight:900;cursor:pointer;transition:opacity .2s;box-shadow:0 3px 12px rgba(255,27,107,.3)}
.thumb-gen-wrap .tg-save-btn:hover{opacity:.9}
.thumb-gen-wrap .tg-save-btn:disabled{opacity:.5;cursor:not-allowed}
.thumb-body{display:grid;grid-template-columns:1fr 300px;gap:0}
.thumb-controls{padding:20px 22px;border-right:1.5px solid var(--border)}
.thumb-preview-col{padding:20px 18px;background:linear-gradient(180deg,#fff0f6,#fff8fb);display:flex;flex-direction:column;align-items:center;gap:12px}
.thumb-preview-label{font-size:11px;font-weight:900;color:var(--pink);letter-spacing:.3px;align-self:flex-start}
.ctrl-row{margin-bottom:16px}
.ctrl-label{font-size:11px;font-weight:900;color:#666;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.ctrl-input{width:100%;padding:9px 12px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;font-family:inherit;outline:none;transition:border-color .2s;color:#222;resize:vertical}
.ctrl-input:focus{border-color:var(--pink)}
.ctrl-charcount{font-size:10px;color:#bbb;text-align:right;margin-top:3px}
.color-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:6px;margin-bottom:16px}
.color-swatch{width:100%;aspect-ratio:1;border-radius:8px;cursor:pointer;border:2.5px solid transparent;transition:all .18s;position:relative;overflow:hidden}
.color-swatch:hover{transform:scale(1.12);box-shadow:0 3px 10px rgba(0,0,0,.2)}
.color-swatch.selected{border-color:#222;box-shadow:0 0 0 2px #fff,0 0 0 4px #222;transform:scale(1.1)}
.color-swatch-num{position:absolute;bottom:1px;right:2px;font-size:8px;font-weight:700;color:rgba(255,255,255,.8);line-height:1;text-shadow:0 1px 2px rgba(0,0,0,.5)}
.premium-color-wrap{margin-bottom:16px}
.premium-title{font-size:11px;font-weight:900;color:#666;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.premium-color-wrap .color-grid{margin-bottom:0}
.carbon-bg{background:linear-gradient(160deg,rgba(45,45,55,.45) 0%,transparent 40%,rgba(55,55,65,.3) 100%),url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Crect width='8' height='8' fill='%230d0d12'/%3E%3Crect width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='6' width='2' height='2' fill='%2318181f'/%3E%3Crect y='6' width='2' height='2' fill='%2318181f'/%3E%3C/svg%3E") repeat!important;background-size:100% 100%,8px 8px!important}
.txt-color-opts{display:flex;gap:8px}
.txt-color-btn{display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;border:2px solid #eee;font-size:12px;font-weight:700;cursor:pointer;background:#f5f5f5;font-family:inherit;transition:all .18s;color:#555}
.txt-color-btn.selected{border-color:var(--pink);background:#fff0f6;color:var(--pink)}
.badge-opts{display:flex;flex-wrap:wrap;gap:6px}
.badge-opt{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid #eee;background:#f9f9f9;color:#666;transition:all .18s}
.badge-opt.selected{background:var(--pink);color:#fff;border-color:var(--pink)}
.badge-opt-none{border-style:dashed}
.badge-opt-none.selected{background:#fff;color:var(--pink)}
.motion-opts{display:flex;flex-wrap:wrap;gap:6px}
.motion-btn{padding:5px 13px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid #eee;background:#f9f9f9;color:#666;font-family:inherit;transition:all .18s}
.motion-btn.selected{background:var(--pink);color:#fff;border-color:var(--pink)}
.wave-toggle{display:flex;align-items:center;gap:8px;cursor:pointer}
.wave-toggle input{accent-color:var(--pink);width:15px;height:15px}
.wave-toggle-label{font-size:12px;color:#555}
.border-opts{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.border-btn{width:36px;height:36px;border-radius:8px;cursor:pointer;border:2px solid #eee;transition:all .18s;position:relative;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#999}
.border-btn:hover{transform:scale(1.1)}
.border-btn.selected{box-shadow:0 0 0 2px #fff,0 0 0 4px var(--pink);transform:scale(1.1)}
.border-btn-none{border:2px dashed #ddd;font-size:10px;color:#bbb}
/* 미리보기 카드 */
.job-card{position:relative;border-radius:12px;overflow:hidden;border:none;background:#fff;box-shadow:inset 0 0 0 0.75px #f0e0e8, 0 0 0 0.75px #f0e0e8}
.job-card-banner{height:auto;aspect-ratio:16/9;padding:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:14px;font-weight:900;color:#fff;text-align:center;line-height:1.4}
.job-card-banner span{position:relative;z-index:1;line-height:1.4;transition:font-size .15s}
.tpc-sub{display:block;font-size:12px;font-weight:500;margin-top:2px;opacity:.9;transition:font-size .15s}
.pv-icon-badge{position:absolute;top:7px;right:7px;font-size:10px;font-weight:900;padding:2px 7px;border-radius:9px;z-index:10;color:#fff}
.job-card-body{padding:10px 12px}
.job-card-location{font-size:11px;color:#888;margin-bottom:4px}
.job-loc-badge{background:#fff0f6;color:var(--pink);padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;margin-right:4px}
.job-desc{font-size:13px;font-weight:700;color:#333;margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.job-card-footer{display:flex;justify-content:space-between;align-items:center}
.job-wage{font-size:12px;font-weight:700;color:var(--pink)}
@keyframes motion-pulse-scale{0%,100%{transform:scale(1)}50%{transform:scale(1.25)}}
@keyframes motion-soft-blink{0%,100%{opacity:1}50%{opacity:.3}}
@keyframes motion-glow-pulse{0%,100%{text-shadow:none}50%{text-shadow:0 0 10px #fff,0 0 25px #fff,0 0 50px rgba(255,255,255,.7),0 0 80px rgba(255,255,255,.4)}}
@keyframes motion-bounce{0%,100%{transform:translateY(0)}25%{transform:translateY(-10px)}50%{transform:translateY(0)}65%{transform:translateY(-5px)}80%{transform:translateY(0)}90%{transform:translateY(-2px)}}
@keyframes wave-diag{0%{background-position:0% 0%}50%{background-position:100% 100%}100%{background-position:0% 0%}}
.pv-motion-shimmer{animation:motion-pulse-scale 1.4s ease-in-out infinite!important;display:inline-block!important}
.pv-motion-soft-blink{animation:motion-soft-blink 1.8s ease-in-out infinite!important}
.pv-motion-glow{animation:motion-glow-pulse 2s ease-in-out infinite!important}
.pv-motion-bounce{animation:motion-bounce 1.2s ease infinite!important}
.pv-wave-active{animation:wave-diag 4s ease-in-out infinite!important;background-size:400% 400%!important}
</style>

<div class="hero-form-wrap">
  <div class="hero-form-header">
    <h2>🏆 <?php echo $is_edit ? '히어로배너 편집 (#'.$sb_id.')' : '히어로배너 생성'; ?></h2>
    <a href="./eve_special_banner.php" class="hero-form-back">← 목록으로</a>
  </div>

  <!-- 기본 정보 -->
  <div class="hero-meta-section">
    <h3>📋 배너 정보</h3>
    <div class="meta-row">
      <span class="meta-label">연결 광고 (jr_id)</span>
      <div class="jr-search-wrap">
        <input type="text" class="meta-input" id="hero-jr-id" placeholder="jr_id 입력 (예: 4)" value="<?php echo $sb_jr_id ?: ''; ?>">
        <button type="button" class="jr-search-btn" onclick="searchJr()">확인</button>
      </div>
    </div>
    <div class="jr-search-result" id="jrResult"></div>
    <div class="meta-row">
      <span class="meta-label">클릭 링크</span>
      <input type="text" class="meta-input" id="hero-link" placeholder="비워두면 연결된 jr_id의 광고 페이지로 이동" value="<?php echo htmlspecialchars($sb_link); ?>">
    </div>
    <div class="meta-row">
      <span class="meta-label">표시 순서</span>
      <input type="number" class="meta-input meta-input-sm" id="hero-position" min="0" max="99" value="<?php echo $sb_position; ?>">
    </div>
    <div class="meta-row">
      <span class="meta-label">메모</span>
      <input type="text" class="meta-input" id="hero-memo" placeholder="계약 내용, 금액 등 (내부용)" value="<?php echo htmlspecialchars($sb_memo); ?>">
    </div>
  </div>

  <!-- 썸네일 에디터 -->
  <div class="thumb-gen-wrap" id="thumb-gen-section">
    <div class="tg-section-header">
      <span class="tg-section-label">🎨 썸네일 에디터</span>
      <button type="button" class="tg-save-btn" id="tg-save-btn" onclick="saveHero()">💾 저장</button>
    </div>
    <div class="thumb-body">
      <div class="thumb-controls">
        <div class="ctrl-row">
          <div class="ctrl-label">🎨 컬러 선택 <span style="color:#bbb;font-weight:400;">(무료 20종)</span></div>
          <div class="color-grid" id="tg-color-grid">
            <?php foreach ($gradients as $num => $grad) {
              $sel = ((string)$num === (string)$saved_grad) ? ' selected' : '';
              echo '<div class="color-swatch'.$sel.'" data-grad="'.$num.'" style="background:'.$grad.'" onclick="selectGrad(this)" title="컬러 '.$num.'"><span class="color-swatch-num">'.$num.'</span></div>';
            } ?>
          </div>
        </div>
        <div class="ctrl-row">
          <div class="premium-color-wrap">
            <div class="premium-title">유료 컬러 <span style="color:#aaa;font-weight:400;">(4종)</span></div>
            <div class="color-grid" id="tg-premium-grid">
              <?php
              $premium_colors = array(
                array('num'=>'P1','name'=>'메탈릭골드','bg'=>'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)'),
                array('num'=>'P2','name'=>'메탈릭실버','bg'=>'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)'),
                array('num'=>'P3','name'=>'카본','bg'=>'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)'),
                array('num'=>'P4','name'=>'오로라','bg'=>'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)'),
              );
              foreach ($premium_colors as $pc) {
                $psel = ((string)$saved_grad === $pc['num']) ? ' selected' : '';
                $extra_cls = ($pc['num'] === 'P3') ? ' carbon-bg' : '';
                echo '<div class="color-swatch'.$psel.$extra_cls.'" data-grad="'.$pc['num'].'" style="background:'.$pc['bg'].'" onclick="selectGrad(this)" title="'.$pc['name'].'"><span class="color-swatch-num">'.$pc['num'].'</span></div>';
              }
              ?>
            </div>
          </div>
        </div>
        <div class="ctrl-row">
          <div class="ctrl-label">✏️ 썸네일 제목</div>
          <input type="text" class="ctrl-input" id="tg-title" maxlength="20" placeholder="업소명을 입력하세요" value="<?php echo htmlspecialchars($thumb_title, ENT_QUOTES); ?>" oninput="updatePreview();countChar(this,'tg-title-cnt',20)">
          <div class="ctrl-charcount"><span id="tg-title-cnt"><?php echo mb_strlen($thumb_title, 'UTF-8'); ?></span>/20</div>
        </div>
        <div class="ctrl-row">
          <div class="ctrl-label">💬 홍보 문구</div>
          <input type="text" class="ctrl-input" id="tg-text" maxlength="60" placeholder="예) 시급 15,000원 · 초보환영 · 당일지급" value="<?php echo htmlspecialchars($thumb_text, ENT_QUOTES); ?>" oninput="updatePreview();countChar(this,'tg-text-cnt',60)">
          <div class="ctrl-charcount"><span id="tg-text-cnt"><?php echo mb_strlen($thumb_text, 'UTF-8'); ?></span>/60</div>
        </div>
        <div class="ctrl-row">
          <div class="ctrl-label">🖊️ 텍스트 컬러</div>
          <div class="txt-color-opts" id="tg-textcolor-grid">
            <button type="button" class="txt-color-btn<?php echo $thumb_text_color === 'rgb(255,255,255)' ? ' selected' : ''; ?>" data-tcolor="rgb(255,255,255)" onclick="selectTextColor(this)"><span style="width:14px;height:14px;border-radius:50%;background:#fff;border:1.5px solid #ddd;display:inline-block"></span> 흰색</button>
            <button type="button" class="txt-color-btn<?php echo $thumb_text_color === 'rgb(68,68,68)' ? ' selected' : ''; ?>" data-tcolor="rgb(68,68,68)" onclick="selectTextColor(this)"><span style="width:14px;height:14px;border-radius:50%;background:#333;display:inline-block"></span> 다크그레이</button>
          </div>
        </div>
        <div class="ctrl-row">
          <div class="ctrl-label">🏷️ 뱃지</div>
          <div class="badge-opts" id="tg-icon-grid">
            <?php foreach ($icons as $key => $ic) {
              $sel = ($thumb_icon === $key) ? ' selected' : '';
              if ($key === '') {
                echo '<button type="button" class="badge-opt badge-opt-none'.$sel.'" data-icon="" data-icon-bg="" data-icon-label="" onclick="selectIcon(this)">없음</button>';
              } else {
                echo '<button type="button" class="badge-opt'.$sel.'" data-icon="'.$key.'" data-icon-bg="'.$ic['bg'].'" data-icon-label="'.htmlspecialchars($ic['label'], ENT_QUOTES).'" onclick="selectIcon(this)" style="background:'.($sel?'':'#fff0f6').';color:'.($sel?'':'#FF1B6B').';border-color:'.($sel?'':'#ffd6e7').'">'.$ic['label'].'</button>';
              }
            } ?>
          </div>
        </div>
        <div class="ctrl-row">
          <div class="ctrl-label">✨ 제목 모션</div>
          <div class="motion-opts" id="tg-motion-grid">
            <?php foreach ($motions as $key => $label) {
              $sel = ($thumb_motion === $key) ? ' selected' : '';
              echo '<button type="button" class="motion-btn'.$sel.'" data-motion="'.$key.'" onclick="selectMotion(this)">'.$label.'</button>';
            } ?>
          </div>
        </div>
        <div class="ctrl-row">
          <div class="ctrl-label">🌊 컬러 웨이브</div>
          <label class="wave-toggle">
            <input type="checkbox" id="tg-wave-chk" <?php echo $thumb_wave ? 'checked' : ''; ?> onchange="toggleWave(this.checked)">
            <span class="wave-toggle-label">배경 웨이브 효과 적용</span>
          </label>
        </div>
        <div class="ctrl-row" style="margin-bottom:0">
          <div class="ctrl-label">🖼️ 테두리</div>
          <div class="border-opts" id="tg-border-grid">
            <button type="button" class="border-btn border-btn-none<?php echo !$thumb_border ? ' selected' : ''; ?>" title="없음" data-border="" onclick="selectBorder(this)">없음</button>
            <button type="button" class="border-btn<?php echo $thumb_border==='gold' ? ' selected' : ''; ?>" title="골드" data-border="gold" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#FFD700,#FFA500);border:none;box-shadow:inset 0 0 0 2px rgba(255,255,255,.3)"></button>
            <button type="button" class="border-btn<?php echo $thumb_border==='pink' ? ' selected' : ''; ?>" title="핫핑크" data-border="pink" onclick="selectBorder(this)" style="background:#FF1B6B;border:none"></button>
            <button type="button" class="border-btn<?php echo $thumb_border==='charcoal' ? ' selected' : ''; ?>" title="차콜" data-border="charcoal" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#2c2c2c,#4a4a4a);border:none"></button>
            <button type="button" class="border-btn<?php echo $thumb_border==='royalblue' ? ' selected' : ''; ?>" title="로얄블루" data-border="royalblue" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#1a3a8a,#4169E1);border:none"></button>
            <button type="button" class="border-btn<?php echo $thumb_border==='royalpurple' ? ' selected' : ''; ?>" title="로얄퍼플" data-border="royalpurple" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#4B0082,#7B2FBE);border:none"></button>
          </div>
        </div>
      </div>

      <!-- 미리보기 -->
      <div class="thumb-preview-col">
        <div class="thumb-preview-label">👁️ 미리보기</div>
        <div class="job-card" id="tg-pv-card" style="width:100%">
          <div class="job-card-banner<?php echo $thumb_wave ? ' pv-wave-active' : ''; ?><?php echo ($saved_grad === 'P3' && !$thumb_wave) ? ' carbon-bg' : ''; ?>" id="tg-pv-banner" style="<?php echo $pv_banner_style; ?>">
            <span id="tpc-title" class="<?php echo $thumb_motion ? 'pv-motion-'.htmlspecialchars($thumb_motion) : ''; ?>" style="color:<?php echo htmlspecialchars($thumb_text_color); ?>"><?php echo htmlspecialchars($thumb_title ?: '업소명', ENT_QUOTES); ?><span class="tpc-sub" id="tpc-text"><?php echo htmlspecialchars($thumb_text, ENT_QUOTES); ?></span></span>
          </div>
          <?php if ($thumb_icon && isset($icons[$thumb_icon])) { ?>
          <div class="pv-icon-badge" id="tg-pv-icon" style="background:<?php echo $icons[$thumb_icon]['bg']; ?>"><?php echo $icons[$thumb_icon]['label']; ?></div>
          <?php } else { ?>
          <div class="pv-icon-badge" id="tg-pv-icon" style="display:none"></div>
          <?php } ?>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">히어로</span> <span>배너</span></div>
            <div class="job-desc" id="tg-pv-desc"><?php echo htmlspecialchars($thumb_title ?: '히어로배너 미리보기'); ?></div>
            <div class="job-card-footer"><span class="job-wage">특수배너</span></div>
          </div>
        </div>
        <div style="font-size:10px;color:#aaa;text-align:center;line-height:1.6;margin-top:4px">
          💡 이 썸네일은 메인 상단<br>히어로 영역에 표시됩니다.
        </div>
        <button type="button" class="tg-save-btn" onclick="saveHero()" style="width:100%;padding:11px;border-radius:12px;font-size:13px;margin-top:10px">💾 저장</button>
      </div>
    </div>
  </div>
</div>

<script>
var _thumbGrads = <?php echo json_encode($all_grads, JSON_UNESCAPED_UNICODE); ?>;
var _thumbSelected = '<?php echo addslashes($saved_grad ?: "1"); ?>';
var _thumbIcon = '<?php echo addslashes($thumb_icon); ?>';
var _thumbMotion = '<?php echo addslashes($thumb_motion); ?>';
var _thumbWave = <?php echo $thumb_wave ? 'true' : 'false'; ?>;
var _thumbTextColor = '<?php echo addslashes($thumb_text_color); ?>';
var _thumbBorder = '<?php echo addslashes($thumb_border); ?>';

function _applyBannerBg(){
  var banner=document.getElementById('tg-pv-banner');
  if(!banner||!_thumbGrads[_thumbSelected])return;
  var g=_thumbGrads[_thumbSelected];
  banner.classList.remove('carbon-bg');
  if(_thumbWave){
    var m=g.match(/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/g);
    if(m&&m.length>=2){
      banner.style.background='linear-gradient(135deg,'+m[0]+','+m[1]+','+(m[2]||m[0])+','+m[0]+','+m[1]+')';
      banner.style.backgroundSize='400% 400%';
    }else{banner.style.background=g;banner.style.backgroundSize='400% 400%';}
    banner.classList.add('pv-wave-active');
  }else{
    banner.style.background=g;banner.style.backgroundSize='';
    banner.classList.remove('pv-wave-active');
    if(_thumbSelected==='P3')banner.classList.add('carbon-bg');
  }
}
function _applyBorder(){
  var card=document.getElementById('tg-pv-card');if(!card)return;
  var borders={gold:'#FFD700',pink:'#FF1B6B',charcoal:'#3a3a3a',royalblue:'#4169E1',royalpurple:'#7B2FBE'};
  if(borders[_thumbBorder]){
    card.style.boxShadow='inset 0 0 0 2px '+borders[_thumbBorder]+', 0 0 0 2px '+borders[_thumbBorder]+', 0 6px 24px rgba(0,0,0,.18)';
  } else {
    card.style.boxShadow='inset 0 0 0 0.75px #f0e0e8, 0 0 0 0.75px #f0e0e8';
  }
}
function selectGrad(btn){
  document.querySelectorAll('.color-swatch').forEach(function(b){b.classList.remove('selected');});
  btn.classList.add('selected');
  _thumbSelected=btn.getAttribute('data-grad');
  _applyBannerBg();
}
function updatePreview(){
  var t=document.getElementById('tg-title'),x=document.getElementById('tg-text');
  var pt=document.getElementById('tpc-title'),px=document.getElementById('tpc-text');
  var tv=t.value||'업소명';
  if(pt){var tLen=Array.from(tv).length;pt.childNodes[0].textContent=tv;pt.style.fontSize=tLen<=6?'14px':tLen<=10?'13px':tLen<=14?'12px':'11px';}
  if(px){var xv=x.value||'';px.textContent=xv;var xLen=Array.from(xv).length;px.style.fontSize=xLen<=15?'12px':xLen<=25?'11px':xLen<=40?'10px':'9px';}
  var desc=document.getElementById('tg-pv-desc');
  if(desc) desc.textContent=tv;
}
function countChar(el,spanId){var sp=document.getElementById(spanId);if(sp)sp.textContent=Array.from(el.value).length;}
function selectTextColor(btn){
  document.querySelectorAll('#tg-textcolor-grid .txt-color-btn').forEach(function(b){b.classList.remove('selected');});
  btn.classList.add('selected');
  _thumbTextColor=btn.getAttribute('data-tcolor')||'rgb(255,255,255)';
  var pt=document.getElementById('tpc-title');if(pt)pt.style.color=_thumbTextColor;
}
function selectIcon(btn){
  document.querySelectorAll('#tg-icon-grid .badge-opt').forEach(function(b){b.classList.remove('selected');});
  btn.classList.add('selected');
  _thumbIcon=btn.getAttribute('data-icon')||'';
  var pvIcon=document.getElementById('tg-pv-icon');
  if(pvIcon){
    if(_thumbIcon){pvIcon.style.display='';pvIcon.style.background=btn.getAttribute('data-icon-bg')||'#ccc';pvIcon.textContent=btn.getAttribute('data-icon-label')||'';}
    else{pvIcon.style.display='none';}
  }
}
function selectMotion(btn){
  document.querySelectorAll('#tg-motion-grid .motion-btn').forEach(function(b){b.classList.remove('selected');});
  btn.classList.add('selected');
  _thumbMotion=btn.getAttribute('data-motion')||'';
  var pt=document.getElementById('tpc-title');if(pt)pt.className=_thumbMotion?'pv-motion-'+_thumbMotion:'';
}
function toggleWave(checked){_thumbWave=checked;_applyBannerBg();}
function selectBorder(btn){
  document.querySelectorAll('#tg-border-grid .border-btn').forEach(function(b){b.classList.remove('selected');});
  btn.classList.add('selected');
  _thumbBorder=btn.getAttribute('data-border')||'';
  _applyBorder();
}

function searchJr(){
  var jrId=document.getElementById('hero-jr-id').value.trim();
  if(!jrId){alert('jr_id를 입력하세요.');return;}
  var xhr=new XMLHttpRequest();
  xhr.open('GET','./eve_special_banner_update.php?act=search&q='+encodeURIComponent(jrId)+'&type=hero&token=<?php echo $token; ?>');
  xhr.onload=function(){
    var box=document.getElementById('jrResult');
    if(xhr.status!==200){box.style.display='block';box.innerHTML='<span style="color:red">검색 오류</span>';return;}
    try{var data=JSON.parse(xhr.responseText);}catch(e){box.style.display='block';box.innerHTML='<span style="color:red">파싱 오류</span>';return;}
    if(!data.length){box.style.display='block';box.innerHTML='<span style="color:red">해당 jr_id의 진행중인 광고를 찾을 수 없습니다.</span>';return;}
    var r=data[0];
    box.style.display='block';
    box.innerHTML='✅ <b>#'+r.jr_id+'</b> '+escHtml(r.jr_company||'—')+' · '+escHtml(r.jr_nickname||'—')+' · '+escHtml(r.mb_id||'—')+' · 남은기간: '+r.remaining;
  };
  xhr.send();
}

function saveHero(){
  var btn=document.getElementById('tg-save-btn');if(btn)btn.disabled=true;
  var form=document.createElement('form');
  form.method='POST';form.action='./eve_special_banner_update.php';
  var fields={
    act:'save_hero',
    sb_id:'<?php echo $sb_id; ?>',
    jr_id:document.getElementById('hero-jr-id').value.trim(),
    link:document.getElementById('hero-link').value.trim(),
    position:document.getElementById('hero-position').value,
    memo:document.getElementById('hero-memo').value,
    thumb_gradient:_thumbSelected||'1',
    thumb_title:(document.getElementById('tg-title')||{}).value||'',
    thumb_text:(document.getElementById('tg-text')||{}).value||'',
    thumb_icon:_thumbIcon||'',
    thumb_motion:_thumbMotion||'',
    thumb_wave:_thumbWave?'1':'0',
    thumb_text_color:_thumbTextColor||'rgb(255,255,255)',
    thumb_border:_thumbBorder||'',
    token:'<?php echo $token; ?>'
  };
  for(var k in fields){var inp=document.createElement('input');inp.type='hidden';inp.name=k;inp.value=fields[k];form.appendChild(inp);}
  document.body.appendChild(form);form.submit();
}

function escHtml(s){var d=document.createElement('div');d.appendChild(document.createTextNode(s));return d.innerHTML;}

_applyBorder();
</script>

<?php
require_once './admin.tail.php';
