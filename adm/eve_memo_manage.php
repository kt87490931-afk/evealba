<?php
/**
 * 어드민 - 쪽지관리
 * 전체 쪽지 보내기, 회원가입 시 자동 쪽지 설정
 */
$sub_menu = '910950';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

include_once G5_LIB_PATH . '/ev_memo.lib.php';

// 원화기호·백슬래시 제거 (한국어 Windows에서 \ 가 ₩로 표시되며, 저장 시마다 늘어나는 현상 방지)
function _ev_strip_won($s) {
    $s = (string)$s;
    $s = str_replace('\\', '', $s); // 백슬래시 제거 (한국어 환경에서 ₩로 표시됨)
    $s = preg_replace('/[\x{20A9}\x{FFE6}]/u', '', $s); // 원화기호 제거
    return $s;
}

$tb_config = 'g5_ev_memo_config';
$tb_exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb_config}'", false));

$msg = '';
$msg_type = '';

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    auth_check_menu($auth, $sub_menu, 'w');
    check_admin_token();

    $action = trim($_POST['action'] ?? '');

    if ($action === 'send') {
        $target = trim($_POST['target'] ?? '');
        $content = isset($_POST['me_memo']) ? _ev_strip_won(stripslashes(substr(trim($_POST['me_memo']), 0, 65536))) : '';

        if (!$content) {
            $msg = '쪽지 내용을 입력하세요.';
            $msg_type = 'error';
        } elseif (!in_array($target, array('general', 'biz_approved', 'all'), true)) {
            $msg = '대상을 선택하세요.';
            $msg_type = 'error';
        } else {
            set_time_limit(300);

            $where = "1=1";
            if ($target === 'general') {
                $where = "(mb_1 = '' OR mb_1 IS NULL OR mb_1 != 'biz') AND (mb_leave_date = '' OR mb_leave_date IS NULL) AND (mb_intercept_date = '' OR mb_intercept_date IS NULL)";
            } elseif ($target === 'biz_approved') {
                $where = "mb_1 = 'biz' AND mb_7 = 'approved' AND (mb_leave_date = '' OR mb_leave_date IS NULL) AND (mb_intercept_date = '' OR mb_intercept_date IS NULL)";
            } else {
                $where = "(mb_leave_date = '' OR mb_leave_date IS NULL) AND (mb_intercept_date = '' OR mb_intercept_date IS NULL)";
            }

            $r = sql_query("SELECT mb_id FROM {$g5['member_table']} WHERE {$where}");
            $mb_list = array();
            while ($row = sql_fetch_array($r)) {
                $mb_list[] = $row['mb_id'];
            }

            $send_mb_id = $member['mb_id'] ?? '';
            $sent = 0;
            $sent_mb_ids = array();
            foreach ($mb_list as $mb_id) {
                if (ev_send_memo($mb_id, $content, $send_mb_id)) {
                    $sent++;
                    $sent_mb_ids[] = $mb_id;
                }
            }
            if ($sent > 0 && function_exists('ev_memo_log')) {
                ev_memo_log('manual_bulk', $sent, $sent_mb_ids, utf8_strcut(strip_tags($content), 200, ''), $target, $send_mb_id);
            }

            $msg = number_format($sent) . '명에게 쪽지를 발송했습니다.';
            $msg_type = 'success';
        }
    } elseif ($action === 'config_save' && $tb_exists) {
        $on = isset($_POST['em_join_memo_on']) && $_POST['em_join_memo_on'] == '1' ? 1 : 0;
        $general = isset($_POST['em_join_memo_general']) ? _ev_strip_won(stripslashes(substr(trim($_POST['em_join_memo_general']), 0, 65536))) : '';
        $biz = isset($_POST['em_join_memo_biz']) ? _ev_strip_won(stripslashes(substr(trim($_POST['em_join_memo_biz']), 0, 65536))) : '';
        $monthly = isset($_POST['em_monthly_coupon_memo']) ? _ev_strip_won(stripslashes(substr(trim($_POST['em_monthly_coupon_memo']), 0, 65536))) : '';
        $general_esc = sql_escape_string($general);
        $biz_esc = sql_escape_string($biz);
        $monthly_esc = sql_escape_string($monthly);

        $cols_check = array();
        $cr = sql_query("SHOW COLUMNS FROM {$tb_config}", false);
        if ($cr) while ($r = sql_fetch_array($cr)) $cols_check[] = $r['Field'];
        $set_clause = "em_join_memo_on = '{$on}', em_join_memo_general = '{$general_esc}', em_join_memo_biz = '{$biz_esc}'";
        if (in_array('em_monthly_coupon_memo', $cols_check)) {
            $set_clause .= ", em_monthly_coupon_memo = '{$monthly_esc}'";
        }
        sql_query("UPDATE {$tb_config} SET {$set_clause} WHERE emc_id = 1");
        $msg = '설정이 저장되었습니다.';
        $msg_type = 'success';
    }
}

// 설정 로드
$cfg = array('em_join_memo_on' => 0, 'em_join_memo_general' => '', 'em_join_memo_biz' => '', 'em_monthly_coupon_memo' => '');
$has_monthly_col = false;
if ($tb_exists) {
    $cfg_row = sql_fetch("SELECT * FROM {$tb_config} WHERE emc_id = 1");
    if ($cfg_row) {
        $cfg = $cfg_row;
        // DB에 원화기호가 있으면 즉시 제거 후 저장 (저장 시마다 늘어나는 문제 방지)
        $needs_clean = false;
        $fields_clean = array('em_join_memo_general', 'em_join_memo_biz');
        if (in_array('em_monthly_coupon_memo', array_keys($cfg_row))) $fields_clean[] = 'em_monthly_coupon_memo';
        foreach ($fields_clean as $f) {
            if (!empty($cfg_row[$f]) && (strpos($cfg_row[$f], '\\') !== false || preg_match('/[\x{20A9}\x{FFE6}]/u', $cfg_row[$f]))) {
                $cfg[$f] = _ev_strip_won($cfg_row[$f]);
                $needs_clean = true;
            }
        }
        if ($needs_clean) {
            $cols_check = array();
            $cr = sql_query("SHOW COLUMNS FROM {$tb_config}", false);
            if ($cr) while ($r = sql_fetch_array($cr)) $cols_check[] = $r['Field'];
            $upd = array();
            foreach ($fields_clean as $f) {
                if (in_array($f, $cols_check)) $upd[] = "`{$f}` = '" . sql_escape_string(_ev_strip_won($cfg_row[$f] ?? '')) . "'";
            }
            if (!empty($upd)) sql_query("UPDATE {$tb_config} SET " . implode(', ', $upd) . " WHERE emc_id = 1");
        }
    }
    $cols_check = array();
    $cr = sql_query("SHOW COLUMNS FROM {$tb_config}", false);
    if ($cr) while ($r = sql_fetch_array($cr)) $cols_check[] = $r['Field'];
    $has_monthly_col = in_array('em_monthly_coupon_memo', $cols_check);
}

$g5['title'] = '쪽지관리';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_desc01 local_desc">
  <p>※ 쪽지는 알람/알림 기능으로 활용됩니다. 추후 기프티콘 발송 시에도 동일한 대상 구분을 사용합니다.</p>
</div>

<?php if ($msg) { ?>
<p class="tbl_desc" style="padding:10px;background:<?php echo $msg_type === 'success' ? '#e8f5e9' : '#ffebee'; ?>;color:<?php echo $msg_type === 'success' ? '#2E7D32' : '#c62828'; ?>;border-radius:6px;">
  <?php echo htmlspecialchars($msg); ?>
</p>
<?php } ?>

<?php if (!$tb_exists) { ?>
<p style="padding:20px;color:#c00;">g5_ev_memo_config 테이블이 없습니다. <a href="./run_migration_016.php">마이그레이션 016 실행</a>을 먼저 실행하세요.</p>
<p><a href="./run_migration_016.php" class="btn btn_01">마이그레이션 016 실행</a></p>
<?php } elseif (!$has_monthly_col) { ?>
<p style="padding:20px;color:#e65100;">매월1일 쿠폰 쪽지 설정을 사용하려면 <a href="./run_migration_018.php">마이그레이션 018 실행</a>이 필요합니다.</p>
<p><a href="./run_migration_018.php" class="btn btn_01">마이그레이션 018 실행</a></p>
<?php } ?>

<div class="tbl_frm01 tbl_wrap" style="margin-top:24px;">
  <h3>1. 전체 쪽지 보내기</h3>
  <form method="post" id="frm_send" accept-charset="UTF-8" onsubmit="return confirm('선택한 대상에게 쪽지를 발송하시겠습니까?');">
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="send">
    <table class="tbl_wrap">
      <tbody>
        <tr>
          <th scope="row">대상</th>
          <td>
            <label><input type="radio" name="target" value="general"> 일반회원</label>
            <label style="margin-left:20px;"><input type="radio" name="target" value="biz_approved"> 기업회원(승인)</label>
            <label style="margin-left:20px;"><input type="radio" name="target" value="all" checked> 전체회원</label>
          </td>
        </tr>
        <tr>
          <th scope="row">내용</th>
          <td>
            <textarea name="me_memo" rows="6" class="frm_input" style="width:100%;max-width:600px;" placeholder="쪽지 내용 (최대 65536자)" required></textarea>
          </td>
        </tr>
        <tr>
          <th scope="row"></th>
          <td>
            <button type="submit" class="btn btn_02">발송</button>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
</div>

<?php if ($tb_exists) { ?>
<div class="tbl_frm01 tbl_wrap" style="margin-top:32px;">
  <h3>2. 회원가입 시 자동 쪽지 설정</h3>
  <form method="post" accept-charset="UTF-8">
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="config_save">
    <table class="tbl_wrap">
      <tbody>
        <tr>
          <th scope="row">사용 여부</th>
          <td>
            <label><input type="radio" name="em_join_memo_on" value="1" <?php echo !empty($cfg['em_join_memo_on']) ? 'checked' : ''; ?>> 사용함</label>
            <label style="margin-left:20px;"><input type="radio" name="em_join_memo_on" value="0" <?php echo empty($cfg['em_join_memo_on']) ? 'checked' : ''; ?>> 사용안함</label>
          </td>
        </tr>
        <tr>
          <th scope="row">일반회원 메시지</th>
          <td>
            <textarea name="em_join_memo_general" rows="3" class="frm_input" style="width:100%;max-width:600px;"><?php echo htmlspecialchars(_ev_strip_won($cfg['em_join_memo_general'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
          </td>
        </tr>
        <tr>
          <th scope="row">기업회원 메시지</th>
          <td>
            <textarea name="em_join_memo_biz" rows="3" class="frm_input" style="width:100%;max-width:600px;"><?php echo htmlspecialchars(_ev_strip_won($cfg['em_join_memo_biz'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
          </td>
        </tr>
        <?php if ($has_monthly_col) { ?>
        <tr>
          <th scope="row">3. 매월 1일 쿠폰 발급 쪽지</th>
          <td>
            <textarea name="em_monthly_coupon_memo" rows="4" class="frm_input" style="width:100%;max-width:600px;" placeholder="매월 1일 자동 발급 시 발송. 비워두면 기본 문구 사용. 이모지 입력 가능."><?php echo htmlspecialchars(_ev_strip_won($cfg['em_monthly_coupon_memo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            <p class="frm_info" style="margin-top:6px;">비워두면 "쿠폰이 도착하였습니다. [쿠폰명]" 사용</p>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <th scope="row"></th>
          <td>
            <button type="submit" class="btn btn_01">저장</button>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<?php } ?>

<?php
$tb_log = 'g5_ev_memo_log';
$log_exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb_log}'", false));
if ($log_exists) {
    $eml_type_labels = array('manual_bulk' => '수동발송', 'join_general' => '자동(일반가입)', 'join_biz' => '자동(기업가입)', 'monthly_coupon' => '매월1일쿠폰');
    $eml_target_labels = array('general' => '일반회원', 'biz_approved' => '기업회원(승인)', 'all' => '전체회원');

    $rows_per_page = 20;
    $total_count = (int)sql_fetch("SELECT COUNT(*) AS c FROM {$tb_log}")['c'];
    $total_page = $total_count > 0 ? ceil($total_count / $rows_per_page) : 1;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    if ($page > $total_page) $page = $total_page;
    $offset = ($page - 1) * $rows_per_page;

    $list = sql_query("SELECT * FROM {$tb_log} ORDER BY eml_id DESC LIMIT {$offset}, {$rows_per_page}");
    $paging_url = './eve_memo_manage.php?';
?>
<div class="tbl_frm01 tbl_wrap" style="margin-top:32px;">
  <h3>3. 발송 내역</h3>
  <p class="tbl_desc">수동 발송·자동 쪽지 발송 이력을 확인합니다. (마이그레이션 019 이후 발송분만 표시)</p>
  <div style="margin-top:16px; overflow-x:auto;">
    <table class="tbl_head01 tbl_wrap">
      <thead>
        <tr>
          <th>유형</th>
          <th>대상</th>
          <th>발송건수</th>
          <th>수신자</th>
          <th>발송일시</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $empty = true;
      while ($row = sql_fetch_array($list)) {
          $empty = false;
          $type_label = isset($eml_type_labels[$row['eml_type']]) ? $eml_type_labels[$row['eml_type']] : $row['eml_type'];
          $target_label = !empty($row['eml_target']) && isset($eml_target_labels[$row['eml_target']]) ? $eml_target_labels[$row['eml_target']] : ($row['eml_target'] ?: '-');
          $recipients = array();
          if (!empty($row['eml_recipients'])) {
              $decoded = @json_decode($row['eml_recipients'], true);
              if (is_array($decoded)) $recipients = $decoded;
          }
          $recv_display = '-';
          if (!empty($recipients)) {
              if (count($recipients) <= 5) {
                  $recv_display = implode(', ', array_map('htmlspecialchars', $recipients));
              } else {
                  $first5 = array_slice($recipients, 0, 5);
                  $recv_display = implode(', ', array_map('htmlspecialchars', $first5)) . ' 외 ' . (count($recipients) - 5) . '명';
              }
          }
          ?>
        <tr>
          <td><?php echo htmlspecialchars($type_label); ?></td>
          <td><?php echo htmlspecialchars($target_label); ?></td>
          <td><?php echo number_format((int)$row['eml_count']); ?>건</td>
          <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis;"><?php echo $recv_display; ?></td>
          <td><?php echo htmlspecialchars($row['eml_datetime'] ?? ''); ?></td>
        </tr>
      <?php }
      if ($empty) { ?><tr><td colspan="5" class="empty_table">발송 내역이 없습니다.</td></tr><?php } ?>
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
    } ?>
  </div>
</div>
<?php } else { ?>
<div class="tbl_frm01 tbl_wrap" style="margin-top:32px;">
  <h3>3. 발송 내역</h3>
  <p style="padding:12px; color:#e65100;">발송 내역을 보려면 <a href="./run_migration_019.php">마이그레이션 019</a>를 먼저 실행하세요.</p>
</div>
<?php } ?>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
