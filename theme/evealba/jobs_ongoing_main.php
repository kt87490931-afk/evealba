<?php if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_register_url = $jobs_base_url ? $jobs_base_url.'/jobs_register.php' : '/jobs_register.php';
$jobs_extend_popup_url = $jobs_base_url ? $jobs_base_url.'/jobs_extend_popup.php' : '/jobs_extend_popup.php';
$jobs_view_url_base = $jobs_base_url ? $jobs_base_url.'/jobs_view.php' : '/jobs_view.php';

$list = array();
$total_count = 0;
if ($is_member) {
    $jr_table = 'g5_jobs_register';
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if (sql_num_rows($tb_check)) {
        $mb_id_esc = addslashes($member['mb_id']);
        $sql = "SELECT * FROM `g5_jobs_register` WHERE mb_id = '{$mb_id_esc}' AND jr_status IN ('pending','ongoing') ORDER BY jr_datetime DESC";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) {
            $status = $row['jr_status'];
            $status_label = ($status === 'pending') ? '입금대기중' : '진행중';
            $list[] = array(
                'jr_id' => $row['jr_id'],
                'wr_id' => $row['jr_id'],
                'subject' => $row['jr_subject_display'] ?: '[제목없음]',
                'datetime2' => date('Y-m-d', strtotime($row['jr_datetime'])),
                'status' => $status,
                'status_label' => $status_label,
                'ad_period' => $row['jr_ad_period'] ? $row['jr_ad_period'].'일' : '—',
                'jump_count' => $row['jr_jump_count'],
                'view_href' => $jobs_view_url_base.'?jr_id='.$row['jr_id']
            );
        }
        $total_count = count($list);
    }
}
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div class="page-title-bar">
  <h2 class="page-title">📋 진행중인 채용정보</h2>
</div>

<div id="bo_list" class="ev-board-list jobs-ongoing-list" style="width:100%;">

  <div class="board-topbar">
    <div class="board-topbar-left">
      <h2 class="board-page-title">진행중인 채용정보</h2>
      <span class="board-count">총 <strong><?php echo number_format($total_count); ?></strong>건</span>
    </div>
    <div class="board-btns">
      <a href="<?php echo $jobs_register_url; ?>" class="btn-write">✏️ 채용공고 등록</a>
      <a href="<?php echo $jobs_register_url; ?>" class="btn-list">📋 채용정보등록</a>
    </div>
  </div>

  <div class="board-wrap jobs-ongoing-wrap">
    <div class="board-thead jobs-ongoing-thead">
      <div class="board-th">날짜</div>
      <div class="board-th td-title">제목</div>
      <div class="board-th">상태</div>
      <div class="board-th">광고기간</div>
      <div class="board-th">점프횟수</div>
      <div class="board-th">연장</div>
    </div>

    <?php if (count($list) > 0) {
      foreach ($list as $row) {
        $extend_url = $jobs_extend_popup_url . '?jr_id=' . (isset($row['jr_id']) ? $row['jr_id'] : '');
    ?>
    <a href="<?php echo isset($row['view_href']) ? $row['view_href'] : '#'; ?>" class="board-row jobs-ongoing-row">
      <div class="board-td td-date"><?php echo isset($row['datetime2']) ? $row['datetime2'] : ''; ?></div>
      <div class="board-td td-title">
        <div class="td-title-inner">
          <span class="post-title-text"><?php echo isset($row['subject']) ? htmlspecialchars($row['subject']) : ''; ?></span>
        </div>
      </div>
      <div class="board-td td-status">
        <span class="status-badge status-<?php echo isset($row['status']) ? $row['status'] : 'pending'; ?>"><?php echo isset($row['status_label']) ? $row['status_label'] : ''; ?></span>
      </div>
      <div class="board-td td-period"><?php echo isset($row['ad_period']) ? $row['ad_period'] : '—'; ?></div>
      <div class="board-td td-jump"><?php echo isset($row['jump_count']) ? number_format($row['jump_count']) : '—'; ?></div>
      <div class="board-td td-extend">
        <button type="button" class="btn-extend" onclick="event.preventDefault();event.stopPropagation();openExtendPopup('<?php echo $extend_url; ?>');">연장</button>
      </div>
    </a>
    <?php }
    } ?>

    <?php if (count($list) == 0) { ?>
    <div class="board-row empty-row">
      <div class="board-td" style="grid-column:1/-1;text-align:center;padding:50px 20px;">
        <p style="font-size:15px;color:#888;margin-bottom:8px;">등록된 진행중인 채용정보가 없습니다.</p>
        <p style="font-size:13px;color:#aaa;">채용공고를 등록하고 결제하시면 여기에 표시됩니다.</p>
        <a href="<?php echo $jobs_register_url; ?>" class="btn-write" style="margin-top:16px;display:inline-flex;">✏️ 채용공고 등록하기</a>
      </div>
    </div>
    <?php } ?>
  </div>

  <div class="board-bottom">
    <a href="<?php echo $jobs_register_url; ?>" class="btn-write">✏️ 채용공고 등록</a>
    <a href="<?php echo $jobs_register_url; ?>" class="btn-list">📋 채용정보등록</a>
  </div>

</div>

<!-- 연장 팝업 (광고유료결제 섹션) -->
<div id="extendModal" class="jobs-extend-modal" style="display:none;">
  <div class="extend-modal-overlay" onclick="closeExtendModal()"></div>
  <div class="extend-modal-content">
    <div class="extend-modal-header">
      <h3>광고 연장</h3>
      <button type="button" class="extend-modal-close" onclick="closeExtendModal()" aria-label="닫기">×</button>
    </div>
    <div class="extend-modal-body">
      <iframe id="extendIframe" src="about:blank" frameborder="0" style="width:100%;min-height:500px;border:none;"></iframe>
    </div>
  </div>
</div>

<script>
function openExtendPopup(url) {
  var modal = document.getElementById('extendModal');
  var iframe = document.getElementById('extendIframe');
  if (modal && iframe) {
    iframe.src = url;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}
function closeExtendModal() {
  var modal = document.getElementById('extendModal');
  var iframe = document.getElementById('extendIframe');
  if (modal) modal.style.display = 'none';
  if (iframe) iframe.src = 'about:blank';
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeExtendModal();
});
</script>
