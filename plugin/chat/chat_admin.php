<?php
// /plugin/chat/chat_admin.php
// 관리자 전용(새창) 채팅 관리 페이지 - 심플 UI(그누보드 관리자 느낌)

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
// 탭
$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'manage';
$tab = in_array($tab, array('manage','notice'), true) ? $tab : 'manage';

// 최근 신고 탭 데이터 준비
$rp_rows  = array();
$rp_total = 0;
$rp_page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$rp_per   = 20;
$rp_q     = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($tab === 'reports') {

    // 신고 테이블 자동 선택: chat_report2 우선, 없으면 chat_report
    $tbl2 = $g5['table_prefix'].'chat_report2';
    $tbl1 = $g5['table_prefix'].'chat_report';

    $use_table = $tbl1;
    $chk = sql_fetch(" SHOW TABLES LIKE '".sql_real_escape_string($tbl2)."' ");
    if ($chk && !empty($chk)) {
        $use_table = $tbl2;
    }

    // 검색 조건
    $where = " 1 ";
    if ($rp_q !== '') {
        $q = sql_real_escape_string($rp_q);
        $where .= " AND (
            reporter_id LIKE '%{$q}%'
            OR reporter_nick LIKE '%{$q}%'
            OR target_id LIKE '%{$q}%'
            OR target_nick LIKE '%{$q}%'
            OR reason LIKE '%{$q}%'
        ) ";
    }

    // 총 개수
    $cRow = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$use_table} WHERE {$where} ");
    $rp_total = (int)($cRow && isset($cRow['cnt']) ? $cRow['cnt'] : 0);

    $off = ($rp_page - 1) * $rp_per;

    // 목록 (누적 신고수: target_id 기준 / 최신순)
    $rs = sql_query("
        SELECT
            r.*,
            (SELECT COUNT(*) FROM {$use_table} rr WHERE rr.target_id = r.target_id) AS target_cnt
        FROM {$use_table} r
        WHERE {$where}
        ORDER BY r.id DESC
        LIMIT {$off}, {$rp_per}
    ");

    while ($r = sql_fetch_array($rs)) {
        $rp_rows[] = $r;
    }
}


?>
<?php
// ✅ 그누보드 관리자 스타일(팝업) 적용: sub 우선
$g5['title'] = '채팅 관리';

$head_sub = G5_ADMIN_PATH.'/admin.head.sub.php';
$head     = G5_ADMIN_PATH.'/admin.head.php';

if (is_file($head_sub)) {
    include_once($head_sub);
} else {
    // sub가 없으면 head로 로드되는데, 이 경우 "관리자 프레임"이 뜨므로 CSS로 숨김 처리
    include_once($head);
}
?>

<link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/admin.css">
<link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/admin_extend.css">
<link rel="stylesheet" href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_admin_style.css?ver=20260102">
<script>document.documentElement.classList.add('sp-chat-admin');</script>
<script>window.SP_CHAT_TOPNAV = true;</script>
<script>
  if (window.SP_CHAT_TOPNAV) {
    function makeTopNav(){
      if (document.getElementById('spChatTopNav')) return;
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
        if (path.indexOf(it.href.split('/').pop()) !== -1) a.className = 'on';
        nav.appendChild(a);
      });

      logo.appendChild(nav);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', makeTopNav);
    } else {
      makeTopNav();
    }
  }
</script>


<div class="sp-shell">
  <aside class="sp-side">
    <div class="sp-brand">SCOREPOINT</div>
    <nav class="sp-nav">
      <a class="<?php echo ($tab==='manage'?'on':''); ?>" href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_admin.php?tab=manage">채팅관리</a>
<a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_notice.php" class="<?php echo ($tab==="notice"?"on":""); ?>">공지/규정/금칙어</a>
<a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_report_admin.php" class="<?php echo ($tab==="report"?"on":""); ?>">최근신고</a>
<a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_banlist.php" class="<?php echo ($tab==="banlist"?"on":""); ?>">밴리스트</a>
    </nav>
  </aside>

  <main class="sp-main">
    <div class="sp-topbar">
      <div class="sp-top-title"><?php echo ($tab==='notice' ? '공지 / 규정 / 금칙어' : '채팅관리'); ?></div>
      <div class="sp-top-meta">관리자: <?php echo htmlspecialchars($member['mb_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
    </div>

    <div class="sp-content">



<?php if ($tab !== 'notice') { ?>
    <div class="grid">
        <div class="card">
            <h3>운영 / 제재</h3>

            <div class="row">
                <label><input type="checkbox" id="adm-freeze"> 채팅 동결(입력 잠금)</label>
                <button class="btn primary" id="adm-freeze-apply">적용</button>
            </div>

            <div class="split"></div>

            <h3 style="margin-top:0;">닉네임 밴</h3>
            <div class="row">
                <label>닉네임</label>
                <input type="text" id="adm-ban-nick" placeholder="밴할 닉네임">
            </div>
            <div class="row">
                <label>기간</label>
                <select id="adm-ban-min">
                    <option value="10">10분</option>
                    <option value="60">60분</option>
                    <option value="600">600분</option>
                    <option value="0">영구</option>
                </select>
            </div>
            <div class="row">
                <label>사유</label>
                <input type="text" id="adm-ban-reason" placeholder="한 줄 사유(관리자용)">
            </div>
            <div class="row" style="justify-content:flex-end;">
                <button class="btn red" id="adm-ban-apply">밴</button>
            </div>
            <div class="help">밴 대상은 회원만(비회원 채팅 불가). 밴/해제는 밴리스트에서 관리합니다.</div>

            <div class="split"></div>

            <h3 style="margin-top:0;">채팅창 비우기</h3>
            <div class="row" style="justify-content:flex-end;">
                <button class="btn gray" id="adm-clear-chat">채팅창 비우기</button>
            </div>
            <div class="help">전체 채팅을 DB에서 삭제합니다. (복구 불가)</div>

            <div class="split"></div>

            <h3 style="margin-top:0;">도배 / 신고 설정</h3>
            <div class="row">
                <label>연속 전송 제한(초)</label>
                <input type="number" id="adm-spam-sec" min="0" step="1" value="<?php echo (int)$spam_sec; ?>">
            </div>
            <div class="row">
                <label>동일내용 반복 제한(초)</label>
                <input type="number" id="adm-repeat-sec" min="0" step="1" value="<?php echo (int)$repeat_sec; ?>">
            </div>
            <div class="row">
                <label>신고 누적 임계(명)</label>
                <input type="number" id="adm-report-limit" min="1" step="1" value="<?php echo (int)$report_lim; ?>">
            </div>
            <div class="row">
                <label>자동밴 시간(분)</label>
                <input type="number" id="adm-autoban-min" min="0" step="1" value="<?php echo (int)$autoban_min; ?>">
            </div>
            <div class="row">
    <label>접속자 가산(+n)</label>
    <input type="number" id="adm-online-fake-add" min="0" step="1" value="<?php echo (int)$online_fake_add; ?>">
</div>

        </div>

        <div class="card">
            <h3>공지 / 규정 / 금칙어</h3>

            <div class="row" style="align-items:flex-start;">
                <label>공지(상단 띠)</label>
                <textarea id="adm-notice-text" placeholder="공지 내용을 입력"><?php echo h($notice); ?></textarea>
            </div>

            <div class="row" style="align-items:flex-start;">
                <label>채팅규정</label>
                <textarea id="adm-rule-text" placeholder="채팅규정(채팅규정 탭에 표시)"><?php echo h($rule); ?></textarea>
            </div>

            <div class="row" style="align-items:flex-start;">
                <label>금칙어</label>
                <textarea id="adm-badwords" placeholder="금칙어 목록 (줄바꿈/쉼표 구분)"><?php echo h($bad); ?></textarea>
            </div>

            <div class="help">금칙어 포함 시 전송 차단(기본). 마스킹/우회방지 등은 추후 확장합니다.</div>

            <div class="row" style="justify-content:flex-end;margin-top:10px;">
                <button class="btn primary" id="adm-config-save">설정 저장</button>
            </div>
        </div>
    </div>
        <?php } else { ?>

    <?php
    // 밴 테이블(현재 밴 상태 확인용)
    $tbl_ban = $g5['table_prefix'].'chat_ban';
    ?>

    <div class="grid">
        <div class="card">
            <h3>최근 신고</h3>

            <div class="row" style="gap:10px; align-items:center;">
                <label style="min-width:120px;">닉네임/아이디 검색</label>
                <input type="text" id="rp-q" value="<?php echo h($rp_q); ?>" placeholder="신고자/대상 닉/아이디 검색">
                <button class="btn primary" id="rp-search">검색</button>
            </div>

            <div class="split"></div>

            <?php if ($rp_total <= 0) { ?>
                <div class="help">신고 내역이 없습니다.</div>
            <?php } else { ?>

                <div class="help" style="margin-bottom:10px;">총 <?php echo (int)$rp_total; ?>건 / 페이지당 20개</div>

                <table class="tbl" style="width:100%; border-collapse:collapse;">
                    <thead>
                    <tr>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #eef0f3;">날짜 및 시간</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #eef0f3;">신고한 닉네임</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #eef0f3;">신고당한 닉네임</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #eef0f3;">신고사유</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid #eef0f3;">신고누적횟수</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid #eef0f3;">현재 밴 상태</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid #eef0f3;">벤시키기</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rp_rows as $r) { ?>
                        <?php
                        $tid = (string)$r['target_id'];
                        $cnt = 0;

                        // 신고 누적: member.mb_3 사용(현재 chat_report_save.php 기준)
                        $mrow = sql_fetch("select mb_3 as cnt from {$g5['member_table']} where mb_id='".sql_real_escape_string($tid)."' limit 1 ");
                        if ($mrow) $cnt = (int)($mrow['cnt'] ?? 0);

                        // 현재 밴 상태
                        $ban_state = '정상';
                        $ban_row = sql_fetch("
  select is_active, ban_until
  from {$tbl_ban}
  where mb_id='".sql_real_escape_string($tid)."'
  order by id desc
  limit 1
");

if ($ban_row && (int)$ban_row['is_active'] === 1) {
  $until = isset($ban_row['ban_until']) ? trim($ban_row['ban_until']) : '';

  // ban_until이 비어있거나 0000이면 영구로 간주
  if ($until === '' || $until === '0000-00-00 00:00:00') {
    $ban_state = '밴중';
  } else {
    // 만료되지 않았을 때만 밴중
    if (strtotime($until) > time()) $ban_state = '밴중';
  }
}

                        ?>
                        <tr>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6;"><?php echo h($r['created_at']); ?></td>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6;">
                                <?php echo h($r['reporter_nick']); ?>
                                <div class="help" style="margin:2px 0 0 0;"><?php echo h($r['reporter_id']); ?></div>
                            </td>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6;">
                                <?php echo h($r['target_nick']); ?>
                                <div class="help" style="margin:2px 0 0 0;"><?php echo h($r['target_id']); ?></div>
                            </td>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6;"><?php echo h($r['reason']); ?></td>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:800;"><?php echo (int)$cnt; ?></td>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:800;">
                                <?php echo h($ban_state); ?>
                            </td>
                            <td style="padding:10px; border-bottom:1px solid #f3f4f6; text-align:center;">
                                <select id="rp-ban-min-<?php echo (int)$r['id']; ?>" style="padding:6px 8px; border:1px solid #dcdfe4; border-radius:8px;">
                                    <option value="10">10분</option>
                                    <option value="30">30분</option>
                                    <option value="60">60분</option>
                                    <option value="1440">1일</option>
                                    <option value="0">영구</option>
                                </select>
                                <button
                                    type="button"
                                    class="btn red"
                                    data-rp-ban="1"
                                    data-rp-id="<?php echo (int)$r['id']; ?>"
                                    data-rp-mb="<?php echo h($tid); ?>"
                                    style="margin-left:8px;"
                                >밴</button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <?php
                $rp_pages = (int)ceil($rp_total / $rp_per);
                if ($rp_pages < 1) $rp_pages = 1;

                $base = G5_PLUGIN_URL.'/chat/chat_report_admin.php';
                if ($rp_q !== '') $base .= '&q='.urlencode($rp_q);
                ?>

                <div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:flex-end; margin-top:12px;">
                    <?php for($p=1; $p<=$rp_pages; $p++){ ?>
                        <?php
                        $u = $base.'&page='.$p;
                        $on = ($p === $rp_page) ? 'background:#0b3a6a;color:#fff;border-color:#0b3a6a;' : '';
                        ?>
                        <a href="<?php echo h($u); ?>" style="text-decoration:none; border:1px solid #dcdfe4; padding:6px 10px; border-radius:8px; font-weight:800; <?php echo $on; ?>">
                            <?php echo (int)$p; ?>
                        </a>
                    <?php } ?>
                </div>

            <?php } ?>

        </div>
    </div>

<?php } ?>

</div>

<?php if ($tab !== 'reports') { ?>
<script>
(function(){
  var AJAX = "<?php echo $CHAT_AJAX_URL; ?>";

  function post(body){
    return fetch(AJAX, {
      method:'POST',
      credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: body
    }).then(function(r){ return r.json(); });
  }

  var btnFreeze = document.getElementById('adm-freeze-apply');
  if (btnFreeze) {
    btnFreeze.addEventListener('click', function(){
      var freeze = document.getElementById('adm-freeze').checked ? 1 : 0;
      post('act=admin_freeze&freeze=' + encodeURIComponent(freeze)).then(function(j){
        if(!j || j.ok!==1) return alert(j && j.msg ? j.msg : '실패');
        alert('적용되었습니다.');
      });
    });
  }

  var btnBan = document.getElementById('adm-ban-apply');
  if (btnBan) {
    btnBan.addEventListener('click', function(){
      var nick = (document.getElementById('adm-ban-nick').value||'').trim();
      var min = document.getElementById('adm-ban-min').value||'10';
      var reason = (document.getElementById('adm-ban-reason').value||'').trim();
      if(!nick) return alert('닉네임을 입력하세요.');
      if(!confirm('['+nick+'] 닉네임을 밴 처리할까요?')) return;

      post(
        'act=admin_ban_nick'
        + '&nick=' + encodeURIComponent(nick)
        + '&min=' + encodeURIComponent(min)
        + '&reason=' + encodeURIComponent(reason)
      ).then(function(j){
        if(!j || j.ok!==1) return alert(j && j.msg ? j.msg : '실패');
        alert('밴 처리 완료');
      });
    });
  }

  var btnClear = document.getElementById('adm-clear-chat');
  if (btnClear) {
    btnClear.addEventListener('click', function(){
      if(!confirm('전체 채팅을 삭제할까요? (복구 불가)')) return;
      post('act=admin_clear_chat').then(function(j){
        if(!j || j.ok!==1) return alert(j && j.msg ? j.msg : '실패');
        alert('삭제 완료');
      });
    });
  }

  var btnSave = document.getElementById('adm-save-config');
  if (btnSave) {
    btnSave.addEventListener('click', function(){
      var spamSec = document.getElementById('adm-spam-sec').value||'2';
      var repeatSec = document.getElementById('adm-repeat-sec').value||'30';
      var reportLim = document.getElementById('adm-report-limit').value||'10';
      var autobanMin = document.getElementById('adm-autoban-min').value||'10';
      var onlineFakeAdd = document.getElementById('adm-online-fake-add').value||'0';

      var noticeText = document.getElementById('adm-notice-text').value||'';
      var ruleText   = document.getElementById('adm-rule-text').value||'';
      var badwords   = document.getElementById('adm-badwords').value||'';

      post(
        'act=admin_config_save'
        + '&spam_sec=' + encodeURIComponent(spamSec)
        + '&repeat_sec=' + encodeURIComponent(repeatSec)
        + '&report_limit=' + encodeURIComponent(reportLim)
        + '&autoban_min=' + encodeURIComponent(autobanMin)
        + '&online_fake_add=' + encodeURIComponent(onlineFakeAdd)
        + '&notice_text=' + encodeURIComponent(noticeText)
        + '&rule_text=' + encodeURIComponent(ruleText)
        + '&badwords=' + encodeURIComponent(badwords)
      ).then(function(j){
        if(!j || j.ok!==1) return alert(j && j.msg ? j.msg : '실패');
        alert('저장 완료');
      });
    });
  }
})();
</script>
<?php } else { ?>
<script>
(function(){
  var AJAX = "<?php echo $CHAT_AJAX_URL; ?>";

  function post(body){
    return fetch(AJAX, {
      method:'POST',
      credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: body
    }).then(function(r){ return r.json(); });
  }

  // 검색 버튼
  var btnSearch = document.getElementById('rp-search');
  if (btnSearch) {
    btnSearch.addEventListener('click', function(){
      var q = (document.getElementById('rp-q').value || '').trim();
      var url = "<?php echo G5_PLUGIN_URL; ?>/chat/chat_report_admin.php";
      if (q) url += "&q=" + encodeURIComponent(q);
      location.href = url;
    });
  }

  // 최근 신고에서 밴 버튼
  document.addEventListener('click', function(e){
    var btn = e.target.closest('button[data-rp-ban="1"]');
    if(!btn) return;

    var rid = btn.getAttribute('data-rp-id') || '';
    var mb  = btn.getAttribute('data-mb') || '';
    var nick= btn.getAttribute('data-nick') || '';

    if(!rid || !mb) return alert('대상 정보가 없습니다.');

    var sel = document.getElementById('rp-ban-min-' + rid);
    var min = sel ? (sel.value || '10') : '10';

    if(!confirm('['+nick+'] 밴 처리할까요?')) return;

    post(
      'act=admin_ban'
      + '&mb_id=' + encodeURIComponent(mb)
      + '&min=' + encodeURIComponent(min)
      + '&reason=' + encodeURIComponent('최근 신고에서 밴')
    ).then(function(j){
      if(!j || j.ok !== 1) return alert(j && j.msg ? j.msg : '밴 실패');
      alert('밴 처리 완료');
      location.reload();
    });
  }, true);
})();
</script>
<?php } ?>

  </main>
</div>

<?php
$tail_sub = G5_ADMIN_PATH.'/admin.tail.sub.php';
$tail     = G5_ADMIN_PATH.'/admin.tail.php';

if (is_file($tail_sub)) {
    include_once($tail_sub);
} else {
    include_once($tail);
}
?>
