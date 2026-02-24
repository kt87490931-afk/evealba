<?php
/**
 * 쪽지함 공통 헤더 - eve_alba_messages_1.html 참조 (top-widget + tab-header)
 * 필요 변수: $memo_recv_count, $memo_unread_count, $memo_send_count, $memo_current_tab(recv|unread|send|form), $member_type(기업회원|일반회원)
 */
if (!defined('_GNUBOARD_')) exit;
$memo_recv_count = isset($memo_recv_count) ? (int)$memo_recv_count : 0;
$memo_unread_count = isset($memo_unread_count) ? (int)$memo_unread_count : 0;
$memo_send_count = isset($memo_send_count) ? (int)$memo_send_count : 0;
$memo_current_tab = isset($memo_current_tab) ? $memo_current_tab : 'recv';
$member_type = isset($member_type) ? $member_type : '일반회원';
$member_name = isset($member['mb_nick']) ? get_text($member['mb_nick']) : '';
$member_id = isset($member['mb_id']) ? $member['mb_id'] : '';
$role_icon = (strpos($member_type, '기업') !== false) ? '🏢' : '👤';
?>
<div class="memo-box-header">
  <div class="tw-profile">
    <div class="tw-avatar">
      <?php echo $member_id ? get_member_profile_img($member_id) : '👤'; ?>
    </div>
    <div class="tw-info">
      <span class="tw-name"><?php echo htmlspecialchars($member_name); ?> <span>님</span></span>
      <span class="tw-role"><?php echo $role_icon; ?> <?php echo htmlspecialchars($member_type); ?></span>
    </div>
  </div>
  <div class="tw-divider"></div>
  <div class="tw-stats">
    <a href="./memo.php?kind=recv" class="tw-stat">
      <span class="tw-stat-num"><?php echo $memo_recv_count; ?></span>
      <span class="tw-stat-label">받은쪽지</span>
    </a>
    <a href="./memo.php?kind=unread" class="tw-stat">
      <span class="tw-stat-num orange"><?php echo $memo_unread_count; ?></span>
      <span class="tw-stat-label">미확인</span>
    </a>
    <a href="./memo.php?kind=send" class="tw-stat">
      <span class="tw-stat-num dark"><?php echo $memo_send_count; ?></span>
      <span class="tw-stat-label">보낸쪽지</span>
    </a>
  </div>
  <div class="memo-tabs-wrap">
    <div class="tab-header">
      <a href="./memo.php?kind=recv" class="tab-btn <?php echo ($memo_current_tab==='recv')?'active':''; ?>">
        <span class="tab-btn-icon">📥</span>
        <span>받은쪽지함</span>
        <?php if ($memo_recv_count) { ?><span class="tbb"><?php echo $memo_recv_count; ?></span><?php } ?>
      </a>
      <a href="./memo.php?kind=unread" class="tab-btn <?php echo ($memo_current_tab==='unread')?'active':''; ?>">
        <span class="tab-btn-icon">🔔</span>
        <span>미열람목록</span>
        <?php if ($memo_unread_count) { ?><span class="tbb orange"><?php echo $memo_unread_count; ?></span><?php } ?>
      </a>
      <a href="./memo.php?kind=send" class="tab-btn <?php echo ($memo_current_tab==='send')?'active':''; ?>">
        <span class="tab-btn-icon">📤</span>
        <span>보낸쪽지함</span>
      </a>
    </div>
  </div>
</div>
