<?php
/**
 * 어드민 - 점프 헬스 모니터링
 */
$sub_menu = '910910';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '점프 헬스 모니터링';
require_once './admin.head.php';

$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!$tb_check || !sql_num_rows($tb_check)) {
    echo '<div class="local_desc01 local_desc"><p>채용정보등록 테이블이 없습니다.</p></div>';
    require_once './admin.tail.php';
    exit;
}

$col_check = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_jump_remain'", false);
$has_jump_cols = ($col_check && @sql_num_rows($col_check));

if (!$has_jump_cols) {
    echo '<div class="local_desc01 local_desc"><p>점프 컬럼이 아직 생성되지 않았습니다. 마이그레이션 011을 실행해 주세요.</p></div>';
    require_once './admin.tail.php';
    exit;
}

$stat_total = sql_fetch("SELECT
    COUNT(*) as cnt,
    SUM(jr_jump_total) as total_given,
    SUM(jr_jump_used) as total_used,
    SUM(jr_jump_remain) as total_remain,
    SUM(CASE WHEN jr_auto_jump=1 THEN 1 ELSE 0 END) as auto_on
FROM g5_jobs_register WHERE jr_status='ongoing' AND jr_jump_total > 0");

$stat_24h = sql_fetch("SELECT COUNT(*) as cnt FROM g5_jobs_jump_log WHERE jl_datetime >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stat_1h = sql_fetch("SELECT COUNT(*) as cnt FROM g5_jobs_jump_log WHERE jl_datetime >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$stat_auto_24h = sql_fetch("SELECT COUNT(*) as cnt FROM g5_jobs_jump_log WHERE jl_type='auto' AND jl_datetime >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stat_manual_24h = sql_fetch("SELECT COUNT(*) as cnt FROM g5_jobs_jump_log WHERE jl_type='manual' AND jl_datetime >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");

$log_check = sql_query("SHOW TABLES LIKE 'g5_jobs_jump_log'", false);
$has_log = ($log_check && sql_num_rows($log_check));

$purchase_check = sql_query("SHOW TABLES LIKE 'g5_jobs_jump_purchase'", false);
$has_purchase = ($purchase_check && sql_num_rows($purchase_check));

$pending_purchases = 0;
if ($has_purchase) {
    $pp = sql_fetch("SELECT COUNT(*) as cnt FROM g5_jobs_jump_purchase WHERE jp_status='pending'");
    $pending_purchases = (int)$pp['cnt'];
}
?>

<style>
.jh-wrap{font-family:'Noto Sans KR',sans-serif}
.jh-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:24px}
.jh-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;text-align:center}
.jh-card-label{font-size:11px;color:#888;margin-bottom:4px}
.jh-card-val{font-size:22px;font-weight:900;color:#333}
.jh-card-val.pink{color:#FF1B6B}
.jh-card-val.blue{color:#3B82F6}
.jh-card-val.green{color:#10B981}
.jh-card-val.orange{color:#FF6B35}
.jh-card-val.purple{color:#6B21A8}
.jh-table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;margin-bottom:20px}
.jh-table th{background:#f8f9fa;padding:10px 12px;text-align:left;font-size:12px;font-weight:700;color:#555;border-bottom:2px solid #e5e7eb}
.jh-table td{padding:10px 12px;font-size:12px;color:#333;border-bottom:1px solid #f0f0f0}
.jh-section{margin-bottom:30px}
.jh-section h3{font-size:15px;font-weight:900;color:#333;margin:0 0 12px;padding-bottom:8px;border-bottom:2px solid #FF1B6B}
.badge-auto{background:#6B21A8;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px}
.badge-manual{background:#FF1B6B;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px}
.badge-pending{background:#F59E0B;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px}
.badge-confirmed{background:#10B981;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px}
.btn-confirm-purchase{padding:4px 12px;border:none;border-radius:5px;background:#10B981;color:#fff;font-size:11px;cursor:pointer}
</style>

<div class="jh-wrap">
<div class="jh-cards">
  <div class="jh-card">
    <div class="jh-card-label">활성 광고 (점프 보유)</div>
    <div class="jh-card-val pink"><?php echo number_format((int)$stat_total['cnt']); ?>건</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">총 부여</div>
    <div class="jh-card-val blue"><?php echo number_format((int)$stat_total['total_given']); ?>회</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">총 사용</div>
    <div class="jh-card-val orange"><?php echo number_format((int)$stat_total['total_used']); ?>회</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">총 잔여</div>
    <div class="jh-card-val green"><?php echo number_format((int)$stat_total['total_remain']); ?>회</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">자동 점프 ON</div>
    <div class="jh-card-val purple"><?php echo number_format((int)$stat_total['auto_on']); ?>건</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">24시간 점프</div>
    <div class="jh-card-val"><?php echo number_format((int)$stat_24h['cnt']); ?>회</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">1시간 점프</div>
    <div class="jh-card-val"><?php echo number_format((int)$stat_1h['cnt']); ?>회</div>
  </div>
  <div class="jh-card">
    <div class="jh-card-label">미확인 구매</div>
    <div class="jh-card-val" style="color:#F59E0B"><?php echo number_format($pending_purchases); ?>건</div>
  </div>
</div>

<!-- 광고별 점프 현황 -->
<div class="jh-section">
  <h3>광고별 점프 현황</h3>
  <table class="jh-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>회원</th>
        <th>광고제목</th>
        <th>총 부여</th>
        <th>사용</th>
        <th>잔여</th>
        <th>자동</th>
        <th>다음 점프</th>
        <th>마지막 점프</th>
        <th>마감일</th>
      </tr>
    </thead>
    <tbody>
<?php
$jobs = sql_query("SELECT jr_id, mb_id, jr_title, jr_jump_total, jr_jump_used, jr_jump_remain,
    jr_auto_jump, jr_auto_jump_next, jr_jump_datetime, jr_end_date
    FROM g5_jobs_register
    WHERE jr_status='ongoing' AND jr_jump_total > 0
    ORDER BY jr_jump_datetime DESC
    LIMIT 100");
while ($j = sql_fetch_array($jobs)) { ?>
      <tr>
        <td><?php echo $j['jr_id']; ?></td>
        <td><?php echo htmlspecialchars($j['mb_id']); ?></td>
        <td><?php echo htmlspecialchars(cut_str($j['jr_title'], 20)); ?></td>
        <td><?php echo number_format((int)$j['jr_jump_total']); ?></td>
        <td><?php echo number_format((int)$j['jr_jump_used']); ?></td>
        <td><strong style="color:#10B981"><?php echo number_format((int)$j['jr_jump_remain']); ?></strong></td>
        <td><?php echo (int)$j['jr_auto_jump'] ? '<span class="badge-auto">ON</span>' : 'OFF'; ?></td>
        <td><?php echo $j['jr_auto_jump_next'] ? substr($j['jr_auto_jump_next'], 5, 11) : '—'; ?></td>
        <td><?php echo $j['jr_jump_datetime'] ? substr($j['jr_jump_datetime'], 5, 11) : '—'; ?></td>
        <td><?php echo $j['jr_end_date'] ?: '—'; ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>

<!-- 최근 점프 이력 -->
<?php if ($has_log) { ?>
<div class="jh-section">
  <h3>최근 점프 이력 (최신 50건)</h3>
  <table class="jh-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>광고ID</th>
        <th>회원</th>
        <th>유형</th>
        <th>차감 전</th>
        <th>차감 후</th>
        <th>시각</th>
      </tr>
    </thead>
    <tbody>
<?php
$logs = sql_query("SELECT * FROM g5_jobs_jump_log ORDER BY jl_datetime DESC LIMIT 50");
while ($l = sql_fetch_array($logs)) { ?>
      <tr>
        <td><?php echo $l['jl_id']; ?></td>
        <td><?php echo $l['jr_id']; ?></td>
        <td><?php echo htmlspecialchars($l['mb_id']); ?></td>
        <td><span class="badge-<?php echo $l['jl_type']; ?>"><?php echo $l['jl_type'] === 'auto' ? '자동' : '수동'; ?></span></td>
        <td><?php echo number_format((int)$l['jl_remain_before']); ?></td>
        <td><?php echo number_format((int)$l['jl_remain_after']); ?></td>
        <td><?php echo $l['jl_datetime']; ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>

<!-- 추가 구매 내역 -->
<?php if ($has_purchase) { ?>
<div class="jh-section">
  <h3>점프 추가 구매 내역</h3>
  <table class="jh-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>광고ID</th>
        <th>회원</th>
        <th>구매 횟수</th>
        <th>금액</th>
        <th>상태</th>
        <th>신청일</th>
        <th>확인일</th>
        <th>처리</th>
      </tr>
    </thead>
    <tbody>
<?php
$purchases = sql_query("SELECT * FROM g5_jobs_jump_purchase ORDER BY jp_datetime DESC LIMIT 50");
while ($p = sql_fetch_array($purchases)) { ?>
      <tr>
        <td><?php echo $p['jp_id']; ?></td>
        <td><?php echo $p['jr_id']; ?></td>
        <td><?php echo htmlspecialchars($p['mb_id']); ?></td>
        <td><?php echo number_format((int)$p['jp_count']); ?>회</td>
        <td><?php echo number_format((int)$p['jp_amount']); ?>원</td>
        <td><span class="badge-<?php echo $p['jp_status']; ?>"><?php echo $p['jp_status'] === 'confirmed' ? '확인' : '대기'; ?></span></td>
        <td><?php echo $p['jp_datetime']; ?></td>
        <td><?php echo $p['jp_confirmed_datetime'] ?: '—'; ?></td>
        <td>
<?php if ($p['jp_status'] === 'pending') { ?>
          <button type="button" class="btn-confirm-purchase" onclick="confirmPurchase(<?php echo (int)$p['jp_id']; ?>, <?php echo (int)$p['jr_id']; ?>, <?php echo (int)$p['jp_count']; ?>)">입금확인</button>
<?php } else { echo '완료'; } ?>
        </td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>

<script>
function confirmPurchase(jpId, jrId, count) {
    if (!confirm('점프 ' + count.toLocaleString() + '회 구매를 확인하시겠습니까?\n(잔여 횟수에 즉시 추가됩니다)')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo G5_ADMIN_URL; ?>/eve_jump_purchase_confirm.php';
    var inputs = {jp_id: jpId, jr_id: jrId, token: '<?php echo $token; ?>'};
    for (var k in inputs) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = k; inp.value = inputs[k];
        form.appendChild(inp);
    }
    document.body.appendChild(form);
    form.submit();
}
</script>
<?php } ?>

</div>

<?php
$token = get_session('ss_admin_token') ?: get_admin_token();
require_once './admin.tail.php';
