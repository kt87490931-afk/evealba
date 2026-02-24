<?php
if (!defined('_GNUBOARD_')) exit;
$nick = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage']);
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

<div class="content-card">
  <div class="msg-toolbar">
    <span class="msg-toolbar-title">쪽지 내용</span>
  </div>
  <article id="memo_view_contents" style="padding:18px;">
    <div class="msg-hrow" style="margin-bottom:12px;">
      <span class="msg-sender"><?php echo $nick; ?></span>
      <span style="font-size:12px;color:#888;"><i class="fa fa-clock-o"></i> <?php echo $memo['me_send_datetime']; ?></span>
      <a href="<?php echo $list_link; ?>" class="btn-tb" style="margin-left:auto;"><i class="fa fa-list"></i> 목록</a>
      <a href="<?php echo $del_link; ?>" onclick="del(this.href); return false;" class="btn-tb danger"><i class="fa fa-trash-o"></i> 삭제</a>
    </div>
    <div class="memo_btn" style="margin-bottom:14px;">
      <?php if ($prev_link) { ?><a href="<?php echo $prev_link; ?>" class="btn-tb"><i class="fa fa-chevron-left"></i> 이전쪽지</a><?php } ?>
      <?php if ($next_link) { ?><a href="<?php echo $next_link; ?>" class="btn-tb">다음쪽지 <i class="fa fa-chevron-right"></i></a><?php } ?>
    </div>
    <div style="font-size:14px;color:#444;line-height:1.8;">
      <?php echo conv_content($memo['me_memo'], 0); ?>
    </div>
  </article>
  <div class="win_btn" style="border-top:2px solid var(--pale-pink);padding:14px 18px;">
    <?php if ($kind == 'recv') { ?><a href="<?php echo $memo_form_url; ?>?me_id=<?php echo $memo['me_id']; ?>" class="memo-btn-write" style="display:inline-flex;margin-right:8px;">↩ 답장</a><?php } ?>
    <button type="button" onclick="if(window.history.length>1){history.back();}else{location.href='<?php echo G5_URL; ?>';} return false;" class="btn_close">뒤로</button>
  </div>
</div>
