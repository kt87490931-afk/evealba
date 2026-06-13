<?php
/**
 * Readdy형 인스타 피드 — 스토리 아래 채용 카드 타임라인
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_feed_list')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_feed_merged = function_exists('get_jobs_feed_list') ? get_jobs_feed_list(0, 50) : array();
$_feed_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_feed_regions = isset($ev_regions) ? $ev_regions : array();
$_feed_jobs = isset($ev_jobs) ? $ev_jobs : array();
if (empty($_feed_regions) && file_exists(G5_LIB_PATH . '/ev_master.lib.php')) {
    include_once G5_LIB_PATH . '/ev_master.lib.php';
    $_feed_regions = ev_get_regions();
    $_feed_jobs = ev_get_jobs();
}
?>
<div class="renewal-feed-zone">
  <div class="feed-filter-bar">
    <h3 class="feed-zone-title">채용정보</h3>
    <form method="get" action="<?php echo $_feed_base; ?>/jobs.php" class="feed-filter-form">
      <select name="er_id" aria-label="지역">
        <option value="">지역 전체</option>
<?php foreach ($_feed_regions as $_fr) { ?>
        <option value="<?php echo (int)$_fr['er_id']; ?>"><?php echo htmlspecialchars($_fr['er_name']); ?></option>
<?php } ?>
      </select>
      <select name="ej_id" aria-label="직종">
        <option value="">직종 전체</option>
<?php foreach ($_feed_jobs as $_fj) { ?>
        <option value="<?php echo (int)$_fj['ej_id']; ?>"><?php echo htmlspecialchars($_fj['ej_name']); ?></option>
<?php } ?>
      </select>
    </form>
    <div class="view-toggle" role="tablist">
      <button type="button" class="view-btn" data-view="list" role="tab">리스트</button>
      <button type="button" class="view-btn active" data-view="feed" role="tab">피드</button>
      <button type="button" class="view-btn" data-view="grid" role="tab">그리드</button>
    </div>
  </div>

  <div class="recruit-container view-feed" id="recruitContainer">
<?php
if (!empty($_feed_merged) && function_exists('render_job_card_feed')) {
    foreach ($_feed_merged as $_feed_row) {
        render_job_card_feed($_feed_row);
    }
} else {
    echo '<div class="feed-empty"><p>등록된 채용정보가 없습니다.</p><a href="' . htmlspecialchars($_feed_base . '/jobs_register.php') . '">채용공고 등록하기</a></div>';
}
?>
  </div>
</div>
