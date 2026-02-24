<?php
if (!defined('_GNUBOARD_')) exit;
?>
  </div><!-- /main-area.memo-main -->
    </div><!-- /memo-page-layout -->
  </div><!-- /main-area -->
</div><!-- /page-layout -->

<footer>
  <div class="footer-inner">
    <div class="footer-logo"><em>eve</em>·<span>알바</span></div>
    <div class="footer-links">
      <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
      <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
      <a href="#">청소년보호정책</a>
      <a href="#">광고/제휴 문의</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/cs.php' : '/cs.php'; ?>">고객센터</a>
    </div>
    <div class="footer-text">
      상호명: (주)이브알바 | 대표이사: 홍길동 | 고객센터: 1588-0000<br>
      <span>본 사이트는 성인 유흥알바 구인구직 정보 사이트로, 만 18세 미만은 이용하실 수 없습니다.</span><br>
      © 2026 이브알바(EVE ALBA) All Rights Reserved.
    </div>
  </div>
</footer>

<div class="floating-cta">
  <a href="#" class="float-btn float-kakao" title="카카오톡">💬</a>
  <a href="#" class="float-btn float-top" title="맨 위로" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">▲</a>
</div>

</body>
</html>
<?php echo html_end(); ?>
