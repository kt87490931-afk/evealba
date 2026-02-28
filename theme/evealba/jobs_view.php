<?php
if (!defined('_GNUBOARD_')) exit;

$jr_id = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0;

$_jv_row = null;
$_jv_is_owner = false;
$_jv_data = array();
if ($jr_id) {
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if ($tb_check && sql_num_rows($tb_check)) {
        $_jv_row = sql_fetch("SELECT * FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
    }
    if ($_jv_row && $is_member && $member['mb_id'] === $_jv_row['mb_id']) {
        $_jv_is_owner = true;
    }
    if ($_jv_row && $_jv_row['jr_data']) {
        $_jv_data = json_decode($_jv_row['jr_data'], true);
        if (!is_array($_jv_data)) $_jv_data = array();
    }
}

$_jv_nick = isset($_jv_data['job_nickname']) ? trim($_jv_data['job_nickname']) : ($_jv_row ? $_jv_row['jr_nickname'] : '');
$_jv_comp = isset($_jv_data['job_company']) ? trim($_jv_data['job_company']) : ($_jv_row ? $_jv_row['jr_company'] : '');
$_jv_display = $_jv_nick ?: $_jv_comp ?: '';

if (!isset($ev_regions) || !$ev_regions) {
    @include_once(G5_PATH.'/lib/ev_region_fallback.inc.php');
    if (isset($ev_regions_fallback)) $ev_regions = $ev_regions_fallback;
    if (isset($ev_region_details_fallback)) $ev_region_details = $ev_region_details_fallback;
}
if (!isset($ev_regions)) $ev_regions = array();
if (!isset($ev_region_details)) $ev_region_details = array();

$_reg_map = array();
foreach ($ev_regions as $_r) $_reg_map[$_r['er_id']] = $_r['er_name'];
$_regd_map = array();
foreach ($ev_region_details as $_rd) $_regd_map[$_rd['erd_id']] = $_rd['erd_name'];

$_jv_r1 = isset($_jv_data['job_work_region_1']) ? trim($_jv_data['job_work_region_1']) : '';
$_jv_rd1 = isset($_jv_data['job_work_region_detail_1']) ? trim($_jv_data['job_work_region_detail_1']) : '';
$_jv_region = '';
if ($_jv_r1) {
    $_jv_region = isset($_reg_map[(int)$_jv_r1]) ? $_reg_map[(int)$_jv_r1] : $_jv_r1;
    if ($_jv_rd1) $_jv_region .= ' ' . (isset($_regd_map[(int)$_jv_rd1]) ? $_regd_map[(int)$_jv_rd1] : $_jv_rd1);
}

$_jv_job1 = isset($_jv_data['job_job1']) ? trim($_jv_data['job_job1']) : '';
$_jv_job2 = isset($_jv_data['job_job2']) ? trim($_jv_data['job_job2']) : '';
$_jv_jobtype = trim(implode(' ', array_filter(array($_jv_job1, $_jv_job2))));

$_jv_salary_type = isset($_jv_data['job_salary_type']) ? trim($_jv_data['job_salary_type']) : '';
$_jv_salary_amt = isset($_jv_data['job_salary_amt']) ? trim($_jv_data['job_salary_amt']) : '';

$_jv_ai_intro = isset($_jv_data['ai_intro']) ? trim($_jv_data['ai_intro']) : '';
$_jv_meta_desc = $_jv_ai_intro ? mb_substr(strip_tags($_jv_ai_intro), 0, 150, 'UTF-8') : ($_jv_region . ' ' . $_jv_jobtype . ' 채용정보');

$_jv_title_parts = array_filter(array($_jv_region, $_jv_jobtype, $_jv_display));
$_jv_page_title = $_jv_title_parts ? implode(' ', $_jv_title_parts) . ' - 채용정보' : '채용정보 상세';

$_jv_clean_url = '';
if ($_jv_row) {
    $_slug_region = $_jv_region ? preg_replace('/\s+/', '-', $_jv_region) : '전국';
    $_slug_job = $_jv_jobtype ?: '기타';
    $_slug_name = $_jv_display ?: '채용';
    $_jv_clean_url = '/jobs/' . urlencode($_slug_region) . '/' . urlencode($_slug_job) . '/' . urlencode($_slug_name) . '-' . $jr_id;
}

$g5['title'] = $_jv_page_title . ' | ' . $config['cf_title'];

if ($_jv_is_owner) {
    $jobs_mypage_active = 'ongoing';
    $jobs_breadcrumb_current = '채용정보 상세';
    include_once(G5_THEME_PATH.'/head_jobs_register.php');
} else {
    include_once(G5_THEME_PATH.'/head_jobs.php');
    echo '<style>.left-sidebar{display:none!important}.page-layout{grid-template-columns:1fr!important}</style>';
}
?>

<?php if ($_jv_row) { ?>
<!-- SEO: Dynamic Meta -->
<meta name="description" content="<?php echo htmlspecialchars($_jv_meta_desc); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($_jv_page_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($_jv_meta_desc); ?>">
<meta property="og:type" content="website">
<?php if ($_jv_clean_url) { ?><link rel="canonical" href="http://<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_jv_clean_url); ?>">
<meta property="og:url" content="http://<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_jv_clean_url); ?>"><?php } ?>

<!-- SEO: JSON-LD JobPosting -->
<script type="application/ld+json">
<?php
$_jld = array(
    '@context' => 'https://schema.org',
    '@type' => 'JobPosting',
    'title' => $_jv_display ? $_jv_region . ' ' . $_jv_jobtype . ' ' . $_jv_display : $_jv_page_title,
    'datePosted' => date('Y-m-d', strtotime($_jv_row['jr_datetime'])),
    'description' => $_jv_meta_desc,
    'jobLocation' => array(
        '@type' => 'Place',
        'address' => array(
            '@type' => 'PostalAddress',
            'addressRegion' => $_jv_region ?: '대한민국',
            'addressCountry' => 'KR'
        )
    ),
    'hiringOrganization' => array(
        '@type' => 'Organization',
        'name' => $_jv_display ?: '이브알바'
    )
);
if ($_jv_row['jr_end_date']) {
    $_jld['validThrough'] = $_jv_row['jr_end_date'] . 'T23:59:59+09:00';
}
if ($_jv_salary_type && $_jv_salary_type !== '급여협의') {
    $_jld['baseSalary'] = array(
        '@type' => 'MonetaryAmount',
        'currency' => 'KRW',
        'value' => array(
            '@type' => 'QuantitativeValue',
            'value' => preg_replace('/[^0-9]/', '', $_jv_salary_amt) ?: 0,
            'unitText' => $_jv_salary_type
        )
    );
}
echo json_encode($_jld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
</script>
<?php } ?>

<?php include(G5_THEME_PATH.'/jobs_view_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
