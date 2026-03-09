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
$res = sql_query("SELECT mb_nick FROM {$g5['member_table']} WHERE mb_recommend = '{$ref_mb_id_esc}' AND (mb_leave_date = '' OR mb_leave_date IS NULL) ORDER BY mb_datetime DESC");
while ($row = sql_fetch_array($res)) {
    $list[] = get_text($row['mb_nick']);
}
$cnt = count($list);

include_once('./_head.php');
?>
<div style="padding:20px;">
  <h3 style="margin:0 0 16px;">본인을 추천한 회원들</h3>
  <p><strong><?php echo $cnt; ?>명</strong></p>
  <?php if ($cnt > 0) { ?>
  <ul style="margin:0;padding-left:20px;">
    <?php foreach ($list as $nick) { ?>
    <li><?php echo htmlspecialchars($nick); ?></li>
    <?php } ?>
  </ul>
  <?php } else { ?>
  <p style="color:#888;">아직 없습니다.</p>
  <?php } ?>
</div>
<?php include_once('./_tail.php'); ?>
