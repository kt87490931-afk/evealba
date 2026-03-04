<?php
if (!defined('_GNUBOARD_')) exit;
$sender_nick = get_text($mb['mb_nick'] ?: '정보없음');
if ($kind == "recv") {
    $kind_str = "보낸";
    $kind_date = "받은";
} else {
    $kind_str = "받는";
    $kind_date = "보낸";
}
$memo_list_url = G5_BBS_URL.'/memo.php';
$memo_form_url = G5_BBS_URL.'/memo_form.php';
?>
<div class="tab-header memo-tabs">
  <a href="<?php echo $memo_list_url; ?>?kind=recv" class="tab-btn <?php echo ($memo_current_tab==='recv')?'active':''; ?>">
    <span class="tab-btn-icon">📥</span>받은쪽지함 <?php if ($memo_recv_count) { ?><span class="tbb"><?php echo $memo_recv_count; ?></span><?php } ?>
  </a>
  <a href="<?php echo $memo_list_url; ?>?kind=unread" class="tab-btn <?php echo ($memo_current_tab==='unread')?'active':''; ?>">
    <span class="tab-btn-icon">🔔</span>미열람목록 <?php if ($memo_unread_count) { ?><span class="tbb orange"><?php echo $memo_unread_count; ?></span><?php } ?>
  </a>
  <a href="<?php echo $memo_list_url; ?>?kind=send" class="tab-btn <?php echo ($memo_current_tab==='send')?'active':''; ?>">
    <span class="tab-btn-icon">📤</span>보낸쪽지함
  </a>
  <a href="<?php echo $memo_form_url; ?>" class="tab-btn">
    <span class="tab-btn-icon">✉️</span>쪽지보내기
  </a>
</div>

<div class="message-card memo-view-card">
  <div class="card-header memo-view-header">
    <i class="fa fa-envelope" aria-hidden="true"></i>
    <h2>쪽지 내용</h2>
  </div>
  <div class="card-meta memo-view-meta">
    <div class="sender-badge">
      <span class="sender-name"><?php echo $sender_nick; ?></span>
    </div>
    <span class="meta-divider">|</span>
    <div class="send-date">
      <i class="fa fa-clock-o" aria-hidden="true"></i>
      <?php echo $memo['me_send_datetime']; ?>
    </div>
    <div class="meta-actions">
      <a href="<?php echo $list_link; ?>" class="btn btn-list"><i class="fa fa-list"></i> 목록</a>
      <a href="<?php echo $del_link; ?>" onclick="del(this.href); return false;" class="btn btn-delete"><i class="fa fa-trash-o"></i> 삭제</a>
    </div>
  </div>
  <div class="card-nav memo-view-nav">
    <?php if ($prev_link) { ?><a href="<?php echo $prev_link; ?>" class="btn-prev"><i class="fa fa-chevron-left"></i> 이전쪽지</a><?php } ?>
    <?php if ($next_link) { ?><a href="<?php echo $next_link; ?>" class="btn-prev">다음쪽지 <i class="fa fa-chevron-right"></i></a><?php } ?>
  </div>
  <div class="card-body memo-view-body">
    <div class="message-text"><?php echo conv_content($memo['me_memo'], 0); ?></div>
  </div>
  <div class="card-footer memo-view-footer">
    <?php if ($kind == 'recv') { ?><a href="<?php echo $memo_form_url; ?>?me_id=<?php echo $memo['me_id']; ?>" class="btn btn-reply"><i class="fa fa-reply"></i> 답장</a><?php } ?>
    <button type="button" onclick="if(window.history.length>1){history.back();}else{location.href='<?php echo G5_URL; ?>';} return false;" class="btn-back">뒤로</button>
  </div>
</div>
