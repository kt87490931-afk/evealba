<?php
/**
 * 히어로 배너 (공통 include)
 * - DB(g5_special_banner)에서 active 히어로배너를 로드하여 동적 렌더링
 * - 복수 배너 등록 시 클라이언트 슬라이드쇼로 자동 전환
 * - 셔플 간격은 어드민 설정값 사용
 */
if (!defined('_GNUBOARD_')) exit;

$_hero_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';

$_hero_gradients = array(
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
    13 => 'linear-gradient(135deg,rgb(30,60,114),rgb(42,82,152))',
    14 => 'linear-gradient(135deg,rgb(255,243,176),rgb(170,218,255))',
    15 => 'linear-gradient(135deg,rgb(249,83,198),rgb(255,107,157))',
    16 => 'linear-gradient(135deg,rgb(255,0,110),rgb(131,56,236))',
    17 => 'linear-gradient(135deg,rgb(67,206,162),rgb(24,90,157))',
    18 => 'linear-gradient(135deg,rgb(19,78,94),rgb(113,178,128))',
    19 => 'linear-gradient(135deg,rgb(255,153,102),rgb(255,94,98))',
    20 => 'linear-gradient(135deg,rgb(86,171,47),rgb(168,224,99))',
    'P1' => 'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)',
    'P2' => 'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)',
    'P3' => 'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)',
    'P4' => 'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)',
);

$_hero_icons = array(
    'beginner' => array('label' => '💖 초보환영', 'bg' => '#FF1B6B'),
    'room'     => array('label' => '🏡 원룸제공', 'bg' => '#FF6B35'),
    'luxury'   => array('label' => '💎 고급시설', 'bg' => '#8B00FF'),
    'black'    => array('label' => '📋 블랙 관리', 'bg' => '#333'),
    'phone'    => array('label' => '📱 폰비지급', 'bg' => '#0077B6'),
    'size'     => array('label' => '👗 사이즈X', 'bg' => '#E91E63'),
    'set'      => array('label' => '🎀 세트환영', 'bg' => '#FF9800'),
    'pickup'   => array('label' => '🚗 픽업가능', 'bg' => '#4CAF50'),
    'member'   => array('label' => '🙋 1회원제운영', 'bg' => '#7B1FA2'),
    'kkongbi'  => array('label' => '💰 꽁비지급', 'bg' => '#00897B'),
);

$_hero_border_colors = array(
    'gold'        => '#FFD700',
    'pink'        => '#FF1B6B',
    'charcoal'    => '#3a3a3a',
    'royalblue'   => '#4169E1',
    'royalpurple' => '#7B2FBE',
);

$_hero_rows = array();
$_hero_shuffle_sec = 5;
$_hero_check = sql_query("SHOW TABLES LIKE '{$_hero_sb_table}'");
if ($_hero_check && sql_num_rows($_hero_check) > 0) {
    $_hero_res = sql_query("SELECT * FROM {$_hero_sb_table} WHERE sb_type = 'hero' AND sb_status = 'active' ORDER BY sb_position ASC LIMIT 10");
    while ($_hr = sql_fetch_array($_hero_res)) {
        $_hero_rows[] = $_hr;
    }
    $_hero_cfg = sql_fetch("SELECT sb_data FROM {$_hero_sb_table} WHERE sb_type = 'config' AND sb_status = 'active' LIMIT 1");
    if ($_hero_cfg && $_hero_cfg['sb_data']) {
        $_cfg_d = json_decode($_hero_cfg['sb_data'], true);
        if (isset($_cfg_d['hero_shuffle_sec'])) {
            $_hero_shuffle_sec = max(1, (int)$_cfg_d['hero_shuffle_sec']);
        }
    }
}

if (!empty($_hero_rows)) :
shuffle($_hero_rows);
$_hero_total = count($_hero_rows);
?>
<div class="hero-section">
  <div class="hero-slideshow" id="heroSlideshow">
<?php foreach ($_hero_rows as $_hi => $_hb) :
    $_hd = $_hb['sb_data'] ? json_decode($_hb['sb_data'], true) : array();
    if (!is_array($_hd)) $_hd = array();

    $_h_grad_key     = isset($_hd['thumb_gradient']) ? $_hd['thumb_gradient'] : '1';
    $_h_gradient     = isset($_hero_gradients[$_h_grad_key]) ? $_hero_gradients[$_h_grad_key] : $_hero_gradients[1];
    $_h_title        = isset($_hd['thumb_title']) ? $_hd['thumb_title'] : '';
    $_h_text1        = isset($_hd['thumb_text']) ? $_hd['thumb_text'] : '';
    $_h_text2        = isset($_hd['thumb_text2']) ? $_hd['thumb_text2'] : '';
    $_h_icon         = isset($_hd['thumb_icon']) ? $_hd['thumb_icon'] : '';
    $_h_motion       = isset($_hd['thumb_motion']) ? $_hd['thumb_motion'] : '';
    $_h_wave         = isset($_hd['thumb_wave']) ? (int)$_hd['thumb_wave'] : 0;
    $_h_title_color  = isset($_hd['thumb_text_color']) ? $_hd['thumb_text_color'] : '#ffffff';
    $_h_border       = isset($_hd['thumb_border']) ? $_hd['thumb_border'] : '';
    $_h_title_size   = isset($_hd['title_size']) ? $_hd['title_size'] : '30px';
    $_h_title_weight = isset($_hd['title_weight']) ? $_hd['title_weight'] : '900';
    $_h_text_size    = isset($_hd['text_size']) ? $_hd['text_size'] : '14px';
    $_h_text_color   = isset($_hd['text_color']) ? $_hd['text_color'] : '#ffffff';
    $_h_text_weight  = isset($_hd['text_weight']) ? $_hd['text_weight'] : '500';
    $_h_text2_size   = isset($_hd['text2_size']) ? $_hd['text2_size'] : '14px';
    $_h_text2_color  = isset($_hd['text2_color']) ? $_hd['text2_color'] : '#ffffff';
    $_h_text2_weight = isset($_hd['text2_weight']) ? $_hd['text2_weight'] : '500';
    $_h_shop_name    = isset($_hd['shop_name']) ? $_hd['shop_name'] : '';
    $_h_shop_size    = isset($_hd['shop_size']) ? $_hd['shop_size'] : '13px';
    $_h_shop_weight  = isset($_hd['shop_weight']) ? $_hd['shop_weight'] : '700';
    $_h_shop_color   = isset($_hd['shop_color']) ? $_hd['shop_color'] : '#ffffff';
    $_h_shop_pos_x   = isset($_hd['shop_pos_x']) ? (float)$_hd['shop_pos_x'] : 3;
    $_h_shop_pos_y   = isset($_hd['shop_pos_y']) ? (float)$_hd['shop_pos_y'] : 8;
    $_h_title_pos_x  = isset($_hd['title_pos_x']) ? (float)$_hd['title_pos_x'] : 3;
    $_h_title_pos_y  = isset($_hd['title_pos_y']) ? (float)$_hd['title_pos_y'] : 25;
    $_h_text1_pos_x  = isset($_hd['text1_pos_x']) ? (float)$_hd['text1_pos_x'] : 3;
    $_h_text1_pos_y  = isset($_hd['text1_pos_y']) ? (float)$_hd['text1_pos_y'] : 55;
    $_h_text2_pos_x  = isset($_hd['text2_pos_x']) ? (float)$_hd['text2_pos_x'] : 3;
    $_h_text2_pos_y  = isset($_hd['text2_pos_y']) ? (float)$_hd['text2_pos_y'] : 72;

    $_h_bg_style = 'background:' . $_h_gradient . ';';
    if ($_h_wave) {
        preg_match_all('/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/', $_h_gradient, $_h_m);
        if (!empty($_h_m[0]) && count($_h_m[0]) >= 2) {
            $c = $_h_m[0];
            $_h_bg_style = 'background:linear-gradient(135deg,'.$c[0].','.$c[1].','.(isset($c[2])?$c[2]:$c[0]).','.$c[0].','.$c[1].');background-size:400% 400%;animation:heroWave 3s ease infinite;';
        }
    }

    $_h_border_style = '';
    if ($_h_border && isset($_hero_border_colors[$_h_border])) {
        $_bc = $_hero_border_colors[$_h_border];
        $_h_border_style = 'box-shadow:inset 0 0 0 2px '.$_bc.', 0 0 0 2px '.$_bc.', 0 2px 8px rgba(0,0,0,.10);';
    }

    $_h_link = !empty($_hb['sb_link']) ? $_hb['sb_link'] : '';
    if (!$_h_link && !empty($_hb['sb_jr_id'])) {
        $_h_link = G5_URL . '/jobs_view.php?jr_id=' . (int)$_hb['sb_jr_id'];
    }

    $_h_badge_html = '';
    if ($_h_icon && isset($_hero_icons[$_h_icon])) {
        $_h_badge_html = '<div class="hero-badge" style="background:'.htmlspecialchars($_hero_icons[$_h_icon]['bg']).'">'.htmlspecialchars($_hero_icons[$_h_icon]['label']).'</div>';
    }

    $_h_motion_cls = $_h_motion ? ' pv-motion-'.htmlspecialchars($_h_motion) : '';
    $_h_slide_style = $_hi === 0 ? 'opacity:1;z-index:2;' : 'opacity:0;z-index:1;';
?>
    <div class="hero-slide" style="<?php echo $_h_slide_style; ?>">
      <?php if ($_h_link) : ?><a href="<?php echo htmlspecialchars($_h_link); ?>" style="text-decoration:none;display:block;height:100%"><?php endif; ?>
      <div class="hero-main" style="<?php echo $_h_bg_style . $_h_border_style; ?>">
        <?php if ($_h_shop_name) : ?>
        <span class="hero-text" style="position:absolute;left:<?php echo $_h_shop_pos_x; ?>%;top:<?php echo $_h_shop_pos_y; ?>%;font-size:<?php echo htmlspecialchars($_h_shop_size); ?>;color:<?php echo htmlspecialchars($_h_shop_color); ?>;font-weight:<?php echo htmlspecialchars($_h_shop_weight); ?>;max-width:85%"><?php echo htmlspecialchars($_h_shop_name); ?></span>
        <?php endif; ?>
        <?php if ($_h_title) : ?>
        <h2 class="hero-text<?php echo $_h_motion_cls; ?>" style="position:absolute;left:<?php echo $_h_title_pos_x; ?>%;top:<?php echo $_h_title_pos_y; ?>%;font-size:<?php echo htmlspecialchars($_h_title_size); ?>;color:<?php echo htmlspecialchars($_h_title_color); ?>;font-weight:<?php echo htmlspecialchars($_h_title_weight); ?>;max-width:85%"><?php echo htmlspecialchars($_h_title); ?></h2>
        <?php endif; ?>
        <?php if ($_h_text1) : ?>
        <p class="hero-text" style="position:absolute;left:<?php echo $_h_text1_pos_x; ?>%;top:<?php echo $_h_text1_pos_y; ?>%;font-size:<?php echo htmlspecialchars($_h_text_size); ?>;color:<?php echo htmlspecialchars($_h_text_color); ?>;font-weight:<?php echo htmlspecialchars($_h_text_weight); ?>;max-width:85%"><?php echo htmlspecialchars($_h_text1); ?></p>
        <?php endif; ?>
        <?php if ($_h_text2) : ?>
        <p class="hero-text" style="position:absolute;left:<?php echo $_h_text2_pos_x; ?>%;top:<?php echo $_h_text2_pos_y; ?>%;font-size:<?php echo htmlspecialchars($_h_text2_size); ?>;color:<?php echo htmlspecialchars($_h_text2_color); ?>;font-weight:<?php echo htmlspecialchars($_h_text2_weight); ?>;max-width:85%"><?php echo htmlspecialchars($_h_text2); ?></p>
        <?php endif; ?>
        <?php echo $_h_badge_html; ?>
      </div>
      <?php if ($_h_link) : ?></a><?php endif; ?>
    </div>
<?php endforeach; ?>
  </div>
<?php if ($_hero_total > 1) : ?>
  <div class="hero-dots" id="heroDots">
    <?php for ($_di = 0; $_di < $_hero_total; $_di++) : ?>
    <span class="hero-dot<?php echo $_di === 0 ? ' active' : ''; ?>" data-idx="<?php echo $_di; ?>"></span>
    <?php endfor; ?>
  </div>
<?php endif; ?>
</div>
<?php if ($_hero_total > 1) : ?>
<style>
.hero-slideshow { position:relative; height:190px; border-radius:14px; overflow:hidden; }
.hero-slide { position:absolute; top:0; left:0; width:100%; height:100%; transition:opacity .8s ease; }
.hero-slide .hero-main { height:100%; border-radius:0; }
.hero-dots { display:flex; justify-content:center; gap:6px; margin-top:8px; }
.hero-dot { width:8px; height:8px; border-radius:50%; background:rgba(0,0,0,.2); cursor:pointer; transition:all .3s; }
.hero-dot.active { background:var(--hot-pink,#FF1B6B); width:20px; border-radius:4px; }
@media(max-width:768px){
  .hero-slideshow { height:150px; }
}
</style>
<script>
(function(){
  var slides = document.querySelectorAll('#heroSlideshow .hero-slide');
  var dots = document.querySelectorAll('#heroDots .hero-dot');
  var total = slides.length;
  if (total < 2) return;
  var cur = 0;
  var interval = <?php echo $_hero_shuffle_sec; ?> * 1000;
  var timer;

  function show(idx) {
    slides[cur].style.opacity = '0';
    slides[cur].style.zIndex = '1';
    if (dots[cur]) dots[cur].classList.remove('active');
    cur = idx % total;
    slides[cur].style.opacity = '1';
    slides[cur].style.zIndex = '2';
    if (dots[cur]) dots[cur].classList.add('active');
  }

  function next() { show(cur + 1); }

  function start() { timer = setInterval(next, interval); }
  function stop() { clearInterval(timer); }

  dots.forEach(function(d) {
    d.addEventListener('click', function() {
      stop();
      show(parseInt(this.dataset.idx));
      start();
    });
  });

  start();
})();
</script>
<?php endif; ?>
<?php
endif;
?>
