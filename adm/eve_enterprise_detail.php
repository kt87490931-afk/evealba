<?php
/**
 * 어드민 - 기업회원 상세 정보 (AJAX HTML 반환)
 * 사용자 입력 vs OCR 추출 비교 + 업태/종목 승인가능 판단
 */
$sub_menu = '910300';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$mb_id = isset($_GET['mb_id']) ? trim($_GET['mb_id']) : '';
if (!$mb_id) { echo '<p>잘못된 요청</p>'; exit; }

$mb_id_esc = sql_escape_string($mb_id);
$mb = sql_fetch("SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$mb_id_esc}' AND mb_1 = 'biz'");
if (!$mb) { echo '<p>기업회원 정보를 찾을 수 없습니다.</p>'; exit; }

$status = $mb['mb_7'] ?: 'pending';
$status_label = $status === 'approved' ? '✅ 승인완료' : ($status === 'rejected' ? '❌ 반려' : '⏳ 승인대기');

$ocr_data = $mb['mb_8'] ? json_decode($mb['mb_8'], true) : array();
$user_input = isset($ocr_data['user_input']) ? $ocr_data['user_input'] : array();
$ocr_result = isset($ocr_data['ocr_result']) ? $ocr_data['ocr_result'] : array();

function eve_normalize_addr($addr) {
    $addr = trim($addr);
    $addr = preg_replace('/\s+/', '', $addr);
    $addr = str_replace(array('특별시','광역시','특별자치시','특별자치도'), array('시','시','시','도'), $addr);
    $addr = preg_replace('/[^가-힣0-9a-zA-Z]/', '', $addr);
    return mb_strtolower($addr, 'UTF-8');
}

function eve_field_similarity($a, $b) {
    $a = trim($a); $b = trim($b);
    if ($a === $b) return 100;
    if (!$a || !$b) return 0;
    $na = preg_replace('/\s+/', '', $a);
    $nb = preg_replace('/\s+/', '', $b);
    if ($na === $nb) return 100;
    similar_text($na, $nb, $pct);
    return round($pct);
}

function eve_addr_similarity($a, $b) {
    $na = eve_normalize_addr($a);
    $nb = eve_normalize_addr($b);
    if ($na === $nb) return 100;
    if (!$na || !$nb) return 0;
    similar_text($na, $nb, $pct);
    return round($pct);
}

function eve_sim_badge($pct) {
    if ($pct >= 90) return '<span style="color:#2E7D32;font-weight:700;">'.$pct.'% ✅</span>';
    if ($pct >= 60) return '<span style="color:#E65100;font-weight:700;">'.$pct.'% ⚠</span>';
    return '<span style="color:#C62828;font-weight:700;">'.$pct.'% ❌</span>';
}

$allowed_types = array();
$allowed_items = array();
$tb_cat = 'g5_eve_biz_category';
$tb_check = sql_query("SHOW TABLES LIKE '{$tb_cat}'", false);
if ($tb_check && sql_num_rows($tb_check)) {
    $cat_res = sql_query("SELECT cat_type, cat_name FROM `{$tb_cat}` WHERE cat_enabled = 1");
    while ($cat = sql_fetch_array($cat_res)) {
        if ($cat['cat_type'] === 'type') $allowed_types[] = $cat['cat_name'];
        else $allowed_items[] = $cat['cat_name'];
    }
}

function eve_check_biz_allowed($ocr_value, $allowed_list) {
    if (!$ocr_value || empty($allowed_list)) return array('match' => false, 'matched' => '');
    $ocr_value = trim($ocr_value);
    $ocr_parts = preg_split('/[,，、\/\s]+/', $ocr_value);
    foreach ($ocr_parts as $part) {
        $part = trim($part);
        if (!$part) continue;
        foreach ($allowed_list as $allowed) {
            if (mb_strpos($part, $allowed, 0, 'UTF-8') !== false || mb_strpos($allowed, $part, 0, 'UTF-8') !== false) {
                return array('match' => true, 'matched' => $allowed);
            }
        }
    }
    return array('match' => false, 'matched' => '');
}

$doc_url = $mb['mb_6'] ? G5_URL . '/' . $mb['mb_6'] : '';

$ocr_biz_type = isset($ocr_result['biz_type']) ? $ocr_result['biz_type'] : '';
$ocr_biz_item = isset($ocr_result['biz_item']) ? $ocr_result['biz_item'] : '';

$type_check = eve_check_biz_allowed($ocr_biz_type, $allowed_types);
$item_check = eve_check_biz_allowed($ocr_biz_item, $allowed_items);
$biz_approvable = ($type_check['match'] || $item_check['match']);

$fields = array(
    array('label' => '사업자번호', 'user' => $mb['mb_2'], 'ocr' => isset($ocr_result['biz_num']) ? $ocr_result['biz_num'] : '', 'type' => 'normal'),
    array('label' => '상호', 'user' => $mb['mb_3'], 'ocr' => isset($ocr_result['biz_name']) ? $ocr_result['biz_name'] : '', 'type' => 'normal'),
    array('label' => '대표자', 'user' => $mb['mb_4'], 'ocr' => isset($ocr_result['biz_rep']) ? $ocr_result['biz_rep'] : '', 'type' => 'normal'),
    array('label' => '주소', 'user' => $mb['mb_5'], 'ocr' => isset($ocr_result['biz_addr']) ? $ocr_result['biz_addr'] : '', 'type' => 'addr'),
);
?>

<h3 style="margin:0 0 16px;font-size:18px;">🏢 기업회원 상세정보</h3>

<table class="eve-cmp-table">
<tr><th>아이디</th><td><strong><?php echo $mb['mb_id']; ?></strong></td></tr>
<tr><th>이름</th><td><?php echo $mb['mb_name']; ?></td></tr>
<tr><th>이메일</th><td><?php echo $mb['mb_email']; ?></td></tr>
<tr><th>연락처</th><td><?php echo $mb['mb_hp']; ?></td></tr>
<tr><th>업종</th><td><?php echo $mb['mb_9']; ?></td></tr>
<tr><th>가입일</th><td><?php echo $mb['mb_datetime']; ?></td></tr>
<tr><th>승인상태</th><td><?php echo $status_label; ?></td></tr>
</table>

<?php if ($doc_url) { ?>
<h4 style="margin:16px 0 8px;font-size:15px;">📄 첨부문서</h4>
<div style="text-align:center;margin-bottom:16px;">
    <img src="<?php echo $doc_url; ?>" style="max-width:100%;max-height:400px;border-radius:8px;border:2px solid #eee;">
</div>
<?php } ?>

<?php if ($ocr_biz_type || $ocr_biz_item) { ?>
<h4 style="margin:16px 0 8px;font-size:15px;">🏷️ 업태/종목 승인 판단</h4>
<div style="background:<?php echo $biz_approvable ? '#E8F5E9' : '#FFEBEE'; ?>;border-radius:10px;padding:14px 18px;margin-bottom:16px;border:2px solid <?php echo $biz_approvable ? '#4CAF50' : '#F44336'; ?>;">
    <div style="font-size:16px;font-weight:900;margin-bottom:8px;color:<?php echo $biz_approvable ? '#2E7D32' : '#C62828'; ?>;">
        <?php echo $biz_approvable ? '✅ 승인 가능 업종' : '❌ 승인 불가 (관련없는 업종)'; ?>
    </div>
    <table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="padding:4px 0;font-size:13px;width:60px;font-weight:700;">업태</td>
        <td style="padding:4px 0;font-size:13px;"><?php echo htmlspecialchars($ocr_biz_type ?: '—'); ?></td>
        <td style="padding:4px 0;font-size:13px;width:120px;text-align:right;">
            <?php if ($ocr_biz_type) {
                if ($type_check['match']) echo '<span style="color:#2E7D32;font-weight:700;">✅ 허용 ('.$type_check['matched'].')</span>';
                else echo '<span style="color:#C62828;font-weight:700;">❌ 미등록</span>';
            } else echo '<span style="color:#999;">—</span>'; ?>
        </td>
    </tr>
    <tr>
        <td style="padding:4px 0;font-size:13px;font-weight:700;">종목</td>
        <td style="padding:4px 0;font-size:13px;"><?php echo htmlspecialchars($ocr_biz_item ?: '—'); ?></td>
        <td style="padding:4px 0;font-size:13px;text-align:right;">
            <?php if ($ocr_biz_item) {
                if ($item_check['match']) echo '<span style="color:#2E7D32;font-weight:700;">✅ 허용 ('.$item_check['matched'].')</span>';
                else echo '<span style="color:#C62828;font-weight:700;">❌ 미등록</span>';
            } else echo '<span style="color:#999;">—</span>'; ?>
        </td>
    </tr>
    </table>
</div>
<?php } elseif (!empty($ocr_result)) { ?>
<div style="background:#FFF3E0;border-radius:10px;padding:14px 18px;margin-bottom:16px;border:2px solid #FF9800;">
    <span style="font-size:13px;color:#E65100;font-weight:600;">⚠ 업태/종목 정보가 없습니다. AI 재스캔을 실행해주세요.</span>
</div>
<?php } ?>

<h4 style="margin:16px 0 8px;font-size:15px;">📊 사용자 입력 vs AI 인식 비교</h4>

<?php if (empty($ocr_result)) { ?>
<p style="color:#999;font-size:13px;">AI OCR 인식 결과가 없습니다. 아래 "AI 재스캔" 버튼을 눌러주세요.</p>
<?php } ?>

<table class="eve-cmp-table">
<thead>
<tr>
    <th style="width:80px;">항목</th>
    <th>사용자 입력</th>
    <th>AI 인식</th>
    <th style="width:80px;">일치도</th>
</tr>
</thead>
<tbody>
<?php foreach ($fields as $f) {
    $sim = 0;
    if ($f['ocr']) {
        $sim = ($f['type'] === 'addr') ? eve_addr_similarity($f['user'], $f['ocr']) : eve_field_similarity($f['user'], $f['ocr']);
    }
?>
<tr>
    <th><?php echo $f['label']; ?></th>
    <td><?php echo htmlspecialchars($f['user']); ?></td>
    <td><?php echo $f['ocr'] ? htmlspecialchars($f['ocr']) : '<span style="color:#ccc;">—</span>'; ?></td>
    <td><?php echo $f['ocr'] ? eve_sim_badge($sim) : '—'; ?></td>
</tr>
<?php } ?>
<?php if ($ocr_biz_type || $ocr_biz_item) { ?>
<tr>
    <th>업태</th>
    <td colspan="2"><?php echo htmlspecialchars($ocr_biz_type ?: '—'); ?></td>
    <td><?php echo $type_check['match'] ? '<span style="color:#2E7D32;font-weight:700;">✅</span>' : ($ocr_biz_type ? '<span style="color:#C62828;font-weight:700;">❌</span>' : '—'); ?></td>
</tr>
<tr>
    <th>종목</th>
    <td colspan="2"><?php echo htmlspecialchars($ocr_biz_item ?: '—'); ?></td>
    <td><?php echo $item_check['match'] ? '<span style="color:#2E7D32;font-weight:700;">✅</span>' : ($ocr_biz_item ? '<span style="color:#C62828;font-weight:700;">❌</span>' : '—'); ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<?php if ($status === 'pending' || $status === '') { ?>
<div style="text-align:center;margin-top:20px;padding-top:16px;border-top:2px solid #eee;">
    <button type="button" class="eve-act-btn eve-act-approve" style="padding:10px 30px;font-size:14px;" onclick="eveAction('approve','<?php echo $mb['mb_id']; ?>');eveCloseDetail();">✅ 승인</button>
    <button type="button" class="eve-act-btn eve-act-reject" style="padding:10px 30px;font-size:14px;" onclick="eveAction('reject','<?php echo $mb['mb_id']; ?>');eveCloseDetail();">❌ 반려</button>
</div>
<?php } ?>

<div style="margin-top:12px;text-align:center;">
    <button type="button" style="padding:6px 16px;border:1px solid #4285f4;border-radius:6px;background:#fff;color:#4285f4;font-size:12px;cursor:pointer;" onclick="eveRescanOcr('<?php echo $mb['mb_id']; ?>')">🔄 AI 재스캔</button>
</div>
