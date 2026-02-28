<?php
/**
 * 어드민 - 기업회원 상세 정보 (AJAX HTML 반환)
 * 사용자 입력 vs OCR 추출 비교, 문서 이미지 표시
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

$doc_url = $mb['mb_6'] ? G5_URL . '/' . $mb['mb_6'] : '';

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

<h4 style="margin:16px 0 8px;font-size:15px;">📊 사용자 입력 vs AI 인식 비교</h4>

<?php if (empty($ocr_result)) { ?>
<p style="color:#999;font-size:13px;">AI OCR 인식 결과가 없습니다. (문서 재스캔이 필요할 수 있습니다)</p>
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
</tbody>
</table>

<?php if ($status === 'pending' || $status === '') { ?>
<div style="text-align:center;margin-top:20px;padding-top:16px;border-top:2px solid #eee;">
    <button type="button" class="eve-act-btn eve-act-approve" style="padding:10px 30px;font-size:14px;" onclick="eveAction('approve','<?php echo $mb['mb_id']; ?>');eveCloseDetail();">✅ 승인</button>
    <button type="button" class="eve-act-btn eve-act-reject" style="padding:10px 30px;font-size:14px;" onclick="eveAction('reject','<?php echo $mb['mb_id']; ?>');eveCloseDetail();">❌ 반려</button>
</div>
<?php } ?>

<?php if (!empty($ocr_result) || $doc_url) { ?>
<div style="margin-top:12px;text-align:center;">
    <button type="button" style="padding:6px 16px;border:1px solid #4285f4;border-radius:6px;background:#fff;color:#4285f4;font-size:12px;cursor:pointer;" onclick="eveRescanOcr('<?php echo $mb['mb_id']; ?>')">🔄 AI 재스캔</button>
</div>
<?php } ?>
