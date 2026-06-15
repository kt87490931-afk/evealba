<?php
/**
 * 흘러가는 줄광고 마퀴 (시안 marquee-bar)
 * — 흘러가는줄광고 옵션 구매 + 점프순, 지역·업체명·제목·급여
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_line_ad_list')) {
    @include_once(G5_PATH . '/extend/jobs_list_helper.php');
}

$_mq_line = function_exists('get_jobs_line_ad_list') ? get_jobs_line_ad_list(30) : array();
$_mq_spans = '';

if (!empty($_mq_line)) {
    foreach ($_mq_line as $_mq) {
        if (function_exists('_jlh_marquee_line_html')) {
            $_mq_spans .= _jlh_marquee_line_html($_mq);
        }
    }
}

if ($_mq_spans === '') {
    $_mq_dummy = array(
        array('강남', '헤라클럽', '시급 20만원', '룸살롱 스텝 모집'),
        array('홍대', '하이퍼블릭 이브', '시급 15만원', '초보환영'),
        array('신사', '퍼블릭라운지', '꼼당 10만원', '즉시출근'),
        array('이태원', '이브VIP', '일급 100만원', '보장'),
        array('압구정', '클럽마샤', '일급 150만원', '밀빵OK'),
    );
    foreach ($_mq_dummy as $_mq_d) {
        $_mq_spans .= '<span class="urgent-tag">' . htmlspecialchars($_mq_d[0]) . '</span>'
            . htmlspecialchars($_mq_d[1] . ' · ' . $_mq_d[2] . ' · ' . $_mq_d[3]) . '&nbsp;&nbsp;';
    }
}
?>
<div class="marquee-bar">
  <div class="marquee-inner">
    <span><?php echo $_mq_spans; ?></span>
    <span><?php echo $_mq_spans; ?></span>
  </div>
</div>
