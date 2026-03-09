<?php
/**
 * 본인을 추천인으로 지정한 회원 목록 (닉네임만 표시)
 * mode=count: JSON {"cnt":N}
 * mode 없음: HTML 팝업
 */
include_once('./_common.php');

if (!$is_member) {
    if (isset($_GET['mode']) && $_GET['mode'] === 'count') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('cnt' => 0), JSON_UNESCAPED_UNICODE);
        exit;
    }
    alert('로그인 후 이용해 주세요.', G5_BBS_URL.'/login.php');
}

$ref_mb_id = $member['mb_id'];
$ref_mb_id_esc = sql_escape_string($ref_mb_id);

if (isset($_GET['mode']) && $_GET['mode'] === 'count') {
    header('Content-Type: application/json; charset=utf-8');
    $r = sql_fetch("SELECT COUNT(*) AS cnt FROM {$g5['member_table']} WHERE mb_recommend = '{$ref_mb_id_esc}' AND (mb_leave_date = '' OR mb_leave_date IS NULL)");
    echo json_encode(array('cnt' => (int)($r['cnt'] ?? 0)), JSON_UNESCAPED_UNICODE);
    exit;
}

$list = array();
$res = sql_query("SELECT mb_nick, mb_datetime FROM {$g5['member_table']} WHERE mb_recommend = '{$ref_mb_id_esc}' AND (mb_leave_date = '' OR mb_leave_date IS NULL) ORDER BY mb_datetime DESC");
while ($row = sql_fetch_array($res)) {
    $list[] = array('nick' => get_text($row['mb_nick']), 'date' => substr($row['mb_datetime'], 0, 10));
}
$cnt = count($list);

// mode=body: 모달용 HTML 조각 반환 (AJAX)
if (isset($_GET['mode']) && $_GET['mode'] === 'body') {
    header('Content-Type: text/html; charset=utf-8');
    ob_start();
    ?>
    <div class="ev-referral-modal-body">
      <p class="ev-referral-summary"><strong><?php echo $cnt; ?>명</strong></p>
      <?php if ($cnt > 0) { ?>
      <ul class="ev-referral-list">
        <?php foreach ($list as $r) { ?>
        <li class="ev-referral-item">
          <span class="ev-referral-avatar"><?php echo htmlspecialchars(mb_substr($r['nick'], 0, 1, 'UTF-8')); ?></span>
          <span class="ev-referral-nick"><?php echo htmlspecialchars($r['nick']); ?></span>
          <span class="ev-referral-date"><?php echo $r['date']; ?></span>
        </li>
        <?php } ?>
      </ul>
      <?php } else { ?>
      <p class="ev-referral-empty">🎀 아직 나를 추천한 회원이 없습니다.</p>
      <?php } ?>
    </div>
    <?php
    echo ob_get_clean();
    exit;
}

include_once('./_head.php');
?>
<div style="padding:20px;">
  <h3 style="margin:0 0 16px;">본인을 추천한 회원들</h3>
  <p><strong><?php echo $cnt; ?>명</strong></p>
  <?php if ($cnt > 0) { ?>
  <ul style="margin:0;padding-left:20px;">
    <?php foreach ($list as $r) { ?>
    <li><?php echo htmlspecialchars($r['nick']); ?></li>
    <?php } ?>
  </ul>
  <?php } else { ?>
  <p style="color:#888;">아직 없습니다.</p>
  <?php } ?>
</div>
<?php include_once('./_tail.php'); ?>
