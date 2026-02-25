<?php if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_register_url = $jobs_base_url ? $jobs_base_url.'/jobs_register.php' : '/jobs_register.php';

$list = array();
$total_count = 0;
if ($is_member) {
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if (sql_num_rows($tb_check)) {
        $mb_id_esc = addslashes($member['mb_id']);
        $sql = "SELECT jr_id, jr_datetime, jr_total_amount, jr_subject_display, jr_status, jr_ad_period FROM g5_jobs_register WHERE mb_id = '{$mb_id_esc}' ORDER BY jr_datetime DESC";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) {
            $st = $row['jr_status'];
            $status_label = ($st === 'pending') ? '입금대기중' : (($st === 'ongoing') ? '진행중' : '마감');
            $list[] = array(
                'jr_id' => $row['jr_id'],
                'datetime2' => date('Y-m-d H:i', strtotime($row['jr_datetime'])),
                'total_amount' => $row['jr_total_amount'],
                'subject' => $row['jr_subject_display'] ?: '[제목없음]',
                'status_label' => $status_label,
                'ad_period' => $row['jr_ad_period'] ? $row['jr_ad_period'].'일' : '—'
            );
        }
        $total_count = count($list);
    }
}
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div class="page-title-bar">
  <h2 class="page-title">💳 유료결제 내역</h2>
</div>

<div id="bo_list" class="ev-board-list jobs-payment-list" style="width:100%;">
  <div class="board-topbar">
    <div class="board-topbar-left">
      <h2 class="board-page-title">유료결제 내역</h2>
      <span class="board-count">총 <strong><?php echo number_format($total_count); ?></strong>건</span>
    </div>
    <div class="board-btns">
      <a href="<?php echo $jobs_register_url; ?>" class="btn-write">✏️ 채용공고 등록</a>
    </div>
  </div>

  <div class="board-wrap jobs-ongoing-wrap">
    <div class="board-thead jobs-ongoing-thead">
      <div class="board-th">결제일시</div>
      <div class="board-th td-title">채용정보</div>
      <div class="board-th">금액</div>
      <div class="board-th">기간</div>
      <div class="board-th">상태</div>
    </div>
    <?php if (count($list) > 0) {
      foreach ($list as $row) { ?>
    <div class="board-row jobs-ongoing-row" style="cursor:default;">
      <div class="board-td td-date"><?php echo htmlspecialchars($row['datetime2']); ?></div>
      <div class="board-td td-title"><span class="post-title-text"><?php echo htmlspecialchars($row['subject']); ?></span></div>
      <div class="board-td td-price"><?php echo number_format($row['total_amount']); ?>원</div>
      <div class="board-td"><?php echo htmlspecialchars($row['ad_period']); ?></div>
      <div class="board-td td-status"><span class="status-badge"><?php echo htmlspecialchars($row['status_label']); ?></span></div>
    </div>
    <?php }
    } else { ?>
    <div class="board-row empty-row">
      <div class="board-td" style="grid-column:1/-1;text-align:center;padding:50px 20px;">
        <p style="font-size:15px;color:#888;">결제 내역이 없습니다.</p>
        <a href="<?php echo $jobs_register_url; ?>" class="btn-write" style="margin-top:16px;display:inline-flex;">✏️ 채용공고 등록</a>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
