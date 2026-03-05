<?php
/**
 * 어드민 - 매칭시스템
 * On/Off, 조건 설정, 수동 실행, 매칭 이력 조회
 */
$sub_menu = '910960';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

if (file_exists(G5_LIB_PATH . '/ev_matching.lib.php')) {
    include_once G5_LIB_PATH . '/ev_matching.lib.php';
}
if (file_exists(G5_LIB_PATH . '/ev_memo.lib.php')) {
    include_once G5_LIB_PATH . '/ev_memo.lib.php';
}

$tb_log = 'g5_ev_matching_log';
$tb_cfg = 'g5_ev_matching_config';
$tb_check = sql_query("SHOW TABLES LIKE '{$tb_log}'", false);
$tb_exists = ($tb_check && sql_num_rows($tb_check) > 0);

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    auth_check_menu($auth, $sub_menu, 'w');
    check_admin_token();

    $action = trim($_POST['action'] ?? '');

    if ($action === 'config_save' && $tb_exists) {
        $enabled = isset($_POST['mc_enabled']) && $_POST['mc_enabled'] == '1' ? '1' : '0';
        $min_rate = (int)($_POST['mc_min_rate'] ?? 70);
        $min_rate = max(0, min(100, $min_rate));
        $re_days = (int)($_POST['mc_re_match_days'] ?? 7);
        $re_days = max(1, min(90, $re_days));
        $min_eve = (int)($_POST['mc_min_eve_count'] ?? 10);
        $min_ent = (int)($_POST['mc_min_ent_count'] ?? 5);

        $updates = array(
            array('enabled', $enabled),
            array('min_rate', (string)$min_rate),
            array('re_match_days', (string)$re_days),
            array('min_eve_count', (string)$min_eve),
            array('min_ent_count', (string)$min_ent),
        );
        foreach ($updates as $u) {
            $k = sql_escape_string($u[0]);
            $v = sql_escape_string($u[1]);
            sql_query("INSERT INTO {$tb_cfg} (mc_key, mc_value, mc_updated) VALUES ('{$k}', '{$v}', NOW()) ON DUPLICATE KEY UPDATE mc_value = '{$v}', mc_updated = NOW()");
        }
        $msg = '설정이 저장되었습니다.';
        $msg_type = 'success';
    } elseif ($action === 'run_now' && $tb_exists && function_exists('ev_matching_run')) {
        $result = ev_matching_run();
        if ($result['ok']) {
            $pairs = isset($result['diag']['pairs']) ? (int)$result['diag']['pairs'] : 0;
            $msg = "매칭 실행 완료. {$pairs}쌍 매칭되었습니다.";
            $msg_type = 'success';
        } else {
            $msg = '매칭 실행 실패: ' . ($result['msg'] ?? '');
            $msg_type = 'error';
        }
    }
}

$cfg = array(
    'enabled' => '0',
    'min_rate' => '70',
    're_match_days' => '7',
    'min_eve_count' => '10',
    'min_ent_count' => '5',
);
if ($tb_exists) {
    $r_cfg = @sql_query("SELECT mc_key, mc_value FROM {$tb_cfg}", false);
    if ($r_cfg && sql_num_rows($r_cfg) > 0) {
        while ($row = sql_fetch_array($r_cfg)) {
            $k = isset($row['mc_key']) ? $row['mc_key'] : '';
            $v = isset($row['mc_value']) ? $row['mc_value'] : '';
            if (isset($cfg[$k])) {
                $cfg[$k] = (string)$v;
            }
        }
    }
}

$today_count = 0;
$eve_count = 0;
$ent_count = 0;
if ($tb_exists) {
    $row = @sql_fetch("SELECT COUNT(*) AS cnt FROM {$tb_log} WHERE DATE(matched_at) = CURDATE()");
    $today_count = isset($row['cnt']) ? (int)$row['cnt'] : 0;
    $row = @sql_fetch("SELECT COUNT(*) AS cnt FROM g5_resume WHERE rs_status='active' AND rs_job1!='' AND rs_job1 IS NOT NULL AND rs_work_region!='' AND rs_work_region IS NOT NULL");
    $eve_count = isset($row['cnt']) ? (int)$row['cnt'] : 0;
    $row = @sql_fetch("SELECT COUNT(*) AS cnt FROM g5_jobs_register WHERE jr_status='ongoing' AND (jr_end_date IS NULL OR jr_end_date >= CURDATE())");
    $ent_count = isset($row['cnt']) ? (int)$row['cnt'] : 0;
}

$g5['title'] = '매칭시스템';
$token = get_session('ss_admin_token') ?: get_admin_token();
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_desc01 local_desc">
  <p>기업회원과 이브회원을 AI 기반으로 매일 1쌍씩 매칭합니다. 기업·이브회원 가입이 충분할 때 활성화하세요.</p>
</div>

<?php if ($msg) { ?>
<p class="tbl_desc" style="padding:10px;background:<?php echo $msg_type === 'success' ? '#e8f5e9' : '#ffebee'; ?>;color:<?php echo $msg_type === 'success' ? '#2E7D32' : '#c62828'; ?>;border-radius:6px;">
  <?php echo htmlspecialchars($msg); ?>
</p>
<?php } ?>

<?php if (!$tb_exists) { ?>
<p style="padding:14px;background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;margin-bottom:16px;">
  <strong>⚠ g5_ev_matching_log 테이블이 없습니다.</strong> <a href="<?php echo G5_URL; ?>/run_migration.php">run_migration.php</a>를 실행해주세요.
</p>
<?php } ?>

<form method="post" action="" id="frm_matching_config">
<input type="hidden" name="action" value="config_save">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
<div class="tbl_head01 tbl_wrap" id="ev_matching_main" style="padding:20px;background:#fff;border:1px solid #ddd;border-radius:8px;">
  <h2 class="h2_frm">현황 · 설정</h2>
  <table>
    <thead><tr><th scope="col">항목</th><th scope="col">값</th></tr></thead>
    <tbody>
      <tr><td><strong>매칭 On/Off</strong></td>
        <td><label><input type="radio" name="mc_enabled" value="1" <?php echo $cfg['enabled'] === '1' ? 'checked' : ''; ?>> 활성</label>
            <label style="margin-left:12px;"><input type="radio" name="mc_enabled" value="0" <?php echo $cfg['enabled'] !== '1' ? 'checked' : ''; ?>> 비활성</label></td></tr>
      <tr><td>오늘 매칭 건수</td><td><?php echo number_format($today_count); ?>쌍</td></tr>
      <tr><td>이브회원 후보 수</td><td><?php echo number_format($eve_count); ?>명</td></tr>
      <tr><td>기업회원 후보 수</td><td><?php echo number_format($ent_count); ?>건</td></tr>
      <tr><td>최소 일치율 (%)</td><td><input type="number" name="mc_min_rate" value="<?php echo htmlspecialchars($cfg['min_rate']); ?>" min="0" max="100" size="5"></td></tr>
      <tr><td>재매칭 허용 일수</td><td><input type="number" name="mc_re_match_days" value="<?php echo htmlspecialchars($cfg['re_match_days']); ?>" min="1" max="90" size="5">일</td></tr>
      <tr><td>최소 이브회원 수</td><td><input type="number" name="mc_min_eve_count" value="<?php echo htmlspecialchars($cfg['min_eve_count']); ?>" min="0" size="5">명</td></tr>
      <tr><td>최소 기업회원 수</td><td><input type="number" name="mc_min_ent_count" value="<?php echo htmlspecialchars($cfg['min_ent_count']); ?>" min="0" size="5">건</td></tr>
    </tbody>
  </table>
  <p style="margin:14px 0 0 0;"><button type="submit" class="btn_frmline">설정 저장</button></p>
</div>
</form>
<form method="post" action="" style="margin-top:12px;" onsubmit="return confirm('지금 매칭을 실행하시겠습니까?');">
<input type="hidden" name="action" value="run_now">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
<button type="submit" class="btn btn_02">지금 매칭 실행</button>
</form>

<div class="tbl_head01 tbl_wrap" style="margin-top:24px;">
  <h2 class="h2_frm">최근 매칭 이력</h2>
  <table>
    <thead>
      <tr>
        <th scope="col">이브회원</th>
        <th scope="col">기업회원</th>
        <th scope="col">일치율</th>
        <th scope="col">매칭일시</th>
      </tr>
    </thead>
    <tbody>
<?php
$log_rows = array();
if ($tb_exists) {
    $r = @sql_query("SELECT m.mb_id_eve, m.mb_id_ent, m.match_rate, m.matched_at
        FROM {$tb_log} m ORDER BY m.matched_at DESC LIMIT 30");
    if ($r) {
        while ($row = sql_fetch_array($r)) {
            $log_rows[] = $row;
        }
    }
}
foreach ($log_rows as $row) {
?>
      <tr>
        <td><?php echo htmlspecialchars($row['mb_id_eve']); ?></td>
        <td><?php echo htmlspecialchars($row['mb_id_ent']); ?></td>
        <td><?php echo (int)$row['match_rate']; ?>%</td>
        <td><?php echo htmlspecialchars($row['matched_at']); ?></td>
      </tr>
<?php }
if (empty($log_rows)) {
?>
      <tr><td colspan="4" style="text-align:center;color:#888;">매칭 이력이 없습니다.</td></tr>
<?php } ?>
    </tbody>
  </table>
</div>

<p style="margin-top:20px;font-size:12px;color:#888;">Cron 등록 예: <code>0 6 * * * cd /var/www/evealba && php cron_matching.php</code></p>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
