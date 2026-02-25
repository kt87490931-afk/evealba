<?php
/**
 * 광고 연장 팝업 - 광고유료결제 섹션만 표시
 * - 진행중인 채용정보 리스트의 연장 버튼에서 iframe으로 로드
 */
include_once('./_common.php');

if (!$is_member) {
    echo '<script>alert("로그인 후 이용해주세요.");</script>';
    exit;
}

$wr_id = isset($_GET['wr_id']) ? preg_replace('/[^0-9]/', '', $_GET['wr_id']) : '';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>광고 연장 - <?php echo $config['cf_title']; ?></title>
  <link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/css/evealba.css?v=<?php echo @filemtime(G5_THEME_PATH.'/css/evealba.css'); ?>">
  <link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/css/resume_register.css?v=<?php echo @filemtime(G5_THEME_PATH.'/css/resume_register.css'); ?>">
  <style>
    body { padding:16px; margin:0; background:#fff; font-family:'Noto Sans KR',sans-serif; }
    .extend-popup-wrap { max-width:700px; margin:0 auto; }
  </style>
</head>
<body>
<div class="extend-popup-wrap">
  <?php include G5_THEME_PATH.'/inc/jobs_register_ad_section.inc.php'; ?>
</div>
<script>
function toggleSec(head) {
  head.classList.toggle('open');
  var body = head.nextElementSibling;
  if(body) body.classList.toggle('collapsed');
}
function calcTotal() {
  var total = 0;
  document.querySelectorAll('[data-price]').forEach(function(chk){
    if(chk.checked) total += parseInt(chk.dataset.price);
  });
  document.querySelectorAll('#hl-30,#hl-60,#hl-90').forEach(function(chk){
    if(chk.checked) total += parseInt(chk.dataset.price);
  });
  var fmt = total.toLocaleString('ko-KR') + ' 원';
  var el1 = document.getElementById('totalAmount');
  var el2 = document.getElementById('totalAmount2');
  if(el1) el1.textContent = fmt;
  if(el2) el2.textContent = fmt;
}
document.querySelectorAll('.sec-head').forEach(function(h){
  h.onclick = function(){ toggleSec(this); };
});
document.querySelectorAll('[data-price]').forEach(function(el){
  el.onchange = function(){ calcTotal(); };
});
calcTotal();
</script>
</body>
</html>
