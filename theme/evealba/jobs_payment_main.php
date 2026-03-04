<?php if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_register_url = $jobs_base_url ? $jobs_base_url.'/jobs_register.php' : '/jobs_register.php';
$jobs_view_url_base = $jobs_base_url ? $jobs_base_url.'/jobs_view.php?jr_id=' : '/jobs_view.php?jr_id=';

$list_register = array();
$list_thumb = array();
$list_jump = array();
$mb_id_esc = $is_member ? addslashes($member['mb_id']) : '';

if ($is_member && $mb_id_esc) {
    $tb_jr = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if ($tb_jr && sql_num_rows($tb_jr)) {
        $r = sql_query("SELECT jr_id, jr_datetime, jr_total_amount, jr_subject_display, jr_status, jr_ad_period FROM g5_jobs_register WHERE mb_id = '{$mb_id_esc}' ORDER BY jr_datetime DESC");
        while ($row = sql_fetch_array($r)) {
            $st = $row['jr_status'];
            $status_label = ($st === 'pending') ? '입금대기중' : (($st === 'ongoing') ? '진행중' : '마감');
            $list_register[] = array(
                'jr_id' => $row['jr_id'],
                'datetime2' => date('Y-m-d H:i', strtotime($row['jr_datetime'])),
                'total_amount' => (int)($row['jr_total_amount'] ?? 0),
                'subject' => $row['jr_subject_display'] ?: '[제목없음]',
                'status_label' => $status_label,
                'ad_period' => $row['jr_ad_period'] ? $row['jr_ad_period'].'일' : '—'
            );
        }
    }
    $tb_thumb = sql_query("SHOW TABLES LIKE 'g5_jobs_thumb_option_paid'", false);
    if ($tb_thumb && sql_num_rows($tb_thumb)) {
        @include_once(G5_LIB_PATH.'/ev_thumb_option.lib.php');
        $opt_labels = function_exists('ev_thumb_get_daily_rates') ? array('badge'=>'뱃지','motion'=>'제목모션','wave'=>'컬러웨이브','border'=>'테두리','premium_color'=>'유료컬러') : array();
        $val_labels = array('beginner'=>'초보환영','room'=>'원룸제공','gold'=>'골드테두리','pink'=>'핫핑크','charcoal'=>'차콜','shimmer'=>'글씨확대','soft-blink'=>'소프트블링크','glow'=>'글로우','bounce'=>'바운스','1'=>'컬러웨이브','P1'=>'메탈릭골드','P2'=>'메탈릭실버','P3'=>'카본','P4'=>'오로라');
        $r = @sql_query("SELECT t.jtp_id, t.jr_id, t.jtp_option_key, t.jtp_option_value, t.jtp_valid_until, t.jtp_amount, t.jtp_created_at, j.jr_subject_display FROM g5_jobs_thumb_option_paid t LEFT JOIN g5_jobs_register j ON t.jr_id = j.jr_id WHERE t.mb_id = '{$mb_id_esc}' ORDER BY t.jtp_created_at DESC LIMIT 200", false);
        if ($r) while ($row = sql_fetch_array($r)) {
            $key = $row['jtp_option_key'];
            $val = $row['jtp_option_value'];
            $opt_name = $opt_labels[$key] ?? $key;
            $val_name = $val_labels[$val] ?? $val;
            $list_thumb[] = array(
                'jtp_id' => $row['jtp_id'],
                'jr_id' => $row['jr_id'],
                'datetime2' => date('Y-m-d H:i', strtotime($row['jtp_created_at'])),
                'amount' => (int)($row['jtp_amount'] ?? 0),
                'subject' => $row['jr_subject_display'] ?: ('#'.$row['jr_id']),
                'option_label' => $opt_name . ' - ' . $val_name,
                'valid_until' => $row['jtp_valid_until'] ?? '—'
            );
        }
    }
    $tb_jump = sql_query("SHOW TABLES LIKE 'g5_jobs_jump_purchase'", false);
    if ($tb_jump && sql_num_rows($tb_jump)) {
        $r = @sql_query("SELECT p.jp_id, p.jr_id, p.jp_count, p.jp_amount, p.jp_status, p.jp_datetime, p.jp_confirmed_datetime, j.jr_subject_display FROM g5_jobs_jump_purchase p LEFT JOIN g5_jobs_register j ON p.jr_id = j.jr_id WHERE p.mb_id = '{$mb_id_esc}' ORDER BY p.jp_datetime DESC LIMIT 200", false);
        if ($r) while ($row = sql_fetch_array($r)) {
            $st = $row['jp_status'];
            $status_label = ($st === 'confirmed') ? '입금확인' : '입금대기';
            $list_jump[] = array(
                'jp_id' => $row['jp_id'],
                'jr_id' => $row['jr_id'],
                'datetime2' => date('Y-m-d H:i', strtotime($row['jp_datetime'])),
                'amount' => (int)($row['jp_amount'] ?? 0),
                'count' => (int)($row['jp_count'] ?? 0),
                'subject' => $row['jr_subject_display'] ?: ('#'.$row['jr_id']),
                'status_label' => $status_label
            );
        }
    }
}
$total_count = count($list_register) + count($list_thumb) + count($list_jump);
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div class="page-title-bar">
  <h2 class="page-title">💳 유료결제 내역</h2>
</div>

<div id="bo_list" class="ev-board-list jobs-payment-list" style="width:100%;">
  <div class="board-topbar">
    <div class="board-topbar-left">
      <h2 class="board-page-title">유료결제 내역</h2>
      <span class="board-count">총 <strong><?php echo number_format($total_count); ?></strong>건</span>
    </div>
    <div class="board-btns">
      <a href="<?php echo $jobs_register_url; ?>" class="btn-write">✏️ 채용공고 등록</a>
    </div>
  </div>

  <!-- ① 채용공고 등록 -->
  <div class="payment-section" style="margin-bottom:32px;">
    <h3 class="payment-section-title" style="font-size:16px;margin:0 0 12px;color:#333;border-bottom:2px solid #FF1B6B;padding-bottom:8px;">📝 채용공고 등록</h3>
    <div class="board-wrap jobs-ongoing-wrap">
      <div class="board-thead jobs-ongoing-thead">
        <div class="board-th">등록일시</div>
        <div class="board-th td-title">채용정보</div>
        <div class="board-th">금액</div>
        <div class="board-th">기간</div>
        <div class="board-th">상태</div>
      </div>
      <?php if (count($list_register) > 0) {
        foreach ($list_register as $row) {
          $view_href = $jobs_view_url_base . $row['jr_id'];
      ?>
      <div class="board-row jobs-ongoing-row" style="cursor:default;">
        <div class="board-td td-date"><?php echo htmlspecialchars($row['datetime2']); ?></div>
        <div class="board-td td-title"><a href="<?php echo htmlspecialchars($view_href); ?>" class="post-title-text" style="text-decoration:none;color:inherit;"><?php echo htmlspecialchars($row['subject']); ?></a></div>
        <div class="board-td td-price"><?php echo number_format($row['total_amount']); ?>원</div>
        <div class="board-td"><?php echo htmlspecialchars($row['ad_period']); ?></div>
        <div class="board-td td-status"><span class="status-badge"><?php echo htmlspecialchars($row['status_label']); ?></span></div>
      </div>
      <?php } } else { ?>
      <div class="board-row empty-row">
        <div class="board-td" style="grid-column:1/-1;text-align:center;padding:24px 20px;color:#888;">채용공고 등록 내역이 없습니다.</div>
      </div>
      <?php } ?>
    </div>
  </div>

  <!-- ② 썸네일옵션 구매 -->
  <div class="payment-section" style="margin-bottom:32px;">
    <h3 class="payment-section-title" style="font-size:16px;margin:0 0 12px;color:#333;border-bottom:2px solid #FF1B6B;padding-bottom:8px;">🖼 썸네일옵션 구매</h3>
    <div class="board-wrap jobs-ongoing-wrap">
      <div class="board-thead jobs-ongoing-thead">
        <div class="board-th">구매일시</div>
        <div class="board-th td-title">채용정보</div>
        <div class="board-th">옵션</div>
        <div class="board-th">금액</div>
        <div class="board-th">유효기간</div>
      </div>
      <?php if (count($list_thumb) > 0) {
        foreach ($list_thumb as $row) {
          $view_href = $jobs_view_url_base . $row['jr_id'];
      ?>
      <div class="board-row jobs-ongoing-row" style="cursor:default;">
        <div class="board-td td-date"><?php echo htmlspecialchars($row['datetime2']); ?></div>
        <div class="board-td td-title"><a href="<?php echo htmlspecialchars($view_href); ?>" style="text-decoration:none;color:inherit;"><?php echo htmlspecialchars($row['subject']); ?></a></div>
        <div class="board-td"><?php echo htmlspecialchars($row['option_label']); ?></div>
        <div class="board-td td-price"><?php echo number_format($row['amount']); ?>원</div>
        <div class="board-td"><?php echo htmlspecialchars($row['valid_until']); ?></div>
      </div>
      <?php } } else { ?>
      <div class="board-row empty-row">
        <div class="board-td" style="grid-column:1/-1;text-align:center;padding:24px 20px;color:#888;">썸네일옵션 구매 내역이 없습니다.</div>
      </div>
      <?php } ?>
    </div>
  </div>

  <!-- ③ 점프 구매 -->
  <div class="payment-section" style="margin-bottom:32px;">
    <h3 class="payment-section-title" style="font-size:16px;margin:0 0 12px;color:#333;border-bottom:2px solid #FF1B6B;padding-bottom:8px;">⚡ 점프 구매</h3>
    <div class="board-wrap jobs-ongoing-wrap">
      <div class="board-thead jobs-ongoing-thead">
        <div class="board-th">구매일시</div>
        <div class="board-th td-title">채용정보</div>
        <div class="board-th">점프 횟수</div>
        <div class="board-th">금액</div>
        <div class="board-th">상태</div>
      </div>
      <?php if (count($list_jump) > 0) {
        foreach ($list_jump as $row) {
          $view_href = $jobs_view_url_base . $row['jr_id'];
      ?>
      <div class="board-row jobs-ongoing-row" style="cursor:default;">
        <div class="board-td td-date"><?php echo htmlspecialchars($row['datetime2']); ?></div>
        <div class="board-td td-title"><a href="<?php echo htmlspecialchars($view_href); ?>" style="text-decoration:none;color:inherit;"><?php echo htmlspecialchars($row['subject']); ?></a></div>
        <div class="board-td"><?php echo number_format($row['count']); ?>회</div>
        <div class="board-td td-price"><?php echo number_format($row['amount']); ?>원</div>
        <div class="board-td td-status"><span class="status-badge"><?php echo htmlspecialchars($row['status_label']); ?></span></div>
      </div>
      <?php } } else { ?>
      <div class="board-row empty-row">
        <div class="board-td" style="grid-column:1/-1;text-align:center;padding:24px 20px;color:#888;">점프 구매 내역이 없습니다.</div>
      </div>
      <?php } ?>
    </div>
  </div>

  <?php if ($total_count === 0) { ?>
  <div class="board-row empty-row" style="text-align:center;padding:40px 20px;">
    <p style="font-size:15px;color:#888;">결제 내역이 없습니다.</p>
    <a href="<?php echo $jobs_register_url; ?>" class="btn-write" style="margin-top:16px;display:inline-flex;">✏️ 채용공고 등록</a>
  </div>
  <?php } ?>
</div>
