<?php
if (!defined('_GNUBOARD_')) exit;
$list_count = count($list);
$memo_form_url = G5_BBS_URL.'/memo_form.php';
$memo_list_url = G5_BBS_URL.'/memo.php';
$memo_delete_url = G5_BBS_URL.'/memo_delete.php';
$list_kind_param = isset($list_kind_param) ? $list_kind_param : $memo_current_tab;
$search_keyword = isset($_GET['search']) ? clean_xss_tags($_GET['search'], 0, 1) : '';
?>
<div class="tab-header memo-tabs">
  <a href="<?php echo $memo_list_url; ?>?kind=recv" class="tab-btn <?php echo ($memo_current_tab==='recv')?'active':''; ?>">
    <span class="tab-btn-icon">📥</span>받은쪽지함
    <?php if ($memo_recv_count) { ?><span class="tbb"><?php echo $memo_recv_count; ?></span><?php } ?>
  </a>
  <a href="<?php echo $memo_list_url; ?>?kind=unread" class="tab-btn <?php echo ($memo_current_tab==='unread')?'active':''; ?>">
    <span class="tab-btn-icon">🔔</span>미열람목록
    <?php if ($memo_unread_count) { ?><span class="tbb orange"><?php echo $memo_unread_count; ?></span><?php } ?>
  </a>
  <a href="<?php echo $memo_list_url; ?>?kind=send" class="tab-btn <?php echo ($memo_current_tab==='send')?'active':''; ?>">
    <span class="tab-btn-icon">📤</span>보낸쪽지함
  </a>
  <a href="<?php echo $memo_form_url; ?>" class="tab-btn">
    <span class="tab-btn-icon">✉️</span>쪽지보내기
  </a>
</div>

<?php
$list_kind_param = ($memo_current_tab === 'unread') ? 'unread' : $kind;
$search_keyword = isset($_GET['st']) ? trim($_GET['st']) : '';
?>
<div class="content-card">
  <form method="post" action="<?php echo G5_BBS_URL; ?>/memo_delete.php" id="memo-list-form">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="hidden" name="kind" value="<?php echo $kind; ?>">
    <div class="msg-toolbar">
      <span class="msg-toolbar-title"><?php echo ($memo_current_tab==='recv') ? '📥 받은 쪽지함' : (($memo_current_tab==='unread') ? '🔔 미열람 목록' : '📤 보낸 쪽지함'); ?> <span>(총 <?php echo $list_count; ?>건)</span></span>
      <div class="msg-toolbar-actions">
        <form method="get" action="<?php echo $memo_list_url; ?>" class="msg-search-form">
          <input type="hidden" name="kind" value="<?php echo $list_kind_param; ?>">
          <div class="msg-search">
            <input type="text" name="st" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="내용/보낸이 검색" maxlength="50">
            <button type="submit">검색</button>
          </div>
        </form>
        <?php if ($list_count > 0) { ?>
        <button type="button" class="btn-tb" id="memo-select-all" aria-label="전체선택">전체선택</button>
        <button type="submit" class="btn-tb danger" name="btn_delete">선택삭제</button>
        <?php } ?>
      </div>
    </div>
    <div class="memo-list-actions">
      <a href="<?php echo $memo_form_url; ?>" class="memo-btn-write">✉️ 쪽지 쓰기</a>
    </div>
  <?php if ($list_count > 0) { ?>
  <ul class="msg-list">
    <?php
    for ($i=0; $i<$list_count; $i++) {
      $row = $list[$i];
      $readed = (substr($row['me_read_datetime'],0,1) != '0');
      $memo_preview = utf8_strcut(strip_tags($row['me_memo']), 30, '..');
      $item_class = $readed ? '' : ' unread';
    ?>
    <li class="msg-item<?php echo $item_class; ?>">
      <div class="msg-chk">
        <input type="checkbox" class="msg-checkbox" name="me_id[]" value="<?php echo $row['me_id']; ?>" id="me_id_<?php echo $row['me_id']; ?>">
      </div>
      <a href="<?php echo $row['view_href']; ?>" class="msg-body">
        <div class="msg-hrow">
          <span class="msg-sender"><?php echo get_text($row['name']); ?></span>
          <?php if (!$readed) { ?><span class="mbadge new">미열람</span><?php } ?>
        </div>
        <div class="msg-title"><?php echo $readed ? get_text($memo_preview) : '<b>'.get_text($memo_preview).'</b>'; ?></div>
        <div class="msg-preview"><?php echo get_text($memo_preview); ?></div>
      </a>
      <div class="msg-meta">
        <span class="msg-date"><?php echo $row['send_datetime']; ?></span>
        <span class="msg-st<?php echo $readed ? '' : ' unread'; ?>"><?php echo $readed ? '열람' : '미열람'; ?></span>
        <a href="<?php echo $row['del_href']; ?>" onclick="del(this.href); return false;" class="memo-del" title="삭제"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
      </div>
    </li>
    <?php } ?>
  </ul>
  <?php } else { ?>
  <div class="empty-state">
    <div class="empty-icon">📭</div>
    <p class="empty-title">쪽지가 없습니다</p>
    <p class="empty-sub">받은/보낸 쪽지가 없습니다.</p>
  </div>
  <?php } ?>
  </form>
  <?php echo $write_pages; ?>
  <p class="win_desc"><i class="fa fa-info-circle" aria-hidden="true"></i> 쪽지 보관일수는 최장 <strong><?php echo $config['cf_memo_del']; ?></strong>일 입니다.</p>
  <div class="win_btn">
    <button type="button" onclick="if(window.history.length>1){history.back();}else{location.href='<?php echo G5_URL; ?>';} return false;" class="btn_close">뒤로</button>
  </div>
</div>
<script>
(function(){
  var form = document.getElementById('memo-list-form');
  if (!form) return;
  var btn = document.getElementById('memo-select-all');
  if (btn) {
    btn.addEventListener('click', function(){
      var cbs = form.querySelectorAll('.msg-checkbox');
      var any = false;
      for (var i = 0; i < cbs.length; i++) { if (cbs[i].checked) { any = true; break; } }
      for (var j = 0; j < cbs.length; j++) cbs[j].checked = !any;
    });
  }
  form.addEventListener('submit', function(e){
    if (e.target.querySelector('button[name=btn_delete]') && !e.target.querySelector('.msg-checkbox:checked')) {
      e.preventDefault();
      alert('삭제할 쪽지를 선택해 주세요.');
    }
  });
})();
</script>
