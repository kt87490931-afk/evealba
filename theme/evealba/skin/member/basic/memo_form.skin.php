<?php
if (!defined('_GNUBOARD_')) exit;
$memo_list_url = G5_BBS_URL.'/memo.php';
$memo_form_url = G5_BBS_URL.'/memo_form.php';
?>
<div class="tab-header memo-tabs">
  <a href="<?php echo $memo_list_url; ?>?kind=recv" class="tab-btn <?php echo ($memo_current_tab==='recv')?'active':''; ?>">
    <span class="tab-btn-icon">📥</span>받은쪽지함 <?php if (isset($memo_recv_count) && $memo_recv_count) { ?><span class="tbb"><?php echo $memo_recv_count; ?></span><?php } ?>
  </a>
  <a href="<?php echo $memo_list_url; ?>?kind=unread" class="tab-btn <?php echo ($memo_current_tab==='unread')?'active':''; ?>">
    <span class="tab-btn-icon">🔔</span>미열람목록 <?php if (isset($memo_unread_count) && $memo_unread_count) { ?><span class="tbb orange"><?php echo $memo_unread_count; ?></span><?php } ?>
  </a>
  <a href="<?php echo $memo_list_url; ?>?kind=send" class="tab-btn <?php echo ($memo_current_tab==='send')?'active':''; ?>">
    <span class="tab-btn-icon">📤</span>보낸쪽지함
  </a>
  <a href="<?php echo $memo_form_url; ?>" class="tab-btn active">
    <span class="tab-btn-icon">✉️</span>쪽지보내기
  </a>
</div>

<div class="content-card">
  <div class="msg-toolbar">
    <span class="msg-toolbar-title">✉️ 쪽지 보내기</span>
  </div>
  <form name="fmemoform" action="<?php echo $memo_action_url; ?>" onsubmit="return fmemoform_submit(this);" method="post" autocomplete="off" class="compose-section" style="padding:0;">
    <div class="crow" style="align-items:flex-start;">
      <div class="clabel" style="align-self:flex-start;padding-top:16px;">받는사람</div>
      <div class="ccell col" style="padding:12px 16px;">
        <div class="memo-recv-fixed" style="padding:8px 0;">운영자</div>
        <input type="hidden" name="me_recv_mb_id" value="<?php echo htmlspecialchars($me_recv_mb_id); ?>">
        <?php if ($config['cf_memo_send_point']) { ?>
        <span style="font-size:11px;color:#bbb;">쪽지 보낼때 <?php echo number_format($config['cf_memo_send_point']); ?>점 차감</span>
        <?php } ?>
      </div>
    </div>
    <div class="crow" style="align-items:flex-start;">
      <div class="clabel" style="align-self:flex-start;padding-top:14px;">내용</div>
      <div class="ccell col" style="padding:12px 16px;">
        <textarea name="me_memo" id="me_memo" required class="ci-ta" placeholder="쪽지 내용을 입력해주세요 (최대 2000자)" maxlength="2000"><?php echo $content; ?></textarea>
        <span class="char-c memo-char-count" style="margin-top:5px;">0/2000</span>
      </div>
    </div>
    <div class="crow" style="align-items:flex-start;">
      <div class="clabel" style="align-self:flex-start;padding-top:14px;">자동등록방지</div>
      <div class="ccell" style="padding:12px 16px;">
        <?php echo captcha_html(); ?>
      </div>
    </div>
    <div class="compose-btns">
      <button type="button" onclick="if(window.history.length>1){history.back();}else{location.href='<?php echo G5_URL; ?>';} return false;" class="btn-reset">뒤로</button>
      <button type="submit" id="btn_submit" class="btn-send">📨 쪽지 보내기</button>
    </div>
  </form>
</div>
<script>
function fmemoform_submit(f) {
  <?php echo chk_captcha_js(); ?>
  return true;
}
document.getElementById('me_memo').addEventListener('input', function(){
  var c = this.value.length;
  var el = document.querySelector('.memo-char-count');
  if(el) el.textContent = c + '/2000';
});
</script>
