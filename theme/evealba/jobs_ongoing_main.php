<?php if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_register_url = $jobs_base_url ? $jobs_base_url.'/jobs_register.php' : '/jobs_register.php';
$jobs_extend_popup_url = $jobs_base_url ? $jobs_base_url.'/jobs_extend_popup.php' : '/jobs_extend_popup.php';
$jobs_view_url_base = $jobs_base_url ? $jobs_base_url.'/jobs_view.php' : '/jobs_view.php';

$list = array();
$total_count = 0;
if ($is_member) {
    $jr_table = 'g5_jobs_register';
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if ($tb_check && sql_num_rows($tb_check)) {
        $mb_id_esc = addslashes($member['mb_id']);
        $today_esc = date('Y-m-d');
        $sql = "SELECT * FROM `g5_jobs_register` WHERE mb_id = '{$mb_id_esc}' AND jr_status IN ('pending','ongoing') AND (jr_end_date IS NULL OR jr_end_date >= '{$today_esc}' OR jr_status = 'pending') ORDER BY jr_datetime DESC";
        $result = sql_query($sql);
        $today = date('Y-m-d');
        while ($row = sql_fetch_array($result)) {
            $status = $row['jr_status'];
            $payment_ok = !empty($row['jr_payment_confirmed']);
            $approved = !empty($row['jr_approved']);
            if ($status === 'ongoing') {
                $status_label = '진행중';
                $status_class = 'ongoing';
            } elseif ($status === 'pending' && $payment_ok) {
                $status_label = '입금확인';
                $status_class = 'payment-ok';
            } else {
                $status_label = '입금대기중';
                $status_class = 'payment-wait';
            }
            $remaining = '—';
            if ($approved && !empty($row['jr_end_date'])) {
                $end_ts = strtotime($row['jr_end_date']);
                $today_ts = strtotime($today);
                if ($end_ts >= $today_ts) {
                    $remaining = (int)(($end_ts - $today_ts) / 86400) . '일';
                } else {
                    $remaining = '마감';
                }
            }
            $ad_labels = isset($row['jr_ad_labels']) ? trim($row['jr_ad_labels']) : '';
            if (!$ad_labels) {
                $jc = (int)($row['jr_jump_count'] ?? 0);
                $period = (int)($row['jr_ad_period'] ?? 30);
                $ad_labels = ($jc <= 300) ? '줄광고 30일' : (($jc <= 700) ? '줄광고 60일' : (($jc <= 1200) ? '줄광고 90일' : "줄광고 {$period}일"));
            }
            $jr_data_arr = !empty($row['jr_data']) ? @json_decode($row['jr_data'], true) : array();
            if (!is_array($jr_data_arr)) $jr_data_arr = array();
            $thumb_grad = isset($jr_data_arr['thumb_gradient']) ? $jr_data_arr['thumb_gradient'] : '1';
            $thumb_title_text = $jr_data_arr['thumb_title'] ?? $row['jr_nickname'] ?? $row['jr_company'] ?? '';
            $thumb_icon_key = isset($jr_data_arr['thumb_icon']) ? trim($jr_data_arr['thumb_icon']) : '';
            $thumb_border_key = isset($jr_data_arr['thumb_border']) ? trim($jr_data_arr['thumb_border']) : '';
            $can_view = ($status === 'ongoing') || $payment_ok;
            $list[] = array(
                'jr_id' => $row['jr_id'],
                'wr_id' => $row['jr_id'],
                'subject' => $row['jr_title'] ?: ($row['jr_subject_display'] ?: '[제목없음]'),
                'datetime2' => date('Y-m-d', strtotime($row['jr_datetime'])),
                'status' => $status,
                'status_class' => $status_class,
                'status_label' => $status_label,
                'ad_period' => $row['jr_ad_period'] ? $row['jr_ad_period'].'일' : '—',
                'jump_count' => $row['jr_jump_count'],
                'remaining' => $remaining,
                'ad_labels' => $ad_labels,
                'total_amount' => (int)($row['jr_total_amount'] ?? 0),
                'nickname' => isset($row['jr_nickname']) ? trim($row['jr_nickname']) : '',
                'thumb_grad' => $thumb_grad,
                'thumb_title_text' => $thumb_title_text,
                'thumb_icon' => $thumb_icon_key,
                'thumb_border' => $thumb_border_key,
                'view_href' => $can_view ? ($jobs_view_url_base.'?jr_id='.$row['jr_id']) : '#',
                'can_view' => $can_view
            );
        }
        $total_count = count($list);
    }
}

$_grad_map = array(
    '1'=>'linear-gradient(135deg,rgb(255,182,193),rgb(255,105,180))',
    '2'=>'linear-gradient(135deg,rgb(255,218,185),rgb(255,140,105))',
    '3'=>'linear-gradient(135deg,rgb(173,216,230),rgb(100,149,237))',
    '4'=>'linear-gradient(135deg,rgb(221,160,221),rgb(186,85,211))',
    '5'=>'linear-gradient(135deg,rgb(144,238,144),rgb(60,179,113))',
    '6'=>'linear-gradient(135deg,rgb(255,255,224),rgb(255,215,0))',
    'P1'=>'linear-gradient(135deg,rgb(255,215,0),rgb(218,165,32),rgb(184,134,11))',
    'P2'=>'linear-gradient(135deg,rgb(192,192,192),rgb(169,169,169),rgb(128,128,128))',
    'P3'=>'linear-gradient(135deg,rgb(30,30,30),rgb(60,60,60),rgb(30,30,30))',
    'P4'=>'linear-gradient(135deg,rgb(255,105,180),rgb(138,43,226),rgb(0,191,255))',
);
$_border_map = array(
    'gold'=>'border:none;box-shadow:inset 0 0 0 1px #FFD700,0 0 0 1px #FFD700;',
    'pink'=>'border:none;box-shadow:inset 0 0 0 1px #FF1B6B,0 0 0 1px #FF1B6B;',
    'charcoal'=>'border:none;box-shadow:inset 0 0 0 1px #444,0 0 0 1px #444;',
    'royalblue'=>'border:none;box-shadow:inset 0 0 0 1px #4169E1,0 0 0 1px #4169E1;',
    'royalpurple'=>'border:none;box-shadow:inset 0 0 0 1px #7B2FBE,0 0 0 1px #7B2FBE;',
);
$_icon_map = array(
    'new'=>array('bg'=>'#FF1B6B','label'=>'초보환영'),
    'all'=>array('bg'=>'#FF6B35','label'=>'원종제공'),
    'lux'=>array('bg'=>'#8B5CF6','label'=>'고급시설'),
    'hair'=>array('bg'=>'#EC4899','label'=>'블랙 관리'),
    'money'=>array('bg'=>'#10B981','label'=>'포비지급'),
    'sizex'=>array('bg'=>'#3B82F6','label'=>'사이즈X'),
    'set'=>array('bg'=>'#F59E0B','label'=>'세트환영'),
    'dual'=>array('bg'=>'#6366F1','label'=>'떡업가능'),
    'once'=>array('bg'=>'#14B8A6','label'=>'1회원제운영'),
    'room'=>array('bg'=>'#E11D48','label'=>'공비지급'),
);
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div id="bo_list" class="ev-board-list jobs-ongoing-list" style="width:100%;">

  <div class="board-topbar">
    <div class="board-topbar-left">
      <h2 class="board-page-title">진행중인 채용정보</h2>
      <span class="board-count">총 <strong><?php echo number_format($total_count); ?></strong>건</span>
    </div>
  </div>

  <div class="board-wrap jobs-ongoing-wrap">
    <div class="board-thead jobs-ongoing-thead">
      <div class="board-th">번호</div>
      <div class="board-th">썸네일</div>
      <div class="board-th th-title">제목</div>
      <div class="board-th th-date">등록일</div>
      <div class="board-th">광고기간</div>
      <div class="board-th">남은기간</div>
      <div class="board-th">상태</div>
      <div class="board-th">연장</div>
    </div>

    <?php if (count($list) > 0) {
      $num = $total_count;
      foreach ($list as $row) {
        $extend_url = $jobs_extend_popup_url . '?jr_id=' . (isset($row['jr_id']) ? $row['jr_id'] : '');
        $_tg = isset($row['thumb_grad']) ? $row['thumb_grad'] : '1';
        $_tbg = isset($_grad_map[$_tg]) ? $_grad_map[$_tg] : $_grad_map['1'];
        $_tt = htmlspecialchars($row['thumb_title_text'] ?? '');
        $_tb = isset($row['thumb_border']) && isset($_border_map[$row['thumb_border']]) ? $_border_map[$row['thumb_border']] : '';
        $_ti = isset($row['thumb_icon']) && isset($_icon_map[$row['thumb_icon']]) ? $_icon_map[$row['thumb_icon']] : null;
    ?>
    <a href="<?php echo isset($row['view_href']) ? htmlspecialchars($row['view_href']) : '#'; ?>" class="board-row jobs-ongoing-row<?php echo empty($row['can_view']) ? ' row-blocked' : ''; ?>"<?php if (empty($row['can_view'])) { ?> onclick="event.preventDefault();alert('입금확인 후 이용 가능합니다.');return false;"<?php } ?>>
      <div class="board-td td-num"><?php echo $num--; ?></div>
      <div class="board-td td-thumb">
        <div class="mini-thumb" style="width:56px;height:56px;border-radius:10px;background:<?php echo $_tbg; ?>;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;<?php echo $_tb; ?>">
          <span style="color:#fff;font-size:10px;font-weight:900;text-align:center;line-height:1.3;text-shadow:0 1px 3px rgba(0,0,0,.3);padding:2px 4px;"><?php echo $_tt ?: '—'; ?></span>
          <?php if ($_ti) { ?>
          <span style="position:absolute;top:2px;right:2px;font-size:7px;font-weight:900;padding:1px 4px;border-radius:6px;color:#fff;background:<?php echo $_ti['bg']; ?>"><?php echo $_ti['label']; ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="board-td td-title">
        <div class="td-title-inner">
          <div class="td-title-top">
            <span class="post-title-text"><?php echo isset($row['subject']) ? htmlspecialchars($row['subject']) : ''; ?></span>
          </div>
          <div class="td-title-bottom">
            <?php if (!empty($row['total_amount'])) { ?><span class="td-price"><?php echo number_format($row['total_amount']); ?>원</span><?php } ?>
            <?php if (!empty($row['ad_labels'])) { ?><span class="cat-badge cat-jobs"><?php echo htmlspecialchars(cut_str(str_replace(',', ', ', $row['ad_labels']), 20)); ?></span><?php } ?>
          </div>
        </div>
      </div>
      <div class="board-td td-date"><?php echo isset($row['datetime2']) ? $row['datetime2'] : ''; ?></div>
      <div class="board-td td-period"><?php echo isset($row['ad_period']) ? $row['ad_period'] : '—'; ?></div>
      <div class="board-td td-remaining"><?php echo isset($row['remaining']) ? $row['remaining'] : '—'; ?></div>
      <div class="board-td td-status">
        <span class="status-badge status-<?php echo isset($row['status_class']) ? $row['status_class'] : 'payment-wait'; ?>"><?php echo isset($row['status_label']) ? htmlspecialchars($row['status_label']) : ''; ?></span>
        <?php if (empty($row['can_view'])) { ?><span class="hint-blocked">입금확인 후 이용 가능</span><?php } ?>
      </div>
      <div class="board-td td-extend">
        <button type="button" class="btn-extend" onclick="event.preventDefault();event.stopPropagation();openExtendPopup('<?php echo $extend_url; ?>');">연장</button>
      </div>
    </a>
    <?php }
    } ?>

    <?php if (count($list) == 0) { ?>
    <div class="board-row empty-row">
      <div class="board-td" style="grid-column:1/-1;text-align:center;padding:50px 20px;">
        <p style="font-size:15px;color:#888;margin-bottom:8px;">등록된 진행중인 채용정보가 없습니다.</p>
        <p style="font-size:13px;color:#aaa;">채용공고를 등록하고 결제하시면 여기에 표시됩니다.</p>
      </div>
    </div>
    <?php } ?>
  </div>

</div>

<!-- 연장 팝업 (광고유료결제 섹션) -->
<div id="extendModal" class="jobs-extend-modal" style="display:none;">
  <div class="extend-modal-overlay" onclick="closeExtendModal()"></div>
  <div class="extend-modal-content">
    <div class="extend-modal-header">
      <h3>광고 연장</h3>
      <button type="button" class="extend-modal-close" onclick="closeExtendModal()" aria-label="닫기">×</button>
    </div>
    <div class="extend-modal-body">
      <iframe id="extendIframe" src="about:blank" frameborder="0" style="width:100%;min-height:500px;border:none;"></iframe>
    </div>
  </div>
</div>

<script>
function openExtendPopup(url) {
  var modal = document.getElementById('extendModal');
  var iframe = document.getElementById('extendIframe');
  if (modal && iframe) {
    iframe.src = url;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}
function closeExtendModal() {
  var modal = document.getElementById('extendModal');
  var iframe = document.getElementById('extendIframe');
  if (modal) modal.style.display = 'none';
  if (iframe) iframe.src = 'about:blank';
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeExtendModal();
});
</script>
