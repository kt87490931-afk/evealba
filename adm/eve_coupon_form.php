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
    'ec_line_ad_days' => 0,
    'ec_issue_type' => 'manual',
    'ec_auto_trigger' => '',
    'ec_issue_target_scope' => 'all',
    'ec_issue_target_mb_id' => '',
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
      <tr id="tr_line_ad_days" style="<?php echo ($row['ec_type'] ?? '') === 'line_ad_free' ? '' : 'display:none;'; ?>">
        <th scope="row"><label for="ec_line_ad_days">줄광고 무료 기간</label></th>
        <td>
          <select name="ec_line_ad_days" id="ec_line_ad_days" class="frm_input">
            <option value="0">선택</option>
            <option value="30" <?php echo (int)($row['ec_line_ad_days'] ?? 0) === 30 ? 'selected' : ''; ?>>30일</option>
            <option value="60" <?php echo (int)($row['ec_line_ad_days'] ?? 0) === 60 ? 'selected' : ''; ?>>60일</option>
            <option value="90" <?php echo (int)($row['ec_line_ad_days'] ?? 0) === 90 ? 'selected' : ''; ?>>90일</option>
          </select>
          <span class="frm_info">30일=70,000원 / 60일=125,000원 / 90일=170,000원 (할인금액에 자동 반영)</span>
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
        <th scope="row"><label for="ec_issue_type">발급유형</label></th>
        <td>
          <select name="ec_issue_type" id="ec_issue_type" class="frm_input">
            <option value="manual" <?php echo ($row['ec_issue_type'] ?? 'manual') === 'manual' ? 'selected' : ''; ?>>수동</option>
            <option value="auto" <?php echo ($row['ec_issue_type'] ?? '') === 'auto' ? 'selected' : ''; ?>>자동</option>
          </select>
          <span class="frm_info">수동: 관리자가 직접 발급. 자동: 조건에 맞으면 시스템이 자동 발급</span>
        </td>
      </tr>
      <tr id="tr_auto_trigger" style="<?php echo ($row['ec_issue_type'] ?? '') === 'auto' ? '' : 'display:none;'; ?>">
        <th scope="row"><label for="ec_auto_trigger">자동 발급 시점</label></th>
        <td>
          <select name="ec_auto_trigger" id="ec_auto_trigger" class="frm_input">
            <option value="">선택</option>
            <option value="on_approval" <?php echo ($row['ec_auto_trigger'] ?? '') === 'on_approval' ? 'selected' : ''; ?>>가입인증 후</option>
            <option value="monthly_1st" <?php echo ($row['ec_auto_trigger'] ?? '') === 'monthly_1st' ? 'selected' : ''; ?>>매월 1일</option>
          </select>
          <span class="frm_info">가입인증 후: 기업회원 승인 시 즉시 발급. 매월 1일: 매월 1일 크론 실행 시 전체 기업회원에게 발급</span>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="ec_issue_target_scope">발급대상</label></th>
        <td>
          <select name="ec_issue_target_scope" id="ec_issue_target_scope" class="frm_input">
            <option value="all" <?php echo ($row['ec_issue_target_scope'] ?? 'all') === 'all' ? 'selected' : ''; ?>>전체</option>
            <option value="individual" <?php echo ($row['ec_issue_target_scope'] ?? '') === 'individual' ? 'selected' : ''; ?>>개인</option>
          </select>
          <span class="frm_info">전체: 대상 회원 전체. 개인: 지정한 회원ID에만 발급</span>
        </td>
      </tr>
      <tr id="tr_target_mb_id" style="<?php echo ($row['ec_issue_target_scope'] ?? '') === 'individual' ? '' : 'display:none;'; ?>">
        <th scope="row"><label for="ec_issue_target_mb_id">대상 회원ID</label></th>
        <td>
          <input type="text" name="ec_issue_target_mb_id" id="ec_issue_target_mb_id" value="<?php echo htmlspecialchars($row['ec_issue_target_mb_id'] ?? ''); ?>" class="frm_input" size="20" placeholder="회원ID">
          <span class="frm_info">개인 발급 시 이 회원에게만 발급됩니다</span>
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
  if (f.ec_issue_type.value === 'auto' && !f.ec_auto_trigger.value) {
    alert('자동 발급 선택 시 시점(가입인증 후/매월 1일)을 선택하세요.');
    f.ec_auto_trigger.focus(); return false;
  }
  if (f.ec_issue_target_scope.value === 'individual' && !f.ec_issue_target_mb_id.value.trim()) {
    alert('개인 발급 선택 시 대상 회원ID를 입력하세요.');
    f.ec_issue_target_mb_id.focus(); return false;
  }
  if (f.ec_type.value === 'line_ad_free' && (!f.ec_line_ad_days || f.ec_line_ad_days.value === '0')) {
    alert('줄광고 무료 선택 시 기간(30일/60일/90일)을 선택하세요.');
    if (f.ec_line_ad_days) f.ec_line_ad_days.focus(); return false;
  }
  return true;
}
document.addEventListener('DOMContentLoaded', function() {
  var issueType = document.getElementById('ec_issue_type');
  var scope = document.getElementById('ec_issue_target_scope');
  var ecType = document.getElementById('ec_type');
  var lineAdDays = document.getElementById('ec_line_ad_days');
  var discountValue = document.getElementById('ec_discount_value');
  var priceMap = {30:70000, 60:125000, 90:170000};
  function toggle() {
    document.getElementById('tr_auto_trigger').style.display = issueType && issueType.value === 'auto' ? '' : 'none';
    document.getElementById('tr_target_mb_id').style.display = scope && scope.value === 'individual' ? '' : 'none';
    var tr = document.getElementById('tr_line_ad_days');
    if (tr) tr.style.display = ecType && ecType.value === 'line_ad_free' ? '' : 'none';
    if (ecType && ecType.value === 'line_ad_free' && lineAdDays && lineAdDays.value && priceMap[lineAdDays.value] && discountValue) {
      discountValue.value = priceMap[lineAdDays.value];
    }
  }
  if (issueType) issueType.addEventListener('change', toggle);
  if (scope) scope.addEventListener('change', toggle);
  if (ecType) ecType.addEventListener('change', toggle);
  if (lineAdDays) lineAdDays.addEventListener('change', function() {
    if (ecType && ecType.value === 'line_ad_free' && priceMap[this.value] && discountValue) discountValue.value = priceMap[this.value];
  });
  toggle();
});
</script>

<?php require_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
