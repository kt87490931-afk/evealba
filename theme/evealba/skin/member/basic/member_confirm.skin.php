<?php
if (!defined('_GNUBOARD_')) exit;

$_ev_confirm_renewal = defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI && defined('G5_IS_MEMBER_CONFIRM_PAGE') && G5_IS_MEMBER_CONFIRM_PAGE;
if (!$_ev_confirm_renewal) {
    add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
}
$_ev_login_url = G5_BBS_URL . '/login.php';
?>

<?php if ($_ev_confirm_renewal) { ?>
<div class="breadcrumb">
  <a href="<?php echo G5_URL; ?>">🏠 메인</a>
  <span class="bc-sep">›</span>
  <a href="<?php echo $_ev_login_url; ?>">로그인</a>
  <span class="bc-sep">›</span>
  <span class="bc-cur">🔒 비밀번호 확인</span>
</div>

<div class="form-area">
  <div class="form-card">
    <div class="form-card-head">
      <span class="fh-icon">🔒</span>
      <span class="fh-title">회원 비밀번호 확인</span>
    </div>
    <div class="form-card-body">

      <div class="confirm-info">
        <div class="ci-icon">🛡️</div>
        <div class="ci-text">
          <?php if ($url == 'member_leave.php') { ?>
          비밀번호를 입력하시면 <strong>회원탈퇴가 완료</strong>됩니다.
          <?php } else { ?>
          회원님의 정보를 안전하게 보호하기 위해<br>
          <strong>비밀번호를 한번 더 확인합니다.</strong>
          <?php } ?>
        </div>
      </div>

      <div class="id-display">
        <div>
          <div class="id-label">회원아이디</div>
          <div class="id-value"><?php echo get_text($member['mb_id']); ?></div>
        </div>
      </div>

      <form name="fmemberconfirm" action="<?php echo $url; ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
        <input type="hidden" name="mb_id" value="<?php echo $member['mb_id']; ?>">
        <input type="hidden" name="w" value="u">

        <div class="form-group">
          <div class="form-label">비밀번호 <span class="req">*</span></div>
          <div class="pw-wrap">
            <input type="password" name="mb_password" id="confirm_mb_password" required class="form-input" maxlength="20" placeholder="비밀번호를 입력해주세요" autocomplete="current-password">
            <button type="button" class="pw-toggle" data-target="confirm_mb_password" aria-label="비밀번호 표시">👁</button>
          </div>
        </div>

        <button type="submit" class="btn-primary" id="btn_submit">확인</button>
        <a href="<?php echo $_ev_login_url; ?>" class="btn-dark" style="display:block;text-align:center;text-decoration:none;">← 로그인으로 돌아가기</a>
      </form>

    </div>
  </div>
</div>

<?php include G5_THEME_PATH . '/inc/renewal_footer_in_main.php'; ?>

<?php } else { ?>
<!-- 회원 비밀번호 확인 (레거시) { -->
<div id="mb_confirm" class="mbskin">
    <h1><?php echo $g5['title'] ?></h1>
    <p>
        <strong>비밀번호를 한번 더 입력해주세요.</strong>
        <?php if ($url == 'member_leave.php') { ?>
        비밀번호를 입력하시면 회원탈퇴가 완료됩니다.
        <?php } else { ?>
        회원님의 정보를 안전하게 보호하기 위해 비밀번호를 한번 더 확인합니다.
        <?php }  ?>
    </p>
    <form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
    <input type="hidden" name="w" value="u">
    <fieldset>
        <span class="confirm_id">회원아이디</span>
        <span id="mb_confirm_id"><?php echo $member['mb_id'] ?></span>
        <label for="confirm_mb_password" class="sound_only">비밀번호<strong>필수</strong></label>
        <input type="password" name="mb_password" id="confirm_mb_password" required class="required frm_input" size="15" maxLength="20" placeholder="비밀번호">
        <input type="submit" value="확인" id="btn_submit" class="btn_submit">
    </fieldset>
    </form>
</div>
<?php } ?>

<script>
function fmemberconfirm_submit(f)
{
    var btn = document.getElementById('btn_submit');
    if (btn) btn.disabled = true;
    return true;
}
</script>
