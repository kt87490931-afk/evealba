<?php
/**
 * 어드민 - 추천인·이력서 관리
 * 추천인 카운터, 추천인 이력서 카운터 조회 및 이력서 삭제(작성 취소)
 */
$sub_menu = '910970';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '추천인·이력서 관리';
require_once G5_ADMIN_PATH . '/admin.head.php';

$tb_rs = 'g5_resume';
$tb_mb = $g5['member_table'];
$rs_exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb_rs}'", false)) > 0;
$resume_list = array();
if ($rs_exists) {
    $r = sql_query("SELECT r.rs_id, r.mb_id, r.rs_nick, r.rs_title, r.rs_datetime, m.mb_recommend
        FROM {$tb_rs} r
        LEFT JOIN {$tb_mb} m ON r.mb_id = m.mb_id
        WHERE r.rs_status = 'active'
        ORDER BY r.rs_datetime DESC LIMIT 100");
    while ($row = sql_fetch_array($r)) {
        $resume_list[] = $row;
    }
}
$token = get_session('ss_admin_token') ?: get_admin_token();
?>
<div class="local_desc01 local_desc">
  <p>회원을 추천인으로 지정한 가입자 수(추천인 카운터)와 그 중 이력서를 작성한 회원 수(추천인 이력서 카운터)를 관리합니다. 기프티콘 발송 기준으로 사용됩니다.</p>
</div>

<div class="local_desc02 local_desc" style="margin-top:10px;">
  <a href="./member_list.php" class="btn btn_01">회원 목록 (추천인/이력서 카운터)</a>
</div>

<div class="tbl_head01 tbl_wrap" style="margin-top:16px;">
  <h2 class="h2_frm">이력서 목록 (삭제 = 작성 취소, 카운터 차감)</h2>
  <table>
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">회원ID</th>
        <th scope="col">닉네임</th>
        <th scope="col">제목</th>
        <th scope="col">추천인</th>
        <th scope="col">작성일</th>
        <th scope="col">삭제</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rs_exists || empty($resume_list)) { ?>
      <tr><td colspan="7" class="empty_table">등록된 이력서가 없습니다.</td></tr>
      <?php } else {
          foreach ($resume_list as $row) {
              $ref_nick = '';
              if (!empty($row['mb_recommend'])) {
                  $ids = array_map('trim', array_filter(explode(',', str_replace(['，', ' '], ',', $row['mb_recommend']))));
                  $nicks = array();
                  foreach ($ids as $id) {
                      $rr = sql_fetch("SELECT mb_nick FROM {$tb_mb} WHERE mb_id = '".sql_real_escape_string($id)."'");
                      $nicks[] = $rr ? get_text($rr['mb_nick']) : $id;
                  }
                  $ref_nick = implode(', ', $nicks);
              }
      ?>
      <tr>
        <td><?php echo (int)$row['rs_id']; ?></td>
        <td><?php echo htmlspecialchars($row['mb_id']); ?></td>
        <td><?php echo htmlspecialchars($row['rs_nick']); ?></td>
        <td><?php echo htmlspecialchars($row['rs_title']); ?></td>
        <td><?php echo htmlspecialchars($ref_nick); ?></td>
        <td><?php echo htmlspecialchars($row['rs_datetime']); ?></td>
        <td><a href="./eve_resume_delete.php?rs_id=<?php echo (int)$row['rs_id']; ?>&token=<?php echo urlencode($token); ?>" class="btn btn_02" onclick="return confirm('이력서를 삭제(작성 취소)하시겠습니까? 추천인 이력서 카운터가 1 차감됩니다.');">삭제</a></td>
      </tr>
      <?php } } ?>
    </tbody>
  </table>
</div>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
