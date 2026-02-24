<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
include_once(G5_THEME_PATH.'/inc/memo_header.php');
?>
<div id="memo_list" class="new_win memo-popup-wrap">
    <div class="new_win_con2">
        <div class="memo-list-actions">
            <a href="./memo_form.php" class="memo-btn-write">쪽지 쓰기</a>
        </div>
        <div class="memo_list">
            <ul>
            <?php
            for ($i=0; $i<count($list); $i++) {
                $readed = (substr($list[$i]['me_read_datetime'],0,1) == 0) ? '' : 'read';
                $memo_preview = utf8_strcut(strip_tags($list[$i]['me_memo']), 30, '..');
            ?>
            <li class="<?php echo $readed; ?>">
                <div class="memo_li profile_big_img">
                    <?php echo get_member_profile_img($list[$i]['mb_id']); ?>
                    <?php if (! $readed){ ?><span class="no_read">안 읽은 쪽지</span><?php } ?>
                </div>
                <div class="memo_li memo_name">
                    <?php echo $list[$i]['name']; ?> <span class="memo_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['send_datetime']; ?></span>
                    <div class="memo_preview">
                        <a href="<?php echo $list[$i]['view_href']; ?>"><?php echo $memo_preview; ?></a>
                    </div>
                </div>
                <a href="<?php echo $list[$i]['del_href']; ?>" onclick="del(this.href); return false;" class="memo_del"><i class="fa fa-trash-o" aria-hidden="true"></i> <span class="sound_only">삭제</span></a>
            </li>
            <?php } ?>
            <?php if ($i==0) { echo '<li class="empty_table">자료가 없습니다.</li>'; } ?>
            </ul>
        </div>
        <?php echo $write_pages; ?>
        <p class="win_desc"><i class="fa fa-info-circle" aria-hidden="true"></i> 쪽지 보관일수는 최장 <strong><?php echo $config['cf_memo_del'] ?></strong>일 입니다.</p>
        <div class="win_btn">
            <button type="button" onclick="window.close();" class="btn_close">창닫기</button>
        </div>
    </div>
</div>
