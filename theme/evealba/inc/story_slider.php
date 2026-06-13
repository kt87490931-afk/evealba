<?php
/**
 * 스토리 원형 슬라이더 — Readdy형 (가로 스크롤, 화살표 없음)
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_story_udae = function_exists('get_jobs_by_type') ? get_jobs_by_type('우대', 8) : array();
$_story_premium = function_exists('get_jobs_by_type') ? get_jobs_by_type('프리미엄', 7) : array();
$_story_items = array_merge($_story_udae, $_story_premium);
if (empty($_story_items) && function_exists('get_jobs_feed_list')) {
    $_story_items = array_slice(get_jobs_feed_list(0, 20), 0, 15);
}

function _ev_story_thumb_html($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $thumb_file = isset($jr_data['thumb_file']) ? trim($jr_data['thumb_file']) : '';
    if ($thumb_file && defined('G5_DATA_URL')) {
        $url = G5_DATA_URL . '/jobs/' . $thumb_file;
        return '<img src="' . htmlspecialchars($url) . '" alt="" loading="lazy">';
    }
    if (function_exists('_jlh_feed_placeholder_img')) {
        $url = _jlh_feed_placeholder_img((int)$row['jr_id'], 132, 132);
        return '<img src="' . htmlspecialchars($url) . '" alt="" loading="lazy">';
    }
    $nick = $row['jr_nickname'] ?: ($row['jr_company'] ?: '?');
    return htmlspecialchars(mb_substr($nick, 0, 1, 'UTF-8'));
}
?>
<?php if (!empty($_story_items)) { ?>
<div class="story-slider-wrap">
  <div class="story-slider" tabindex="0">
<?php foreach ($_story_items as $_st) {
    $_st_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_st) : ((defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . (int)$_st['jr_id']);
    $_st_nick = $_st['jr_nickname'] ?: ($_st['jr_company'] ?: '업소');
?>
    <a href="<?php echo htmlspecialchars($_st_link); ?>" class="story-item">
      <div class="story-ring">
        <div class="story-ring-inner"><?php echo _ev_story_thumb_html($_st); ?></div>
      </div>
      <span class="story-name"><?php echo htmlspecialchars(mb_substr($_st_nick, 0, 8, 'UTF-8')); ?></span>
    </a>
<?php } ?>
  </div>
</div>
<?php } ?>
