<?php
/**
 * ScorePoint 게시물 신고내용 로그 (Gnuboard Admin)
 * - 특정 게시물(bo_table, wr_id)에 대한 g5_board_report 목록: 신고일시, 신고자, 신고사유, 상세내용
 * - 게시물관리 페이지에서 "신고내용" 링크로 진입
 *
 * 경로: /adm/scorepoint/scorepoint_board_report_log.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910800';
if ($sub_menu === '') {
    $sub_menu = '910800';
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

$bo_table = isset($_GET['bo_table']) ? preg_replace('/[^a-zA-Z0-9_]/', '', trim($_GET['bo_table'])) : '';
$wr_id = isset($_GET['wr_id']) ? (int)$_GET['wr_id'] : 0;

if ($bo_table === '' || $wr_id <= 0) {
    alert('게시물 정보가 올바르지 않습니다.');
}

$board = sql_fetch(" SELECT bo_table, bo_subject FROM {$g5['board_table']} WHERE bo_table = '" . sql_real_escape_string($bo_table) . "' LIMIT 1 ");
if (!$board || !isset($board['bo_table'])) {
    alert('존재하지 않는 게시판입니다.');
}

$write_table = $g5['write_prefix'] . $bo_table;
$write = sql_fetch(" SELECT wr_id, wr_subject, wr_name, mb_id, wr_datetime FROM {$write_table} WHERE wr_id = " . (int)$wr_id . " LIMIT 1 ");
if (!$write || !isset($write['wr_id'])) {
    alert('존재하지 않는 게시물입니다.');
}

$tbl_report = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'board_report';
$bo_esc = sql_real_escape_string($bo_table);
$sql = " SELECT br_id, reporter_mb_id, reporter_nick, reason, detail, report_ip, created_at
    FROM {$tbl_report}
    WHERE bo_table = '{$bo_esc}' AND wr_id = " . (int)$wr_id . "
    ORDER BY created_at DESC ";
$result = sql_query($sql);

$listall = '<a href="' . G5_ADMIN_URL . '/scorepoint/scorepoint_board_post_manage.php?sub_menu=' . $sub_menu . '" class="ov_listall">게시물관리로</a>';
$g5['title'] = '신고내용 로그';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">게시판 </span><span class="ov_num"><?php echo htmlspecialchars($board['bo_subject'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($bo_table, ENT_QUOTES, 'UTF-8'); ?>)</span></span>
    <span class="btn_ov01"><span class="ov_txt">글번호 </span><span class="ov_num"><?php echo (int)$wr_id; ?></span></span>
    <span class="btn_ov01"><span class="ov_txt">제목 </span><span class="ov_num"><?php echo htmlspecialchars(get_text(cut_str($write['wr_subject'], 60)), ENT_QUOTES, 'UTF-8'); ?></span></span>
</div>

<div class="tbl_head01 tbl_wrap" style="margin-top:16px;">
    <table>
        <caption class="sound_only">신고내용 로그</caption>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">신고일시</th>
                <th scope="col">신고자 ID</th>
                <th scope="col">신고자 닉네임</th>
                <th scope="col">신고사유</th>
                <th scope="col">상세내용</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            while ($row = sql_fetch_array($result)) {
                $bg = 'bg' . ($i % 2);
            ?>
            <tr class="<?php echo $bg; ?>">
                <td class="td_num"><?php echo (int)$row['br_id']; ?></td>
                <td class="td_datetime"><?php echo htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($row['reporter_mb_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($row['reporter_nick'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($row['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo nl2br(htmlspecialchars($row['detail'] ?? '', ENT_QUOTES, 'UTF-8')); ?></td>
            </tr>
            <?php
                $i++;
            }
            if ($i === 0) {
                echo '<tr><td colspan="6" class="empty_table">신고 내역이 없습니다.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
