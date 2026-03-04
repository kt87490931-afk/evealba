<?php
if (!defined('_GNUBOARD_')) exit;

/* 채용정보/인재정보 등 반응형 레이아웃 페이지: 메인과 동일한 tail 사용 (추천업소 플로팅배너 포함) */
$_ev_use_pc_tail = defined('_JOBS_') || defined('_JOBS_REGION_') || defined('_JOBS_VIEW_') || defined('_JOBS_REGISTER_') || defined('_JOBS_ONGOING_') || defined('_JOBS_ENDED_') || defined('_JOBS_JUMP_SHOP_') || defined('_TALENT_') || defined('_TALENT_VIEW_');
if (G5_IS_MOBILE && !$_ev_use_pc_tail) {
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
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/cs.php' : '/cs.php'; ?>">고객센터</a>
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

<!-- EVE CHAT (iframe 격리) -->
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

<!-- FLOATING RECOMMEND + CTA (썸네일상점은 jobs_thumb_shop_main에서 출력) -->
<?php if (!defined('_THUMB_SHOP_FLOATS_DONE_')) { include_once(G5_THEME_PATH . '/inc/float_banners.php'); } ?>

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
/* 인재정보: 업직종 탭 전환 */
function setTab(el, type) {
  var cards = document.querySelectorAll('.type-tab-card');
  if (cards) cards.forEach(function(c){ c.classList.remove('active'); });
  if (el) el.classList.add('active');
}
/* 이브수다방: 사이드 커뮤니티 메뉴 active */
document.querySelectorAll('.side-comm-item').forEach(function(el){
  el.addEventListener('click', function(e){
    e.preventDefault();
    document.querySelectorAll('.side-comm-item').forEach(function(i){ i.classList.remove('active'); });
    el.classList.add('active');
  });
});
/* 고객센터: FAQ 아코디언 */
function toggleFaq(el) {
  var item = el.parentElement;
  var isOpen = item.classList.contains('open');
  document.querySelectorAll('.faq-item').forEach(function(i){ i.classList.remove('open'); });
  if (!isOpen) item.classList.add('open');
}
/* 고객센터: 사이드 CS 메뉴 active */
document.querySelectorAll('.side-cs-item').forEach(function(el){
  el.addEventListener('click', function(){
    document.querySelectorAll('.side-cs-item').forEach(function(i){ i.classList.remove('active'); });
    el.classList.add('active');
  });
});
</script>

<?php if ($config['cf_analytics']) { echo $config['cf_analytics']; } ?>

<?php
if (defined('_JOBS_') && is_file(G5_THEME_PATH.'/js/jobs_filter.js')) {
    echo '<script src="'.G5_THEME_URL.'/js/jobs_filter.js?v='.@filemtime(G5_THEME_PATH.'/js/jobs_filter.js').'"></script>';
}
if (defined('_TALENT_') && is_file(G5_THEME_PATH.'/js/talent_filter.js')) {
    echo '<script src="'.G5_THEME_URL.'/js/talent_filter.js?v='.@filemtime(G5_THEME_PATH.'/js/talent_filter.js').'"></script>';
}
if (is_file(G5_THEME_PATH.'/js/sp_user_menu_common.js')) {
    echo '<script src="'.G5_THEME_URL.'/js/sp_user_menu_common.js?v='.@filemtime(G5_THEME_PATH.'/js/sp_user_menu_common.js').'"></script>';
}
include_once(G5_THEME_PATH."/tail.sub.php");
?>
