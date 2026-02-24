<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<article id="bo_v" class="ev-view-wrap" style="width:<?php echo $width; ?>">

    <div class="font-size-bar">
        <button type="button" class="btn-font" onclick="ev_changeFontSize(16,this)">글자크게</button>
        <button type="button" class="btn-font active" onclick="ev_changeFontSize(14,this)">글자작게</button>
    </div>

    <div class="view-wrap">
        <div class="view-head">
            <span class="view-head-icon">📄</span>
            <span class="view-head-title"><?php echo $board['bo_subject']; ?></span>
            <span class="view-head-sub">상세보기</span>
        </div>

        <div class="view-meta">
            <div class="view-meta-title-row">
                <?php if ($category_name) { ?>
                <span class="vm-cat-badge"><?php echo $view['ca_name']; ?></span>
                <?php } ?>
                <span class="vm-title"><?php echo cut_str(get_text($view['wr_subject']), 70); ?></span>
                <?php if ($view['wr_ip']) { ?><span class="vm-badge-mobile">mobile</span><?php } ?>
            </div>
            <div class="view-meta-info">
                <div class="vm-info-item">
                    <span class="vm-info-icon">✍️</span>
                    <span>작성인</span>
                    <span class="vm-info-val pink"><?php echo $view['name']; ?></span>
                </div>
                <div class="vm-info-item">
                    <span class="vm-info-icon">📅</span>
                    <span>등록일</span>
                    <span class="vm-info-val"><?php echo date('Y-m-d', strtotime($view['wr_datetime'])); ?></span>
                </div>
                <div class="vm-info-item">
                    <span class="vm-info-icon">👁</span>
                    <span>조회수</span>
                    <span class="vm-info-val"><?php echo number_format($view['wr_hit']); ?></span>
                </div>
                <?php if ($board['bo_use_good']) { ?>
                <div class="vm-info-item">
                    <span class="vm-info-icon">❤️</span>
                    <span>추천</span>
                    <span class="vm-info-val pink"><?php echo number_format($view['wr_good']); ?></span>
                </div>
                <?php } ?>
                <div class="vm-info-item">
                    <span class="vm-info-icon">💬</span>
                    <span>댓글</span>
                    <span class="vm-info-val pink"><?php echo number_format($view['wr_comment']); ?></span>
                </div>
            </div>
        </div>

        <div id="bo_v_atc">
        <div class="view-content" id="viewContent"><?php echo get_view_thumbnail($view['content']); ?></div>
        </div>

        <?php if ($board['bo_use_good'] || $board['bo_use_nogood']) { ?>
        <div class="view-rec-area">
            <?php if ($good_href) { ?>
            <a href="<?php echo $good_href.'&amp;'.$qstr; ?>" id="good_button" class="btn-rec">
                <span>👍</span> 추천하기
            </a>
            <?php } ?>
            <div class="rec-count">
                <span>❤️</span>
                <span class="num"><?php echo number_format($view['wr_good']); ?></span>
            </div>
            <?php if ($nogood_href) { ?>
            <a href="<?php echo $nogood_href.'&amp;'.$qstr; ?>" id="nogood_button" class="btn-unrec">
                <span>👎</span> 비추천
            </a>
            <?php } ?>
        </div>
        <?php } ?>

        <div class="view-notices">
            <p>* 커뮤니티 정책과 맞지 않는 게시물의 경우 블라인드 또는 삭제될 수 있습니다.</p>
        </div>

        <div class="view-actions">
            <a href="<?php echo $list_href; ?>" class="btn-action btn-list2">📋 목록</a>
            <?php if ($update_href) { ?><a href="<?php echo $update_href; ?>" class="btn-action btn-edit">✏️ 수정</a><?php } ?>
            <?php if ($delete_href) { ?><a href="<?php echo $delete_href; ?>" onclick="del(this.href); return false;" class="btn-action btn-delete">🗑 삭제</a><?php } ?>
            <?php if ($write_href) { ?><a href="<?php echo $write_href; ?>" class="btn-action btn-write2">✍️ 글쓰기</a><?php } ?>
        </div>
    </div>

    <?php if ($prev_href || $next_href) { ?>
    <div class="view-nav">
        <?php if ($prev_href) { ?>
        <div class="view-nav-row">
            <div class="vn-label vn-prev">⬆ 이전글</div>
            <a href="<?php echo $prev_href; ?>" class="vn-title"><?php echo $prev_wr_subject; ?></a>
            <div class="vn-date"><?php echo str_replace('-', '.', substr($prev_wr_date, 2, 8)); ?></div>
        </div>
        <?php } ?>
        <?php if ($next_href) { ?>
        <div class="view-nav-row">
            <div class="vn-label vn-next">⬇ 다음글</div>
            <a href="<?php echo $next_href; ?>" class="vn-title"><?php echo $next_wr_subject; ?></a>
            <div class="vn-date"><?php echo str_replace('-', '.', substr($next_wr_date, 2, 8)); ?></div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

    <?php
    include_once(G5_BBS_PATH.'/view_comment.php');
    ?>

    <div class="view-bottom-btns">
        <a href="<?php echo $list_href; ?>" class="btn-bottom btn-b-list">📋 목록으로</a>
        <?php if ($write_href) { ?><a href="<?php echo $write_href; ?>" class="btn-bottom btn-b-write">✍️ 글쓰기</a><?php } ?>
    </div>

</article>

<?php
$cnt = 0;
if ($view['file']['count']) {
    for ($i=0; $i<count($view['file']); $i++) {
        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) $cnt++;
    }
}
if ($cnt) {
?>
<section id="bo_v_file" class="ev-v-file">
    <h2>첨부파일</h2>
    <ul>
    <?php
    for ($i=0; $i<count($view['file']); $i++) {
        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
    ?>
    <li>
        <a href="<?php echo $view['file'][$i]['href']; ?>" class="view_file_download">
            <strong><?php echo $view['file'][$i]['source']; ?></strong> (<?php echo $view['file'][$i]['size']; ?>)
        </a>
    </li>
    <?php
        }
    }
    ?>
    </ul>
</section>
<?php } ?>

<script>
function ev_changeFontSize(size, btn) {
    var el = document.getElementById('viewContent');
    if (el) el.style.fontSize = size + 'px';
    var btns = document.querySelectorAll('.btn-font');
    for (var i=0; i<btns.length; i++) btns[i].classList.remove('active');
    if (btn) btn.classList.add('active');
}

$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });
    $("#bo_v_atc").viewimageresize();
    <?php if ($board['bo_use_good'] || $board['bo_use_nogood']) { ?>
    $("#good_button, #nogood_button").click(function() {
        var $tx = $(this).hasClass('btn-rec') ? $("#bo_v_act_good") : $("#bo_v_act_nogood");
        excute_good(this.href, $(this), $tx);
        return false;
    });
    <?php } ?>
});

<?php if ($board['bo_use_good'] || $board['bo_use_nogood']) { ?>
function excute_good(href, $el, $tx) {
    $.post(href, { js: "on" }, function(data) {
        if (data.error) { alert(data.error); return; }
        if (data.count) {
            $(".rec-count .num").text(data.count);
            if ($tx.length) $tx.text("이 글을 추천하셨습니다.").fadeIn(200).delay(2500).fadeOut(200);
        }
    }, "json");
}
<?php } ?>
</script>
