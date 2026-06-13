<?php
/**
 * 시안 스토리 슬라이더
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_story_udae = function_exists('get_jobs_by_type') ? get_jobs_by_type('우대', 5) : array();
$_story_premium = function_exists('get_jobs_by_type') ? get_jobs_by_type('프리미엄', 5) : array();
$_story_special = function_exists('get_jobs_by_type') ? get_jobs_by_type('스페셜', 3) : array();
$_story_urgent = function_exists('get_jobs_by_type') ? get_jobs_by_type('급구', 3) : array();
$_story_recomm = function_exists('get_jobs_by_type') ? get_jobs_by_type('추천', 4) : array();
$_story_items = array_merge($_story_udae, $_story_premium, $_story_special, $_story_urgent, $_story_recomm);
if (empty($_story_items) && function_exists('get_jobs_feed_list')) {
    $_story_items = array_slice(get_jobs_feed_list(0, 20), 0, 15);
}

function _ev_story_thumb_url($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $thumb_file = isset($jr_data['thumb_file']) ? trim($jr_data['thumb_file']) : '';
    if ($thumb_file && defined('G5_DATA_URL')) {
        return G5_DATA_URL . '/jobs/' . $thumb_file;
    }
    if (function_exists('_jlh_feed_placeholder_img')) {
        return _jlh_feed_placeholder_img((int)$row['jr_id'], 100, 100);
    }
    return '';
}

function _ev_story_ring_class($ad_labels) {
    if (function_exists('_jlh_grade_info')) {
        $g = _jlh_grade_info($ad_labels);
        return $g['ring_class'] ?: 'grade-recommend';
    }
    if (strpos($ad_labels, '우대') !== false) return 'grade-top';
    if (strpos($ad_labels, '프리미엄') !== false) return 'grade-premium';
    if (strpos($ad_labels, '스페셜') !== false) return 'grade-special';
    if (strpos($ad_labels, '급구') !== false) return 'grade-urgent';
    if (strpos($ad_labels, '추천') !== false) return 'grade-recommend';
    return 'grade-recommend';
}
?>
<?php if (!empty($_story_items)) { ?>
<div class="story-wrap">
  <div class="story-row" id="storyRow">
<?php foreach ($_story_items as $_st) {
    $_st_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_st) : ((defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . (int)$_st['jr_id']);
    $_st_nick = $_st['jr_nickname'] ?: ($_st['jr_company'] ?: '업소');
    $_st_thumb = _ev_story_thumb_url($_st);
    $_st_ring = _ev_story_ring_class($_st['jr_ad_labels'] ?? '');
?>
    <div class="story-item" data-href="<?php echo htmlspecialchars($_st_link, ENT_QUOTES, 'UTF-8'); ?>" role="link" tabindex="0">
      <div class="story-ring <?php echo htmlspecialchars($_st_ring); ?>">
        <img src="<?php echo htmlspecialchars($_st_thumb); ?>" alt="" loading="lazy">
      </div>
      <span class="story-name"><?php echo htmlspecialchars(mb_substr($_st_nick, 0, 8, 'UTF-8')); ?></span>
    </div>
<?php } ?>
  </div>
</div>
<?php } ?>
