<?php if (!defined('_GNUBOARD_')) exit;

$jr_id = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0;
if (!$jr_id || !$is_member) {
    echo '<script>alert("잘못된 접근입니다."); history.back();</script>';
    return;
}

$jr_table = 'g5_jobs_register';
$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!sql_num_rows($tb_check)) {
    echo '<script>alert("데이터를 찾을 수 없습니다."); history.back();</script>';
    return;
}

$mb_id_esc = addslashes($member['mb_id']);
$row = sql_fetch("SELECT * FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    echo '<script>alert("권한이 없거나 데이터가 없습니다."); history.back();</script>';
    return;
}

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_ongoing_url = $jobs_base_url ? $jobs_base_url.'/jobs_ongoing.php' : '/jobs_ongoing.php';

$status = $row['jr_status'];
$status_label = ($status === 'pending') ? '입금대기중' : '진행중';

$data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
$nick = isset($data['job_nickname']) ? $data['job_nickname'] : $row['jr_nickname'];
$comp = isset($data['job_company']) ? $data['job_company'] : $row['jr_company'];
$title = isset($data['job_title']) ? $data['job_title'] : $row['jr_title'];
$desc_location = isset($data['desc_location']) ? $data['desc_location'] : '';
$desc_env = isset($data['desc_env']) ? $data['desc_env'] : '';
$desc_benefit = isset($data['desc_benefit']) ? $data['desc_benefit'] : '';
$desc_qualify = isset($data['desc_qualify']) ? $data['desc_qualify'] : '';
$desc_extra = isset($data['desc_extra']) ? $data['desc_extra'] : '';
$ai_summary = ''; // TODO: AI 생성 소개글 (진행중일 때만)
?>
<div class="page-title-bar">
  <h2 class="page-title"><?php echo htmlspecialchars($row['jr_subject_display']); ?></h2>
  <span class="status-badge status-<?php echo $status; ?>"><?php echo $status_label; ?></span>
</div>

<div class="jobs-view-wrap">
  <div class="form-card" style="margin-bottom:16px;">
    <div class="sec-head open">
      <span class="sec-head-icon">📋</span>
      <span class="sec-head-title">AI업소소개글용 종합정리</span>
    </div>
    <div class="sec-body">
      <div class="aip-row"><div class="aip-label">🏢 닉네임 · 상호</div><div class="aip-value"><?php echo htmlspecialchars($nick ?: $comp ?: '—'); ?></div></div>
      <div class="aip-row"><div class="aip-label">📋 채용제목</div><div class="aip-value"><?php echo htmlspecialchars($title ?: '—'); ?></div></div>
      <?php if ($desc_location) { ?><div class="aip-row"><div class="aip-label">📍 업소 위치 및 업소 소개</div><div class="aip-value"><?php echo nl2br(htmlspecialchars($desc_location)); ?></div></div><?php } ?>
      <?php if ($desc_env) { ?><div class="aip-row"><div class="aip-label">🪑 근무환경</div><div class="aip-value"><?php echo nl2br(htmlspecialchars($desc_env)); ?></div></div><?php } ?>
      <?php if ($desc_benefit) { ?><div class="aip-row"><div class="aip-label">💰 지원 혜택 및 복리후생</div><div class="aip-value"><?php echo nl2br(htmlspecialchars($desc_benefit)); ?></div></div><?php } ?>
      <?php if ($desc_qualify) { ?><div class="aip-row"><div class="aip-label">📋 지원 자격 및 우대사항</div><div class="aip-value"><?php echo nl2br(htmlspecialchars($desc_qualify)); ?></div></div><?php } ?>
      <?php if ($desc_extra) { ?><div class="aip-row"><div class="aip-label">📝 추가 상세설명</div><div class="aip-value"><?php echo nl2br(htmlspecialchars($desc_extra)); ?></div></div><?php } ?>
    </div>
  </div>

  <?php if ($status === 'ongoing' && $ai_summary) { ?>
  <div class="form-card" style="margin-bottom:16px;">
    <div class="sec-head open">
      <span class="sec-head-icon">🤖</span>
      <span class="sec-head-title">AI 소개글</span>
    </div>
    <div class="sec-body"><?php echo nl2br(htmlspecialchars($ai_summary)); ?></div>
  </div>
  <?php } ?>

  <div style="text-align:center;margin:20px 0;">
    <a href="<?php echo $jobs_ongoing_url; ?>" class="btn-list">📋 목록으로</a>
  </div>
</div>
