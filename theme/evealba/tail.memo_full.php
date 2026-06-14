<?php
if (!defined('_GNUBOARD_')) exit;
?>
  </main><!-- /feed-main -->
<?php include G5_THEME_PATH . '/inc/panel_right.php'; ?>
</div><!-- /app-wrap -->

<footer class="footer">
  <div class="logo-footer">eve'알바</div>
  <div class="footer-links">
    <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
    <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . '/cs.php' : '/cs.php'; ?>">고객센터</a>
  </div>
  <p style="margin-top:8px;color:#666;">© 2026 이브알바(EVE ALBA) All Rights Reserved.</p>
</footer>

<?php include G5_THEME_PATH . '/inc/mobile_tabbar.php'; ?>

</body>
</html>
<?php echo html_end(); ?>
