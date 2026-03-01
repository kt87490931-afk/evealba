<?php
/**
 * EveAlba 채팅로그 (어드민) - Gnuboard Admin
 * 경로: /adm/scorepoint/scorepoint_chat_log.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910700';
if ($sub_menu === '') {
    $sub_menu = '910700';
}

$adm_dir = dirname(__DIR__);
$adm_dir_real = @realpath($adm_dir);
if ($adm_dir_real && is_dir($adm_dir_real)) {
    $adm_dir = $adm_dir_real;
}
$old_cwd = @getcwd();
if ($adm_dir && is_dir($adm_dir)) {
    @chdir($adm_dir);
}
require_once $adm_dir . '/_common.php';
if ($old_cwd) {
    @chdir($old_cwd);
}

if (!isset($is_admin) || !$is_admin) {
    alert('관리자만 접근 가능합니다.');
}
auth_check_menu($auth, $sub_menu, 'r');

$tbl_chat = isset($g5['chat_table']) ? $g5['chat_table'] : (isset($g5['chat_msg_table']) ? $g5['chat_msg_table'] : 'g5_chat');
$tbl_ban = isset($g5['chat_ban_table']) ? $g5['chat_ban_table'] : 'g5_chat_ban';
$tbl_member = isset($g5['member_table']) ? $g5['member_table'] : 'g5_member';

$tbl_report = '';
$chk = sql_fetch("SHOW TABLES LIKE 'g5_chat_report2'");
if ($chk && count($chk)) $tbl_report = 'g5_chat_report2';
if ($tbl_report === '') {
    $chk = sql_fetch("SHOW TABLES LIKE 'g5_chat_report'");
    if ($chk && count($chk)) $tbl_report = 'g5_chat_report';
}

$nick = isset($_GET['nick']) ? trim($_GET['nick']) : '';
$rid = isset($_GET['rid']) ? (int)$_GET['rid'] : 0;
if (!$rid && isset($_GET['report_id'])) $rid = (int)$_GET['report_id'];
if ($nick === '' && isset($_GET['target_id'])) $nick = trim((string)$_GET['target_id']);
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 120;
if ($limit < 30) $limit = 30;
if ($limit > 300) $limit = 300;
$window_min = isset($_GET['win']) ? (int)$_GET['win'] : 15;
if ($window_min < 3) $window_min = 3;
if ($window_min > 180) $window_min = 180;

$report = null;
$target_mb = null;
$range_from = '';
$range_to = '';

if ($rid > 0 && $tbl_report !== '') {
    $r = sql_fetch("SELECT * FROM {$tbl_report} WHERE id = {$rid} LIMIT 1");
    if (!$r) $r = sql_fetch("SELECT * FROM {$tbl_report} WHERE report_id = {$rid} LIMIT 1");
    if ($r) {
        $report = $r;
        $cand_target = array('reported_nick', 'target_nick', 'to_nick', 'mb_nick', 'nick', 'reported_mb_nick');
        foreach ($cand_target as $k) {
            if (isset($r[$k]) && trim($r[$k]) !== '') { $nick = trim($r[$k]); break; }
        }
        $reporter_nick = '';
        $cand_reporter = array('reporter_nick', 'from_nick', 'by_nick', 'report_mb_nick', 'writer_nick');
        foreach ($cand_reporter as $k) {
            if (isset($r[$k]) && trim($r[$k]) !== '') { $reporter_nick = trim($r[$k]); break; }
        }
        $dt = '';
        $cand_dt = array('created_at', 'report_datetime', 'regdate', 'datetime', 'created', 'write_datetime');
        foreach ($cand_dt as $k) {
            if (isset($r[$k]) && trim($r[$k]) !== '') { $dt = trim($r[$k]); break; }
        }
        if ($dt !== '') {
            $ts = strtotime($dt);
            if ($ts) {
                $range_from = date('Y-m-d H:i:s', $ts - ($window_min * 60));
                $range_to = date('Y-m-d H:i:s', $ts + ($window_min * 60));
            }
        }
        if ($nick !== '') {
            $target_mb = sql_fetch("SELECT mb_id, mb_nick, mb_today_login FROM {$tbl_member} WHERE mb_nick='" . sql_real_escape_string($nick) . "' LIMIT 1");
        }
        $reason = '';
        foreach (array('reason', 'reason_text', 'report_reason', 'report_msg', 'msg') as $k) {
            if (isset($r[$k]) && trim($r[$k]) !== '') { $reason = trim($r[$k]); break; }
        }
        $rip = '';
        foreach (array('ip', 'report_ip', 'remote_addr', 'writer_ip') as $k) {
            if (isset($r[$k]) && trim($r[$k]) !== '') { $rip = trim($r[$k]); break; }
        }
        $report['_sp_reason'] = $reason;
        $report['_sp_ip'] = $rip;
        $report['_sp_reporter_nick'] = isset($reporter_nick) ? $reporter_nick : '';
        $report['_sp_dt'] = $dt;
    }
}

if ($nick === '') {
    alert('nick 또는 rid 파라미터가 필요합니다.');
}

$ban_row = null;
if ($target_mb && isset($target_mb['mb_id']) && $target_mb['mb_id']) {
    $ban_row = sql_fetch("SELECT * FROM {$tbl_ban} WHERE mb_id='" . sql_real_escape_string($target_mb['mb_id']) . "' ORDER BY banned_at DESC LIMIT 1");
}

$where = " WHERE 1 AND cm_nick='" . sql_real_escape_string($nick) . "' ";
if ($range_from !== '' && $range_to !== '') {
    $where .= " AND cm_datetime BETWEEN '" . sql_real_escape_string($range_from) . "' AND '" . sql_real_escape_string($range_to) . "' ";
    $sql = "SELECT cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime FROM {$tbl_chat} {$where} ORDER BY cm_id ASC LIMIT " . (int)$limit;
} else {
    $sql = "SELECT cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime FROM {$tbl_chat} {$where} ORDER BY cm_id DESC LIMIT " . (int)$limit;
}
$list = array();
$rs = sql_query($sql);
while ($row = sql_fetch_array($rs)) {
    $list[] = $row;
}
if ($range_from === '' || $range_to === '') {
    $list = array_reverse($list);
}

$reporter_list = array();
if ($report && isset($report['_sp_reporter_nick']) && $report['_sp_reporter_nick'] !== '') {
    $rn = $report['_sp_reporter_nick'];
    $w2 = " WHERE cm_nick='" . sql_real_escape_string($rn) . "' ";
    if ($range_from !== '' && $range_to !== '') {
        $w2 .= " AND cm_datetime BETWEEN '" . sql_real_escape_string($range_from) . "' AND '" . sql_real_escape_string($range_to) . "' ";
        $rs2 = sql_query("SELECT cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime FROM {$tbl_chat} {$w2} ORDER BY cm_id ASC LIMIT 80");
        while ($row = sql_fetch_array($rs2)) $reporter_list[] = $row;
    }
}

$CHAT_AJAX_URL = G5_PLUGIN_URL . '/chat/chat_ajax.php';
$base_url = G5_ADMIN_URL . '/scorepoint/';
$back_url = $base_url . 'scorepoint_chat_reports.php?sub_menu=910700';

$g5['title'] = '채팅로그 - ' . $nick;
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<style>
.sp-chat-log-table thead th,
.sp-chat-log-table tbody td { text-align: left !important; }
.sp-chat-log-table .td_datetime { width: 140px; }
.sp-chat-log-table .td_nick { width: 100px; }
.sp-chat-form tbody th,
.sp-chat-form tbody td { text-align: left !important; }
</style>

<div class="local_ov01 local_ov">
    <a href="<?php echo $base_url; ?>scorepoint_chat_manage.php?sub_menu=910500" class="btn_ov01">채팅관리</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_notice.php?sub_menu=910501" class="btn_ov01">공지/규정/금칙어</a>
    <a href="<?php echo $back_url; ?>" class="btn_ov01">최근신고</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_banlist.php?sub_menu=910600" class="btn_ov01">밴리스트</a>
    <span class="btn_ov01"><span class="ov_txt">채팅로그 </span><span class="ov_num"><?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?></span></span>
</div>

<div class="local_desc01 local_desc" style="margin-top:12px;">
    <p>
        <strong>조회범위</strong> <?php echo ($range_from && $range_to) ? (htmlspecialchars($range_from, ENT_QUOTES, 'UTF-8') . ' ~ ' . htmlspecialchars($range_to, ENT_QUOTES, 'UTF-8') . ' (±' . (int)$window_min . '분)') : ('최신 ' . (int)$limit . '개'); ?>
        <?php if ($report && isset($report['_sp_dt']) && $report['_sp_dt'] !== ''): ?>
            | <strong>신고시각</strong> <?php echo htmlspecialchars($report['_sp_dt'], ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
        <?php if ($report && isset($report['_sp_reason']) && $report['_sp_reason'] !== ''): ?>
            | <strong>사유</strong> <?php echo htmlspecialchars($report['_sp_reason'], ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
        <?php if ($report && isset($report['_sp_ip']) && $report['_sp_ip'] !== ''): ?>
            | <strong>IP</strong> <?php echo htmlspecialchars($report['_sp_ip'], ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
    </p>
</div>

<div class="tbl_head01 tbl_wrap sp-chat-log-table" style="margin-top:16px;">
    <table>
        <caption class="sound_only">대상 채팅 로그</caption>
        <thead>
            <tr>
                <th scope="col" class="td_datetime">시간</th>
                <th scope="col" class="td_nick">닉</th>
                <th scope="col">내용</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="3" class="empty_table">로그가 없습니다.</td></tr>
            <?php else: foreach ($list as $r): ?>
            <tr>
                <td class="td_datetime"><?php echo htmlspecialchars($r['cm_datetime'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left td_nick"><?php echo htmlspecialchars($r['cm_nick'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($r['cm_content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div style="margin:12px 0;">
    <a href="?sub_menu=<?php echo $sub_menu; ?>&nick=<?php echo urlencode($nick); ?>&limit=120" class="btn btn_02">최신120</a>
    <a href="?sub_menu=<?php echo $sub_menu; ?>&nick=<?php echo urlencode($nick); ?>&limit=200" class="btn btn_02">최신200</a>
    <?php if ($rid): ?>
    <a href="?sub_menu=<?php echo $sub_menu; ?>&nick=<?php echo urlencode($nick); ?>&rid=<?php echo (int)$rid; ?>&win=10" class="btn btn_02">±10분</a>
    <a href="?sub_menu=<?php echo $sub_menu; ?>&nick=<?php echo urlencode($nick); ?>&rid=<?php echo (int)$rid; ?>&win=15" class="btn btn_02">±15분</a>
    <a href="?sub_menu=<?php echo $sub_menu; ?>&nick=<?php echo urlencode($nick); ?>&rid=<?php echo (int)$rid; ?>&win=30" class="btn btn_02">±30분</a>
    <?php endif; ?>
</div>

<div class="tbl_head01 tbl_wrap sp-chat-form" style="margin-top:20px; max-width:500px;">
    <table>
        <caption class="sound_only">대상 정보 및 제재</caption>
        <colgroup><col style="width:100px;"><col></colgroup>
        <tbody>
            <tr>
                <th scope="row">닉</th>
                <td class="td_left"><?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php if ($target_mb && isset($target_mb['mb_id'])): ?>
            <tr>
                <th scope="row">ID</th>
                <td class="td_left"><?php echo htmlspecialchars($target_mb['mb_id'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php endif; ?>
            <?php
            $state = '오프라인';
            if ($target_mb && !empty($target_mb['mb_today_login'])) {
                $ts = strtotime($target_mb['mb_today_login']);
                if ($ts && (time() - $ts) <= 300) $state = '온라인';
            }
            $is_banned = false;
            if ($ban_row && isset($ban_row['is_active']) && (int)$ban_row['is_active'] === 1) {
                if (empty($ban_row['ban_until']) || $ban_row['ban_until'] === '0000-00-00 00:00:00') {
                    $is_banned = true;
                } else {
                    $until = strtotime($ban_row['ban_until']);
                    if ($until && $until > time()) $is_banned = true;
                }
            }
            ?>
            <tr>
                <th scope="row">상태</th>
                <td class="td_left"><?php echo $is_banned ? '밴' : $state; ?></td>
            </tr>
            <tr>
                <th scope="row">제재</th>
                <td class="td_left">
                    <button type="button" class="btn btn_02 sp-ban-btn" data-min="10">10분 밴</button>
                    <button type="button" class="btn btn_02 sp-ban-btn" data-min="60">60분 밴</button>
                    <button type="button" class="btn btn_02 sp-ban-btn" data-min="600">600분 밴</button>
                    <button type="button" class="btn btn_02 sp-ban-btn" data-min="0">영구 밴</button>
                    <button type="button" class="btn btn_02" id="sp-unban-btn">밴 해제</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php if (!empty($reporter_list)): ?>
<div class="tbl_head01 tbl_wrap sp-chat-log-table" style="margin-top:20px;">
    <h3 class="h2_frm">신고자 로그 (같은 시간대)</h3>
    <table>
        <caption class="sound_only">신고자 채팅 로그</caption>
        <thead>
            <tr>
                <th scope="col" class="td_datetime">시간</th>
                <th scope="col" class="td_nick">닉</th>
                <th scope="col">내용</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reporter_list as $r): ?>
            <tr>
                <td class="td_datetime"><?php echo htmlspecialchars($r['cm_datetime'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left td_nick"><?php echo htmlspecialchars($r['cm_nick'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($r['cm_content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<script>
(function(){
    var AJAX = <?php echo json_encode($CHAT_AJAX_URL, JSON_UNESCAPED_SLASHES); ?>;
    var NICK = <?php echo json_encode($nick, JSON_UNESCAPED_UNICODE); ?>;

    function post(body){
        return fetch(AJAX, { method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}, body: body }).then(function(r){ return r.json(); });
    }

    document.querySelectorAll('.sp-ban-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var min = this.getAttribute('data-min') || '10';
            var reason = (min === '0') ? '채팅로그(어드민)에서 영구 밴' : ('채팅로그(어드민)에서 ' + min + '분 밴');
            if (!confirm('[' + NICK + '] ' + (min === '0' ? '영구' : (min + '분')) + ' 밴 처리할까요?')) return;
            post('act=admin_ban_nick&nick=' + encodeURIComponent(NICK) + '&min=' + encodeURIComponent(min) + '&reason=' + encodeURIComponent(reason)).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '실패'); return; }
                alert('처리되었습니다.');
                location.reload();
            });
        });
    });

    var unbanBtn = document.getElementById('sp-unban-btn');
    if (unbanBtn) {
        unbanBtn.addEventListener('click', function(){
            if (!confirm('[' + NICK + '] 밴을 해제할까요?')) return;
            post('act=admin_unban&nick=' + encodeURIComponent(NICK)).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '실패'); return; }
                alert('해제되었습니다.');
                location.reload();
            });
        });
    }
})();
</script>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
