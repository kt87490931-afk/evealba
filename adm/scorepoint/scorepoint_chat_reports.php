<?php
/**
 * EveAlba 최근 신고 - Gnuboard Admin
 * 경로: /adm/scorepoint/scorepoint_chat_reports.php
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

function sp_chat_col_exists($table, $col) {
    $col = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$col);
    if ($col === '') return false;
    $row = sql_fetch("SHOW COLUMNS FROM `" . str_replace('`', '', $table) . "` LIKE '{$col}'");
    return ($row && !empty($row));
}

function sp_chat_ban_state_of($tbl_ban, $mb_id) {
    $id = sql_real_escape_string((string)$mb_id);
    $row = sql_fetch(" SELECT is_active, ban_until FROM {$tbl_ban} WHERE mb_id='{$id}' ORDER BY id DESC LIMIT 1 ");
    $ret = array('label' => '정상', 'until' => '', 'is_ban' => false);
    if (!$row || (int)($row['is_active'] ?? 0) !== 1) return $ret;
    $until = isset($row['ban_until']) ? trim($row['ban_until']) : '';
    if ($until === '' || $until === '0000-00-00 00:00:00') {
        return array('label' => '영구정지', 'until' => '영구', 'is_ban' => true);
    }
    $ts = strtotime($until);
    if ($ts <= time()) return $ret;
    $remain_min = (int)ceil(($ts - time()) / 60);
    if ($remain_min <= 10) $label = '채금10분';
    else if ($remain_min <= 60) $label = '채금60분';
    else $label = '채금600분';
    return array('label' => $label, 'until' => $until, 'is_ban' => true);
}

function sp_chat_is_online($member_table, $mb_id) {
    $id = sql_real_escape_string((string)$mb_id);
    $row = sql_fetch(" SELECT mb_today_login FROM {$member_table} WHERE mb_id='{$id}' LIMIT 1 ");
    if (!$row || empty($row['mb_today_login'])) return false;
    $t = strtotime($row['mb_today_login']);
    return ($t > 0 && (time() - $t) <= 300);
}

$tbl_ban = isset($g5['chat_ban_table']) ? $g5['chat_ban_table'] : 'g5_chat_ban';
$member_table = isset($g5['member_table']) ? $g5['member_table'] : 'g5_member';
$tbl_pref = isset($g5['table_prefix']) ? (string)$g5['table_prefix'] : 'g5_';
$tbl1 = $tbl_pref . 'chat_report';
$tbl2 = 'g5_chat_report';
$tbl3 = 'chat_report';

$sp_table_count = function($t) {
    $t = trim((string)$t);
    if ($t === '') return -1;
    $row = sql_fetch("SELECT COUNT(*) AS cnt FROM `" . str_replace('`', '', $t) . "`");
    return ($row && isset($row['cnt'])) ? (int)$row['cnt'] : -1;
};
$c1 = $sp_table_count($tbl1);
$c2 = $sp_table_count($tbl2);
$c3 = $sp_table_count($tbl3);
$use_table = ($c1 > 0) ? $tbl1 : (($c2 > 0) ? $tbl2 : (($c3 > 0) ? $tbl3 : $tbl1));

$COL_TARGET_NICK = sp_chat_col_exists($use_table, 'target_nick') ? 'target_nick' : (sp_chat_col_exists($use_table, 'reported_nick') ? 'reported_nick' : '');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per = 20;

$tbl_safe = '`' . str_replace('`', '', $use_table) . '`';
$where = " 1 ";
if ($q !== '') {
    $qq = sql_real_escape_string($q);
    $conds = array("reporter_id LIKE '%{$qq}%'", "reporter_nick LIKE '%{$qq}%'", "target_id LIKE '%{$qq}%'", "reason LIKE '%{$qq}%'");
    if ($COL_TARGET_NICK !== '') $conds[] = "{$COL_TARGET_NICK} LIKE '%{$qq}%'";
    $where .= " AND (" . implode(' OR ', $conds) . ") ";
}

$row_cnt = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$tbl_safe} WHERE {$where} ");
$total_count = (int)($row_cnt['cnt'] ?? 0);
$total_page = $total_count > 0 ? (int)ceil($total_count / $per) : 1;
if ($page > $total_page) $page = $total_page;
$from_record = ($page - 1) * $per;

$result = sql_query("
    SELECT r.*, (SELECT COUNT(*) FROM {$tbl_safe} rr WHERE rr.target_id = r.target_id) AS target_cnt
    FROM {$tbl_safe} r
    WHERE {$where}
    ORDER BY r.id DESC
    LIMIT {$from_record}, {$per}
");
$report_list = array();
while ($r = sql_fetch_array($result)) {
    $report_list[] = $r;
}

$CHAT_AJAX_URL = G5_PLUGIN_URL . '/chat/chat_ajax.php';
$CHAT_LOG_URL = G5_ADMIN_URL . '/scorepoint/scorepoint_chat_log.php';
$base_url = G5_ADMIN_URL . '/scorepoint/';
$qstr = 'sub_menu=' . $sub_menu . '&q=' . urlencode($q) . '&page=' . $page;

$g5['title'] = '최근 신고';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>

<div class="local_ov01 local_ov">
    <a href="<?php echo $base_url; ?>scorepoint_chat_manage.php?sub_menu=910500" class="btn_ov01">채팅관리</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_notice.php?sub_menu=910501" class="btn_ov01">공지/규정/금칙어</a>
    <span class="btn_ov01"><span class="ov_txt">최근신고 </span><span class="ov_num"><?php echo number_format($total_count); ?> 건</span></span>
    <a href="<?php echo $base_url; ?>scorepoint_chat_banlist.php?sub_menu=910600" class="btn_ov01">밴리스트</a>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <input type="hidden" name="sub_menu" value="<?php echo $sub_menu; ?>">
    <label for="rp_q" class="sound_only">검색어</label>
    <input type="text" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" id="rp_q" class="frm_input" placeholder="신고자/대상 닉네임·아이디·사유">
    <input type="submit" class="btn_submit" value="검색">
</form>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption class="sound_only">최근 신고 목록</caption>
        <thead>
            <tr>
                <th scope="col">날짜·시간</th>
                <th scope="col">신고당한 닉네임</th>
                <th scope="col">신고한 닉네임</th>
                <th scope="col">사유</th>
                <th scope="col">누적횟수</th>
                <th scope="col">현재상태</th>
                <th scope="col">제재</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($report_list as $row) {
                $tid = (string)$row['target_id'];
                $target_nick = $COL_TARGET_NICK ? ($row[$COL_TARGET_NICK] ?? '') : ($row['reported_nick'] ?? '');
                $cnt = (int)($row['target_cnt'] ?? 0);
                $ban = sp_chat_ban_state_of($tbl_ban, $tid);
                $online = sp_chat_is_online($member_table, $tid);
                $state_txt = $ban['label'];
                $online_txt = $online ? '온라인' : '오프라인';
                $bg = 'bg' . ($i % 2);
                $i++;
            ?>
            <tr class="<?php echo $bg; ?>" data-report-id="<?php echo (int)$row['id']; ?>" data-target-id="<?php echo htmlspecialchars($tid, ENT_QUOTES, 'UTF-8'); ?>" data-target-nick="<?php echo htmlspecialchars($target_nick, ENT_QUOTES, 'UTF-8'); ?>">
                <td class="td_datetime"><?php echo htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($target_nick, ENT_QUOTES, 'UTF-8'); ?><br><span class="frm_info"><?php echo htmlspecialchars($tid, ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td class="td_left"><?php echo htmlspecialchars($row['reporter_nick'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br><span class="frm_info"><?php echo htmlspecialchars($row['reporter_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td class="td_left"><?php echo htmlspecialchars($row['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_num"><?php echo (int)$cnt; ?></td>
                <td class="td_left"><?php echo htmlspecialchars($state_txt, ENT_QUOTES, 'UTF-8'); ?> · <?php echo $online_txt; ?></td>
                <td class="td_left">
                    <a href="<?php echo $CHAT_LOG_URL; ?>?sub_menu=910700&amp;target_id=<?php echo urlencode($tid); ?>&amp;rid=<?php echo (int)$row['id']; ?>&amp;nick=<?php echo urlencode($target_nick); ?>" class="btn btn_02">채팅로그</a>
                    <select class="frm_input rp-ban-min" style="width:70px;display:inline-block;margin-left:4px;">
                        <option value="unban">밴해제</option>
                        <option value="10">10분</option>
                        <option value="60">60분</option>
                        <option value="600">600분</option>
                        <option value="0">영구</option>
                    </select>
                    <button type="button" class="btn btn_02 rp-apply-ban" style="margin-left:4px;">적용</button>
                </td>
            </tr>
            <?php
            }
            if ($i === 0) {
                echo '<tr><td colspan="7" class="empty_table">신고 내역이 없습니다.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$paging_url = $_SERVER['SCRIPT_NAME'] . '?sub_menu=' . $sub_menu . '&q=' . urlencode($q) . '&page=';
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $paging_url);
?>

<script>
(function(){
    var AJAX = <?php echo json_encode($CHAT_AJAX_URL, JSON_UNESCAPED_SLASHES); ?>;
    function post(body){
        return fetch(AJAX, { method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}, body: body }).then(function(r){ return r.json(); });
    }
    document.addEventListener('click', function(e){
        var btn = e.target.closest('button.rp-apply-ban');
        if (!btn) return;
        var tr = btn.closest('tr[data-target-id]');
        if (!tr) return;
        var targetId = tr.getAttribute('data-target-id') || '';
        var targetNick = tr.getAttribute('data-target-nick') || '';
        var sel = tr.querySelector('select.rp-ban-min');
        var v = sel ? (sel.value || '') : '';
        if (!v) return;
        if (v === 'unban') {
            if (!confirm('[' + targetNick + '] 밴을 해제할까요?')) return;
            post('act=admin_unban&mb_id=' + encodeURIComponent(targetId)).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '해제 실패'); return; }
                location.reload();
            });
            return;
        }
        var label = (v === '0') ? '영구정지' : (v + '분');
        if (!confirm('[' + targetNick + '] ' + label + ' 밴 처리할까요?')) return;
        post('act=admin_ban&mb_id=' + encodeURIComponent(targetId) + '&min=' + encodeURIComponent(v) + '&reason=' + encodeURIComponent('최근 신고에서 제재')).then(function(j){
            if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '제재 실패'); return; }
            location.reload();
        });
    }, true);
})();
</script>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
