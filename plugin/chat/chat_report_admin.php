<?php
// /plugin/chat/chat_report_admin.php
// 최근 신고(자동화 체크리스트) 전용 페이지
// - chat_admin.php(설정/운영)과 분리
// - 기능 로직은 chat_ajax.php(기존) 호출로 처리
// - UI/조회만 담당 (퇴보 방지)

if (!defined('_GNUBOARD_')) {
    include_once(__DIR__ . '/../../common.php');
}
include_once(G5_PLUGIN_PATH.'/chat/_common.php');

if (!isset($is_admin) || !$is_admin) die('Access denied.');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

// 기존 AJAX 엔드포인트(밴/해제 등은 여기로 보냄)
$CHAT_AJAX_URL = G5_PLUGIN_URL.'/chat/chat_ajax.php';

// ✅ 신고 테이블 선택 (무조건 chat_report 사용, prefix/실테이블 자동보정)
$tbl_pref = isset($g5['table_prefix']) ? (string)$g5['table_prefix'] : '';
$tbl1 = $tbl_pref.'chat_report';          // 기본(권장): g5_chat_report
$tbl2 = 'g5_chat_report';                // 강제 예비(운영에서 실제로 존재하는 경우)
$tbl3 = 'chat_report';                   // 최후 예비(prefix 없이 만들어진 경우)

// 테이블 row count 안전 조회 (오류면 -1 반환)
$sp_table_count = function($t) {
    $t = trim((string)$t);
    if ($t === '') return -1;
    $row = sql_fetch("SELECT COUNT(*) AS cnt FROM `{$t}`");
    if (!$row || !isset($row['cnt'])) return -1;
    return (int)$row['cnt'];
};

$c1 = $sp_table_count($tbl1);
$c2 = $sp_table_count($tbl2);
$c3 = $sp_table_count($tbl3);

// ✅ 우선순위: (1) prefix chat_report에 데이터 있으면 그걸 사용
//            (2) g5_chat_report에 데이터 있으면 그걸 사용
//            (3) chat_report에 데이터 있으면 그걸 사용
//            (4) 그 외에는 tbl1(기본명) 사용
if ($c1 > 0)      $use_table = $tbl1;
else if ($c2 > 0) $use_table = $tbl2;
else if ($c3 > 0) $use_table = $tbl3;
else              $use_table = $tbl1;

// 컬럼 호환(구버전/신버전) 체크: target_nick vs reported_nick
function sp_col_exists($table, $col){
    $col = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$col);
    if ($col === '') return false;
    $row = sql_fetch("SHOW COLUMNS FROM {$table} LIKE '{$col}'");
    return ($row && !empty($row));
}
$COL_TARGET_NICK = sp_col_exists($use_table, 'target_nick') ? 'target_nick' : (sp_col_exists($use_table, 'reported_nick') ? 'reported_nick' : '');

// 신고사유 매핑(필요시 추후 확장)
$REASON_MAP = array(
    'spam'      => '도배/광고',
    'abuse'     => '욕설/비방',
    'harass'    => '괴롭힘/혐오',
    'sexual'    => '음란/선정',
    'gambling'  => '도박/불법',
    'etc'       => '기타',
);

function map_reason($reason, $REASON_MAP){
    $r = trim((string)$reason);
    if ($r === '') return '';
    $key = strtolower($r);
    if (isset($REASON_MAP[$key])) return $REASON_MAP[$key];
    // 이미 한글/문장으로 들어온 경우 그대로 노출
    return $r;
}

// 온라인 판정: 회원 테이블 mb_today_login 기준(기본 5분)
function is_online($mb_id){
    global $g5;
    $id = sql_real_escape_string((string)$mb_id);
    $row = sql_fetch(" select mb_today_login from {$g5['member_table']} where mb_id='{$id}' limit 1 ");
    if (!$row || empty($row['mb_today_login'])) return false;
    $t = strtotime($row['mb_today_login']);
    if ($t <= 0) return false;
    return (time() - $t) <= 300; // 5분
}

// 밴 상태 텍스트
function ban_state_of($mb_id){
    global $tbl_ban;

    $id = sql_real_escape_string((string)$mb_id);
    $row = sql_fetch(" SELECT is_active, ban_until FROM {$tbl_ban} WHERE mb_id='{$id}' ORDER BY id DESC LIMIT 1 ");

    // 기본: 정상
    $ret = array('label'=>'정상', 'until'=>'', 'is_ban'=>false);

    if (!$row) return $ret;
    if ((int)($row['is_active'] ?? 0) !== 1) return $ret;

    $until = isset($row['ban_until']) ? trim($row['ban_until']) : '';

    // 영구정지(만료값이 없거나 0이면 영구로 간주)
    if ($until === '' || $until === '0000-00-00 00:00:00') {
        return array('label'=>'영구정지', 'until'=>'영구', 'is_ban'=>true);
    }

    $ts = strtotime($until);
    if ($ts <= time()) return $ret; // 이미 만료

    $remain_min = (int)ceil(($ts - time()) / 60);

    // 표시 라벨은 요구사항 고정: 정상/채금10분/채금60분/채금600분/영구정지
    if ($remain_min <= 10)       $label = '채금10분';
    else if ($remain_min <= 60)  $label = '채금60분';
    else                         $label = '채금600분';

    return array('label'=>$label, 'until'=>$until, 'is_ban'=>true);
}

// 최근 신고 리스트 조회
function fetch_reports($use_table, $q, $page, $per){
    global $COL_TARGET_NICK;

    // 안전한 테이블 표기(백틱)
    $tbl = '`'.str_replace('`','', (string)$use_table).'`';
    $where = " 1 ";
    if ($q !== '') {
        $qq = sql_real_escape_string($q);
        $conds = array();
        $conds[] = "reporter_id LIKE '%{$qq}%'";
        $conds[] = "reporter_nick LIKE '%{$qq}%'";
        $conds[] = "target_id LIKE '%{$qq}%'";
        if (!empty($COL_TARGET_NICK)) {
            $conds[] = "{$COL_TARGET_NICK} LIKE '%{$qq}%'";
        }
        $conds[] = "reason LIKE '%{$qq}%'";
        $where .= " AND (".implode(' OR ', $conds).") ";
    }

    $cRow = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$tbl} WHERE {$where} ");
    $total = (int)($cRow && isset($cRow['cnt']) ? $cRow['cnt'] : 0);

    $offset = max(0, ($page-1) * $per);
    $rs = sql_query("
        SELECT r.*
        , (SELECT COUNT(*) FROM {$tbl} rr WHERE rr.target_id = r.target_id) AS target_cnt
        FROM {$tbl} r
        WHERE {$where}
        ORDER BY r.id DESC
        LIMIT {$offset}, {$per}
    ");
    $rows = array();
    while ($r = sql_fetch_array($rs)) $rows[] = $r;

    return array($rows, $total);
}

// AJAX 모드: tbody HTML + 요약을 JSON으로 반환(자동 갱신용)
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per  = 30;

    list($rows, $total) = fetch_reports($use_table, $q, $page, $per);

    ob_start();
    foreach ($rows as $r) {
        $tid = (string)$r['target_id'];
        $rid = (string)$r['reporter_id'];

        // 누적 신고수: member.mb_3 사용(현재 chat_report_save.php 기준)
        // 누적 신고수: report 테이블 기준(target_cnt) 사용(저장/동기화 이슈 방지)
        $cnt = (int)($r['target_cnt'] ?? 0);

        $ban = ban_state_of($tid);
        $online = is_online($tid);

        $state_txt = $ban['label'];
        $state_cls = ($state_txt === '정상') ? 'st-ok' : 'st-ban';
        $online_txt = $online ? '온라인' : '오프라인';

        $reason = map_reason($r['reason'] ?? '', $REASON_MAP);

        $ip = '';
        if (isset($r['ip'])) $ip = (string)$r['ip'];
        else if (isset($r['reporter_ip'])) $ip = (string)$r['reporter_ip'];
        else if (isset($r['target_ip'])) $ip = (string)$r['target_ip'];

        ?>
        <tr data-report-id="<?php echo (int)$r['id']; ?>" data-target-id="<?php echo h($tid); ?>" data-reporter-id="<?php echo h($rid); ?>" data-target-nick="<?php echo h($COL_TARGET_NICK ? ($r[$COL_TARGET_NICK] ?? '') : ($r['reported_nick'] ?? '')); ?>">
            <td class="td-dt"><?php echo h($r['created_at'] ?? ''); ?></td>

            <td class="td-nick">
                <div class="nick"><?php echo h($COL_TARGET_NICK ? ($r[$COL_TARGET_NICK] ?? '') : ($r['reported_nick'] ?? '')); ?></div>
                <div class="sub"><?php echo h($tid); ?></div>
            </td>

            <td class="td-nick">
                <div class="nick"><?php echo h($r['reporter_nick'] ?? ''); ?></div>
                <div class="sub"><?php echo h($rid); ?></div>
            </td>

            <td class="td-reason"><?php echo h($reason); ?></td>

            <td class="td-btn">
                <button type="button" class="btn mini" data-act="log">채팅로그</button>
            </td>

            <td class="td-num"><?php echo (int)$cnt; ?></td>

            <td class="td-state">
                <span class="state <?php echo h($state_cls); ?>"><?php echo h($state_txt); ?></span>
                <div class="sub" style="margin-top:2px;">
                    <?php echo h($online_txt); ?><?php if (!empty($ban['is_ban']) && !empty($ban['until'])) echo ' · '.h($ban['until']); ?>
                </div>
            </td>


            <td class="td-ip"><?php echo h($ip); ?></td>

            <td class="td-act">
                <select class="sel" data-act="banmin">
                    <option value="unban">▼ 밴해제</option>
                    <option value="10">▼ 10분</option>
                    <option value="60">▼ 60분</option>
                    <option value="600">▼ 600분</option>
                    <option value="0">▼ 영구정지</option>
                </select>
                <button type="button" class="btn danger mini" data-act="apply">적용</button>
            </td>
        </tr>
        <?php
    }
    $tbody = ob_get_clean();

    header('Content-Type: application/json; charset=UTF-8');
	$payload = array(
		'ok' => 1,
		'total' => $total,
		'tbody' => $tbody,
	);
	// 디버그(요청 시): 실제 사용 테이블/컬럼/COUNT 확인
	if (isset($_GET['debug']) && $_GET['debug'] == '1') {
		$tbl_dbg = '`'.str_replace('`','', (string)$use_table).'`';
		$cnt_dbg = sql_fetch("SELECT COUNT(*) AS cnt FROM {$tbl_dbg}");
		$payload['debug'] = array(
			'use_table' => $use_table,
			'COL_TARGET_NICK' => $COL_TARGET_NICK,
			'count_all' => (int)($cnt_dbg['cnt'] ?? 0),
			'q' => isset($_GET['q']) ? (string)$_GET['q'] : '',
			'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
		);
	}
	echo json_encode($payload);
    exit;
}

// ----------- 페이지 렌더 -----------
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per  = 30;

list($rows, $total) = fetch_reports($use_table, $q, $page, $per);

// 관리자 헤더
$g5['title'] = '최근 신고';
add_stylesheet('<link rel="stylesheet" href="'.G5_PLUGIN_URL.'/chat/chat_admin_style.css?ver=20260102">', 0);
$head_sub = G5_ADMIN_PATH.'/admin.head.sub.php';
$head     = G5_ADMIN_PATH.'/admin.head.php';
if (is_file($head_sub)) include_once($head_sub);
else include_once($head);
?>

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
      <a href="<?php echo h(G5_PLUGIN_URL.'/chat/chat_admin.php?tab=manage'); ?>">채팅관리</a>
      <a href="<?php echo h(G5_PLUGIN_URL.'/chat/chat_notice.php'); ?>">공지/규정/금칙어</a>
      <a class="on" href="<?php echo h(G5_PLUGIN_URL.'/chat/chat_report_admin.php'); ?>">최근신고</a>
      <a href="<?php echo h(G5_PLUGIN_URL.'/chat/chat_banlist.php'); ?>">밴리스트</a>
    </nav>
  </aside>

  <div class="sp-main">
    <div class="sp-topbar">
      <div>
        <div class="sp-title">최근 신고</div>
        <div class="sp-sub">신고 누적/제재 상태를 빠르게 확인합니다.</div>
      </div>
      <div class="sp-sub">관리자: <?php echo h($member['mb_id']); ?></div>
    </div>

    <div class="sp-content">
      <div class="sp-card">
    <div class="sp-card-head">
      <div>
        <h2>최근 신고</h2>
        <div class="meta">
          <span id="rp-meta">총 <?php echo (int)$total; ?>건 · 페이지당 <?php echo (int)$per; ?>개</span>
          <span class="meta" style="margin-left:8px;">(자동 갱신 15초)</span>
        </div>
      </div>

      <div class="search">
        <input type="text" id="rp-q" value="<?php echo h($q); ?>" placeholder="신고자/대상 닉네임/아이디/사유 검색">
        <button type="button" class="btn primary" id="rp-search">검색</button>
        <button type="button" class="btn" id="rp-refresh">새로고침</button>
      </div>
    </div>

    <div class="table-wrap">
      <div class="sp-table-wrap">
<table class="tbl tbl">
        <thead>
          <tr>
            <th>날짜&시간</th>
            <th>신고당한닉네임</th>
            <th>신고한닉네임</th>
            <th>사유</th>
            <th style="text-align:center;">채팅로그</th>
            <th style="text-align:center;">누적횟수</th>
            <th style="text-align:center;">현재상태</th>
            <th>아이피</th>
            <th style="text-align:center;">제재</th>
          </tr>
        </thead>
        <tbody id="rp-tbody">
        <?php if ($total <= 0) { ?>
          <tr><td colspan="9" style="padding:18px; color:#64748b;">신고 내역이 없습니다.</td></tr>
        <?php } else { ?>
          <?php foreach ($rows as $r) {
              $tid = (string)$r['target_id'];
              $rid = (string)$r['reporter_id'];

        // 누적 신고수: report 테이블 기준(target_cnt) 사용(저장/동기화 이슈 방지)
        $cnt = (int)($r['target_cnt'] ?? 0);

        $ban = ban_state_of($tid);
        $online = is_online($tid);

        $state_txt = $ban['label'];
        $state_cls = ($state_txt === '정상') ? 'st-ok' : 'st-ban';
        $online_txt = $online ? '온라인' : '오프라인';

              $reason = map_reason($r['reason'] ?? '', $REASON_MAP);

              $ip = '';
              if (isset($r['ip'])) $ip = (string)$r['ip'];
              else if (isset($r['reporter_ip'])) $ip = (string)$r['reporter_ip'];
              else if (isset($r['target_ip'])) $ip = (string)$r['target_ip'];
          ?>
          <tr data-report-id="<?php echo (int)$r['id']; ?>" data-target-id="<?php echo h($tid); ?>" data-reporter-id="<?php echo h($rid); ?>" data-target-nick="<?php echo h($COL_TARGET_NICK ? ($r[$COL_TARGET_NICK] ?? '') : ($r['reported_nick'] ?? '')); ?>">
            <td class="td-dt"><?php echo h($r['created_at'] ?? ''); ?></td>

            <td class="td-nick">
                <div class="nick"><?php echo h($COL_TARGET_NICK ? ($r[$COL_TARGET_NICK] ?? '') : ($r['reported_nick'] ?? '')); ?></div>
              <div class="sub"><?php echo h($tid); ?></div>
            </td>

            <td class="td-nick">
              <div class="nick"><?php echo h($r['reporter_nick'] ?? ''); ?></div>
              <div class="sub"><?php echo h($rid); ?></div>
            </td>

            <td class="td-reason"><?php echo h($reason); ?></td>

            <td class="td-btn">
              <button type="button" class="btn mini" data-act="log">채팅로그</button>
            </td>

            <td class="td-num"><?php echo (int)$cnt; ?></td>
            <td class="td-state">
                <span class="state <?php echo h($state_cls); ?>"><?php echo h($state_txt); ?></span>
                <div class="sub" style="margin-top:2px;">
                    <?php echo h($online_txt); ?><?php if (!empty($ban['is_ban']) && !empty($ban['until'])) echo ' · '.h($ban['until']); ?>
                </div>
            </td>


            <td class="td-ip"><?php echo h($ip); ?></td>

            <td class="td-act">
              <select class="sel" data-act="banmin">
                <option value="unban">▼ 밴해제</option>
                <option value="10">▼ 10분</option>
                <option value="60">▼ 60분</option>
                <option value="600">▼ 600분</option>
                <option value="0">▼ 영구정지</option>
              </select>
              <button type="button" class="btn danger mini" data-act="apply">적용</button>
            </td>
          </tr>
          <?php } ?>
        <?php } ?>
        </tbody>
      </table>
</div>
    </div>

    <?php
      $pages = (int)ceil($total / $per);
      if ($pages < 1) $pages = 1;
      $base = G5_PLUGIN_URL.'/chat/chat_report_admin.php';
      if ($q !== '') $base .= '?q='.urlencode($q);
      $join = ($q !== '') ? '&' : '?';
    ?>
    <?php if ($pages > 1) { ?>
    <div class="pager">
      <?php for($p=1; $p<=$pages; $p++){
        $u = $base . $join . 'page=' . $p;
        $on = ($p === $page) ? 'on' : '';
      ?>
        <a class="<?php echo $on; ?>" href="<?php echo h($u); ?>"><?php echo (int)$p; ?></a>
      <?php } ?>
    </div>
    <?php } ?>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var AJAX = "<?php echo h($CHAT_AJAX_URL); ?>";
  var REFRESH_MS = 15000;

  function post(body){
    return fetch(AJAX, {
      method:'POST',
      credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: body
    }).then(function(r){ return r.json(); });
  }

  function qSel(sel){ return document.querySelector(sel); }
  function encodeQuery(obj){
    var parts = [];
    for (var k in obj){
      if (!Object.prototype.hasOwnProperty.call(obj, k)) continue;
      parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]));
    }
    return parts.join('&');
  }

  // 검색
  var btnSearch = qSel('#rp-search');
  if (btnSearch){
    btnSearch.addEventListener('click', function(){
      var q = (qSel('#rp-q').value || '').trim();
      var url = "<?php echo h(G5_PLUGIN_URL.'/chat/chat_report_admin.php'); ?>";
      if (q) url += "?q=" + encodeURIComponent(q);
      location.href = url;
    });
  }
  var btnRefresh = qSel('#rp-refresh');
  if (btnRefresh){
    btnRefresh.addEventListener('click', function(){ refreshNow(true); });
  }

  // 자동 갱신
  var refreshing = false;
  function refreshNow(manual){
    if (refreshing) return;
    refreshing = true;

    var q = (qSel('#rp-q').value || '').trim();
    var page = <?php echo (int)$page; ?>;

    var url = "<?php echo h(G5_PLUGIN_URL.'/chat/chat_report_admin.php'); ?>?ajax=1&" + encodeQuery({q:q, page:page});
    fetch(url, {credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(function(j){
        if (!j || j.ok !== 1) return;
        var tbody = qSel('#rp-tbody');
        if (tbody) tbody.innerHTML = j.tbody || '';
        var meta = qSel('#rp-meta');
        if (meta) meta.textContent = "총 " + (j.total||0) + "건 · 페이지당 <?php echo (int)$per; ?>개";
      })
      .catch(function(){})
      .finally(function(){ refreshing = false; });
  }
  setInterval(function(){ refreshNow(false); }, REFRESH_MS);

  // 이벤트 위임: 채팅로그/제재 적용
  document.addEventListener('click', function(e){
    var btn = e.target.closest('button[data-act]');
    if (!btn) return;

    var act = btn.getAttribute('data-act');
    var tr = btn.closest('tr[data-report-id]');
    if (!tr) return;

    var reportId = tr.getAttribute('data-report-id') || '';
    var targetId = tr.getAttribute('data-target-id') || '';
    var reporterId = tr.getAttribute('data-reporter-id') || '';

    if (act === 'log'){
      var targetNick = tr.getAttribute('data-target-nick') || '';
      var url = "<?php echo h(G5_PLUGIN_URL.'/chat/chat_report_log.php'); ?>"
        + "?target_id=" + encodeURIComponent(targetId)
        + "&rid=" + encodeURIComponent(reportId)
        + "&nick=" + encodeURIComponent(targetNick);
      window.open(url, 'report_log', 'width=1100,height=800,scrollbars=yes');
      return;
    }

    }

    if (act === 'apply'){
      var sel = tr.querySelector('select[data-act="banmin"]');
      var v = sel ? (sel.value || '') : '';
      if (!v) return;

      if (v === 'unban'){
        if (!confirm('['+targetId+'] 밴을 해제할까요?')) return;
        // chat_ajax.php에 admin_unban이 없을 수 있어, 실패 시 안내
        post('act=admin_unban&mb_id=' + encodeURIComponent(targetId)).then(function(j){
          if (!j || j.ok !== 1) {
            // fallback: min=0으로 해제 요청(서버 구현에 따라 다름)
            return post('act=admin_ban&mb_id=' + encodeURIComponent(targetId) + '&min=' + encodeURIComponent(-1) + '&reason=' + encodeURIComponent('신고관리에서 해제'));
          }
          return j;
        }).then(function(j2){
          if (!j2 || j2.ok !== 1) return alert((j2 && j2.msg) ? j2.msg : '해제 실패(서버에 admin_unban 구현 필요)');
          refreshNow(true);
        });
        return;
      }

      var min = v; // 10/60/600/0
      var label = (min === '0') ? '영구정지' : (min + '분');
      if (!confirm('['+targetId+'] ' + label + ' 밴 처리할까요?')) return;

      post(
        'act=admin_ban'
        + '&mb_id=' + encodeURIComponent(targetId)
        + '&min=' + encodeURIComponent(min)
        + '&reason=' + encodeURIComponent('최근 신고에서 제재')
      ).then(function(j){
        if (!j || j.ok !== 1) return alert((j && j.msg) ? j.msg : '제재 실패');
        refreshNow(true);
      });
      return;
    }
  }, true);

})();
</script>

<?php
$tail_sub = G5_ADMIN_PATH.'/admin.tail.sub.php';
$tail     = G5_ADMIN_PATH.'/admin.tail.php';
if (is_file($tail_sub)) include_once($tail_sub);
else include_once($tail);
?>