<?php
/**
 * 이브알바 좌측 사이드바 (left_login + 채팅위젯만)
 * - football/cheer 위젯 제거
 */
if (!defined('_GNUBOARD_')) exit;

$leftLoginFile = isset($leftLoginFile) ? $leftLoginFile : (defined('G5_THEME_PATH') ? G5_THEME_PATH.'/left-login.php' : '');
$chatFile      = isset($chatFile)      ? $chatFile      : (defined('G5_PLUGIN_PATH') ? G5_PLUGIN_PATH.'/chat/chat_box.php' : '');
?>
<aside class="ev-left">
  <section class="ev-box ev-box-login">
    <?php
    if ($leftLoginFile && is_file($leftLoginFile)) {
        include $leftLoginFile;
    } else {
        if (function_exists('outlogin')) {
            echo outlogin('theme/basic');
        } else {
            echo '<div style="padding:12px;">left-login 없음</div>';
        }
    }
    ?>
  </section>
  <section class="ev-box ev-box-chat" id="ev-chat-box">
    <?php
    if ($chatFile && is_file($chatFile)) {
        include $chatFile;
    } else {
        echo '<div style="padding:12px;">채팅 없음</div>';
    }
    ?>
  </section>
  <a class="ev-float-chat" id="ev-float-chat" href="<?php echo defined('G5_PLUGIN_URL') ? G5_PLUGIN_URL : G5_URL.'/plugin'; ?>/chat/chat_popup.php" target="_blank" rel="noopener" aria-label="채팅 열기">채팅</a>
</aside>
