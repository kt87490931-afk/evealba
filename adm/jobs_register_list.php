<?php
/**
 * 어드민 - 채용정보등록 목록 (입금확인)
 */
$sub_menu = '300830';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$token = get_session('ss_admin_token') ?: get_admin_token();

// 테이블 존재 확인
$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!sql_num_rows($tb_check)) {
    $g5['title'] = '채용정보등록 관리';
    require_once './admin.head.php';
    echo '<div class="local_desc01 local_desc"><p>채용정보등록 테이블이 없습니다.</p></div>';
    require_once './admin.tail.php';
    exit;
}

$jr_table = 'g5_jobs_register';
$sql_search = " where (1) ";

// 상태 필터
$st = isset($_GET['st']) ? preg_replace('/[^a-z]/', '', $_GET['st']) : '';
if ($st === 'pending') {
    $sql_search .= " and jr_status = 'pending' ";
} elseif ($st === 'ongoing') {
    $sql_search .= " and jr_status = 'ongoing' ";
} elseif ($st === 'ended') {
    $sql_search .= " and (jr_status = 'ended' OR (jr_end_date IS NOT NULL AND jr_end_date < CURDATE())) ";
}

$sql_common = " from {$jr_table} ";
$sql_order = " order by jr_datetime desc ";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = isset($config['cf_page_rows']) ? (int)$config['cf_page_rows'] : 20;
$total_page = ceil($total_count / $rows);
if ($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';
$qstr = 'st=' . urlencode($st);

$g5['title'] = '채용정보등록 관리';
require_once './admin.head.php';

// 입금대기중 수
$pending_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$jr_table} WHERE jr_status = 'pending'")['cnt'];
$ongoing_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$jr_table} WHERE jr_status = 'ongoing'")['cnt'];
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">전체</span><span class="ov_num"><?php echo number_format($total_count); ?>건</span></span>
    <a href="?st=pending" class="btn_ov01<?php echo $st === 'pending' ? ' on' : ''; ?>"><span class="ov_txt">입금대기중</span><span class="ov_num"><?php echo number_format($pending_cnt); ?>건</span></a>
    <a href="?st=ongoing" class="btn_ov01<?php echo $st === 'ongoing' ? ' on' : ''; ?>"><span class="ov_txt">진행중</span><span class="ov_num"><?php echo number_format($ongoing_cnt); ?>건</span></a>
    <a href="?st=ended" class="btn_ov01<?php echo $st === 'ended' ? ' on' : ''; ?>"><span class="ov_txt">마감</span><span class="ov_num"><?php echo number_format($total_count - $pending_cnt - $ongoing_cnt); ?>건</span></a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">회원ID</th>
                <th scope="col">닉네임/업체</th>
                <th scope="col">채용제목</th>
                <th scope="col">표시제목</th>
                <th scope="col">상태</th>
                <th scope="col">금액</th>
                <th scope="col">등록일</th>
                <th scope="col">종료일</th>
                <th scope="col">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
            $result = sql_query($sql);
            $i = 0;
            while ($row = sql_fetch_array($result)) {
                $status = $row['jr_status'];
                $status_label = ($status === 'pending') ? '입금대기중' : (($status === 'ongoing') ? '진행중' : '마감');
                $is_ended = ($status === 'ended' || ($row['jr_end_date'] && $row['jr_end_date'] < date('Y-m-d')));
                if ($is_ended) $status_label = '마감';
                $bg = 'bg' . ($i % 2);
                $confirm_url = './jobs_register_confirm_update.php?jr_id=' . $row['jr_id'] . '&token=' . (isset($token) ? $token : '');
                ?>
                <tr class="<?php echo $bg; ?>">
                    <td class="td_num"><?php echo (int)$row['jr_id']; ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($row['mb_id']); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars(cut_str($row['jr_title'], 30)); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars(cut_str($row['jr_subject_display'], 40)); ?></td>
                    <td class="td_num"><span class="status-badge status-<?php echo $status; ?>"><?php echo $status_label; ?></span></td>
                    <td class="td_num"><?php echo number_format($row['jr_total_amount']); ?>원</td>
                    <td class="td_datetime"><?php echo $row['jr_datetime']; ?></td>
                    <td class="td_datetime"><?php echo $row['jr_end_date'] ?: '—'; ?></td>
                    <td class="td_mng td_mng_l">
                        <?php if ($status === 'pending') { ?>
                            <a href="<?php echo $confirm_url; ?>" class="btn btn_02" onclick="return confirm('이 건을 입금확인 하시겠습니까?');">입금확인</a>
                        <?php } else { ?>
                            —
                        <?php } ?>
                    </td>
                </tr>
                <?php
                $i++;
            }
            if ($i == 0) {
                echo '<tr><td colspan="10" class="empty_table"><span>자료가 없습니다.</span></td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?" . ($qstr ? $qstr . '&' : '') . "page="); ?>

<?php
require_once './admin.tail.php';
