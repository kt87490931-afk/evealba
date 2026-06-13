<?php
/**
 * 급구 마퀴 배너 (시안 marquee-bar)
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_mq_urgent = function_exists('get_jobs_by_type') ? get_jobs_by_type('급구', 30) : array();
$_mq_spans = '';

if (!empty($_mq_urgent)) {
    $_mq_intro_map = array();
    $_mq_ids = array_map(function ($r) { return (int)$r['jr_id']; }, $_mq_urgent);
    if (!empty($_mq_ids) && sql_num_rows(sql_query("SHOW TABLES LIKE 'g5_jobs_ai_content'", false))) {
        $_mq_ids_str = implode(',', array_unique($_mq_ids));
        $_mq_res = sql_query("SELECT jr_id, ai_data FROM g5_jobs_ai_content WHERE jr_id IN ({$_mq_ids_str}) AND is_active = 1 ORDER BY id DESC", false);
        if ($_mq_res) {
            $_mq_seen = array();
            while ($_mq_ar = sql_fetch_array($_mq_res)) {
                if (isset($_mq_seen[$_mq_ar['jr_id']])) continue;
                $_mq_seen[$_mq_ar['jr_id']] = 1;
                $_mq_ad = !empty($_mq_ar['ai_data']) ? @json_decode($_mq_ar['ai_data'], true) : null;
                if (is_array($_mq_ad) && !empty($_mq_ad['ai_intro'])) {
                    $_mq_intro_map[(int)$_mq_ar['jr_id']] = trim(preg_replace('/<[^>]+>/', '', $_mq_ad['ai_intro']));
                }
            }
        }
    }
    foreach ($_mq_urgent as $_mq) {
        $_mq_data = is_string($_mq['jr_data']) ? @json_decode($_mq['jr_data'], true) : (array)$_mq['jr_data'];
        if (!is_array($_mq_data)) $_mq_data = array();
        $_mq_region = function_exists('_jlh_region_name') ? _jlh_region_name($_mq_data['job_work_region_1'] ?? '') : '';
        $_mq_region = $_mq_region ?: '급구';
        $_mq_title = trim($_mq['jr_title'] ?? '');
        $_mq_promo = isset($_mq_intro_map[(int)$_mq['jr_id']]) ? $_mq_intro_map[(int)$_mq['jr_id']] : '';
        if (!$_mq_promo) {
            $_mq_employ = isset($_mq_data['employ_type']) ? trim($_mq_data['employ_type']) : '';
            $_mq_promo = $_mq_title . ($_mq_employ ? ' ' . $_mq_employ : '');
        }
        $_mq_promo = htmlspecialchars(mb_substr(trim($_mq_promo), 0, 40, 'UTF-8'));
        $_mq_spans .= '<span class="urgent-tag">' . htmlspecialchars(mb_substr($_mq_region, 0, 6, 'UTF-8')) . '</span>'
            . $_mq_promo . '&nbsp;&nbsp;';
    }
    if ($_mq_spans !== '') {
        $_mq_spans = '<span class="urgent-tag">급구</span>' . $_mq_spans;
    }
} else {
    $_mq_dummy = array(
        array('급구', '관련OK·밀빵OK·당일면접'),
        array('홍대', '하이퍼블릭 이브 시급 15만원·초보환영'),
        array('신사', '퍼블릭라운지 꼼당 10만원·즉시출근'),
        array('이태원', '이브VIP 하루 100만원 보장'),
        array('압구정', '헤라클럽 시급 20만원·2시간 40만원'),
        array('강남', '클럽마샤 일급 150만원·밀빵OK·당일면접'),
    );
    foreach ($_mq_dummy as $_mq_d) {
        $_mq_spans .= '<span class="urgent-tag">' . htmlspecialchars($_mq_d[0]) . '</span>'
            . htmlspecialchars($_mq_d[1]) . '&nbsp;&nbsp;';
    }
}
?>
<div class="marquee-bar">
  <div class="marquee-inner">
    <span><?php echo $_mq_spans; ?></span>
    <span><?php echo $_mq_spans; ?></span>
  </div>
</div>
