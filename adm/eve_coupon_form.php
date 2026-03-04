<?php
/**
 * 어드민 - 쿠폰 생성/수정 폼
 * ec_code 미노출 (자동생성)
 */
$sub_menu = '910940';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$w = isset($_GET['w']) ? preg_replace('/[^a-z]/', '', $_GET['w']) : '';
$ec_id = isset($_GET['ec_id']) ? (int)$_GET['ec_id'] : 0;

$row = array(
    'ec_name' => '',
    'ec_target' => 'biz',
    'ec_type' => 'ad',
    'ec_discount_type' => 'percent',
    'ec_discount_value' => 0,
    'ec_min_amount' => 0,
    'ec_max_discount' => 0,
    'ec_valid_from' => '',
    'ec_valid_to' => '',
    'ec_use_limit' => 0,
    'ec_issue_from' => '',
    'ec_issue_to' => '',
    'ec_issue_limit_per_member' => 0,
    'ec_is_active' => 1
);

$tb = 'g5_ev_coupon';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));
if (!$exists) {
    alert('g5_ev_coupon 테이블이 없습니다. 마이그레이션 012를 먼저 실행하세요.', './run_migration_012.php');
}

$html_title = '쿠폰 추가';
if ($w === 'u' && $ec_id) {
    $row = sql_fetch("SELECT * FROM {$tb} WHERE ec_id = '{$ec_id}'");
    if (!$row) alert('쿠폰을 찾을 수 없습니다.', './eve_coupon_list.php');
    $html_title = '쿠폰 수정';
}

$g5['title'] = $html_title;
require_once G5_ADMIN_PATH . '/admin.head.php';

$type_labels = array('thumb' => '썸네일옵션', 'ad' => '채용공고', 'line_ad_free' => '줄광고 무료', 'gift' => '기프티콘');
$target_labels = array('biz' => '기업회원', 'personal' => '일반회원');
$disc_type_labels = array('percent' => '할인율(%)', 'amount' => '할인금액(원)');
?>
<form name="frm" method="post" action="./eve_coupon_form_update.php" onsubmit="return frm_check(this);">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="ec_id" value="<?php echo (int)$ec_id; ?>">
<?php echo get_admin_token(); ?>

<div class="tbl_frm01 tbl_wrap">
  <table>
    <colgroup>
      <col class="grid_4">
      <col>
    </colgroup>
    <tbody>
      <tr>
        <th scope="row"><label for="ec_name">쿠폰명 *</label></th>
        <td>
          <input type="text" name="ec_name" id="ec_name" value="<?php echo htmlspecialchars($row['ec_name']); ?>" required class="frm_input" size="60" placeholder="예: 줄광고3달무료, 채용공고30%할인">
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_target">대상</label></th>
        <td>
          <select name="ec_target" id="ec_target" class="frm_input">
            <?php foreach ($target_labels as $v => $l) { ?>
            <option value="<?php echo $v; ?>" <?php echo ($row['ec_target'] ?? 'biz') === $v ? 'selected' : ''; ?>><?php echo $l; ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_type">유형</label></th>
        <td>
          <select name="ec_type" id="ec_type" class="frm_input">
            <?php foreach ($type_labels as $v => $l) { ?>
            <option value="<?php echo $v; ?>" <?php echo ($row['ec_type'] ?? 'ad') === $v ? 'selected' : ''; ?>><?php echo $l; ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_discount_type">할인 방식</label></th>
        <td>
          <select name="ec_discount_type" id="ec_discount_type" class="frm_input">
            <?php foreach ($disc_type_labels as $v => $l) { ?>
            <option value="<?php echo $v; ?>" <?php echo ($row['ec_discount_type'] ?? 'percent') === $v ? 'selected' : ''; ?>><?php echo $l; ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_discount_value">할인율/금액 *</label></th>
        <td>
          <input type="number" name="ec_discount_value" id="ec_discount_value" value="<?php echo (int)($row['ec_discount_value'] ?? 0); ?>" required class="frm_input" size="10" min="0">
          <span class="frm_info">percent: 1~100, amount: 원 단위 (줄광고3달무료=170000)</span>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_min_amount">최소 결제금액</label></th>
        <td>
          <input type="number" name="ec_min_amount" id="ec_min_amount" value="<?php echo (int)($row['ec_min_amount'] ?? 0); ?>" class="frm_input" size="10" min="0">
          <span class="frm_info">0=제한없음</span>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_max_discount">최대할인금액</label></th>
        <td>
          <input type="number" name="ec_max_discount" id="ec_max_discount" value="<?php echo (int)($row['ec_max_discount'] ?? 0); ?>" class="frm_input" size="10" min="0">
          <span class="frm_info">할인율 적용 시 상한, 0=제한없음</span>
        </td>
      </tr>
      <tr>
        <th scope="row">사용 유효기간</th>
        <td>
          <input type="date" name="ec_valid_from" value="<?php echo htmlspecialchars($row['ec_valid_from'] ?? ''); ?>" class="frm_input"> ~
          <input type="date" name="ec_valid_to" value="<?php echo htmlspecialchars($row['ec_valid_to'] ?? ''); ?>" class="frm_input">
          <span class="frm_info">발급된 쿠폰의 사용 가능 기간. 비워두면 제한없음</span>
        </td>
      </tr>
      <tr>
        <th scope="row">발급 가능 기간</th>
        <td>
          <input type="date" name="ec_issue_from" value="<?php echo htmlspecialchars($row['ec_issue_from'] ?? ''); ?>" class="frm_input"> ~
          <input type="date" name="ec_issue_to" value="<?php echo htmlspecialchars($row['ec_issue_to'] ?? ''); ?>" class="frm_input">
          <span class="frm_info">이 기간에만 발급 가능. 비워두면 제한없음</span>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_use_limit">총 발급 수 제한</label></th>
        <td>
          <input type="number" name="ec_use_limit" id="ec_use_limit" value="<?php echo (int)($row['ec_use_limit'] ?? 0); ?>" class="frm_input" size="10" min="0">
          <span class="frm_info">0=무제한</span>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_issue_limit_per_member">1인당 발급 제한</label></th>
        <td>
          <input type="number" name="ec_issue_limit_per_member" id="ec_issue_limit_per_member" value="<?php echo (int)($row['ec_issue_limit_per_member'] ?? 0); ?>" class="frm_input" size="10" min="0">
          <span class="frm_info">0=무제한</span>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_is_active">상태</label></th>
        <td>
          <select name="ec_is_active" id="ec_is_active" class="frm_input">
            <option value="1" <?php echo ($row['ec_is_active'] ?? 1) ? 'selected' : ''; ?>>활성</option>
            <option value="0" <?php echo empty($row['ec_is_active']) ? 'selected' : ''; ?>>비활성</option>
          </select>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="btn_fixed_top">
  <button type="submit" class="btn btn_01">저장</button>
  <a href="./eve_coupon_list.php" class="btn btn_02">목록</a>
</div>
</form>

<script>
function frm_check(f) {
  if (!f.ec_name.value.trim()) { alert('쿠폰명을 입력하세요.'); f.ec_name.focus(); return false; }
  var v = parseInt(f.ec_discount_value.value, 10);
  if (isNaN(v) || v < 0) { alert('할인율/금액을 입력하세요.'); f.ec_discount_value.focus(); return false; }
  return true;
}
</script>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
