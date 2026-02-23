<?php
// /plugin/chat/chat_banlist.php
if (!defined('_GNUBOARD_')) {
    include_once(__DIR__ . '/../../common.php');
}
include_once(G5_PLUGIN_PATH.'/chat/_common.php');

if (!isset($is_admin) || !$is_admin) die('Access denied.');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

$status = isset($_GET['status']) ? $_GET['status'] : 'active'; // active|expired|all
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$tbl_ban = 'g5_chat_ban';

// 만료 자동 정리
sql_query("
    update {$tbl_ban}
    set is_active = 0,
        unbanned_at = if(unbanned_at is null, now(), unbanned_at)
    where is_active = 1
      and ban_until is not null
      and ban_until <> '0000-00-00 00:00:00'
      and ban_until <= now()
", false);

// 해제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_unban'])) {
    $mb_id = trim($_POST['mb_id']);
    if ($mb_id !== '') {
        sql_query("
            update {$tbl_ban}
            set is_active = 0,
                unbanned_by = '".sql_real_escape_string($member['mb_id'])."',
                unbanned_at = now(),
                updated_at = now()
            where mb_id = '".sql_real_escape_string($mb_id)."'
        ", false);
    }
    // redirect
    $redir = G5_PLUGIN_URL.'/chat/chat_banlist.php?status='.urlencode($status).'&q='.urlencode($q);
    goto_url($redir);
}

$where = " where 1 ";
if ($status === 'active') $where .= " and is_active = 1 ";
else if ($status === 'expired') $where .= " and is_active = 0 ";
if ($q !== '') {
    $qq = sql_real_escape_string($q);
    $where .= " and (mb_nick like '%{$qq}%' or mb_id like '%{$qq}%') ";
}

$list = array();
$rs = sql_query(" select * from {$tbl_ban} {$where} order by banned_at desc limit 300 ");
while($r = sql_fetch_array($rs)) $list[] = $r;

?>
<!doctype html>
<html lang="ko">
<head>
    <link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/admin_extend.css">
<link rel="stylesheet" href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_admin_style.css?ver=20260102">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>밴리스트</title>

</head>
<body class="sp-chat-admin">
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
      <a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_admin.php?tab=manage">채팅관리</a>
      <a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_notice.php">공지/규정/금칙어</a>
      <a href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_report_admin.php">최근신고</a>
      <a class="on" href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_banlist.php">밴리스트</a>
    </nav>
  </aside>

  <div class="sp-main">
    <div class="sp-topbar">
      <div>
        <div class="sp-title">밴리스트</div>
        <div class="sp-sub">자동/수동 밴 상태를 확인하고 해제할 수 있습니다.</div>
      </div>
      <div class="sp-sub">관리자: <?php echo h($member['mb_id']); ?></div>
    </div>

    <div class="sp-content">
      <div class="sp-card">
        <div class="sp-card-head"><div class="meta"><div class="h">밴 목록</div><div class="d">상태/검색으로 필터링할 수 있습니다.</div></div><div class="sp-toolbar"><form method="get" class="filters">
            <select name="status">
                <option value="active" <?php echo ($status==='active'?'selected':''); ?>>활성(밴중)</option>
                <option value="expired" <?php echo ($status==='expired'?'selected':''); ?>>만료/해제</option>
                <option value="all" <?php echo ($status==='all'?'selected':''); ?>>전체</option>
            </select>
            <input type="text" name="q" value="<?php echo h($q); ?>" placeholder="닉네임/아이디 검색" style="min-width:220px;">
            <button class="btn primary" type="submit">검색</button>
            <a class="btn gray" href="<?php echo G5_PLUGIN_URL; ?>/chat/chat_banlist.php">초기화</a>
        </form>

        <div class="sp-table-wrap">
<table class="tbl">
            <thead>
                <tr>
                    <th style="width:72px;">상태</th>
                    <th style="width:140px;">닉네임</th>
                    <th style="width:140px;">아이디</th>
                    <th style="width:160px;">밴 시작</th>
                    <th style="width:90px;">기간</th>
                    <th style="width:160px;">끝나는 시각</th>
                    <th>사유 / 관리자</th>
                    <th style="width:120px;">IP / 신고</th>
                    <th style="width:92px;">해제</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!count($list)) { ?>
                <tr><td colspan="9" style="text-align:center;color:#6b7280;padding:30px 8px;">데이터가 없습니다.</td></tr>
            <?php } ?>
            <?php foreach($list as $r) {
                $is_active = (int)$r['is_active'] === 1;
                $dur = isset($r['duration_min']) ? (int)$r['duration_min'] : 0;
                $until = isset($r['ban_until']) ? $r['ban_until'] : '';
            ?>
                <tr>
                    <td>
                        <?php if ($is_active) { ?>
                            <span class="tag on">밴중</span>
                        <?php } else { ?>
                            <span class="tag off">해제</span>
                        <?php } ?>
                    </td>
                    <td><?php echo h($r['mb_nick']); ?></td>
                    <td class="mono"><?php echo h($r['mb_id']); ?></td>
                    <td class="mono"><?php echo h($r['banned_at']); ?></td>
                    <td><?php echo ($dur>0 ? h($dur.'분') : '영구'); ?></td>
                    <td class="mono"><?php echo ($until ? h($until) : '-'); ?></td>
                    <td>
                        <div><?php echo h($r['reason']); ?></div>
                        <div class="small">
                            banned_by: <span class="mono"><?php echo h($r['banned_by']); ?></span>
                            <?php if (!$is_active && $r['unbanned_at']) { ?>
                                <br>unbanned_by: <span class="mono"><?php echo h($r['unbanned_by']); ?></span> /
                                <span class="mono"><?php echo h($r['unbanned_at']); ?></span>
                            <?php } ?>
                        </div>
                        <div class="small" style="margin-top:6px;">
                            [밴 당시 채팅기록] [신고기록] (추후 링크 예정)
                        </div>
                    </td>
                    <td>
                        <div class="mono"><?php echo h($r['ip_at_ban']); ?></div>
                        <div class="small">신고: <?php echo (int)$r['report_count']; ?></div>
                    </td>
                    <td class="actions">
                        <?php if ($is_active) { ?>
                        <form method="post" onsubmit="return confirm('해제하시겠습니까?');">
                            <input type="hidden" name="mb_id" value="<?php echo h($r['mb_id']); ?>">
                            <input type="hidden" name="do_unban" value="1">
                            <button class="btn primary" type="submit" style="width:80px;">해제</button>
                        </form>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
</div>
          </div>
    </div>
  </div>
</div>
</body>
</html>