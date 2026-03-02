<?php
/**
 * 히어로 배너 (공통 include)
 * - DB(g5_special_banner)에서 active 히어로배너를 로드하여 동적 렌더링
 * - 메인/채용정보 등 모든 페이지에서 동일 노출
 */
if (!defined('_GNUBOARD_')) exit;

$_hero_sb_table = $g5['prefix'] . 'special_banner';

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
$_hero_check = sql_query("SHOW TABLES LIKE '{$_hero_sb_table}'");
if ($_hero_check && sql_num_rows($_hero_check) > 0) {
    $_hero_res = sql_query("SELECT * FROM {$_hero_sb_table} WHERE sb_type = 'hero' AND sb_status = 'active' ORDER BY sb_position ASC LIMIT 10");
    while ($_hr = sql_fetch_array($_hero_res)) {
        $_hero_rows[] = $_hr;
    }
}

if (!empty($_hero_rows)) :
foreach ($_hero_rows as $_hb) :
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
    $_h_pos_x        = isset($_hd['text_pos_x']) ? (float)$_hd['text_pos_x'] : 3;
    $_h_pos_y        = isset($_hd['text_pos_y']) ? (float)$_hd['text_pos_y'] : 50;

    // background style
    $_h_bg_style = 'background:' . $_h_gradient . ';';
    if ($_h_wave) {
        preg_match_all('/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/', $_h_gradient, $_h_m);
        if (!empty($_h_m[0]) && count($_h_m[0]) >= 2) {
            $c = $_h_m[0];
            $_h_bg_style = 'background:linear-gradient(135deg,'.$c[0].','.$c[1].','.(isset($c[2])?$c[2]:$c[0]).','.$c[0].','.$c[1].');background-size:400% 400%;animation:heroWave 3s ease infinite;';
        }
    }

    // border style
    $_h_border_style = '';
    if ($_h_border && isset($_hero_border_colors[$_h_border])) {
        $_bc = $_hero_border_colors[$_h_border];
        $_h_border_style = 'box-shadow:inset 0 0 0 2px '.$_bc.', 0 0 0 2px '.$_bc.', 0 2px 8px rgba(0,0,0,.10);';
    }

    // link
    $_h_link = !empty($_hb['sb_link']) ? $_hb['sb_link'] : '';
    if (!$_h_link && !empty($_hb['sb_jr_id'])) {
        $_h_link = G5_URL . '/jobs_view.php?jr_id=' . (int)$_hb['sb_jr_id'];
    }

    // badge
    $_h_badge_html = '';
    if ($_h_icon && isset($_hero_icons[$_h_icon])) {
        $_h_badge_html = '<div class="hero-badge" style="background:'.htmlspecialchars($_hero_icons[$_h_icon]['bg']).'">'.htmlspecialchars($_hero_icons[$_h_icon]['label']).'</div>';
    }

    // motion class
    $_h_motion_cls = $_h_motion ? ' pv-motion-'.htmlspecialchars($_h_motion) : '';
?>
<!-- 히어로 배너 (#<?php echo (int)$_hb['sb_id']; ?>) -->
<div class="hero-section">
  <?php if ($_h_link) : ?><a href="<?php echo htmlspecialchars($_h_link); ?>" style="text-decoration:none;display:block"><?php endif; ?>
  <div class="hero-main" style="<?php echo $_h_bg_style . $_h_border_style; ?>">
    <div class="hero-text" style="position:absolute;left:<?php echo $_h_pos_x; ?>%;top:<?php echo $_h_pos_y; ?>%;transform:translateY(-50%);max-width:85%">
      <h2 class="<?php echo $_h_motion_cls; ?>" style="font-size:<?php echo htmlspecialchars($_h_title_size); ?>;color:<?php echo htmlspecialchars($_h_title_color); ?>;font-weight:<?php echo htmlspecialchars($_h_title_weight); ?>"><?php echo htmlspecialchars($_h_title); ?></h2>
      <?php if ($_h_text1) : ?>
      <p style="font-size:<?php echo htmlspecialchars($_h_text_size); ?>;color:<?php echo htmlspecialchars($_h_text_color); ?>;font-weight:<?php echo htmlspecialchars($_h_text_weight); ?>"><?php echo htmlspecialchars($_h_text1); ?></p>
      <?php endif; ?>
      <?php if ($_h_text2) : ?>
      <p style="font-size:<?php echo htmlspecialchars($_h_text2_size); ?>;color:<?php echo htmlspecialchars($_h_text2_color); ?>;font-weight:<?php echo htmlspecialchars($_h_text2_weight); ?>;margin-top:4px"><?php echo htmlspecialchars($_h_text2); ?></p>
      <?php endif; ?>
    </div>
    <?php echo $_h_badge_html; ?>
  </div>
  <?php if ($_h_link) : ?></a><?php endif; ?>
</div>
<?php
endforeach;
endif;
?>
