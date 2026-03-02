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
<?php
$_fr_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';
$_fr_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_fr_rows = array();
$_fr_tb_check = sql_query("SHOW TABLES LIKE '{$_fr_sb_table}'");
if ($_fr_tb_check && sql_num_rows($_fr_tb_check) > 0) {
    $_fr_res = sql_query("SELECT sb.*, jr.jr_company, jr.jr_title, jr.jr_subject_display, jr.jr_data
        FROM {$_fr_sb_table} sb
        LEFT JOIN {$_fr_jr_table} jr ON sb.sb_jr_id = jr.jr_id
        WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
        ORDER BY sb.sb_position ASC LIMIT 6");
    while ($_fr_r = sql_fetch_array($_fr_res)) {
        $_fr_rows[] = $_fr_r;
    }
}
$_fr_gradients = array(
    1  => 'linear-gradient(135deg,rgb(255,65,108),rgb(255,75,43))',
    2  => 'linear-gradient(135deg,rgb(255,94,98),rgb(255,195,113))',
    3  => 'linear-gradient(135deg,rgb(238,9,121),rgb(255,106,0))',
    4  => 'linear-gradient(135deg,rgb(74,0,224),rgb(142,45,226))',
    5  => 'linear-gradient(135deg,rgb(67,233,123),rgb(56,249,215))',
    6  => 'linear-gradient(135deg,rgb(29,209,161),rgb(9,132,227))',
    7  => 'linear-gradient(135deg,rgb(196,113,237),rgb(246,79,89))',
    8  => 'linear-gradient(135deg,rgb(36,198,220),rgb(81,74,157))',
    9  => 'linear-gradient(135deg,rgb(0,210,255),rgb(58,123,213))',
    10 => 'linear-gradient(135deg,rgb(236,64,122),rgb(240,98,146))',
    11 => 'linear-gradient(135deg,rgb(118,75,162),rgb(102,126,234))',
    12 => 'linear-gradient(135deg,rgb(72,85,99),rgb(41,50,60))',
);
?>
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
<?php if (!empty($_fr_rows)) : ?>
<?php foreach ($_fr_rows as $_fr) :
    $_fr_link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . (int)$_fr['sb_jr_id'];
    $_fr_company = $_fr['jr_company'] ?: '업소명';
    $_fr_title = $_fr['jr_title'] ?: ($_fr['jr_subject_display'] ?: '');

    $_fr_jd = !empty($_fr['jr_data']) ? json_decode($_fr['jr_data'], true) : array();
    $_fr_wage_text = '';
    if (!empty($_fr_jd['job_tc'])) {
        $_fr_wage_text = $_fr_jd['job_tc'];
    } elseif (!empty($_fr_jd['job_salary'])) {
        $_fr_wage_text = $_fr_jd['job_salary'];
    } elseif (!empty($_fr_title)) {
        $_fr_wage_text = mb_strimwidth($_fr_title, 0, 30, '…');
    }

    $_fr_sd = !empty($_fr['sb_data']) ? json_decode($_fr['sb_data'], true) : array();
    $_fr_grad_key = isset($_fr_sd['thumb_gradient']) ? $_fr_sd['thumb_gradient'] : ((int)$_fr['sb_position'] ?: 1);
    $_fr_grad = isset($_fr_gradients[$_fr_grad_key]) ? $_fr_gradients[$_fr_grad_key] : $_fr_gradients[1];
?>
      <a href="<?php echo htmlspecialchars($_fr_link); ?>" class="fr-card">
        <div class="fr-banner" style="background:<?php echo $_fr_grad; ?>"><?php echo htmlspecialchars($_fr_company); ?><?php if ($_fr_wage_text) : ?><b><?php echo htmlspecialchars($_fr_wage_text); ?></b><?php endif; ?></div>
        <div class="fr-info"><div class="fr-name"><?php echo htmlspecialchars($_fr_company); ?></div><?php if ($_fr_wage_text) : ?><div class="fr-wage"><?php echo htmlspecialchars($_fr_wage_text); ?></div><?php endif; ?></div>
      </a>
<?php endforeach; ?>
<?php else : ?>
      <div style="padding:20px 12px;text-align:center;color:#999;font-size:13px;">등록된 추천업소가 없습니다.</div>
<?php endif; ?>
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
