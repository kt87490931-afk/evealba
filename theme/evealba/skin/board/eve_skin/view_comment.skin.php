<?php
if (!defined('_GNUBOARD_')) exit;
?>
<script>var char_min = parseInt(<?php echo $comment_min; ?>); var char_max = parseInt(<?php echo $comment_max; ?>);</script>

<div class="comment-wrap">
    <div class="comment-head">
        <span class="comment-head-title">💬 댓글</span>
        <span class="comment-count-badge"><?php echo number_format($view['wr_comment']); ?>개</span>
    </div>

    <section id="bo_vc">
        <h2 class="sound_only">댓글목록</h2>
        <div class="comment-list">
        <?php
        $cmt_amt = count($list);
        for ($i=0; $i<$cmt_amt; $i++) {
            $comment_id = $list[$i]['wr_id'];
            $cmt_depth = strlen($list[$i]['wr_comment_reply']);
            $c_reply_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w';
            $c_edit_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w';
            $is_comment_reply_edit = ($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) ? 1 : 0;
        ?>
        <div class="comment-item<?php echo $cmt_depth ? ' reply-item' : ''; ?>" id="c_<?php echo $comment_id; ?>">
            <div class="comment-top">
                <div class="comment-avatar<?php echo ($list[$i]['mb_id'] == $write['mb_id']) ? ' op' : ''; ?>"><?php echo get_member_profile_img($list[$i]['mb_id']) ?: '👤'; ?></div>
                <span class="comment-nick"><?php echo get_text($list[$i]['wr_name']); ?></span>
                <?php if ($list[$i]['mb_id'] == $write['mb_id']) { ?><span class="comment-badge-op">글쓴이</span><?php } ?>
                <span class="comment-date"><?php echo $list[$i]['datetime']; ?></span>
            </div>
            <div class="comment-text"><?php echo $list[$i]['content']; ?></div>
            <?php if ($is_comment_reply_edit) { ?>
            <div class="comment-actions">
                <?php if ($list[$i]['is_reply']) { ?><a href="<?php echo $c_reply_href; ?>" onclick="comment_box('<?php echo $comment_id; ?>', 'c'); return false;" class="comment-btn">💬 답글</a><?php } ?>
                <?php if ($list[$i]['is_edit']) { ?><a href="<?php echo $c_edit_href; ?>" onclick="comment_box('<?php echo $comment_id; ?>', 'cu'); return false;" class="comment-btn">✏️ 수정</a><?php } ?>
                <?php if ($list[$i]['is_del']) { ?><a href="<?php echo $list[$i]['del_link']; ?>" onclick="return comment_delete();" class="comment-btn">🗑 삭제</a><?php } ?>
            </div>
            <?php } ?>
            <span id="edit_<?php echo $comment_id; ?>" class="bo_vc_w"></span>
            <span id="reply_<?php echo $comment_id; ?>" class="bo_vc_w"></span>
            <input type="hidden" value="<?php echo strstr($list[$i]['wr_option'],'secret') ? 1 : 0; ?>" id="secret_comment_<?php echo $comment_id; ?>">
            <textarea id="save_comment_<?php echo $comment_id; ?>" style="display:none"><?php echo get_text($list[$i]['content1'], 0); ?></textarea>
        </div>
        <?php } ?>
        <?php if ($cmt_amt == 0) { ?><p class="comment-empty">등록된 댓글이 없습니다.</p><?php } ?>
        </div>
    </section>

    <?php if ($is_comment_write) {
        if ($w == '') $w = 'c';
    ?>
    <div class="comment-form" id="bo_vc_w">
        <h2 class="sound_only">댓글쓰기</h2>
        <form name="fviewcomment" id="fviewcomment" action="<?php echo $comment_action_url; ?>" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="w" value="<?php echo $w; ?>" id="w">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
        <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
        <input type="hidden" name="comment_id" value="<?php echo $c_id; ?>" id="comment_id">
        <input type="hidden" name="sca" value="<?php echo $sca; ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
        <input type="hidden" name="stx" value="<?php echo $stx; ?>">
        <input type="hidden" name="spt" value="<?php echo $spt; ?>">
        <input type="hidden" name="page" value="<?php echo $page; ?>">
        <input type="hidden" name="is_good" value="">
        <div class="comment-form-top">
            <div class="comment-form-avatar"><?php echo $is_member ? get_member_profile_img($member['mb_id']) : '🌸'; ?></div>
            <span class="comment-form-nick"><?php echo $is_member ? get_text($member['mb_nick']) : '비회원'; ?> 님</span>
        </div>
        <textarea id="wr_content" name="wr_content" class="comment-textarea" maxlength="10000" required placeholder="댓글을 입력해주세요" <?php if ($comment_min || $comment_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?php } ?>><?php echo $c_wr_content; ?></textarea>
        <?php if ($comment_min || $comment_max) { ?><script>check_byte('wr_content', 'char_count');</script><?php } ?>
        <div class="comment-form-bottom">
            <span class="comment-form-left"><?php if ($comment_min || $comment_max) { ?><span id="char_count"></span>글자<?php } ?></span>
            <?php if ($is_guest) { echo $captcha_html; } ?>
            <button type="submit" id="btn_submit" class="btn-comment-submit">💬 댓글 등록</button>
        </div>
        <?php if ($is_guest) { ?>
        <div style="margin-top:8px;">
            <input type="text" name="wr_name" value="<?php echo get_cookie('ck_sns_name'); ?>" id="wr_name" required placeholder="이름" class="wi-input">
            <input type="password" name="wr_password" id="wr_password" required placeholder="비밀번호" class="wi-input">
        </div>
        <?php } ?>
        <input type="checkbox" name="wr_secret" value="secret" id="wr_secret"> <label for="wr_secret">비밀글</label>
        </form>
    </div>

    <script>
    var save_before = '';
    var save_html = document.getElementById('bo_vc_w') ? document.getElementById('bo_vc_w').innerHTML : '';

    function fviewcomment_submit(f) {
        f.is_good.value = 0;
        var content = ""; $.ajax({url: g5_bbs_url+"/ajax.filter.php", type: "POST", data: { subject: "", content: f.wr_content.value }, dataType: "json", async: false, success: function(d){ content = d.content; }});
        if (content) { alert("내용에 금지단어('"+content+"')가 포함되어있습니다"); return false; }
        if (typeof char_min !== 'undefined' && char_min > 0) { var c = parseInt(document.getElementById('char_count').innerHTML); if (char_min > c) { alert("댓글은 "+char_min+"글자 이상 입력하세요."); return false; }}
        if (typeof char_max !== 'undefined' && char_max > 0) { var c = parseInt(document.getElementById('char_count').innerHTML); if (char_max < c) { alert("댓글은 "+char_max+"글자 이하로 입력하세요."); return false; }}
        if (!f.wr_content.value.replace(/\s/g,'')) { alert("댓글을 입력하세요."); return false; }
        <?php if ($is_guest) echo chk_captcha_js(); ?>
        set_comment_token(f);
        document.getElementById("btn_submit").disabled = "disabled";
        return true;
    }
    function comment_box(comment_id, work) {
        var el_id = comment_id ? (work=='c' ? 'reply_'+comment_id : 'edit_'+comment_id) : 'bo_vc_w';
        var respond = document.getElementById('fviewcomment');
        if (!respond) return;
        var target = document.getElementById(el_id);
        if (target && save_before != el_id) {
            if (save_before) document.getElementById(save_before).style.display = 'none';
            target.style.display = '';
            target.appendChild(respond);
            document.getElementById('wr_content').value = '';
            if (work == 'cu' && comment_id) {
                var ta = document.getElementById('save_comment_'+comment_id);
                if (ta) document.getElementById('wr_content').value = ta.value;
            }
            document.getElementById('comment_id').value = comment_id || '';
            document.getElementById('w').value = work;
            save_before = el_id;
        }
    }
    function comment_delete() { return confirm("이 댓글을 삭제하시겠습니까?"); }
    comment_box('', 'c');
    </script>
    <?php } ?>
</div>
