<?php
/**
 * 빠른 메뉴 공통 위젯 - 모든 사이드바에서 로그인 위젯 아래에 include
 */
if (!defined('_GNUBOARD_')) exit;

$_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
?>
<div class="sidebar-widget">
  <div class="widget-title">⚡ 빠른 메뉴</div>
  <div class="widget-body">
    <div class="quick-links">
      <a href="<?php echo $_base; ?>/jobs_register.php" class="quick-link-btn"><span class="ql-icon">📋</span>채용공고 등록</a>
      <a href="<?php echo $_base; ?>/resume_register.php" class="quick-link-btn"><span class="ql-icon">👩</span>이력서 등록</a>
      <a href="<?php echo $_base; ?>/jobs.php" class="quick-link-btn"><span class="ql-icon">📍</span>지역별 채용</a>
      <a href="<?php echo $_base; ?>/sudabang.php" class="quick-link-btn"><span class="ql-icon">💬</span>수다방</a>
      <a href="javascript:void(0);" class="quick-link-btn" onclick="try{window.eveChatToggle?window.eveChatToggle():document.getElementById('eveChatTrigger')&&document.getElementById('eveChatTrigger').click();}catch(e){}"><span class="ql-icon">💬</span>채팅</a>
      <a href="<?php echo G5_BBS_URL; ?>/memo.php" class="quick-link-btn"><span class="ql-icon">📩</span>쪽지</a>
    </div>
  </div>
</div>
