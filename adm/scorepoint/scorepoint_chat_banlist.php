<?php
/**
 * EveAlba 밴리스트 - Gnuboard Admin
 * 경로: /adm/scorepoint/scorepoint_chat_banlist.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910600';
if ($sub_menu === '') {
    $sub_menu = '910600';
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

$tbl_ban = isset($g5['chat_ban_table']) ? $g5['chat_ban_table'] : 'g5_chat_ban';
$status = isset($_GET['status']) ? trim($_GET['status']) : 'active';
if (!in_array($status, array('active', 'expired', 'all'), true)) {
    $status = 'active';
}
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

sql_query("
    UPDATE {$tbl_ban}
    SET is_active = 0,
        unbanned_at = IF(unbanned_at IS NULL, NOW(), unbanned_at)
    WHERE is_active = 1
      AND ban_until IS NOT NULL
      AND ban_until <> '0000-00-00 00:00:00'
      AND ban_until <= NOW()
", false);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_unban'])) {
    $mb_id = trim($_POST['mb_id'] ?? '');
    if ($mb_id !== '') {
        $mb_id_esc = sql_real_escape_string($mb_id);
        $admin_id = isset($member['mb_id']) ? sql_real_escape_string($member['mb_id']) : '';
        sql_query("
            UPDATE {$tbl_ban}
            SET is_active = 0,
                unbanned_by = '{$admin_id}',
                unbanned_at = NOW(),
                updated_at = NOW()
            WHERE mb_id = '{$mb_id_esc}'
        ", false);
    }
    $redir = G5_ADMIN_URL . '/scorepoint/scorepoint_chat_banlist.php?sub_menu=' . $sub_menu . '&status=' . urlencode($status) . '&q=' . urlencode($q);
    goto_url($redir);
}

$where = " WHERE 1 ";
if ($status === 'active') {
    $where .= " AND is_active = 1 ";
} elseif ($status === 'expired') {
    $where .= " AND is_active = 0 ";
}
if ($q !== '') {
    $qq = sql_real_escape_string($q);
    $where .= " AND (mb_nick LIKE '%{$qq}%' OR mb_id LIKE '%{$qq}%') ";
}

$list = array();
$rs = sql_query(" SELECT * FROM {$tbl_ban} {$where} ORDER BY banned_at DESC LIMIT 300 ");
while ($r = sql_fetch_array($rs)) {
    $list[] = $r;
}

$base_url = G5_ADMIN_URL . '/scorepoint/';
$qstr = 'sub_menu=' . $sub_menu . '&status=' . urlencode($status) . '&q=' . urlencode($q);

$g5['title'] = '밴리스트';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<style>
.sp-tbl-left thead th,
.sp-tbl-left tbody td { text-align: left !important; }
</style>

<div class="local_ov01 local_ov">
    <a href="<?php echo $base_url; ?>scorepoint_chat_manage.php?sub_menu=910500" class="btn_ov01">채팅관리</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_notice.php?sub_menu=910501" class="btn_ov01">공지/규정/금칙어</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_reports.php?sub_menu=910700" class="btn_ov01">최근신고</a>
    <span class="btn_ov01"><span class="ov_txt">밴리스트 </span><span class="ov_num"><?php echo count($list); ?> 건</span></span>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <input type="hidden" name="sub_menu" value="<?php echo $sub_menu; ?>">
    <label for="status" class="sound_only">상태</label>
    <select name="status" id="status" class="frm_input">
        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>활성(밴중)</option>
        <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>만료/해제</option>
        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>전체</option>
    </select>
    <label for="q" class="sound_only">검색어</label>
    <input type="text" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" id="q" class="frm_input" placeholder="닉네임/아이디 검색">
    <input type="submit" class="btn_submit" value="검색">
    <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sub_menu=<?php echo $sub_menu; ?>" class="btn btn_02">초기화</a>
</form>

<div class="tbl_head01 tbl_wrap sp-tbl-left">
    <table>
        <caption class="sound_only">밴 목록</caption>
        <thead>
            <tr>
                <th scope="col" style="width:72px;">상태</th>
                <th scope="col">닉네임</th>
                <th scope="col">아이디</th>
                <th scope="col">밴 시작</th>
                <th scope="col" style="width:80px;">기간</th>
                <th scope="col">끝나는 시각</th>
                <th scope="col">사유 / 관리자</th>
                <th scope="col">IP / 신고</th>
                <th scope="col" style="width:80px;">해제</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($list) === 0) {
                echo '<tr><td colspan="9" class="empty_table">데이터가 없습니다.</td></tr>';
            }
            $bi = 0;
            foreach ($list as $row) {
                $is_active = (int)$row['is_active'] === 1;
                $dur = isset($row['duration_min']) ? (int)$row['duration_min'] : 0;
                $until = isset($row['ban_until']) ? $row['ban_until'] : '';
                $bg = 'bg' . ($bi % 2);
                $bi++;
            ?>
            <tr class="<?php echo $bg; ?>">
                <td class="td_num">
                    <?php if ($is_active) { ?>
                    <span class="btn btn_03" style="cursor:default;">밴중</span>
                    <?php } else { ?>
                    <span class="frm_info">해제</span>
                    <?php } ?>
                </td>
                <td class="td_left"><?php echo htmlspecialchars($row['mb_nick'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($row['mb_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_datetime"><?php echo htmlspecialchars($row['banned_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_num"><?php echo $dur > 0 ? (int)$dur . '분' : '영구'; ?></td>
                <td class="td_datetime"><?php echo $until ? htmlspecialchars($until, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                <td class="td_left">
                    <?php echo htmlspecialchars($row['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    <br><span class="frm_info">banned_by: <?php echo htmlspecialchars($row['banned_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if (!$is_active && !empty($row['unbanned_at'])) { ?>
                    <br><span class="frm_info">unbanned_by: <?php echo htmlspecialchars($row['unbanned_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($row['unbanned_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php } ?>
                </td>
                <td class="td_left">
                    <span class="frm_info"><?php echo htmlspecialchars($row['ip_at_ban'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    <br>신고: <?php echo (int)($row['report_count'] ?? 0); ?>
                </td>
                <td class="td_num">
                    <?php if ($is_active) { ?>
                    <form method="post" onsubmit="return confirm('해제하시겠습니까?');" style="margin:0;">
                        <input type="hidden" name="mb_id" value="<?php echo htmlspecialchars($row['mb_id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="do_unban" value="1">
                        <button type="submit" class="btn btn_02">해제</button>
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

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
