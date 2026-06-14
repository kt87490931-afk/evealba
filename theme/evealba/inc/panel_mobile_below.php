<?php
/**
 * 모바일 — 피드 아래 추천·알림 (Readdy 하단 패널)
 */
if (!defined('_GNUBOARD_')) exit;

$_mb_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_mb_sb = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';
$_mb_jr = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_mb_rows = array();
$_mb_chk = sql_query("SHOW TABLES LIKE '{$_mb_sb}'", false);
if ($_mb_chk && sql_num_rows($_mb_chk) > 0) {
    $_mb_res = sql_query("SELECT jr.* FROM {$_mb_sb} sb
        INNER JOIN {$_mb_jr} jr ON sb.sb_jr_id = jr.jr_id
        WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
        ORDER BY sb.sb_position ASC LIMIT 4", false);
    if ($_mb_res) {
        while ($_mb_r = sql_fetch_array($_mb_res)) {
            $_mb_rows[] = $_mb_r;
        }
    }
}

$_mb_memo = 0;
if (!empty($is_member) && !empty($member['mb_id']) && function_exists('get_memo_not_read')) {
    $_mb_memo = (int)get_memo_not_read($member['mb_id']);
}
?>
<div class="renewal-mobile-panels">
  <section class="panel-section panel-mobile-block">
    <h4>추천 구인</h4>
<?php if (!empty($_mb_rows)) {
    foreach ($_mb_rows as $_mb_row) {
        $_mb_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_mb_row) : $_mb_base . '/jobs_view.php?jr_id=' . (int)$_mb_row['jr_id'];
        $_mb_name = $_mb_row['jr_nickname'] ?: ($_mb_row['jr_company'] ?: '업소');
        $_mb_jd = is_string($_mb_row['jr_data']) ? json_decode($_mb_row['jr_data'], true) : (array)$_mb_row['jr_data'];
        $_mb_wage = '';
        if (!empty($_mb_jd['job_salary_amt'])) {
            $_mb_wage = number_format((int)$_mb_jd['job_salary_amt']) . '원';
        }
        $_mb_reg = function_exists('_jlh_region_name') ? _jlh_region_name($_mb_jd['job_work_region_1'] ?? '') : '';
?>
    <a href="<?php echo htmlspecialchars($_mb_link); ?>" class="panel-recommend-item">
      <div class="panel-recommend-thumb"><?php echo htmlspecialchars(mb_substr($_mb_name, 0, 2, 'UTF-8')); ?></div>
      <div class="panel-recommend-info">
        <div class="panel-recommend-name"><?php echo htmlspecialchars(mb_substr($_mb_name, 0, 14, 'UTF-8')); ?></div>
        <div class="panel-recommend-meta"><?php echo $_mb_wage ? htmlspecialchars($_mb_wage) : '협의'; ?><?php if ($_mb_reg) { ?> · <?php echo htmlspecialchars($_mb_reg); ?><?php } ?></div>
      </div>
    </a>
<?php }
} else { ?>
    <p class="panel-notice-empty">로그인 후 추천 구인정보를 확인해보세요.</p>
<?php } ?>
  </section>

  <section class="panel-section panel-mobile-block">
    <h4>새로운 알림</h4>
<?php if ($is_member) { ?>
    <p class="panel-notice-text">읽지 않은 쪽지 <?php echo $_mb_memo > 0 ? '<strong>' . $_mb_memo . '건</strong>' : '없음'; ?></p>
    <a href="<?php echo $_mb_base; ?>/memo_full.php" class="panel-chat-btn">알림 &amp; 채팅 열기</a>
<?php } else { ?>
    <p class="panel-notice-empty">로그인 후 확인해보세요.</p>
<?php } ?>
  </section>

  <section class="panel-section panel-mobile-block">
    <h4>새로운 1:1 채팅</h4>
<?php if ($is_member) { ?>
    <a href="<?php echo $_mb_base; ?>/memo_full.php?tab=chat" class="panel-chat-btn">1:1 채팅 열기</a>
<?php } else { ?>
    <p class="panel-notice-empty">로그인 후 확인해보세요.</p>
<?php } ?>
  </section>
</div>
