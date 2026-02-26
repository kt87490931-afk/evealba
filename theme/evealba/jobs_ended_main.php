<?php if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_register_url = $jobs_base_url ? $jobs_base_url.'/jobs_register.php' : '/jobs_register.php';
$jobs_view_url_base = $jobs_base_url ? $jobs_base_url.'/jobs_view.php' : '/jobs_view.php';

$list = array();
$total_count = 0;
if ($is_member) {
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if ($tb_check && sql_num_rows($tb_check)) {
        $mb_id_esc = addslashes($member['mb_id']);
        $today = date('Y-m-d');
        $sql = "SELECT * FROM g5_jobs_register WHERE mb_id = '{$mb_id_esc}' AND (jr_status = 'ended' OR (jr_end_date IS NOT NULL AND jr_end_date < '{$today}')) ORDER BY jr_datetime DESC";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) {
            $list[] = array(
                'jr_id' => $row['jr_id'],
                'subject' => $row['jr_subject_display'] ?: '[제목없음]',
                'datetime2' => date('Y-m-d', strtotime($row['jr_datetime'])),
                'ad_period' => $row['jr_ad_period'] ? $row['jr_ad_period'].'일' : '—',
                'view_href' => $jobs_view_url_base.'?jr_id='.$row['jr_id']
            );
        }
        $total_count = count($list);
    }
}
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div class="page-title-bar">
  <h2 class="page-title">📁 마감된 채용정보</h2>
</div>

<div id="bo_list" class="ev-board-list jobs-ongoing-list jobs-ended-list" style="width:100%;">
  <div class="board-topbar">
    <div class="board-topbar-left">
      <h2 class="board-page-title">마감된 채용정보</h2>
      <span class="board-count">총 <strong><?php echo number_format($total_count); ?></strong>건</span>
    </div>
    <div class="board-btns">
      <a href="<?php echo $jobs_register_url; ?>" class="btn-write">✏️ 채용공고 등록</a>
    </div>
  </div>

  <div class="board-wrap jobs-ongoing-wrap">
    <div class="board-thead jobs-ongoing-thead">
      <div class="board-th">날짜</div>
      <div class="board-th td-title">제목</div>
      <div class="board-th">광고기간</div>
      <div class="board-th">보기</div>
    </div>
    <?php if (count($list) > 0) {
      foreach ($list as $row) { ?>
    <a href="<?php echo $row['view_href']; ?>" class="board-row jobs-ongoing-row">
      <div class="board-td td-date"><?php echo htmlspecialchars($row['datetime2']); ?></div>
      <div class="board-td td-title"><div class="td-title-inner"><span class="post-title-text"><?php echo htmlspecialchars($row['subject']); ?></span></div></div>
      <div class="board-td td-period"><?php echo htmlspecialchars($row['ad_period']); ?></div>
      <div class="board-td">보기</div>
    </a>
    <?php }
    } else { ?>
    <div class="board-row empty-row">
      <div class="board-td" style="grid-column:1/-1;text-align:center;padding:50px 20px;">
        <p style="font-size:15px;color:#888;">마감된 채용정보가 없습니다.</p>
        <a href="<?php echo $jobs_register_url; ?>" class="btn-write" style="margin-top:16px;display:inline-flex;">✏️ 채용공고 등록</a>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
