<?php
if (!defined('_GNUBOARD_')) exit;

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 분류: 어드민 게시판수정 > 분류(bo_category_list) 연동
$ev_categories = array();
if ($is_category && $board['bo_category_list']) {
    $ev_categories = array_filter(array_map('trim', explode('|', $board['bo_category_list'])));
}
?>

<section id="bo_w" class="ev-write-wrap">
    <h2 class="sound_only"><?php echo $g5['title']; ?></h2>

    <form name="fwrite" id="fwrite" action="<?php echo $action_url; ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>">
    <input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="spt" value="<?php echo $spt; ?>">
    <input type="hidden" name="sst" value="<?php echo $sst; ?>">
    <input type="hidden" name="sod" value="<?php echo $sod; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <?php
    $option_hidden = '';
    if ($is_dhtml_editor) $option_hidden .= '<input type="hidden" value="html1" name="html">';
    if ($is_secret && $is_secret==1) $option_hidden .= '<input type="hidden" name="secret" value="secret">';
    echo $option_hidden;
    ?>

    <div class="section-topbar">
        <h2 class="page-title">✏️ 게시물 등록하기</h2>
        <span class="page-subtitle"><?php echo $board['bo_subject']; ?></span>
    </div>

    <div class="write-wrap">
        <div class="write-head">
            <span class="write-head-icon">✏️</span>
            <span class="write-head-title">게시물 등록하기</span>
            <span class="write-head-sub">* 이미지 첨부 시 추천 +5 적용</span>
        </div>

        <div class="write-notice">
            <ul>
                <li>커뮤니티 정책과 맞지 않는 게시물의 경우 블라인드 또는 삭제될 수 있습니다.</li>
                <li class="hl">글 작성 시 이미지를 첨부하면 추천 +5가 적용됩니다.</li>
            </ul>
        </div>

        <div class="write-body">

            <?php if ($is_category && count($ev_categories) > 0) { ?>
            <div class="write-row">
                <div class="write-label">카테고리 <span class="req">*</span></div>
                <div class="write-cell">
                    <div class="cat-select-row">
                        <input type="radio" class="cat-radio-btn" name="ca_name" id="cat_empty" value="" <?php echo empty($write['ca_name']) ? 'checked' : ''; ?>>
                        <label class="cat-radio-label" for="cat_empty">전체</label>
                        <?php foreach ($ev_categories as $cat) {
                            $cat_id = 'cat_'.preg_replace('/[^a-zA-Z0-9가-힣]/', '_', $cat);
                        ?>
                        <input type="radio" class="cat-radio-btn" name="ca_name" id="<?php echo $cat_id; ?>" value="<?php echo htmlspecialchars($cat); ?>" <?php echo (isset($write['ca_name']) && $write['ca_name'] == $cat) ? 'checked' : ''; ?>>
                        <label class="cat-radio-label" for="<?php echo $cat_id; ?>"><?php echo htmlspecialchars($cat); ?></label>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($is_name) { ?>
            <div class="write-row">
                <div class="write-label">작성인</div>
                <div class="write-cell">
                    <input type="text" name="wr_name" value="<?php echo $name; ?>" id="wr_name" required class="wi-input wi-input-sm" placeholder="이름">
                </div>
            </div>
            <?php } ?>

            <?php if ($is_password) { ?>
            <div class="write-row">
                <div class="write-label">비밀번호</div>
                <div class="write-cell">
                    <input type="password" name="wr_password" id="wr_password" <?php echo $password_required; ?> class="wi-input wi-pw" placeholder="비밀번호 (비회원용)">
                </div>
            </div>
            <?php } ?>

            <?php if ($is_secret && ($is_admin || $is_secret==1)) { ?>
            <div class="write-row">
                <div class="write-label">글잠금여부</div>
                <div class="write-cell">
                    <div class="wi-check-row">
                        <input type="checkbox" class="wi-checkbox" id="secret" name="secret" value="secret" <?php echo $secret_checked; ?>>
                        <label class="wi-check-label" for="secret">🔒 비밀글</label>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="write-row">
                <div class="write-label">제목 <span class="req">*</span></div>
                <div class="write-cell">
                    <input type="text" name="wr_subject" value="<?php echo $subject; ?>" id="wr_subject" required class="wi-input" placeholder="제목을 입력해주세요">
                    <?php if ($is_member && $is_member) { ?>
                    <div id="autosave_wrapper">
                        <script src="<?php echo G5_JS_URL; ?>/autosave.js"></script>
                        <?php if($editor_content_js) echo $editor_content_js; ?>
                        <button type="button" id="btn_autosave" class="btn_frmline">임시저장 (<span id="autosave_count"><?php echo $autosave_count; ?></span>)</button>
                        <div id="autosave_pop"><strong>임시 저장 목록</strong><ul></ul><div><button type="button" class="autosave_close">닫기</button></div></div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="write-row">
                <div class="write-label">내용 <span class="req">*</span></div>
                <div class="write-cell" style="flex-direction:column;align-items:stretch;">
                    <div class="wr_content <?php echo $is_dhtml_editor ? $config['cf_editor'] : ''; ?>">
                        <?php if($write_min || $write_max) { ?>
                        <p id="char_count_desc">최소 <?php echo $write_min; ?>글자 이상, 최대 <?php echo $write_max; ?>글자 이하</p>
                        <?php } ?>
                        <?php echo $editor_html; ?>
                        <?php if($write_min || $write_max) { ?>
                        <div id="char_count_wrap"><span id="char_count"></span>글자</div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php for ($i=0; $is_file && $i<$file_count; $i++) { ?>
            <div class="write-row">
                <div class="write-label">첨부<?php echo $i+1; ?></div>
                <div class="write-cell">
                    <input type="file" name="bf_file[]" id="bf_file_<?php echo $i+1; ?>" title="파일첨부 <?php echo $i+1; ?>" class="frm_file">
                    <?php if ($w == 'u' && $file[$i]['file']) { ?>
                    <label><input type="checkbox" name="bf_file_del[<?php echo $i; ?>]" value="1"> <?php echo $file[$i]['source']; ?> 삭제</label>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <?php if ($is_use_captcha) { ?>
            <div class="write-row">
                <div class="write-label">도배방지</div>
                <div class="write-cell">
                    <?php echo $captcha_html; ?>
                </div>
            </div>
            <?php } ?>

        </div>

        <div class="write-actions">
            <a href="<?php echo get_pretty_url($bo_table); ?>" class="btn-back-list">📋 목록</a>
            <button type="button" class="btn-re-write" onclick="document.getElementById('fwrite').reset();">🔄 다시쓰기</button>
            <button type="submit" id="btn_submit" class="btn-submit">◈ 등록</button>
        </div>
    </div>
    </form>

    <script>
    <?php if($write_min || $write_max) { ?>
    var char_min = parseInt(<?php echo $write_min; ?>);
    var char_max = parseInt(<?php echo $write_max; ?>);
    check_byte("wr_content", "char_count");
    <?php } ?>

    function fwrite_submit(f) {
        <?php echo $editor_js; ?>
        <?php echo $captcha_js; ?>
        document.getElementById("btn_submit").disabled = "disabled";
        return true;
    }
    </script>
</section>
