<?php
/**
 * 쪽지함 전용 헤드 - 새창 팝업용, 메인 해드/사이드바 없음
 */
if (!defined('_GNUBOARD_')) exit;

$g5_debug['php']['begin_time'] = isset($begin_time) ? $begin_time : get_microtime();
if (!isset($g5['title'])) { $g5['title'] = '쪽지함'; $g5_head_title = $g5['title']; }
else { $g5_head_title = $g5['title'] . ' | ' . $config['cf_title']; }
$g5_head_title = strip_tags($g5_head_title);
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $g5_head_title; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL ?>/evealba.css?ver=<?php echo G5_CSS_VER ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_URL ?>/css/memo_popup.css?ver=<?php echo G5_CSS_VER ?>">
<link rel="stylesheet" href="<?php echo G5_JS_URL ?>/font-awesome/css/font-awesome.min.css">
<?php $memo_skin_url = isset($member_skin_url) ? $member_skin_url : (G5_THEME_URL.'/skin/member/basic'); ?>
<link rel="stylesheet" href="<?php echo $memo_skin_url; ?>/style.css">
<script src="<?php echo G5_JS_URL ?>/jquery-1.12.4.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/common.js?ver=<?php echo G5_JS_VER ?>"></script>
<script>
var g5_url="<?php echo G5_URL ?>"; var g5_bbs_url="<?php echo G5_BBS_URL ?>";
</script>
</head>
<body class="memo-popup-body">
