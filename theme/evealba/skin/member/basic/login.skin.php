<?php
if (!defined('_GNUBOARD_')) exit;

$_ev_login_renewal = defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI && defined('G5_IS_LOGIN_PAGE') && G5_IS_LOGIN_PAGE;
if (!$_ev_login_renewal) {
    add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
}
$_ev_reg_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . '/eve_register.php' : '/eve_register.php';
$_ev_confirm_url = G5_BBS_URL . '/member_confirm.php?url=' . urlencode(G5_BBS_URL . '/register_form.php');
?>

<?php if ($_ev_login_renewal) { ?>
<div class="breadcrumb">
  <a href="<?php echo G5_URL; ?>">🏠 메인</a>
  <span class="bc-sep">›</span>
  <span class="bc-cur">🔐 로그인</span>
</div>

<div class="form-area">
  <div class="form-card">
    <div class="form-card-head">
      <span class="fh-icon">🔐</span>
      <span class="fh-title">로그인</span>
      <span class="fh-sub">이브알바 회원 전용</span>
    </div>
    <div class="form-card-body">
      <form name="flogin" action="<?php echo $login_action_url; ?>" onsubmit="return flogin_submit(this);" method="post">
        <input type="hidden" name="url" value="<?php echo $login_url; ?>">

        <div class="form-group">
          <div class="form-label">아이디 <span class="req">*</span></div>
          <input type="text" name="mb_id" id="login_id" required class="form-input" maxlength="20" placeholder="아이디를 입력해주세요" autocomplete="username">
        </div>

        <div class="form-group">
          <div class="form-label">비밀번호 <span class="req">*</span></div>
          <div class="pw-wrap">
            <input type="password" name="mb_password" id="login_pw" required class="form-input" maxlength="20" placeholder="비밀번호를 입력해주세요" autocomplete="current-password">
            <button type="button" class="pw-toggle" data-target="login_pw" aria-label="비밀번호 표시">👁</button>
          </div>
        </div>

        <div class="auto-row" data-check="login_auto_login">
          <input type="checkbox" name="auto_login" id="login_auto_login" value="1" class="sound_only">
          <div class="auto-check" id="autoCheckVisual"></div>
          <span class="auto-label">자동 로그인</span>
        </div>

        <button type="submit" class="btn-primary">로그인</button>
      </form>

      <div class="login-links">
        <a href="<?php echo G5_BBS_URL; ?>/password_lost.php">아이디 찾기</a>
        <span class="sep">|</span>
        <a href="<?php echo G5_BBS_URL; ?>/password_lost.php">비밀번호 찾기</a>
        <span class="sep">|</span>
        <a href="<?php echo htmlspecialchars($_ev_confirm_url); ?>">비밀번호 확인</a>
      </div>

      <div class="join-cta">
        아직 회원이 아니신가요? <a href="<?php echo htmlspecialchars($_ev_reg_url); ?>">회원가입 하기 →</a>
      </div>

      <?php @include_once(get_social_skin_path() . '/social_login.skin.php'); ?>
    </div>
  </div>
</div>

<?php include G5_THEME_PATH . '/inc/renewal_footer_in_main.php'; ?>

<?php } else { ?>
<!-- 로그인 (레거시) { -->
<div id="mb_login" class="mbskin">
    <div class="mbskin_box">
        <h1><?php echo $g5['title'] ?></h1>
        <div class="mb_log_cate">
            <h2><span class="sound_only">회원</span>로그인</h2>
            <a href="<?php echo $_ev_reg_url; ?>" class="join">회원가입</a>
        </div>
        <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
        <input type="hidden" name="url" value="<?php echo $login_url ?>">
        <fieldset id="login_fs">
            <legend>회원로그인</legend>
            <label for="login_id" class="sound_only">회원아이디<strong class="sound_only"> 필수</strong></label>
            <input type="text" name="mb_id" id="login_id" required class="frm_input required" size="20" maxLength="20" placeholder="아이디">
            <label for="login_pw" class="sound_only">비밀번호<strong class="sound_only"> 필수</strong></label>
            <input type="password" name="mb_password" id="login_pw" required class="frm_input required" size="20" maxLength="20" placeholder="비밀번호">
            <button type="submit" class="btn_submit">로그인</button>
            <div id="login_info">
                <div class="login_if_auto chk_box">
                    <input type="checkbox" name="auto_login" id="login_auto_login" class="selec_chk">
                    <label for="login_auto_login"><span></span> 자동로그인</label>
                </div>
                <div class="login_if_lpl">
                    <a href="<?php echo G5_BBS_URL ?>/password_lost.php">ID/PW 찾기</a>
                </div>
            </div>
        </fieldset>
        </form>
        <?php @include_once(get_social_skin_path().'/social_login.skin.php'); ?>
    </div>
<?php } ?>

    <?php if (isset($default['de_level_sell']) && $default['de_level_sell'] == 1) { ?>

	<?php if (preg_match("/orderform.php/", $url)) { ?>
    <section id="mb_login_notmb" class="<?php echo $_ev_login_renewal ? 'form-area' : ''; ?>">
        <h2>비회원 구매</h2>
        <p>비회원으로 주문하시는 경우 포인트는 지급하지 않습니다.</p>
        <div id="guest_privacy">
            <?php echo conv_content($default['de_guest_privacy'], $config['cf_editor']); ?>
        </div>
		<div class="chk_box">
			<input type="checkbox" id="agree" value="1" class="selec_chk">
        	<label for="agree"><span></span> 개인정보수집에 대한 내용을 읽었으며 이에 동의합니다.</label>
		</div>
        <div class="btn_confirm">
            <a href="javascript:guest_submit(document.flogin);" class="btn_submit">비회원으로 구매하기</a>
        </div>
        <script>
        function guest_submit(f)
        {
            if (document.getElementById('agree')) {
                if (!document.getElementById('agree').checked) {
                    alert("개인정보수집에 대한 내용을 읽고 이에 동의하셔야 합니다.");
                    return;
                }
            }
            f.url.value = "<?php echo $url; ?>";
            f.action = "<?php echo $url; ?>";
            f.submit();
        }
        </script>
    </section>

    <?php } else if (preg_match("/orderinquiry.php$/", $url)) { ?>
    <div id="mb_login_od_wr" class="<?php echo $_ev_login_renewal ? 'form-area' : ''; ?>">
        <h2>비회원 주문조회 </h2>
        <fieldset id="mb_login_od">
            <legend>비회원 주문조회</legend>
            <form name="forderinquiry" method="post" action="<?php echo urldecode($url); ?>" autocomplete="off">
            <label for="od_id" class="od_id sound_only">주문서번호<strong class="sound_only"> 필수</strong></label>
            <input type="text" name="od_id" value="<?php echo get_text($od_id); ?>" id="od_id" required class="frm_input required" size="20" placeholder="주문서번호">
            <label for="od_pwd" class="od_pwd sound_only">비밀번호 <strong>필수</strong></label>
            <input type="password" name="od_pwd" size="20" id="od_pwd" required class="frm_input required" placeholder="비밀번호">
            <button type="submit" class="btn_submit">확인</button>
            </form>
        </fieldset>
        <section id="mb_login_odinfo">
            <p>메일로 발송해드린 주문서의 <strong>주문번호</strong> 및 주문 시 입력하신 <strong>비밀번호</strong>를 정확히 입력해주십시오.</p>
        </section>
    </div>
    <?php } ?>

    <?php } ?>

<?php if (!$_ev_login_renewal) { ?>
</div>
<?php } ?>

<script>
function flogin_submit(f)
{
    if (typeof jQuery !== 'undefined' && jQuery(document.body).triggerHandler('login_sumit', [f, 'flogin']) === false) {
        return false;
    }
    return true;
}
</script>
