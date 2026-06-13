<?php
/**
 * 시안 피드 — 뷰탭·통계·채용 카드
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_feed_list')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_feed_merged = function_exists('get_jobs_feed_list') ? get_jobs_feed_list(0, 50) : array();
$_feed_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';

$_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_stat_today_jobs = 3427;
$_stat_resumes = 12841;
$_stat_shops = 8920;
$_stat_visitors = 24153;
$_stat_matches = 1203;
if (sql_num_rows(sql_query("SHOW TABLES LIKE '{$_jr_table}'", false))) {
    $_st_r = sql_fetch("SELECT COUNT(*) AS cnt FROM {$_jr_table} WHERE DATE(jr_datetime) = CURDATE() AND jr_status = 'ongoing'");
    if ($_st_r && (int)$_st_r['cnt'] > 0) $_stat_today_jobs = (int)$_st_r['cnt'];
    $_st_all = sql_fetch("SELECT COUNT(*) AS cnt FROM {$_jr_table} WHERE jr_status = 'ongoing'");
    if ($_st_all && (int)$_st_all['cnt'] > 0) $_stat_shops = (int)$_st_all['cnt'];
}
?>
<div class="view-tabs" role="tablist">
  <button type="button" class="view-tab active" data-view="list" role="tab">≡ 리스트</button>
  <button type="button" class="view-tab" data-view="feed" role="tab">⊞ 피드</button>
  <button type="button" class="view-tab" data-view="grid" role="tab">⊟ 그리드</button>
</div>

<div class="stats-bar">
  <div class="stat-item">
    <div class="stat-label">오늘 채용공고</div>
    <div class="stat-val"><?php echo number_format($_stat_today_jobs); ?></div>
  </div>
  <div class="stat-item">
    <div class="stat-label">등록 이력서</div>
    <div class="stat-val"><?php echo number_format($_stat_resumes); ?></div>
  </div>
  <div class="stat-item">
    <div class="stat-label">가입 업소</div>
    <div class="stat-val"><?php echo number_format($_stat_shops); ?></div>
  </div>
  <div class="stat-item">
    <div class="stat-label">오늘 접속자</div>
    <div class="stat-val"><?php echo number_format($_stat_visitors); ?></div>
  </div>
  <div class="stat-item">
    <div class="stat-label">오늘 매칭</div>
    <div class="stat-val"><?php echo number_format($_stat_matches); ?></div>
  </div>
</div>

<div class="feed-container view-list" id="feedContainer">
<?php
if (!empty($_feed_merged) && function_exists('render_job_card_feed')) {
    foreach ($_feed_merged as $_feed_row) {
        render_job_card_feed($_feed_row);
    }
} else {
    echo '<div class="feed-empty" style="padding:40px;text-align:center;color:#888;"><p>등록된 채용정보가 없습니다.</p>'
        . '<a href="' . htmlspecialchars($_feed_base . '/jobs_register.php') . '" style="color:var(--pink);">채용공고 등록하기</a></div>';
}
?>
</div>
