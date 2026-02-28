<?php
// /plugin/chat/eve_chat_box.php — 이브알바 실시간 채팅 프론트엔드
if (!defined('_GNUBOARD_')) exit;
include_once(G5_PLUGIN_PATH.'/chat/_common.php');

$_chat_admin = (isset($is_admin) && $is_admin) ? true : false;
$_chat_ajax  = G5_PLUGIN_URL.'/chat/chat_ajax.php';
$_chat_member = (isset($member) && isset($member['mb_id']) && $member['mb_id']);
$_chat_can   = false;
$_chat_deny  = '';
if (!$_chat_member) {
    $_chat_deny = 'login';
} else {
    $type = isset($member['mb_1']) ? $member['mb_1'] : '';
    $sex  = isset($member['mb_sex']) ? $member['mb_sex'] : '';
    if ($type === 'normal' && $sex === 'F') {
        $_chat_can = true;
    } else {
        $_chat_deny = 'denied';
    }
}

$_chat_cfg = sql_fetch(" SELECT * FROM {$g5['chat_config_table']} LIMIT 1 ");
$_chat_notice = '';
if (isset($_chat_cfg['cf_notice_text']) && $_chat_cfg['cf_notice_text'] !== '') {
    $_chat_notice = $_chat_cfg['cf_notice_text'];
}
?>
<style>
/* ===================== EVE ALBA CHAT ===================== */
:root {
  --ec-hot-pink:#FF1B6B;--ec-deep-pink:#C90050;--ec-light-pink:#FF6BA8;
  --ec-pale-pink:#FFD6E7;--ec-dark:#1A0010;--ec-dark2:#2D0020;
  --ec-gold:#FFD700;--ec-orange:#FF6B35;--ec-bg:#FFF0F5;--ec-chat-bg:#FDF5F9;
  --ec-white:#ffffff;--ec-gray:#888;--ec-border:#F0E0E8;
  --ec-radius:16px;--ec-shadow:0 8px 32px rgba(255,27,107,.15);
}
#eveChatWindow{
  position:fixed;bottom:90px;right:28px;z-index:1100;
  width:360px;max-width:calc(100vw - 24px);max-height:calc(100vh - 110px);
  background:var(--ec-white);border-radius:var(--ec-radius);
  box-shadow:var(--ec-shadow);overflow:hidden;display:none;
  flex-direction:column;border:1.5px solid var(--ec-border);
  animation:ecSlideUp .3s ease both;
}
#eveChatWindow.ec-open{display:flex;}
@keyframes ecSlideUp{from{opacity:0;transform:translateY(20px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.ec-header{
  background:linear-gradient(135deg,var(--ec-dark2),var(--ec-hot-pink));
  padding:14px 16px 12px;display:flex;align-items:center;gap:10px;flex-shrink:0;position:relative;overflow:hidden;
}
.ec-header::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.06),transparent);animation:ecShimmer 3s linear infinite}
@keyframes ecShimmer{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
.ec-hdr-icon{font-size:22px;flex-shrink:0}
.ec-hdr-info{flex:1;min-width:0}
.ec-hdr-title{color:var(--ec-white);font-size:15px;font-weight:900;line-height:1.2;display:flex;align-items:center;gap:7px}
.ec-region-btn{
  display:inline-flex;align-items:center;gap:4px;
  background:rgba(255,255,255,.25);border-radius:10px;padding:2px 9px;
  font-size:12px;font-weight:700;color:var(--ec-white);backdrop-filter:blur(4px);
  border:1px solid rgba(255,255,255,.3);cursor:pointer;transition:background .2s;white-space:nowrap;
}
.ec-region-btn:hover{background:rgba(255,255,255,.35)}
.ec-region-btn .ec-arrow{font-size:9px;margin-left:2px;transition:transform .25s;display:inline-block}
.ec-region-btn.ec-open .ec-arrow{transform:rotate(180deg)}
.ec-hdr-sub{color:rgba(255,255,255,.75);font-size:11px;margin-top:3px;display:flex;align-items:center;gap:6px}
.ec-online-dot{width:7px;height:7px;background:#4ADE80;border-radius:50%;animation:ecBlink 1.5s ease-in-out infinite;flex-shrink:0}
@keyframes ecBlink{0%,100%{opacity:1}50%{opacity:.3}}
.ec-hdr-actions{display:flex;gap:6px;flex-shrink:0}
.ec-icon-btn{
  width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.18);
  border:1px solid rgba(255,255,255,.25);color:var(--ec-white);font-size:14px;
  display:flex;align-items:center;justify-content:center;transition:background .2s;
  backdrop-filter:blur(4px);cursor:pointer;
}
.ec-icon-btn:hover{background:rgba(255,255,255,.32)}
.ec-icon-btn--text{width:auto;border-radius:14px;padding:0 10px;font-size:11px;font-weight:700;letter-spacing:.3px;white-space:nowrap}
.ec-close-btn{
  width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.18);
  border:1px solid rgba(255,255,255,.25);color:var(--ec-white);font-size:15px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;
}
.ec-close-btn:hover{background:rgba(255,255,255,.35)}

/* Region Dropdown */
.ec-region-dd{background:var(--ec-white);border-bottom:2px solid var(--ec-border);overflow:hidden;max-height:0;transition:max-height .3s cubic-bezier(.4,0,.2,1);flex-shrink:0}
.ec-region-dd.ec-open{max-height:200px}
.ec-region-dd-inner{padding:12px 14px 14px}
.ec-rd-title{font-size:11px;font-weight:700;color:var(--ec-gray);margin-bottom:9px;letter-spacing:.5px;display:flex;align-items:center;gap:5px}
.ec-region-grid{display:flex;flex-wrap:wrap;gap:6px}
.ec-chip{
  padding:5px 12px;border-radius:20px;border:1.5px solid var(--ec-border);
  background:var(--ec-white);font-size:12px;font-weight:700;color:#666;
  cursor:pointer;transition:all .18s;white-space:nowrap;position:relative;overflow:hidden;
}
.ec-chip::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--ec-orange),var(--ec-hot-pink));opacity:0;transition:opacity .18s}
.ec-chip span{position:relative;z-index:1}
.ec-chip:hover{border-color:var(--ec-light-pink);color:var(--ec-hot-pink)}
.ec-chip.active{border-color:transparent;color:var(--ec-white);box-shadow:0 2px 8px rgba(255,27,107,.3)}
.ec-chip.active::before{opacity:1}
.ec-chip-all{background:linear-gradient(135deg,var(--ec-dark2),var(--ec-hot-pink));color:var(--ec-white);border-color:transparent;box-shadow:0 2px 8px rgba(255,27,107,.3)}
.ec-chip-all::before{display:none}
.ec-rd-count{
  margin-top:10px;padding:7px 12px;
  background:linear-gradient(135deg,#fff5f8,#fff0f5);border-radius:10px;
  border:1.5px solid var(--ec-pale-pink);display:flex;align-items:center;gap:7px;font-size:12px;color:#666;
}
.ec-rd-count strong{color:var(--ec-hot-pink);font-weight:900;font-size:14px}

/* Notice */
.ec-notice-wrap{padding:10px 12px 0;background:var(--ec-chat-bg);flex-shrink:0}
.ec-notice{
  background:linear-gradient(135deg,#fff8f0,#fff0e8);border:1.5px solid #FFD0AA;
  border-radius:10px;padding:8px 12px;display:flex;align-items:flex-start;gap:7px;
}
.ec-notice-icon{font-size:14px;flex-shrink:0;margin-top:1px}
.ec-notice-text{font-size:11px;color:#AA5500;line-height:1.6}
.ec-notice-text strong{font-weight:700}

/* Messages */
.ec-messages{
  flex:1;overflow-y:auto;padding:14px 12px;background:var(--ec-chat-bg);
  display:flex;flex-direction:column;gap:4px;min-height:200px;scroll-behavior:smooth;
}
.ec-messages::-webkit-scrollbar{width:4px}
.ec-messages::-webkit-scrollbar-track{background:transparent}
.ec-messages::-webkit-scrollbar-thumb{background:var(--ec-pale-pink);border-radius:2px}

.ec-system{
  text-align:center;font-size:11px;color:var(--ec-gray);padding:4px 12px;
  background:rgba(255,255,255,.7);border-radius:10px;align-self:center;
  border:1px solid var(--ec-border);margin:2px 0;
}
.ec-msg-row{display:flex;align-items:flex-end;gap:6px;animation:ecMsgIn .2s ease both}
@keyframes ecMsgIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.ec-msg-row.ec-me{flex-direction:row-reverse}
.ec-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,var(--ec-pale-pink),var(--ec-light-pink));
  display:flex;align-items:center;justify-content:center;font-size:18px;
  flex-shrink:0;border:2px solid var(--ec-white);box-shadow:0 2px 8px rgba(255,27,107,.15);align-self:flex-end;
}
.ec-avatar.ec-me-av{background:linear-gradient(135deg,var(--ec-orange),var(--ec-hot-pink))}
.ec-avatar.ec-admin-av{background:linear-gradient(135deg,var(--ec-dark2),var(--ec-hot-pink))}
.ec-msg-content{max-width:230px;display:flex;flex-direction:column;gap:3px}
.ec-msg-row.ec-me .ec-msg-content{align-items:flex-end}
.ec-msg-name{font-size:11px;font-weight:700;color:#666;display:flex;align-items:center;gap:5px;padding:0 4px}
.ec-msg-name .ec-rtag{background:var(--ec-pale-pink);color:var(--ec-hot-pink);border-radius:6px;padding:1px 6px;font-size:10px;font-weight:700}
.ec-bubble{padding:9px 13px;border-radius:16px;font-size:13px;line-height:1.6;word-break:break-word;max-width:100%}
.ec-bubble.ec-other{background:var(--ec-white);color:var(--ec-dark);border-radius:4px 16px 16px 16px;box-shadow:0 2px 8px rgba(0,0,0,.06);border:1.5px solid var(--ec-border)}
.ec-bubble.ec-me-b{background:linear-gradient(135deg,var(--ec-orange),var(--ec-hot-pink));color:var(--ec-white);border-radius:16px 4px 16px 16px;box-shadow:0 3px 12px rgba(255,27,107,.3)}
.ec-msg-time{font-size:10px;color:#bbb;white-space:nowrap;padding:0 4px}

/* Input */
.ec-input-area{border-top:2px solid var(--ec-border);background:var(--ec-white);padding:10px 12px;flex-shrink:0}
.ec-input-row{
  display:flex;align-items:flex-end;gap:8px;background:#fdf5f9;
  border:1.5px solid var(--ec-border);border-radius:24px;padding:8px 8px 8px 14px;
  transition:border-color .2s,box-shadow .2s;
}
.ec-input-row:focus-within{border-color:var(--ec-hot-pink);box-shadow:0 0 0 3px rgba(255,27,107,.08)}
.ec-input{
  flex:1;border:none;background:transparent;font-size:13px;color:var(--ec-dark);
  resize:none;outline:none;max-height:80px;min-height:22px;line-height:1.5;font-family:'Noto Sans KR',sans-serif;
}
.ec-input::placeholder{color:#bbb}
.ec-send-btn{
  width:38px;height:38px;border-radius:50%;
  background:linear-gradient(135deg,var(--ec-orange),var(--ec-hot-pink));
  border:none;color:var(--ec-white);font-size:16px;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
  transition:transform .2s,box-shadow .2s;box-shadow:0 3px 10px rgba(255,27,107,.3);cursor:pointer;
}
.ec-send-btn:hover{transform:scale(1.08);box-shadow:0 5px 16px rgba(255,27,107,.45)}
.ec-send-btn:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
.ec-input-hint{margin-top:6px;font-size:10px;color:#ccc;text-align:center}
.ec-status{display:none;padding:6px 10px;font-size:11px;color:#e53935;background:#fff5f5;border-top:1px solid var(--ec-border);text-align:center}

/* Login/Deny Wall */
.ec-wall{
  flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:32px 20px;text-align:center;background:var(--ec-chat-bg);gap:14px;
}
.ec-wall-icon{font-size:48px;margin-bottom:4px}
.ec-wall-title{font-size:16px;font-weight:900;color:var(--ec-dark)}
.ec-wall-sub{font-size:13px;color:var(--ec-gray);line-height:1.7}
.ec-wall-btn{
  padding:11px 28px;background:linear-gradient(135deg,var(--ec-orange),var(--ec-hot-pink));
  color:var(--ec-white);border:none;border-radius:24px;font-size:14px;font-weight:900;
  box-shadow:0 4px 14px rgba(255,27,107,.3);cursor:pointer;transition:transform .2s;
}
.ec-wall-btn:hover{transform:scale(1.04)}

/* Modal */
.ec-modal-overlay{
  position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;
  display:none;align-items:center;justify-content:center;padding:16px;
}
.ec-modal-overlay.ec-show{display:flex}
.ec-modal-box{
  background:var(--ec-white);border-radius:18px;width:100%;max-width:360px;overflow:hidden;
  box-shadow:0 20px 60px rgba(0,0,0,.25);animation:ecModalIn .25s ease both;max-height:85vh;display:flex;flex-direction:column;
}
@keyframes ecModalIn{from{opacity:0;transform:scale(.94) translateY(12px)}to{opacity:1;transform:scale(1) translateY(0)}}
.ec-modal-head{
  background:linear-gradient(135deg,var(--ec-dark2),var(--ec-hot-pink));
  padding:16px 18px;display:flex;align-items:center;gap:10px;flex-shrink:0;
}
.ec-modal-head-icon{font-size:22px}
.ec-modal-head-title{color:var(--ec-white);font-size:15px;font-weight:900;flex:1}
.ec-modal-close{
  width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.2);
  border:1px solid rgba(255,255,255,.3);color:var(--ec-white);font-size:13px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;
}
.ec-modal-close:hover{background:rgba(255,255,255,.38)}
.ec-modal-body{padding:18px;overflow-y:auto;flex:1}
.ec-modal-body::-webkit-scrollbar{width:4px}
.ec-modal-body::-webkit-scrollbar-thumb{background:var(--ec-pale-pink);border-radius:2px}
.ec-rule-item{display:flex;gap:10px;align-items:flex-start;padding:11px 13px;border-radius:12px;background:#fff8fb;border:1.5px solid var(--ec-border);margin-bottom:8px}
.ec-rule-num{
  width:22px;height:22px;border-radius:50%;
  background:linear-gradient(135deg,var(--ec-orange),var(--ec-hot-pink));
  color:var(--ec-white);font-size:11px;font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.ec-rule-text{font-size:12px;color:#444;line-height:1.7}
.ec-rule-text strong{color:var(--ec-hot-pink);font-weight:700}
.ec-rules-footer{margin-top:14px;padding:10px 13px;background:linear-gradient(135deg,#fff0e8,#fff8f0);border:1.5px solid #FFD0AA;border-radius:10px;font-size:11px;color:#AA5500;line-height:1.7;text-align:center}

/* Nick menu (report/ignore) */
.ec-nick-menu{
  position:fixed;z-index:2100;min-width:140px;background:var(--ec-white);
  border:1.5px solid var(--ec-border);border-radius:12px;box-shadow:0 6px 24px rgba(0,0,0,.18);
  padding:6px;display:none;font-size:12px;
}
.ec-nick-menu-item{
  padding:8px 12px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .15s;
}
.ec-nick-menu-item:hover{background:#fff0f5}
.ec-nick-menu-item.ec-danger{color:#e53935}
.ec-nick-menu-item.ec-danger:hover{background:#ffebee}

/* Report modal */
.ec-report-reasons{display:flex;flex-direction:column;gap:6px;margin:12px 0}
.ec-report-reason{
  padding:10px 14px;border-radius:10px;border:1.5px solid var(--ec-border);
  background:var(--ec-white);cursor:pointer;font-size:13px;transition:all .15s;
}
.ec-report-reason:hover{border-color:var(--ec-hot-pink);background:#fff5f8}
.ec-report-reason.selected{border-color:var(--ec-hot-pink);background:#fff0f5;font-weight:700;color:var(--ec-hot-pink)}

/* Mobile */
@media(max-width:768px){
  #eveChatWindow{width:calc(100vw - 16px);right:8px;bottom:80px;max-height:calc(100vh - 100px)}
}
</style>

<!-- CHAT WINDOW -->
<div id="eveChatWindow">
  <!-- Header -->
  <div class="ec-header">
    <div class="ec-hdr-icon">💬</div>
    <div class="ec-hdr-info">
      <div class="ec-hdr-title">
        실시간 채팅
        <button class="ec-region-btn" id="ecRegionToggle">
          <span id="ecCurrentRegion">전체</span>
          <span class="ec-arrow" id="ecRegionArrow">▼</span>
        </button>
      </div>
      <div class="ec-hdr-sub">
        <span class="ec-online-dot"></span>
        <span>👩 <span id="ecOnlineNum">0</span>명 접속 중</span>
      </div>
    </div>
    <div class="ec-hdr-actions">
      <button class="ec-icon-btn" title="새로고침" id="ecRefresh">🔄</button>
      <button class="ec-icon-btn ec-icon-btn--text" title="채팅규정" id="ecRulesBtn">채팅규정</button>
      <button class="ec-close-btn" title="닫기" id="ecCloseBtn">✕</button>
    </div>
  </div>

  <!-- Region Dropdown -->
  <div class="ec-region-dd" id="ecRegionDD">
    <div class="ec-region-dd-inner">
      <div class="ec-rd-title">📍 지역을 선택하세요</div>
      <div class="ec-region-grid" id="ecRegionGrid">
        <button class="ec-chip ec-chip-all active" data-region="전체"><span>전체</span></button>
        <button class="ec-chip" data-region="서울"><span>서울</span></button>
        <button class="ec-chip" data-region="경기"><span>경기</span></button>
        <button class="ec-chip" data-region="인천"><span>인천</span></button>
        <button class="ec-chip" data-region="부산"><span>부산</span></button>
        <button class="ec-chip" data-region="대구"><span>대구</span></button>
        <button class="ec-chip" data-region="광주"><span>광주</span></button>
        <button class="ec-chip" data-region="대전"><span>대전</span></button>
        <button class="ec-chip" data-region="울산"><span>울산</span></button>
        <button class="ec-chip" data-region="강원"><span>강원</span></button>
        <button class="ec-chip" data-region="경남"><span>경남</span></button>
        <button class="ec-chip" data-region="경북"><span>경북</span></button>
        <button class="ec-chip" data-region="전남"><span>전남</span></button>
        <button class="ec-chip" data-region="세종"><span>세종</span></button>
        <button class="ec-chip" data-region="제주"><span>제주</span></button>
      </div>
      <div class="ec-rd-count">
        <span>👩</span>
        <span id="ecRdCountText"><strong id="ecRdCountNum">0</strong>명이 <strong>전체</strong> 채팅 중</span>
      </div>
    </div>
  </div>

  <?php if ($_chat_deny === 'login') { ?>
  <!-- 비로그인 -->
  <div class="ec-wall">
    <div class="ec-wall-icon">🔒</div>
    <div class="ec-wall-title">로그인이 필요합니다</div>
    <div class="ec-wall-sub">이브알바 실시간 채팅은<br>일반회원(여성)만 이용 가능합니다.</div>
    <button class="ec-wall-btn" onclick="location.href='<?php echo G5_BBS_URL; ?>/login.php'">로그인</button>
  </div>
  <?php } else if ($_chat_deny === 'denied') { ?>
  <!-- 권한 없음 -->
  <div class="ec-wall">
    <div class="ec-wall-icon">🚫</div>
    <div class="ec-wall-title">이용이 제한됩니다</div>
    <div class="ec-wall-sub">실시간 채팅은<br><strong>일반회원(여성)</strong>만 이용 가능합니다.</div>
  </div>
  <?php } else { ?>
  <!-- 공지 배너 -->
  <?php if ($_chat_notice) { ?>
  <div class="ec-notice-wrap" id="ecNoticeWrap">
    <div class="ec-notice">
      <span class="ec-notice-icon">📢</span>
      <div class="ec-notice-text"><strong>[공지]</strong> <?php echo nl2br(htmlspecialchars($_chat_notice)); ?></div>
    </div>
  </div>
  <?php } ?>

  <!-- Messages -->
  <div class="ec-messages" id="ecMessages">
    <div class="ec-system">💗 이브알바 채팅방에 오신 것을 환영합니다!</div>
  </div>

  <!-- Status -->
  <div class="ec-status" id="ecStatus"></div>

  <!-- Input -->
  <div class="ec-input-area">
    <div class="ec-input-row">
      <textarea class="ec-input" id="ecInput" placeholder="메시지를 입력하세요 (Enter 전송)" rows="1" autocomplete="off"></textarea>
      <button class="ec-send-btn" id="ecSendBtn">➤</button>
    </div>
    <div class="ec-input-hint">Enter 전송 · Shift+Enter 줄바꿈</div>
  </div>
  <?php } ?>
</div>

<!-- 채팅규정 모달 -->
<div class="ec-modal-overlay" id="ecRulesModal">
  <div class="ec-modal-box">
    <div class="ec-modal-head">
      <span class="ec-modal-head-icon">📋</span>
      <span class="ec-modal-head-title">채팅 규정</span>
      <button class="ec-modal-close" onclick="document.getElementById('ecRulesModal').classList.remove('ec-show')">✕</button>
    </div>
    <div class="ec-modal-body">
      <div class="ec-rule-item"><div class="ec-rule-num">1</div><div class="ec-rule-text"><strong>욕설·비방 금지</strong><br>다른 이용자를 향한 욕설, 비방, 인신공격은 즉시 이용 제한됩니다.</div></div>
      <div class="ec-rule-item"><div class="ec-rule-num">2</div><div class="ec-rule-text"><strong>광고·스팸 금지</strong><br>허가되지 않은 광고, 홍보, 스팸 메시지 작성은 금지됩니다.</div></div>
      <div class="ec-rule-item"><div class="ec-rule-num">3</div><div class="ec-rule-text"><strong>도배 금지</strong><br>같은 내용의 반복 작성(도배)은 자동으로 차단됩니다.</div></div>
      <div class="ec-rule-item"><div class="ec-rule-num">4</div><div class="ec-rule-text"><strong>개인정보 보호</strong><br>자신 또는 타인의 연락처·주소 등 개인정보를 공개하지 마세요.</div></div>
      <div class="ec-rule-item"><div class="ec-rule-num">5</div><div class="ec-rule-text"><strong>음란·불법 콘텐츠 금지</strong><br>음란물, 불법 정보 유포 시 법적 조치가 취해질 수 있습니다.</div></div>
      <div class="ec-rule-item"><div class="ec-rule-num">6</div><div class="ec-rule-text"><strong>분쟁 유발 금지</strong><br>다른 이용자와의 의도적 분쟁 유발, 타인 비하 표현을 삼가주세요.</div></div>
      <div class="ec-rule-item"><div class="ec-rule-num">7</div><div class="ec-rule-text"><strong>미성년자 이용 불가</strong><br>본 채팅 서비스는 만 18세 이상만 이용할 수 있습니다.</div></div>
      <div class="ec-rules-footer">⚠️ 규정 위반 시 <strong>경고 → 일시정지 → 영구정지</strong> 순으로 제재됩니다.</div>
    </div>
  </div>
</div>

<!-- 신고 모달 -->
<div class="ec-modal-overlay" id="ecReportModal">
  <div class="ec-modal-box">
    <div class="ec-modal-head">
      <span class="ec-modal-head-icon">🚨</span>
      <span class="ec-modal-head-title">신고하기 — <span id="ecReportTarget"></span></span>
      <button class="ec-modal-close" onclick="document.getElementById('ecReportModal').classList.remove('ec-show')">✕</button>
    </div>
    <div class="ec-modal-body">
      <div style="font-size:12px;color:var(--ec-gray);margin-bottom:8px;">신고 사유를 선택해 주세요.</div>
      <div class="ec-report-reasons" id="ecReportReasons">
        <div class="ec-report-reason" data-reason="욕설/비방">🤬 욕설·비방</div>
        <div class="ec-report-reason" data-reason="광고/스팸">📢 광고·스팸</div>
        <div class="ec-report-reason" data-reason="도배">🔁 도배</div>
        <div class="ec-report-reason" data-reason="음란/불법">🔞 음란·불법 콘텐츠</div>
        <div class="ec-report-reason" data-reason="개인정보 노출">🔓 개인정보 노출</div>
        <div class="ec-report-reason" data-reason="기타">📝 기타</div>
      </div>
      <button class="ec-wall-btn" id="ecReportSubmit" style="width:100%;margin-top:8px" disabled>신고 접수</button>
    </div>
  </div>
</div>

<!-- 닉네임 클릭 메뉴 -->
<div class="ec-nick-menu" id="ecNickMenu">
  <div class="ec-nick-menu-item" id="ecMenuIgnore">🙈 무시하기</div>
  <div class="ec-nick-menu-item ec-danger" id="ecMenuReport">🚨 신고하기</div>
</div>

<?php if ($_chat_can) { ?>
<script>
(function(){
  var AJAX = "<?php echo $_chat_ajax; ?>";
  var MY_ID = "<?php echo addslashes($my_id); ?>";
  var MY_NICK = "<?php echo addslashes($my_nick); ?>";
  var IS_ADMIN = <?php echo $_chat_admin ? 1 : 0; ?>;

  var state = { last_id:0, region:'전체', freeze:0, sending:false, lastSendTs:0 };
  var pollTimer = null, pingTimer = null, idleTimer = null;
  var lastActiveTs = Date.now(), stoppedByIdle = false;

  var el = {
    win: document.getElementById('eveChatWindow'),
    msgs: document.getElementById('ecMessages'),
    input: document.getElementById('ecInput'),
    sendBtn: document.getElementById('ecSendBtn'),
    onlineNum: document.getElementById('ecOnlineNum'),
    status: document.getElementById('ecStatus'),
    regionBtn: document.getElementById('ecRegionToggle'),
    regionDD: document.getElementById('ecRegionDD'),
    regionLabel: document.getElementById('ecCurrentRegion'),
    rdCountNum: document.getElementById('ecRdCountNum'),
    rdCountText: document.getElementById('ecRdCountText'),
    nickMenu: document.getElementById('ecNickMenu'),
    rulesBtn: document.getElementById('ecRulesBtn'),
    rulesModal: document.getElementById('ecRulesModal'),
    reportModal: document.getElementById('ecReportModal'),
    reportTarget: document.getElementById('ecReportTarget'),
    reportSubmit: document.getElementById('ecReportSubmit'),
    closeBtn: document.getElementById('ecCloseBtn'),
    refreshBtn: document.getElementById('ecRefresh')
  };

  var IGNORE_KEY = 'eve_chat_ignored_v1';
  var JOIN_LAST_ID_KEY = 'eve_chat_join_last_id_v1';
  var menuTarget = {mb_id:'',nick:''};
  var reportReason = '';

  function loadIgnored(){ try{ var a=JSON.parse(localStorage.getItem(IGNORE_KEY)||'[]'); return Array.isArray(a)?a:[]; }catch(e){return[];} }
  function saveIgnored(a){ try{localStorage.setItem(IGNORE_KEY,JSON.stringify(a||[]));}catch(e){} }
  function isIgnored(id){ return loadIgnored().indexOf(String(id))>=0; }
  function toggleIgnore(id){
    id=String(id||'');if(!id) return;
    var a=loadIgnored(),idx=a.indexOf(id),ign;
    if(idx>=0){a.splice(idx,1);ign=false;}else{a.push(id);ign=true;}
    saveIgnored(a);
    var nodes=el.msgs.querySelectorAll('.ec-msg-row[data-mb="'+id+'"]');
    nodes.forEach(function(n){n.style.display=ign?'none':'';});
    return ign;
  }

  function loadJoinLastId(){ try{var v=localStorage.getItem(JOIN_LAST_ID_KEY);return v?parseInt(v,10):null;}catch(e){return null;} }
  function saveJoinLastId(id){ try{localStorage.setItem(JOIN_LAST_ID_KEY,String(id));}catch(e){} }
  var savedJoin = loadJoinLastId();
  if(savedJoin!==null) state.last_id = savedJoin;

  function escHtml(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
  function setStatus(t){
    if(!el.status) return;
    if(!t){el.status.style.display='none';el.status.textContent='';return;}
    el.status.textContent=t;el.status.style.display='block';
  }

  function fmtTime(dt){
    if(!dt) return '';
    var d = new Date(dt.replace(/-/g,'/'));
    var h = String(d.getHours()).padStart(2,'0');
    var m = String(d.getMinutes()).padStart(2,'0');
    return h+':'+m;
  }

  function appendMessages(list){
    if(!el.msgs || !Array.isArray(list)) return;
    list.forEach(function(row){
      var cid = parseInt(row.cm_id||0,10)||0;
      state.last_id = Math.max(state.last_id, cid);
      var mbid = row.mb_id||'';
      if(mbid && isIgnored(mbid)) return;

      var isMe = (mbid === MY_ID);
      var r = document.createElement('div');
      r.className = 'ec-msg-row'+(isMe?' ec-me':'');
      r.dataset.mb = mbid;

      var av = document.createElement('div');
      av.className = 'ec-avatar'+(isMe?' ec-me-av':'');
      av.textContent = '👩';

      var content = document.createElement('div');
      content.className = 'ec-msg-content';

      if(!isMe){
        var name = document.createElement('div');
        name.className = 'ec-msg-name';
        name.innerHTML = '👩 <strong>'+escHtml(row.cm_nick||'')+'</strong>'
          + (row.cm_region && row.cm_region !== '전체' ? '<span class="ec-rtag">'+escHtml(row.cm_region)+'</span>' : '');
        name.style.cursor = 'pointer';
        name.dataset.mb = mbid;
        name.dataset.nick = row.cm_nick||'';
        name.addEventListener('click', function(ev){
          ev.preventDefault(); ev.stopPropagation();
          if(!this.dataset.mb) return;
          showNickMenu(ev.clientX, ev.clientY, this.dataset.mb, this.dataset.nick);
        });
        content.appendChild(name);
      }

      var bwrap = document.createElement('div');
      bwrap.style.cssText = 'display:flex;align-items:flex-end;gap:5px;'+(isMe?'flex-direction:row-reverse;':'');

      var bubble = document.createElement('div');
      bubble.className = 'ec-bubble '+(isMe?'ec-me-b':'ec-other');
      bubble.innerHTML = escHtml(row.cm_content||'').replace(/\n/g,'<br>');

      var time = document.createElement('div');
      time.className = 'ec-msg-time';
      time.textContent = fmtTime(row.cm_datetime);

      bwrap.appendChild(bubble);
      bwrap.appendChild(time);
      content.appendChild(bwrap);
      r.appendChild(av);
      r.appendChild(content);
      el.msgs.appendChild(r);
    });
    el.msgs.scrollTop = el.msgs.scrollHeight;
  }

  function addSysMsg(text){
    var s = document.createElement('div');
    s.className = 'ec-system';
    s.textContent = text;
    el.msgs.appendChild(s);
    el.msgs.scrollTop = el.msgs.scrollHeight;
  }

  // AJAX
  function chatLoad(){
    var url = AJAX+'?act=list&last_id='+encodeURIComponent(state.last_id)+'&region='+encodeURIComponent(state.region);
    fetch(url,{method:'GET',credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(j){
      if(!j||j.ok!==1){setStatus('연결 점검 중...');return;}
      setStatus('');
      if(typeof j.online_count!=='undefined'){
        var oc=parseInt(j.online_count,10);
        if(!isNaN(oc)){
          el.onlineNum.textContent=oc;
          el.rdCountNum.textContent=oc;
        }
      }
      var pf=state.freeze, nf=(j.freeze==1?1:0);
      if(pf!==nf){
        state.freeze=nf;
        addSysMsg(nf?'⚠️ 운영자가 채팅을 동결했습니다.':'✅ 채팅 동결이 해제되었습니다.');
      }
      if(j.freeze==1){el.input.disabled=true;el.sendBtn.disabled=true;}
      else{el.input.disabled=false;el.sendBtn.disabled=false;}
      appendMessages(j.list||[]);
    })
    .catch(function(){setStatus('서버 연결이 불안정합니다.');});
  }

  function chatPing(){
    fetch(AJAX+'?act=ping&region='+encodeURIComponent(state.region),{method:'GET',credentials:'same-origin'}).catch(function(){});
  }

  function chatHello(forceReset, done){
    fetch(AJAX+'?act=hello&region='+encodeURIComponent(state.region),{method:'GET',credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(j){
      if(j&&j.ok===1){
        if(typeof j.freeze!=='undefined') state.freeze=(j.freeze==1?1:0);
        if(el.onlineNum&&typeof j.online_count!=='undefined'){
          var oc=parseInt(j.online_count,10);
          if(!isNaN(oc)){el.onlineNum.textContent=oc;el.rdCountNum.textContent=oc;}
        }
        if(typeof j.last_id!=='undefined'){
          var lid=parseInt(j.last_id,10);
          if(!isNaN(lid)&&lid>=0){
            if(forceReset){state.last_id=lid;saveJoinLastId(lid);}
            else if(savedJoin===null){state.last_id=lid;saveJoinLastId(lid);savedJoin=lid;}
          }
        }
      }
      if(typeof done==='function') done();
    })
    .catch(function(){if(typeof done==='function') done();});
  }

  function chatSend(){
    if(!el.input||el.input.disabled) return;
    if(state.sending) return;
    var now=Date.now();
    if(now-state.lastSendTs<1900){setStatus('연속 전송 제한(2초)입니다.');return;}
    var content=(el.input.value||'').trim();
    if(!content) return;
    markActive();
    state.sending=true;state.lastSendTs=now;

    fetch(AJAX,{
      method:'POST',credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body:'act=send&content='+encodeURIComponent(content)+'&region='+encodeURIComponent(state.region)
    })
    .then(function(r){return r.json();})
    .then(function(j){
      if(!j||j.ok!==1){if(j&&j.msg) setStatus(j.msg);else setStatus('전송 실패');return;}
      el.input.value='';el.input.style.height='auto';setStatus('');chatLoad();
    })
    .catch(function(){setStatus('서버 연결이 불안정합니다.');})
    .finally(function(){state.sending=false;});
  }

  // Loop control
  function startLoop(){
    if(pollTimer) clearInterval(pollTimer);
    if(pingTimer) clearInterval(pingTimer);
    chatPing();chatLoad();
    pollTimer=setInterval(chatLoad,4000);
    pingTimer=setInterval(chatPing,30000);
  }
  function stopLoop(msg){
    if(pollTimer){clearInterval(pollTimer);pollTimer=null;}
    if(pingTimer){clearInterval(pingTimer);pingTimer=null;}
    if(msg) setStatus(msg);
  }
  function markActive(){
    lastActiveTs=Date.now();
    if(stoppedByIdle){stoppedByIdle=false;setStatus('');startLoop();}
  }
  ['click','keydown','scroll','touchstart','mousemove'].forEach(function(evt){
    document.addEventListener(evt,markActive,{passive:true});
  });
  idleTimer=setInterval(function(){
    if(stoppedByIdle) return;
    if(Date.now()-lastActiveTs>=10*60*1000){
      stoppedByIdle=true;
      stopLoop('10분간 활동이 없어 연결이 중단되었습니다. 클릭하면 재연결됩니다.');
    }
  },5000);

  // Region toggle
  el.regionBtn.addEventListener('click',function(){
    var dd=el.regionDD, btn=this;
    if(dd.classList.contains('ec-open')){dd.classList.remove('ec-open');btn.classList.remove('ec-open');}
    else{dd.classList.add('ec-open');btn.classList.add('ec-open');}
  });

  el.regionDD.querySelectorAll('.ec-chip').forEach(function(chip){
    chip.addEventListener('click',function(){
      el.regionDD.querySelectorAll('.ec-chip').forEach(function(c){c.classList.remove('active');});
      this.classList.add('active');
      var newRegion = this.dataset.region;
      if(newRegion !== state.region){
        state.region = newRegion;
        state.last_id = 0;
        savedJoin = null;
        localStorage.removeItem(JOIN_LAST_ID_KEY);
        el.regionLabel.textContent = newRegion;
        el.rdCountText.innerHTML = '<strong>'+el.rdCountNum.textContent+'</strong>명이 <strong>'+escHtml(newRegion)+'</strong> 채팅 중';
        el.msgs.innerHTML = '';
        addSysMsg('📍 '+newRegion+' 채팅방으로 이동했습니다.');
        chatHello(true, function(){ startLoop(); });
      }
      setTimeout(function(){
        el.regionDD.classList.remove('ec-open');
        el.regionBtn.classList.remove('ec-open');
      },200);
    });
  });

  // Input
  el.input.addEventListener('keydown',function(e){
    if(e.isComposing||e.repeat) return;
    if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();e.stopPropagation();chatSend();}
  });
  el.input.addEventListener('input',function(){
    this.style.height='auto';this.style.height=Math.min(this.scrollHeight,80)+'px';
  });
  el.sendBtn.addEventListener('click',chatSend);

  // Close
  el.closeBtn.addEventListener('click',function(){
    el.win.classList.remove('ec-open');
  });

  // Refresh
  el.refreshBtn.addEventListener('click',function(){
    el.msgs.innerHTML='';
    addSysMsg('🔄 새로고침 중...');
    state.last_id=0;
    savedJoin=null;
    localStorage.removeItem(JOIN_LAST_ID_KEY);
    chatHello(true,function(){
      el.msgs.innerHTML='';
      addSysMsg('💗 이브알바 채팅방에 오신 것을 환영합니다!');
      chatLoad();
    });
  });

  // Rules
  el.rulesBtn.addEventListener('click',function(){
    document.getElementById('ecRulesModal').classList.add('ec-show');
  });
  document.getElementById('ecRulesModal').addEventListener('click',function(e){
    if(e.target===this) this.classList.remove('ec-show');
  });

  // Nick menu
  function showNickMenu(x,y,mbid,nick){
    menuTarget.mb_id=mbid;menuTarget.nick=nick;
    var m=el.nickMenu;
    m.style.display='block';
    var w=m.offsetWidth,h=m.offsetHeight;
    var px=Math.min(x,window.innerWidth-w-8);
    var py=Math.min(y,window.innerHeight-h-8);
    m.style.left=px+'px';m.style.top=py+'px';

    var ignBtn=document.getElementById('ecMenuIgnore');
    ignBtn.textContent=isIgnored(mbid)?'🙈 무시해제':'🙈 무시하기';
  }
  document.addEventListener('click',function(e){
    if(!el.nickMenu.contains(e.target)) el.nickMenu.style.display='none';
  },true);

  document.getElementById('ecMenuIgnore').addEventListener('click',function(){
    if(!menuTarget.mb_id) return;
    var ign=toggleIgnore(menuTarget.mb_id);
    addSysMsg(ign?'🙈 '+menuTarget.nick+'님을 무시합니다.':'✅ '+menuTarget.nick+'님의 무시를 해제했습니다.');
    el.nickMenu.style.display='none';
  });

  document.getElementById('ecMenuReport').addEventListener('click',function(){
    if(!menuTarget.mb_id) return;
    el.nickMenu.style.display='none';
    reportReason='';
    document.getElementById('ecReportTarget').textContent=menuTarget.nick;
    document.querySelectorAll('.ec-report-reason').forEach(function(r){r.classList.remove('selected');});
    document.getElementById('ecReportSubmit').disabled=true;
    document.getElementById('ecReportModal').classList.add('ec-show');
  });

  document.querySelectorAll('.ec-report-reason').forEach(function(r){
    r.addEventListener('click',function(){
      document.querySelectorAll('.ec-report-reason').forEach(function(rr){rr.classList.remove('selected');});
      this.classList.add('selected');
      reportReason=this.dataset.reason;
      document.getElementById('ecReportSubmit').disabled=false;
    });
  });

  document.getElementById('ecReportSubmit').addEventListener('click',function(){
    if(!reportReason||!menuTarget.mb_id) return;
    this.disabled=true;this.textContent='접수 중...';
    fetch(AJAX,{
      method:'POST',credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body:'act=report&target_id='+encodeURIComponent(menuTarget.mb_id)
          +'&target_nick='+encodeURIComponent(menuTarget.nick)
          +'&reason='+encodeURIComponent(reportReason)
    })
    .then(function(r){return r.json();})
    .then(function(j){
      addSysMsg('🚨 '+menuTarget.nick+'님을 신고했습니다.');
      document.getElementById('ecReportModal').classList.remove('ec-show');
    })
    .catch(function(){alert('신고 요청 중 오류가 발생했습니다.');})
    .finally(function(){
      document.getElementById('ecReportSubmit').disabled=false;
      document.getElementById('ecReportSubmit').textContent='신고 접수';
    });
  });

  document.getElementById('ecReportModal').addEventListener('click',function(e){
    if(e.target===this) this.classList.remove('ec-show');
  });

  // Global toggle function
  window.toggleEveChat = function(){
    if(el.win.classList.contains('ec-open')){
      el.win.classList.remove('ec-open');
    } else {
      el.win.classList.add('ec-open');
      setTimeout(function(){el.msgs.scrollTop=el.msgs.scrollHeight;},50);
    }
  };

  // Start
  chatHello(false, function(){ startLoop(); });
})();
</script>
<?php } else { ?>
<script>
window.toggleEveChat = function(){
  var w = document.getElementById('eveChatWindow');
  if(w.classList.contains('ec-open')) w.classList.remove('ec-open');
  else w.classList.add('ec-open');
};
</script>
<?php } ?>
