<?php
/**
 * 어드민 - 쿠폰 관리
 * g5_ev_coupon 마스터 CRUD, 발급 (ec_code 미노출)
 */
$sub_menu = '910940';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '쿠폰 관리';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'g5_ev_coupon';
$tb_issue = 'g5_ev_coupon_issue';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
?>
<div class="local_desc01 local_desc">
  <p>이브알바 쿠폰(기업회원: 광고/썸네일 할인, 일반회원: 기프티콘)을 관리합니다. 쿠폰번호는 사용하지 않습니다.</p>
</div>

<?php if (!$exists) { ?>
<div class="tbl_head01 tbl_wrap">
  <p style="padding:20px;color:#c00;">g5_ev_coupon 테이블이 없습니다. <a href="./run_migration_012.php">마이그레이션 012 실행</a>을 클릭하여 테이블을 생성해 주세요.</p>
  <p><a href="./run_migration_012.php" class="btn btn_01">마이그레이션 012 실행 (테이블 생성)</a></p>
</div>
<?php } else {
    $list = array();
    $res = sql_query("SELECT * FROM {$tb} ORDER BY ec_id DESC LIMIT 100");
    while ($r = sql_fetch_array($res)) { $list[] = $r; }
?>
<div class="local_desc02 local_desc" style="margin-top:10px;">
  <a href="./eve_coupon_form.php" class="btn btn_01">+ 쿠폰 추가</a>
  <a href="./run_migration_013.php" class="btn btn_02">마이그레이션 013 (발급기간 컬럼)</a>
</div>
<div class="tbl_head01 tbl_wrap">
  <table>
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">이름</th>
        <th scope="col">대상</th>
        <th scope="col">유형</th>
        <th scope="col">할인</th>
        <th scope="col">사용유효기간</th>
        <th scope="col">발급가능기간</th>
        <th scope="col">상태</th>
        <th scope="col">관리</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($list)) { ?>
      <tr><td colspan="9" class="empty_table">등록된 쿠폰이 없습니다. <a href="./eve_coupon_form.php">쿠폰 추가</a></td></tr>
      <?php } else {
          foreach ($list as $row) {
              $target_txt = ($row['ec_target'] ?? 'biz') === 'biz' ? '기업' : '일반';
              $type_map = array('thumb'=>'썸네일','ad'=>'채용공고','line_ad_free'=>'줄광고무료','gift'=>'기프티콘');
              $type_txt = $type_map[$row['ec_type'] ?? ''] ?? $row['ec_type'];
              $disc_txt = ($row['ec_discount_type'] ?? 'percent') === 'percent' ? (int)($row['ec_discount_value'] ?? 0).'%' : number_format((int)($row['ec_discount_value'] ?? 0)).'원';
              $valid = (isset($row['ec_valid_from']) && $row['ec_valid_from'] ? $row['ec_valid_from'] : '-') . ' ~ ' . (isset($row['ec_valid_to']) && $row['ec_valid_to'] ? $row['ec_valid_to'] : '-');
              $issue = '-';
              if (isset($row['ec_issue_from']) && isset($row['ec_issue_to'])) {
                  $issue = (isset($row['ec_issue_from']) && $row['ec_issue_from'] ? $row['ec_issue_from'] : '-') . ' ~ ' . (isset($row['ec_issue_to']) && $row['ec_issue_to'] ? $row['ec_issue_to'] : '-');
              }
              $active = !empty($row['ec_is_active']) ? '활성' : '비활성';
      ?>
      <tr>
        <td><?php echo (int)$row['ec_id']; ?></td>
        <td><?php echo htmlspecialchars($row['ec_name']); ?></td>
        <td><?php echo $target_txt; ?></td>
        <td><?php echo $type_txt; ?></td>
        <td><?php echo $disc_txt; ?></td>
        <td><?php echo $valid; ?></td>
        <td><?php echo $issue; ?></td>
        <td><?php echo $active; ?></td>
        <td>
          <a href="./eve_coupon_form.php?w=u&ec_id=<?php echo (int)$row['ec_id']; ?>" class="btn btn_03">수정</a>
          <a href="./eve_coupon_issue.php?ec_id=<?php echo (int)$row['ec_id']; ?>" class="btn btn_02">발급</a>
          <a href="./eve_coupon_delete.php?ec_id=<?php echo (int)$row['ec_id']; ?>" class="btn btn_02" onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
        </td>
      </tr>
      <?php }
      } ?>
    </tbody>
  </table>
</div>
<?php } ?>

<?php
require_once G5_ADMIN_PATH.'/admin.tail.php';
?>
