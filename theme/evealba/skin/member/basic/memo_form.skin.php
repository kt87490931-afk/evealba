<?php
if (!defined('_GNUBOARD_')) exit;

add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
include_once(G5_THEME_PATH.'/inc/memo_header.php');
?>
<div id="memo_write" class="new_win memo-popup-wrap">
    <div class="new_win_con2">
        <form name="fmemoform" action="<?php echo $memo_action_url; ?>" onsubmit="return fmemoform_submit(this);" method="post" autocomplete="off">
        <div class="form_01 memo-form-inner">
            <h2 class="sound_only">쪽지쓰기</h2>
            <ul>
                <li>
                    <label for="me_recv_mb_id" class="memo-label">받는사람</label>
                    <div class="memo-recv-fixed">운영자</div>
                    <input type="hidden" name="me_recv_mb_id" value="<?php echo htmlspecialchars($me_recv_mb_id); ?>">
                    <?php if ($config['cf_memo_send_point']) { ?>
                    <span class="frm_info">쪽지 보낼때 <?php echo number_format($config['cf_memo_send_point']); ?>점의 포인트를 차감합니다.</span>
                    <?php } ?>
                </li>
                <li>
                    <label for="me_memo" class="sound_only">내용</label>
                    <textarea name="me_memo" id="me_memo" required class="required" placeholder="쪽지 내용을 입력해주세요 (최대 2000자)" maxlength="2000"><?php echo $content ?></textarea>
                    <span class="memo-char-count">0/2000</span>
                </li>
                <li>
                    <span class="sound_only">자동등록방지</span>
                    <?php echo captcha_html(); ?>
                </li>
            </ul>
        </div>
        <div class="win_btn">
            <button type="submit" id="btn_submit" class="btn btn_b02 reply_btn">보내기</button>
            <button type="button" onclick="if(window.history.length>1){history.back();}else{location.href='<?php echo G5_URL; ?>';} return false;" class="btn_close">뒤로</button>
        </div>
        </form>
    </div>
</div>
<script>
function fmemoform_submit(f) {
    <?php echo chk_captcha_js(); ?>
    return true;
}
document.getElementById('me_memo').addEventListener('input', function(){
    var c = this.value.length;
    var el = document.querySelector('.memo-char-count');
    if(el) el.textContent = c + '/2000';
});
</script>
