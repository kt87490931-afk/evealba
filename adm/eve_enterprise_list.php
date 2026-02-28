<?php
/**
 * 어드민 - 기업회원 승인관리
 * mb_1='biz' 회원 목록 조회, 승인/반려 처리
 * mb_7: pending(승인대기), approved(승인), rejected(반려)
 */
$sub_menu = '910300';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$token = get_session('ss_admin_token') ?: get_admin_token();

$st = isset($_GET['st']) ? preg_replace('/[^a-z]/', '', $_GET['st']) : 'pending';
$sf = isset($_GET['sf']) ? trim($_GET['sf']) : '';
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';

$sql_search = " WHERE mb_1 = 'biz' ";
if ($st === 'pending')  $sql_search .= " AND (mb_7 = 'pending' OR mb_7 = '') ";
elseif ($st === 'approved') $sql_search .= " AND mb_7 = 'approved' ";
elseif ($st === 'rejected') $sql_search .= " AND mb_7 = 'rejected' ";
elseif ($st === 'all') { /* 전체 */ }
else $sql_search .= " AND (mb_7 = 'pending' OR mb_7 = '') ";

if ($sf && $stx) {
    $stx_esc = sql_escape_string($stx);
    if ($sf === 'mb_id') $sql_search .= " AND mb_id LIKE '%{$stx_esc}%' ";
    elseif ($sf === 'mb_name') $sql_search .= " AND mb_name LIKE '%{$stx_esc}%' ";
    elseif ($sf === 'mb_3') $sql_search .= " AND mb_3 LIKE '%{$stx_esc}%' ";
    elseif ($sf === 'mb_2') $sql_search .= " AND mb_2 LIKE '%{$stx_esc}%' ";
}

$sst = 'mb_datetime';
$sod = 'desc';
$sql_order = " ORDER BY {$sst} {$sod} ";

$cnt_row = sql_fetch("SELECT count(*) as cnt FROM {$g5['member_table']} {$sql_search}");
$total_count = (int)$cnt_row['cnt'];

$rows = 20;
$total_page = ceil($total_count / $rows) ?: 1;
if ($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$pending_cnt  = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['member_table']} WHERE mb_1='biz' AND (mb_7='pending' OR mb_7='')")['cnt'];
$approved_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['member_table']} WHERE mb_1='biz' AND mb_7='approved'")['cnt'];
$rejected_cnt = (int)sql_fetch("SELECT count(*) as cnt FROM {$g5['member_table']} WHERE mb_1='biz' AND mb_7='rejected'")['cnt'];
$all_cnt = $pending_cnt + $approved_cnt + $rejected_cnt;

$g5['title'] = '기업회원 승인관리';
require_once './admin.head.php';

function eve_normalize_addr($addr) {
    $addr = trim($addr);
    $addr = preg_replace('/\s+/', '', $addr);
    $addr = str_replace(array('특별시','광역시','특별자치시','특별자치도'), array('시','시','시','도'), $addr);
    $addr = preg_replace('/[^가-힣0-9a-zA-Z]/', '', $addr);
    return mb_strtolower($addr, 'UTF-8');
}

function eve_addr_similarity($a, $b) {
    $na = eve_normalize_addr($a);
    $nb = eve_normalize_addr($b);
    if ($na === $nb) return 100;
    if (!$na || !$nb) return 0;
    similar_text($na, $nb, $pct);
    return round($pct);
}
?>

<style>
.eve-ent-stats { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
.eve-ent-stats a, .eve-ent-stats span { display:inline-block; padding:6px 16px; border-radius:6px; font-size:13px; font-weight:600; text-decoration:none; border:1px solid #ddd; color:#666; background:#fff; }
.eve-ent-stats a:hover, .eve-ent-stats a.on { background:#FF1B6B; color:#fff; border-color:#FF1B6B; }
.eve-ent-badge { display:inline-block; padding:2px 10px; border-radius:12px; font-size:11px; font-weight:700; }
.eve-ent-badge.pending { background:#FFF3E0; color:#E65100; }
.eve-ent-badge.approved { background:#E8F5E9; color:#2E7D32; }
.eve-ent-badge.rejected { background:#FFEBEE; color:#C62828; }
.eve-doc-thumb { width:60px; height:60px; object-fit:cover; border-radius:6px; cursor:pointer; border:1px solid #eee; }
.eve-detail-modal { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; }
.eve-detail-modal.show { display:flex; }
.eve-detail-box { background:#fff; border-radius:12px; padding:24px; max-width:700px; width:95%; max-height:85vh; overflow-y:auto; position:relative; }
.eve-detail-close { position:absolute; top:12px; right:16px; font-size:24px; cursor:pointer; color:#999; background:none; border:none; }
.eve-cmp-table { width:100%; border-collapse:collapse; margin:12px 0; }
.eve-cmp-table th, .eve-cmp-table td { padding:8px 12px; border:1px solid #eee; font-size:13px; }
.eve-cmp-table th { background:#f8f8f8; width:100px; text-align:left; }
.eve-sim-bar { display:inline-block; height:6px; border-radius:3px; min-width:4px; }
.eve-sim-ok { background:#4CAF50; }
.eve-sim-warn { background:#FF9800; }
.eve-sim-bad { background:#F44336; }
.eve-act-btn { padding:6px 14px; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; margin:2px; }
.eve-act-approve { background:#4CAF50; color:#fff; }
.eve-act-reject { background:#F44336; color:#fff; }
.eve-search-bar { display:flex; gap:6px; margin-bottom:12px; align-items:center; }
.eve-search-bar select, .eve-search-bar input { padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px; }
.eve-search-bar button { padding:6px 14px; border:none; border-radius:6px; background:#555; color:#fff; font-size:13px; cursor:pointer; }
</style>

<div class="eve-ent-stats">
    <a href="?st=all" class="<?php echo $st==='all'?'on':''; ?>">전체 <?php echo $all_cnt; ?>건</a>
    <a href="?st=pending" class="<?php echo $st==='pending'?'on':''; ?>">승인대기 <?php echo $pending_cnt; ?>건</a>
    <a href="?st=approved" class="<?php echo $st==='approved'?'on':''; ?>">승인완료 <?php echo $approved_cnt; ?>건</a>
    <a href="?st=rejected" class="<?php echo $st==='rejected'?'on':''; ?>">반려 <?php echo $rejected_cnt; ?>건</a>
</div>

<form method="get" class="eve-search-bar">
    <input type="hidden" name="st" value="<?php echo $st; ?>">
    <select name="sf">
        <option value="mb_id" <?php echo $sf==='mb_id'?'selected':''; ?>>아이디</option>
        <option value="mb_name" <?php echo $sf==='mb_name'?'selected':''; ?>>이름</option>
        <option value="mb_3" <?php echo $sf==='mb_3'?'selected':''; ?>>상호</option>
        <option value="mb_2" <?php echo $sf==='mb_2'?'selected':''; ?>>사업자번호</option>
    </select>
    <input type="text" name="stx" value="<?php echo htmlspecialchars($stx); ?>" placeholder="검색어">
    <button type="submit">검색</button>
</form>

<div class="tbl_head01 tbl_wrap">
<table>
<caption>기업회원 승인관리 목록</caption>
<thead>
<tr>
    <th scope="col">No</th>
    <th scope="col">가입일</th>
    <th scope="col">아이디</th>
    <th scope="col">이름</th>
    <th scope="col">사업자번호</th>
    <th scope="col">상호</th>
    <th scope="col">대표자</th>
    <th scope="col">주소</th>
    <th scope="col">문서</th>
    <th scope="col">상태</th>
    <th scope="col">관리</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT * FROM {$g5['member_table']} {$sql_search} {$sql_order} LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);
$num = $total_count - $from_record;

while ($mb = sql_fetch_array($result)) {
    $status = $mb['mb_7'] ?: 'pending';
    $status_class = $status;
    $status_label = $status === 'approved' ? '승인완료' : ($status === 'rejected' ? '반려' : '승인대기');

    $doc_url = '';
    if ($mb['mb_6']) {
        $doc_url = G5_URL . '/' . $mb['mb_6'];
    }

    $ocr_data = $mb['mb_8'] ? json_decode($mb['mb_8'], true) : array();
    $ocr_input = isset($ocr_data['user_input']) ? $ocr_data['user_input'] : array();
    $ocr_ai = isset($ocr_data['ocr_result']) ? $ocr_data['ocr_result'] : array();

    $mb_no = (int)$mb['mb_no'];
?>
<tr>
    <td><?php echo $num--; ?></td>
    <td><?php echo substr($mb['mb_datetime'], 0, 10); ?></td>
    <td><strong><?php echo $mb['mb_id']; ?></strong></td>
    <td><?php echo $mb['mb_name']; ?></td>
    <td><?php echo $mb['mb_2']; ?></td>
    <td><?php echo $mb['mb_3']; ?></td>
    <td><?php echo $mb['mb_4']; ?></td>
    <td style="max-width:200px;font-size:12px;"><?php echo mb_substr($mb['mb_5'], 0, 30, 'UTF-8'); ?></td>
    <td>
        <?php if ($doc_url) { ?>
        <img src="<?php echo $doc_url; ?>" class="eve-doc-thumb" onclick="eveShowDetail('<?php echo $mb['mb_id']; ?>')" title="클릭하여 상세보기">
        <?php } else { echo '—'; } ?>
    </td>
    <td><span class="eve-ent-badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
    <td>
        <button type="button" class="eve-act-btn" style="background:#4285f4;color:#fff;" onclick="eveShowDetail('<?php echo $mb['mb_id']; ?>')">상세</button>
        <?php if ($status === 'pending' || $status === '') { ?>
        <button type="button" class="eve-act-btn eve-act-approve" onclick="eveAction('approve','<?php echo $mb['mb_id']; ?>')">승인</button>
        <button type="button" class="eve-act-btn eve-act-reject" onclick="eveAction('reject','<?php echo $mb['mb_id']; ?>')">반려</button>
        <?php } ?>
    </td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

<?php echo get_paging(G5_IS_MOBILE ? 3 : 5, $page, $total_page, '?st='.$st.($sf?'&sf='.$sf:'').($stx?'&stx='.urlencode($stx):'').'&page='); ?>

<!-- 상세 모달 -->
<div class="eve-detail-modal" id="eveDetailModal">
    <div class="eve-detail-box">
        <button class="eve-detail-close" onclick="eveCloseDetail()">&times;</button>
        <div id="eveDetailContent">로딩중...</div>
    </div>
</div>

<script>
function eveShowDetail(mbId) {
    var modal = document.getElementById('eveDetailModal');
    var content = document.getElementById('eveDetailContent');
    content.innerHTML = '<p style="text-align:center;padding:40px;">⏳ 로딩중...</p>';
    modal.classList.add('show');

    fetch('<?php echo G5_ADMIN_URL; ?>/eve_enterprise_detail.php?mb_id=' + encodeURIComponent(mbId))
    .then(function(r){ return r.text(); })
    .then(function(html){
        content.innerHTML = html;
    })
    .catch(function(){
        content.innerHTML = '<p style="color:red;">데이터를 불러올 수 없습니다.</p>';
    });
}

function eveCloseDetail() {
    document.getElementById('eveDetailModal').classList.remove('show');
}

function eveAction(action, mbId) {
    var msg = action === 'approve' ? '이 기업회원을 승인하시겠습니까?' : '이 기업회원을 반려하시겠습니까?';
    if (!confirm(msg)) return;

    var reason = '';
    if (action === 'reject') {
        reason = prompt('반려 사유를 입력해주세요:', '');
        if (reason === null) return;
    }

    var fd = new FormData();
    fd.append('token', '<?php echo $token; ?>');
    fd.append('action', action);
    fd.append('mb_id', mbId);
    fd.append('reason', reason);

    fetch('<?php echo G5_ADMIN_URL; ?>/eve_enterprise_update.php', { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
        alert(d.msg || (d.ok ? '처리 완료' : '처리 실패'));
        if (d.ok) location.reload();
    })
    .catch(function(){ alert('네트워크 오류'); });
}

function eveRescanOcr(mbId) {
    if (!confirm('AI로 문서를 다시 스캔하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('token', '<?php echo $token; ?>');
    fd.append('action', 'rescan');
    fd.append('mb_id', mbId);

    fetch('<?php echo G5_ADMIN_URL; ?>/eve_enterprise_update.php', { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
        alert(d.msg || (d.ok ? '재스캔 완료' : '재스캔 실패'));
        if (d.ok) eveShowDetail(mbId);
    })
    .catch(function(){ alert('네트워크 오류'); });
}

document.getElementById('eveDetailModal').addEventListener('click', function(e) {
    if (e.target === this) eveCloseDetail();
});
</script>

<?php require_once './admin.tail.php'; ?>
