<?php
/**
 * 쪽지함 공통 헤더 - 2열 반응형, 쪽지보내기 탭 제거
 * 필요 변수: $memo_recv_count, $memo_unread_count, $memo_send_count, $memo_current_tab(recv|unread|send|form), $member_type(기업회원|일반회원)
 */
if (!defined('_GNUBOARD_')) exit;
$memo_recv_count = isset($memo_recv_count) ? (int)$memo_recv_count : 0;
$memo_unread_count = isset($memo_unread_count) ? (int)$memo_unread_count : 0;
$memo_send_count = isset($memo_send_count) ? (int)$memo_send_count : 0;
$memo_current_tab = isset($memo_current_tab) ? $memo_current_tab : 'recv';
$member_type = isset($member_type) ? $member_type : '일반회원';
$member_name = isset($member['mb_nick']) ? get_text($member['mb_nick']) : '';
?>
<div class="memo-box-header">
  <div class="memo-header-row memo-header-2col">
    <div class="memo-header-col memo-header-user">
      <span class="memo-user-name"><?php echo htmlspecialchars($member_name); ?> 님</span>
      <span class="memo-user-type"><?php echo htmlspecialchars($member_type); ?></span>
    </div>
    <div class="memo-header-col memo-header-stats">
      <span class="memo-stat"><strong><?php echo $memo_recv_count; ?></strong> 받은쪽지</span>
      <span class="memo-stat"><strong><?php echo $memo_unread_count; ?></strong> 미확인</span>
      <span class="memo-stat"><strong><?php echo $memo_send_count; ?></strong> 보낸쪽지</span>
    </div>
  </div>
  <div class="memo-header-nav">
    <a href="./memo.php?kind=recv" class="memo-nav-item <?php echo ($memo_current_tab==='recv')?'active':''; ?>">받은쪽지함<?php if($memo_recv_count){ ?><span class="memo-badge"><?php echo $memo_recv_count; ?></span><?php } ?></a>
    <a href="./memo.php?kind=unread" class="memo-nav-item <?php echo ($memo_current_tab==='unread')?'active':''; ?>">미열람목록<?php if($memo_unread_count){ ?><span class="memo-badge"><?php echo $memo_unread_count; ?></span><?php } ?></a>
    <a href="./memo.php?kind=send" class="memo-nav-item <?php echo ($memo_current_tab==='send')?'active':''; ?>">보낸쪽지함</a>
  </div>
</div>
