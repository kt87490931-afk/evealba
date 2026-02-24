<?php
if (!defined('_GNUBOARD_')) exit;
$nick = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage']);
if($kind == "recv") {
    $kind_str = "보낸";
    $kind_date = "받은";
} else {
    $kind_str = "받는";
    $kind_date = "보낸";
}
$memo_recv_count = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_recv_mb_id = '{$member['mb_id']}' AND me_type='recv'")['cnt'];
$memo_unread_count = function_exists('get_memo_not_read') ? get_memo_not_read($member['mb_id']) : 0;
$memo_send_count = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_send_mb_id = '{$member['mb_id']}' AND me_type='send'")['cnt'];
$member_type = (isset($member['mb_2']) && (strpos($member['mb_2'], 'biz') !== false || $member['mb_2'] === '기업')) ? '기업회원' : '일반회원';
$memo_current_tab = $kind;
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
include_once(G5_THEME_PATH.'/inc/memo_header.php');
?>
<!-- 쪽지보기 시작 { -->
<div id="memo_view" class="new_win memo-popup-wrap">
    <div class="new_win_con2">
        <article id="memo_view_contents">
            <header>
                <h2>쪽지 내용</h2>
            </header>
            <div id="memo_view_ul">
                <div class="memo_view_li memo_view_name">
                	<ul class="memo_from">
                		<li class="memo_profile">
				            <?php echo get_member_profile_img($mb['mb_id']); ?>
				        </li>
						<li class="memo_view_nick"><?php echo $nick ?></li>
						<li class="memo_view_date"><span class="sound_only"><?php echo $kind_date ?>시간</span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $memo['me_send_datetime'] ?></li> 
						<li class="memo_op_btn list_btn"><a href="<?php echo $list_link ?>" class="btn_b01 btn"><i class="fa fa-list" aria-hidden="true"></i><span class="sound_only">목록</span></a></li>
						<li class="memo_op_btn del_btn"><a href="<?php echo $del_link; ?>" onclick="del(this.href); return false;" class="memo_del btn_b01 btn"><i class="fa fa-trash-o" aria-hidden="true"></i> <span class="sound_only">삭제</span></a></li>	
					</ul>
                    <div class="memo_btn">
                    	<?php if($prev_link) {  ?>
			            <a href="<?php echo $prev_link ?>" class="btn_left"><i class="fa fa-chevron-left" aria-hidden="true"></i> 이전쪽지</a>
			            <?php }  ?>
			            <?php if($next_link) {  ?>
			            <a href="<?php echo $next_link ?>" class="btn_right">다음쪽지 <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
			            <?php }  ?>  
                    </div>
                </div>
            </div>
            <p>
                <?php echo conv_content($memo['me_memo'], 0) ?>
            </p>
        </article>
		<div class="win_btn">
			<?php if ($kind == 'recv') {  ?><a href="./memo_form.php?me_id=<?php echo $memo['me_id'] ?>" class="reply_btn">답장</a><?php }  ?>
			<button type="button" onclick="window.close();" class="btn_close">창닫기</button>
    	</div>
    </div>
</div>
<!-- } 쪽지보기 끝 -->