<?php
/**
 * 우측 패널 — 검색·추천·알림·채팅 (float_banners 쿼리 재사용)
 */
if (!defined('_GNUBOARD_')) exit;

if (!defined('_PANEL_RIGHT_DONE_')) define('_PANEL_RIGHT_DONE_', true);

$_pr_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_pr_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';
$_pr_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_pr_rows = array();
$_pr_tb_check = sql_query("SHOW TABLES LIKE '{$_pr_sb_table}'");
if ($_pr_tb_check && sql_num_rows($_pr_tb_check) > 0) {
    $_pr_res = sql_query("SELECT jr.*
        FROM {$_pr_sb_table} sb
        LEFT JOIN {$_pr_jr_table} jr ON sb.sb_jr_id = jr.jr_id
        WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
        ORDER BY sb.sb_position ASC LIMIT 5");
    while ($_pr_r = sql_fetch_array($_pr_res)) {
        if (!empty($_pr_r['jr_id'])) $_pr_rows[] = $_pr_r;
    }
}

if (!function_exists('render_premium_card') && is_file(G5_PATH . '/extend/jobs_list_helper.php')) {
    include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_pr_memo_badge = 0;
if (!empty($is_member) && !empty($member['mb_id']) && function_exists('get_memo_not_read')) {
    $_pr_memo_badge = (int)get_memo_not_read($member['mb_id']);
}

$_pr_regions = isset($ev_regions) ? $ev_regions : array();
$_pr_jobs = isset($ev_jobs) ? $ev_jobs : array();
if (empty($_pr_regions) && file_exists(G5_LIB_PATH . '/ev_master.lib.php')) {
    include_once G5_LIB_PATH . '/ev_master.lib.php';
    $_pr_regions = ev_get_regions();
    $_pr_jobs = ev_get_jobs();
}
?>
<aside class="panel-right" aria-label="우측 패널">
  <div class="panel-section">
    <h4>채용정보</h4>
    <form method="get" action="<?php echo $_pr_base; ?>/jobs.php">
      <select name="er_id" aria-label="지역">
        <option value="">지역 전체</option>
<?php foreach ($_pr_regions as $_pr) { ?>
        <option value="<?php echo (int)$_pr['er_id']; ?>"><?php echo htmlspecialchars($_pr['er_name']); ?></option>
<?php } ?>
      </select>
      <select name="ej_id" aria-label="직종">
        <option value="">직종 전체</option>
<?php foreach ($_pr_jobs as $_pj) { ?>
        <option value="<?php echo (int)$_pj['ej_id']; ?>"><?php echo htmlspecialchars($_pj['ej_name']); ?></option>
<?php } ?>
      </select>
      <input type="text" name="stx" placeholder="🔍 채용정보 검색" maxlength="50">
    </form>
  </div>

  <div class="panel-section">
    <h4>추천 구인</h4>
<?php if (!empty($_pr_rows)) {
    foreach ($_pr_rows as $_pr_row) {
        $_pr_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_pr_row) : $_pr_base . '/jobs_view.php?jr_id=' . (int)$_pr_row['jr_id'];
        $_pr_name = $_pr_row['jr_nickname'] ?: ($_pr_row['jr_company'] ?: '업소');
        $_pr_title = $_pr_row['jr_title'] ?: '';
        $_pr_jd = is_string($_pr_row['jr_data']) ? json_decode($_pr_row['jr_data'], true) : (array)$_pr_row['jr_data'];
        $_pr_thumb = isset($_pr_jd['thumb_file']) ? trim($_pr_jd['thumb_file']) : '';
        if ($_pr_thumb && defined('G5_DATA_URL')) {
            $_pr_img = G5_DATA_URL . '/jobs/' . $_pr_thumb;
        } elseif (function_exists('_jlh_feed_placeholder_img')) {
            $_pr_img = _jlh_feed_placeholder_img((int)$_pr_row['jr_id'], 96, 96);
        } else {
            $_pr_img = '';
        }
?>
    <a href="<?php echo htmlspecialchars($_pr_link); ?>" class="panel-recommend-item">
      <div class="panel-recommend-thumb"><?php if ($_pr_img) { ?><img src="<?php echo htmlspecialchars($_pr_img); ?>" alt="" loading="lazy"><?php } else { echo htmlspecialchars(mb_substr($_pr_name, 0, 2, 'UTF-8')); } ?></div>
      <div class="panel-recommend-info">
        <div class="panel-recommend-name"><?php echo htmlspecialchars(mb_substr($_pr_name, 0, 14, 'UTF-8')); ?></div>
        <div class="panel-recommend-meta"><?php echo htmlspecialchars(mb_substr($_pr_title, 0, 24, 'UTF-8')); ?></div>
      </div>
    </a>
<?php }
} else { ?>
    <p class="panel-notice-empty">등록된 추천 구인이 없습니다.</p>
<?php } ?>
  </div>

  <div class="panel-section">
    <h4>새로운 알림</h4>
<?php if ($is_member) { ?>
    <p style="font-size:12px;color:#555;">
      읽지 않은 쪽지
      <?php if ($_pr_memo_badge > 0) { ?><strong style="color:var(--pink-main);"><?php echo $_pr_memo_badge; ?></strong>건<?php } else { ?>없음<?php } ?>
    </p>
    <a href="<?php echo $_pr_base; ?>/memo_full.php" class="panel-chat-btn" style="margin-top:8px;text-align:center;text-decoration:none;">쪽지함 열기</a>
<?php } else { ?>
    <p class="panel-notice-empty">로그인 후 확인해보세요.</p>
<?php } ?>
  </div>

  <div class="panel-section">
    <h4>새로운 1:1 채팅</h4>
<?php if ($is_member) { ?>
    <button type="button" class="panel-chat-btn" onclick="if(typeof toggleEveChat==='function')toggleEveChat();else{var u='<?php echo G5_PLUGIN_URL; ?>/chat/eve_chat_frame.php';window.open(u,'eveChatPopup','width=420,height=720');}">채팅 열기</button>
<?php } else { ?>
    <p class="panel-notice-empty">로그인 후 확인해보세요.</p>
<?php } ?>
  </div>
</aside>
