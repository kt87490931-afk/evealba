<?php
/**
 * 어드민 - 썸네일상점 관리
 * 옵션별 가격 설정, 판매 현황
 */
$sub_menu = '910930';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '썸네일상점 관리';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'g5_jobs_thumb_option_paid';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
?>
<div class="local_desc01 local_desc">
  <p>썸네일 유료 옵션(뱃지, 테두리, 모션, 웨이브, 유료컬러) 가격 및 판매 현황을 관리합니다.</p>
</div>

<?php if (!$exists) { ?>
<div class="tbl_head01 tbl_wrap">
  <p style="padding:20px;color:#c00;">g5_jobs_thumb_option_paid 테이블이 없습니다. 아래 버튼을 클릭하여 마이그레이션을 실행해 주세요.</p>
  <p><a href="./run_migration_012.php" class="btn btn_01">마이그레이션 012 실행 (테이블 생성)</a></p>
</div>
<?php } else {
    $cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb}");
    $total_amt = sql_fetch("SELECT SUM(jtp_amount) AS s FROM {$tb}");
?>
<div class="tbl_head01 tbl_wrap">
  <table>
    <thead>
      <tr>
        <th scope="col">항목</th>
        <th scope="col">값</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>총 결제 건수</td><td><?php echo number_format($cnt['c'] ?? 0); ?>건</td></tr>
      <tr><td>총 결제 금액</td><td><?php echo number_format($total_amt['s'] ?? 0); ?>원</td></tr>
    </tbody>
  </table>
</div>
<p style="margin-top:12px;color:#888;font-size:13px;">옵션별 가격은 lib/ev_thumb_option.lib.php에서 일일 단가로 설정됩니다. (badge: 1,000원, motion: 1,000원, wave: 1,667원, border: 1,000원, premium: 1,667원)</p>
<?php } ?>

<?php
require_once G5_ADMIN_PATH.'/admin.tail.php';
?>
