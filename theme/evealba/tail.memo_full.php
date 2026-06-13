<?php
if (!defined('_GNUBOARD_')) exit;
?>
  </div><!-- /main-area.memo-main -->
    </div><!-- /memo-page-layout -->
  </div><!-- /main-area -->
<?php if (defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI && !G5_IS_MOBILE) { include G5_THEME_PATH.'/inc/panel_right.php'; } ?>
</div><!-- /page-layout -->

<?php if (defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI) { include G5_THEME_PATH.'/inc/mobile_tabbar.php'; } ?>

<footer>
  <div class="footer-inner">
    <div class="footer-logo"><em>eve</em>·<span>알바</span></div>
    <div class="footer-links">
      <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
      <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
      <a href="#">청소년보호정책</a>
      <a href="#">광고/제휴 문의</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/cs.php' : '/cs.php'; ?>">고객센터</a>
    </div>
    <div class="footer-text">
      상호명: (주)이브알바 | 대표이사: 홍길동 | 고객센터: 1588-0000<br>
      <span>본 사이트는 성인 유흥알바 구인구직 정보 사이트로, 만 18세 미만은 이용하실 수 없습니다.</span><br>
      © 2026 이브알바(EVE ALBA) All Rights Reserved.
    </div>
  </div>
</footer>

<!-- EVE CHAT (iframe 격리) - 메인/채용정보 등과 동일 -->
<iframe id="eveChatFrame"
  src="<?php echo G5_PLUGIN_URL; ?>/chat/eve_chat_frame.php"
  style="position:fixed;bottom:90px;right:28px;width:390px;height:calc(100vh - 110px);max-height:750px;border:1.5px solid #F0E0E8;border-radius:16px;box-shadow:0 8px 32px rgba(255,27,107,.15);z-index:1100;display:none;background:#fff;overflow:hidden;"
  allow="autoplay"
  loading="lazy"></iframe>
<style>
@media(max-width:768px){
  #eveChatFrame{top:0!important;left:0!important;right:0!important;bottom:0!important;width:100%!important;height:100%!important;max-height:none!important;border:none!important;border-radius:0!important;box-shadow:none!important;z-index:9999!important;}
}
</style>
<script>
(function(){
  var frame=document.getElementById('eveChatFrame');
  var isOpen=false;
  window.toggleEveChat=function(){
    isOpen=!isOpen;
    frame.style.display=isOpen?'block':'none';
  };
  window.addEventListener('message',function(e){
    if(e.data&&e.data.type==='eve-chat-close'){
      isOpen=false;
      frame.style.display='none';
    }
  });
})();
</script>

<!-- FLOATING RECOMMEND + CTA (메인/채용정보 등과 동일 - float_banners.php) -->
<?php if (!(defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI && !G5_IS_MOBILE)) { include_once(G5_THEME_PATH . '/inc/float_banners.php'); } else { ?>
<div class="floating-cta">
  <a href="#" class="float-btn float-kakao" title="카카오톡 문의"><img src="<?php echo G5_THEME_URL; ?>/img/logo_kakao.svg" alt="카카오톡" style="width:26px;height:26px;"></a>
  <button type="button" class="float-btn float-chat" title="실시간 채팅" onclick="if(typeof toggleEveChat==='function')toggleEveChat();return false;">💬</button>
  <a href="#" class="float-btn float-top" title="맨 위로" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">▲</a>
</div>
<?php } ?>

</body>
</html>
<?php echo html_end(); ?>
