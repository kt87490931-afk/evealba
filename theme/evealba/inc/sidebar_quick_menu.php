<?php
/**
 * 빠른 메뉴 공통 위젯 - 모든 사이드바에서 로그인 위젯 아래에 include
 */
if (!defined('_GNUBOARD_')) exit;

$_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$memo_badge = 0;
if (!empty($is_member) && !empty($member['mb_id'])) {
  if (is_file(G5_LIB_PATH . '/eve_chat_dm.lib.php')) {
    include_once(G5_LIB_PATH . '/eve_chat_dm.lib.php');
    $memo_badge = min(99, (int)eve_chat_dm_unread_count($member['mb_id']));
  }
}
?>
<div class="sidebar-widget">
  <div class="widget-title">⚡ 빠른 메뉴</div>
  <div class="widget-body">
    <div class="quick-links">
      <a href="<?php echo $_base; ?>/jobs_register.php" class="quick-link-btn"><span class="ql-icon">📋</span>채용공고 등록</a>
      <a href="<?php echo $_base; ?>/resume_register.php" class="quick-link-btn"><span class="ql-icon">👩</span>이력서 등록</a>
      <a href="<?php echo $_base; ?>/jobs.php" class="quick-link-btn"><span class="ql-icon">📍</span>지역별 채용</a>
      <a href="<?php echo $_base; ?>/sudabang.php" class="quick-link-btn"><span class="ql-icon">💬</span>수다방</a>
      <a href="<?php echo $_base; ?>/memo_full.php" class="quick-link-btn ql-memo"><span class="ql-icon">🔔</span><span class="ql-memo-label">알림·채팅<?php if ($memo_badge > 0) { ?> <span class="ql-memo-badge">+<?php echo $memo_badge; ?></span><?php } ?></span></a>
    </div>
  </div>
</div>
