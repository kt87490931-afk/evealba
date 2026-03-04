<?php
/**
 * 어드민 - 이브알바 결제내역 통합 (통계 + 판매내역 + 월별 매출)
 * 채용공고등록, 점프구매, 썸네일옵션구매
 */
$sub_menu = '910050';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '결제내역 · 매출통계';
require_once './admin.head.php';

$jobs_view_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . '/jobs_view.php' : '/jobs_view.php';

// 탭: list | monthly
$tab = isset($_GET['tab']) ? preg_replace('/[^a-z]/', '', $_GET['tab']) : 'list';
if (!in_array($tab, array('list', 'monthly'))) $tab = 'list';

// 기간 파라미터
$range = isset($_GET['range']) ? preg_replace('/[^a-z]/', '', $_GET['range']) : '';
$st_date = isset($_GET['st_date']) ? preg_replace('/[^0-9\-]/', '', $_GET['st_date']) : '';
$ed_date = isset($_GET['ed_date']) ? preg_replace('/[^0-9\-]/', '', $_GET['ed_date']) : '';
$ptype = isset($_GET['ptype']) ? preg_replace('/[^a-z]/', '', $_GET['ptype']) : 'all';
$sel_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

if ($range === 'today') {
    $st_date = $ed_date = date('Y-m-d');
} elseif ($range === 'week') {
    $st_date = date('Y-m-d', strtotime('monday this week'));
    $ed_date = date('Y-m-d');
} elseif ($range === 'month') {
    $st_date = date('Y-m-01');
    $ed_date = date('Y-m-d');
}
if (!$st_date) $st_date = date('Y-m-d', strtotime('-30 days'));
if (!$ed_date) $ed_date = date('Y-m-d');

$st_sql = "'" . $st_date . " 00:00:00'";
$ed_sql = "'" . $ed_date . " 23:59:59'";

$tb_jr = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
$tb_jp = sql_query("SHOW TABLES LIKE 'g5_jobs_jump_purchase'", false);
$tb_thumb = sql_query("SHOW TABLES LIKE 'g5_jobs_thumb_option_paid'", false);
$has_jr = ($tb_jr && sql_num_rows($tb_jr));
$has_jp = ($tb_jp && sql_num_rows($tb_jp));
$has_thumb = ($tb_thumb && sql_num_rows($tb_thumb));

$jr_cols = array();
if ($has_jr) {
    $c = sql_query("SHOW COLUMNS FROM g5_jobs_register", false);
    while ($r = sql_fetch_array($c)) $jr_cols[$r['Field']] = 1;
}

// ── 통계: 오늘 / 이번주 / 이번달 / 입금대기 ──
$stat_today = 0;
$stat_week = 0;
$stat_month = 0;
$stat_pending = 0;

if ($has_jr) {
    $today_s = date('Y-m-d') . ' 00:00:00';
    $today_e = date('Y-m-d') . ' 23:59:59';
    $r = sql_fetch("SELECT COALESCE(SUM(jr_total_amount), 0) as s FROM g5_jobs_register WHERE jr_status='ongoing' AND (jr_approved=1 OR jr_payment_confirmed=1) AND ((jr_approved_datetime >= '{$today_s}' AND jr_approved_datetime <= '{$today_e}') OR (jr_approved_datetime IS NULL AND jr_datetime >= '{$today_s}' AND jr_datetime <= '{$today_e}'))");
    $stat_today += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jr_total_amount), 0) as s FROM g5_jobs_register WHERE jr_status='ongoing' AND (jr_approved=1 OR jr_payment_confirmed=1) AND ((jr_approved_datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY)) OR (jr_approved_datetime IS NULL AND jr_datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY)))");
    $stat_week += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jr_total_amount), 0) as s FROM g5_jobs_register WHERE jr_status='ongoing' AND (jr_approved=1 OR jr_payment_confirmed=1) AND ((jr_approved_datetime >= DATE_FORMAT(NOW(), '%Y-%m-01')) OR (jr_approved_datetime IS NULL AND jr_datetime >= DATE_FORMAT(NOW(), '%Y-%m-01')))");
    $stat_month += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jr_total_amount), 0) as s FROM g5_jobs_register WHERE jr_status='pending' AND (jr_payment_confirmed=0 OR jr_payment_confirmed IS NULL)");
    $stat_pending += (int)($r['s'] ?? 0);
}
if ($has_jp) {
    $r = sql_fetch("SELECT COALESCE(SUM(jp_amount), 0) as s FROM g5_jobs_jump_purchase WHERE jp_status='confirmed' AND jp_confirmed_datetime >= '" . date('Y-m-d') . " 00:00:00' AND jp_confirmed_datetime <= '" . date('Y-m-d') . " 23:59:59'");
    $stat_today += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jp_amount), 0) as s FROM g5_jobs_jump_purchase WHERE jp_status='confirmed' AND jp_confirmed_datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stat_week += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jp_amount), 0) as s FROM g5_jobs_jump_purchase WHERE jp_status='confirmed' AND jp_confirmed_datetime >= DATE_FORMAT(NOW(), '%Y-%m-01')");
    $stat_month += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jp_amount), 0) as s FROM g5_jobs_jump_purchase WHERE jp_status='pending'");
    $stat_pending += (int)($r['s'] ?? 0);
}
if ($has_thumb) {
    $r = sql_fetch("SELECT COALESCE(SUM(jtp_amount), 0) as s FROM g5_jobs_thumb_option_paid WHERE jtp_created_at >= '" . date('Y-m-d') . " 00:00:00' AND jtp_created_at <= '" . date('Y-m-d') . " 23:59:59'");
    $stat_today += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jtp_amount), 0) as s FROM g5_jobs_thumb_option_paid WHERE jtp_created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stat_week += (int)($r['s'] ?? 0);
    $r = sql_fetch("SELECT COALESCE(SUM(jtp_amount), 0) as s FROM g5_jobs_thumb_option_paid WHERE jtp_created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
    $stat_month += (int)($r['s'] ?? 0);
}

// ── 월별 매출 (tab=monthly) ──
$monthly_rows = array();
if ($tab === 'monthly') {
    for ($m = 1; $m <= 12; $m++) {
        $ym = sprintf('%04d-%02d', $sel_year, $m);
        $ym_start = $ym . '-01 00:00:00';
        $ym_end = date('Y-m-t', strtotime($ym . '-01')) . ' 23:59:59';
        $sum = 0;
        if ($has_jr) {
            $r = sql_fetch("SELECT COALESCE(SUM(jr_total_amount), 0) as s FROM g5_jobs_register WHERE jr_status='ongoing' AND (jr_approved=1 OR jr_payment_confirmed=1) AND ((jr_approved_datetime >= '{$ym_start}' AND jr_approved_datetime <= '{$ym_end}') OR (jr_approved_datetime IS NULL AND jr_datetime >= '{$ym_start}' AND jr_datetime <= '{$ym_end}'))");
            $sum += (int)($r['s'] ?? 0);
        }
        if ($has_jp) {
            $r = sql_fetch("SELECT COALESCE(SUM(jp_amount), 0) as s FROM g5_jobs_jump_purchase WHERE jp_status='confirmed' AND jp_confirmed_datetime >= '{$ym_start}' AND jp_confirmed_datetime <= '{$ym_end}'");
            $sum += (int)($r['s'] ?? 0);
        }
        if ($has_thumb) {
            $r = sql_fetch("SELECT COALESCE(SUM(jtp_amount), 0) as s FROM g5_jobs_thumb_option_paid WHERE jtp_created_at >= '{$ym_start}' AND jtp_created_at <= '{$ym_end}'");
            $sum += (int)($r['s'] ?? 0);
        }
        $monthly_rows[$m] = $sum;
    }
}

// ── 판매내역 조회 (tab=list) ──
$items = array();
$opt_labels = array('badge'=>'뱃지','motion'=>'제목모션','wave'=>'컬러웨이브','border'=>'테두리','premium_color'=>'유료컬러');
$val_labels = array('beginner'=>'초보환영','room'=>'원룸제공','pickup'=>'픽업가능','gold'=>'골드','pink'=>'핫핑크','charcoal'=>'차콜','shimmer'=>'글씨확대','soft-blink'=>'소프트블링크','glow'=>'글로우','bounce'=>'바운스','1'=>'컬러웨이브','P1'=>'메탈릭골드','P2'=>'메탈릭실버','P3'=>'카본','P4'=>'오로라');

if ($tab === 'list' && $has_jr && ($ptype === 'all' || $ptype === 'register')) {
    $where = " (jr_approved_datetime >= {$st_sql} AND jr_approved_datetime <= {$ed_sql}) OR (jr_approved_datetime IS NULL AND jr_datetime >= {$st_sql} AND jr_datetime <= {$ed_sql}) OR (jr_status='pending' AND jr_datetime >= {$st_sql} AND jr_datetime <= {$ed_sql}) ";
    $sel = "jr_id, mb_id, jr_total_amount, jr_datetime, jr_approved_datetime, jr_status, jr_approved, jr_payment_confirmed, jr_ad_period";
    if (isset($jr_cols['jr_ad_labels']) && $jr_cols['jr_ad_labels']) $sel .= ", jr_ad_labels";
    $r = sql_query("SELECT {$sel} FROM g5_jobs_register WHERE {$where} ORDER BY COALESCE(jr_approved_datetime, jr_datetime) DESC LIMIT 500");
    while ($row = sql_fetch_array($r)) {
        $dt = $row['jr_approved_datetime'] ?: $row['jr_datetime'];
        $status = ($row['jr_status'] === 'ongoing' && ($row['jr_approved'] || $row['jr_payment_confirmed'])) ? '완료' : (($row['jr_status'] === 'pending') ? '입금대기' : '마감');
        $labels = isset($row['jr_ad_labels']) ? trim(str_replace(',', ', ', $row['jr_ad_labels'])) : '';
        $detail = $labels ?: ('기간 ' . ($row['jr_ad_period'] ?? 0) . '일');
        $items[] = array(
            'type' => 'register',
            'dt' => $dt,
            'jr_id' => $row['jr_id'],
            'mb_id' => $row['mb_id'],
            'amount' => (int)$row['jr_total_amount'],
            'detail' => $detail,
            'coupon' => '—',
            'discount' => 0,
            'status' => $status
        );
    }
}
if ($tab === 'list' && $has_jp && ($ptype === 'all' || $ptype === 'jump')) {
    $where = " (jp_status='confirmed' AND jp_confirmed_datetime >= {$st_sql} AND jp_confirmed_datetime <= {$ed_sql}) OR (jp_status='pending' AND jp_datetime >= {$st_sql} AND jp_datetime <= {$ed_sql}) ";
    $r = sql_query("SELECT p.jp_id, p.jr_id, p.mb_id, p.jp_count, p.jp_amount, p.jp_status, p.jp_datetime, p.jp_confirmed_datetime FROM g5_jobs_jump_purchase p WHERE {$where} ORDER BY COALESCE(p.jp_confirmed_datetime, p.jp_datetime) DESC LIMIT 500");
    while ($row = sql_fetch_array($r)) {
        $dt = ($row['jp_status'] === 'confirmed' && $row['jp_confirmed_datetime']) ? $row['jp_confirmed_datetime'] : $row['jp_datetime'];
        $items[] = array(
            'type' => 'jump',
            'dt' => $dt,
            'jr_id' => $row['jr_id'],
            'mb_id' => $row['mb_id'],
            'amount' => (int)$row['jp_amount'],
            'detail' => '점프 ' . number_format((int)$row['jp_count']) . '회 추가',
            'coupon' => '—',
            'discount' => 0,
            'status' => $row['jp_status'] === 'confirmed' ? '입금확인' : '입금대기'
        );
    }
}
if ($tab === 'list' && $has_thumb && ($ptype === 'all' || $ptype === 'thumb')) {
    $tb_coupon = sql_query("SHOW TABLES LIKE 'g5_ev_coupon'", false);
    $has_coupon = ($tb_coupon && sql_num_rows($tb_coupon));
    $join_coupon = $has_coupon ? " LEFT JOIN g5_ev_coupon c ON t.jtp_coupon_id = c.ec_id " : "";
    $sel_coupon = $has_coupon ? ", c.ec_name as coupon_name " : "";
    $r = sql_query("SELECT t.jtp_id, t.jr_id, t.mb_id, t.jtp_option_key, t.jtp_option_value, t.jtp_amount, t.jtp_coupon_id, t.jtp_coupon_discount, t.jtp_created_at, t.jtp_valid_until {$sel_coupon} FROM g5_jobs_thumb_option_paid t {$join_coupon} WHERE t.jtp_created_at >= {$st_sql} AND t.jtp_created_at <= {$ed_sql} ORDER BY t.jtp_created_at DESC LIMIT 500");
    while ($row = sql_fetch_array($r)) {
        $opt = $opt_labels[$row['jtp_option_key']] ?? $row['jtp_option_key'];
        $val = $val_labels[$row['jtp_option_value']] ?? $row['jtp_option_value'];
        $disc = (int)($row['jtp_coupon_discount'] ?? 0);
        $cname = ($has_coupon && !empty($row['coupon_name'])) ? $row['coupon_name'] : (($disc > 0) ? '쿠폰' : '—');
        if ($disc <= 0) $cname = '—';
        $items[] = array(
            'type' => 'thumb',
            'dt' => $row['jtp_created_at'],
            'jr_id' => $row['jr_id'],
            'mb_id' => $row['mb_id'],
            'amount' => (int)$row['jtp_amount'],
            'detail' => '썸네일옵션: ' . $opt . ' - ' . $val,
            'coupon' => $cname,
            'discount' => $disc,
            'status' => '완료'
        );
    }
}
if ($tab === 'list') {
    usort($items, function($a, $b) { return strcmp($b['dt'], $a['dt']); });
    $items = array_slice($items, 0, 300);
}

$type_labels = array('register' => '채용공고등록', 'jump' => '점프구매', 'thumb' => '썸네일옵션');
?>
<style>
.ep-wrap{font-family:'Noto Sans KR',sans-serif}
.ep-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
@media(max-width:900px){.ep-cards{grid-template-columns:repeat(2,1fr)}}
.ep-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;text-align:center}
.ep-card-label{font-size:11px;color:#888;margin-bottom:4px}
.ep-card-val{font-size:20px;font-weight:900;color:#333}
.ep-card-val.pink{color:#FF1B6B}
.ep-card-val.blue{color:#3B82F6}
.ep-card-val.green{color:#10B981}
.ep-card-val.orange{color:#F59E0B}
.ep-tabs{margin-bottom:16px;display:flex;gap:4px}
.ep-tabs a{padding:8px 16px;border:1px solid #ddd;background:#fff;border-radius:6px;text-decoration:none;color:#555;font-size:13px;font-weight:600}
.ep-tabs a:hover{background:#f8f9fa}
.ep-tabs a.on{background:#FF1B6B;color:#fff;border-color:#FF1B6B}
.ep-filter{margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.ep-filter input[type=date]{padding:6px 10px;border:1px solid #ddd;border-radius:5px}
.ep-filter .ep-qbtn{padding:6px 12px;border:1px solid #ddd;background:#fff;border-radius:5px;text-decoration:none;color:#333;font-size:12px;cursor:pointer}
.ep-filter .ep-qbtn.on{background:#FF1B6B;color:#fff;border-color:#FF1B6B}
.ep-filter select{padding:6px 10px;border:1px solid #ddd;border-radius:5px}
.ep-table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;margin-bottom:20px;font-size:12px}
.ep-table th{background:#f8f9fa;padding:10px 12px;text-align:left;font-weight:700;color:#555;border-bottom:2px solid #e5e7eb}
.ep-table td{padding:10px 12px;color:#333;border-bottom:1px solid #f0f0f0}
.ep-type{font-size:10px;padding:2px 6px;border-radius:4px;background:#f0f0f0;color:#555}
.ep-type.reg{background:#e8f4fd;color:#1976d2}
.ep-type.jump{background:#fff3e0;color:#e65100}
.ep-type.thumb{background:#f3e5f5;color:#7b1fa2}
.ep-status{font-size:10px;padding:2px 6px;border-radius:4px}
.ep-status.done{background:#e8f5e9;color:#2e7d32}
.ep-status.confirmed{background:#e3f2fd;color:#1565c0}
.ep-status.pending{background:#fff8e1;color:#f57f17}
.ep-monthly-wrap{background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
.ep-monthly-table{width:100%;border-collapse:collapse;font-size:13px}
.ep-monthly-table th{background:#f8f9fa;padding:12px;text-align:center;font-weight:700;color:#555}
.ep-monthly-table td{padding:12px;text-align:center;border-bottom:1px solid #f0f0f0}
.ep-monthly-table td:nth-child(1){font-weight:600;color:#333}
.ep-monthly-table .ep-m-sum{font-weight:900;color:#FF1B6B}
</style>

<div class="ep-wrap">
<h2 class="sound_only">결제내역 · 매출통계</h2>

<!-- 통계 카드 -->
<div class="ep-cards">
  <div class="ep-card">
    <div class="ep-card-label">오늘 매출</div>
    <div class="ep-card-val pink"><?php echo number_format($stat_today); ?>원</div>
  </div>
  <div class="ep-card">
    <div class="ep-card-label">이번 주 매출</div>
    <div class="ep-card-val blue"><?php echo number_format($stat_week); ?>원</div>
  </div>
  <div class="ep-card">
    <div class="ep-card-label">이번 달 매출</div>
    <div class="ep-card-val green"><?php echo number_format($stat_month); ?>원</div>
  </div>
  <div class="ep-card">
    <div class="ep-card-label">입금대기</div>
    <div class="ep-card-val orange"><?php echo number_format($stat_pending); ?>원</div>
  </div>
</div>

<!-- 탭 -->
<div class="ep-tabs">
  <a href="?sub_menu=910050&tab=list" class="<?php echo $tab === 'list' ? 'on' : ''; ?>">판매내역</a>
  <a href="?sub_menu=910050&tab=monthly" class="<?php echo $tab === 'monthly' ? 'on' : ''; ?>">월별 매출</a>
</div>

<?php if ($tab === 'monthly') { ?>
<!-- 월별 매출 -->
<form method="get" class="ep-filter">
  <input type="hidden" name="sub_menu" value="910050">
  <input type="hidden" name="tab" value="monthly">
  <span>연도</span>
  <select name="year" onchange="this.form.submit()">
    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--) { ?>
    <option value="<?php echo $y; ?>"<?php echo $sel_year === $y ? ' selected' : ''; ?>><?php echo $y; ?>년</option>
    <?php } ?>
  </select>
</form>
<div class="ep-monthly-wrap">
<table class="ep-monthly-table">
<caption>월별 매출 (<?php echo $sel_year; ?>년)</caption>
<thead><tr><th>월</th><th>매출</th><th>월</th><th>매출</th><th>월</th><th>매출</th><th>월</th><th>매출</th></tr></thead>
<tbody>
<?php for ($row = 0; $row < 3; $row++) { ?>
<tr>
<?php for ($col = 0; $col < 4; $col++) {
  $m = $row * 4 + $col + 1;
  $sum = $monthly_rows[$m] ?? 0;
?>
  <td><?php echo $m; ?>월</td>
  <td class="ep-m-sum"><?php echo number_format($sum); ?>원</td>
<?php } ?>
</tr>
<?php } ?>
<tr><td colspan="2" style="font-weight:800;"><?php echo $sel_year; ?>년 합계</td><td colspan="6" class="ep-m-sum"><?php echo number_format(array_sum($monthly_rows)); ?>원</td></tr>
</tbody>
</table>
</div>

<?php } else { ?>
<!-- 기간/유형 필터 -->
<form method="get" action="" class="ep-filter">
  <input type="hidden" name="sub_menu" value="910050">
  <input type="hidden" name="tab" value="list">
  <span>기간</span>
  <input type="date" name="st_date" value="<?php echo htmlspecialchars($st_date); ?>">
  <span>~</span>
  <input type="date" name="ed_date" value="<?php echo htmlspecialchars($ed_date); ?>">
  <a href="?sub_menu=910050&tab=list&range=today&ptype=<?php echo $ptype; ?>" class="ep-qbtn<?php echo $range==='today'?' on':''; ?>">오늘</a>
  <a href="?sub_menu=910050&tab=list&range=week&ptype=<?php echo $ptype; ?>" class="ep-qbtn<?php echo $range==='week'?' on':''; ?>">이번주</a>
  <a href="?sub_menu=910050&tab=list&range=month&ptype=<?php echo $ptype; ?>" class="ep-qbtn<?php echo $range==='month'?' on':''; ?>">이번달</a>
  <span>유형</span>
  <select name="ptype" onchange="this.form.submit()">
    <option value="all"<?php echo $ptype==='all'?' selected':''; ?>>전체</option>
    <option value="register"<?php echo $ptype==='register'?' selected':''; ?>>채용공고등록</option>
    <option value="jump"<?php echo $ptype==='jump'?' selected':''; ?>>점프구매</option>
    <option value="thumb"<?php echo $ptype==='thumb'?' selected':''; ?>>썸네일옵션</option>
  </select>
  <button type="submit" class="btn_frmline">조회</button>
</form>

<!-- 판매내역 테이블 -->
<div class="tbl_head01 tbl_wrap">
<table class="ep-table">
<caption>판매내역</caption>
<thead>
<tr>
  <th scope="col">일시</th>
  <th scope="col">유형</th>
  <th scope="col">상세 (결제한 광고)</th>
  <th scope="col">쿠폰</th>
  <th scope="col">할인</th>
  <th scope="col">금액</th>
  <th scope="col">상태</th>
  <th scope="col">회원</th>
</tr>
</thead>
<tbody>
<?php if (count($items) > 0) {
  foreach ($items as $row) {
    $cls = 'ep-type ' . ($row['type'] === 'register' ? 'reg' : ($row['type'] === 'jump' ? 'jump' : 'thumb'));
    if ($row['status'] === '완료') $st_cls = 'ep-status done';
    elseif ($row['status'] === '입금확인') $st_cls = 'ep-status confirmed';
    else $st_cls = 'ep-status pending';
?>
<tr>
  <td><?php echo date('Y-m-d H:i', strtotime($row['dt'])); ?></td>
  <td><span class="<?php echo $cls; ?>"><?php echo htmlspecialchars($type_labels[$row['type']] ?? $row['type']); ?></span></td>
  <td><?php echo htmlspecialchars($row['detail']); ?></td>
  <td><?php echo htmlspecialchars($row['coupon']); ?></td>
  <td><?php echo $row['discount'] > 0 ? '-' . number_format($row['discount']) . '원' : '—'; ?></td>
  <td><?php echo number_format($row['amount']); ?>원</td>
  <td><span class="<?php echo $st_cls; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
  <td><?php echo htmlspecialchars($row['mb_id']); ?></td>
</tr>
<?php }
} else { ?>
<tr><td colspan="8" style="text-align:center;padding:40px;color:#888;">해당 기간 결제 내역이 없습니다.</td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php } ?>

<p class="local_desc01 local_desc" style="margin-top:12px;">채용공고등록·점프·썸네일옵션 통합. 상세는 jr_ad_labels(광고라벨) 기준. 입금확인=점프 입금확인, 완료=채용공고/썸네일 결제완료.</p>
</div>

<?php
require_once './admin.tail.php';
