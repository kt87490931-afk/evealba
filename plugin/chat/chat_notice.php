<?php
// /plugin/chat/chat_notice.php
// 공지/규정/금칙어 전용 관리 페이지 (채팅관리에서 분리)
if (!defined('_GNUBOARD_')) {
    include_once(__DIR__ . '/../../common.php');
}
include_once(G5_PLUGIN_PATH.'/chat/_common.php');

if (!isset($is_admin) || !$is_admin) die('Access denied.');

$CHAT_AJAX_URL = G5_PLUGIN_URL.'/chat/chat_ajax.php';
$cfg = sql_fetch(" select * from g5_chat_config limit 1 ");
if (!$cfg) $cfg = array();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

$notice = isset($cfg['cf_notice_text']) ? $cfg['cf_notice_text'] : (isset($cfg['cf_notice_txt']) ? $cfg['cf_notice_txt'] : '');
$rule   = isset($cfg['cf_rule_text']) ? $cfg['cf_rule_text'] : '';
$bad    = isset($cfg['cf_badwords']) ? $cfg['cf_badwords'] : '';

$spam_sec   = isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2;
$repeat_sec = isset($cfg['cf_repeat_sec']) ? (int)$cfg['cf_repeat_sec'] : 30;
$report_lim = isset($cfg['cf_report_limit']) ? (int)$cfg['cf_report_limit'] : 10;
$autoban_min= isset($cfg['cf_autoban_min']) ? (int)$cfg['cf_autoban_min'] : 10;
$online_fake_add = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;

// ✅ 그누보드 관리자 헤더(새창) 적용: sub 우선
$g5['title'] = '공지/규정/금칙어';
add_stylesheet('<link rel=\"stylesheet\" href=\"'.G5_PLUGIN_URL.'/chat/chat_admin_style.css?ver=20260102\">', 0);
$head_sub = G5_ADMIN_PATH.'/admin.head.sub.php';
$head     = G5_ADMIN_PATH.'/admin.head.php';
if (is_file($head_sub)) include_once($head_sub);
else include_once($head);
?>
<link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/admin.css">
<link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/admin_extend.css">

<script>document.documentElement.classList.add('sp-chat-admin');</script>
<script>
(function(){
  function makeTopNav(){
    // 이미 있으면 종료
    if (document.getElementById('spChatTopNav')) return;

    // "ADMINISTRATOR"가 있는 로고 영역 찾기(그누보드5 관리자 스킨별로 다를 수 있어 후보 다 잡음)
    var logo =
      document.querySelector('#logo') ||
      document.querySelector('#hd h1#logo') ||
      document.querySelector('#hd h1') ||
      document.querySelector('.logo');

    if (!logo) return;

    var nav = document.createElement('div');
    nav.id = 'spChatTopNav';

    var base = <?php echo json_encode(G5_PLUGIN_URL.'/chat/', JSON_UNESCAPED_UNICODE); ?>;

    var items = [
      { href: base + 'chat_admin.php',        text: '채팅관리' },
      { href: base + 'chat_notice.php',       text: '공지/규정/금칙어' },
      { href: base + 'chat_report_admin.php', text: '최근신고' },
      { href: base + 'chat_banlist.php',      text: '밴리스트' }
    ];

    var path = (location.pathname || '');

    items.forEach(function(it){
      var a = document.createElement('a');
      a.href = it.href;
      a.textContent = it.text;

      // 현재 페이지 on 표시
      if (path.indexOf(it.href.split('/').pop()) !== -1) a.className = 'on';
      nav.appendChild(a);
    });

    // 로고(ADMINISTRATOR) 오른쪽에 붙임
    logo.appendChild(nav);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', makeTopNav);
  } else {
    makeTopNav();
  }
})();
</script>

<div class="sp-shell">
  <aside class="sp-side">
    <div class="sp-brand">SCOREPOINT</div>
    <nav class="sp-nav">
      <a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_admin.php">채팅관리</a>
      <a class="on" href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_notice.php">공지/규정/금칙어</a>
      <a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_report_admin.php">최근신고</a>
      <a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_banlist.php">밴리스트</a>
    </nav>
  </aside>

  <main class="sp-main">
    <div class="sp-topbar">
      <div class="sp-top-title">공지 / 규정 / 금칙어</div>
      <div class="sp-top-meta">관리자: <?php echo h($member['mb_id'] ?? ''); ?></div>
    </div>

    <div class="sp-content">
      <div class="grid" style="grid-template-columns:1fr;">
        <div class="card">
          <h3>공지(상단 띠)</h3>
          <div class="row" style="align-items:flex-start;">
            <label style="min-width:120px;">공지</label>
            <textarea id="adm-notice-text" placeholder="공지 내용을 입력"><?php echo h($notice); ?></textarea>
          </div>

          <div class="split"></div>

          <h3 style="margin-top:0;">채팅규정</h3>
          <div class="row" style="align-items:flex-start;">
            <label style="min-width:120px;">규정</label>
            <textarea id="adm-rule-text" placeholder="채팅규정을 입력"><?php echo h($rule); ?></textarea>
          </div>

          <div class="split"></div>

          <h3 style="margin-top:0;">금칙어</h3>
          <div class="row" style="align-items:flex-start;">
            <label style="min-width:120px;">금칙어</label>
            <textarea id="adm-badwords" placeholder="금칙어 목록 (줄바꿈/쉼표 구분)"><?php echo h($bad); ?></textarea>
          </div>

          <div class="help">금칙어 포함 시 전송 차단(기본). 마스킹/우회방지 등은 추후 확장합니다.</div>

          <div class="row" style="justify-content:flex-end;margin-top:10px;">
            <button class="btn primary" id="adm-save-notice">설정 저장</button>
          </div>
        </div>
      </div>
    </div>

<script>
(function(){
  var AJAX = <?php echo json_encode($CHAT_AJAX_URL, JSON_UNESCAPED_UNICODE); ?>;

  // 다른 설정값은 여기서 편집하지 않지만, 저장 시 함께 전송(덮어쓰기 방지)
  var SPAM_SEC        = <?php echo (int)$spam_sec; ?>;
  var REPEAT_SEC      = <?php echo (int)$repeat_sec; ?>;
  var REPORT_LIMIT    = <?php echo (int)$report_lim; ?>;
  var AUTOBAN_MIN     = <?php echo (int)$autoban_min; ?>;
  var ONLINE_FAKE_ADD = <?php echo (int)$online_fake_add; ?>;

  function post(body){
    return fetch(AJAX, {
      method:'POST',
      credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: body
    }).then(function(r){ return r.json(); });
  }

  var btn = document.getElementById('adm-save-notice');
  if (!btn) return;

  btn.addEventListener('click', function(){
    var noticeText = document.getElementById('adm-notice-text').value||'';
    var ruleText   = document.getElementById('adm-rule-text').value||'';
    var badwords   = document.getElementById('adm-badwords').value||'';

    post(
      'act=admin_config_save'
      + '&spam_sec=' + encodeURIComponent(String(SPAM_SEC))
      + '&repeat_sec=' + encodeURIComponent(String(REPEAT_SEC))
      + '&report_limit=' + encodeURIComponent(String(REPORT_LIMIT))
      + '&autoban_min=' + encodeURIComponent(String(AUTOBAN_MIN))
      + '&online_fake_add=' + encodeURIComponent(String(ONLINE_FAKE_ADD))
      + '&notice_text=' + encodeURIComponent(noticeText)
      + '&rule_text=' + encodeURIComponent(ruleText)
      + '&badwords=' + encodeURIComponent(badwords)
    ).then(function(j){
      if(!j || j.ok!==1) return alert(j && j.msg ? j.msg : '실패');
      alert('저장 완료');
    });
  });
})();
</script>

  </main>
</div>

<?php
$tail_sub = G5_ADMIN_PATH.'/admin.tail.sub.php';
$tail     = G5_ADMIN_PATH.'/admin.tail.php';
if (is_file($tail_sub)) include_once($tail_sub);
else include_once($tail);
?>
