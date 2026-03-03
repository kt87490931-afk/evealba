<?php
/**
 * 어드민 - 쿠폰 관리
 * g5_ev_coupon 마스터 CRUD, 발급 현황
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
  <p>이브알바 쿠폰(기업회원: 광고/썸네일 할인, 일반회원: 기프티콘)을 관리합니다.</p>
</div>

<?php if (!$exists) { ?>
<div class="tbl_head01 tbl_wrap">
  <p style="padding:20px;color:#c00;">g5_ev_coupon 테이블이 없습니다. 프로젝트 루트에서 <code>php run_migration.php</code> 또는 <code>php scripts/run_migration_012.php</code>를 실행해 주세요.</p>
</div>
<?php } else {
    $list = array();
    $res = sql_query("SELECT * FROM {$tb} ORDER BY ec_id DESC LIMIT 50");
    while ($r = sql_fetch_array($res)) { $list[] = $r; }
?>
<div class="tbl_head01 tbl_wrap">
  <table>
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">코드</th>
        <th scope="col">이름</th>
        <th scope="col">대상</th>
        <th scope="col">유형</th>
        <th scope="col">할인</th>
        <th scope="col">유효기간</th>
        <th scope="col">상태</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($list)) { ?>
      <tr><td colspan="8" class="empty_table">등록된 쿠폰이 없습니다.</td></tr>
      <?php } else {
          foreach ($list as $row) {
              $target_txt = $row['ec_target'] === 'biz' ? '기업' : '일반';
              $type_txt = $row['ec_type'] === 'thumb' ? '썸네일' : ($row['ec_type'] === 'ad' ? '광고' : '기프티콘');
              $disc_txt = $row['ec_discount_type'] === 'percent' ? $row['ec_discount_value'].'%' : number_format($row['ec_discount_value']).'원';
              $valid = ($row['ec_valid_from'] ?: '-') . ' ~ ' . ($row['ec_valid_to'] ?: '-');
              $active = $row['ec_is_active'] ? '활성' : '비활성';
      ?>
      <tr>
        <td><?php echo (int)$row['ec_id']; ?></td>
        <td><?php echo htmlspecialchars($row['ec_code']); ?></td>
        <td><?php echo htmlspecialchars($row['ec_name']); ?></td>
        <td><?php echo $target_txt; ?></td>
        <td><?php echo $type_txt; ?></td>
        <td><?php echo $disc_txt; ?></td>
        <td><?php echo $valid; ?></td>
        <td><?php echo $active; ?></td>
      </tr>
      <?php }
      } ?>
    </tbody>
  </table>
</div>
<p style="margin-top:12px;color:#888;font-size:13px;">쿠폰 생성·수정·발급 기능은 추후 구현 예정입니다.</p>
<?php } ?>

<?php
require_once G5_ADMIN_PATH.'/admin.tail.php';
?>
