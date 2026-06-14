<?php
/**
 * 리뉴얼 푸터 — main 내부 (register / job detail 시안)
 */
if (!defined('_GNUBOARD_')) exit;
$_ev_cs_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/cs.php' : '/cs.php';
?>
<footer class="footer footer-in-main">
  <div class="logo-footer">eve'알바</div>
  <div class="footer-links">
    <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
    <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
    <a href="#">청소년보호정책</a>
    <a href="#">광고/제휴 문의</a>
    <a href="<?php echo $_ev_cs_url; ?>">고객센터</a>
  </div>
  <p>상호명: (주)이브알바 | EVE ALBA &nbsp; 대표자: 홍길동 &nbsp; 사업자등록번호: 000-00-00000</p>
  <p class="adult-notice">※ 이 사이트는 성인 유흥 유구인구직 정보 사이트로, 만 18세 미만은 이용하실 수 없습니다.</p>
  <p style="margin-top:8px;color:#666;">© 2026 이브알바(EVE ALBA) All Rights Reserved.</p>
</footer>
