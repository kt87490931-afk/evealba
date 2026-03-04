<?php
/**
 * 채용정보 스크랩 목록 (이력서 MYPAGE - 채용정보 스크랩)
 */
include_once('./_common.php');

define('_JOBS_SCRAP_', true);
define('_JOBS_', true);
if (!defined('_GNUBOARD_')) exit;

if (!$is_member) {
    goto_url(G5_BBS_URL.'/login.php?url='.urlencode(G5_URL.'/jobs_scrap_list.php'));
}

if (defined('G5_THEME_PATH')) {
    $jobs_mypage_active = 'scrap';
    $resume_mypage_active = 'scrap';
    $jobs_breadcrumb_current = '📋 채용정보 스크랩';
    $g5['title'] = '채용정보 스크랩 - '.$config['cf_title'];
    include_once(G5_THEME_PATH.'/head_resume_register.php');
?>

<?php
$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$mb_id_esc = addslashes($member['mb_id']);
$list = array();
$tb = 'g5_jobs_scrap';
$tb_check = sql_query("SHOW TABLES LIKE '{$tb}'", false);
if ($tb_check && sql_num_rows($tb_check)) {
    $r = sql_query("SELECT s.js_id, s.jr_id, s.js_datetime, j.jr_subject_display, j.jr_status, j.jr_end_date, j.mb_id as jr_mb_id
        FROM {$tb} s
        LEFT JOIN g5_jobs_register j ON s.jr_id = j.jr_id
        WHERE s.mb_id = '{$mb_id_esc}'
        ORDER BY s.js_datetime DESC");
    while ($row = sql_fetch_array($r)) {
        if ($row['jr_id']) {
            $st = $row['jr_status'] ?? '';
            $status_label = ($st === 'pending') ? '입금대기' : (($st === 'ongoing') ? '진행중' : '마감');
            $list[] = array(
                'js_id' => $row['js_id'],
                'jr_id' => $row['jr_id'],
                'datetime2' => date('Y-m-d H:i', strtotime($row['js_datetime'])),
                'subject' => $row['jr_subject_display'] ?: ('#'.$row['jr_id']),
                'status_label' => $status_label,
                'view_url' => $jobs_base_url.'/jobs_view.php?jr_id='.$row['jr_id']
            );
        }
    }
}
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div class="page-title-bar">
  <h2 class="page-title">📋 채용정보 스크랩</h2>
</div>

<div id="bo_list" class="ev-board-list jobs-scrap-list" style="width:100%;">
  <div class="board-topbar">
    <div class="board-topbar-left">
      <h2 class="board-page-title">채용정보 스크랩</h2>
      <span class="board-count">총 <strong><?php echo number_format(count($list)); ?></strong>건</span>
    </div>
    <div class="board-btns">
      <a href="<?php echo $jobs_base_url ? $jobs_base_url.'/jobs.php' : '/jobs.php'; ?>" class="btn-write">📋 채용정보 보기</a>
    </div>
  </div>

  <div class="board-wrap jobs-ongoing-wrap">
    <div class="board-thead jobs-ongoing-thead">
      <div class="board-th">스크랩일시</div>
      <div class="board-th td-title">채용정보</div>
      <div class="board-th">상태</div>
      <div class="board-th">관리</div>
    </div>
    <?php if (count($list) > 0) {
      foreach ($list as $row) { ?>
    <div class="board-row jobs-ongoing-row" style="cursor:default;">
      <div class="board-td td-date"><?php echo htmlspecialchars($row['datetime2']); ?></div>
      <div class="board-td td-title">
        <a href="<?php echo htmlspecialchars($row['view_url']); ?>" class="post-title-text" style="text-decoration:none;color:inherit;"><?php echo htmlspecialchars($row['subject']); ?></a>
      </div>
      <div class="board-td td-status"><span class="status-badge"><?php echo htmlspecialchars($row['status_label']); ?></span></div>
      <div class="board-td">
        <a href="<?php echo htmlspecialchars($row['view_url']); ?>" class="btn-write" style="padding:6px 14px;font-size:12px;">보기</a>
      </div>
    </div>
    <?php }
    } else { ?>
    <div class="board-row empty-row">
      <div class="board-td" style="grid-column:1/-1;text-align:center;padding:50px 20px;">
        <p style="font-size:15px;color:#888;">스크랩한 채용정보가 없습니다.</p>
        <p style="font-size:13px;color:#aaa;margin-top:8px;">채용정보 상세 페이지에서 ⭐스크랩 버튼을 눌러 저장하세요.</p>
        <a href="<?php echo $jobs_base_url ? $jobs_base_url.'/jobs.php' : '/jobs.php'; ?>" class="btn-write" style="margin-top:16px;display:inline-flex;">📋 채용정보 보기</a>
      </div>
    </div>
    <?php } ?>
  </div>
</div>

<?php
    include_once(G5_THEME_PATH.'/tail.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>채용정보 스크랩 페이지입니다. 테마를 적용해 주세요.</p>
<?php include_once(G5_PATH.'/tail.php'); ?>
