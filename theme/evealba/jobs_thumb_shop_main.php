<?php
/**
 * 썸네일상점 메인 - 디자인 + 구매
 */
if (!defined('_GNUBOARD_')) exit;

$jobs_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$thumb_shop_url = $jobs_base . '/jobs_thumb_shop.php';

if (!$is_member) {
    echo '<div class="thumb-shop-guest" style="padding:40px 20px;text-align:center;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">';
    echo '<h2 style="margin:0 0 12px;font-size:20px;color:#333;">🖼️ 썸네일상점</h2>';
    echo '<p style="margin:0 0 20px;color:#666;line-height:1.6;">채용광고 썸네일을 꾸미고 유료 옵션을 구매하려면<br>로그인 후 이용해 주세요.</p>';
    echo '<a href="'.G5_BBS_URL.'/login.php?url='.urlencode($thumb_shop_url).'" style="display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;font-weight:700;">로그인</a>';
    echo '</div>';
    return;
}

// 기업회원 전용 (썸네일 옵션은 기업회원 광고용)
$_is_biz = false;
if ($member['mb_id']) {
    $__mb = sql_fetch("SELECT mb_1 FROM g5_member WHERE mb_id = '".addslashes($member['mb_id'])."'");
    $_is_biz = isset($__mb['mb_1']) && $__mb['mb_1'] === 'biz';
}

if (!$_is_biz) {
    echo '<div class="thumb-shop-personal" style="padding:40px 20px;text-align:center;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);">';
    echo '<h2 style="margin:0 0 12px;font-size:20px;color:#333;">🖼️ 썸네일상점</h2>';
    echo '<p style="margin:0 0 20px;color:#666;line-height:1.6;">썸네일 옵션은 기업회원 전용 서비스입니다.</p>';
    echo '<a href="'.$jobs_base.'/jobs.php'" style="display:inline-block;padding:12px 24px;background:#444;color:#fff;border-radius:8px;text-decoration:none;">채용정보로 이동</a>';
    echo '</div>';
    return;
}

// 진행중인 채용광고 목록
$mb_esc = addslashes($member['mb_id']);
$ongoing_list = array();
$chk = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if ($chk && sql_num_rows($chk)) {
    $res = sql_query("SELECT jr_id, jr_subject_display, jr_end_date, jr_status, jr_payment_confirmed FROM g5_jobs_register WHERE mb_id = '{$mb_esc}' AND jr_status IN ('pending','ongoing') AND (jr_payment_confirmed = 1 OR jr_status = 'ongoing') ORDER BY jr_id DESC LIMIT 50");
    if ($res) while ($r = sql_fetch_array($res)) {
        $ongoing_list[] = $r;
    }
}
?>
<div class="thumb-shop-main" style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);padding:24px;">
  <h2 style="margin:0 0 8px;font-size:22px;color:#333;">🖼️ 썸네일상점</h2>
  <p style="margin:0 0 24px;color:#666;font-size:14px;">채용광고 썸네일을 꾸미고 유료 옵션을 구매하세요. 구매 시 쿠폰을 적용할 수 있습니다.</p>

  <?php if (empty($ongoing_list)) { ?>
  <div style="padding:40px 20px;text-align:center;background:#f8f8f8;border-radius:8px;">
    <p style="margin:0 0 16px;color:#888;">진행중인 채용광고가 없습니다.</p>
    <a href="<?php echo $jobs_base; ?>/jobs_register.php" style="display:inline-block;padding:10px 20px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;">채용공고 등록하기</a>
  </div>
  <?php } else { ?>
  <div class="ts-select-job" style="margin-bottom:24px;">
    <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;">적용할 채용광고 선택</label>
    <select id="ts-jr-id" style="padding:10px 12px;border:1px solid #ddd;border-radius:8px;min-width:280px;font-size:14px;">
      <option value="">선택하세요</option>
      <?php foreach ($ongoing_list as $o) {
          $end = $o['jr_end_date'] ? date('Y-m-d', strtotime($o['jr_end_date'])) : '';
          $label = $o['jr_subject_display'] ?: ('#'.$o['jr_id']);
          echo '<option value="'.(int)$o['jr_id'].'" data-end="'.$end.'">#'.$o['jr_id'].' '.htmlspecialchars($label).($end ? ' (종료 '.$end.')' : '').'</option>';
      } ?>
    </select>
  </div>

  <div id="ts-design-area" style="display:none;margin-top:20px;padding-top:20px;border-top:1px solid #eee;">
    <p style="color:#888;font-size:13px;">썸네일 디자인 및 구매 UI는 준비 중입니다. 채용정보 수정 페이지에서 썸네일을 꾸밀 수 있습니다.</p>
    <a href="<?php echo $jobs_base; ?>/jobs_view.php?jr_id=" id="ts-edit-link" style="display:inline-block;margin-top:12px;padding:10px 20px;background:#444;color:#fff;border-radius:8px;text-decoration:none;">해당 채용광고 수정하러 가기</a>
  </div>
  <?php } ?>
</div>

<script>
(function(){
  var sel = document.getElementById('ts-jr-id');
  var area = document.getElementById('ts-design-area');
  var link = document.getElementById('ts-edit-link');
  if (!sel || !area) return;
  sel.addEventListener('change', function(){
    var v = this.value;
    if (v) {
      area.style.display = 'block';
      if (link) link.href = '<?php echo $jobs_base; ?>/jobs_view.php?jr_id=' + v + '&mode=edit';
    } else {
      area.style.display = 'none';
    }
  });
})();
</script>
