<?php
/**
 * 수다방 우측 패널 — 채용검색 + 커뮤니티 인기글 + 추천구인
 */
if (!defined('_GNUBOARD_')) exit;
if (defined('_PANEL_RIGHT_DONE_')) return;
define('_PANEL_RIGHT_DONE_', true);

$_ps_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$write_prefix = $g5['write_prefix'];
$bbs_url = G5_BBS_URL;

$_ps_hot = array();
foreach (array('night' => '밤문화', 'law' => '법률', 'couple' => '단짝') as $_bt => $_bl) {
    $_tb = @sql_query("SHOW TABLES LIKE '{$write_prefix}{$_bt}'", false);
    if (!$_tb || !sql_num_rows($_tb)) continue;
    $_rows = array();
    $_res = @sql_query("SELECT wr_id, wr_subject, wr_good FROM {$write_prefix}{$_bt} WHERE wr_is_comment=0 ORDER BY wr_good DESC, wr_hit DESC LIMIT 3", false);
    if ($_res) {
        while ($_r = sql_fetch_array($_res)) {
            $_r['bo_table'] = $_bt;
            $_r['board_label'] = $_bl;
            $_rows[] = $_r;
        }
    }
    $_ps_hot = array_merge($_ps_hot, $_rows);
}
usort($_ps_hot, function ($a, $b) { return (int)$b['wr_good'] - (int)$a['wr_good']; });
$_ps_hot = array_slice($_ps_hot, 0, 5);

$_pr_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';
$_pr_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_pr_rows = array();
$_pr_tb_check = sql_query("SHOW TABLES LIKE '{$_pr_sb_table}'", false);
if ($_pr_tb_check && sql_num_rows($_pr_tb_check) > 0) {
    $_pr_res = sql_query("SELECT jr.* FROM {$_pr_sb_table} sb LEFT JOIN {$_pr_jr_table} jr ON sb.sb_jr_id = jr.jr_id WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active' ORDER BY sb.sb_position ASC LIMIT 3", false);
    while ($_pr_r = sql_fetch_array($_pr_res)) {
        if (!empty($_pr_r['jr_id'])) $_pr_rows[] = $_pr_r;
    }
}
?>
<aside class="panel-right" aria-label="우측 패널">
  <div class="panel-card">
    <div class="panel-card-head">채용정보</div>
    <div class="search-panel">
      <form method="get" action="<?php echo $_ps_base; ?>/jobs.php">
        <div class="select-row">
          <select aria-label="지역" disabled><option>지역 전체</option></select>
          <select aria-label="직종" disabled><option>직종 전체</option></select>
        </div>
        <div class="search-input-row">
          <input type="text" name="stx" placeholder="🔍 채용정보 검색">
          <button type="submit">검색</button>
        </div>
      </form>
    </div>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">🔥 커뮤니티 인기글</div>
    <div class="panel-hot-list">
<?php if (empty($_ps_hot)) { ?>
      <p class="panel-empty">인기글이 없습니다.</p>
<?php } else {
    foreach ($_ps_hot as $_hi => $_hp) {
        $_hrank = $_hi + 1;
        $_href = $bbs_url . '/board.php?bo_table=' . $_hp['bo_table'] . '&wr_id=' . (int)$_hp['wr_id'];
?>
      <a class="panel-hot-item" href="<?php echo $_href; ?>">
        <div class="panel-hot-rank<?php echo $_hrank <= 3 ? ' top3' : ''; ?>"><?php echo $_hrank; ?></div>
        <div class="panel-hot-text"><?php echo htmlspecialchars(mb_substr($_hp['wr_subject'], 0, 24, 'UTF-8')); ?></div>
        <div class="panel-hot-board"><?php echo htmlspecialchars($_hp['board_label']); ?></div>
      </a>
<?php }
} ?>
    </div>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">💖 추천 구인</div>
    <div class="recommend-list">
<?php if (!empty($_pr_rows)) {
    foreach ($_pr_rows as $_pr_row) {
        $_pr_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_pr_row) : $_ps_base . '/jobs_view.php?jr_id=' . (int)$_pr_row['jr_id'];
        $_pr_name = $_pr_row['jr_nickname'] ?: ($_pr_row['jr_company'] ?: '업소');
        $_pr_jd = is_string($_pr_row['jr_data']) ? json_decode($_pr_row['jr_data'], true) : (array)$_pr_row['jr_data'];
        $_pr_img = function_exists('_jlh_feed_placeholder_img') ? _jlh_feed_placeholder_img((int)$_pr_row['jr_id'], 100, 100) : '';
?>
      <div class="recommend-item" data-href="<?php echo htmlspecialchars($_pr_link, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="recommend-thumb"><img src="<?php echo htmlspecialchars($_pr_img); ?>" alt="" loading="lazy"></div>
        <div class="recommend-info">
          <div class="rec-name"><?php echo htmlspecialchars(mb_substr($_pr_name, 0, 16, 'UTF-8')); ?></div>
          <div class="rec-salary">급여협의</div>
          <div class="rec-loc">›</div>
        </div>
      </div>
<?php }
} else { ?>
      <p class="panel-empty">등록된 추천 구인이 없습니다.</p>
<?php } ?>
    </div>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">🔔 새로운 알림</div>
<?php if ($is_member) { ?>
    <p class="panel-empty">새 알림을 확인해보세요.</p>
    <a class="btn-panel-login" href="<?php echo $_ps_base; ?>/memo_full.php">쪽지함 열기</a>
<?php } else { ?>
    <p class="panel-empty">로그인 후 확인해보세요.</p>
    <a class="btn-panel-login" href="<?php echo G5_BBS_URL; ?>/login.php">로그인하기</a>
<?php } ?>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">💬 새로운 1:1 채팅</div>
<?php if ($is_member) { ?>
    <p class="panel-empty">채팅을 시작해보세요.</p>
    <a class="btn-panel-login" href="#" onclick="if(typeof toggleEveChat==='function'){toggleEveChat();}return false;">채팅 열기</a>
<?php } else { ?>
    <p class="panel-empty">로그인 후 확인해보세요.</p>
    <a class="btn-panel-login" href="<?php echo G5_BBS_URL; ?>/login.php">로그인하기</a>
<?php } ?>
  </div>
</aside>
