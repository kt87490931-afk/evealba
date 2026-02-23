<?php
if (!defined('_GNUBOARD_')) exit;

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/tail.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/shop.tail.php');
    return;
}
?>

  </div><!-- /main-area -->
</div><!-- /page-layout -->

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-logo"><em>eve</em>·<span>알바</span></div>
    <div class="footer-links">
      <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
      <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
      <a href="#">청소년보호정책</a>
      <a href="#">광고/제휴 문의</a>
      <a href="#">사이트맵</a>
      <a href="#">고객센터</a>
    </div>
    <div class="footer-text">
      상호명: (주)이브알바 | 대표이사: 홍길동 | 사업자등록번호: 000-00-00000<br>
      통신판매업 신고번호: 제0000-서울강남-0000호 | 고객센터: 1588-0000<br>
      주소: 서울특별시 강남구 테헤란로 00길 00, 00층<br>
      <span>본 사이트는 성인 유흥알바 구인구직 정보 사이트로, 만 18세 미만은 이용하실 수 없습니다.</span><br>
      © 2026 이브알바(EVE ALBA) All Rights Reserved.
    </div>
  </div>
</footer>

<!-- CHAT PANEL -->
<div class="chat-panel-overlay" id="chatOverlay"></div>
<div class="chat-panel" id="chatPanel">
  <div class="chat-panel-header">
    <h3>💬 실시간 채팅</h3>
    <button type="button" class="chat-panel-close" id="chatClose">×</button>
  </div>
  <div class="chat-panel-body">
    <div class="chat-placeholder">
      <span class="icon">💭</span>
      <p>이브알바 실시간 채팅방입니다.<br>로그인 후 이용해 주세요.</p>
    </div>
  </div>
</div>

<!-- FLOATING CTA -->
<div class="floating-cta">
  <a href="#" class="float-btn float-kakao" title="카카오톡 문의">💬</a>
  <button type="button" class="float-btn float-chat" id="chatOpen" title="채팅">💭</button>
  <a href="#" class="float-btn float-top" title="맨 위로" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">▲</a>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var header = this.closest('.tab-header');
    if (header) {
      header.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
      this.classList.add('active');
    }
  });
});
(function(){
  var overlay = document.getElementById('chatOverlay');
  var panel = document.getElementById('chatPanel');
  var openBtn = document.getElementById('chatOpen');
  var closeBtn = document.getElementById('chatClose');
  function openChat() {
    if (overlay) overlay.classList.add('is-open');
    if (panel) panel.classList.add('is-open');
  }
  function closeChat() {
    if (overlay) overlay.classList.remove('is-open');
    if (panel) panel.classList.remove('is-open');
  }
  if (openBtn) openBtn.addEventListener('click', function(e) { e.preventDefault(); openChat(); });
  if (closeBtn) closeBtn.addEventListener('click', closeChat);
  if (overlay) overlay.addEventListener('click', closeChat);
})();
</script>

<?php if ($config['cf_analytics']) { echo $config['cf_analytics']; } ?>

<?php
if (is_file(G5_THEME_PATH.'/js/sp_user_menu_common.js')) {
    echo '<script src="'.G5_THEME_URL.'/js/sp_user_menu_common.js?v='.@filemtime(G5_THEME_PATH.'/js/sp_user_menu_common.js').'"></script>';
}
include_once(G5_THEME_PATH."/tail.sub.php");
?>
