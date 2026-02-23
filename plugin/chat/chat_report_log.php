<?php
// /plugin/chat/chat_report_log.php
// 최근신고(체크리스트) "채팅로그" 상세 페이지 (새창)

include_once('../../common.php');
include_once(G5_PLUGIN_PATH.'/chat/_common.php');

if (!isset($is_admin) || !$is_admin) {
    http_response_code(403);
    echo '권한이 없습니다.';
    exit;
}

// ---- 테이블 ----
$tbl_chat = isset($g5['chat_table']) ? $g5['chat_table'] : (isset($g5['chat_msg_table']) ? $g5['chat_msg_table'] : 'g5_chat');
$tbl_ban  = isset($g5['chat_ban_table']) ? $g5['chat_ban_table'] : 'g5_chat_ban';
$tbl_member = $g5['member_table'];

// 신고 테이블 자동 감지
$tbl_report = '';
$chk = sql_fetch("SHOW TABLES LIKE 'g5_chat_report2'");
if ($chk && count($chk)) $tbl_report = 'g5_chat_report2';
if ($tbl_report === '') {
    $chk = sql_fetch("SHOW TABLES LIKE 'g5_chat_report'");
    if ($chk && count($chk)) $tbl_report = 'g5_chat_report';
}

// ---- 파라미터 ----
$nick = isset($_GET['nick']) ? trim($_GET['nick']) : '';
$rid  = isset($_GET['rid']) ? (int)$_GET['rid'] : 0; // report id (있으면 시점 기준으로 뽑음)
// 호환: report_id / target_id 파라미터 지원
if (!$rid && isset($_GET['report_id'])) $rid = (int)$_GET['report_id'];
if ($nick === '' && isset($_GET['target_id'])) $nick = trim((string)$_GET['target_id']);
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 120;
if ($limit < 30) $limit = 30;
if ($limit > 300) $limit = 300;

// 시간 범위(분)
$window_min = isset($_GET['win']) ? (int)$_GET['win'] : 15; // 기본 ±15분
if ($window_min < 3) $window_min = 3;
if ($window_min > 180) $window_min = 180;

$report = null;
$target_mb = null;
$reporter_mb = null;

$range_from = '';
$range_to   = '';

if ($rid > 0 && $tbl_report !== '') {
    // 신고 테이블 컬럼명이 사이트마다 다를 수 있어 흔한 키를 최대한 커버
    // 우선: id/pk는 rid로 받고, 시간/닉/신고자/사유/IP 후보들을 유연하게 읽음
    $r = sql_fetch("select * from {$tbl_report} where id = {$rid} limit 1");
    if (!$r) $r = sql_fetch("select * from {$tbl_report} where report_id = {$rid} limit 1");
    if ($r) {
        $report = $r;
        // 대상 닉네임 후보
        $cand_target = array('reported_nick','target_nick','to_nick','mb_nick','nick','reported_mb_nick');
        foreach($cand_target as $k){
            if (isset($r[$k]) && trim($r[$k]) !== '') { $nick = trim($r[$k]); break; }
        }

        // 신고자 닉네임 후보
        $reporter_nick = '';
        $cand_reporter = array('reporter_nick','from_nick','by_nick','report_mb_nick','writer_nick');
        foreach($cand_reporter as $k){
            if (isset($r[$k]) && trim($r[$k]) !== '') { $reporter_nick = trim($r[$k]); break; }
        }

        // 신고 시각 후보
        $dt = '';
        $cand_dt = array('created_at','report_datetime','regdate','datetime','created','write_datetime');
        foreach($cand_dt as $k){
            if (isset($r[$k]) && trim($r[$k]) !== '') { $dt = trim($r[$k]); break; }
        }

        if ($dt !== '') {
            $ts = strtotime($dt);
            if ($ts) {
                $from_ts = $ts - ($window_min*60);
                $to_ts   = $ts + ($window_min*60);
                $range_from = date('Y-m-d H:i:s', $from_ts);
                $range_to   = date('Y-m-d H:i:s', $to_ts);
            }
        }

        // 회원 정보
        if ($nick !== '') {
            $target_mb = sql_fetch("select mb_id, mb_nick, mb_today_login from {$tbl_member} where mb_nick='".sql_real_escape_string($nick)."' limit 1");
        }
        if ($reporter_nick !== '') {
            $reporter_mb = sql_fetch("select mb_id, mb_nick, mb_today_login from {$tbl_member} where mb_nick='".sql_real_escape_string($reporter_nick)."' limit 1");
        }

        // report reason / ip
        $reason = '';
        $cand_reason = array('reason','reason_text','report_reason','report_msg','msg');
        foreach($cand_reason as $k){
            if (isset($r[$k]) && trim($r[$k]) !== '') { $reason = trim($r[$k]); break; }
        }
        $rip = '';
        $cand_ip = array('ip','report_ip','remote_addr','writer_ip');
        foreach($cand_ip as $k){
            if (isset($r[$k]) && trim($r[$k]) !== '') { $rip = trim($r[$k]); break; }
        }

        $report['_sp_reason'] = $reason;
        $report['_sp_ip'] = $rip;
        $report['_sp_reporter_nick'] = $reporter_nick;
        $report['_sp_dt'] = $dt;
    }
}

if ($nick === '') {
    echo 'nick 또는 rid 파라미터가 필요합니다.';
    exit;
}

// 밴 상태
$ban_row = null;
if ($target_mb && isset($target_mb['mb_id']) && $target_mb['mb_id']) {
    $ban_row = sql_fetch("select * from {$tbl_ban} where mb_id='".sql_real_escape_string($target_mb['mb_id'])."' order by banned_at desc limit 1");
}

// ---- 로그 조회 ----
$where = " where 1 ";
$where .= " and cm_nick='".sql_real_escape_string($nick)."' ";

// 신고 시점 기준 범위가 있으면 그 범위로, 없으면 최신 limit개
if ($range_from !== '' && $range_to !== '') {
    $where .= " and cm_datetime between '".sql_real_escape_string($range_from)."' and '".sql_real_escape_string($range_to)."' ";
    $sql = "select cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime from {$tbl_chat} {$where} order by cm_id asc limit {$limit}";
} else {
    $sql = "select cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime from {$tbl_chat} {$where} order by cm_id desc limit {$limit}";
}

$list = array();
$rs = sql_query($sql);
while($row = sql_fetch_array($rs)){
    $list[] = $row;
}
if ($range_from === '' || $range_to === '') {
    // 최신 역순으로 뽑은 경우 보기 좋게 다시 오름차순
    $list = array_reverse($list);
}

// 신고자 로그도 함께(있으면)
$reporter_list = array();
if ($report && isset($report['_sp_reporter_nick']) && $report['_sp_reporter_nick'] !== '') {
    $rn = $report['_sp_reporter_nick'];
    $w2 = " where cm_nick='".sql_real_escape_string($rn)."' ";
    if ($range_from !== '' && $range_to !== '') {
        $w2 .= " and cm_datetime between '".sql_real_escape_string($range_from)."' and '".sql_real_escape_string($range_to)."' ";
        $sql2 = "select cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime from {$tbl_chat} {$w2} order by cm_id asc limit 80";
        $rs2 = sql_query($sql2);
        while($row = sql_fetch_array($rs2)) $reporter_list[] = $row;
    }
}

// ---- 출력 ----
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

$ttl = '채팅로그 - '.h($nick);
?><!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $ttl; ?></title>
<style>
    :root{--bg:#f6f7f9;--card:#fff;--line:#e6e8ee;--text:#111827;--muted:#6b7280;--brand:#0b3a6f;--danger:#b91c1c;--good:#047857;}
    body{margin:0;background:var(--bg);color:var(--text);font:14px/1.45 system-ui,-apple-system,Segoe UI,Roboto,'맑은 고딕',sans-serif;}
    .wrap{max-width:1200px;margin:0 auto;padding:16px;}
    .topbar{display:flex;gap:10px;align-items:center;justify-content:space-between;margin-bottom:12px;}
    .title{font-size:16px;font-weight:800;}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;height:34px;padding:0 12px;border-radius:10px;border:1px solid var(--line);background:#fff;color:#111;cursor:pointer;text-decoration:none;font-weight:700;}
    .btn.primary{background:var(--brand);border-color:var(--brand);color:#fff;}
    .btn.danger{background:var(--danger);border-color:var(--danger);color:#fff;}
    .grid{display:grid;grid-template-columns:1fr;gap:12px;}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;box-shadow:0 8px 24px rgba(16,24,40,.06);}
    .card h3{margin:0;padding:12px 14px;border-bottom:1px solid var(--line);font-size:14px;font-weight:900;}
    .card .body{padding:12px 14px;}
    .meta{display:flex;flex-wrap:wrap;gap:10px 14px;color:var(--muted);}
    .meta b{color:var(--text);}
    .pill{display:inline-flex;align-items:center;height:22px;padding:0 10px;border-radius:999px;background:#eef2ff;color:#3730a3;font-weight:800;font-size:12px;}
    .pill.danger{background:#fee2e2;color:#991b1b;}
    .pill.good{background:#dcfce7;color:#166534;}
    .log{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;}
    .log th,.log td{padding:10px 10px;border-bottom:1px solid var(--line);vertical-align:top;text-align:left;}
    .log thead th{background:#f9fafb;font-size:12px;color:var(--muted);font-weight:700;}
    .log tbody td{text-align:left;}
    .log tr:last-child td{border-bottom:0;}
    .nick{font-weight:900;}
    .content{white-space:pre-wrap;word-break:break-word;}
    .row-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;justify-content:flex-start;}
    .muted{color:var(--muted);}

    @media (min-width: 960px){
        .grid{grid-template-columns:1.15fr .85fr;}
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <div class="title">채팅로그: <?php echo h($nick); ?></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a class="btn" href="javascript:window.close();">닫기</a>
            <a class="btn primary" href="<?php echo (defined('G5_ADMIN_URL') ? G5_ADMIN_URL.'/scorepoint/scorepoint_chat_reports.php?sub_menu=950602' : G5_PLUGIN_URL.'/chat/chat_report_admin.php'); ?>">최근신고로</a>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>대상 채팅 로그</h3>
            <div class="body">
                <div class="meta" style="margin-bottom:10px;">
                    <span><b>조회범위</b> <?php echo ($range_from && $range_to) ? (h($range_from).' ~ '.h($range_to).' (±'.$window_min.'분)') : ('최신 '.$limit.'개'); ?></span>
                    <?php if ($report && isset($report['_sp_dt']) && $report['_sp_dt'] !== ''): ?>
                        <span><b>신고시각</b> <?php echo h($report['_sp_dt']); ?></span>
                    <?php endif; ?>
                    <?php if ($report && isset($report['_sp_reason']) && $report['_sp_reason'] !== ''): ?>
                        <span><b>사유</b> <?php echo h($report['_sp_reason']); ?></span>
                    <?php endif; ?>
                    <?php if ($report && isset($report['_sp_ip']) && $report['_sp_ip'] !== ''): ?>
                        <span><b>IP</b> <?php echo h($report['_sp_ip']); ?></span>
                    <?php endif; ?>
                </div>

                <table class="log">
                    <thead>
                        <tr>
                            <th style="width:120px;">시간</th>
                            <th style="width:90px;">닉</th>
                            <th>내용</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$list): ?>
                            <tr><td colspan="3" class="muted">로그가 없습니다.</td></tr>
                        <?php else: foreach($list as $r): ?>
                            <tr>
                                <td class="muted"><?php echo h($r['cm_datetime']); ?></td>
                                <td class="nick"><?php echo h($r['cm_nick']); ?></td>
                                <td class="content"><?php echo h($r['cm_content']); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>

                <div class="row-actions">
                    <a class="btn" href="?nick=<?php echo urlencode($nick); ?>&limit=120">최신120</a>
                    <a class="btn" href="?nick=<?php echo urlencode($nick); ?>&limit=200">최신200</a>
                    <?php if ($rid): ?>
                        <a class="btn" href="?nick=<?php echo urlencode($nick); ?>&rid=<?php echo (int)$rid; ?>&win=10">±10분</a>
                        <a class="btn" href="?nick=<?php echo urlencode($nick); ?>&rid=<?php echo (int)$rid; ?>&win=15">±15분</a>
                        <a class="btn" href="?nick=<?php echo urlencode($nick); ?>&rid=<?php echo (int)$rid; ?>&win=30">±30분</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:12px;">
                <h3>대상 정보</h3>
                <div class="body">
                    <div class="meta">
                        <span><b>닉</b> <?php echo h($nick); ?></span>
                        <?php if ($target_mb && isset($target_mb['mb_id'])): ?>
                            <span><b>ID</b> <?php echo h($target_mb['mb_id']); ?></span>
                        <?php endif; ?>
                        <?php
                            $state = '오프라인';
                            if ($target_mb && !empty($target_mb['mb_today_login'])) {
                                $ts = strtotime($target_mb['mb_today_login']);
                                if ($ts && (time() - $ts) <= 300) $state = '온라인';
                            }
                            $is_banned = false;
                            if ($ban_row && isset($ban_row['is_active']) && (int)$ban_row['is_active'] === 1) {
                                // ban_until이 NULL/빈값이면 영구, 미래면 유효
                                if (!isset($ban_row['ban_until']) || $ban_row['ban_until'] === null || $ban_row['ban_until'] === '' || $ban_row['ban_until'] === '0000-00-00 00:00:00') {
                                    $is_banned = true;
                                } else {
                                    $until = strtotime($ban_row['ban_until']);
                                    if ($until && $until > time()) $is_banned = true;
                                }
                            }
                        ?>
                        <span><b>상태</b>
                            <?php if ($is_banned): ?><span class="pill danger">밴</span>
                            <?php else: ?>
                                <span class="pill <?php echo ($state==='온라인')?'good':''; ?>"><?php echo h($state); ?></span>
                            <?php endif; ?>
                        </span>
                        <?php if ($is_banned): ?>
                            <span><b>만료</b> <?php echo (isset($ban_row['ban_until']) && $ban_row['ban_until']) ? h($ban_row['ban_until']) : '영구'; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="row-actions" style="margin-top:12px;">
                        <button class="btn danger" type="button" onclick="applyBan(10)">10분 밴</button>
                        <button class="btn danger" type="button" onclick="applyBan(60)">60분 밴</button>
                        <button class="btn danger" type="button" onclick="applyBan(600)">600분 밴</button>
                        <button class="btn danger" type="button" onclick="applyBan(0)">영구 밴</button>
                        <button class="btn" type="button" onclick="applyUnban()">밴 해제</button>
                    </div>
                    <div class="muted" style="margin-top:8px;">* 제재는 chat_ajax.php 관리자 액션을 호출합니다.</div>
                </div>
            </div>

            <?php if ($reporter_list): ?>
            <div class="card">
                <h3>신고자 로그 (같은 시간대)</h3>
                <div class="body">
                    <div class="meta" style="margin-bottom:10px;">
                        <span><b>신고자</b> <?php echo h($report['_sp_reporter_nick']); ?></span>
                        <span class="muted">(대상과 같은 조회범위)</span>
                    </div>
                    <table class="log">
                        <thead>
                            <tr>
                                <th style="width:120px;">시간</th>
                                <th style="width:90px;">닉</th>
                                <th>내용</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($reporter_list as $r): ?>
                            <tr>
                                <td class="muted"><?php echo h($r['cm_datetime']); ?></td>
                                <td class="nick"><?php echo h($r['cm_nick']); ?></td>
                                <td class="content"><?php echo h($r['cm_content']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function(){
    function post(url, data){
        return fetch(url, {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
            body: new URLSearchParams(data).toString(),
            credentials:'same-origin'
        }).then(r=>r.json());
    }

    window.applyBan = function(min){
        var reason = '';
        if (min === 0) reason = '최근신고(로그)에서 영구 밴';
        else reason = '최근신고(로그)에서 '+min+'분 밴';

        post('chat_ajax.php', {act:'admin_ban_nick', nick:'<?php echo addslashes($nick); ?>', min:String(min), reason:reason})
            .then(function(res){
                if (!res || !res.ok){ alert((res && res.msg) ? res.msg : '실패'); return; }
                alert('처리되었습니다.');
                location.reload();
            }).catch(function(){ alert('통신 오류'); });
    };

    window.applyUnban = function(){
        post('chat_ajax.php', {act:'admin_unban', nick:'<?php echo addslashes($nick); ?>'})
            .then(function(res){
                if (!res || !res.ok){ alert((res && res.msg) ? res.msg : '실패'); return; }
                alert('해제되었습니다.');
                location.reload();
            }).catch(function(){ alert('통신 오류'); });
    };
})();
</script>
</body>
</html>