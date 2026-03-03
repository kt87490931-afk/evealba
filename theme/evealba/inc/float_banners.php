<?php
/**
 * 플로팅배너: 추천업소 패널 + CTA (카카오/채팅/맨위로)
 * tail.php, jobs_thumb_shop_main.php 등에서 include
 */
if (!defined('_GNUBOARD_')) exit;
$_fr_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';
$_fr_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_fr_rows = array();
$_fr_tb_check = sql_query("SHOW TABLES LIKE '{$_fr_sb_table}'");
if ($_fr_tb_check && sql_num_rows($_fr_tb_check) > 0) {
    $_fr_res = sql_query("SELECT jr.*
        FROM {$_fr_sb_table} sb
        LEFT JOIN {$_fr_jr_table} jr ON sb.sb_jr_id = jr.jr_id
        WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
        ORDER BY sb.sb_position ASC LIMIT 6");
    while ($_fr_r = sql_fetch_array($_fr_res)) {
        if (!empty($_fr_r['jr_id'])) $_fr_rows[] = $_fr_r;
    }
}
if (!function_exists('render_premium_card') && is_file(G5_PATH . '/extend/jobs_list_helper.php')) {
    include_once(G5_PATH . '/extend/jobs_list_helper.php');
}
?>
<!-- FLOATING RECOMMEND + CTA -->
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
<?php if (!empty($_fr_rows) && function_exists('render_premium_card')) : ?>
<?php foreach ($_fr_rows as $_fr_row) : ?>
      <?php render_premium_card($_fr_row, 'fr-card'); ?>
<?php endforeach; ?>
<?php elseif (!empty($_fr_rows)) : ?>
<?php foreach ($_fr_rows as $_fr_row) :
    $_fr_link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . (int)$_fr_row['jr_id'];
    $_fr_name = $_fr_row['jr_nickname'] ?: ($_fr_row['jr_company'] ?: '업소명');
?>
      <a href="<?php echo htmlspecialchars($_fr_link); ?>" class="fr-card" style="display:block;text-decoration:none;color:inherit;">
        <div class="premium-banner" style="background:linear-gradient(135deg,rgb(255,65,108),rgb(255,75,43))"><?php echo htmlspecialchars($_fr_name); ?></div>
        <div class="premium-body"><div class="premium-name"><?php echo htmlspecialchars($_fr_name); ?></div></div>
      </a>
<?php endforeach; ?>
<?php else : ?>
      <div style="padding:20px 12px;text-align:center;color:#999;font-size:13px;">등록된 추천업소가 없습니다.</div>
<?php endif; ?>
    </div>
  </div>
</div>
<script>
function toggleFloatRecommend(){var el=document.getElementById('floatRecommend');if(el)el.classList.toggle('fr-open');}
(function(){
  var fr=document.getElementById('floatRecommend');
  if(!fr) return;
  var ma=document.querySelector('.main-area');
  var gap=14;
  var defTop=120;
  function pos(){
    if(!ma){
      fr.style.opacity='1'; fr.style.pointerEvents=''; fr.style.top=(defTop+'px');
      return;
    }
    var r=ma.getBoundingClientRect();
    var panelH=fr.offsetHeight||200;
    var mainTop=r.top+gap;
    var mainBot=r.bottom-panelH-gap;
    if(mainTop>mainBot) mainTop=mainBot;
    if(r.bottom<0||r.top>window.innerHeight){ fr.style.opacity='1'; fr.style.pointerEvents=''; fr.style.top=(defTop+'px'); }
    else { fr.style.opacity='1'; fr.style.pointerEvents=''; var t=mainTop<gap?gap:mainTop; if(t>mainBot)t=mainBot; fr.style.top=(t+'px'); }
  }
  window.addEventListener('scroll',pos,{passive:true});
  window.addEventListener('resize',pos,{passive:true});
  pos();
})();
</script>
<div class="floating-cta">
  <a href="#" class="float-btn float-kakao" title="카카오톡 문의"><img src="<?php echo G5_THEME_URL; ?>/img/logo_kakao.svg" alt="카카오톡" style="width:26px;height:26px;"></a>
  <button type="button" class="float-btn float-chat" id="chatOpen" title="실시간 채팅" onclick="if(typeof toggleEveChat==='function')toggleEveChat();else if(typeof toggleEveChatMobile==='function')toggleEveChatMobile();return false;">💬</button>
  <a href="#" class="float-btn float-top" title="맨 위로" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">▲</a>
</div>
