<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/shop.tail.php');
    return;
}

?>
    </div>
</div>


<?php echo poll('theme/basic'); // 설문조사 ?>
<?php echo visit('theme/basic'); // 방문자수 ?>


<div id="ft">
    <div id="ft_copy">
        <div id="ft_company">
            <a href="<?php echo get_pretty_url('content', 'company'); ?>">회사소개</a>
            <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
            <a href="<?php echo get_pretty_url('content', 'provision'); ?>">서비스이용약관</a>
        </div>
        Copyright &copy; <b>소유하신 도메인.</b> All rights reserved.<br>
    </div>
    <div class="ft_cnt">
    	<h2>사이트 정보</h2>
        <p class="ft_info">
        	회사명 : 회사명 / 대표 : 대표자명<br>
			주소  : OO도 OO시 OO구 OO동 123-45<br>
			사업자 등록번호  : 123-45-67890<br>
			전화 :  02-123-4567  팩스  : 02-123-4568<br>
			통신판매업신고번호 :  제 OO구 - 123호<br>
			개인정보관리책임자 :  정보책임자명<br>
		</p>
    </div>
    <button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>
    <?php
    if(G5_DEVICE_BUTTON_DISPLAY && G5_IS_MOBILE) { ?>
    <a href="<?php echo get_device_change_url(); ?>" id="device_change">PC 버전으로 보기</a>
    <?php
    }

    if ($config['cf_analytics']) {
        echo $config['cf_analytics'];
    }
    ?>
</div>
<script>
jQuery(function($) {

    $( document ).ready( function() {

        // 폰트 리사이즈 쿠키있으면 실행
        font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
        
        //상단고정
        if( $(".top").length ){
            var jbOffset = $(".top").offset();
            $( window ).scroll( function() {
                if ( $( document ).scrollTop() > jbOffset.top ) {
                    $( '.top' ).addClass( 'fixed' );
                }
                else {
                    $( '.top' ).removeClass( 'fixed' );
                }
            });
        }

        //상단으로
        $("#top_btn").on("click", function() {
            $("html, body").animate({scrollTop:0}, '500');
            return false;
        });

    });
});
</script>

<!-- 추천업소 + 카카오톡/채팅/맨위로 플로팅 (썸네일상점은 jobs_thumb_shop_main에서 출력) -->
<?php if (!defined('_THUMB_SHOP_FLOATS_DONE_')) { include_once(G5_THEME_PATH . '/inc/float_banners.php'); } ?>

<!-- EVE CHAT 모바일 전체화면 -->
<iframe id="eveChatFrame"
  src="<?php echo G5_PLUGIN_URL; ?>/chat/eve_chat_frame.php"
  style="position:fixed;top:0;left:0;right:0;bottom:0;width:100%;height:100%;border:none;border-radius:0;box-shadow:none;z-index:9999;display:none;background:#fff;overflow:hidden;"
  allow="autoplay"
  loading="lazy"></iframe>

<button type="button" id="eveChatOpenMobile"
  style="position:fixed;bottom:16px;right:16px;z-index:9998;width:56px;height:56px;border-radius:50%;border:none;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-size:24px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(255,27,107,.4);cursor:pointer;"
  onclick="toggleEveChatMobile()">💭</button>

<script>
(function(){
  var frame = document.getElementById('eveChatFrame');
  var btn = document.getElementById('eveChatOpenMobile');
  var isOpen = false;
  window.toggleEveChatMobile = function(){
    isOpen = !isOpen;
    frame.style.display = isOpen ? 'block' : 'none';
    btn.style.display = isOpen ? 'none' : 'flex';
  };
  window.addEventListener('message', function(e){
    if(e.data && e.data.type === 'eve-chat-close'){
      isOpen = false;
      frame.style.display = 'none';
      btn.style.display = 'flex';
    }
  });
})();
</script>

<?php
include_once(G5_THEME_PATH."/tail.sub.php");