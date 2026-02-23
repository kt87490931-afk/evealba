<?php
// /plugin/chat/chat_popup.php
if (!defined('_GNUBOARD_')) {
    include_once(__DIR__ . '/../../common.php'); // /plugin/chat -> /common.php
}
if (!defined('SP_CHAT_POPUP')) define('SP_CHAT_POPUP', true);
?><!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>채팅</title>

<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/style.css">
<script src="<?php echo G5_THEME_URL; ?>/js/sp_user_menu_common.js"></script>
<style>
html,body{margin:0;padding:0;background:#f4f6f8;}
.sp-chat-popup-wrap{
  width:100%;
  max-width:560px;
  margin:0 auto;
  padding:10px 10px 18px;
  box-sizing:border-box;
}

/* 팝업에서는 폭 100% 강제 */
.sp-chat-popup-wrap .sp-login-box,
.sp-chat-popup-wrap .livechat-outer,
.sp-chat-popup-wrap .livechat-box{
  width:100% !important;
  max-width:100% !important;
  box-sizing:border-box;
}
.sp-chat-popup-wrap .sp-login-box{ height:auto !important; }

/* 채팅 높이(화면에 맞게) */
.sp-chat-popup-wrap .livechat-box{
  height: calc(100vh - 210px) !important;
}
</style>
</head>
<body>
  <div class="sp-chat-popup-wrap">
    <?php
      // 로그인(테마)
      include_once(G5_THEME_PATH.'/left-login.php');

      // 채팅(플러그인)
      include_once(G5_PLUGIN_PATH.'/chat/chat_box.php');
    ?>
  </div>
</body>
</html>
