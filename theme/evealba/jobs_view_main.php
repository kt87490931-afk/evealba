<?php if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH . '/jobs_ai_content.lib.php');
include_once(G5_EXTEND_PATH . '/gemini_config.php');

function _jobs_view_msg($msg, $type = 'back') {
    $html = '<div class="jobs-view-msg" style="padding:24px;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin:16px 0;text-align:center;">';
    $html .= '<p style="margin:0 0 12px;font-size:15px;color:#333;">'.$msg.'</p>';
    if ($type === 'back') {
        $html .= '<a href="javascript:history.back()" style="display:inline-block;padding:10px 20px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;font-weight:700;">이전으로</a>';
    }
    $html .= '</div>';
    return $html;
}

$jr_id = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0;
if (!$jr_id) {
    echo _jobs_view_msg('잘못된 접근입니다.');
    echo '<script>alert("잘못된 접근입니다."); history.back();</script>';
    return;
}

$jr_table = 'g5_jobs_register';
$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!$tb_check || !sql_num_rows($tb_check)) {
    echo _jobs_view_msg('데이터를 찾을 수 없습니다.');
    echo '<script>alert("데이터를 찾을 수 없습니다."); history.back();</script>';
    return;
}

$is_owner = false;
$is_edit_mode = isset($_GET['mode']) && $_GET['mode'] === 'edit';
if ($is_member) {
    $mb_id_esc = addslashes($member['mb_id']);
    $row = sql_fetch("SELECT * FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
    if ($row) $is_owner = true;
}
if (!$row) {
    $row = sql_fetch("SELECT * FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
}
if (!$row) {
    echo _jobs_view_msg('데이터가 없습니다.');
    echo '<script>alert("데이터가 없습니다."); history.back();</script>';
    return;
}

// 추천(좋아요) 데이터
$_jr_good = isset($row['jr_good']) ? (int)$row['jr_good'] : 0;
$_jr_good_used = 0;
$_jr_good_limit = 10;
if ($is_member) {
    $col_chk = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_good'", false);
    if (!$col_chk || !@sql_num_rows($col_chk)) {
        @sql_query("ALTER TABLE g5_jobs_register ADD COLUMN jr_good INT UNSIGNED NOT NULL DEFAULT 0", false);
    }
    $tb_chk = @sql_query("SHOW TABLES LIKE 'g5_jobs_good'", false);
    if ($tb_chk && @sql_num_rows($tb_chk)) {
        $_gu = @sql_fetch("SELECT jg_count FROM g5_jobs_good WHERE jr_id='".(int)$jr_id."' AND mb_id='".addslashes($member['mb_id'])."' AND jg_date='".date('Y-m-d')."'");
        if ($_gu) $_jr_good_used = (int)$_gu['jg_count'];
    }
}

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_good_url = $jobs_base_url ? $jobs_base_url.'/jobs_good.php' : '/jobs_good.php';
$jobs_ongoing_url = $jobs_base_url ? $jobs_base_url.'/jobs_ongoing.php' : '/jobs_ongoing.php';
$jobs_ai_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_ai_section_save.php' : '/jobs_ai_section_save.php';
$jobs_basic_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_basic_info_save.php' : '/jobs_basic_info_save.php';
$jobs_bulk_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_editor_bulk_save.php' : '/jobs_editor_bulk_save.php';
$jobs_cards_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_editor_cards_save.php' : '/jobs_editor_cards_save.php';
$jobs_img_save_url = $jobs_base_url ? $jobs_base_url.'/jobs_image_save.php' : '/jobs_image_save.php';

$status = $row['jr_status'];
$payment_ok = !empty($row['jr_payment_confirmed']);
$status_label = ($status === 'ongoing') ? '진행중' : ($payment_ok ? '입금확인' : '입금대기중');
$status_class = ($status === 'ongoing') ? 'ongoing' : ($payment_ok ? 'payment-ok' : 'payment-wait');

// 입금대기중: 상세 열람 차단 (URL 직접 접근 포함)
if ($status === 'pending' && !$payment_ok) {
    echo '<div class="jobs-view-msg" style="padding:24px;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin:16px 0;text-align:center;">';
    echo '<p style="margin:0 0 12px;font-size:15px;color:#333;">입금확인 후 이용 가능합니다. 진행중인 채용정보에서 확인해 주세요.</p>';
    echo '<a href="'.htmlspecialchars($jobs_ongoing_url).'" style="display:inline-block;padding:10px 20px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;font-weight:700;">진행중인 채용정보로 이동</a>';
    echo '</div>';
    echo '<script>alert("입금확인 후 이용 가능합니다."); location.href="'.addslashes($jobs_ongoing_url).'";</script>';
    echo '<noscript><meta http-equiv="refresh" content="2;url='.htmlspecialchars($jobs_ongoing_url).'"></noscript>';
    return;
}

$data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
if (!is_array($data)) $data = array();

$_opt_end_date = $row['jr_end_date'] ?? '';
$_opt_remaining_days = 0;
if ($_opt_end_date) {
    $_opt_remaining_days = max(0, (int)((strtotime($_opt_end_date . ' 23:59:59') - time()) / 86400));
}
$_opt_daily_rates = array(
    'premium' => 1667,
    'badge'   => 1000,
    'motion'  => 1000,
    'wave'    => 1667,
    'border'  => 1000,
);
$_author_mb = sql_fetch("SELECT mb_nick, mb_1, mb_3, mb_4, mb_5 FROM g5_member WHERE mb_id = '".addslashes($row['mb_id'])."'");
$nick = $_author_mb['mb_nick'] ?: (isset($data['job_nickname']) ? trim($data['job_nickname']) : $row['jr_nickname']);
$_biz_company = isset($_author_mb['mb_3']) ? trim($_author_mb['mb_3']) : '';
$_biz_ceo     = isset($_author_mb['mb_4']) ? trim($_author_mb['mb_4']) : '';
$_biz_addr    = isset($_author_mb['mb_5']) ? trim($_author_mb['mb_5']) : '';
$_is_biz      = (isset($_author_mb['mb_1']) && $_author_mb['mb_1'] === 'biz');
$comp = isset($data['job_company']) ? trim($data['job_company']) : $row['jr_company'];
$title = isset($data['job_title']) ? trim($data['job_title']) : $row['jr_title'];
$contact = isset($data['job_contact']) ? trim($data['job_contact']) : '';
$employ_type = isset($data['employ-type']) ? trim($data['employ-type']) : '고용';
$salary_type = isset($data['job_salary_type']) ? trim($data['job_salary_type']) : '';
$salary_amt = isset($data['job_salary_amt']) ? trim($data['job_salary_amt']) : '';
$salary_disp = $salary_type ? (($salary_type === '급여협의') ? '급여협의' : $salary_type . ($salary_amt ? ' ' . number_format((int)preg_replace('/[^0-9]/','',$salary_amt)) . '원' : '')) : '';
$reg1_id = isset($data['job_work_region_1']) ? trim($data['job_work_region_1']) : '';
$reg1_detail_id = isset($data['job_work_region_detail_1']) ? trim($data['job_work_region_detail_1']) : '';
$reg2_id = isset($data['job_work_region_2']) ? trim($data['job_work_region_2']) : '';
$reg2_detail_id = isset($data['job_work_region_detail_2']) ? trim($data['job_work_region_detail_2']) : '';
$reg3_id = isset($data['job_work_region_3']) ? trim($data['job_work_region_3']) : '';
$reg3_detail_id = isset($data['job_work_region_detail_3']) ? trim($data['job_work_region_detail_3']) : '';

if (!isset($ev_regions) || !$ev_regions) {
    @include_once(G5_PATH.'/lib/ev_region_fallback.inc.php');
    if (isset($ev_regions_fallback)) $ev_regions = $ev_regions_fallback;
    if (isset($ev_region_details_fallback)) $ev_region_details = $ev_region_details_fallback;
}
if (!isset($ev_regions)) $ev_regions = array();
if (!isset($ev_region_details)) $ev_region_details = array();

$_reg_name_map = array();
foreach ($ev_regions as $_r) $_reg_name_map[$_r['er_id']] = $_r['er_name'];
$_regd_name_map = array();
foreach ($ev_region_details as $_rd) $_regd_name_map[$_rd['erd_id']] = $_rd['erd_name'];

$region = '';
if ($reg1_id) {
    $r1_name = isset($_reg_name_map[(int)$reg1_id]) ? $_reg_name_map[(int)$reg1_id] : $reg1_id;
    $d1_name = isset($_regd_name_map[(int)$reg1_detail_id]) ? $_regd_name_map[(int)$reg1_detail_id] : $reg1_detail_id;
    $region = $r1_name . ($d1_name ? ' ' . $d1_name : '');
}
$job1 = isset($data['job_job1']) ? trim($data['job_job1']) : '';
$job2 = isset($data['job_job2']) ? trim($data['job_job2']) : '';
$jobtype = ($job1 !== '' || $job2 !== '') ? trim(implode(' / ', array_filter(array($job1, $job2)))) : '';
$amenity = isset($data['amenity']) && is_array($data['amenity']) ? implode(', ', array_map('trim', $data['amenity'])) : (isset($data['amenity']) ? trim($data['amenity']) : '');
$keyword = isset($data['keyword']) && is_array($data['keyword']) ? implode(', ', array_map('trim', $data['keyword'])) : (isset($data['keyword']) ? trim($data['keyword']) : '');
$mbti = isset($data['mbti_prefer']) && is_array($data['mbti_prefer']) ? implode(', ', array_map('trim', $data['mbti_prefer'])) : '';
$sns_parts = array();
if (!empty($data['job_kakao'])) $sns_parts[] = '카카오: '.$data['job_kakao'];
if (!empty($data['job_line'])) $sns_parts[] = '라인: '.$data['job_line'];
if (!empty($data['job_telegram'])) $sns_parts[] = '텔레그램: '.$data['job_telegram'];
$sns_disp = implode(', ', $sns_parts);
$desc_location = isset($data['desc_location']) ? trim($data['desc_location']) : '';
$desc_env = isset($data['desc_env']) ? trim($data['desc_env']) : '';
$desc_benefit = isset($data['desc_benefit']) ? trim($data['desc_benefit']) : '';
$desc_qualify = isset($data['desc_qualify']) ? trim($data['desc_qualify']) : '';
$desc_extra = isset($data['desc_extra']) ? trim($data['desc_extra']) : '';

$_aic = aic_get_active($jr_id);
$_aic_src = $_aic ? 'aic' : 'jr_data';
$_ai = function($key, $fallback = '') use ($_aic, $data) {
    if ($_aic && !empty($_aic[$key])) return trim($_aic[$key]);
    if (!empty($data[$key])) return trim($data[$key]);
    return $fallback;
};

$ai_summary = $_ai('ai_content');
$ai_intro = $_ai('ai_intro');
$ai_location = $_ai('ai_location');
$ai_env = $_ai('ai_env');
$ai_benefit = $_ai('ai_benefit');
$ai_wrapup = $_ai('ai_wrapup');
$ai_card1_title = $_ai('ai_card1_title');
$ai_card1_desc = $_ai('ai_card1_desc');
$ai_card2_title = $_ai('ai_card2_title');
$ai_card2_desc = $_ai('ai_card2_desc');
$ai_card3_title = $_ai('ai_card3_title');
$ai_card3_desc = $_ai('ai_card3_desc');
$ai_card4_title = $_ai('ai_card4_title');
$ai_card4_desc = $_ai('ai_card4_desc');
$ai_welfare = $_ai('ai_welfare', $ai_benefit);
$ai_qualify = $_ai('ai_qualify');
$ai_extra = $_ai('ai_extra', $ai_wrapup);
$ai_mbti_comment_val = $_ai('ai_mbti_comment');
$ai_version = $_aic ? (int)$_aic['_version'] : 0;
$has_sections = !empty($ai_intro) || !empty($ai_location) || !empty($ai_env) || !empty($ai_welfare) || !empty($ai_extra) || !empty($ai_card1_desc);
$show_ai = ($status === 'ongoing' || $payment_ok) && ($ai_summary || $has_sections);
$can_edit = $is_owner && $is_edit_mode && ($status === 'ongoing' || $payment_ok);

// AI 큐 상태 (입금확인 후 AI 미완성 시 로딩/실패 UI용)
$ai_queue_status = '';
$ai_queue_error = '';
if (($status === 'ongoing' || $payment_ok) && !$ai_summary && !$has_sections) {
    $tbq = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
    if ($tbq && sql_num_rows($tbq)) {
        $q_row = sql_fetch("SELECT status, error_msg FROM g5_jobs_ai_queue WHERE jr_id = '".(int)$jr_id."' ORDER BY id DESC LIMIT 1", false);
        if ($q_row) {
            $ai_queue_status = $q_row['status'];
            $ai_queue_error = isset($q_row['error_msg']) ? trim($q_row['error_msg']) : '';
        }
    }
}
$title_employ = $title ? $title . ' · ' . $employ_type : $employ_type;
$amenity_arr = is_array($data['amenity'] ?? null) ? array_map('trim', $data['amenity']) : (trim($amenity ?? '') ? explode(',', $amenity) : array());
?>
<?php
$sns_kakao = !empty($data['job_kakao']) ? trim($data['job_kakao']) : '';
$sns_line = !empty($data['job_line']) ? trim($data['job_line']) : '';
$sns_telegram = !empty($data['job_telegram']) ? trim($data['job_telegram']) : '';
$banner_comp = $nick ?: $comp ?: '—';
$biz_title = isset($data['job_title']) && trim($data['job_title']) !== '' ? trim($data['job_title']) : ($row['jr_title'] ?: ($row['jr_subject_display'] ?? ''));

$saved_theme = isset($data['theme']) ? trim($data['theme']) : 'pink';
if (!in_array($saved_theme, array('pink', 'black', 'blue'))) $saved_theme = 'pink';

$thumb_gradient = isset($data['thumb_gradient']) ? trim($data['thumb_gradient']) : '';
$thumb_title = isset($data['thumb_title']) ? trim($data['thumb_title']) : '';
$thumb_text = isset($data['thumb_text']) ? trim($data['thumb_text']) : '';
$thumb_icon = isset($data['thumb_icon']) ? trim($data['thumb_icon']) : '';
$thumb_motion = isset($data['thumb_motion']) ? trim($data['thumb_motion']) : '';
$thumb_wave = isset($data['thumb_wave']) ? (int)$data['thumb_wave'] : 0;
$thumb_text_color = isset($data['thumb_text_color']) ? trim($data['thumb_text_color']) : 'rgb(255,255,255)';
$thumb_border = isset($data['thumb_border']) ? trim($data['thumb_border']) : '';
?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/jobs_view_editor.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/jobs_view_editor.css'); ?>">

<!-- ═══ 썸네일 생성 ═══ -->
<style>
/* 썸네일 섹션 래퍼 */
.thumb-gen-wrap{max-width:958px;margin:0 auto 12px;background:#fff;border:1.5px solid var(--border,#fce8f0);border-radius:16px;overflow:hidden;font-family:'Noto Sans KR',sans-serif}
.thumb-gen-wrap .tg-section-header{background:linear-gradient(90deg,var(--th-section-hd1,#fff0f6),var(--th-section-hd2,#fff8fb));padding:11px 20px;border-bottom:1.5px solid var(--border,#fce8f0);display:flex;align-items:center;justify-content:space-between}
.thumb-gen-wrap .tg-section-label{font-size:12px;font-weight:900;color:var(--pink,#FF1B6B);letter-spacing:.3px}
.thumb-gen-wrap .tg-save-btn{padding:5px 18px;border:none;border-radius:8px;background:linear-gradient(135deg,var(--orange,#FF6B35),var(--pink,#FF1B6B));color:#fff;font-size:12px;font-weight:900;cursor:pointer;transition:opacity .2s;box-shadow:0 3px 12px rgba(255,27,107,.3)}
.thumb-gen-wrap .tg-save-btn:hover{opacity:.9}
.thumb-gen-wrap .tg-save-btn:disabled{opacity:.5;cursor:not-allowed}
/* 그리드 레이아웃 */
.thumb-body{display:grid;grid-template-columns:1fr 300px;gap:0}
.thumb-controls{padding:20px 22px;border-right:1.5px solid var(--border,#fce8f0)}
.thumb-preview-col{padding:20px 18px;background:linear-gradient(180deg,var(--th-section-hd1,#fff0f6),var(--th-section-hd2,#fff8fb));display:flex;flex-direction:column;align-items:center;gap:12px}
.thumb-preview-label{font-size:11px;font-weight:900;color:var(--pink,#FF1B6B);letter-spacing:.3px;align-self:flex-start}
/* 컨트롤 행 */
.ctrl-row{margin-bottom:16px}
.ctrl-label{font-size:11px;font-weight:900;color:#666;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.ctrl-input{width:100%;padding:9px 12px;border:1.5px solid #f0e0e8;border-radius:10px;font-size:13px;font-family:inherit;outline:none;transition:border-color .2s;color:#222;resize:vertical}
.ctrl-input:focus{border-color:var(--pink,#FF1B6B)}
.ctrl-charcount{font-size:10px;color:#bbb;text-align:right;margin-top:3px}
/* 무료 컬러 그리드 */
.color-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:6px;margin-bottom:16px}
.color-swatch{width:100%;aspect-ratio:1;border-radius:8px;cursor:pointer;border:2.5px solid transparent;transition:all .18s;position:relative;overflow:hidden}
.color-swatch:hover{transform:scale(1.12);box-shadow:0 3px 10px rgba(0,0,0,.2)}
.color-swatch.selected{border-color:#222;box-shadow:0 0 0 2px #fff,0 0 0 4px #222;transform:scale(1.1)}
.color-swatch-num{position:absolute;bottom:1px;right:2px;font-size:8px;font-weight:700;color:rgba(255,255,255,.8);line-height:1;text-shadow:0 1px 2px rgba(0,0,0,.5)}
/* 유료 컬러 */
.premium-color-wrap{margin-bottom:16px}
.premium-title{font-size:11px;font-weight:900;color:#666;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.premium-color-wrap .color-grid{margin-bottom:0}
/* 카본 파이버 패턴 (P3) */
.carbon-bg{background:linear-gradient(160deg,rgba(45,45,55,.45) 0%,transparent 40%,rgba(55,55,65,.3) 100%),url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Crect width='8' height='8' fill='%230d0d12'/%3E%3Crect width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='2' width='2' height='2' fill='%2318181f'/%3E%3Crect x='4' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='4' width='2' height='2' fill='%2318181f'/%3E%3Crect x='6' y='6' width='2' height='2' fill='%2318181f'/%3E%3Crect y='6' width='2' height='2' fill='%2318181f'/%3E%3C/svg%3E") repeat!important;background-size:100% 100%,8px 8px!important}
/* 텍스트 컬러 */
.txt-color-opts{display:flex;gap:8px}
.txt-color-btn{display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;border:2px solid #eee;font-size:12px;font-weight:700;cursor:pointer;background:#f5f5f5;font-family:inherit;transition:all .18s;color:#555}
.txt-color-btn.selected{border-color:var(--pink,#FF1B6B);background:#fff0f6;color:var(--pink,#FF1B6B)}
/* 뱃지 선택 */
.badge-opts{display:flex;flex-wrap:wrap;gap:6px}
.badge-opt{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid #eee;background:#f9f9f9;color:#666;transition:all .18s}
.badge-opt.selected{background:var(--pink,#FF1B6B);color:#fff;border-color:var(--pink,#FF1B6B)}
.badge-opt-none{border-style:dashed}
.badge-opt-none.selected{background:#fff;color:var(--pink,#FF1B6B)}
/* 모션 선택 */
.motion-opts{display:flex;flex-wrap:wrap;gap:6px}
.motion-btn{padding:5px 13px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid #eee;background:#f9f9f9;color:#666;font-family:inherit;transition:all .18s}
.motion-btn.selected{background:var(--pink,#FF1B6B);color:#fff;border-color:var(--pink,#FF1B6B)}
/* 웨이브 토글 */
.wave-toggle{display:flex;align-items:center;gap:8px;cursor:pointer}
.wave-toggle input{accent-color:var(--pink,#FF1B6B);width:15px;height:15px}
.wave-toggle-label{font-size:12px;color:#555}
/* 테두리 옵션 */
.border-opts{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.border-btn{width:36px;height:36px;border-radius:8px;cursor:pointer;border:2px solid #eee;transition:all .18s;position:relative;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#999}
.border-btn:hover{transform:scale(1.1)}
.border-btn.selected{box-shadow:0 0 0 2px #fff,0 0 0 4px var(--pink,#FF1B6B);transform:scale(1.1)}
.border-btn-none{border:2px dashed #ddd;font-size:10px;color:#bbb}
/* 미리보기 카드 border-radius 유지 */
#tg-pv-card.job-card{cursor:default;transition:box-shadow .2s,outline .2s;border-radius:12px!important;overflow:hidden}
#tg-pv-card.job-card:hover{transform:none}
#tg-pv-card .job-card-banner{height:auto;aspect-ratio:16/9;padding:16px}
#tg-pv-card .job-card-banner span{position:relative;z-index:1;line-height:1.4;transition:font-size .15s}
#tg-pv-card .tpc-sub{display:block;font-size:12px;font-weight:500;margin-top:2px;opacity:.9;transition:font-size .15s}
.pv-icon-badge{position:absolute;top:7px;right:7px;font-size:10px;font-weight:900;padding:2px 7px;border-radius:9px;z-index:10;color:#fff}
/* 기간 선택 & 총금액 (기존 유지) */
.tg-option-price{margin-top:6px;padding:6px 10px;background:#fff8fb;border:1px solid #f0e0e8;border-radius:8px;font-size:12px}
.tg-total-wrap{margin-top:14px;background:linear-gradient(135deg,var(--dark2,#2D0020),#5C0040);border-radius:12px;padding:14px 16px;color:#fff}
.tg-total-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.tg-total-header .tth-label{font-size:13px;font-weight:700}
.tg-total-header .tth-amount{font-size:18px;font-weight:900;color:var(--gold,#FFD700)}
.tg-total-items{border-top:1px solid rgba(255,255,255,.15);padding-top:8px}
.tg-total-items .tti-row{display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:3px 0;color:rgba(255,255,255,.85)}
.tg-total-items .tti-row .tti-name{font-weight:500}
.tg-total-items .tti-row .tti-price{font-weight:700;color:var(--gold,#FFD700)}
.tg-total-items .tti-empty{font-size:11px;color:rgba(255,255,255,.5);padding:4px 0}
/* 모션 키프레임 */
@keyframes motion-pulse-scale{0%,100%{transform:scale(1)}50%{transform:scale(1.25)}}
@keyframes motion-soft-blink{0%,100%{opacity:1}50%{opacity:.3}}
@keyframes motion-glow-pulse{0%,100%{text-shadow:none}50%{text-shadow:0 0 10px #fff,0 0 25px #fff,0 0 50px rgba(255,255,255,.7),0 0 80px rgba(255,255,255,.4)}}
@keyframes motion-bounce{0%,100%{transform:translateY(0)}25%{transform:translateY(-10px)}50%{transform:translateY(0)}65%{transform:translateY(-5px)}80%{transform:translateY(0)}90%{transform:translateY(-2px)}}
@keyframes wave-diag{0%{background-position:0% 0%}50%{background-position:100% 100%}100%{background-position:0% 0%}}
.pv-motion-shimmer{animation:motion-pulse-scale 1.4s ease-in-out infinite!important;display:inline-block!important}
.pv-motion-soft-blink{animation:motion-soft-blink 1.8s ease-in-out infinite!important}
.pv-motion-glow{animation:motion-glow-pulse 2s ease-in-out infinite!important}
.pv-motion-bounce{animation:motion-bounce 1.2s ease infinite!important}
.pv-wave-active{animation:wave-diag 4s ease-in-out infinite!important;background-size:400% 400%!important}
/* 반응형 */
@media(max-width:768px){
  .thumb-body{grid-template-columns:1fr}
  .thumb-controls{border-right:none;border-bottom:1.5px solid var(--border,#fce8f0)}
  .thumb-preview-col{order:-1}
  .color-grid{grid-template-columns:repeat(5,1fr)}
}
</style>

<?php if ($is_owner && $is_edit_mode && $row['jr_status'] === 'ongoing') {
    $jmp_remain = isset($row['jr_jump_remain']) ? (int)$row['jr_jump_remain'] : 0;
    $jmp_used   = isset($row['jr_jump_used']) ? (int)$row['jr_jump_used'] : 0;
    $jmp_total  = isset($row['jr_jump_total']) ? (int)$row['jr_jump_total'] : 0;
    $jmp_auto   = isset($row['jr_auto_jump']) ? (int)$row['jr_auto_jump'] : 0;
    $jmp_next   = isset($row['jr_auto_jump_next']) ? $row['jr_auto_jump_next'] : '';
    $jmp_last   = isset($row['jr_jump_datetime']) ? $row['jr_jump_datetime'] : '';
    $jmp_end    = $row['jr_end_date'] ?: '';
    $jmp_days_left = $jmp_end ? max(0, floor((strtotime($jmp_end) - strtotime(date('Y-m-d'))) / 86400)) : 0;
    $jmp_interval = ($jmp_remain > 0 && $jmp_days_left > 0)
        ? max(10, floor(($jmp_days_left * 24 * 60) / $jmp_remain)) : 0;
?>
<div class="jump-control-bar" id="jump-control-bar">
  <div class="jcb-header">
    <span class="jcb-label">🔝 점프 관리</span>
    <span class="jcb-remain">잔여 <strong id="jcb-remain-num"><?php echo number_format($jmp_remain); ?></strong>회 / <?php echo number_format($jmp_total); ?>회</span>
  </div>
  <div class="jcb-body">
    <div class="jcb-info">
      <div class="jcb-stat"><span>사용</span><strong><?php echo number_format($jmp_used); ?>회</strong></div>
      <div class="jcb-stat"><span>남은기간</span><strong><?php echo $jmp_days_left; ?>일</strong></div>
      <?php if ($jmp_auto && $jmp_interval) { ?>
      <div class="jcb-stat"><span>자동간격</span><strong>약 <?php echo $jmp_interval; ?>분</strong></div>
      <?php } ?>
      <?php if ($jmp_last) { ?>
      <div class="jcb-stat"><span>마지막 점프</span><strong><?php echo substr($jmp_last, 5, 11); ?></strong></div>
      <?php } ?>
    </div>
    <div class="jcb-actions">
      <button type="button" class="jcb-btn jcb-btn-manual" id="btn-manual-jump" onclick="doManualJump(<?php echo $jr_id; ?>)">⚡ 수동 점프</button>
      <label class="jcb-toggle">
        <input type="checkbox" id="chk-auto-jump" <?php echo $jmp_auto ? 'checked' : ''; ?> onchange="toggleAutoJump(<?php echo $jr_id; ?>, this.checked)">
        <span class="jcb-toggle-slider"></span>
        <span class="jcb-toggle-text">자동 점프</span>
      </label>
      <?php if ($jmp_auto && $jmp_next) { ?>
      <span class="jcb-next" id="jcb-next-time">다음: <?php echo substr($jmp_next, 5, 11); ?></span>
      <?php } ?>
    </div>
  </div>
  <div class="jcb-desc">
    <strong>자동 점프란?</strong>
    남은 기간과 잔여 점프 횟수를 기반으로 균등한 간격으로 자동 점프됩니다.
    <?php if ($jmp_remain > 0 && $jmp_days_left > 0) { ?>
    예) 남은 <?php echo $jmp_days_left; ?>일, 잔여 <?php echo number_format($jmp_remain); ?>회 → 약 <?php echo $jmp_interval; ?>분마다 자동 점프
    <?php } ?>
    수동 점프를 사용하면 간격이 자동으로 재계산됩니다.
  </div>
  <?php if ($jmp_remain <= 0) { ?>
  <div class="jcb-empty">잔여 점프 횟수가 없습니다. <a href="<?php echo rtrim(G5_URL,'/'); ?>/jobs_jump_shop.php">점프 추가 구매</a></div>
  <?php } ?>
</div>
<style>
.jump-control-bar{max-width:958px;margin:0 auto 12px;background:#fff;border:2px solid #FF1B6B;border-radius:14px;overflow:hidden;font-family:'Noto Sans KR',sans-serif}
.jcb-header{background:linear-gradient(90deg,#FF1B6B,#FF6B35);padding:10px 20px;display:flex;align-items:center;justify-content:space-between}
.jcb-label{font-size:13px;font-weight:900;color:#fff}
.jcb-remain{font-size:12px;color:rgba(255,255,255,.9)}
.jcb-remain strong{color:#fff;font-size:16px;margin:0 2px}
.jcb-body{padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.jcb-info{display:flex;gap:18px;flex-wrap:wrap}
.jcb-stat{display:flex;flex-direction:column;align-items:center;gap:2px}
.jcb-stat span{font-size:10px;color:#999}
.jcb-stat strong{font-size:13px;color:#333;font-weight:700}
.jcb-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.jcb-btn-manual{padding:8px 20px;border:none;border-radius:10px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;font-size:13px;font-weight:900;cursor:pointer;transition:all .2s;box-shadow:0 3px 12px rgba(255,27,107,.3)}
.jcb-btn-manual:hover{transform:translateY(-1px);box-shadow:0 5px 18px rgba(255,27,107,.4)}
.jcb-btn-manual:disabled{opacity:.5;cursor:not-allowed;transform:none}
.jcb-toggle{display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none}
.jcb-toggle input{display:none}
.jcb-toggle-slider{width:40px;height:22px;background:#ddd;border-radius:11px;position:relative;transition:background .2s}
.jcb-toggle-slider::after{content:'';position:absolute;top:2px;left:2px;width:18px;height:18px;border-radius:50%;background:#fff;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.jcb-toggle input:checked+.jcb-toggle-slider{background:#FF1B6B}
.jcb-toggle input:checked+.jcb-toggle-slider::after{transform:translateX(18px)}
.jcb-toggle-text{font-size:12px;font-weight:700;color:#555}
.jcb-next{font-size:11px;color:#888;background:#f5f5f5;padding:4px 10px;border-radius:6px}
.jcb-desc{padding:10px 20px;background:#f9f5ff;border-top:1px solid #f0e0f8;font-size:11px;color:#666;line-height:1.7}
.jcb-desc strong{color:#6B21A8;display:block;margin-bottom:2px;font-size:12px}
.jcb-empty{padding:10px 20px;background:#fff5f8;text-align:center;font-size:12px;color:#FF1B6B;border-top:1px solid #fce8f0}
.jcb-empty a{color:#FF1B6B;font-weight:700;text-decoration:underline}
@media(max-width:600px){
  .jcb-body{flex-direction:column;align-items:stretch}
  .jcb-info{justify-content:space-around}
  .jcb-actions{justify-content:center}
}
</style>
<script>
function doManualJump(jrId){
  var btn=document.getElementById('btn-manual-jump');
  if(btn.disabled)return;
  btn.disabled=true;btn.textContent='⏳ 점프중...';
  fetch('<?php echo rtrim(G5_URL,"/"); ?>/jobs_jump.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'jr_id='+jrId,
    credentials:'same-origin'
  }).then(function(r){return r.json();}).then(function(res){
    btn.disabled=false;btn.textContent='⚡ 수동 점프';
    if(res.ok){
      alert(res.msg+' (잔여: '+res.remain+'회)');
      document.getElementById('jcb-remain-num').textContent=res.remain.toLocaleString();
      if(res.remain<=0){btn.disabled=true;}
    }else{alert(res.msg);}
  }).catch(function(){btn.disabled=false;btn.textContent='⚡ 수동 점프';alert('점프 처리 중 오류가 발생했습니다.');});
}
function toggleAutoJump(jrId,on){
  fetch('<?php echo rtrim(G5_URL,"/"); ?>/jobs_jump_auto_toggle.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'jr_id='+jrId+'&auto_jump='+(on?1:0),
    credentials:'same-origin'
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok){
      alert(res.msg);
      var nextEl=document.getElementById('jcb-next-time');
      if(res.auto_next&&on){
        if(!nextEl){
          var acts=document.querySelector('.jcb-actions');
          var sp=document.createElement('span');sp.className='jcb-next';sp.id='jcb-next-time';
          sp.textContent='다음: '+res.auto_next.substring(5,16);
          acts.appendChild(sp);
        }else{nextEl.textContent='다음: '+res.auto_next.substring(5,16);nextEl.style.display='';}
      }else if(nextEl){nextEl.style.display='none';}
    }else{
      alert(res.msg);
      document.getElementById('chk-auto-jump').checked=!on;
    }
  }).catch(function(){alert('자동 점프 설정 중 오류가 발생했습니다.');document.getElementById('chk-auto-jump').checked=!on;});
}
</script>
<?php } ?>

<?php if ($is_owner && $is_edit_mode) { ?>
<div class="thumb-gen-wrap" id="thumb-gen-section">
  <div class="tg-section-header">
    <span class="tg-section-label">🎨 썸네일 생성</span>
    <button type="button" class="tg-save-btn" id="tg-save-btn" onclick="saveThumb()">💾 저장</button>
  </div>
  <div class="thumb-body">
    <div class="thumb-controls">
      <?php
      $gradients = array(
        1  => 'linear-gradient(135deg,rgb(255,65,108),rgb(255,75,43))',
        2  => 'linear-gradient(135deg,rgb(255,94,98),rgb(255,195,113))',
        3  => 'linear-gradient(135deg,rgb(238,9,121),rgb(255,106,0))',
        4  => 'linear-gradient(135deg,rgb(74,0,224),rgb(142,45,226))',
        5  => 'linear-gradient(135deg,rgb(67,233,123),rgb(56,249,215))',
        6  => 'linear-gradient(135deg,rgb(29,209,161),rgb(9,132,227))',
        7  => 'linear-gradient(135deg,rgb(196,113,237),rgb(246,79,89))',
        8  => 'linear-gradient(135deg,rgb(36,198,220),rgb(81,74,157))',
        9  => 'linear-gradient(135deg,rgb(0,210,255),rgb(58,123,213))',
        10 => 'linear-gradient(135deg,rgb(236,64,122),rgb(240,98,146))',
        11 => 'linear-gradient(135deg,rgb(118,75,162),rgb(102,126,234))',
        12 => 'linear-gradient(135deg,rgb(72,85,99),rgb(41,50,60))',
        13 => 'linear-gradient(135deg,rgb(30,60,114),rgb(42,82,152))',
        14 => 'linear-gradient(135deg,rgb(255,243,176),rgb(170,218,255))',
        15 => 'linear-gradient(135deg,rgb(249,83,198),rgb(255,107,157))',
        16 => 'linear-gradient(135deg,rgb(255,0,110),rgb(131,56,236))',
        17 => 'linear-gradient(135deg,rgb(67,206,162),rgb(24,90,157))',
        18 => 'linear-gradient(135deg,rgb(19,78,94),rgb(113,178,128))',
        19 => 'linear-gradient(135deg,rgb(255,153,102),rgb(255,94,98))',
        20 => 'linear-gradient(135deg,rgb(86,171,47),rgb(168,224,99))',
      );
      $saved_grad = $thumb_gradient ?: '1';
      $icons = array(
        '' => array('label' => '없음', 'bg' => '#ccc'),
        'beginner' => array('label' => '💖 초보환영', 'bg' => '#FF1B6B'),
        'room' => array('label' => '🏡 원룸제공', 'bg' => '#FF6B35'),
        'luxury' => array('label' => '💎 고급시설', 'bg' => '#8B00FF'),
        'black' => array('label' => '📋 블랙 관리', 'bg' => '#333'),
        'phone' => array('label' => '📱 폰비지급', 'bg' => '#0077B6'),
        'size' => array('label' => '👗 사이즈X', 'bg' => '#E91E63'),
        'set' => array('label' => '🎀 세트환영', 'bg' => '#FF9800'),
        'pickup' => array('label' => '🚗 픽업가능', 'bg' => '#4CAF50'),
        'member' => array('label' => '🙋 1회원제운영', 'bg' => '#7B1FA2'),
        'kkongbi' => array('label' => '💰 꽁비지급', 'bg' => '#00897B'),
      );
      ?>
      <!-- 무료 컬러 20종 -->
      <div class="ctrl-row">
        <div class="ctrl-label">🎨 컬러 선택 <span style="color:#bbb;font-weight:400;">(무료 20종)</span></div>
        <div class="color-grid" id="tg-color-grid">
          <?php foreach ($gradients as $num => $grad) {
            $sel = ((string)$num === (string)$saved_grad) ? ' selected' : '';
            echo '<div class="color-swatch'.$sel.'" data-grad="'.$num.'" style="background:'.$grad.'" onclick="selectGrad(this)" title="컬러 '.$num.'"><span class="color-swatch-num">'.$num.'</span></div>';
          } ?>
        </div>
      </div>

      <!-- 유료 컬러 4종 -->
      <div class="ctrl-row">
        <div class="premium-color-wrap">
          <div class="premium-title">유료 컬러 <span style="color:#aaa;font-weight:400;">(4종)</span></div>
          <div class="color-grid" id="tg-premium-grid">
            <?php
            $premium_colors = array(
              array('num'=>'P1','name'=>'메탈릭골드','bg'=>'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)'),
              array('num'=>'P2','name'=>'메탈릭실버','bg'=>'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)'),
              array('num'=>'P3','name'=>'카본','bg'=>'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)'),
              array('num'=>'P4','name'=>'오로라','bg'=>'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)'),
            );
            foreach ($premium_colors as $pc) {
              $psel = ($thumb_grad === $pc['num']) ? ' selected' : '';
              $extra_cls = ($pc['num'] === 'P3') ? ' carbon-bg' : '';
              echo '<div class="color-swatch'.$psel.$extra_cls.'" data-grad="'.$pc['num'].'" style="background:'.$pc['bg'].'" onclick="selectGrad(this)" title="'.$pc['name'].' (유료)"><span class="color-swatch-num">'.$pc['num'].'</span></div>';
            }
            ?>
          </div>
          <div class="tg-option-price" id="tg-premium-price" style="display:none"></div>
        </div>
      </div>

      <!-- 썸네일 제목 -->
      <div class="ctrl-row">
        <div class="ctrl-label">✏️ 썸네일 제목</div>
        <input type="text" class="ctrl-input" id="tg-title" maxlength="20" placeholder="업소명을 입력하세요" value="<?php echo htmlspecialchars($thumb_title ?: $nick ?: $comp ?: '', ENT_QUOTES); ?>" oninput="updatePreview();countChar(this,'tg-title-cnt',20)">
        <div class="ctrl-charcount"><span id="tg-title-cnt"><?php echo mb_strlen($thumb_title ?: $nick ?: $comp ?: '', 'UTF-8'); ?></span>/20</div>
      </div>

      <!-- 홍보 문구 -->
      <div class="ctrl-row">
        <div class="ctrl-label">💬 홍보 문구</div>
        <input type="text" class="ctrl-input" id="tg-text" maxlength="60" placeholder="예) 시급 15,000원 · 초보환영 · 당일지급" value="<?php echo htmlspecialchars($thumb_text, ENT_QUOTES); ?>" oninput="updatePreview();countChar(this,'tg-text-cnt',60)">
        <div class="ctrl-charcount"><span id="tg-text-cnt"><?php echo mb_strlen($thumb_text, 'UTF-8'); ?></span>/60</div>
      </div>

      <!-- 텍스트 컬러 -->
      <div class="ctrl-row">
        <div class="ctrl-label">🖊️ 텍스트 컬러</div>
        <div class="txt-color-opts" id="tg-textcolor-grid">
          <button type="button" class="txt-color-btn<?php echo $thumb_text_color === 'rgb(255,255,255)' ? ' selected' : ''; ?>" data-tcolor="rgb(255,255,255)" onclick="selectTextColor(this)"><span style="width:14px;height:14px;border-radius:50%;background:#fff;border:1.5px solid #ddd;display:inline-block"></span> 흰색</button>
          <button type="button" class="txt-color-btn<?php echo $thumb_text_color === 'rgb(68,68,68)' ? ' selected' : ''; ?>" data-tcolor="rgb(68,68,68)" onclick="selectTextColor(this)"><span style="width:14px;height:14px;border-radius:50%;background:#333;display:inline-block"></span> 다크그레이</button>
        </div>
      </div>

      <!-- 뱃지 -->
      <div class="ctrl-row">
        <div class="ctrl-label">🏷️ 뱃지</div>
        <div class="badge-opts" id="tg-icon-grid">
          <?php foreach ($icons as $key => $ic) {
            $sel = ($thumb_icon === $key) ? ' selected' : '';
            if ($key === '') {
              echo '<button type="button" class="badge-opt badge-opt-none'.$sel.'" data-icon="" data-icon-bg="" data-icon-label="" onclick="selectIcon(this)">없음</button>';
            } else {
              echo '<button type="button" class="badge-opt'.$sel.'" data-icon="'.$key.'" data-icon-bg="'.$ic['bg'].'" data-icon-label="'.htmlspecialchars($ic['label'], ENT_QUOTES).'" onclick="selectIcon(this)" style="background:'.($sel?'':'#fff0f6').';color:'.($sel?'':'#FF1B6B').';border-color:'.($sel?'':'#ffd6e7').'">'.$ic['label'].'</button>';
            }
          } ?>
        </div>
        <div class="tg-option-price" id="tg-badge-price" style="<?php echo $thumb_icon ? '' : 'display:none'; ?>"></div>
      </div>

      <!-- 제목 모션 -->
      <div class="ctrl-row">
        <div class="ctrl-label">✨ 제목 모션</div>
        <div class="motion-opts" id="tg-motion-grid">
          <?php
          $motions = array(
            '' => '없음',
            'shimmer' => '🌸 글씨 확대',
            'soft-blink' => '💫 소프트 블링크',
            'glow' => '💡 글로우 글씨',
            'bounce' => '🔔 바운스',
          );
          foreach ($motions as $key => $label) {
            $sel = ($thumb_motion === $key) ? ' selected' : '';
            echo '<button type="button" class="motion-btn'.$sel.'" data-motion="'.$key.'" onclick="selectMotion(this)">'.$label.'</button>';
          }
          ?>
        </div>
        <div class="tg-option-price" id="tg-motion-price" style="<?php echo $thumb_motion ? '' : 'display:none'; ?>"></div>
      </div>

      <!-- 컬러 웨이브 -->
      <div class="ctrl-row">
        <div class="ctrl-label">🌊 컬러 웨이브</div>
        <label class="wave-toggle">
          <input type="checkbox" id="tg-wave-chk" <?php echo $thumb_wave ? 'checked' : ''; ?> onchange="toggleWave(this.checked)">
          <span class="wave-toggle-label">배경 웨이브 효과 적용</span>
        </label>
        <div class="tg-option-price" id="tg-wave-price" style="<?php echo $thumb_wave ? '' : 'display:none'; ?>"></div>
      </div>

      <!-- 테두리 -->
      <div class="ctrl-row" style="margin-bottom:0">
        <div class="ctrl-label">🖼️ 테두리</div>
        <div class="border-opts" id="tg-border-grid">
          <button type="button" class="border-btn border-btn-none<?php echo !$thumb_border ? ' selected' : ''; ?>" title="없음" data-border="" onclick="selectBorder(this)">없음</button>
          <button type="button" class="border-btn<?php echo $thumb_border==='gold' ? ' selected' : ''; ?>" title="골드" data-border="gold" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#FFD700,#FFA500);border:none;box-shadow:inset 0 0 0 2px rgba(255,255,255,.3)"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='pink' ? ' selected' : ''; ?>" title="핫핑크" data-border="pink" onclick="selectBorder(this)" style="background:#FF1B6B;border:none"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='charcoal' ? ' selected' : ''; ?>" title="차콜" data-border="charcoal" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#2c2c2c,#4a4a4a);border:none"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='royalblue' ? ' selected' : ''; ?>" title="로얄블루" data-border="royalblue" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#1a3a8a,#4169E1);border:none"></button>
          <button type="button" class="border-btn<?php echo $thumb_border==='royalpurple' ? ' selected' : ''; ?>" title="로얄퍼플" data-border="royalpurple" onclick="selectBorder(this)" style="background:linear-gradient(135deg,#4B0082,#7B2FBE);border:none"></button>
        </div>
        <div class="tg-option-price" id="tg-border-price" style="<?php echo $thumb_border ? '' : 'display:none'; ?>"></div>
      </div>
    </div>

    <!-- 오른쪽: 미리보기 -->
    <div class="thumb-preview-col">
      <div class="thumb-preview-label">👁️ 미리보기</div>
      <?php
      $r1_name_disp = isset($_reg_name_map[(int)$reg1_id]) ? $_reg_name_map[(int)$reg1_id] : ($reg1_id ?: '지역');
      $d1_name_disp = isset($_regd_name_map[(int)$reg1_detail_id]) ? $_regd_name_map[(int)$reg1_detail_id] : ($reg1_detail_id ?: '상세지역');
      $pv_title_line1 = $thumb_title ?: $nick ?: $comp ?: '업소명';
      $pv_title_line2 = $thumb_text ?: '';
      $pv_desc = $biz_title ?: '광고제목';
      $all_grads_php = $gradients;
      $all_grads_php['P1'] = 'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)';
      $all_grads_php['P2'] = 'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)';
      $all_grads_php['P3'] = 'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)';
      $all_grads_php['P4'] = 'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)';
      $pv_grad = isset($all_grads_php[$saved_grad]) ? $all_grads_php[$saved_grad] : (isset($gradients[(int)($saved_grad ?: 1)]) ? $gradients[(int)($saved_grad ?: 1)] : $gradients[1]);
      $pv_banner_style = '';
      if ($thumb_wave) {
          preg_match_all('/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/', $pv_grad, $pv_m);
          if (!empty($pv_m[0]) && count($pv_m[0]) >= 2) {
              $c1 = $pv_m[0][0]; $c2 = $pv_m[0][1]; $c3 = isset($pv_m[0][2]) ? $pv_m[0][2] : $c1;
              $pv_banner_style = 'background:linear-gradient(135deg,'.$c1.','.$c2.','.$c3.','.$c1.','.$c2.');background-size:400% 400%';
          } else {
              $pv_banner_style = 'background:'.$pv_grad.';background-size:400% 400%';
          }
      } else {
          $pv_banner_style = 'background:'.$pv_grad;
      }
      ?>
      <div class="job-card" id="tg-pv-card" style="width:100%">
        <div class="job-card-banner<?php echo $thumb_wave ? ' pv-wave-active' : ''; ?><?php echo ($saved_grad === 'P3' && !$thumb_wave) ? ' carbon-bg' : ''; ?>" id="tg-pv-banner" style="<?php echo $pv_banner_style; ?>">
          <span id="tpc-title" class="<?php echo $thumb_motion ? 'pv-motion-'.htmlspecialchars($thumb_motion) : ''; ?>" style="color:<?php echo htmlspecialchars($thumb_text_color); ?>"><?php echo htmlspecialchars($pv_title_line1, ENT_QUOTES); ?><span class="tpc-sub" id="tpc-text"><?php echo htmlspecialchars($pv_title_line2, ENT_QUOTES); ?></span></span>
        </div>
        <?php if ($thumb_icon && isset($icons[$thumb_icon])) { ?>
        <div class="pv-icon-badge" id="tg-pv-icon" style="background:<?php echo $icons[$thumb_icon]['bg']; ?>"><?php echo $icons[$thumb_icon]['label']; ?></div>
        <?php } else { ?>
        <div class="pv-icon-badge" id="tg-pv-icon" style="display:none"></div>
        <?php } ?>
        <div class="job-card-body">
          <div class="job-card-location" id="tg-pv-loc">
            <span class="job-loc-badge" id="pv-loc-r1"><?php echo htmlspecialchars($r1_name_disp); ?></span>
            <span id="pv-loc-detail"><?php echo htmlspecialchars($d1_name_disp . ' ' . ($job1 ?: '업종')); ?></span>
          </div>
          <div class="job-desc" id="tg-pv-desc"><?php echo htmlspecialchars($pv_desc); ?></div>
          <div class="job-card-footer">
            <span class="job-wage" id="tg-pv-wage"><?php echo htmlspecialchars($salary_disp ?: '급여조건'); ?></span>
          </div>
        </div>
      </div>
      <div style="font-size:10px;color:#aaa;text-align:center;line-height:1.6;margin-top:4px">
        💡 이 썸네일은 메인, 채용정보,<br>지역별채용 페이지에 표시됩니다.
      </div>
      <!-- 광고 잔여 기간 안내 -->
      <div style="width:100%;background:#2a1525;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:8px 12px;margin-bottom:8px;font-size:11px;color:#ddd;line-height:1.6">
        📆 광고 종료일: <b style="color:#FFD700"><?php echo $_opt_end_date ?: '미정'; ?></b>
        &nbsp;|&nbsp; 잔여: <b style="color:#FF1B6B"><?php echo $_opt_remaining_days; ?>일</b>
        <div style="margin-top:2px;font-size:10px;color:#999">옵션 비용 = 일일 단가 × 잔여일수 (광고 기간 내 적용)</div>
      </div>
      <!-- 총 신청 금액 -->
      <div class="tg-total-wrap" id="tg-total-wrap" style="width:100%">
        <div class="tg-total-header">
          <span class="tth-label">🛒 총 옵션 비용</span>
          <span class="tth-amount" id="tg-total-amount">0 원</span>
        </div>
        <div class="tg-total-items" id="tg-total-items">
          <div class="tti-empty">선택된 유료 옵션이 없습니다</div>
        </div>
      </div>
      <button type="button" class="tg-save-btn" onclick="saveThumb()" style="width:100%;padding:11px;border-radius:12px;font-size:13px">💾 저장</button>
    </div>
  </div>
</div>
<?php } ?>

<?php if ($is_owner && $is_edit_mode) { ?>
<!-- 테마 스위처 (소유자 전용) -->
<div id="theme-switcher">
  <div class="ts-inner">
    <span class="ts-label">🎨 테마</span>
    <button type="button" class="ts-btn ts-pink<?php echo $saved_theme==='pink'?' active':''; ?>" data-theme="pink"><span class="ts-dot"></span> 핑크</button>
    <button type="button" class="ts-btn ts-black<?php echo $saved_theme==='black'?' active':''; ?>" data-theme="black"><span class="ts-dot"></span> 블랙</button>
    <button type="button" class="ts-btn ts-blue<?php echo $saved_theme==='blue'?' active':''; ?>" data-theme="blue"><span class="ts-dot"></span> 블루</button>
    <button type="button" class="ts-btn-save" id="btn-save-theme" onclick="saveThemeChoice()" style="margin-left:8px;padding:5px 14px;border:none;border-radius:8px;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-size:11px;font-weight:900;cursor:pointer;">💾 테마저장</button>
  </div>
</div>
<?php } ?>

<article id="bo_v" class="ev-view-wrap jobs-view-wrap jobs-view-editor-wrap<?php echo $saved_theme !== 'pink' ? ' theme-'.$saved_theme : ''; ?>">
  <?php
  /* ═══ AI 생성 필드 매핑 (jr_data) ═══
   * ai_intro         : 인사말
   * ai_card1_title~4 : 포인트카드 제목 (1~4)
   * ai_card1_desc~4  : 포인트카드 설명 (1~4)
   * ai_location      : 업소 위치 상세 (섹션④)
   * ai_env           : 근무환경 상세 (섹션⑤)
   * ai_welfare       : 복리후생 (섹션⑨, 구 ai_benefit fallback)
   * ai_qualify       : 자격/우대 (섹션⑩)
   * ai_extra         : 추가설명 (섹션⑪, 구 ai_wrapup fallback)
   * ai_mbti_comment  : MBTI 한마디
   * ai_content       : 종합 답글(레거시)
   * 고정 입력 필드   : job_nickname, job_company, amenity, keyword, mbti_prefer, jr_images 등
   */ ?>
  <!-- eve_alba_ad_editor_3 디자인 100% -->
  <div class="page-wrap jobs-ad-post">
    <!-- 상단 배너 (eve_alba_ad_editor_3 top-header) -->
    <div class="top-header">
      <?php if ($jobtype) { ?><div class="biz-badge" id="disp-biztype">🏮 <?php echo htmlspecialchars($jobtype); ?></div><?php } ?>
      <?php if ($banner_comp && $banner_comp !== '—') { ?><div class="biz-name" id="disp-bizname">🌸 <?php echo htmlspecialchars($banner_comp); ?></div><?php } ?>
      <?php if ($biz_title) { ?><div class="biz-title" id="disp-biztitle"><?php echo htmlspecialchars($biz_title); ?></div><?php } ?>
      <div class="tags">
        <span class="tag tag-loc" id="disp-loc-tag" style="<?php echo !$region?'display:none':''; ?>"><?php echo $region ? '📍 '.htmlspecialchars($region) : ''; ?></span>
        <span class="tag tag-pay" id="disp-pay-tag" style="<?php echo !$salary_disp?'display:none':''; ?>"><?php echo $salary_disp ? '💰 '.htmlspecialchars($salary_disp) : ''; ?></span>
        <?php if ($amenity) { $a1 = explode(',', $amenity); $a1 = array_slice(array_map('trim', $a1), 0, 2); foreach ($a1 as $a) { if ($a) { ?><span class="tag tag-daily">✅ <?php echo htmlspecialchars($a); ?></span><?php } } } ?>
      </div>
    </div>

    <!-- ════════ 🖼️ 업소 이미지 슬라이더 ════════ -->
    <?php
      $jr_images = isset($data['jr_images']) && is_array($data['jr_images']) ? $data['jr_images'] : array();
      $active_images = array();
      foreach ($jr_images as $_img) { if (!empty($_img['url'])) $active_images[] = $_img; }
    ?>
    <div class="section img-slider-section">
      <div class="section-header">
        <span class="section-label">🖼️ 업소 이미지</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('imgslider')">✏️ 수정</button><?php } ?>
      </div>
      <div id="slider-container">
        <?php if (empty($active_images)) { ?>
        <div class="slider-empty">
          <span class="slider-empty-icon">📷</span>
          업소 이미지를 등록해주세요.<br>
          <span style="font-size:11px;color:#ddd;margin-top:4px;display:block;">수정 버튼을 눌러 이미지를 추가하세요.</span>
        </div>
        <?php } else { ?>
        <div class="slider-wrap">
          <?php if (count($active_images) > 1) { ?><div class="slider-counter">1 / <?php echo count($active_images); ?></div><?php } ?>
          <div class="slider-track" id="slider-track">
            <?php foreach ($active_images as $_i => $_img) { ?>
            <div class="slide" onclick="openFullImage('<?php echo htmlspecialchars(addslashes($_img['url'])); ?>')">
              <img src="<?php echo htmlspecialchars($_img['url']); ?>" alt="업소이미지<?php echo $_i+1; ?>">
              <?php if (!empty($_img['caption'])) { ?><div class="slide-caption"><?php echo htmlspecialchars($_img['caption']); ?></div><?php } ?>
            </div>
            <?php } ?>
          </div>
          <?php if (count($active_images) > 1) { ?>
          <button type="button" class="slider-arrow prev" onclick="prevSlide()">‹</button>
          <button type="button" class="slider-arrow next" onclick="nextSlide()">›</button>
          <?php } ?>
        </div>
        <?php if (count($active_images) > 1) { ?>
        <div class="slider-indicators">
          <?php foreach ($active_images as $_i => $_img) { ?>
          <button type="button" class="s-dot<?php echo $_i===0?' active':''; ?>" onclick="goSlide(<?php echo $_i; ?>)"></button>
          <?php } ?>
        </div>
        <?php } ?>
        <?php } ?>
      </div>
    </div>

    <!-- 원본 이미지 뷰어 -->
    <div class="img-fullview-overlay" id="img-fullview" onclick="closeFullImage()">
      <button type="button" class="img-fullview-close" onclick="closeFullImage()">✕</button>
      <img id="img-fullview-img" src="" alt="원본 이미지">
    </div>

    <!-- ① 기본 정보 (eve_alba_ad_editor_3 — display + modal) -->
    <div class="section ad-basic-info" data-jr-id="<?php echo (int)$jr_id; ?>">
      <div class="section-header">
        <span class="section-label">📋 기본 정보</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit btn-edit-basic" onclick="openModal('basic')">✏️ 수정</button><?php } ?>
      </div>
      <table class="info-table">
        <tbody>
          <tr><td class="lbl">🏷️ 닉네임</td><td class="val" id="disp-nick"><?php echo htmlspecialchars($nick ?: '—'); ?></td></tr>
          <tr><td class="lbl">🏪 상호</td><td class="val" id="disp-comp"><?php echo htmlspecialchars($comp ?: '—'); ?> <button type="button" class="btn-biz-info" onclick="openBizInfoPopup()">기업정보 확인하기</button></td></tr>
          <tr><td class="lbl">📞 연락처</td><td class="val val-pink" id="disp-tel"><?php echo htmlspecialchars($contact); ?></td></tr>
          <tr><td class="lbl">💬 SNS</td><td class="val" id="disp-sns"><?php
            $_img_base = G5_THEME_URL.'/img';
            $sns_chips = array();
            if ($sns_kakao) $sns_chips[] = '<span class="sns-chip" style="background:#FEE500;color:#3C1E1E;"><img src="'.$_img_base.'/logo_kakao.svg" alt="카카오" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;">'.htmlspecialchars($sns_kakao).'</span>';
            if ($sns_line) $sns_chips[] = '<span class="sns-chip" style="background:#06C755;color:#fff;"><img src="'.$_img_base.'/logo_line.svg" alt="라인" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;">'.htmlspecialchars($sns_line).'</span>';
            if ($sns_telegram) $sns_chips[] = '<span class="sns-chip" style="background:#26A5E4;color:#fff;"><img src="'.$_img_base.'/logo_telegram.svg" alt="텔레그램" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;">'.htmlspecialchars($sns_telegram).'</span>';
            echo implode(' ', $sns_chips ?: array('-'));
          ?></td></tr>
          <tr><td class="lbl">💰 급여조건</td><td class="val" id="disp-pay"><span style="display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-size:12px;font-weight:900;padding:4px 14px;border-radius:20px;"><?php echo htmlspecialchars($salary_disp ?: '—'); ?></span></td></tr>
          <tr><td class="lbl">📍 근무&광고 지역</td><td class="val" id="disp-loc"><?php echo htmlspecialchars($region ?: '—'); ?></td></tr>
          <tr><td class="lbl">🏮 업종/직종</td><td class="val" id="disp-bizcat"><?php
            if ($jobtype) { $cats = array_map('trim', explode('/', $jobtype)); foreach ($cats as $c) { if ($c) echo '<span class="cat-chip" style="background:#FFE4F0;color:#C9007A;">'.htmlspecialchars($c).'</span> '; } } else { echo '—'; }
          ?></td></tr>
        </tbody>
      </table>
    </div>

    <?php
      /* eve_alba_ad_editor_3 100% — 모든 영역 항상 표시, 데이터만 삽입 구조 */
      $pt1_title = $ai_card1_title ?: (!empty($ai_location) ? '역에서 가까워요!' : ($region ? '접근이 편해요!' : '접근 편의'));
      $pt1_desc = $ai_card1_desc ?: (!empty($ai_location) ? $ai_location : ($region ? htmlspecialchars($region).' 인근에서 편하게 출퇴근하실 수 있어요.' : '—'));
      $pt2_title = $ai_card2_title ?: (!empty($ai_env) ? '신규 인테리어' : '근무환경');
      $pt2_desc = $ai_card2_desc ?: (!empty($ai_env) ? $ai_env : '—');
      $pt3_title = $ai_card3_title ?: ((!empty($ai_welfare) || $salary_disp || $amenity) ? '급여 시원하게!' : '급여·혜택');
      $pt3_desc = $ai_card3_desc ?: (!empty($ai_welfare) ? $ai_welfare : (($salary_disp || $amenity) ? trim(($salary_disp ? '급여 협의 가능해요. ' : '').($amenity ? htmlspecialchars($amenity) : '')) : '—'));
      $pt4_title = $ai_card4_title ?: (!empty($ai_extra) ? '텃세 NO! 친구와 함께!' : '환영 분위기');
      $pt4_desc = $ai_card4_desc ?: (!empty($ai_extra) ? $ai_extra : '—');
      $kw_arr = is_array($data['keyword'] ?? null) ? array_map('trim', $data['keyword']) : (trim($keyword ?? '') ? array_map('trim', explode(',', $keyword)) : array());
      $mbti_arr = is_array($data['mbti_prefer'] ?? null) ? array_map('trim', $data['mbti_prefer']) : (trim($mbti ?? '') ? array_map('trim', explode(',', $mbti)) : array());
      $ai_mbti_text = $ai_mbti_comment_val;
    ?>
    <?php /* eve_alba_ad_editor_3 ②~⑪ 100% display+modal — 항상 표시 */ ?>
    <!-- ② 채용제목·고용형태 (display + modal) -->
    <div class="section ad-intro" data-section="ai_intro" data-jr-id="<?php echo (int)$jr_id; ?>">
      <div class="section-header">
        <span class="section-label">📝 채용제목 · 고용형태</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('recruit')">✏️ 수정</button><?php } ?>
      </div>
      <div class="section-body">
        <div class="intro-bar">💖 안녕하세요, 예비 공주님들!</div>
        <div class="section-text" id="disp-recruit"><?php echo nl2br(htmlspecialchars($ai_intro ?? '')); ?></div>
      </div>
    </div>
    <!-- ③ 포인트 카드 (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">✨ 이런 점이 달라요</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('cards')">✏️ 수정</button><?php } ?>
      </div>
      <div class="cards-grid">
        <div class="p-card card-pink">
          <div class="p-card-icon">🚶‍♀️</div>
          <div class="p-card-title" id="disp-c1t"><?php echo htmlspecialchars($pt1_title); ?></div>
          <div class="p-card-desc" id="disp-c1d"><?php echo nl2br(htmlspecialchars($pt1_desc)); ?></div>
        </div>
        <div class="p-card card-gold">
          <div class="p-card-icon">💎</div>
          <div class="p-card-title" id="disp-c2t"><?php echo htmlspecialchars($pt2_title); ?></div>
          <div class="p-card-desc" id="disp-c2d"><?php echo nl2br(htmlspecialchars($pt2_desc)); ?></div>
        </div>
        <div class="p-card card-green">
          <div class="p-card-icon">💵</div>
          <div class="p-card-title" id="disp-c3t"><?php echo htmlspecialchars($pt3_title); ?></div>
          <div class="p-card-desc" id="disp-c3d"><?php echo nl2br(htmlspecialchars($pt3_desc)); ?></div>
        </div>
        <div class="p-card card-purple">
          <div class="p-card-icon">👯‍♀️</div>
          <div class="p-card-title" id="disp-c4t"><?php echo htmlspecialchars($pt4_title); ?></div>
          <div class="p-card-desc" id="disp-c4d"><?php echo nl2br(htmlspecialchars($pt4_desc)); ?></div>
        </div>
      </div>
    </div>
    <!-- ④ 업소 위치 및 소개 (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">📍 업소 위치 및 업소 소개</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('location')">✏️ 수정</button><?php } ?>
      </div>
      <div class="detail-block" style="padding-top:18px;">
        <div class="detail-row">
          <div class="detail-badge">📍 업소 위치</div>
          <div class="detail-box" id="disp-location"><?php echo nl2br(htmlspecialchars($ai_location ?: $pt1_desc)); ?></div>
        </div>
      </div>
    </div>
    <!-- ⑤ 근무환경 (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">🏢 근무환경</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('workenv')">✏️ 수정</button><?php } ?>
      </div>
      <div class="detail-block" style="padding-top:18px;">
        <div class="detail-row">
          <div class="detail-badge">🏢 근무환경</div>
          <div class="detail-box" id="disp-workenv"><?php echo nl2br(htmlspecialchars($ai_env ?: $pt2_desc)); ?></div>
        </div>
      </div>
    </div>
    <?php
      $BENEFITS_ARR = array('당일지급','선불가능','순번확실','원룸제공','만근비지원','성형지원','출퇴근지원','식사제공','팁별도','인센티브','홀복지원','갯수보장','지명우대','초이스없음','해외여행지원','뒷방없음','따당가능','푸쉬가능','밀방없음','칼퇴보장','텃새없음','숙식제공');
      $amenity_checked = array_flip(array_map('trim', $amenity_arr));
    ?>
    <!-- ⑥ 편의사항 (display chips + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">✅ 편의사항</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('benefits')">✏️ 수정</button><?php } ?>
      </div>
      <div class="chips-wrap" id="disp-benefits"><?php
        foreach ($amenity_arr as $a) { if ($a) echo '<span class="chip">✅ '.htmlspecialchars($a).'</span>'; }
        if (empty($amenity_arr)) echo '<span class="chips-empty">선택된 편의사항이 없습니다</span>';
      ?></div>
    </div>
    <?php
      $KEYWORDS_ARR = array('신규업소','초보가능','경력우대','주말알바','투잡알바','당일지급','생리휴무','룸싸롱','주점','바','요정','다방','마사지','아가씨','초미씨','미씨','TC','44사이즈우대','박스환영','장기근무','타지역우대','에이스우대','업소','기타');
      $kw_checked = array_flip($kw_arr);
    ?>
    <!-- ⑦ 키워드 (display chips + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">🔖 키워드</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('keywords')">✏️ 수정</button><?php } ?>
      </div>
      <div class="chips-wrap" id="disp-keywords"><?php
        foreach ($kw_arr as $k) { if ($k) echo '<span class="chip chip-kw">🏷️ '.htmlspecialchars($k).'</span>'; }
        if (empty($kw_arr)) echo '<span class="chips-empty">선택된 키워드가 없습니다</span>';
      ?></div>
    </div>
    <?php
      $mbti_checked = array_flip($mbti_arr);
      $MBTI_GROUPS_PHP = array(
        array('name'=>'NT — 분석가형','dot'=>'#1565C0','cls'=>'mbti-nt','types'=>array(
          array('t'=>'INTJ','d'=>'고객 성향 빠른 분석, 장기 단골 전략 설계에 강함'),
          array('t'=>'INTP','d'=>'대화 주제 확장력 뛰어나고 지적 매력 어필 가능'),
          array('t'=>'ENTJ','d'=>'목표 매출 설정·관리 능력 우수, 자기 브랜딩 강함'),
          array('t'=>'ENTP','d'=>'말 센스 좋고 토론·농담으로 분위기 반전 능력 탁월')
        )),
        array('name'=>'NF — 외교관형','dot'=>'#2E7D32','cls'=>'mbti-nf','types'=>array(
          array('t'=>'INFJ','d'=>'깊은 공감 능력, 감정 상담형 고객에게 매우 강함'),
          array('t'=>'INFP','d'=>'순수·감성 매력, 특정 고객층에게 강한 팬층 형성'),
          array('t'=>'ENFJ','d'=>'고객을 특별하게 만들어주는 능력, VIP 관리 최강'),
          array('t'=>'ENFP','d'=>'밝은 에너지와 리액션, 첫인상 흡입력 매우 높음')
        )),
        array('name'=>'SJ — 관리자형','dot'=>'#E65100','cls'=>'mbti-sj','types'=>array(
          array('t'=>'ISTJ','d'=>'시간·약속 철저, 안정적인 신뢰 구축형'),
          array('t'=>'ISFJ','d'=>'섬세한 배려, 단골 관리 지속력 강함'),
          array('t'=>'ESTJ','d'=>'실적 관리·목표 달성 집요함'),
          array('t'=>'ESFJ','d'=>'친화력 최고 수준, 관계 유지 능력 뛰어남')
        )),
        array('name'=>'SP — 탐험가형','dot'=>'#C62828','cls'=>'mbti-sp','types'=>array(
          array('t'=>'ISTP','d'=>'상황 판단 빠름, 감정 휘둘림 적음'),
          array('t'=>'ISFP','d'=>'자연스러운 매력, 부드러운 분위기 형성'),
          array('t'=>'ESTP','d'=>'밀당·텐션 조절 능숙, 현장 적응력 강함'),
          array('t'=>'ESFP','d'=>'분위기 메이커, 고객 몰입도 상승 능력 탁월')
        ))
      );
    ?>
    <!-- ⑧ 선호 MBTI (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">🧠 선호 MBTI</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('mbti')">✏️ 수정</button><?php } ?>
      </div>
      <div class="mbti-wrap" id="disp-mbti"><?php
        foreach ($MBTI_GROUPS_PHP as $g) {
          $active = array();
          foreach ($g['types'] as $item) { if (in_array($item['t'], $mbti_arr)) $active[] = $item; }
          if (empty($active)) continue;
          echo '<div class="mbti-group"><div class="mbti-group-title"><span class="mbti-group-dot" style="background:'.$g['dot'].';"></span>'.$g['name'].'</div><div class="mbti-cards">';
          foreach ($active as $item) echo '<div class="mbti-card '.$g['cls'].' selected"><div class="mbti-card-name">'.$item['t'].'</div><div class="mbti-card-desc">'.htmlspecialchars($item['d']).'</div><span class="mbti-card-check">●</span></div>';
          echo '</div></div>';
        }
        if (empty($mbti_arr)) echo '<div style="padding:14px 0;color:#ccc;font-size:12px;">선택된 MBTI가 없습니다</div>';
      ?></div>
      <div class="mbti-text-section">
        <div class="mbti-text-label"><span>💬 MBTI 관련 한마디</span><?php if ($can_edit) { ?><button type="button" class="btn-edit" style="font-size:10px;padding:3px 10px;" onclick="openModal('mbti-text')">✏️ 수정</button><?php } ?></div>
        <div class="mbti-text-display" id="disp-mbti-text"><?php echo nl2br(htmlspecialchars($ai_mbti_text ?: '우리 업소는 어떤 MBTI도 환영해요!')); ?></div>
      </div>
    </div>
    <!-- ⑨ 지원 혜택 및 복리후생 (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">🎁 지원 혜택 및 복리후생</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('welfare')">✏️ 수정</button><?php } ?>
      </div>
      <div class="detail-block" style="padding-top:18px;">
        <div class="detail-row">
          <div class="detail-badge">💰 급여 혜택</div>
          <div class="detail-box" id="disp-welfare"><?php echo nl2br(htmlspecialchars($ai_welfare ?: $pt3_desc)); ?></div>
        </div>
      </div>
    </div>
    <!-- ⑩ 지원 자격 및 우대사항 (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">📋 지원 자격 및 우대사항</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('qualify')">✏️ 수정</button><?php } ?>
      </div>
      <div class="detail-block" style="padding-top:18px;">
        <div class="detail-row">
          <div class="detail-badge">📋 지원 자격</div>
          <div class="detail-box" id="disp-qualify"><?php echo nl2br(htmlspecialchars($ai_qualify ?: $desc_qualify ?: '—')); ?></div>
        </div>
      </div>
    </div>
    <!-- ⑪ 추가 상세설명 (display + modal) -->
    <div class="section">
      <div class="section-header">
        <span class="section-label">📄 추가 상세설명</span>
        <?php if ($can_edit) { ?><button type="button" class="btn-edit" onclick="openModal('extra')">✏️ 수정</button><?php } ?>
      </div>
      <div class="promise-body">
        <div style="font-size:12px;font-weight:900;color:var(--pink);margin-bottom:10px;">🎀 언니 사장의 약속</div>
        <div class="promise-list" id="disp-extra"><?php echo nl2br(htmlspecialchars($ai_extra ?: $desc_extra ?: '—')); ?></div>
      </div>
    </div>


    <!-- CTA 하단 연락처 (톤별 마무리 인사말) -->
    <?php
      $_cta_tone = isset($data['ai_tone']) ? $data['ai_tone'] : 'unnie';
      $_cta_default = ['title' => '💌 지금 바로 연락주세요! 기다리고 있을게요~', 'sub' => '자다가 깨서 연락 주셔도 괜찮아요! 🌙 24시간 열려 있어요'];
      $_cta = isset($gemini_closing[$_cta_tone]) ? $gemini_closing[$_cta_tone] : $_cta_default;
    ?>
    <div class="section cta-footer">
      <div class="cta-title"><?php echo $_cta['title']; ?></div>
      <div class="cta-sub"><?php echo $_cta['sub']; ?></div>
      <?php if ($sns_kakao || $sns_line || $sns_telegram) { ?>
      <div class="cta-btns">
        <?php if ($sns_kakao) { ?><a href="https://open.kakao.com/o/s/<?php echo htmlspecialchars($sns_kakao); ?>" target="_blank" rel="noopener" class="cta-btn cta-kakao"><img src="<?php echo $_img_base; ?>/logo_kakao.svg" alt="카카오" class="cta-btn-logo"> 카카오 <?php echo htmlspecialchars($sns_kakao); ?></a><?php } ?>
        <?php if ($sns_line) { ?><span class="cta-btn cta-line"><img src="<?php echo $_img_base; ?>/logo_line.svg" alt="라인" class="cta-btn-logo"> 라인 <?php echo htmlspecialchars($sns_line); ?></span><?php } ?>
        <?php if ($sns_telegram) { ?><span class="cta-btn cta-tg"><img src="<?php echo $_img_base; ?>/logo_telegram.svg" alt="텔레그램" class="cta-btn-logo"> 텔레그램 <?php echo htmlspecialchars($sns_telegram); ?></span><?php } ?>
      </div>
      <?php } ?>
      <?php if ($contact) { ?><a href="tel:<?php echo preg_replace('/[^0-9+]/','',$contact); ?>" class="cta-phone">📞 <?php echo htmlspecialchars($contact); ?></a><?php } ?>
      <?php if ($nick && $nick !== '—') { ?><div class="cta-watermark">🌸 이브알바 EVE ALBA — <?php echo htmlspecialchars($nick); ?></div><?php } ?>
    </div>

    <!-- 추천하기 -->
    <?php if ($row['jr_status'] === 'ongoing') { ?>
    <div class="jobs-good-area" style="display:flex;align-items:center;justify-content:center;gap:14px;padding:24px 0;margin:0 auto;">
      <?php if ($is_member) { ?>
      <button type="button" id="jobs-good-btn" class="btn-rec" onclick="doJobsGood()" style="display:inline-flex;align-items:center;gap:6px;padding:12px 28px;border:none;border-radius:30px;background:linear-gradient(135deg,#FF1B6B,#FF6B35);color:#fff;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 3px 12px rgba(255,27,107,.3);transition:transform .15s,box-shadow .15s;">
        <span style="font-size:18px;">👍</span> 추천하기
      </button>
      <?php } else { ?>
      <button type="button" class="btn-rec" onclick="alert('회원만 추천할 수 있습니다.')" style="display:inline-flex;align-items:center;gap:6px;padding:12px 28px;border:none;border-radius:30px;background:#ccc;color:#fff;font-size:15px;font-weight:700;cursor:pointer;">
        <span style="font-size:18px;">👍</span> 추천하기
      </button>
      <?php } ?>
      <div style="display:flex;align-items:center;gap:6px;padding:10px 20px;background:rgba(255,182,193,.3);border-radius:30px;">
        <span style="font-size:20px;">❤️</span>
        <span id="jobs-good-count" style="font-size:18px;font-weight:900;color:#FF1B6B;"><?php echo number_format($_jr_good); ?></span>
      </div>
    </div>
    <?php if ($is_member) { ?>
    <div id="jobs-good-info" style="text-align:center;font-size:12px;color:#999;margin-top:-12px;padding-bottom:16px;">오늘 <?php echo $_jr_good_used; ?>/<?php echo $_jr_good_limit; ?> 사용</div>
    <?php } ?>
    <?php } ?>
  </div>

    <!-- ① 기본정보 모달 (eve_alba_ad_editor_3) -->
    <?php if ($can_edit) { ?>
    <div id="modal-basic" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header">
          <span class="modal-title">📋 기본 정보 수정</span>
          <button type="button" class="modal-close" onclick="closeModal('basic')">✕</button>
        </div>
        <div class="modal-body">
          <div class="modal-field"><label class="modal-label">🏷️ 닉네임</label><input type="text" class="modal-input" id="inp-nick" value="<?php echo htmlspecialchars($nick ?: ''); ?>" readonly disabled style="background:#f0f0f0;color:#999;cursor:not-allowed;" /><span style="font-size:11px;color:#aaa;">회원가입 시 등록한 닉네임 (변경: 회원정보 수정)</span></div>
          <div class="modal-field"><label class="modal-label">🏪 상호</label><input type="text" class="modal-input" id="inp-comp" value="<?php echo htmlspecialchars($comp ?: ''); ?>" readonly disabled style="background:#f0f0f0;color:#999;cursor:not-allowed;" /><span style="font-size:11px;color:#aaa;">채용등록 시 입력한 상호 (변경: 채용정보 수정)</span></div>
          <div class="modal-field"><label class="modal-label">📞 연락처</label><input type="text" class="modal-input" id="inp-tel" value="<?php echo htmlspecialchars($contact); ?>" /></div>
          <div class="modal-field"><label class="modal-label">💬 카카오 ID</label><input type="text" class="modal-input" id="inp-kakao" value="<?php echo htmlspecialchars($sns_kakao); ?>" /></div>
          <div class="modal-field"><label class="modal-label">💬 라인 ID</label><input type="text" class="modal-input" id="inp-line" value="<?php echo htmlspecialchars($sns_line); ?>" /></div>
          <div class="modal-field"><label class="modal-label">💬 텔레그램 ID</label><input type="text" class="modal-input" id="inp-telegram" value="<?php echo htmlspecialchars($sns_telegram); ?>" /></div>
          <div class="modal-field"><label class="modal-label">💰 급여조건</label>
            <div style="display:flex;gap:8px;align-items:center;">
              <select id="inp-salary-type" class="modal-input" style="width:auto;min-width:100px;">
                <option value="급여협의"<?php echo ($salary_type==='급여협의')?' selected':''; ?>>급여협의</option>
                <option value="시급"<?php echo ($salary_type==='시급')?' selected':''; ?>>시급</option>
                <option value="일급"<?php echo ($salary_type==='일급')?' selected':''; ?>>일급</option>
                <option value="주급"<?php echo ($salary_type==='주급')?' selected':''; ?>>주급</option>
                <option value="월급"<?php echo ($salary_type==='월급')?' selected':''; ?>>월급</option>
              </select>
              <input type="text" id="inp-salary-amt" class="modal-input" value="<?php echo htmlspecialchars($salary_amt); ?>" placeholder="금액" style="width:100px;" /><span style="font-size:12px;color:#888;">원</span>
            </div>
          </div>
          <div class="modal-field"><label class="modal-label">📍 근무지역 1순위 * <span style="font-size:11px;color:#888;font-weight:400;">(근무지역)</span></label>
            <div style="display:flex;gap:8px;">
              <select id="inp-reg1" class="modal-input" style="width:50%;" onchange="filterRegDetail('inp-reg1','inp-regd1')">
                <option value="">지역선택</option>
                <?php foreach ($ev_regions as $_r) { ?><option value="<?php echo (int)$_r['er_id']; ?>"<?php echo ((string)$reg1_id===(string)$_r['er_id'])?' selected':''; ?>><?php echo htmlspecialchars($_r['er_name']); ?></option><?php } ?>
              </select>
              <select id="inp-regd1" class="modal-input" style="width:50%;">
                <option value="">세부지역선택</option>
                <?php foreach ($ev_region_details as $_rd) { ?><option value="<?php echo (int)$_rd['erd_id']; ?>" data-er-id="<?php echo (int)$_rd['er_id']; ?>"<?php echo ((string)$reg1_detail_id===(string)$_rd['erd_id'])?' selected':''; ?>><?php echo htmlspecialchars($_rd['erd_name']); ?></option><?php } ?>
              </select>
            </div>
          </div>
          <div class="modal-field"><label class="modal-label">📍 광고지역 2순위 <span style="font-size:11px;color:#888;font-weight:400;">(추가 광고지역)</span></label>
            <div style="display:flex;gap:8px;">
              <select id="inp-reg2" class="modal-input" style="width:50%;" onchange="filterRegDetail('inp-reg2','inp-regd2')">
                <option value="">지역선택</option>
                <?php foreach ($ev_regions as $_r) { ?><option value="<?php echo (int)$_r['er_id']; ?>"<?php echo ((string)$reg2_id===(string)$_r['er_id'])?' selected':''; ?>><?php echo htmlspecialchars($_r['er_name']); ?></option><?php } ?>
              </select>
              <select id="inp-regd2" class="modal-input" style="width:50%;">
                <option value="">세부지역선택</option>
                <?php foreach ($ev_region_details as $_rd) { ?><option value="<?php echo (int)$_rd['erd_id']; ?>" data-er-id="<?php echo (int)$_rd['er_id']; ?>"<?php echo ((string)$reg2_detail_id===(string)$_rd['erd_id'])?' selected':''; ?>><?php echo htmlspecialchars($_rd['erd_name']); ?></option><?php } ?>
              </select>
            </div>
          </div>
          <div class="modal-field"><label class="modal-label">📍 광고지역 3순위 <span style="font-size:11px;color:#888;font-weight:400;">(추가 광고지역)</span></label>
            <div style="display:flex;gap:8px;">
              <select id="inp-reg3" class="modal-input" style="width:50%;" onchange="filterRegDetail('inp-reg3','inp-regd3')">
                <option value="">지역선택</option>
                <?php foreach ($ev_regions as $_r) { ?><option value="<?php echo (int)$_r['er_id']; ?>"<?php echo ((string)$reg3_id===(string)$_r['er_id'])?' selected':''; ?>><?php echo htmlspecialchars($_r['er_name']); ?></option><?php } ?>
              </select>
              <select id="inp-regd3" class="modal-input" style="width:50%;">
                <option value="">세부지역선택</option>
                <?php foreach ($ev_region_details as $_rd) { ?><option value="<?php echo (int)$_rd['erd_id']; ?>" data-er-id="<?php echo (int)$_rd['er_id']; ?>"<?php echo ((string)$reg3_detail_id===(string)$_rd['erd_id'])?' selected':''; ?>><?php echo htmlspecialchars($_rd['erd_name']); ?></option><?php } ?>
              </select>
            </div>
          </div>
          <div class="modal-field"><label class="modal-label">🏮 업종 / 직종</label>
            <div style="display:flex;gap:8px;">
              <select id="inp-job1" class="modal-input" style="width:50%;">
                <option value="">-1차 직종선택-</option>
                <?php foreach (array('단란주점','룸살롱','가라오케','노래방','클럽','바(Bar)','퍼블릭','마사지','풀살롱') as $_j) { ?><option<?php echo ($job1===$_j)?' selected':''; ?>><?php echo $_j; ?></option><?php } ?>
              </select>
              <select id="inp-job2" class="modal-input" style="width:50%;">
                <option value="">-2차 직종선택-</option>
                <?php foreach (array('서빙','도우미','아가씨','TC','미시','초미시') as $_j) { ?><option<?php echo ($job2===$_j)?' selected':''; ?>><?php echo $_j; ?></option><?php } ?>
              </select>
            </div>
          </div>
          <div class="modal-field"><label class="modal-label">📝 채용 제목 (상단 배너 부제목)</label><input type="text" class="modal-input" id="inp-biztitle" value="<?php echo htmlspecialchars($biz_title); ?>" /></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeModal('basic')">취소</button>
          <button type="button" class="btn-save" onclick="saveBasic()">저장</button>
        </div>
      </div>
    </div>
    <div id="modal-recruit" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">📝 채용제목 · 인사말 수정</span><button type="button" class="modal-close" onclick="closeModal('recruit')">✕</button></div>
        <div class="modal-body">
          <div class="modal-field"><label class="modal-label">✏️ 인사말 / 채용 소개글</label><textarea class="modal-input" id="inp-recruit" style="min-height:150px;"><?php echo htmlspecialchars($ai_intro ?? ''); ?></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('recruit')">취소</button><button type="button" class="btn-save" onclick="saveSection('recruit','ai_intro','inp-recruit','disp-recruit')">저장</button></div>
      </div>
    </div>
    <div id="modal-cards" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">✨ 포인트 카드 수정</span><button type="button" class="modal-close" onclick="closeModal('cards')">✕</button></div>
        <div class="modal-body">
          <div style="font-size:11px;color:#aaa;margin-bottom:14px;">각 카드의 제목과 설명을 수정하세요.</div>
          <div class="modal-field"><label class="modal-label">🚶‍♀️ 카드 1 제목</label><input type="text" class="modal-input" id="inp-c1t" value="<?php echo htmlspecialchars($pt1_title); ?>"></div>
          <div class="modal-field"><label class="modal-label">🚶‍♀️ 카드 1 설명</label><textarea class="modal-input" id="inp-c1d" style="min-height:60px;"><?php echo htmlspecialchars($pt1_desc); ?></textarea></div>
          <div class="modal-field"><label class="modal-label">💎 카드 2 제목</label><input type="text" class="modal-input" id="inp-c2t" value="<?php echo htmlspecialchars($pt2_title); ?>"></div>
          <div class="modal-field"><label class="modal-label">💎 카드 2 설명</label><textarea class="modal-input" id="inp-c2d" style="min-height:60px;"><?php echo htmlspecialchars($pt2_desc); ?></textarea></div>
          <div class="modal-field"><label class="modal-label">💵 카드 3 제목</label><input type="text" class="modal-input" id="inp-c3t" value="<?php echo htmlspecialchars($pt3_title); ?>"></div>
          <div class="modal-field"><label class="modal-label">💵 카드 3 설명</label><textarea class="modal-input" id="inp-c3d" style="min-height:60px;"><?php echo htmlspecialchars($pt3_desc); ?></textarea></div>
          <div class="modal-field"><label class="modal-label">👯 카드 4 제목</label><input type="text" class="modal-input" id="inp-c4t" value="<?php echo htmlspecialchars($pt4_title); ?>"></div>
          <div class="modal-field"><label class="modal-label">👯 카드 4 설명</label><textarea class="modal-input" id="inp-c4d" style="min-height:60px;"><?php echo htmlspecialchars($pt4_desc); ?></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('cards')">취소</button><button type="button" class="btn-save" onclick="saveCards()">저장</button></div>
      </div>
    </div>
    <div id="modal-location" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">📍 업소 위치 및 소개 수정</span><button type="button" class="modal-close" onclick="closeModal('location')">✕</button></div>
        <div class="modal-body"><div class="modal-field"><label class="modal-label">✏️ 업소 위치 설명</label><textarea class="modal-input" id="inp-location" style="min-height:120px;"><?php echo htmlspecialchars($ai_location ?: $pt1_desc); ?></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('location')">취소</button><button type="button" class="btn-save" onclick="saveSection('location','ai_location','inp-location','disp-location')">저장</button></div>
      </div>
    </div>
    <div id="modal-workenv" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">🏢 근무환경 수정</span><button type="button" class="modal-close" onclick="closeModal('workenv')">✕</button></div>
        <div class="modal-body"><div class="modal-field"><label class="modal-label">✏️ 근무환경 설명</label><textarea class="modal-input" id="inp-workenv" style="min-height:120px;"><?php echo htmlspecialchars($ai_env ?: $pt2_desc); ?></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('workenv')">취소</button><button type="button" class="btn-save" onclick="saveSection('workenv','ai_env','inp-workenv','disp-workenv')">저장</button></div>
      </div>
    </div>
    <div id="modal-benefits" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">✅ 편의사항 수정</span><button type="button" class="modal-close" onclick="closeModal('benefits')">✕</button></div>
        <div class="modal-body"><div style="font-size:11px;color:#aaa;margin-bottom:14px;">해당하는 편의사항을 선택하세요.</div><div id="benefit-checks" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px 4px;"></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('benefits')">취소</button><button type="button" class="btn-save" onclick="saveBenefits()">저장</button></div>
      </div>
    </div>
    <div id="modal-keywords" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">🔖 키워드 수정</span><button type="button" class="modal-close" onclick="closeModal('keywords')">✕</button></div>
        <div class="modal-body"><div style="font-size:11px;color:#aaa;margin-bottom:14px;">해당하는 키워드를 선택하세요.</div><div id="keyword-checks" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px 4px;"></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('keywords')">취소</button><button type="button" class="btn-save" onclick="saveKeywords()">저장</button></div>
      </div>
    </div>
    <div id="modal-mbti" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">🧠 선호 MBTI 수정</span><button type="button" class="modal-close" onclick="closeModal('mbti')">✕</button></div>
        <div class="modal-body"><div style="font-size:11px;color:#aaa;margin-bottom:14px;">카드를 클릭해서 선택/해제하세요. (다중선택 가능)</div><div id="mbti-checks"></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('mbti')">취소</button><button type="button" class="btn-save" onclick="saveMbti()">저장</button></div>
      </div>
    </div>
    <div id="modal-mbti-text" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">💬 MBTI 관련 한마디 수정</span><button type="button" class="modal-close" onclick="closeModal('mbti-text')">✕</button></div>
        <div class="modal-body"><div class="modal-field"><label class="modal-label">✏️ MBTI 관련 내용</label><textarea class="modal-input" id="inp-mbti-text" style="min-height:120px;"><?php echo htmlspecialchars($ai_mbti_text ?: ''); ?></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('mbti-text')">취소</button><button type="button" class="btn-save" onclick="saveSection('mbti-text','ai_mbti_comment','inp-mbti-text','disp-mbti-text')">저장</button></div>
      </div>
    </div>
    <div id="modal-welfare" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">🎁 지원 혜택 및 복리후생 수정</span><button type="button" class="modal-close" onclick="closeModal('welfare')">✕</button></div>
        <div class="modal-body"><div class="modal-field"><label class="modal-label">✏️ 혜택 내용</label><textarea class="modal-input" id="inp-welfare" style="min-height:130px;"><?php echo htmlspecialchars($ai_welfare ?: $pt3_desc); ?></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('welfare')">취소</button><button type="button" class="btn-save" onclick="saveSection('welfare','ai_welfare','inp-welfare','disp-welfare')">저장</button></div>
      </div>
    </div>
    <div id="modal-qualify" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">📋 지원 자격 및 우대사항 수정</span><button type="button" class="modal-close" onclick="closeModal('qualify')">✕</button></div>
        <div class="modal-body"><div class="modal-field"><label class="modal-label">✏️ 지원 자격 내용</label><textarea class="modal-input" id="inp-qualify" style="min-height:120px;"><?php echo htmlspecialchars($ai_qualify ?: $desc_qualify ?: ''); ?></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('qualify')">취소</button><button type="button" class="btn-save" onclick="saveSection('qualify','ai_qualify','inp-qualify','disp-qualify')">저장</button></div>
      </div>
    </div>
    <div id="modal-extra" class="modal-overlay">
      <div class="modal" onclick="event.stopPropagation();">
        <div class="modal-header"><span class="modal-title">📄 추가 상세설명 수정</span><button type="button" class="modal-close" onclick="closeModal('extra')">✕</button></div>
        <div class="modal-body"><div class="modal-field"><label class="modal-label">✏️ 추가 설명 내용</label><textarea class="modal-input" id="inp-extra" style="min-height:150px;"><?php echo htmlspecialchars($ai_extra ?: $desc_extra ?: ''); ?></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal('extra')">취소</button><button type="button" class="btn-save" onclick="saveSection('extra','ai_extra','inp-extra','disp-extra')">저장</button></div>
      </div>
    </div>
    <!-- 🖼️ 이미지 슬라이더 수정 모달 -->
    <div class="modal-overlay" id="modal-imgslider">
      <div class="modal" style="max-width:580px;" onclick="event.stopPropagation();">
        <div class="modal-header">
          <span class="modal-title">🖼️ 업소 이미지 수정</span>
          <button type="button" class="modal-close" onclick="closeModal('imgslider')">✕</button>
        </div>
        <div class="modal-body">
          <div style="font-size:11px;color:#aaa;margin-bottom:16px;">이미지는 최대 5장까지 등록할 수 있어요. 각 이미지마다 설명을 입력할 수 있습니다.</div>
          <div class="img-modal-list" id="img-modal-list"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeModal('imgslider')">취소</button>
          <button type="button" class="btn-save" onclick="saveImgSlider()">저장</button>
        </div>
      </div>
    </div>
    <?php } ?>

    <?php if ($can_edit) { ?><div class="deploy-bar"><button type="button" class="btn-deploy" onclick="deployPost()">💾 수정사항 저장</button></div><?php } ?>
    <div class="view-notices" style="margin:0 0 16px;width:100%;">
      <p>* 커뮤니티 정책과 맞지 않는 게시물의 경우 블라인드 또는 삭제될 수 있습니다.</p>
    </div>
    <div class="view-actions" style="margin:0 0 16px;width:100%;">
      <?php if ($is_owner && $is_edit_mode) { ?>
      <a href="<?php echo $jobs_ongoing_url; ?>" class="btn-action btn-list2">📋 목록으로</a>
      <?php } else { ?>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="btn-action btn-list2">📋 목록으로</a>
      <?php } ?>
    </div>
</article>
<script>
(function(){
  var jrId = <?php echo (int)$jr_id; ?>;
  var saveUrl = <?php echo json_encode($jobs_ai_save_url); ?>;
  var basicSaveUrl = <?php echo json_encode($jobs_basic_save_url ?? ''); ?>;
  var bulkSaveUrl = <?php echo json_encode($jobs_bulk_save_url ?? ''); ?>;
  var cardsSaveUrl = <?php echo json_encode($jobs_cards_save_url ?? ''); ?>;
  var imgSaveUrl = <?php echo json_encode($jobs_img_save_url ?? ''); ?>;

  var BENEFITS_ALL = ["당일지급","선불가능","순번확실","원룸제공","만근비지원","성형지원","출퇴근지원","식사제공","팁별도","인센티브","홀복지원","갯수보장","지명우대","초이스없음","해외여행지원","뒷방없음","따당가능","푸쉬가능","밀방없음","칼퇴보장","텃새없음","숙식제공"];
  var KEYWORDS_ALL = ["신규업소","초보가능","경력우대","주말알바","투잡알바","당일지급","생리휴무","룸싸롱","주점","바","요정","다방","마사지","아가씨","초미씨","미씨","TC","44사이즈우대","박스환영","장기근무","타지역우대","에이스우대","업소","기타"];
  var MBTI_GROUPS = [
    {name:"NT — 분석가형",dot:"#1565C0",cls:"mbti-nt",types:[{t:"INTJ",d:"고객 성향 빠른 분석, 장기 단골 전략 설계에 강함"},{t:"INTP",d:"대화 주제 확장력 뛰어나고 지적 매력 어필 가능"},{t:"ENTJ",d:"목표 매출 설정·관리 능력 우수, 자기 브랜딩 강함"},{t:"ENTP",d:"말 센스 좋고 토론·농담으로 분위기 반전 능력 탁월"}]},
    {name:"NF — 외교관형",dot:"#2E7D32",cls:"mbti-nf",types:[{t:"INFJ",d:"깊은 공감 능력, 감정 상담형 고객에게 매우 강함"},{t:"INFP",d:"순수·감성 매력, 특정 고객층에게 강한 팬층 형성"},{t:"ENFJ",d:"고객을 특별하게 만들어주는 능력, VIP 관리 최강"},{t:"ENFP",d:"밝은 에너지와 리액션, 첫인상 흡입력 매우 높음"}]},
    {name:"SJ — 관리자형",dot:"#E65100",cls:"mbti-sj",types:[{t:"ISTJ",d:"시간·약속 철저, 안정적인 신뢰 구축형"},{t:"ISFJ",d:"섬세한 배려, 단골 관리 지속력 강함"},{t:"ESTJ",d:"실적 관리·목표 달성 집요함"},{t:"ESFJ",d:"친화력 최고 수준, 관계 유지 능력 뛰어남"}]},
    {name:"SP — 탐험가형",dot:"#C62828",cls:"mbti-sp",types:[{t:"ISTP",d:"상황 판단 빠름, 감정 휘둘림 적음"},{t:"ISFP",d:"자연스러운 매력, 부드러운 분위기 형성"},{t:"ESTP",d:"밀당·텐션 조절 능숙, 현장 적응력 강함"},{t:"ESFP",d:"분위기 메이커, 고객 몰입도 상승 능력 탁월"}]}
  ];
  var selectedBenefits = <?php echo json_encode($amenity_arr); ?>;
  var selectedKeywords = <?php echo json_encode($kw_arr); ?>;
  var selectedMbti = <?php echo json_encode($mbti_arr); ?>;

  function openModal(id){
    if(id==='benefits')buildBenefitChecks();
    if(id==='keywords')buildKeywordChecks();
    if(id==='mbti')buildMbtiChecks();
    var el=document.getElementById('modal-'+id); if(el){ el.classList.add('is-open'); document.body.style.overflow='hidden'; }
  }
  function closeModal(id){ var el=document.getElementById('modal-'+id); if(el){ el.classList.remove('is-open'); document.body.style.overflow=''; } }
  document.querySelectorAll('.modal-overlay').forEach(function(el){
    el.addEventListener('click', function(e){ if(e.target===el){ el.classList.remove('is-open'); document.body.style.overflow=''; } });
  });

  function filterRegDetail(regId,detailId){
    var reg=document.getElementById(regId), detail=document.getElementById(detailId);
    if(!reg||!detail)return;
    var selVal=reg.value;
    var opts=detail.querySelectorAll('option[data-er-id]');
    opts.forEach(function(o){o.style.display=(selVal&&o.getAttribute('data-er-id')!==selVal)?'none':'';});
    var curSel=detail.querySelector('option:checked');
    if(curSel&&curSel.style.display==='none')detail.value='';
  }
  function saveBasic(){
    var name=((document.getElementById('inp-nick')||{}).value||'').trim();
    var tel=((document.getElementById('inp-tel')||{}).value||'').trim();
    var kakao=((document.getElementById('inp-kakao')||{}).value||'').trim();
    var line=((document.getElementById('inp-line')||{}).value||'').trim();
    var tg=((document.getElementById('inp-telegram')||{}).value||'').trim();
    var st=((document.getElementById('inp-salary-type')||{}).value||'급여협의').trim();
    var sa=((document.getElementById('inp-salary-amt')||{}).value||'').trim();
    var biztitle=((document.getElementById('inp-biztitle')||{}).value||'').trim();
    var r1=((document.getElementById('inp-reg1')||{}).value||'');
    var rd1=((document.getElementById('inp-regd1')||{}).value||'');
    var r2=((document.getElementById('inp-reg2')||{}).value||'');
    var rd2=((document.getElementById('inp-regd2')||{}).value||'');
    var r3=((document.getElementById('inp-reg3')||{}).value||'');
    var rd3=((document.getElementById('inp-regd3')||{}).value||'');
    var j1=((document.getElementById('inp-job1')||{}).value||'');
    var j2=((document.getElementById('inp-job2')||{}).value||'');
    var payDisp=st==='급여협의'?'급여협의':st+(sa?(' '+parseInt(String(sa).replace(/[^0-9]/g,''),10).toLocaleString()+'원'):'');
    var reg1Sel=document.getElementById('inp-reg1'), regd1Sel=document.getElementById('inp-regd1');
    var locText=(reg1Sel&&reg1Sel.options[reg1Sel.selectedIndex]?reg1Sel.options[reg1Sel.selectedIndex].text:'')+(regd1Sel&&regd1Sel.value&&regd1Sel.options[regd1Sel.selectedIndex]?' '+regd1Sel.options[regd1Sel.selectedIndex].text:'');
    if(locText==='지역선택')locText='';
    var jobParts=[j1,j2].filter(function(s){return s&&s.indexOf('직종선택')<0;});
    var fd=new FormData();
    fd.append('jr_id',jrId); fd.append('job_nickname',name); fd.append('job_contact',tel);
    fd.append('job_kakao',kakao); fd.append('job_line',line); fd.append('job_telegram',tg);
    fd.append('job_salary_type',st); fd.append('job_salary_amt',sa);
    fd.append('job_work_region_1',r1); fd.append('job_work_region_detail_1',rd1);
    fd.append('job_work_region_2',r2); fd.append('job_work_region_detail_2',rd2);
    fd.append('job_work_region_3',r3); fd.append('job_work_region_detail_3',rd3);
    fd.append('job_job1',j1); fd.append('job_job2',j2);
    fd.append('job_title',biztitle);
    fetch(basicSaveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(res.ok){
        var disp=document.getElementById('disp-nick'); if(disp)disp.textContent=name||'—';
        disp=document.getElementById('disp-tel'); if(disp)disp.textContent=tel||'—';
        var snsHtml=''; if(kakao)snsHtml+='<span class="sns-chip" style="background:#FEE500;color:#333;">카카오 '+kakao+'</span> ';
        if(line)snsHtml+='<span class="sns-chip" style="background:#00B300;color:#fff;">라인 '+line+'</span> ';
        if(tg)snsHtml+='<span class="sns-chip" style="background:#2AABEE;color:#fff;">텔레그램 '+tg+'</span> ';
        disp=document.getElementById('disp-sns'); if(disp)disp.innerHTML=snsHtml||'—';
        disp=document.getElementById('disp-pay'); if(disp)disp.innerHTML='<span style="display:inline-block;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-size:12px;font-weight:900;padding:4px 14px;border-radius:20px;">'+(payDisp||'—')+'</span>';
        disp=document.getElementById('disp-loc'); if(disp)disp.textContent=locText||'—';
        disp=document.getElementById('disp-bizcat'); if(disp){ var chips=jobParts.map(function(c){return '<span class="cat-chip" style="background:#FFE4F0;color:#C9007A;">'+c+'</span>';}); disp.innerHTML=chips.join(' ')||'—'; }
        disp=document.getElementById('disp-bizname'); if(disp)disp.textContent='🌸 '+(name||'—');
        disp=document.getElementById('disp-biztitle'); if(disp)disp.textContent=biztitle||'';
        disp=document.getElementById('disp-loc-tag'); if(disp){ disp.textContent=locText?'📍 '+locText:''; disp.style.display=locText?'':'none'; }
        disp=document.getElementById('disp-pay-tag'); if(disp){ disp.textContent=payDisp?'💰 '+(payDisp.split('·')[0]||payDisp).trim():''; disp.style.display=payDisp?'':'none'; }
        var pvR1=document.getElementById('pv-loc-r1');
        if(pvR1 && reg1Sel && reg1Sel.options[reg1Sel.selectedIndex]) pvR1.textContent=reg1Sel.options[reg1Sel.selectedIndex].text||'지역';
        var pvDetail=document.getElementById('pv-loc-detail');
        if(pvDetail){
          var detailTxt=(regd1Sel&&regd1Sel.value&&regd1Sel.options[regd1Sel.selectedIndex]?regd1Sel.options[regd1Sel.selectedIndex].text:'상세지역');
          var jobTxt=jobParts.length?jobParts[0]:'업종';
          pvDetail.textContent=detailTxt+' '+jobTxt;
        }
        var pvDesc=document.getElementById('tg-pv-desc'); if(pvDesc) pvDesc.textContent=biztitle||'광고제목';
        var pvWage=document.getElementById('tg-pv-wage'); if(pvWage) pvWage.textContent=payDisp||'급여조건';
        closeModal('basic'); alert('저장되었습니다.');
      } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){ alert('저장 중 오류가 발생했습니다.'); });
  }

  function deployPost(){
    if(!confirm('수정사항을 저장하시겠습니까?')) return;
    var btn=document.querySelector('.btn-deploy');
    if(btn){ btn.textContent='저장 중...'; btn.disabled=true; }
    var payload={jr_id:jrId};
    var thumbEl=document.getElementById('thumb-preview');
    if(thumbEl){
      payload.thumb_gradient=_thumbSelected||'1';
      payload.thumb_title=document.getElementById('thumb-title')?document.getElementById('thumb-title').value:'';
      payload.thumb_text=document.getElementById('thumb-text')?document.getElementById('thumb-text').value:'';
      payload.thumb_text_color=_thumbTextColor||'rgb(255,255,255)';
      payload.thumb_icon=_thumbIcon||'';
      payload.thumb_motion=_thumbMotion||'';
      payload.thumb_wave=_thumbWave?1:0;
      payload.thumb_border=_thumbBorder||'';
    }
    fetch(bulkSaveUrl,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)})
    .then(function(r){return r.json();})
    .then(function(d){
      if(btn){ btn.textContent='✅ 저장 완료!'; btn.style.background='linear-gradient(135deg,#2E7D32,#43A047)'; btn.disabled=false;
        setTimeout(function(){ btn.textContent='💾 수정사항 저장'; btn.style.background=''; },3000);
      }
    }).catch(function(){
      if(btn){ btn.textContent='❌ 저장 실패'; btn.disabled=false;
        setTimeout(function(){ btn.textContent='💾 수정사항 저장'; btn.style.background=''; },3000);
      }
      alert('저장 중 오류가 발생했습니다.');
    });
  }

  function buildBenefitChecks(){
    var wrap=document.getElementById('benefit-checks'); if(!wrap)return;
    wrap.innerHTML='';
    BENEFITS_ALL.forEach(function(b){
      var chk=selectedBenefits.indexOf(b)>=0;
      wrap.innerHTML+='<label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;padding:4px 0;"><input type="checkbox" value="'+b+'" '+(chk?'checked':'')+' style="accent-color:#FF1B6B;width:14px;height:14px;cursor:pointer;">'+b+'</label>';
    });
  }
  function buildKeywordChecks(){
    var wrap=document.getElementById('keyword-checks'); if(!wrap)return;
    wrap.innerHTML='';
    KEYWORDS_ALL.forEach(function(k){
      var chk=selectedKeywords.indexOf(k)>=0;
      wrap.innerHTML+='<label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;padding:4px 0;"><input type="checkbox" value="'+k+'" '+(chk?'checked':'')+' style="accent-color:#7B1FA2;width:14px;height:14px;cursor:pointer;">'+k+'</label>';
    });
  }
  function buildMbtiChecks(){
    var wrap=document.getElementById('mbti-checks'); if(!wrap)return;
    wrap.innerHTML='';
    MBTI_GROUPS.forEach(function(g){
      var groupDiv=document.createElement('div'); groupDiv.style.marginBottom='20px';
      var titleDiv=document.createElement('div'); titleDiv.className='mbti-group-title';
      titleDiv.innerHTML='<span class="mbti-group-dot" style="background:'+g.dot+';"></span>'+g.name;
      groupDiv.appendChild(titleDiv);
      var grid=document.createElement('div'); grid.className='mbti-cards';
      g.types.forEach(function(item){
        var isSelected=selectedMbti.indexOf(item.t)>=0;
        var card=document.createElement('div');
        card.className='mbti-card '+g.cls+(isSelected?' selected':'');
        card.dataset.type=item.t;
        card.innerHTML='<div class="mbti-card-name">'+item.t+'</div><div class="mbti-card-desc">'+item.d+'</div><span class="mbti-card-check">'+(isSelected?'●':'○')+'</span>';
        card.addEventListener('click',function(){
          var type=this.dataset.type, idx=selectedMbti.indexOf(type);
          if(idx>=0){ selectedMbti.splice(idx,1); this.classList.remove('selected'); this.querySelector('.mbti-card-check').textContent='○'; }
          else{ selectedMbti.push(type); this.classList.add('selected'); this.querySelector('.mbti-card-check').textContent='●'; }
        });
        grid.appendChild(card);
      });
      groupDiv.appendChild(grid); wrap.appendChild(groupDiv);
    });
  }
  function saveCards(){
    var fd=new FormData(); fd.append('jr_id',jrId);
    fd.append('ai_card1_title',((document.getElementById('inp-c1t')||{}).value||'').trim());
    fd.append('ai_card1_desc',((document.getElementById('inp-c1d')||{}).value||'').trim());
    fd.append('ai_card2_title',((document.getElementById('inp-c2t')||{}).value||'').trim());
    fd.append('ai_card2_desc',((document.getElementById('inp-c2d')||{}).value||'').trim());
    fd.append('ai_card3_title',((document.getElementById('inp-c3t')||{}).value||'').trim());
    fd.append('ai_card3_desc',((document.getElementById('inp-c3d')||{}).value||'').trim());
    fd.append('ai_card4_title',((document.getElementById('inp-c4t')||{}).value||'').trim());
    fd.append('ai_card4_desc',((document.getElementById('inp-c4d')||{}).value||'').trim());
    fetch(cardsSaveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(res.ok){
        ['c1t','c2t','c3t','c4t'].forEach(function(id){ var e=document.getElementById('disp-'+id); if(e)e.textContent=((document.getElementById('inp-'+id)||{}).value||'').trim(); });
        ['c1d','c2d','c3d','c4d'].forEach(function(id){ var e=document.getElementById('disp-'+id),inp=document.getElementById('inp-'+id); if(e&&inp)e.innerHTML=(inp.value||'').trim().replace(/\n/g,'<br>'); });
        closeModal('cards'); alert('저장되었습니다.');
      } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){alert('저장 중 오류가 발생했습니다.');});
  }
  function saveBenefits(){
    selectedBenefits=[];
    var checks=document.querySelectorAll('#benefit-checks input:checked'); checks.forEach(function(c){ selectedBenefits.push(c.value); });
    var fd=new FormData(); fd.append('jr_id',jrId); selectedBenefits.forEach(function(v){ fd.append('amenity[]',v); });
    fetch(bulkSaveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(res.ok){
        var wrap=document.getElementById('disp-benefits'); if(wrap)wrap.innerHTML=selectedBenefits.length?selectedBenefits.map(function(b){return '<span class="chip">✅ '+b+'</span>';}).join(''):'<span class="chips-empty">선택된 편의사항이 없습니다</span>';
        closeModal('benefits'); alert('저장되었습니다.');
      } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){alert('저장 중 오류가 발생했습니다.');});
  }
  function saveKeywords(){
    selectedKeywords=[];
    var checks=document.querySelectorAll('#keyword-checks input:checked'); checks.forEach(function(c){ selectedKeywords.push(c.value); });
    var fd=new FormData(); fd.append('jr_id',jrId); selectedKeywords.forEach(function(v){ fd.append('keyword[]',v); });
    fetch(bulkSaveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(res.ok){
        var wrap=document.getElementById('disp-keywords'); if(wrap)wrap.innerHTML=selectedKeywords.length?selectedKeywords.map(function(k){return '<span class="chip chip-kw">🏷️ '+k+'</span>';}).join(''):'<span class="chips-empty">선택된 키워드가 없습니다</span>';
        closeModal('keywords'); alert('저장되었습니다.');
      } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){alert('저장 중 오류가 발생했습니다.');});
  }
  function renderMbtiDisplay(){
    var wrap=document.getElementById('disp-mbti'); if(!wrap)return;
    wrap.innerHTML='';
    var hasAny=false;
    MBTI_GROUPS.forEach(function(g){
      var active=g.types.filter(function(item){return selectedMbti.indexOf(item.t)>=0;});
      if(active.length===0)return;
      hasAny=true;
      var groupDiv=document.createElement('div'); groupDiv.className='mbti-group';
      var titleDiv=document.createElement('div'); titleDiv.className='mbti-group-title';
      titleDiv.innerHTML='<span class="mbti-group-dot" style="background:'+g.dot+';"></span>'+g.name;
      groupDiv.appendChild(titleDiv);
      var grid=document.createElement('div'); grid.className='mbti-cards';
      active.forEach(function(item){
        var card=document.createElement('div'); card.className='mbti-card '+g.cls+' selected';
        card.innerHTML='<div class="mbti-card-name">'+item.t+'</div><div class="mbti-card-desc">'+item.d+'</div><span class="mbti-card-check">●</span>';
        grid.appendChild(card);
      });
      groupDiv.appendChild(grid); wrap.appendChild(groupDiv);
    });
    if(!hasAny)wrap.innerHTML='<div style="padding:14px 0;color:#ccc;font-size:12px;">선택된 MBTI가 없습니다</div>';
  }
  function saveMbti(){
    selectedMbti=[];
    document.querySelectorAll('#mbti-checks .mbti-card.selected').forEach(function(c){ selectedMbti.push(c.dataset.type); });
    var fd=new FormData(); fd.append('jr_id',jrId); selectedMbti.forEach(function(v){ fd.append('mbti_prefer[]',v); });
    fetch(bulkSaveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(res.ok){ renderMbtiDisplay(); closeModal('mbti'); alert('저장되었습니다.'); } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){alert('저장 중 오류가 발생했습니다.');});
  }

  function saveSection(modalId,sectionKey,inpId,dispId){
    var v=((document.getElementById(inpId)||{}).value||'').trim();
    var fd=new FormData(); fd.append('jr_id',jrId); fd.append('section_key',sectionKey); fd.append('value',v);
    fetch(saveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(res.ok){ var disp=document.getElementById(dispId); if(disp)disp.innerHTML=v.replace(/\n/g,'<br>'); closeModal(modalId); alert('저장되었습니다.'); } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){alert('저장 중 오류가 발생했습니다.');});
  }

  document.querySelectorAll('.jobs-ai-section').forEach(function(block){
    var ta=block.querySelector('.jobs-ai-edit-ta');
    var btnSave=block.querySelector('.btn-save-ai');
    if(!ta||!btnSave)return;
    var sectionKey=block.getAttribute('data-section');
    if(!sectionKey)return;
    btnSave.onclick=function(){
      var v=ta.value; btnSave.disabled=true;
      var fd=new FormData(); fd.append('jr_id',jrId); fd.append('section_key',sectionKey); fd.append('value',v);
      fetch(saveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
        btnSave.disabled=false; if(res.ok)alert('저장되었습니다.'); else alert(res.msg||'저장에 실패했습니다.');
      }).catch(function(){btnSave.disabled=false;alert('저장 중 오류가 발생했습니다.');});
    };
  });

  /* ═══ 썸네일 생성 ═══ */
  var _thumbGrads = <?php
    $all_grads = $gradients;
    $all_grads['P1'] = 'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)';
    $all_grads['P2'] = 'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)';
    $all_grads['P3'] = 'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)';
    $all_grads['P4'] = 'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)';
    echo json_encode($all_grads, JSON_UNESCAPED_UNICODE);
  ?>;
  var _thumbSelected = '<?php echo addslashes($saved_grad ?: "1"); ?>';
  var _thumbIcon = '<?php echo addslashes($thumb_icon); ?>';
  var _thumbMotion = '<?php echo addslashes($thumb_motion); ?>';
  var _thumbWave = <?php echo $thumb_wave ? 'true' : 'false'; ?>;
  var _thumbTextColor = '<?php echo addslashes($thumb_text_color); ?>';
  var _thumbBorder = '<?php echo addslashes($thumb_border); ?>';

  function _applyBannerBg(){
    var banner = document.getElementById('tg-pv-banner');
    if(!banner || !_thumbGrads[_thumbSelected]) return;
    var g = _thumbGrads[_thumbSelected];
    banner.classList.remove('carbon-bg');
    if(_thumbWave){
      var m = g.match(/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/g);
      if(m && m.length >= 2){
        var c1 = m[0], c2 = m[1], c3 = m.length >= 3 ? m[2] : m[0];
        banner.style.background = 'linear-gradient(135deg,'+c1+','+c2+','+c3+','+c1+','+c2+')';
        banner.style.backgroundSize = '400% 400%';
      } else {
        banner.style.background = g;
        banner.style.backgroundSize = '400% 400%';
      }
      banner.classList.add('pv-wave-active');
    } else {
      banner.style.background = g;
      banner.style.backgroundSize = '';
      banner.classList.remove('pv-wave-active');
      if(_thumbSelected === 'P3') banner.classList.add('carbon-bg');
    }
  }
  function _applyBorder(){
    var card = document.getElementById('tg-pv-card');
    if(!card) return;
    card.style.border = 'none';
    card.style.outline = 'none';
    card.style.outlineOffset = '';
    var borders = {gold:'#FFD700',pink:'#FF1B6B',charcoal:'#3a3a3a',royalblue:'#4169E1',royalpurple:'#7B2FBE'};
    if(borders[_thumbBorder]){
      card.style.boxShadow = 'inset 0 0 0 2px '+borders[_thumbBorder]+', 0 0 0 2px '+borders[_thumbBorder]+', 0 6px 24px rgba(0,0,0,.18)';
    } else {
      card.style.boxShadow = 'inset 0 0 0 0.75px #f0e0e8, 0 0 0 0.75px #f0e0e8';
    }
  }
  window.selectGrad = function(btn){
    document.querySelectorAll('.color-swatch').forEach(function(b){b.classList.remove('selected');});
    document.querySelectorAll('.premium-swatch').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbSelected = btn.getAttribute('data-grad');
    _applyBannerBg();
    var isPremium = _thumbSelected && _thumbSelected.charAt(0) === 'P';
    _updateOptionPrice('tg-premium-price','premium','프리미엄', isPremium);
    calcThumbTotal();
  };
  window.updatePreview = function(){
    var t = document.getElementById('tg-title');
    var x = document.getElementById('tg-text');
    var pt = document.getElementById('tpc-title');
    var px = document.getElementById('tpc-text');
    var tv = t.value || '업소명';
    var xv = x.value || '';
    if(pt){
      var tLen = Array.from(tv).length;
      var titleSize = tLen <= 6 ? '14px' : tLen <= 10 ? '13px' : tLen <= 14 ? '12px' : '11px';
      pt.childNodes[0].textContent = tv;
      pt.style.fontSize = titleSize;
    }
    if(px){
      px.textContent = xv;
      var xLen = Array.from(xv).length;
      px.style.fontSize = xLen <= 15 ? '12px' : xLen <= 25 ? '11px' : xLen <= 40 ? '10px' : '9px';
    }
  };
  window.countChar = function(el, spanId, max){
    var len = Array.from(el.value).length;
    var sp = document.getElementById(spanId);
    if(sp) sp.textContent = len;
  };
  window.selectTextColor = function(btn){
    document.querySelectorAll('#tg-textcolor-grid .txt-color-btn').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbTextColor = btn.getAttribute('data-tcolor') || 'rgb(255,255,255)';
    if(window._thumbState) window._thumbState.textColor = _thumbTextColor;
    var pt = document.getElementById('tpc-title');
    if(pt) pt.style.color = _thumbTextColor;
  };
  var _optRemainingDays = <?php echo (int)$_opt_remaining_days; ?>;
  var _optDailyRates = {
    premium: <?php echo (int)$_opt_daily_rates['premium']; ?>,
    badge:   <?php echo (int)$_opt_daily_rates['badge']; ?>,
    motion:  <?php echo (int)$_opt_daily_rates['motion']; ?>,
    wave:    <?php echo (int)$_opt_daily_rates['wave']; ?>,
    border:  <?php echo (int)$_opt_daily_rates['border']; ?>
  };
  var _optEndDate = '<?php echo $_opt_end_date; ?>';

  function _optPriceHtml(type, label, days){
    if(days <= 0) return '<span style="color:#999;font-size:11px;">광고 기간이 만료되었습니다</span>';
    var cost = days * _optDailyRates[type];
    return '<span style="font-size:11px;color:#888;">📅 잔여 <b style="color:#FF1B6B">'+days+'일</b> × '+_optDailyRates[type].toLocaleString()+'원 = </span><b style="color:#FF1B6B;font-size:13px">'+cost.toLocaleString()+'원</b>';
  }

  function _updateOptionPrice(id, type, label, visible){
    var el = document.getElementById(id);
    if(!el) return;
    el.style.display = visible ? '' : 'none';
    if(visible) el.innerHTML = _optPriceHtml(type, label, _optRemainingDays);
  }

  window.selectIcon = function(btn){
    document.querySelectorAll('#tg-icon-grid .badge-opt').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbIcon = btn.getAttribute('data-icon') || '';
    var pvIcon = document.getElementById('tg-pv-icon');
    if(pvIcon){
      if(_thumbIcon){
        pvIcon.style.display = '';
        pvIcon.style.background = btn.getAttribute('data-icon-bg') || '#ccc';
        pvIcon.textContent = btn.getAttribute('data-icon-label') || '';
      } else {
        pvIcon.style.display = 'none';
      }
    }
    _updateOptionPrice('tg-badge-price','badge', btn.getAttribute('data-icon-label')||'뱃지', !!_thumbIcon);
    calcThumbTotal();
  };
  window.selectMotion = function(btn){
    document.querySelectorAll('#tg-motion-grid .motion-btn').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbMotion = btn.getAttribute('data-motion') || '';
    var pt = document.getElementById('tpc-title');
    if(pt) pt.className = _thumbMotion ? 'pv-motion-' + _thumbMotion : '';
    _updateOptionPrice('tg-motion-price','motion','모션', !!_thumbMotion);
    calcThumbTotal();
  };
  window.toggleWave = function(checked){
    _thumbWave = checked;
    _applyBannerBg();
    _updateOptionPrice('tg-wave-price','wave','웨이브', checked);
    calcThumbTotal();
  };
  window.selectBorder = function(btn){
    document.querySelectorAll('#tg-border-grid .border-btn').forEach(function(b){b.classList.remove('selected');});
    btn.classList.add('selected');
    _thumbBorder = btn.getAttribute('data-border') || '';
    _applyBorder();
    _updateOptionPrice('tg-border-price','border','테두리', !!_thumbBorder);
    calcThumbTotal();
  };
  window.calcThumbTotal = function(){
    var items = [];
    var total = 0;
    var days = _optRemainingDays;
    var motionNames = {'shimmer':'글씨 확대','soft-blink':'소프트 블링크','glow':'글로우 글씨','bounce':'바운스'};
    var borderNames = {'gold':'골드 테두리','pink':'핫핑크 테두리','charcoal':'차콜 테두리','royalblue':'로얄블루 테두리','royalpurple':'로얄퍼플 테두리'};
    var premNames = {'P1':'메탈릭골드','P2':'메탈릭실버','P3':'카본','P4':'오로라'};

    if(_thumbIcon && days > 0){
      var ic = document.querySelector('#tg-icon-grid .badge-opt.selected');
      var label = ic ? (ic.getAttribute('data-icon-label')||'뱃지') : '뱃지';
      var cost = days * _optDailyRates.badge;
      items.push({name: '💖 '+label+' ('+days+'일)', price: cost});
      total += cost;
    }
    if(_thumbMotion && days > 0){
      var mLabel = motionNames[_thumbMotion] || '모션';
      var cost2 = days * _optDailyRates.motion;
      items.push({name: '✨ '+mLabel+' ('+days+'일)', price: cost2});
      total += cost2;
    }
    if(_thumbWave && days > 0){
      var cost3 = days * _optDailyRates.wave;
      items.push({name: '🌊 배경 웨이브 ('+days+'일)', price: cost3});
      total += cost3;
    }
    if(_thumbBorder && days > 0){
      var bLabel = borderNames[_thumbBorder] || '테두리';
      var cost4 = days * _optDailyRates.border;
      items.push({name: '🖼️ '+bLabel+' ('+days+'일)', price: cost4});
      total += cost4;
    }
    var isPremium = _thumbSelected && (String(_thumbSelected).charAt(0)==='P');
    if(isPremium && days > 0){
      var pLabel = premNames[_thumbSelected] || '프리미엄 컬러';
      var cost5 = days * _optDailyRates.premium;
      items.push({name: '🎨 '+pLabel+' ('+days+'일)', price: cost5});
      total += cost5;
    }
    _updateOptionPrice('tg-premium-price','premium','프리미엄', isPremium);

    var amtEl = document.getElementById('tg-total-amount');
    if(amtEl) amtEl.textContent = total.toLocaleString('ko-KR') + ' 원';
    var listEl = document.getElementById('tg-total-items');
    if(listEl){
      if(items.length===0){
        listEl.innerHTML='<div class="tti-empty">선택된 유료 옵션이 없습니다</div>';
      } else {
        var html='';
        items.forEach(function(it){
          html+='<div class="tti-row"><span class="tti-name">'+it.name+'</span><span class="tti-price">'+it.price.toLocaleString('ko-KR')+'원</span></div>';
        });
        listEl.innerHTML=html;
      }
    }
  };
  window.saveThumb = function(){
    var btn = document.getElementById('tg-save-btn');
    if(btn) btn.disabled = true;
    var fd = new FormData();
    fd.append('jr_id', jrId);
    fd.append('thumb_gradient', _thumbSelected || '1');
    fd.append('thumb_title', (document.getElementById('tg-title') || {}).value || '');
    fd.append('thumb_text', (document.getElementById('tg-text') || {}).value || '');
    fd.append('thumb_icon', _thumbIcon || '');
    fd.append('thumb_motion', _thumbMotion || '');
    fd.append('thumb_wave', _thumbWave ? '1' : '0');
    fd.append('thumb_text_color', _thumbTextColor || 'rgb(255,255,255)');
    fd.append('thumb_border', _thumbBorder || '');
    fetch(basicSaveUrl, {method:'POST', body:fd, credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(res){
      if(btn) btn.disabled = false;
      if(res.ok) alert('썸네일이 저장되었습니다.');
      else alert(res.msg || '저장에 실패했습니다.');
    })
    .catch(function(e){
      if(btn) btn.disabled = false;
      alert('저장 중 오류가 발생했습니다: ' + (e.message || ''));
    });
  };
  _applyBorder();
  if(_thumbIcon) _updateOptionPrice('tg-badge-price','badge','뱃지', true);
  if(_thumbMotion) _updateOptionPrice('tg-motion-price','motion','모션', true);
  if(_thumbWave) _updateOptionPrice('tg-wave-price','wave','웨이브', true);
  if(_thumbBorder) _updateOptionPrice('tg-border-price','border','테두리', true);
  var isPremInit = _thumbSelected && String(_thumbSelected).charAt(0)==='P';
  if(isPremInit) _updateOptionPrice('tg-premium-price','premium','프리미엄', true);
  calcThumbTotal();

  var _currentTheme = <?php echo json_encode($saved_theme); ?>;
  function setTheme(theme){
    var article = document.getElementById('bo_v');
    if(!article) return;
    article.classList.remove('theme-black','theme-blue');
    if(theme !== 'pink') article.classList.add('theme-'+theme);
    _currentTheme = theme;
    document.querySelectorAll('.ts-btn[data-theme]').forEach(function(b){b.classList.remove('active');});
    var btn=document.querySelector('.ts-'+theme);
    if(btn)btn.classList.add('active');
  }
  document.querySelectorAll('#theme-switcher .ts-btn[data-theme]').forEach(function(btn){ btn.addEventListener('click',function(){ setTheme(this.getAttribute('data-theme')||'pink'); }); });

  window.saveThemeChoice = function(){
    var btn = document.getElementById('btn-save-theme');
    if(btn){ btn.textContent='저장중...'; btn.disabled=true; }
    var fd = new FormData();
    fd.append('jr_id', jrId);
    fd.append('theme', _currentTheme);
    fetch((<?php echo json_encode($jobs_base_url); ?>)+'/jobs_theme_save.php',{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(d){
      if(btn){ btn.textContent = d.success ? '✅ 저장완료' : '❌ 실패'; btn.disabled=false; }
      setTimeout(function(){ if(btn) btn.textContent='💾 테마저장'; }, 2000);
    })
    .catch(function(){
      if(btn){ btn.textContent='❌ 오류'; btn.disabled=false; }
      setTimeout(function(){ if(btn) btn.textContent='💾 테마저장'; }, 2000);
    });
  };

  /* ═══════════════════════════════════════
     🖼️ 업소 이미지 슬라이더
  ═══════════════════════════════════════ */
  var sliderImages = <?php echo json_encode(array_pad(isset($data['jr_images'])&&is_array($data['jr_images'])?$data['jr_images']:array(), 5, array('url'=>'','caption'=>''))); ?>;
  while(sliderImages.length<5) sliderImages.push({url:'',caption:''});
  var sliderCurrent = 0;

  function renderSlider(){
    var container=document.getElementById('slider-container');
    if(!container)return;
    var activeSlides=sliderImages.filter(function(s){return s.url!=='';});
    if(activeSlides.length===0){
      container.innerHTML='<div class="slider-empty"><span class="slider-empty-icon">📷</span>업소 이미지를 등록해주세요.<br><span style="font-size:11px;color:#ddd;margin-top:4px;display:block;">수정 버튼을 눌러 이미지를 추가하세요.</span></div>';
      sliderCurrent=0; return;
    }
    if(sliderCurrent>=activeSlides.length)sliderCurrent=0;
    container.innerHTML='';
    var wrap=document.createElement('div'); wrap.className='slider-wrap';
    if(activeSlides.length>1){var counter=document.createElement('div');counter.className='slider-counter';counter.textContent=(sliderCurrent+1)+' / '+activeSlides.length;wrap.appendChild(counter);}
    var track=document.createElement('div');track.className='slider-track';track.id='slider-track';track.style.transform='translateX(-'+(sliderCurrent*100)+'%)';
    activeSlides.forEach(function(s,i){
      var slide=document.createElement('div');slide.className='slide';
      slide.setAttribute('onclick',"openFullImage('"+s.url.replace(/'/g,"\\'")+"')");
      var img=document.createElement('img');img.src=s.url;img.alt='업소이미지'+(i+1);
      img.addEventListener('error',function(){
        var ph=document.createElement('div');ph.className='slide-placeholder';
        ph.innerHTML='<span class="slide-placeholder-icon">🖼️</span><span class="slide-placeholder-text">이미지를 불러올 수 없습니다</span>';
        img.parentNode.replaceChild(ph,img);
      });
      slide.appendChild(img);
      if(s.caption){var cap=document.createElement('div');cap.className='slide-caption';cap.textContent=s.caption;slide.appendChild(cap);}
      track.appendChild(slide);
    });
    wrap.appendChild(track);
    if(activeSlides.length>1){
      var prev=document.createElement('button');prev.type='button';prev.className='slider-arrow prev';prev.textContent='‹';prev.addEventListener('click',function(e){e.stopPropagation();prevSlide();});
      var next=document.createElement('button');next.type='button';next.className='slider-arrow next';next.textContent='›';next.addEventListener('click',function(e){e.stopPropagation();nextSlide();});
      wrap.appendChild(prev);wrap.appendChild(next);
    }
    container.appendChild(wrap);
    if(activeSlides.length>1){
      var indicators=document.createElement('div');indicators.className='slider-indicators';
      activeSlides.forEach(function(_,i){
        var dot=document.createElement('button');dot.type='button';dot.className='s-dot'+(i===sliderCurrent?' active':'');
        (function(idx){dot.addEventListener('click',function(){goSlide(idx);});})(i);
        indicators.appendChild(dot);
      });
      container.appendChild(indicators);
    }
  }
  function goSlide(idx){var activeSlides=sliderImages.filter(function(s){return s.url!=='';});if(idx<0||idx>=activeSlides.length)return;sliderCurrent=idx;renderSlider();}
  function prevSlide(){var activeSlides=sliderImages.filter(function(s){return s.url!=='';});sliderCurrent=(sliderCurrent-1+activeSlides.length)%activeSlides.length;renderSlider();}
  function nextSlide(){var activeSlides=sliderImages.filter(function(s){return s.url!=='';});sliderCurrent=(sliderCurrent+1)%activeSlides.length;renderSlider();}

  function openFullImage(url){
    var overlay=document.getElementById('img-fullview');
    var img=document.getElementById('img-fullview-img');
    if(overlay&&img){img.src=url;overlay.classList.add('is-open');document.body.style.overflow='hidden';}
  }
  function closeFullImage(){
    var overlay=document.getElementById('img-fullview');
    if(overlay){overlay.classList.remove('is-open');document.body.style.overflow='';}
  }

  function buildImgModalList(){
    var list=document.getElementById('img-modal-list');if(!list)return;
    list.innerHTML='';
    for(var i=0;i<5;i++){
      var s=sliderImages[i]||{url:'',caption:''};
      var previewHtml=s.url
        ?'<img src="'+s.url+'" class="img-preview-thumb" id="img-thumb-'+i+'" alt="미리보기">'
        :'<div style="width:80px;height:56px;background:#f0f0f0;border-radius:8px;border:1.5px dashed #ddd;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:8px;" id="img-thumb-'+i+'">📷</div>';
      var item=document.createElement('div');item.className='img-modal-item';item.id='img-item-'+i;
      item.innerHTML='<div class="img-modal-item-title">📸 이미지 '+(i+1)+(s.url?' <span style="color:#FF1B6B;font-size:10px;">● 등록됨</span>':'')+'</div>'
        +previewHtml
        +'<div class="img-file-row">'
        +'<label class="img-file-label"><input type="file" accept="image/*" data-idx="'+i+'" onchange="onImgFileChange(this)">📁 파일 선택</label>'
        +'<span class="img-file-name" id="img-fname-'+i+'">'+(s.url?'이미지 등록됨':'선택된 파일 없음')+'</span>'
        +(s.url?'<button type="button" class="img-remove-btn" onclick="removeImg('+i+')">✕ 제거</button>':'')
        +'</div>'
        +'<input type="text" class="img-caption-input" id="img-cap-'+i+'" placeholder="이미지 설명 (선택사항)" value="'+(s.caption||'')+'">';
      list.appendChild(item);
    }
  }
  var pendingFiles=[];
  function onImgFileChange(input){
    var idx=parseInt(input.dataset.idx);
    var file=input.files[0]; if(!file)return;
    var url=URL.createObjectURL(file);
    sliderImages[idx].url=url;
    sliderImages[idx]._file=file;
    if(!pendingFiles[idx])pendingFiles[idx]=null;
    pendingFiles[idx]=file;
    var thumb=document.getElementById('img-thumb-'+idx);
    if(thumb){var img=document.createElement('img');img.src=url;img.className='img-preview-thumb';img.id='img-thumb-'+idx;img.alt='미리보기';thumb.parentNode.replaceChild(img,thumb);}
    var fname=document.getElementById('img-fname-'+idx);if(fname)fname.textContent=file.name;
  }
  function removeImg(idx){
    sliderImages[idx]={url:'',caption:'',_removed:true};
    pendingFiles[idx]=null;
    buildImgModalList();
  }
  function saveImgSlider(){
    for(var i=0;i<5;i++){var capEl=document.getElementById('img-cap-'+i);if(capEl)sliderImages[i].caption=capEl.value.trim();}
    var fd=new FormData();fd.append('jr_id',jrId);
    var hasNewFiles=false;
    for(var i=0;i<5;i++){
      if(pendingFiles[i]){fd.append('img_file_'+i,pendingFiles[i]);hasNewFiles=true;}
      fd.append('img_caption_'+i,sliderImages[i].caption||'');
      fd.append('img_url_'+i,sliderImages[i]._removed?'':sliderImages[i].url||'');
      fd.append('img_removed_'+i,sliderImages[i]._removed?'1':'0');
    }
    var btn=document.querySelector('#modal-imgslider .btn-save');if(btn){btn.disabled=true;btn.textContent='저장 중...';}
    fetch(imgSaveUrl,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
      if(btn){btn.disabled=false;btn.textContent='저장';}
      if(res.ok){
        if(res.images){sliderImages=res.images;while(sliderImages.length<5)sliderImages.push({url:'',caption:''});}
        pendingFiles=[];
        sliderCurrent=0;renderSlider();closeModal('imgslider');alert('이미지가 저장되었습니다.');
      } else alert(res.msg||'저장에 실패했습니다.');
    }).catch(function(){if(btn){btn.disabled=false;btn.textContent='저장';}alert('이미지 저장 중 오류가 발생했습니다.');});
  }

  var _origOpenModal=openModal;
  openModal=function(id){
    if(id==='imgslider'){pendingFiles=[];buildImgModalList();document.getElementById('modal-imgslider').classList.add('is-open');document.body.style.overflow='hidden';return;}
    _origOpenModal(id);
  };

  window.openModal=openModal; window.closeModal=closeModal;
  window.saveBasic=saveBasic; window.saveSection=saveSection; window.saveCards=saveCards;
  window.saveBenefits=saveBenefits; window.saveKeywords=saveKeywords; window.saveMbti=saveMbti;
  window.deployPost=deployPost; window.filterRegDetail=filterRegDetail;
  window.openFullImage=openFullImage; window.closeFullImage=closeFullImage;
  window.onImgFileChange=onImgFileChange; window.removeImg=removeImg;
  window.saveImgSlider=saveImgSlider;
  window.goSlide=goSlide; window.prevSlide=prevSlide; window.nextSlide=nextSlide;

  window.doJobsGood=function(){
    var btn=document.getElementById('jobs-good-btn');
    if(!btn)return;
    btn.disabled=true;btn.style.opacity='.6';
    var fd=new FormData();fd.append('jr_id',jrId);
    fetch('<?php echo $jobs_good_url; ?>',{method:'POST',body:fd,credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(res){
      btn.disabled=false;btn.style.opacity='1';
      if(res.ok){
        var cnt=document.getElementById('jobs-good-count');
        if(cnt)cnt.textContent=res.total.toLocaleString();
        var info=document.getElementById('jobs-good-info');
        if(info)info.textContent='오늘 '+res.used+'/'+res.limit+' 사용';
        btn.style.transform='scale(1.15)';
        setTimeout(function(){btn.style.transform='scale(1)';},200);
      } else {
        alert(res.msg||'추천에 실패했습니다.');
      }
    })
    .catch(function(){btn.disabled=false;btn.style.opacity='1';alert('추천 처리 중 오류가 발생했습니다.');});
  };
  filterRegDetail('inp-reg1','inp-regd1');
  filterRegDetail('inp-reg2','inp-regd2');
  filterRegDetail('inp-reg3','inp-regd3');
})();
</script>
<script>
if(typeof window.saveThumb !== 'function'){
  window.saveThumb = function(){
    var btn = document.getElementById('tg-save-btn');
    if(btn) btn.disabled = true;
    var gradBtn = document.querySelector('.color-swatch.selected');
    var iconBtn = document.querySelector('.badge-opt.selected');
    var motionBtn = document.querySelector('.motion-btn.selected');
    var waveChk = document.getElementById('tg-wave-chk');
    var txtBtn = document.querySelector('.txt-color-btn.selected');
    var borderBtn = document.querySelector('.border-btn.selected');
    var fd = new FormData();
    fd.append('jr_id', '<?php echo (int)$jr_id; ?>');
    fd.append('thumb_gradient', gradBtn ? gradBtn.getAttribute('data-grad') : '1');
    fd.append('thumb_title', (document.getElementById('tg-title')||{}).value||'');
    fd.append('thumb_text', (document.getElementById('tg-text')||{}).value||'');
    fd.append('thumb_icon', iconBtn ? (iconBtn.getAttribute('data-icon')||'') : '');
    fd.append('thumb_motion', motionBtn ? (motionBtn.getAttribute('data-motion')||'') : '');
    fd.append('thumb_wave', waveChk && waveChk.checked ? '1' : '0');
    fd.append('thumb_text_color', txtBtn ? (txtBtn.getAttribute('data-tcolor')||'rgb(255,255,255)') : 'rgb(255,255,255)');
    fd.append('thumb_border', borderBtn ? (borderBtn.getAttribute('data-border')||'') : '');
    fetch('<?php echo $jobs_basic_save_url; ?>', {method:'POST',body:fd,credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(res){ if(btn) btn.disabled=false; if(res.ok) alert('썸네일이 저장되었습니다.'); else alert(res.msg||'저장에 실패했습니다.'); })
    .catch(function(e){ if(btn) btn.disabled=false; alert('저장 중 오류: '+(e.message||'')); });
  };
}
</script>

<div id="biz-info-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.45);z-index:99999;align-items:center;justify-content:center;" onclick="closeBizInfoPopup()">
  <div class="biz-info-popup" onclick="event.stopPropagation()">
    <div class="biz-popup-header">
      <span>🏢 기업정보</span>
      <button type="button" onclick="closeBizInfoPopup()" class="biz-popup-close">&times;</button>
    </div>
    <div class="biz-popup-body">
      <div class="biz-popup-row">
        <span class="biz-popup-label">회사명</span>
        <span class="biz-popup-value"><?php echo htmlspecialchars($_biz_company ?: '—'); ?></span>
      </div>
      <div class="biz-popup-row">
        <span class="biz-popup-label">대표자명</span>
        <span class="biz-popup-value"><?php echo htmlspecialchars($_biz_ceo ?: '—'); ?></span>
      </div>
      <div class="biz-popup-row">
        <span class="biz-popup-label">회사주소</span>
        <span class="biz-popup-value"><?php echo htmlspecialchars($_biz_addr ?: '—'); ?></span>
      </div>
    </div>
    <div class="biz-popup-footer">
      <button type="button" onclick="closeBizInfoPopup()" class="biz-popup-ok">확인</button>
    </div>
  </div>
</div>
<style>
.btn-biz-info {
  display:inline-block;
  margin-left:8px;
  padding:4px 12px;
  font-size:12px;
  font-weight:700;
  color:#FF1B6B;
  background:#fff;
  border:1.5px solid #FF1B6B;
  border-radius:6px;
  cursor:pointer;
  vertical-align:middle;
  transition:all .2s;
}
.btn-biz-info:hover {
  background:linear-gradient(135deg,#FF1B6B,#C90050);
  color:#fff;
}
.biz-info-popup {
  background:#fff;
  border-radius:16px;
  width:380px;
  max-width:90vw;
  box-shadow:0 8px 40px rgba(0,0,0,.18);
  overflow:hidden;
  animation:bizPopIn .25s ease;
}
@keyframes bizPopIn {
  from { transform:scale(.9); opacity:0; }
  to   { transform:scale(1);  opacity:1; }
}
.biz-popup-header {
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:16px 20px;
  background:linear-gradient(135deg,#FF1B6B,#C90050);
  color:#fff;
  font-size:16px;
  font-weight:800;
}
.biz-popup-close {
  background:none;
  border:none;
  color:#fff;
  font-size:24px;
  cursor:pointer;
  line-height:1;
  padding:0 4px;
}
.biz-popup-body {
  padding:20px;
}
.biz-popup-row {
  display:flex;
  align-items:flex-start;
  padding:12px 0;
  border-bottom:1px solid #f0f0f0;
}
.biz-popup-row:last-child {
  border-bottom:none;
}
.biz-popup-label {
  flex:0 0 80px;
  font-size:13px;
  font-weight:700;
  color:#888;
}
.biz-popup-value {
  flex:1;
  font-size:14px;
  font-weight:600;
  color:#222;
  word-break:keep-all;
}
.biz-popup-footer {
  padding:0 20px 16px;
  text-align:center;
}
.biz-popup-ok {
  display:inline-block;
  padding:10px 40px;
  background:linear-gradient(135deg,#FF1B6B,#C90050);
  color:#fff;
  border:none;
  border-radius:8px;
  font-size:14px;
  font-weight:700;
  cursor:pointer;
  transition:opacity .2s;
}
.biz-popup-ok:hover { opacity:.85; }
</style>
<script>
function openBizInfoPopup() {
  var ov = document.getElementById('biz-info-overlay');
  if (ov) { ov.style.display = 'flex'; }
}
function closeBizInfoPopup() {
  var ov = document.getElementById('biz-info-overlay');
  if (ov) { ov.style.display = 'none'; }
}
</script>
