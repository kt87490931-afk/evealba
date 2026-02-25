<?php
/**
 * 어드민 - 채용정보등록 관리 (광고등록리스트)
 * 수동 페이지: 입금확인·승인 후 광고 노출
 */
$sub_menu = '910100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$token = get_session('ss_admin_token') ?: get_admin_token();

$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!sql_num_rows($tb_check)) {
    $g5['title'] = '채용정보등록 관리';
    require_once './admin.head.php';
    echo '<div class="local_desc01 local_desc"><p>채용정보등록 테이블이 없습니다.</p></div>';
    require_once './admin.tail.php';
    exit;
}

// jr_payment_confirmed 컬럼 없으면 마이그레이션 008 적용
$col_check = sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_payment_confirmed'", false);
if (!$col_check || !sql_num_rows($col_check)) {
    sql_query("ALTER TABLE g5_jobs_register ADD COLUMN jr_payment_confirmed tinyint NOT NULL DEFAULT 0 AFTER jr_status", false);
    sql_query("ALTER TABLE g5_jobs_register ADD COLUMN jr_approved tinyint NOT NULL DEFAULT 0 AFTER jr_payment_confirmed", false);
    sql_query("ALTER TABLE g5_jobs_register ADD COLUMN jr_approved_datetime datetime DEFAULT NULL AFTER jr_approved", false);
    sql_query("ALTER TABLE g5_jobs_register ADD COLUMN jr_ad_labels varchar(500) NOT NULL DEFAULT '' AFTER jr_data", false);
    sql_query("UPDATE g5_jobs_register SET jr_payment_confirmed=1, jr_approved=1, jr_approved_datetime=jr_datetime WHERE jr_status='ongoing' AND (jr_approved_datetime IS NULL OR jr_approved_datetime='')", false);
}

$jr_table = 'g5_jobs_register';
$sql_search = " where (1) ";

$st = isset($_GET['st']) ? preg_replace('/[^a-z]/', '', $_GET['st']) : '';
if ($st === 'pending') {
    $sql_search .= " and jr_status = 'pending' and (jr_payment_confirmed = 0 or jr_payment_confirmed IS NULL) ";
} elseif ($st === 'payment_ok') {
    $sql_search .= " and jr_payment_confirmed = 1 and (jr_approved = 0 or jr_approved IS NULL) and jr_status = 'pending' ";
} elseif ($st === 'ongoing') {
    $sql_search .= " and jr_status = 'ongoing' ";
} elseif ($st === 'ended') {
    $sql_search .= " and (jr_status = 'ended' OR (jr_end_date IS NOT NULL AND jr_end_date < CURDATE())) ";
}

$sql_common = " from {$jr_table} ";

$sst = isset($_GET['sst']) ? preg_replace('/[^a-z_]/', '', $_GET['sst']) : 'jr_datetime';
$sod = isset($_GET['sod']) && strtolower($_GET['sod']) === 'asc' ? 'asc' : 'desc';
$allowed_sort = array('jr_datetime', 'jr_id', 'mb_id', 'jr_nickname', 'jr_company', 'jr_total_amount', 'jr_ad_period', 'jr_status', 'jr_payment_confirmed', 'jr_approved');
if (!in_array($sst, $allowed_sort)) $sst = 'jr_datetime';
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = isset($config['cf_page_rows']) ? (int)$config['cf_page_rows'] : 20;
$total_page = ceil($total_count / $rows);
if ($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$qstr_arr = array();
if ($st) $qstr_arr[] = 'st=' . urlencode($st);
if ($sst) $qstr_arr[] = 'sst=' . urlencode($sst);
if ($sod) $qstr_arr[] = 'sod=' . urlencode($sod);
$qstr = implode('&', $qstr_arr);

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

$g5['title'] = '채용정보등록 관리';
require_once './admin.head.php';

$pending_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$jr_table} WHERE jr_status = 'pending' AND (jr_payment_confirmed = 0 OR jr_payment_confirmed IS NULL)")['cnt'];
$payment_ok_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$jr_table} WHERE jr_payment_confirmed = 1 AND (jr_approved = 0 OR jr_approved IS NULL) AND jr_status = 'pending'")['cnt'];
$ongoing_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$jr_table} WHERE jr_status = 'ongoing'")['cnt'];
$ended_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$jr_table} WHERE jr_status = 'ended' OR (jr_end_date IS NOT NULL AND jr_end_date < CURDATE())")['cnt'];
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">전체</span><span class="ov_num"><?php echo number_format($total_count); ?>건</span></span>
    <a href="?st=pending" class="btn_ov01<?php echo $st === 'pending' ? ' on' : ''; ?>"><span class="ov_txt">입금대기</span><span class="ov_num"><?php echo number_format($pending_cnt); ?>건</span></a>
    <a href="?st=payment_ok" class="btn_ov01<?php echo $st === 'payment_ok' ? ' on' : ''; ?>"><span class="ov_txt">승인대기</span><span class="ov_num"><?php echo number_format($payment_ok_cnt); ?>건</span></a>
    <a href="?st=ongoing" class="btn_ov01<?php echo $st === 'ongoing' ? ' on' : ''; ?>"><span class="ov_txt">진행중</span><span class="ov_num"><?php echo number_format($ongoing_cnt); ?>건</span></a>
    <a href="?st=ended" class="btn_ov01<?php echo $st === 'ended' ? ' on' : ''; ?>"><span class="ov_txt">마감</span><span class="ov_num"><?php echo number_format($ended_cnt); ?>건</span></a>
</div>

<form name="fjobslist" id="fjobslist" method="post" action="">
<input type="hidden" name="token" value="<?php echo $token; ?>">
<input type="hidden" name="jr_ids" id="jr_ids_hidden" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
            <tr>
                <th scope="col">
                    <label for="chkall" class="sound_only">전체</label>
                    <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                </th>
                <th scope="col"><?php echo subject_sort_link('jr_datetime', $qstr); ?>날짜</a></th>
                <th scope="col"><?php echo subject_sort_link('jr_id', $qstr); ?>ID</a></th>
                <th scope="col"><?php echo subject_sort_link('mb_id', $qstr); ?>아이디</a></th>
                <th scope="col"><?php echo subject_sort_link('jr_nickname', $qstr); ?>닉네임</a></th>
                <th scope="col"><?php echo subject_sort_link('jr_company', $qstr); ?>업소명</a></th>
                <th scope="col"><?php echo subject_sort_link('jr_total_amount', $qstr); ?>신청금액</a></th>
                <th scope="col">신청한광고목록</th>
                <th scope="col">입금상태</th>
                <th scope="col">승인상태</th>
                <th scope="col"><?php echo subject_sort_link('jr_ad_period', $qstr); ?>광고기간</a></th>
                <th scope="col">남은기간</th>
                <th scope="col">바로가기</th>
                <th scope="col">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
            $result = sql_query($sql);
            $i = 0;
            $today = date('Y-m-d');
            $jobs_view_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . '/jobs_view.php' : '/jobs_view.php';

            while ($row = sql_fetch_array($result)) {
                $payment_ok = !empty($row['jr_payment_confirmed']);
                $approved = !empty($row['jr_approved']);
                $payment_label = $payment_ok ? '확인' : '대기';
                $approve_label = $approved ? '승인' : '대기';

                $remaining = '—';
                if ($approved && !empty($row['jr_end_date'])) {
                    $end_ts = strtotime($row['jr_end_date']);
                    $today_ts = strtotime($today);
                    if ($end_ts >= $today_ts) {
                        $remaining = (int)(($end_ts - $today_ts) / 86400) . '일';
                    } else {
                        $remaining = '마감';
                    }
                }

                $ad_labels = isset($row['jr_ad_labels']) ? trim($row['jr_ad_labels']) : '';
                if (!$ad_labels) {
                    $jc = (int)($row['jr_jump_count'] ?? 0);
                    $period = (int)($row['jr_ad_period'] ?? 30);
                    $ad_labels = ($jc <= 300) ? '줄광고 30일' : (($jc <= 700) ? '줄광고 60일' : (($jc <= 1200) ? '줄광고 90일' : "줄광고 {$period}일"));
                }

                $bg = 'bg' . ($i % 2);
                $confirm_url = './jobs_register_confirm_update.php?jr_id=' . $row['jr_id'] . '&token=' . $token;
                $approve_url = './jobs_register_approve_update.php?jr_id=' . $row['jr_id'] . '&token=' . $token;
                $view_url = $jobs_view_base . '?jr_id=' . $row['jr_id'];
                ?>
                <tr class="<?php echo $bg; ?>">
                    <td class="td_chk">
                        <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo (int)$row['jr_id']; ?></label>
                        <input type="checkbox" name="chk[]" value="<?php echo (int)$row['jr_id']; ?>" id="chk_<?php echo $i; ?>">
                    </td>
                    <td class="td_datetime"><?php echo $row['jr_datetime']; ?></td>
                    <td class="td_num"><?php echo (int)$row['jr_id']; ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($row['mb_id']); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($row['jr_nickname']); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars(cut_str($row['jr_company'], 20)); ?></td>
                    <td class="td_num"><?php echo number_format($row['jr_total_amount']); ?>원</td>
                    <td class="td_left" style="font-size:11px;"><?php echo htmlspecialchars(cut_str(str_replace(',', ', ', $ad_labels), 50)); ?></td>
                    <td class="td_num"><span class="status-badge status-payment-<?php echo $payment_ok ? 'ok' : 'wait'; ?>"><?php echo $payment_label; ?></span></td>
                    <td class="td_num"><span class="status-badge status-approve-<?php echo $approved ? 'ok' : 'wait'; ?>"><?php echo $approve_label; ?></span></td>
                    <td class="td_num"><?php echo (int)$row['jr_ad_period']; ?>일</td>
                    <td class="td_num"><?php echo $remaining; ?></td>
                    <td class="td_mng"><a href="<?php echo $view_url; ?>" class="btn btn_02" target="_blank">보기</a></td>
                    <td class="td_mng td_mng_l">
                        <?php if (!$payment_ok) { ?>
                            <a href="<?php echo $confirm_url; ?>" class="btn btn_02" onclick="return confirm('입금확인 하시겠습니까?');">입금확인</a>
                        <?php } elseif (!$approved) { ?>
                            <a href="<?php echo $approve_url; ?>" class="btn btn_03" onclick="return confirm('승인하시면 광고가 노출됩니다. 진행하시겠습니까?');">승인</a>
                        <?php } else { ?>
                            —
                        <?php } ?>
                    </td>
                </tr>
                <?php
                $i++;
            }
            if ($i == 0) {
                echo '<tr><td colspan="14" class="empty_table"><span>자료가 없습니다.</span></td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <button type="button" class="btn btn_02" onclick="fjobslist_do('입금확인')">선택입금확인</button>
    <button type="button" class="btn btn_03" onclick="fjobslist_do('승인')">선택승인</button>
</div>

</form>

<script>
function check_all(f) {
    var chk = f.chkall ? f.chkall.checked : false;
    for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name === 'chk[]') f.elements[i].checked = chk;
    }
}
function fjobslist_do(act) {
    var f = document.getElementById('fjobslist');
    if (!f) return;
    var chks = document.getElementsByName('chk[]');
    var ids = [];
    for (var i = 0; i < chks.length; i++) {
        if (chks[i].checked && chks[i].value && f.contains(chks[i])) ids.push(chks[i].value);
    }
    if (ids.length === 0) { alert('항목을 선택하세요.'); return; }
    var idsStr = ids.join(',');
    var el = document.getElementById('jr_ids_hidden');
    if (el) el.value = idsStr;
    var url;
    if (act === '입금확인') {
        if (!confirm(ids.length + '건 입금확인 하시겠습니까?')) return;
        url = './jobs_register_confirm_update.php?jr_ids=' + encodeURIComponent(idsStr);
    } else if (act === '승인') {
        if (!confirm(ids.length + '건 승인하시겠습니까? 광고가 노출됩니다.')) return;
        url = './jobs_register_approve_update.php?jr_ids=' + encodeURIComponent(idsStr);
    } else return;
    f.action = url;
    f.submit();
}
</script>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?" . ($qstr ? $qstr . '&' : '') . "page="); ?>

<?php
require_once './admin.tail.php';
