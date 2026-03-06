<?php
/**
 * 어드민 - 쿠폰 발급/내역
 * 구분: 지금 발급(즉시) | 개별 발급 | 일괄 발급 | 발급 내역
 */
$sub_menu = '910940';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

include_once G5_LIB_PATH . '/ev_memo.lib.php';

$ec_id = isset($_GET['ec_id']) ? (int)$_GET['ec_id'] : 0;
if (!$ec_id) alert('쿠폰을 선택하세요.', './eve_coupon_list.php');

$tb = 'g5_ev_coupon';
$tb_issue = 'g5_ev_coupon_issue';
$ec = sql_fetch("SELECT * FROM {$tb} WHERE ec_id = '{$ec_id}'");
if (!$ec) alert('쿠폰을 찾을 수 없습니다.', './eve_coupon_list.php');

$msg = '';
$msg_type = 'ok'; // ok, warn, err

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $today = date('Y-m-d');

    if (isset($ec['ec_issue_from']) && $ec['ec_issue_from'] && $today < $ec['ec_issue_from']) {
        $msg = '발급 가능 기간이 아닙니다. (시작: '.$ec['ec_issue_from'].')';
        $msg_type = 'err';
    } elseif (isset($ec['ec_issue_to']) && $ec['ec_issue_to'] && $today > $ec['ec_issue_to']) {
        $msg = '발급 가능 기간이 지났습니다. (종료: '.$ec['ec_issue_to'].')';
        $msg_type = 'err';
    } elseif ($action === 'single') {
        $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
        if (!$mb_id) {
            $msg = '회원ID를 입력하세요.';
            $msg_type = 'err';
        } else {
            $mb = sql_fetch("SELECT mb_id, mb_1, mb_7 FROM {$g5['member_table']} WHERE mb_id = '".sql_escape_string($mb_id)."'");
            if (!$mb) {
                $msg = '회원을 찾을 수 없습니다.';
                $msg_type = 'err';
            } elseif (($ec['ec_target'] ?? 'biz') === 'biz' && (!isset($mb['mb_1']) || ($mb['mb_1'] !== 'biz' && $mb['mb_1'] !== 'business'))) {
                $msg = '기업회원 전용 쿠폰입니다.';
                $msg_type = 'err';
            } elseif (($ec['ec_target'] ?? 'biz') === 'biz' && (!isset($mb['mb_7']) || $mb['mb_7'] !== 'approved')) {
                $msg = '승인된 기업회원에게만 발급 가능합니다. (기업회원 승인관리에서 승인 필요)';
                $msg_type = 'err';
            } else {
                $limit = (int)($ec['ec_issue_limit_per_member'] ?? 0);
                if ($limit > 0) {
                    $cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '".sql_escape_string($mb_id)."'");
                    if (($cnt['c'] ?? 0) >= $limit) {
                        $msg = '1인당 발급 한도('.$limit.'장)를 초과했습니다.';
                        $msg_type = 'err';
                    }
                }
                if ($msg === '') {
                    sql_query("INSERT INTO {$tb_issue} (ec_id, mb_id) VALUES ('{$ec_id}', '".sql_escape_string($mb_id)."')", false);
                    if (!empty($ec['ec_memo_send'])) {
                        $memo_content = '쿠폰이 도착하였습니다. ' . get_text($ec['ec_name']);
                        ev_send_memo($mb_id, $memo_content, '');
                    }
                    $msg = $mb_id . ' 회원에게 발급되었습니다.';
                }
            }
        }
    } elseif ($action === 'bulk') {
        $include_pending = isset($_POST['include_pending']) && $_POST['include_pending'] == '1';
        $mb7_cond = $include_pending ? " AND (mb_7 = 'approved' OR mb_7 = 'pending' OR mb_7 = '' OR mb_7 IS NULL)" : " AND mb_7 = 'approved'";
        $mb_list = array();
        $r = sql_query("SELECT mb_id FROM {$g5['member_table']} WHERE (mb_1 = 'biz' OR mb_1 = 'business') {$mb7_cond}");
        if ($r) while ($row = sql_fetch_array($r)) $mb_list[] = $row['mb_id'];

        $send_memo = (isset($_POST['send_memo']) && $_POST['send_memo'] == '1') || !empty($ec['ec_memo_send']);
        $limit = (int)($ec['ec_issue_limit_per_member'] ?? 0);
        $ec_use_limit = (int)($ec['ec_use_limit'] ?? 0);
        $issued = 0;
        $issued_mb_ids = array();
        foreach ($mb_list as $mb_id) {
            if ($ec_use_limit > 0) {
                $total = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}'");
                if (($total['c'] ?? 0) >= $ec_use_limit) break;
            }
            if ($limit > 0) {
                $cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '".sql_escape_string($mb_id)."'");
                if (($cnt['c'] ?? 0) >= $limit) continue;
            }
            $ex = sql_fetch("SELECT eci_id FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '".sql_escape_string($mb_id)."' LIMIT 1");
            if ($ex) continue;
            sql_query("INSERT INTO {$tb_issue} (ec_id, mb_id) VALUES ('{$ec_id}', '".sql_escape_string($mb_id)."')", false);
            $issued++;
            $issued_mb_ids[] = $mb_id;
        }
        if ($send_memo && !empty($issued_mb_ids)) {
            $memo_content = '쿠폰이 도착하였습니다. ' . get_text($ec['ec_name']);
            foreach ($issued_mb_ids as $mid) ev_send_memo($mid, $memo_content, '');
        }
        if ($issued > 0) {
            $msg = $issued . '명에게 발급되었습니다.' . ($send_memo ? ' (쪽지 발송 완료)' : '');
        } else {
            $msg = '발급 대상이 없습니다. 기업회원(mb_1=biz)이며 승인(승인대기 포함)된 회원이 있는지 확인하세요.';
            $msg_type = 'warn';
        }
    }
}

$type_map = array('thumb'=>'썸네일','ad'=>'채용공고','line_ad_free'=>'줄광고무료','gift'=>'기프티콘');
$type_txt = $type_map[$ec['ec_type'] ?? ''] ?? $ec['ec_type'];
$trigger_map = array('now'=>'지금','on_approval'=>'가입인증 후','monthly_1st'=>'매월 1일');
$it = $ec['ec_issue_type'] ?? '';
$at = trim($ec['ec_auto_trigger'] ?? '');
$auto_txt = ($it === 'auto' && isset($trigger_map[$at])) ? $trigger_map[$at] : '';

$issued_cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}'");
$used_cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND eci_used = 1");
$this_month = date('Y-m');
$month_issued = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND DATE_FORMAT(eci_issued_at, '%Y-%m') = '{$this_month}'");
$month_used = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND eci_used = 1 AND DATE_FORMAT(eci_used_at, '%Y-%m') = '{$this_month}'");

$tab = isset($_GET['tab']) ? preg_replace('/[^a-z]/', '', $_GET['tab']) : ((int)($issued_cnt['c'] ?? 0) > 0 ? 'history' : 'issue');

$g5['title'] = '쿠폰 발급: ' . htmlspecialchars($ec['ec_name']);
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<style>
.ev-coupon-issue { max-width: 900px; }
.ev-coupon-issue .card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px; margin-bottom:20px; }
.ev-coupon-issue .card h3 { margin:0 0 12px 0; font-size:15px; color:#333; }
.ev-coupon-issue .stats { display:flex; gap:24px; margin-bottom:16px; flex-wrap:wrap; }
.ev-coupon-issue .stats span { color:#666; }
.ev-coupon-issue .stats strong { color:#1976d2; }
.ev-coupon-issue .msg-ok { padding:12px; background:#e8f5e9; color:#2e7d32; border-radius:6px; margin-bottom:16px; }
.ev-coupon-issue .msg-warn { padding:12px; background:#fff3e0; color:#e65100; border-radius:6px; margin-bottom:16px; }
.ev-coupon-issue .msg-err { padding:12px; background:#ffebee; color:#c62828; border-radius:6px; margin-bottom:16px; }
.ev-coupon-issue .target-info { font-size:13px; color:#666; margin:10px 0; }
.ev-coupon-issue .frm-inline { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.ev-coupon-issue .frm-inline input[type="text"] { width:140px; }
.ev-coupon-issue .tab-wrap { margin:16px 0; }
.ev-coupon-issue .tab-wrap a { padding:8px 16px; border:1px solid #ddd; border-radius:6px; margin-right:8px; text-decoration:none; color:#333; }
.ev-coupon-issue .tab-wrap a.on { background:#1976d2; color:#fff; border-color:#1976d2; }
</style>

<div class="ev-coupon-issue">
  <div class="card">
    <h3><?php echo htmlspecialchars($ec['ec_name']); ?></h3>
    <p style="color:#888; margin:0 0 12px 0;"><?php echo $type_txt; ?> · 대상: 기업회원<?php if ($auto_txt) { ?> · 자동: <?php echo $auto_txt; ?><?php } ?></p>
    <div class="stats">
      <span>총 발급 <strong><?php echo number_format($issued_cnt['c'] ?? 0); ?></strong>장</span>
      <span>사용 <strong><?php echo number_format($used_cnt['c'] ?? 0); ?></strong>장</span>
      <span>이번 달 발급 <strong><?php echo number_format($month_issued['c'] ?? 0); ?></strong>건</span>
    </div>
    <?php if ((int)($issued_cnt['c'] ?? 0) > 0) {
      $preview = sql_query("SELECT i.mb_id, m.mb_nick FROM {$tb_issue} i LEFT JOIN {$g5['member_table']} m ON i.mb_id = m.mb_id WHERE i.ec_id = '{$ec_id}' ORDER BY i.eci_issued_at DESC LIMIT 10");
      $names = array();
      while ($p = sql_fetch_array($preview)) $names[] = $p['mb_id'] . ($p['mb_nick'] ? '('.$p['mb_nick'].')' : '');
    ?><p style="margin:8px 0 0 0; font-size:13px; color:#666;">발급된 회원: <?php echo htmlspecialchars(implode(', ', $names)); ?><?php if ((int)($issued_cnt['c']) > 10) echo ' ...'; ?></p>
    <?php } ?>
  </div>

  <?php if ($msg) {
    $cls = $msg_type === 'err' ? 'msg-err' : ($msg_type === 'warn' ? 'msg-warn' : 'msg-ok');
    echo '<div class="' . $cls . '">' . htmlspecialchars($msg) . '</div>';
  } ?>

  <div class="card">
    <h3>수동 발급</h3>
    <div class="tab-wrap">
      <a href="?ec_id=<?php echo $ec_id; ?>&tab=issue" class="<?php echo $tab === 'issue' ? 'on' : ''; ?>">개별/일괄</a>
      <a href="?ec_id=<?php echo $ec_id; ?>&tab=history" class="<?php echo $tab === 'history' ? 'on' : ''; ?>">발급 내역</a>
    </div>

    <?php if ($tab === 'issue') { ?>
    <div style="margin-top:16px;">
      <p style="margin-bottom:12px;"><strong>개별 발급</strong></p>
      <form method="post" class="frm-inline">
        <?php echo get_admin_token(); ?>
        <input type="hidden" name="action" value="single">
        <input type="text" name="mb_id" placeholder="회원ID" class="frm_input" required>
        <button type="submit" class="btn btn_01">발급</button>
      </form>

      <p style="margin:24px 0 12px 0;"><strong>일괄 발급</strong> (승인된 기업회원 전체)</p>
      <form method="post">
        <?php echo get_admin_token(); ?>
        <input type="hidden" name="action" value="bulk">
        <label><input type="checkbox" name="include_pending" value="1"> 승인대기 포함</label>
        <label style="margin-left:12px;"><input type="checkbox" name="send_memo" value="1"> 쪽지 발송</label>
        <button type="submit" class="btn btn_02" style="margin-left:12px;" onclick="return confirm('전체 기업회원에게 발급하시겠습니까?');">전체 발급</button>
      </form>
    </div>
    <?php } else {
      $rows_per_page = 20;
      $total_count = (int)($issued_cnt['c'] ?? 0);
      $total_page = $total_count > 0 ? ceil($total_count / $rows_per_page) : 1;
      $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
      if ($page > $total_page) $page = $total_page;
      $offset = ($page - 1) * $rows_per_page;
      $issued_list = sql_query("SELECT i.*, m.mb_name, m.mb_nick FROM {$tb_issue} i LEFT JOIN {$g5['member_table']} m ON i.mb_id = m.mb_id WHERE i.ec_id = '{$ec_id}' ORDER BY i.eci_issued_at DESC LIMIT {$offset}, {$rows_per_page}");
      $paging_url = './eve_coupon_issue.php?ec_id='.$ec_id.'&amp;tab=history&amp;';
    ?>
    <div style="margin-top:16px; overflow-x:auto;">
      <p class="target-info" style="margin-bottom:12px;">발급된 회원: 총 <strong><?php echo number_format($total_count); ?></strong>명</p>
      <table class="tbl_head01 tbl_wrap">
        <thead>
          <tr><th>회원ID</th><th>닉네임</th><th>회원명</th><th>발급일</th><th>사용</th><th>사용일</th></tr>
        </thead>
        <tbody>
        <?php
        $empty = true;
        while ($row = sql_fetch_array($issued_list)) { $empty = false; ?>
          <tr>
            <td><?php echo htmlspecialchars($row['mb_id']); ?></td>
            <td><?php echo htmlspecialchars($row['mb_nick'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['mb_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['eci_issued_at'] ?? ''); ?></td>
            <td><?php echo !empty($row['eci_used']) ? '사용' : '미사용'; ?></td>
            <td><?php echo !empty($row['eci_used']) ? htmlspecialchars($row['eci_used_at'] ?? '-') : '-'; ?></td>
          </tr>
        <?php }
        if ($empty) { ?><tr><td colspan="6" class="empty_table">발급 내역이 없습니다.</td></tr><?php } ?>
        </tbody>
      </table>
      <?php if ($total_page > 1) {
        echo '<div class="pg_wrap" style="margin-top:16px;">';
        echo '<span class="pg" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">';
        if ($page > 1) {
          echo '<a href="'.$paging_url.'page='.($page-1).'" class="pg_page">◀ 이전</a> ';
        }
        echo '<strong>'.$page.' / '.$total_page.'</strong> (총 '.number_format($total_count).'건)';
        if ($page < $total_page) {
          echo ' <a href="'.$paging_url.'page='.($page+1).'" class="pg_page">다음 ▶</a>';
        }
        echo '</span></div>';
      } else if ($total_count > 0) {
        echo '<p class="tbl_desc" style="margin-top:8px;">총 '.number_format($total_count).'건</p>';
      } ?>
    </div>
    <?php } ?>
  </div>

  <p><a href="./eve_coupon_list.php" class="btn btn_02">쿠폰 목록</a></p>
</div>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
