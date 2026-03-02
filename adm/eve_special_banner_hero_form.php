<?php
/**
 * 어드민 - 히어로배너 생성/편집 (실제 히어로 사이즈 미리보기 + 홍보문구1/2 + 폰트 컨트롤)
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
$thumb_text2      = isset($data['thumb_text2']) ? trim($data['thumb_text2']) : '';
$thumb_icon       = isset($data['thumb_icon']) ? trim($data['thumb_icon']) : '';
$thumb_motion     = isset($data['thumb_motion']) ? trim($data['thumb_motion']) : '';
$thumb_wave       = isset($data['thumb_wave']) ? (int)$data['thumb_wave'] : 0;
$thumb_text_color = isset($data['thumb_text_color']) ? trim($data['thumb_text_color']) : '#ffffff';
$thumb_border     = isset($data['thumb_border']) ? trim($data['thumb_border']) : '';

$title_size  = isset($data['title_size']) ? trim($data['title_size']) : '30px';
$title_align = isset($data['title_align']) ? trim($data['title_align']) : 'left';
$text_size   = isset($data['text_size']) ? trim($data['text_size']) : '14px';
$text_color  = isset($data['text_color']) ? trim($data['text_color']) : '#ffffff';
$text_align  = isset($data['text_align']) ? trim($data['text_align']) : 'left';
$text2_size  = isset($data['text2_size']) ? trim($data['text2_size']) : '14px';
$text2_color = isset($data['text2_color']) ? trim($data['text2_color']) : '#ffffff';
$text2_align = isset($data['text2_align']) ? trim($data['text2_align']) : 'left';

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
.jr-search-wrap{display:flex;gap:6px;flex:1}
.jr-search-wrap input{flex:1}
.jr-search-btn{padding:8px 14px;background:#6366f1;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer}
.jr-search-result{margin-top:6px;font-size:12px;color:#333;padding:6px 10px;background:#f8f0ff;border-radius:6px;display:none}

/* 에디터 */
.hero-editor-wrap{max-width:960px;margin:0 auto 12px;background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;font-family:'Noto Sans KR',sans-serif}
.hero-editor-wrap .tg-section-header{background:linear-gradient(90deg,#fff0f6,#fff8fb);padding:11px 20px;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.hero-editor-wrap .tg-section-label{font-size:12px;font-weight:900;color:var(--pink);letter-spacing:.3px}
.hero-editor-wrap .tg-save-btn{padding:5px 18px;border:none;border-radius:8px;background:linear-gradient(135deg,var(--orange),var(--pink));color:#fff;font-size:12px;font-weight:900;cursor:pointer;transition:opacity .2s;box-shadow:0 3px 12px rgba(255,27,107,.3)}
.hero-editor-wrap .tg-save-btn:hover{opacity:.9}
.hero-editor-wrap .tg-save-btn:disabled{opacity:.5;cursor:not-allowed}
.hero-editor-body{padding:20px 22px}

/* 미리보기 - 실제 히어로배너 사이즈 */
.hero-pv-section{margin-bottom:20px}
.hero-pv-label{font-size:11px;font-weight:900;color:var(--pink);letter-spacing:.3px;margin-bottom:8px}
.hero-pv-card{position:relative;border-radius:14px;overflow:hidden;height:190px;display:flex;align-items:center;padding:28px;cursor:default}
.hero-pv-card::before{content:'';position:absolute;top:-50%;right:-10%;width:60%;height:200%;background:radial-gradient(ellipse,rgba(255,27,107,.15),transparent 70%);animation:hero-float 3s ease-in-out infinite;pointer-events:none}
@keyframes hero-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.hero-pv-text{position:relative;z-index:2;max-width:70%}
.hero-pv-text h2{margin:0;font-weight:900;line-height:1.2;text-shadow:0 2px 10px rgba(0,0,0,.5);white-space:pre-wrap;word-break:keep-all}
.hero-pv-text p{margin:7px 0 0;font-weight:500;white-space:pre-wrap;word-break:keep-all}
.hero-pv-badge{position:absolute;top:14px;right:14px;font-weight:900;font-size:12px;padding:5px 12px;border-radius:18px;z-index:10;color:#fff}

/* 컨트롤 그리드 */
.ctrl-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 24px}
.ctrl-grid-full{grid-column:1/-1}
.ctrl-row{margin-bottom:16px}
.ctrl-label{font-size:11px;font-weight:900;color:#666;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.ctrl-input{width:100%;padding:9px 12px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;font-family:inherit;outline:none;transition:border-color .2s;color:#222;box-sizing:border-box}
.ctrl-input:focus{border-color:var(--pink)}
.ctrl-charcount{font-size:10px;color:#bbb;text-align:right;margin-top:3px}

/* 인라인 옵션 그룹 */
.ctrl-inline{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
.ctrl-inline select{padding:6px 10px;border:1.5px solid #f0e0e8;border-radius:8px;font-size:12px;font-family:inherit;outline:none;cursor:pointer;background:#fff}
.ctrl-inline select:focus{border-color:var(--pink)}

/* 색상 선택 (input[type=color]) */
.color-pick-wrap{display:flex;align-items:center;gap:8px}
.color-pick-wrap input[type=color]{width:32px;height:32px;border:2px solid #f0e0e8;border-radius:8px;cursor:pointer;padding:0;background:none;-webkit-appearance:none;appearance:none}
.color-pick-wrap input[type=color]::-webkit-color-swatch-wrapper{padding:2px}
.color-pick-wrap input[type=color]::-webkit-color-swatch{border-radius:4px;border:none}

/* 정렬 버튼 */
.align-opts{display:flex;gap:4px}
.align-btn{width:32px;height:32px;border:1.5px solid #eee;border-radius:6px;cursor:pointer;font-size:14px;background:#f9f9f9;display:flex;align-items:center;justify-content:center;transition:all .18s}
.align-btn:hover{background:#fff0f6}
.align-btn.selected{background:var(--pink);color:#fff;border-color:var(--pink)}

/* 컬러 그리드 */
.color-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:6px;margin-bottom:16px}
.color-swatch{width:100%;aspect-ratio:1;border-radius:8px;cursor:pointer;border:2.5px solid transparent;transition:all .18s;position:relative;overflow:hidden}
.color-swatch:hover{transform:scale(1.12);box-shadow:0 3px 10px rgba(0,0,0,.2)}
.color-swatch.selected{border-color:#222;box-shadow:0 0 0 2px #fff,0 0 0 4px #222;transform:scale(1.1)}
.color-swatch-num{position:absolute;bottom:1px;right:2px;font-size:8px;font-weight:700;color:rgba(255,255,255,.8);line-height:1;text-shadow:0 1px 2px rgba(0,0,0,.5)}
.premium-color-wrap{margin-bottom:16px}
.premium-title{font-size:11px;font-weight:900;color:#666;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.premium-color-wrap .color-grid{margin-bottom:0}
.carbon-bg{background:linear-gradient(160deg,rgba(45,45,55,.45) 0%,transparent 40%,rgba(55,55,65,.3) 100%),url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Crect width='8' height='8' fill='%230d0d12'/%3E%3Crect width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='6' width='2' height='2' fill='%2318181f'/%3E%3Crect y='6' width='2' height='2' fill='%2318181f'/%3E%3C/svg%3E") repeat!important;background-size:100% 100%,8px 8px!important}
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
.hero-bottom-save{text-align:center;margin-top:16px}
.hero-bottom-save .tg-save-btn{width:100%;padding:13px;border-radius:12px;font-size:14px}
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

  <!-- 히어로배너 에디터 -->
  <div class="hero-editor-wrap" id="hero-editor-section">
    <div class="tg-section-header">
      <span class="tg-section-label">🎨 히어로배너 에디터</span>
      <button type="button" class="tg-save-btn" id="tg-save-btn" onclick="saveHero()">💾 저장</button>
    </div>
    <div class="hero-editor-body">

      <!-- 실제 크기 미리보기 -->
      <div class="hero-pv-section">
        <div class="hero-pv-label">👁️ 실제 크기 미리보기 (히어로배너)</div>
        <div class="hero-pv-card<?php echo $thumb_wave ? ' pv-wave-active' : ''; ?><?php echo ($saved_grad === 'P3' && !$thumb_wave) ? ' carbon-bg' : ''; ?>" id="hero-pv-card" style="<?php echo $pv_banner_style; ?>">
          <div class="hero-pv-text" id="hero-pv-text" style="text-align:<?php echo htmlspecialchars($title_align); ?>">
            <h2 id="hero-pv-h2" class="<?php echo $thumb_motion ? 'pv-motion-'.htmlspecialchars($thumb_motion) : ''; ?>" style="font-size:<?php echo htmlspecialchars($title_size); ?>;color:<?php echo htmlspecialchars($thumb_text_color); ?>"><?php echo htmlspecialchars($thumb_title ?: '썸네일 제목'); ?></h2>
            <p id="hero-pv-p1" style="font-size:<?php echo htmlspecialchars($text_size); ?>;color:<?php echo htmlspecialchars($text_color); ?>;text-align:<?php echo htmlspecialchars($text_align); ?>"><?php echo htmlspecialchars($thumb_text ?: '홍보문구1 입력'); ?></p>
            <p id="hero-pv-p2" style="font-size:<?php echo htmlspecialchars($text2_size); ?>;color:<?php echo htmlspecialchars($text2_color); ?>;text-align:<?php echo htmlspecialchars($text2_align); ?>;margin-top:4px"><?php echo htmlspecialchars($thumb_text2 ?: '홍보문구2 입력'); ?></p>
          </div>
          <?php if ($thumb_icon && isset($icons[$thumb_icon])) { ?>
          <div class="hero-pv-badge" id="hero-pv-badge" style="background:<?php echo $icons[$thumb_icon]['bg']; ?>"><?php echo $icons[$thumb_icon]['label']; ?></div>
          <?php } else { ?>
          <div class="hero-pv-badge" id="hero-pv-badge" style="display:none"></div>
          <?php } ?>
        </div>
      </div>

      <!-- 컨트롤 영역 -->
      <div class="ctrl-grid">
        <!-- 썸네일 제목 -->
        <div class="ctrl-row">
          <div class="ctrl-label">📌 썸네일 제목 (메인 타이틀)</div>
          <input type="text" class="ctrl-input" id="tg-title" maxlength="30" placeholder="예) 강남 룸 80개 1등 대일팀!" value="<?php echo htmlspecialchars($thumb_title, ENT_QUOTES); ?>" oninput="updatePreview()">
          <div class="ctrl-charcount"><span id="tg-title-cnt"><?php echo mb_strlen($thumb_title, 'UTF-8'); ?></span>/30</div>
        </div>
        <!-- 썸네일 제목 스타일 -->
        <div class="ctrl-row">
          <div class="ctrl-label">🎨 썸네일 제목 스타일</div>
          <div class="ctrl-inline">
            <select id="tg-title-size" onchange="updatePreview()">
              <option value="24px"<?php echo $title_size==='24px'?' selected':''; ?>>소 (24px)</option>
              <option value="28px"<?php echo $title_size==='28px'?' selected':''; ?>>중 (28px)</option>
              <option value="30px"<?php echo $title_size==='30px'?' selected':''; ?>>대 (30px)</option>
              <option value="36px"<?php echo $title_size==='36px'?' selected':''; ?>>특대 (36px)</option>
              <option value="42px"<?php echo $title_size==='42px'?' selected':''; ?>>초특대 (42px)</option>
            </select>
            <div class="color-pick-wrap">
              <input type="color" id="tg-title-color" value="<?php echo ($thumb_text_color === 'rgb(255,255,255)' || $thumb_text_color === '#ffffff') ? '#ffffff' : '#444444'; ?>" onchange="updatePreview()">
            </div>
            <div class="align-opts" id="tg-title-align">
              <button type="button" class="align-btn<?php echo $title_align==='left'?' selected':''; ?>" data-align="left" onclick="setAlign('title',this)" title="좌측">◀</button>
              <button type="button" class="align-btn<?php echo $title_align==='center'?' selected':''; ?>" data-align="center" onclick="setAlign('title',this)" title="중앙">●</button>
              <button type="button" class="align-btn<?php echo $title_align==='right'?' selected':''; ?>" data-align="right" onclick="setAlign('title',this)" title="우측">▶</button>
            </div>
          </div>
        </div>

        <!-- 홍보문구1 -->
        <div class="ctrl-row">
          <div class="ctrl-label">📢 홍보문구1</div>
          <input type="text" class="ctrl-input" id="tg-text" maxlength="60" placeholder="예) 🔥 하이퍼블릭 밀빵OK · 5인1조 픽업OK!!" value="<?php echo htmlspecialchars($thumb_text, ENT_QUOTES); ?>" oninput="updatePreview()">
          <div class="ctrl-charcount"><span id="tg-text-cnt"><?php echo mb_strlen($thumb_text, 'UTF-8'); ?></span>/60</div>
        </div>
        <!-- 홍보문구1 스타일 -->
        <div class="ctrl-row">
          <div class="ctrl-label">🎨 홍보문구1 스타일</div>
          <div class="ctrl-inline">
            <select id="tg-text-size" onchange="updatePreview()">
              <option value="12px"<?php echo $text_size==='12px'?' selected':''; ?>>소 (12px)</option>
              <option value="14px"<?php echo $text_size==='14px'?' selected':''; ?>>중 (14px)</option>
              <option value="16px"<?php echo $text_size==='16px'?' selected':''; ?>>대 (16px)</option>
              <option value="18px"<?php echo $text_size==='18px'?' selected':''; ?>>특대 (18px)</option>
            </select>
            <div class="color-pick-wrap">
              <input type="color" id="tg-text-color" value="<?php echo (strpos($text_color,'#')===0) ? htmlspecialchars($text_color) : '#ffffff'; ?>" onchange="updatePreview()">
            </div>
            <div class="align-opts" id="tg-text-align">
              <button type="button" class="align-btn<?php echo $text_align==='left'?' selected':''; ?>" data-align="left" onclick="setAlign('text1',this)" title="좌측">◀</button>
              <button type="button" class="align-btn<?php echo $text_align==='center'?' selected':''; ?>" data-align="center" onclick="setAlign('text1',this)" title="중앙">●</button>
              <button type="button" class="align-btn<?php echo $text_align==='right'?' selected':''; ?>" data-align="right" onclick="setAlign('text1',this)" title="우측">▶</button>
            </div>
          </div>
        </div>

        <!-- 홍보문구2 -->
        <div class="ctrl-row">
          <div class="ctrl-label">💬 홍보문구2</div>
          <input type="text" class="ctrl-input" id="tg-text2" maxlength="60" placeholder="예) 시급 15,000원 · 초보환영 · 당일지급" value="<?php echo htmlspecialchars($thumb_text2, ENT_QUOTES); ?>" oninput="updatePreview()">
          <div class="ctrl-charcount"><span id="tg-text2-cnt"><?php echo mb_strlen($thumb_text2, 'UTF-8'); ?></span>/60</div>
        </div>
        <!-- 홍보문구2 스타일 -->
        <div class="ctrl-row">
          <div class="ctrl-label">🎨 홍보문구2 스타일</div>
          <div class="ctrl-inline">
            <select id="tg-text2-size" onchange="updatePreview()">
              <option value="12px"<?php echo $text2_size==='12px'?' selected':''; ?>>소 (12px)</option>
              <option value="14px"<?php echo $text2_size==='14px'?' selected':''; ?>>중 (14px)</option>
              <option value="16px"<?php echo $text2_size==='16px'?' selected':''; ?>>대 (16px)</option>
              <option value="18px"<?php echo $text2_size==='18px'?' selected':''; ?>>특대 (18px)</option>
            </select>
            <div class="color-pick-wrap">
              <input type="color" id="tg-text2-color" value="<?php echo (strpos($text2_color,'#')===0) ? htmlspecialchars($text2_color) : '#ffffff'; ?>" onchange="updatePreview()">
            </div>
            <div class="align-opts" id="tg-text2-align">
              <button type="button" class="align-btn<?php echo $text2_align==='left'?' selected':''; ?>" data-align="left" onclick="setAlign('text2',this)" title="좌측">◀</button>
              <button type="button" class="align-btn<?php echo $text2_align==='center'?' selected':''; ?>" data-align="center" onclick="setAlign('text2',this)" title="중앙">●</button>
              <button type="button" class="align-btn<?php echo $text2_align==='right'?' selected':''; ?>" data-align="right" onclick="setAlign('text2',this)" title="우측">▶</button>
            </div>
          </div>
        </div>

        <!-- 배경 컬러 -->
        <div class="ctrl-row ctrl-grid-full">
          <div class="ctrl-label">🎨 배경 컬러 선택 <span style="color:#bbb;font-weight:400;">(무료 20종)</span></div>
          <div class="color-grid" id="tg-color-grid">
            <?php foreach ($gradients as $num => $grad) {
              $sel = ((string)$num === (string)$saved_grad) ? ' selected' : '';
              echo '<div class="color-swatch'.$sel.'" data-grad="'.$num.'" style="background:'.$grad.'" onclick="selectGrad(this)" title="컬러 '.$num.'"><span class="color-swatch-num">'.$num.'</span></div>';
            } ?>
          </div>
        </div>
        <div class="ctrl-row ctrl-grid-full">
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

        <!-- 뱃지 -->
        <div class="ctrl-row ctrl-grid-full">
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

        <!-- 모션 -->
        <div class="ctrl-row">
          <div class="ctrl-label">✨ 제목 모션</div>
          <div class="motion-opts" id="tg-motion-grid">
            <?php foreach ($motions as $key => $label) {
              $sel = ($thumb_motion === $key) ? ' selected' : '';
              echo '<button type="button" class="motion-btn'.$sel.'" data-motion="'.$key.'" onclick="selectMotion(this)">'.$label.'</button>';
            } ?>
          </div>
        </div>

        <!-- 웨이브 -->
        <div class="ctrl-row">
          <div class="ctrl-label">🌊 컬러 웨이브</div>
          <label class="wave-toggle">
            <input type="checkbox" id="tg-wave-chk" <?php echo $thumb_wave ? 'checked' : ''; ?> onchange="toggleWave(this.checked)">
            <span class="wave-toggle-label">배경 웨이브 효과 적용</span>
          </label>
        </div>

        <!-- 테두리 -->
        <div class="ctrl-row ctrl-grid-full" style="margin-bottom:0">
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

      <div class="hero-bottom-save">
        <button type="button" class="tg-save-btn" onclick="saveHero()">💾 저장</button>
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
var _thumbBorder = '<?php echo addslashes($thumb_border); ?>';

function _applyBannerBg(){
  var banner = document.getElementById('hero-pv-card');
  if(!banner || !_thumbGrads[_thumbSelected]) return;
  var g = _thumbGrads[_thumbSelected];
  banner.classList.remove('carbon-bg');
  if(_thumbWave){
    var m = g.match(/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/g);
    if(m && m.length >= 2){
      banner.style.background = 'linear-gradient(135deg,'+m[0]+','+m[1]+','+(m[2]||m[0])+','+m[0]+','+m[1]+')';
      banner.style.backgroundSize = '400% 400%';
    } else {
      banner.style.background = g;
      banner.style.backgroundSize = '400% 400%';
    }
    banner.classList.add('pv-wave-active');
  } else {
    banner.style.background = g;
    banner.style.backgroundSize = '';
    banner.classList.remove('pv-wave-active');
    if(_thumbSelected === 'P3') banner.classList.add('carbon-bg');
  }
}

function _applyBorder(){
  var card = document.getElementById('hero-pv-card');
  if(!card) return;
  var borders = {gold:'#FFD700',pink:'#FF1B6B',charcoal:'#3a3a3a',royalblue:'#4169E1',royalpurple:'#7B2FBE'};
  if(borders[_thumbBorder]){
    card.style.boxShadow = 'inset 0 0 0 3px '+borders[_thumbBorder]+', 0 0 0 3px '+borders[_thumbBorder];
  } else {
    card.style.boxShadow = 'none';
  }
}

function updatePreview(){
  var title = document.getElementById('tg-title').value || '썸네일 제목';
  var text1 = document.getElementById('tg-text').value || '홍보문구1 입력';
  var text2El = document.getElementById('tg-text2');
  var text2 = text2El ? (text2El.value || '홍보문구2 입력') : '';

  var h2 = document.getElementById('hero-pv-h2');
  var p1 = document.getElementById('hero-pv-p1');
  var p2 = document.getElementById('hero-pv-p2');

  if(h2) h2.textContent = title;
  if(p1) p1.textContent = text1;
  if(p2) p2.textContent = text2;

  var titleSize  = document.getElementById('tg-title-size').value;
  var titleColor = document.getElementById('tg-title-color').value;
  var textSize   = document.getElementById('tg-text-size').value;
  var textColor  = document.getElementById('tg-text-color').value;
  var text2SizeEl = document.getElementById('tg-text2-size');
  var text2ColorEl = document.getElementById('tg-text2-color');
  var text2Size  = text2SizeEl ? text2SizeEl.value : '14px';
  var text2Color = text2ColorEl ? text2ColorEl.value : '#ffffff';

  if(h2){ h2.style.fontSize = titleSize; h2.style.color = titleColor; }
  if(p1){ p1.style.fontSize = textSize; p1.style.color = textColor; }
  if(p2){ p2.style.fontSize = text2Size; p2.style.color = text2Color; }

  var cnt1 = document.getElementById('tg-title-cnt');
  var cnt2 = document.getElementById('tg-text-cnt');
  var cnt3 = document.getElementById('tg-text2-cnt');
  if(cnt1) cnt1.textContent = Array.from(document.getElementById('tg-title').value).length;
  if(cnt2) cnt2.textContent = Array.from(document.getElementById('tg-text').value).length;
  if(cnt3 && text2El) cnt3.textContent = Array.from(text2El.value).length;
}

function setAlign(target, btn){
  var containerMap = {'title':'tg-title-align','text1':'tg-text-align','text2':'tg-text2-align'};
  var containerId = containerMap[target] || 'tg-title-align';
  document.querySelectorAll('#'+containerId+' .align-btn').forEach(function(b){ b.classList.remove('selected'); });
  btn.classList.add('selected');
  var align = btn.getAttribute('data-align');
  if(target === 'title'){
    var wrap = document.getElementById('hero-pv-text');
    if(wrap) wrap.style.textAlign = align;
  } else if(target === 'text1'){
    var p1 = document.getElementById('hero-pv-p1');
    if(p1) p1.style.textAlign = align;
  } else if(target === 'text2'){
    var p2 = document.getElementById('hero-pv-p2');
    if(p2) p2.style.textAlign = align;
  }
}

function selectGrad(btn){
  document.querySelectorAll('.color-swatch').forEach(function(b){ b.classList.remove('selected'); });
  btn.classList.add('selected');
  _thumbSelected = btn.getAttribute('data-grad');
  _applyBannerBg();
}

function selectIcon(btn){
  document.querySelectorAll('#tg-icon-grid .badge-opt').forEach(function(b){ b.classList.remove('selected'); });
  btn.classList.add('selected');
  _thumbIcon = btn.getAttribute('data-icon') || '';
  var badge = document.getElementById('hero-pv-badge');
  if(badge){
    if(_thumbIcon){
      badge.style.display = '';
      badge.style.background = btn.getAttribute('data-icon-bg') || '#ccc';
      badge.textContent = btn.getAttribute('data-icon-label') || '';
    } else {
      badge.style.display = 'none';
    }
  }
}

function selectMotion(btn){
  document.querySelectorAll('#tg-motion-grid .motion-btn').forEach(function(b){ b.classList.remove('selected'); });
  btn.classList.add('selected');
  _thumbMotion = btn.getAttribute('data-motion') || '';
  var h2 = document.getElementById('hero-pv-h2');
  if(h2) h2.className = _thumbMotion ? 'pv-motion-'+_thumbMotion : '';
}

function toggleWave(checked){ _thumbWave = checked; _applyBannerBg(); }

function selectBorder(btn){
  document.querySelectorAll('#tg-border-grid .border-btn').forEach(function(b){ b.classList.remove('selected'); });
  btn.classList.add('selected');
  _thumbBorder = btn.getAttribute('data-border') || '';
  _applyBorder();
}

function searchJr(){
  var jrId = document.getElementById('hero-jr-id').value.trim();
  if(!jrId){ alert('jr_id를 입력하세요.'); return; }
  var xhr = new XMLHttpRequest();
  xhr.open('GET','./eve_special_banner_update.php?act=search&q='+encodeURIComponent(jrId)+'&type=hero&token=<?php echo $token; ?>');
  xhr.onload = function(){
    var box = document.getElementById('jrResult');
    if(xhr.status !== 200){ box.style.display='block'; box.innerHTML='<span style="color:red">검색 오류</span>'; return; }
    try{ var data = JSON.parse(xhr.responseText); }catch(e){ box.style.display='block'; box.innerHTML='<span style="color:red">파싱 오류</span>'; return; }
    if(!data.length){ box.style.display='block'; box.innerHTML='<span style="color:red">해당 jr_id의 진행중인 광고를 찾을 수 없습니다.</span>'; return; }
    var r = data[0];
    box.style.display = 'block';
    box.innerHTML = '✅ <b>#'+r.jr_id+'</b> '+escHtml(r.jr_company||'—')+' · '+escHtml(r.jr_nickname||'—')+' · '+escHtml(r.mb_id||'—')+' · 남은기간: '+r.remaining;
  };
  xhr.send();
}

function saveHero(){
  var btn = document.getElementById('tg-save-btn'); if(btn) btn.disabled = true;

  var titleAlign = 'left', textAlign = 'left', text2Align = 'left';
  var ta = document.querySelector('#tg-title-align .align-btn.selected');
  if(ta) titleAlign = ta.getAttribute('data-align');
  var xa = document.querySelector('#tg-text-align .align-btn.selected');
  if(xa) textAlign = xa.getAttribute('data-align');
  var x2a = document.querySelector('#tg-text2-align .align-btn.selected');
  if(x2a) text2Align = x2a.getAttribute('data-align');

  var form = document.createElement('form');
  form.method = 'POST';
  form.action = './eve_special_banner_update.php';
  var fields = {
    act: 'save_hero',
    sb_id: '<?php echo $sb_id; ?>',
    jr_id: document.getElementById('hero-jr-id').value.trim(),
    link: document.getElementById('hero-link').value.trim(),
    position: document.getElementById('hero-position').value,
    memo: document.getElementById('hero-memo').value,
    thumb_gradient: _thumbSelected || '1',
    thumb_title: (document.getElementById('tg-title') || {}).value || '',
    thumb_text: (document.getElementById('tg-text') || {}).value || '',
    thumb_text2: (document.getElementById('tg-text2') || {}).value || '',
    thumb_icon: _thumbIcon || '',
    thumb_motion: _thumbMotion || '',
    thumb_wave: _thumbWave ? '1' : '0',
    thumb_text_color: document.getElementById('tg-title-color').value || '#ffffff',
    thumb_border: _thumbBorder || '',
    title_size: document.getElementById('tg-title-size').value || '30px',
    title_align: titleAlign,
    text_size: document.getElementById('tg-text-size').value || '14px',
    text_color: document.getElementById('tg-text-color').value || '#ffffff',
    text_align: textAlign,
    text2_size: (document.getElementById('tg-text2-size') || {}).value || '14px',
    text2_color: (document.getElementById('tg-text2-color') || {}).value || '#ffffff',
    text2_align: text2Align,
    token: '<?php echo $token; ?>'
  };
  for(var k in fields){
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = k; inp.value = fields[k];
    form.appendChild(inp);
  }
  document.body.appendChild(form);
  form.submit();
}

function escHtml(s){ var d=document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

_applyBorder();
</script>

<?php
require_once './admin.tail.php';
