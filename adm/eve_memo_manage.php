<?php
/**
 * 어드민 - 쪽지관리
 * 전체 쪽지 보내기, 회원가입 시 자동 쪽지 설정
 */
$sub_menu = '910950';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

include_once G5_LIB_PATH . '/ev_memo.lib.php';

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
        $content = isset($_POST['me_memo']) ? preg_replace("#[\\\\]+$#", "", substr(trim($_POST['me_memo']), 0, 65536)) : '';

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
            foreach ($mb_list as $mb_id) {
                if (ev_send_memo($mb_id, $content, $send_mb_id)) {
                    $sent++;
                }
            }

            $msg = number_format($sent) . '명에게 쪽지를 발송했습니다.';
            $msg_type = 'success';
        }
    } elseif ($action === 'config_save' && $tb_exists) {
        $on = isset($_POST['em_join_memo_on']) && $_POST['em_join_memo_on'] == '1' ? 1 : 0;
        $general = isset($_POST['em_join_memo_general']) ? preg_replace("#[\\\\]+$#", "", substr(trim($_POST['em_join_memo_general']), 0, 65536)) : '';
        $biz = isset($_POST['em_join_memo_biz']) ? preg_replace("#[\\\\]+$#", "", substr(trim($_POST['em_join_memo_biz']), 0, 65536)) : '';
        $general_esc = sql_escape_string($general);
        $biz_esc = sql_escape_string($biz);

        sql_query("UPDATE {$tb_config} SET em_join_memo_on = '{$on}', em_join_memo_general = '{$general_esc}', em_join_memo_biz = '{$biz_esc}' WHERE emc_id = 1");
        $msg = '설정이 저장되었습니다.';
        $msg_type = 'success';
    }
}

// 설정 로드
$cfg = array('em_join_memo_on' => 0, 'em_join_memo_general' => '', 'em_join_memo_biz' => '');
if ($tb_exists) {
    $cfg_row = sql_fetch("SELECT * FROM {$tb_config} WHERE emc_id = 1");
    if ($cfg_row) {
        $cfg = $cfg_row;
    }
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
<?php } ?>

<div class="tbl_frm01 tbl_wrap" style="margin-top:24px;">
  <h3>1. 전체 쪽지 보내기</h3>
  <form method="post" id="frm_send" onsubmit="return confirm('선택한 대상에게 쪽지를 발송하시겠습니까?');">
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
  <form method="post">
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
            <textarea name="em_join_memo_general" rows="3" class="frm_input" style="width:100%;max-width:600px;"><?php echo htmlspecialchars($cfg['em_join_memo_general'] ?? ''); ?></textarea>
          </td>
        </tr>
        <tr>
          <th scope="row">기업회원 메시지</th>
          <td>
            <textarea name="em_join_memo_biz" rows="3" class="frm_input" style="width:100%;max-width:600px;"><?php echo htmlspecialchars($cfg['em_join_memo_biz'] ?? ''); ?></textarea>
          </td>
        </tr>
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

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
