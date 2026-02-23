<?php
// /plugin/chat/chat_box.php
if (!defined('_GNUBOARD_')) exit;

include_once(G5_PLUGIN_PATH.'/chat/_common.php');

// 관리자 여부
$is_chat_admin = (isset($is_admin) && $is_admin) ? true : false;

// 채팅 AJAX URL
$CHAT_AJAX_URL = G5_PLUGIN_URL.'/chat/chat_ajax.php';
$tbl_cfg = isset($g5['chat_config_table']) ? $g5['chat_config_table'] : (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX.'chat_config' : 'g5_chat_config');
$SP_FOLLOW_TOGGLE_URL = (defined('G5_THEME_URL') ? G5_THEME_URL : (G5_URL.'/theme/evealba')).'/ajax/ajax_follow_toggle.php';

// 공용 설정 로드
$cfg = sql_fetch(" SELECT * FROM `{$tbl_cfg}` LIMIT 1 ");
$cf_title      = isset($cfg['cf_title']) ? $cfg['cf_title'] : '실시간 채팅';
$cf_tab1_title = isset($cfg['cf_tab1_title']) ? $cfg['cf_tab1_title'] : '스포츠채팅';
$cf_tab2_title = isset($cfg['cf_tab2_title']) ? $cfg['cf_tab2_title'] : '채팅규정';
$cf_notice_txt = isset($cfg['cf_notice_text']) ? $cfg['cf_notice_text'] : '';
?>
<style>
/* =========================
   CHAT BOX UI (기존 유지 + 안정화)
   ========================= */
.livechat-wrap{
  width:100%;
  height:592px; /* ✅ 총 세로 높이 고정 */
  background:#fff;
  border:1px solid rgba(0,47,98,.20);
  border-radius:14px;
  overflow:hidden;
  display:flex;
  flex-direction:column;
}

.livechat-tabs{
  display:flex;
  background:#f5f8ff;
  border-bottom:1px solid #d8e6f7;
}
.livechat-tab{
  flex:1 1 0;
  padding:10px 10px;
  font-weight:900;
  font-size:12px;
  text-align:center;
  cursor:pointer;
  user-select:none;
}
.livechat-tab.is-active{
  background:#fff;
}
.livechat-head{
  background:#002f62;
  color:#fff;
  padding:8px 10px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:8px;
  font-size:12px;
  font-weight:900;
}
.livechat-head .livechat-left{
  display:flex;align-items:center;gap:8px;
}
.livechat-head .livechat-right{
  display:flex;align-items:center;gap:8px;
}
.livechat-head .livechat-ico{
  width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;
  border-radius:6px;
  background:rgba(255,255,255,.12);
}
.livechat-notice{
  background:#fde9b4;
  color:#b91c1c;
  font-weight:900;
  font-size:11px;
  padding:6px 10px;
  border-bottom:1px solid #f3d98c;
}
.livechat-body{
  flex:1 1 auto;
  overflow:hidden;
  display:flex;
  flex-direction:column;
}

.livechat-messages{
  flex:1 1 auto;
  overflow:auto;
  background:#fff;
}

/* ✅ 메시지 1줄(닉+내용) 안정화 */
.livechat-msg{
  padding:6px 10px;
  border-bottom:1px solid #e6eef7;
  font-size:12px;
  display:flex;
  flex-wrap:wrap;
  align-items:flex-start;
  gap:4px;
}
.livechat-nick{
  font-weight:900;
  color:#0b3a6a;
  display:inline-flex;
  align-items:center;
  gap:6px;
  cursor:pointer;
}
.livechat-content{
  display:inline;
  color:#111;
  word-break:break-word;
  flex:1 1 auto;
  min-width:0;
}
.livechat-level-icon{width:18px;height:18px;display:inline-block;vertical-align:-3px;}

.livechat-foot{
  border-top:1px solid #d8e6f7;
  padding:8px 8px;
  display:flex;
  gap:8px;
  align-items:center;
  background:#fff;
}
.livechat-text{
  width:100%;
  height:32px;
  max-height:32px;
  overflow-y:hidden;
  border:1px solid #d8e6f7;
  border-radius:10px;
  padding:8px 10px;
  font-size:12px;
  resize:none;
  outline:none;
}
.livechat-send{
  width:44px;
  height:36px;
  border:none;
  border-radius:10px;
  background:#002f62;
  color:#fff;
  font-weight:900;
  cursor:pointer;
}
.livechat-send:disabled{
  opacity:.55;
  cursor:not-allowed;
}

/* 상태/온라인 */
.livechat-status{
  display:none;
  padding:8px 10px;
  font-size:11px;
  color:#666;
  background:#f8fbff;
  border-top:1px solid #e6eef7;
}

/* =========================
   ADMIN POPUP (UI만 유지/확대)
   ========================= */
.livechat-admin-pop{
  position:absolute;
  z-index:999999;
  width:360px;
  max-width:calc(100vw - 24px);
  background:#fff;
  border:1px solid rgba(0,47,98,.25);
  border-radius:14px;
  box-shadow:0 14px 30px rgba(0,0,0,.18);
  overflow:hidden;
}
.livechat-admin-pop .hd{
  background:#002f62;
  color:#fff;
  padding:10px 12px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  font-weight:900;
}
.livechat-admin-pop .bd{
  padding:12px;
  font-size:12px;
}
.livechat-admin-pop .row{
  display:flex;
  align-items:center;
  gap:8px;
  margin-bottom:10px;
}
.livechat-admin-pop input[type="text"],
.livechat-admin-pop input[type="number"],
.livechat-admin-pop select{
  height:34px;
  border:1px solid #d8e6f7;
  border-radius:10px;
  padding:0 10px;
  font-size:12px;
  width:100%;
}
.livechat-admin-pop .btn{
  height:34px;
  border:none;
  border-radius:10px;
  background:#002f62;
  color:#fff;
  font-weight:900;
  padding:0 12px;
  cursor:pointer;
}
.livechat-admin-pop .subttl{
  font-weight:900;
  margin:8px 0 6px;
  color:#0b3a6a;
}
.livechat-admin-pop .grid2{
  display:grid;
  grid-template-columns: 1fr 88px;
  gap:8px;
}
.livechat-admin-pop .grid3{
  display:grid;
  grid-template-columns: 1fr 92px 56px;
  gap:8px;
}

/* 모바일에서 팝업 크게 */
@media (max-width: 768px){
  .livechat-admin-pop{
    width:min(420px, calc(100vw - 24px));
  }
}

/* --- PATCH: notice banner + white user icon --- */
.livechat-ico-user{ color:#fff; }
.livechat-notice{
  background:#fff3cd;
  border-bottom:1px solid #f0d9a6;
  color:#b30000;
  font-weight:900;
  padding:8px 10px;
  font-size:12px;
  line-height:1.35;
}
/* --- PATCH: ignore list panel --- */
#livechat-ignore-panel{
  position: fixed;
  z-index: 99998;
  min-width: 220px;
  max-width: 280px;
  background: #111;
  color:#fff;
  border: 1px solid rgba(255,255,255,.15);
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0,0,0,.35);
  padding: 8px;
  font-size: 13px;
  display:none;
}
#livechat-ignore-panel .ig-head{
  font-weight: 800;
  padding: 6px 8px;
  border-bottom: 1px solid rgba(255,255,255,.12);
  margin-bottom: 6px;
}
#livechat-ignore-panel .ig-item{
  display:flex;
  align-items:center;
  justify-content: space-between;
  gap: 8px;
  padding: 7px 8px;
  border-radius: 8px;
  background: rgba(255,255,255,.06);
  margin: 6px 0;
}
#livechat-ignore-panel .ig-item button{
  border:0;
  border-radius: 8px;
  padding: 6px 8px;
  cursor:pointer;
  font-size: 12px;
}
#livechat-ignore-panel .ig-empty{
  padding: 8px;
  opacity: .85;
}
/* ✅ 무활동 끊김 상태(회색 처리) */
.livechat-wrap.is-idle #livechat-text{
  background:#eef1f5 !important;
  color:#6b7280 !important;
}
.livechat-wrap.is-idle #livechat-send{
  background:#cbd5e1 !important;
  cursor:not-allowed !important;
}
.livechat-wrap.is-idle #livechat-text::placeholder{
  color:#9aa3af !important;
}

</style>

<div class="livechat-wrap" id="livechat-wrap" style="position:relative;">
  <div class="livechat-tabs">
    <div class="livechat-tab is-active" id="livechat-tab-chat"><?php echo htmlspecialchars($cf_tab1_title); ?></div>
    <div class="livechat-tab" id="livechat-tab-rule"><?php echo htmlspecialchars($cf_tab2_title); ?></div>
  </div>

  <div class="livechat-head">
    <div class="livechat-left">
      <span class="livechat-ico livechat-ico-user" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" style="display:block;fill:currentColor"><path d="M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5S14.34 11 16 11zm-8 0c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.93 1.97 3.45V19h7v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></span>
      <span id="livechat-online">0명</span>
    </div>
    <div class="livechat-right">
            <span class="livechat-ico" id="livechat-ignorelist" title="무시목록">🙈</span>
<span class="livechat-ico" id="livechat-refresh" title="새로고침">🔄</span>
      <?php if($is_chat_admin){ ?>
        <span class="livechat-ico" id="livechat-admin-open" title="채팅 관리">🛡</span>
      <?php } ?>
      <span class="livechat-ico" id="livechat-bell" title="알림">🔔</span>
    </div>
  </div>


  <div class="livechat-body" id="livechat-body">
    
  <?php
  $notice_txt = '';
  if (isset($cfg['cf_notice_text']) && $cfg['cf_notice_text'] !== '') $notice_txt = $cfg['cf_notice_text'];
  if (isset($cfg['cf_notice_txt']) && $cfg['cf_notice_txt'] !== '') $notice_txt = $cfg['cf_notice_txt'];
  ?>
  <div class="livechat-notice" id="livechat-notice" style="<?php echo ($notice_txt ? '' : 'display:none;'); ?>">
    <?php echo (function_exists('html_purifier') ? html_purifier($notice_txt) : nl2br(htmlspecialchars($notice_txt))); ?>
  </div>

<div class="livechat-messages" id="livechat-messages"></div>

    <div class="livechat-foot">
      <textarea class="livechat-text" id="livechat-text" rows="1" placeholder="메시지를 입력하세요" autocomplete="off"></textarea>
      <button class="livechat-send" id="livechat-send">↵</button>
    </div>

    <div class="livechat-status" id="livechat-status"></div>
  </div>

  <!-- 채팅규정 -->
  <div class="livechat-body" id="livechat-rules" style="display:none;">
    <div style="padding:12px;font-size:12px;line-height:1.55;color:#111;">
      <?php
      $rule = isset($cfg['cf_rule_text']) ? $cfg['cf_rule_text'] : '';
      echo (function_exists('html_purifier') ? html_purifier($rule) : nl2br(htmlspecialchars($rule)));
      ?>
    </div>
  </div>

  <?php if($is_chat_admin){ ?>
  <!-- ADMIN POPUP -->
  <div class="livechat-admin-pop" id="livechat-admin-pop" style="display:none;">
    <div class="hd">
      <span>채팅 관리</span>
      <span style="cursor:pointer;" id="livechat-admin-close">✕</span>
    </div>
    <div class="bd">
      <div class="row" style="justify-content:space-between;">
        <label style="display:flex;align-items:center;gap:6px;font-weight:900;">
          <input type="checkbox" id="adm-freeze">
          채팅 동결(입력 잠금)
        </label>
        <button class="btn" id="adm-freeze-apply">적용</button>
      </div>

      <div class="row grid3">
        <input type="text" id="adm-ban-mb" placeholder="밴할 mb_id 입력">
        <select id="adm-ban-min">
          <option value="10">10분</option>
          <option value="60">60분</option>
          <option value="600">600분</option>
          <option value="0">영구</option>
        </select>
        <button class="btn" id="adm-ban-apply">밴</button>
      </div>

      <div class="subttl">도배/신고 설정</div>
      <div class="row" style="margin-bottom:6px;">
        <small style="color:#666;">※ 메시지 삭제/금칙어/신고누적 등은 다음 단계에서 이어서 붙이면 됩니다.</small>
      </div>

      <div class="row grid2">
        <label style="font-weight:900;line-height:34px;">연속 전송 제한(초)</label>
        <input type="number" id="adm-spam-sec" min="0" step="1" value="<?php echo isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2; ?>">
      </div>
      <div class="row grid2">
        <label style="font-weight:900;line-height:34px;">동일내용 반복 제한(초)</label>
        <input type="number" id="adm-repeat-sec" min="0" step="1" value="<?php echo isset($cfg['cf_repeat_sec']) ? (int)$cfg['cf_repeat_sec'] : 30; ?>">
      </div>
      <div class="row grid2">
        <label style="font-weight:900;line-height:34px;">신고 누적 임계(명)</label>
        <input type="number" id="adm-report-limit" min="1" step="1" value="<?php echo isset($cfg['cf_report_limit']) ? (int)$cfg['cf_report_limit'] : 5; ?>">
      </div>
      <div class="row grid2">
        <label style="font-weight:900;line-height:34px;">자동밴 시간(분)</label>
        <input type="number" id="adm-autoban-min" min="0" step="1" value="<?php echo isset($cfg['cf_autoban_min']) ? (int)$cfg['cf_autoban_min'] : 10; ?>">
      </div>


      <div class="subttl">공지/규정/금칙어</div>

      <div class="row grid2">
        <label style="font-weight:900;line-height:34px;">공지(상단 띠)</label>
        <input type="text" id="adm-notice-text" maxlength="200" placeholder="공지 내용을 입력하세요" value="<?php echo htmlspecialchars(isset($cfg['cf_notice_text']) ? $cfg['cf_notice_text'] : (isset($cfg['cf_notice_txt']) ? $cfg['cf_notice_txt'] : '')); ?>">
      </div>

      <div class="row" style="margin-top:8px;">
        <label style="font-weight:900;display:block;margin-bottom:6px;">채팅규정(채팅규정 탭에 표시)</label>
        <textarea id="adm-rule-text" rows="6" style="width:100%;resize:vertical;"><?php echo htmlspecialchars(isset($cfg['cf_rule_text']) ? $cfg['cf_rule_text'] : ''); ?></textarea>
      </div>

      <div class="row" style="margin-top:8px;">
        <label style="font-weight:900;display:block;margin-bottom:6px;">금칙어 목록(줄바꿈으로 구분)</label>
        <textarea id="adm-badwords" rows="5" style="width:100%;resize:vertical;"><?php echo htmlspecialchars(isset($cfg['cf_badwords']) ? $cfg['cf_badwords'] : ''); ?></textarea>
        <div style="margin-top:6px;color:#666;font-size:11px;line-height:1.4;">
          ※ 금칙어가 포함되면 전송 차단(기본). 마스킹/우회방지 옵션은 다음 단계에서 확장합니다.
        </div>
      </div>

      <div class="row" style="justify-content:flex-end;">
        <button class="btn" id="adm-config-save">설정 저장</button>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script>
(function(){
  var CHAT_AJAX_URL = "<?php echo $CHAT_AJAX_URL; ?>";
  var SP_FOLLOW_TOGGLE_URL = "<?php echo $SP_FOLLOW_TOGGLE_URL; ?>";
  var tabChat = document.getElementById('livechat-tab-chat');
  var tabRule = document.getElementById('livechat-tab-rule');
  var boxMsg  = document.getElementById('livechat-messages');
  var boxRule = document.getElementById('livechat-rules');
  var tabBody = document.getElementById('livechat-body');
  var btnSend = document.getElementById('livechat-send');
  var txtBox  = document.getElementById('livechat-text');
  var btnRefresh = document.getElementById('livechat-refresh');
  var btnIgnoreList = document.getElementById('livechat-ignorelist');
  var statusBar = document.getElementById('livechat-status');
  var onlineCnt = document.getElementById('livechat-online');

  

  var LIVECHAT_STATE = {
    isAdmin: <?php echo $is_chat_admin ? '1':'0'; ?>,
    me_mb_id: "<?php echo isset($member['mb_id']) ? $member['mb_id'] : ''; ?>",
    last_id: 0,
    freeze: 0
  };
    // =========================
  // ✅ 접속시점 last_id 저장/복구 (새로고침해도 유지)
  // =========================
  var SP_CHAT_JOIN_LAST_ID_KEY = 'sp_chat_join_last_id_v1';
  var spJoinLastIdFromStorage = false;

  function spChatLoadJoinLastId(){
    try {
      var v = localStorage.getItem(SP_CHAT_JOIN_LAST_ID_KEY);
      if (v === null || v === '') return null;
      var n = parseInt(v, 10);
      if (isNaN(n) || n < 0) return null;
      return n;
    } catch(e){
      return null;
    }
  }

  function spChatSaveJoinLastId(id){
    try {
      localStorage.setItem(SP_CHAT_JOIN_LAST_ID_KEY, String(id));
    } catch(e){}
  }

  // ✅ 저장된 접속시점(last_id)이 있으면 우선 적용
  (function(){
    var saved = spChatLoadJoinLastId();
    if (saved !== null) {
      LIVECHAT_STATE.last_id = saved;
      spJoinLastIdFromStorage = true;
    }
  })();

  // =========================
  // 무시목록 패널(🙈) + 해제
  // =========================
  var ignorePanel = document.createElement('div');
  ignorePanel.id = 'livechat-ignore-panel';
  ignorePanel.innerHTML = '<div class="ig-head">🙈 무시목록</div><div class="ig-body"></div>';
  document.body.appendChild(ignorePanel);

  function hideIgnorePanel(){
    ignorePanel.style.display = 'none';
  }

  function positionIgnorePanel(){
    if(!btnIgnoreList) return;
    var rect = btnIgnoreList.getBoundingClientRect();
    ignorePanel.style.display = 'block';

    var x = rect.left - ignorePanel.offsetWidth - 8; // 기본: 아이콘 왼쪽에
    var y = rect.bottom + 8;

    // 왼쪽이 부족하면 오른쪽으로
    if(x < 8) x = rect.right + 8;

    // 아래가 부족하면 위로
    var r2 = ignorePanel.getBoundingClientRect();
    if(r2.bottom > window.innerHeight) y = Math.max(8, rect.top - r2.height - 8);

    ignorePanel.style.left = x + 'px';
    ignorePanel.style.top  = y + 'px';
  }

  function renderIgnorePanel(list, nickMap){
    var body = ignorePanel.querySelector('.ig-body');
    if(!body) return;

    if(!list || !list.length){
      body.innerHTML = '<div class="ig-empty">무시한 회원이 없습니다.</div>';
      return;
    }

    var html = '';
    list.forEach(function(mb){
      var nick = (nickMap && nickMap[mb]) ? nickMap[mb] : ('ID: ' + mb);
      html += '<div class="ig-item">'
           +  '<div class="ig-nick">'+ nick +'</div>'
           +  '<button type="button" data-mb="'+ mb +'">해제</button>'
           +  '</div>';
    });
    body.innerHTML = html;
  }

  function openIgnorePanel(){
    var list = spChatLoadIgnored().map(String);
    positionIgnorePanel();

    // 닉네임을 서버에서 조회(기존 무시도 닉으로 복구 가능)
    fetch('/plugin/chat/ajax/chat_get_nicks.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: new URLSearchParams({ mb_ids: list.join(',') })
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
      var map = (res && res.ok && res.map) ? res.map : {};
      renderIgnorePanel(list, map);
    })
    .catch(function(){
      renderIgnorePanel(list, {});
    });
  }

  if(btnIgnoreList){
    btnIgnoreList.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();

      if(ignorePanel.style.display === 'block'){
        hideIgnorePanel();
      } else {
        openIgnorePanel();
      }
    });
  }

  // 패널 내부 해제 버튼
  ignorePanel.addEventListener('click', function(e){
    var btn = e.target.closest('button[data-mb]');
    if(!btn) return;

    var mb = btn.getAttribute('data-mb') || '';
    if(!mb) return;

    // 토글 해제
    spChatToggleIgnore(mb);

    // 다시 렌더
    openIgnorePanel();
  });

  // 바깥 클릭 시 닫기
  document.addEventListener('click', function(e){
    if(ignorePanel.style.display !== 'block') return;
    if(e.target === btnIgnoreList || (btnIgnoreList && btnIgnoreList.contains(e.target))) return;
    if(ignorePanel.contains(e.target)) return;
    hideIgnorePanel();
  }, true);

  function setStatus(text){
    if(!statusBar) return;
    if(!text){
      statusBar.style.display = 'none';
      statusBar.textContent = '';
      return;
    }
    statusBar.textContent = text;
    statusBar.style.display = 'block';
  }

  function switchToChat(){
    tabChat.classList.add('is-active');
    tabRule.classList.remove('is-active');
    tabBody.style.display = 'flex';
    boxRule.style.display = 'none';
  }
  function switchToRule(){
    tabChat.classList.remove('is-active');
    tabRule.classList.add('is-active');
    tabBody.style.display = 'none';
    boxRule.style.display = 'flex';
  }
  tabChat && tabChat.addEventListener('click', switchToChat);
  tabRule && tabRule.addEventListener('click', switchToRule);

  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, function(m){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
    });
  }
  // =========================
  // 닉 클릭 메뉴(승/패/승률/활동내역/무시하기/신고하기/선물하기)
  // =========================
  var SP_CHAT_IGNORE_KEY = 'sp_chat_ignored_mbids_v1';

  function spChatLoadIgnored(){
    try {
      var raw = localStorage.getItem(SP_CHAT_IGNORE_KEY);
      var arr = raw ? JSON.parse(raw) : [];
      return Array.isArray(arr) ? arr : [];
    } catch(e){
      return [];
    }
  }
  function spChatSaveIgnored(arr){
    try { localStorage.setItem(SP_CHAT_IGNORE_KEY, JSON.stringify(arr||[])); } catch(e){}
  }
  function spChatIsIgnored(mb_id){
    if(!mb_id) return false;
    var arr = spChatLoadIgnored();
    return arr.indexOf(String(mb_id)) >= 0;
  }
  function spChatToggleIgnore(mb_id){
    mb_id = String(mb_id||'');
    if(!mb_id) return {ignored:false};
    var arr = spChatLoadIgnored();
    var idx = arr.indexOf(mb_id);
    var ignored;
    if(idx >= 0){
      arr.splice(idx,1);
      ignored = false;
    } else {
      arr.push(mb_id);
      ignored = true;
    }
    spChatSaveIgnored(arr);

    // 이미 출력된 메시지도 즉시 반영(숨김/표시)
    var nodes = document.querySelectorAll('.livechat-msg[data-mb-id="'+mb_id+'"]');
    nodes.forEach(function(n){
      n.style.display = ignored ? 'none' : '';
    });

    return {ignored:ignored};
  }

  // ✅ 공통 유저 상태창 사용 (sp_user_menu_common.js)
  function spChatHideMenu(){
    if(typeof window.spUserMenuHide === 'function') window.spUserMenuHide();
  }

  function spChatShowMenuAt(x, y, mbid, nick){
    if(typeof window.spUserMenuShow !== 'function') return;
    window.spUserMenuShow(x, y, mbid, nick, {
      hideReport: false,
      hideIgnore: false,
      onReport: function(mbid, nick, reason){
        fetch(CHAT_AJAX_URL, {
          method: 'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
          body: new URLSearchParams({ act: 'report', target_id: mbid, target_nick: nick, reason: reason, cm_id: '' })
        })
        .then(function(r){ return r.json(); })
        .then(function(res){
          alert(res.msg || (res.ok ? '신고가 접수되었습니다.' : '신고 실패'));
          spChatHideMenu();
        })
        .catch(function(){ alert('신고 요청 중 오류가 발생했습니다.'); });
      },
      onIgnore: function(mbid){ return spChatToggleIgnore(mbid); },
      getIgnoreLabel: function(mbid){ return spChatIsIgnored(mbid) ? '무시해제' : '무시하기'; }
    });
  }

  // (기존 인라인 메뉴는 sp_user_menu_common.js로 이전됨)

  function appendMessages(list){
    if(!boxMsg) return;
    if(!Array.isArray(list)) return;

    list.forEach(function(row){
      LIVECHAT_STATE.last_id = Math.max(LIVECHAT_STATE.last_id, parseInt(row.cm_id||0,10) || 0);
      var _mbid = row.mb_id || '';
      if (_mbid && spChatIsIgnored(_mbid)) return;

      var msg = document.createElement('div');
      msg.className = 'livechat-msg';
      msg.dataset.mb_id = (_mbid || '');


      // nick wrap (아이콘 + 닉네임)
      var nickWrap = document.createElement('span');
      nickWrap.className = 'livechat-nick';

      // ✅ 아이콘 호환: cm_icon이 없으면 level_icon / icon 계열도 허용
      var iconUrl = (row.cm_icon || row.level_icon || row.level_icon_url || row.icon || row.icon_url || '');
      if (iconUrl) {
        var img = document.createElement('img');
        img.src = iconUrl;
        img.className = 'livechat-level-icon';
        img.alt = 'LV';
        nickWrap.appendChild(img);
      }

      var nickText = document.createElement('span');
      nickText.className = 'livechat-nick-text';
      nickText.textContent = (row.cm_nick || '손님');
      nickWrap.appendChild(nickText);

      // content
      var content = document.createElement('span');
      content.className = 'livechat-content';
      content.textContent = ': ' + (row.cm_content || '');

      // 신고(관리 예정)용 데이터
      nickWrap.dataset.mb_id = row.mb_id || '';
      nickWrap.dataset.cm_id = row.cm_id || '';
      nickText.textContent = (row.cm_nick || '');
      nickWrap.dataset.nick = row.cm_nick || '';



           // 닉 클릭 → 신고하기(현재 단계: 신고만 연결)
      // 닉 클릭 → 메뉴 열기
nickWrap.addEventListener('click', function(ev){
  ev.preventDefault();
  ev.stopPropagation();

  var mbid = this.dataset.mb_id || '';
  var nick = this.dataset.nick  || '';

  // 손님 없음(게스트 채팅 불가) + 안전장치
  if(!mbid || !nick) return;

  spChatShowMenuAt(ev.clientX, ev.clientY, mbid, nick);
});



      msg.appendChild(nickWrap);
      msg.appendChild(content);
      boxMsg.appendChild(msg);
    });

    boxMsg.scrollTop = boxMsg.scrollHeight;
  }

  function livechatLoad(){
    fetch(CHAT_AJAX_URL + '?act=list&last_id=' + encodeURIComponent(LIVECHAT_STATE.last_id), {
      method:'GET',
      credentials:'same-origin'
    })
    .then(function(r){ return r.json(); })
    .then(function(json){
      if(!json || json.ok !== 1){
        setStatus('연결 점검 체크(롤링 중단)');
        return;
      }
            setStatus('');

      // ✅ 접속자 카운트 표시 (online_count 우선, 없으면 cnt_online)
      var oc = null;
      if (typeof json.online_count !== 'undefined') oc = json.online_count;
      else if (typeof json.cnt_online !== 'undefined') oc = json.cnt_online;

      if (onlineCnt && oc !== null && typeof oc !== 'undefined') {
        oc = parseInt(oc, 10);
        if (!isNaN(oc)) onlineCnt.textContent = (oc + '명');
      }



      // ✅ 운영자 동결 상태 변화 안내(1회)
      var prevFreeze = (LIVECHAT_STATE.freeze ? 1 : 0);
      var nowFreeze  = (json.freeze == 1 ? 1 : 0);
      if (prevFreeze !== nowFreeze) {
        LIVECHAT_STATE.freeze = nowFreeze;
        try {
          var sys = document.createElement('div');
          sys.className = 'livechat-msg';
          sys.innerHTML = '<span class="livechat-content" style="font-weight:900;color:#c2410c;">' +
            (nowFreeze ? '운영자가 채팅창을 얼렸습니다.' : '운영자가 채팅 동결을 해제했습니다.') +
            '</span>';
          boxMsg.appendChild(sys);
          boxMsg.scrollTop = boxMsg.scrollHeight;
        } catch(e) {}
      }

      // freeze 처리(입력 잠금)
      if (json.freeze == 1){
        txtBox.disabled = true;
        btnSend.disabled = true;
      } else {
        txtBox.disabled = false;
        btnSend.disabled = false;
      }

      appendMessages(json.list || []);
    })
    .catch(function(){
      setStatus('서버 연결이 불안정합니다.');
    });
  }
  var __spSending = false;
  var __spLastSendTs = 0;

    function livechatSend(){
    if(!txtBox || txtBox.disabled) { alert('채팅이 잠금 상태입니다.'); return; }

    if (__spSending) return;

    var now = Date.now();
    if (now - __spLastSendTs < 1900) {
      setStatus('연속 전송 제한(2초)입니다.');
      return;
    }

    var content = (txtBox.value || '').trim();
    if(!content) return;

    spMarkActive();

    __spSending = true;
    __spLastSendTs = now;

    fetch(CHAT_AJAX_URL, {
      method:'POST',
      credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body:'act=send&content=' + encodeURIComponent(content)
    })
    .then(function(r){ return r.json(); })
    .then(function(json){
      if(!json || json.ok !== 1){
        // ✅ “연속제한”류는 alert 대신 상태바로만
        if (json && json.msg) setStatus(json.msg);
        else setStatus('전송 실패');
        return;
      }
      txtBox.value = '';
      setStatus('');
      livechatLoad();
    })
    .catch(function(){
      setStatus('서버 연결이 불안정합니다.');
    })
    .finally(function(){
      __spSending = false;
    });
  }


  btnSend && btnSend.addEventListener('click', livechatSend);
  // ✅ 엔터로 전송(Shift+Enter는 줄바꿈)
  if (txtBox) {
    txtBox.addEventListener('keydown', function(e){
      // IME(한글 조합) 중 Enter 오작동 방지
      if (e.isComposing) return;

      // 키 꾹 누름 반복 전송 방지
      if (e.repeat) return;

      if (e.key === 'Enter') {
        // Shift+Enter는 줄바꿈 허용
        if (e.shiftKey) return;

        e.preventDefault();
        e.stopPropagation();
        livechatSend();
      }
    });
  }


    // ✅ 새로고침(🔄) = "지금 시점"을 새 접속시점(last_id)으로 재설정
  btnRefresh && btnRefresh.addEventListener('click', function(){
    livechatHello(true, function(){
      // 화면 비우고(선택사항) 새 기준부터 다시 받기
      if (boxMsg) boxMsg.innerHTML = '';
      LIVECHAT_STATE.last_id = Math.max(0, LIVECHAT_STATE.last_id);
      livechatLoad();
    });
  });

    // ✅ 접속 시점 기준(last_id) 초기화 + 새로고침 시 재설정
  // - forceReset=false: 저장된 join_last_id가 있으면 유지
  // - forceReset=true : 지금 시점 last_id로 강제 재설정 + 저장
  function livechatHello(forceReset, done){
    fetch(CHAT_AJAX_URL + '?act=hello', {
      method:'GET',
      credentials:'same-origin'
    })
    .then(function(r){ return r.json(); })
    .then(function(json){
      if(json && json.ok === 1){

        // freeze 반영
        if(typeof json.freeze !== 'undefined'){
          LIVECHAT_STATE.freeze = (json.freeze == 1 ? 1 : 0);
        }

        // online_count 표시
        if(onlineCnt && typeof json.online_count !== 'undefined'){
          var oc = parseInt(json.online_count, 10);
          if(!isNaN(oc)) onlineCnt.textContent = (oc + '명');
        }

        // last_id(접속시점) 처리
        if(typeof json.last_id !== 'undefined'){
          var lid = parseInt(json.last_id, 10);
          if(!isNaN(lid) && lid >= 0){

            // 강제 재설정(🔄)
            if(forceReset === true){
              LIVECHAT_STATE.last_id = lid;
              spChatSaveJoinLastId(lid);
              spJoinLastIdFromStorage = true;

            // 최초 접속(저장값 없을 때만 저장)
            } else if(!spJoinLastIdFromStorage) {
              LIVECHAT_STATE.last_id = lid;
              spChatSaveJoinLastId(lid);
              spJoinLastIdFromStorage = true;
            }
          }
        }
      }

      if(typeof done === 'function') done();
    })
    .catch(function(){
      if(typeof done === 'function') done();
    });
  }


  // ✅ online ping (손님 포함 접속자 카운트용)
  function livechatPing(){
    fetch(CHAT_AJAX_URL + '?act=ping', {
      method:'GET',
      credentials:'same-origin'
    }).catch(function(){});
  }

    // =========================
  // ✅ Polling/Ping 루프 제어 + 10분 무활동 종료
  // =========================
  var spPollTimer = null;
  var spPingTimer = null;
  var spIdleTimer = null;
  var spLastActiveTs = Date.now();
  var spStoppedByIdle = false;
  // ✅ idle 끊김 UI 잠금/해제
  var spIdleLocked = false;

  function spSetIdleUI(isIdle){
    spIdleLocked = (isIdle ? true : false);

    var wrap = document.getElementById('livechat-wrap');

    // wrap class
    if (wrap) {
      if (spIdleLocked) wrap.classList.add('is-idle');
      else wrap.classList.remove('is-idle');
    }

    // 입력/버튼 상태
    // ※ 운영자 동결(freeze=1)이면 idle 해제해도 계속 잠금 유지해야 함
    var shouldDisable = spIdleLocked || (LIVECHAT_STATE.freeze ? true : false);

    if (txtBox) {
      txtBox.disabled = shouldDisable;

      if (spIdleLocked) {
        txtBox.placeholder = '화면을 클릭하면 재연결됩니다.';
      } else {
        // 기본 placeholder 복구(원래 값이 있으면 유지)
        if (!txtBox.getAttribute('data-ph')) {
          txtBox.setAttribute('data-ph', txtBox.placeholder || '메시지를 입력하세요');
        }
        txtBox.placeholder = txtBox.getAttribute('data-ph') || '메시지를 입력하세요';
      }
    }

    if (btnSend) {
      btnSend.disabled = shouldDisable;
    }
  }

  function spStartLoop(){
    if(spPollTimer) clearInterval(spPollTimer);
    if(spPingTimer) clearInterval(spPingTimer);

    livechatPing();   // 접속 즉시 1회
    livechatLoad();   // 목록 1회

    spPollTimer = setInterval(livechatLoad, 4000);
    spPingTimer = setInterval(livechatPing, 30000);
  }

  function spStopLoop(msg){
    if(spPollTimer){ clearInterval(spPollTimer); spPollTimer = null; }
    if(spPingTimer){ clearInterval(spPingTimer); spPingTimer = null; }
    if(msg) setStatus(msg);
  }

   function spMarkActive(){
    spLastActiveTs = Date.now();

    if(spStoppedByIdle){
      spStoppedByIdle = false;

      // ✅ idle UI 해제(입력창/버튼 복구)
      spSetIdleUI(false);

      setStatus('');
      spStartLoop();
    }
  }


    // ✅ 활동 감지(클릭/키보드/스크롤/터치/마우스이동/포인터/포커스)
  [
    'click',
    'keydown',
    'scroll',
    'touchstart',
    'touchmove',
    'mousemove',
    'pointerdown',
    'focus'
  ].forEach(function(evt){
    document.addEventListener(evt, spMarkActive, {passive:true});
  });

  // ✅ 10분 무활동이면 연결 끊김 (UI는 유지, 클릭하면 재연결)
  spIdleTimer = setInterval(function(){
    if(spStoppedByIdle) return;
    if(Date.now() - spLastActiveTs >= 10 * 60 * 1000){
            spStoppedByIdle = true;
      spSetIdleUI(true);
      spStopLoop('10분간 활동이 없어 연결이 중단되었습니다. 화면을 클릭하면 재연결됩니다.');
    }
  }, 5000);

  // ✅ 접속 시점 last_id 세팅 → 그 다음 루프 시작
    livechatHello(false, function(){
    spStartLoop();
  });




  /* =========================
     ADMIN: 어드민 채팅관리 페이지 열기 (ScorePoint 메뉴 950600)
     ========================= */
  <?php if($is_chat_admin){ ?>
  var openBtn = document.getElementById('livechat-admin-open');
  openBtn && openBtn.addEventListener('click', function(){
    var url = '<?php echo (defined("G5_PLUGIN_URL") ? G5_PLUGIN_URL : (G5_URL . "/plugin")); ?>/chat/chat_admin.php';
    var w = (screen && screen.availWidth) ? screen.availWidth : window.innerWidth;
    var h = (screen && screen.availHeight) ? screen.availHeight : window.innerHeight;
    var opts = 'width=' + w + ',height=' + h + ',left=0,top=0,resizable=yes,scrollbars=yes,noopener';
    window.open(url, 'scorepoint_chat_admin', opts);
  });
  <?php } ?>
})();
</script>
