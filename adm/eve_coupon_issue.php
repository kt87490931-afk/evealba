<?php
/**
 * 어드민 - 쿠폰 개인/일괄 발급
 */
$sub_menu = '910940';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$ec_id = isset($_GET['ec_id']) ? (int)$_GET['ec_id'] : 0;
if (!$ec_id) alert('쿠폰을 선택하세요.', './eve_coupon_list.php');

$tb = 'g5_ev_coupon';
$tb_issue = 'g5_ev_coupon_issue';
$ec = sql_fetch("SELECT * FROM {$tb} WHERE ec_id = '{$ec_id}'");
if (!$ec) alert('쿠폰을 찾을 수 없습니다.', './eve_coupon_list.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $today = date('Y-m-d');

    if (isset($ec['ec_issue_from']) && $ec['ec_issue_from'] && $today < $ec['ec_issue_from']) {
        $msg = '발급 가능 기간이 아닙니다. (시작: '.$ec['ec_issue_from'].')';
    } elseif (isset($ec['ec_issue_to']) && $ec['ec_issue_to'] && $today > $ec['ec_issue_to']) {
        $msg = '발급 가능 기간이 지났습니다. (종료: '.$ec['ec_issue_to'].')';
    } elseif ($action === 'single') {
        $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
        if (!$mb_id) $msg = '회원ID를 입력하세요.';
        else {
            $mb = sql_fetch("SELECT mb_id, mb_1, mb_7 FROM {$g5['member_table']} WHERE mb_id = '".sql_escape_string($mb_id)."'");
            if (!$mb) $msg = '회원을 찾을 수 없습니다.';
            elseif (($ec['ec_target'] ?? 'biz') === 'biz' && (!isset($mb['mb_1']) || $mb['mb_1'] !== 'biz')) $msg = '기업회원 전용 쿠폰입니다.';
            elseif (($ec['ec_target'] ?? 'biz') === 'biz' && (!isset($mb['mb_7']) || $mb['mb_7'] !== 'approved')) $msg = '승인된 기업회원에게만 발급 가능합니다.';
            else {
                $limit = (int)($ec['ec_issue_limit_per_member'] ?? 0);
                if ($limit > 0) {
                    $cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '".sql_escape_string($mb_id)."'");
                    if (($cnt['c'] ?? 0) >= $limit) $msg = '1인당 발급 한도('.$limit.'장)를 초과했습니다.';
                }
                if (!$msg) {
                    sql_query("INSERT INTO {$tb_issue} (ec_id, mb_id) VALUES ('{$ec_id}', '".sql_escape_string($mb_id)."')", false);
                    $msg = $mb_id . ' 회원에게 발급되었습니다.';
                }
            }
        }
    } elseif ($action === 'bulk') {
        $target = isset($_POST['bulk_target']) ? trim($_POST['bulk_target']) : 'all_biz';
        $mb_list = array();
        if ($target === 'all_biz') {
            $r = sql_query("SELECT mb_id FROM {$g5['member_table']} WHERE mb_1 = 'biz' AND mb_7 = 'approved'");
            while ($row = sql_fetch_array($r)) $mb_list[] = $row['mb_id'];
        }
        $limit = (int)($ec['ec_issue_limit_per_member'] ?? 0);
        $ec_use_limit = (int)($ec['ec_use_limit'] ?? 0);
        $issued = 0;
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
        }
        $msg = '전체 기업회원 중 ' . $issued . '명에게 발급되었습니다.';
    }
}

$g5['title'] = '쿠폰 발급: ' . htmlspecialchars($ec['ec_name']);
require_once G5_ADMIN_PATH . '/admin.head.php';

$issued_cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}'");
$used_cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND eci_used = 1");
?>
<div class="local_desc01 local_desc">
  <p><strong><?php echo htmlspecialchars($ec['ec_name']); ?></strong> 쿠폰을 개별 또는 일괄로 발급합니다.</p>
  <p>발급 현황: 총 <?php echo number_format($issued_cnt['c'] ?? 0); ?>장 / 사용 <?php echo number_format($used_cnt['c'] ?? 0); ?>장</p>
</div>

<?php if ($msg) { ?><p style="padding:10px;background:#e8f5e9;color:#2E7D32;border-radius:6px;"><?php echo htmlspecialchars($msg); ?></p><?php } ?>

<div class="tbl_frm01 tbl_wrap" style="margin-top:20px;">
  <h3>개별 발급</h3>
  <form method="post" style="margin:10px 0;">
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="single">
    <input type="text" name="mb_id" placeholder="회원ID" class="frm_input" size="20" required>
    <button type="submit" class="btn btn_01">발급</button>
  </form>

  <h3 style="margin-top:24px;">일괄 발급</h3>
  <form method="post" style="margin:10px 0;">
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="bulk">
    <input type="hidden" name="bulk_target" value="all_biz">
    <p>승인된 기업회원(mb_1='biz', mb_7='approved') 전체에게 발급합니다. 이미 보유한 회원은 제외됩니다.</p>
    <button type="submit" class="btn btn_02" onclick="return confirm('전체 기업회원에게 발급하시겠습니까?');">전체 기업회원 발급</button>
  </form>
</div>

<div class="tbl_head01 tbl_wrap" style="margin-top:24px;">
  <h3>최근 발급 내역</h3>
  <table>
    <thead>
      <tr>
        <th>회원ID</th>
        <th>발급일</th>
        <th>사용여부</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $r = sql_query("SELECT i.*, m.mb_name FROM {$tb_issue} i LEFT JOIN {$g5['member_table']} m ON i.mb_id = m.mb_id WHERE i.ec_id = '{$ec_id}' ORDER BY i.eci_issued_at DESC LIMIT 30");
      $empty = true;
      while ($row = sql_fetch_array($r)) { $empty = false; ?>
      <tr>
        <td><?php echo htmlspecialchars($row['mb_id']); ?></td>
        <td><?php echo htmlspecialchars($row['eci_issued_at'] ?? ''); ?></td>
        <td><?php echo !empty($row['eci_used']) ? '사용' : '미사용'; ?></td>
      </tr>
      <?php }
      if ($empty) { ?>
      <tr><td colspan="3" class="empty_table">발급 내역이 없습니다.</td></tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<p style="margin-top:20px;"><a href="./eve_coupon_list.php" class="btn btn_02">목록</a></p>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
