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

<!-- FLOATING RECOMMEND PANEL -->
<div class="float-recommend" id="floatRecommend">
  <button type="button" class="fr-tab" id="frTab" onclick="toggleFloatRecommend()">
    <span class="fr-tab-icon">💎</span>
    <span class="fr-tab-text">추천업소</span>
  </button>
  <div class="fr-panel">
    <div class="fr-header">
      <span class="fr-title">💎 추천업소</span>
      <button type="button" class="fr-close" onclick="toggleFloatRecommend()">&times;</button>
    </div>
    <div class="fr-list">
      <a href="#" class="fr-card">
        <div class="fr-banner g12">동탄스카이<br>아이퍼블릭<b>60분 TC12만원</b></div>
        <div class="fr-info"><div class="fr-name">동탄스카이 아이퍼블릭</div><div class="fr-wage">자유복장 · TC12만원</div></div>
      </a>
      <a href="#" class="fr-card">
        <div class="fr-banner g1">일프로 &amp; 텐카페<b>300만 보상</b></div>
        <div class="fr-info"><div class="fr-name">일프로 · 텐카페</div><div class="fr-wage">300만원 보장</div></div>
      </a>
      <a href="#" class="fr-card">
        <div class="fr-banner" style="background:linear-gradient(135deg,#1A0010,#FF1B6B)">당일<br>백만 UP</div>
        <div class="fr-info"><div class="fr-name">당일 백만원 UP 이벤트</div><div class="fr-wage">기간 한정 특별 혜택</div></div>
      </a>
      <a href="#" class="fr-card">
        <div class="fr-banner g7">강남 VIP<b>순수테이블 2H</b></div>
        <div class="fr-info"><div class="fr-name">강남짬오 이태곤대표</div><div class="fr-wage">면접 후 협의</div></div>
      </a>
      <a href="#" class="fr-card">
        <div class="fr-banner g6">수원 하이퍼<b>TC12 당일지급</b></div>
        <div class="fr-info"><div class="fr-name">아우라 하이퍼블릭</div><div class="fr-wage">면접 후 협의</div></div>
      </a>
      <a href="#" class="fr-card">
        <div class="fr-banner g3">파주 최고TC<b>1시간 10만원</b></div>
        <div class="fr-info"><div class="fr-name">파주최고TC REINA</div><div class="fr-wage">100,000원</div></div>
      </a>
    </div>
  </div>
</div>
<script>
function toggleFloatRecommend(){
  var el=document.getElementById('floatRecommend');
  if(el) el.classList.toggle('fr-open');
}
(function(){
  var fr=document.getElementById('floatRecommend');
  if(!fr) return;
  var ma=document.querySelector('.main-area');
  if(!ma) return;
  var gap=14;
  function pos(){
    var r=ma.getBoundingClientRect();
    var panelH=fr.offsetHeight;
    var mainTop=r.top+gap;
    var mainBot=r.bottom-panelH-gap;
    if(mainTop>mainBot) mainTop=mainBot;
    if(r.bottom<panelH||r.top>window.innerHeight){
      fr.style.opacity='0';
      fr.style.pointerEvents='none';
    } else {
      fr.style.opacity='1';
      fr.style.pointerEvents='';
      var t=mainTop<gap?gap:mainTop;
      if(t>mainBot) t=mainBot;
      fr.style.top=t+'px';
    }
  }
  window.addEventListener('scroll',pos,{passive:true});
  window.addEventListener('resize',pos,{passive:true});
  pos();
})();
</script>

<!-- FLOATING CTA -->
<div class="floating-cta">
  <a href="#" class="float-btn float-kakao" title="카카오톡 문의"><img src="<?php echo G5_THEME_URL; ?>/img/logo_kakao.svg" alt="카카오톡" style="width:26px;height:26px;"></a>
  <button type="button" class="float-btn float-chat" id="chatOpen" title="실시간 채팅" onclick="if(typeof toggleEveChat==='function')toggleEveChat();return false;">💬</button>
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
