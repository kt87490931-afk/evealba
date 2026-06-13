<?php
/**
 * 리뉴얼 피드 — 등급 순서 유지 + 뷰 전환
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_feed_merged = array();
$_feed_types = array(
    array('type' => '우대', 'limit' => 0),
    array('type' => '프리미엄', 'limit' => 0),
    array('type' => '스페셜', 'limit' => 0),
    array('type' => '급구', 'limit' => 10),
    array('type' => '추천', 'limit' => 10),
    array('type' => '줄광고', 'limit' => 20),
);
foreach ($_feed_types as $_ft) {
    if (!function_exists('get_jobs_by_type')) break;
    $_ft_rows = get_jobs_by_type($_ft['type'], $_ft['limit']);
    foreach ($_ft_rows as $_ftr) {
        $_feed_merged[] = $_ftr;
    }
}
?>
<div class="feed-header">
  <h2>📋 채용정보</h2>
  <div class="view-toggle" role="tablist">
    <button type="button" class="view-btn active" data-view="feed" role="tab">피드</button>
    <button type="button" class="view-btn" data-view="list" role="tab">리스트</button>
    <button type="button" class="view-btn" data-view="grid" role="tab">그리드</button>
  </div>
</div>

<div class="recruit-container view-feed" id="recruitContainer">
<?php
if (!empty($_feed_merged) && function_exists('render_job_card_feed')) {
    foreach ($_feed_merged as $_feed_row) {
        render_job_card_feed($_feed_row);
    }
} elseif (!empty($_feed_merged) && function_exists('render_job_card')) {
    foreach ($_feed_merged as $_feed_row) {
        echo '<div class="renewal-feed-card">';
        render_job_card($_feed_row);
        echo '</div>';
    }
} else {
    echo '<p style="padding:24px;text-align:center;color:#999;">등록된 채용정보가 없습니다.</p>';
}
?>
</div>
