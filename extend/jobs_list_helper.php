<?php
/**
 * 채용광고 목록 조회 및 카드 렌더링 헬퍼
 * - get_jobs_by_type(): 광고유형별 ongoing 건 조회
 * - render_job_card(): 썸네일 카드 HTML 출력
 * - render_urgency_card(): 급구/추천 텍스트형 카드
 * - render_premium_card(): 프리미엄/스페셜 카드
 */
if (!defined('_GNUBOARD_')) exit;

function _jlh_clean_url($row, $jr_data = null) {
    if (!$jr_data && !empty($row['jr_data'])) {
        $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    }
    if (!is_array($jr_data)) $jr_data = array();
    $jr_id = (int)$row['jr_id'];
    static $reg_map = null, $regd_map = null;
    if ($reg_map === null) {
        $reg_map = array(); $regd_map = array();
        @include_once(G5_PATH.'/lib/ev_region_fallback.inc.php');
        if (isset($ev_regions_fallback)) foreach ($ev_regions_fallback as $r) $reg_map[$r['er_id']] = $r['er_name'];
        if (isset($ev_region_details_fallback)) foreach ($ev_region_details_fallback as $rd) $regd_map[$rd['erd_id']] = $rd['erd_name'];
    }
    $r1 = isset($jr_data['job_work_region_1']) ? trim($jr_data['job_work_region_1']) : '';
    $rd1 = isset($jr_data['job_work_region_detail_1']) ? trim($jr_data['job_work_region_detail_1']) : '';
    $region = $r1 ? (isset($reg_map[(int)$r1]) ? $reg_map[(int)$r1] : $r1) : '전국';
    if ($rd1 && isset($regd_map[(int)$rd1])) $region .= '-' . $regd_map[(int)$rd1];
    $region = str_replace(' ', '-', $region);
    $job1 = isset($jr_data['job_job1']) ? trim($jr_data['job_job1']) : '';
    $jobtype = $job1 ?: '기타';
    $name = $row['jr_nickname'] ?: ($row['jr_company'] ?: '채용');
    $path = '/jobs/' . urlencode($region) . '/' . urlencode($jobtype) . '/' . urlencode($name) . '-' . $jr_id;
    return (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . $path : $path;
}

$_jlh_gradients = array(
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
    'P1' => 'linear-gradient(135deg,#7D5A00,#FFD700,#C8960C,#FFE566,#A67C00)',
    'P2' => 'linear-gradient(135deg,#8e9eab,#c8d6df,#eef2f3,#b0bec5,#78909c)',
    'P3' => 'linear-gradient(135deg,#0d0d12,#18181f,#0d0d12,#18181f,#0d0d12)',
    'P4' => 'linear-gradient(135deg,#a18cd1,#fbc2eb,#a1c4fd,#c2e9fb,#d4a1f5)',
);

function _jlh_get_gradient($key) {
    global $_jlh_gradients;
    if (isset($_jlh_gradients[$key])) return $_jlh_gradients[$key];
    $k = (int)$key;
    if ($k >= 1 && $k <= 20 && isset($_jlh_gradients[$k])) return $_jlh_gradients[$k];
    return $_jlh_gradients[1];
}

/**
 * 광고유형별 ongoing 건 조회
 * @param string $ad_type  jr_ad_labels에 포함된 키워드 (우대, 프리미엄, 스페셜, 급구, 추천, 줄광고, 특수배너)
 * @param int    $limit    최대 건수
 * @param string $region   지역 필터 (optional)
 * @return array
 */
function get_jobs_by_type($ad_type, $limit = 20, $region = '') {
    $ad_type_esc = sql_escape_string($ad_type);
    $where = "jr_status = 'ongoing' AND jr_approved = 1 AND jr_end_date >= CURDATE()";
    $where .= " AND jr_ad_labels LIKE '%{$ad_type_esc}%'";
    if ($region) {
        $region_esc = sql_escape_string($region);
        $where .= " AND (jr_data LIKE '%\"desc_location\":\"%{$region_esc}%' OR jr_nickname LIKE '%{$region_esc}%' OR jr_company LIKE '%{$region_esc}%')";
    }
    $limit = (int)$limit;
    $limit_sql = $limit > 0 ? " LIMIT {$limit}" : '';
    $sql = "SELECT * FROM g5_jobs_register WHERE {$where} ORDER BY IFNULL(jr_jump_datetime, jr_datetime) DESC, jr_id DESC{$limit_sql}";
    $result = sql_query($sql, false);
    $rows = array();
    if ($result) {
        while ($row = sql_fetch_array($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * 우대 카드형 썸네일 렌더링 - 에디터 미리보기와 동일하게
 */
function render_job_card($row) {
    static $icon_map = null;
    if ($icon_map === null) {
        $icon_map = array(
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
    }

    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $grad_key = isset($jr_data['thumb_gradient']) ? $jr_data['thumb_gradient'] : '1';
    $grad = _jlh_get_gradient($grad_key);
    $title = htmlspecialchars($jr_data['thumb_title'] ?? $row['jr_nickname'] ?? $row['jr_company'] ?? '');
    $text = htmlspecialchars($jr_data['thumb_text'] ?? '');
    $text_color = $jr_data['thumb_text_color'] ?? 'rgb(255,255,255)';
    $thumb_icon = isset($jr_data['thumb_icon']) ? trim($jr_data['thumb_icon']) : '';
    $thumb_border = isset($jr_data['thumb_border']) ? trim($jr_data['thumb_border']) : '';
    $thumb_motion = isset($jr_data['thumb_motion']) ? trim($jr_data['thumb_motion']) : '';
    $thumb_wave = !empty($jr_data['thumb_wave']);
    $jr_id = (int)$row['jr_id'];
    $link = _jlh_clean_url($row, $jr_data);

    $r1 = isset($jr_data['job_work_region_1']) ? trim($jr_data['job_work_region_1']) : '';
    $rd1 = isset($jr_data['job_work_region_detail_1']) ? trim($jr_data['job_work_region_detail_1']) : '';
    $region_name = $r1 ? _jlh_region_name($r1) : '';
    $detail_name = $rd1 ? _jlh_region_detail_name($rd1) : '';
    $job1 = isset($jr_data['job_job1']) ? trim($jr_data['job_job1']) : '';
    $loc_detail = trim($detail_name . ' ' . $job1);

    $desc = htmlspecialchars(mb_substr($jr_data['job_title'] ?? $row['jr_title'] ?? '', 0, 30, 'UTF-8'));

    $sal_type = $jr_data['job_salary_type'] ?? '';
    $sal_amt = $jr_data['job_salary_amt'] ?? '';
    if ($sal_type === '급여협의') {
        $wage_disp = '급여협의';
    } elseif ($sal_type && $sal_amt) {
        $wage_disp = $sal_type . ' ' . number_format((int)preg_replace('/[^0-9]/', '', $sal_amt)) . '원';
    } elseif ($sal_type) {
        $wage_disp = $sal_type;
    } else {
        $wage_disp = '급여협의';
    }

    $carbon_class = ($grad_key === 'P3') ? ' carbon-bg' : '';

    $banner_bg = $grad;
    if ($thumb_wave) {
        preg_match_all('/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/', $grad, $m);
        if (!empty($m[0]) && count($m[0]) >= 2) {
            $c1 = $m[0][0]; $c2 = $m[0][1]; $c3 = isset($m[0][2]) ? $m[0][2] : $m[0][0];
            $banner_bg = "linear-gradient(135deg,{$c1},{$c2},{$c3},{$c1},{$c2})";
        }
    }

    $motion_data = $thumb_motion ? ' data-motion="pv-motion-' . $thumb_motion . '"' : '';
    $border_colors = array('gold'=>'#FFD700','pink'=>'#FF1B6B','charcoal'=>'#3a3a3a','royalblue'=>'#4169E1','royalpurple'=>'#7B2FBE');
    $card_style = '';
    if ($thumb_border && isset($border_colors[$thumb_border])) {
        $bc = $border_colors[$thumb_border];
        $card_style = ' style="box-shadow:0 0 0 3px '.$bc.', 0 2px 8px rgba(0,0,0,.10)"';
    }
    $has_anim = ($thumb_wave || $thumb_motion) ? ' data-lazy-anim="1"' : '';

    echo '<div class="job-card"' . $card_style . $has_anim . '>';
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    $wave_data = $thumb_wave ? ' data-wave="animation:wave-shift 6s ease-in-out infinite;background-size:400% 400%;"' : '';
    echo '<div class="job-card-banner' . $carbon_class . '" style="background:' . $banner_bg . ';color:' . $text_color . ';"' . $wave_data . '>';
    echo '<span' . $motion_data . '>' . $title . '<br>' . $text . '</span>';
    echo '</div>';
    if ($thumb_icon && isset($icon_map[$thumb_icon])) {
        $ic = $icon_map[$thumb_icon];
        echo '<div class="pv-icon-badge" style="position:absolute;top:7px;right:7px;font-size:10px;font-weight:900;padding:2px 7px;border-radius:9px;z-index:10;color:#fff;background:' . $ic['bg'] . '">' . $ic['label'] . '</div>';
    }
    echo '<div class="job-card-body">';
    if ($region_name) echo '<div class="job-card-location"><span class="job-loc-badge">' . htmlspecialchars($region_name) . '</span>' . htmlspecialchars($loc_detail) . '</div>';
    if ($desc) echo '<div class="job-desc">' . $desc . '</div>';
    $jr_good_val = isset($row['jr_good']) ? (int)$row['jr_good'] : 0;
    echo '<div class="job-card-footer"><span class="job-wage">' . htmlspecialchars($wage_disp) . '</span>';
    if ($jr_good_val > 0) echo '<span class="job-good-badge">❤️ ' . number_format($jr_good_val) . '</span>';
    echo '</div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
}

/**
 * 프리미엄/스페셜 카드 렌더링 - 에디터 미리보기와 동일하게
 */
function render_premium_card($row, $card_class = 'premium-card') {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $grad_key = isset($jr_data['thumb_gradient']) ? $jr_data['thumb_gradient'] : '1';
    $grad = _jlh_get_gradient($grad_key);
    $title = htmlspecialchars($jr_data['thumb_title'] ?? $row['jr_nickname'] ?? $row['jr_company'] ?? '');
    $text = htmlspecialchars($jr_data['thumb_text'] ?? '');
    $text_color = $jr_data['thumb_text_color'] ?? 'rgb(255,255,255)';
    $thumb_border = isset($jr_data['thumb_border']) ? trim($jr_data['thumb_border']) : '';
    $thumb_wave = !empty($jr_data['thumb_wave']);
    $thumb_motion = isset($jr_data['thumb_motion']) ? trim($jr_data['thumb_motion']) : '';
    $jr_id = (int)$row['jr_id'];
    $link = _jlh_clean_url($row, $jr_data);
    $carbon_class = ($grad_key === 'P3') ? ' carbon-bg' : '';

    $r1 = isset($jr_data['job_work_region_1']) ? trim($jr_data['job_work_region_1']) : '';
    $rd1 = isset($jr_data['job_work_region_detail_1']) ? trim($jr_data['job_work_region_detail_1']) : '';
    $region_name = $r1 ? _jlh_region_name($r1) : '';
    $detail_name = $rd1 ? _jlh_region_detail_name($rd1) : '';
    $job1 = isset($jr_data['job_job1']) ? trim($jr_data['job_job1']) : '';
    $loc_line = trim($region_name . ' ' . $detail_name . ' ' . $job1);

    $sal_type = $jr_data['job_salary_type'] ?? '';
    $sal_amt = $jr_data['job_salary_amt'] ?? '';
    if ($sal_type === '급여협의') {
        $wage_disp = '급여협의';
    } elseif ($sal_type && $sal_amt) {
        $wage_disp = $sal_type . ' ' . number_format((int)preg_replace('/[^0-9]/', '', $sal_amt)) . '원';
    } elseif ($sal_type) {
        $wage_disp = $sal_type;
    } else {
        $wage_disp = '급여협의';
    }

    $banner_bg = $grad;
    if ($thumb_wave) {
        preg_match_all('/rgb\([^)]+\)|#[0-9a-fA-F]{3,8}/', $grad, $m);
        if (!empty($m[0]) && count($m[0]) >= 2) {
            $c1 = $m[0][0]; $c2 = $m[0][1]; $c3 = isset($m[0][2]) ? $m[0][2] : $m[0][0];
            $banner_bg = "linear-gradient(135deg,{$c1},{$c2},{$c3},{$c1},{$c2})";
        }
    }
    $motion_data = $thumb_motion ? ' data-motion="pv-motion-' . $thumb_motion . '"' : '';
    $border_colors = array('gold'=>'#FFD700','pink'=>'#FF1B6B','charcoal'=>'#3a3a3a','royalblue'=>'#4169E1','royalpurple'=>'#7B2FBE');
    $card_style = '';
    if ($thumb_border && isset($border_colors[$thumb_border])) {
        $bc = $border_colors[$thumb_border];
        $card_style = ' style="box-shadow:0 0 0 3px '.$bc.', 0 2px 8px rgba(0,0,0,.10)"';
    }
    $has_anim = ($thumb_wave || $thumb_motion) ? ' data-lazy-anim="1"' : '';

    echo '<div class="' . $card_class . '"' . $card_style . $has_anim . '>';
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    $wave_data = $thumb_wave ? ' data-wave="animation:wave-shift 6s ease-in-out infinite;background-size:400% 400%;"' : '';
    echo '<div class="premium-banner' . $carbon_class . '" style="background:' . $banner_bg . ';color:' . $text_color . ';"' . $wave_data . '>';
    echo '<span' . $motion_data . '>' . $title . '</span>';
    if ($text) echo '<br><span' . $motion_data . ' style="font-size:11px;opacity:.85">' . $text . '</span>';
    echo '</div>';
    $jr_good_val = isset($row['jr_good']) ? (int)$row['jr_good'] : 0;
    $company_name = htmlspecialchars($row['jr_nickname'] ?: ($row['jr_company'] ?: ''));
    echo '<div class="premium-body">';
    if ($company_name) echo '<div class="premium-name">' . $company_name . '</div>';
    if ($loc_line) echo '<div class="premium-area"><span class="job-loc-badge" style="font-size:10px;padding:1px 5px;border-radius:4px;margin-right:3px">' . htmlspecialchars($region_name) . '</span>' . htmlspecialchars(trim($detail_name . ' · ' . $job1)) . '</div>';
    echo '<div class="premium-wage">' . htmlspecialchars($wage_disp);
    if ($jr_good_val > 0) echo '<span class="job-good-badge" style="float:right">❤️ ' . number_format($jr_good_val) . '</span>';
    echo '</div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
}

/**
 * 급구 카드 렌더링
 */
function render_urgency_card($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $jr_id = (int)$row['jr_id'];
    $link = _jlh_clean_url($row, $jr_data);
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $desc = htmlspecialchars(mb_substr($row['jr_title'] ?: ($jr_data['job_title'] ?? ''), 0, 30, 'UTF-8'));

    $jr_good_val = isset($row['jr_good']) ? (int)$row['jr_good'] : 0;
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="urgency-card">';
    echo '<div class="urgency-name">' . $nickname . '</div>';
    if ($location) echo '<div class="urgency-area">' . $location . '</div>';
    if ($desc) echo '<div class="urgency-desc">' . $desc . '</div>';
    if ($jr_good_val > 0) echo '<div class="job-good-badge" style="margin-top:4px;">❤️ ' . number_format($jr_good_val) . '</div>';
    echo '</div>';
    echo '</a>';
}

/**
 * 추천 카드 렌더링
 */
function render_recommend_card($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $jr_id = (int)$row['jr_id'];
    $link = _jlh_clean_url($row, $jr_data);
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $desc = htmlspecialchars(mb_substr($row['jr_title'] ?: ($jr_data['job_title'] ?? ''), 0, 40, 'UTF-8'));

    $jr_good_val = isset($row['jr_good']) ? (int)$row['jr_good'] : 0;
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="recommend-card">';
    echo '<div><div class="rec-name">' . $nickname . ' <span class="rec-area">' . $location . '</span></div>';
    echo '<div class="rec-desc">' . $desc . '</div></div>';
    echo '<div class="rec-right"><div class="rec-wage">' . $nickname . '</div>';
    if ($jr_good_val > 0) echo '<div class="job-good-badge">❤️ ' . number_format($jr_good_val) . '</div>';
    echo '</div>';
    echo '</div>';
    echo '</a>';
}

/**
 * 지역 ID → 이름 변환 (fallback 데이터 활용)
 */
function _jlh_region_name($er_id) {
    static $map = null;
    if ($map === null) {
        $map = array(1=>'서울',2=>'경기',3=>'인천',4=>'부산',5=>'대구',6=>'광주',7=>'대전',8=>'울산',9=>'강원',10=>'경남',11=>'경북',12=>'전남',13=>'전북',14=>'충남',15=>'충북',16=>'세종',17=>'제주');
    }
    return isset($map[(int)$er_id]) ? $map[(int)$er_id] : '';
}

function _jlh_region_detail_name($erd_id) {
    static $map = null;
    if ($map === null) {
        @include(G5_LIB_PATH . '/ev_region_fallback.inc.php');
        $map = array();
        if (isset($ev_region_details_fallback)) {
            foreach ($ev_region_details_fallback as $rd) $map[(int)$rd['erd_id']] = $rd['erd_name'];
        }
    }
    return isset($map[(int)$erd_id]) ? $map[(int)$erd_id] : '';
}

/**
 * jr_data에서 공통 필드 추출
 */
function _jlh_extract_fields($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $f = array();
    $f['jr_id'] = (int)$row['jr_id'];
    $f['link'] = _jlh_clean_url($row, $jr_data);
    $f['nickname'] = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $f['location'] = htmlspecialchars($jr_data['desc_location'] ?? '');
    $f['region'] = _jlh_region_name($jr_data['job_work_region_1'] ?? '');
    $f['subregion'] = _jlh_region_detail_name($jr_data['job_work_region_detail_1'] ?? '');
    $f['title'] = htmlspecialchars($row['jr_title'] ?: ($jr_data['job_title'] ?? ''));
    $f['job1'] = htmlspecialchars($jr_data['job_job1'] ?? '');
    $f['job2'] = htmlspecialchars($jr_data['job_job2'] ?? '');
    $f['salary_type'] = $jr_data['job_salary_type'] ?? '';
    $f['salary_amt'] = $jr_data['job_salary_amt'] ?? '';
    $f['employ_type'] = $jr_data['employ_type'] ?? '';
    $f['amenity'] = isset($jr_data['amenity']) && is_array($jr_data['amenity']) ? $jr_data['amenity'] : array();
    $f['keyword'] = isset($jr_data['keyword']) && is_array($jr_data['keyword']) ? $jr_data['keyword'] : array();
    $f['grad_key'] = isset($jr_data['thumb_gradient']) ? $jr_data['thumb_gradient'] : '1';
    $f['grad'] = _jlh_get_gradient($f['grad_key']);
    $f['jump_count'] = (int)($row['jr_jump_count'] ?? 0);
    $f['ad_period'] = (int)($row['jr_ad_period'] ?? 30);
    $f['ad_labels'] = $row['jr_ad_labels'] ?? '';

    $sal = $f['salary_type'];
    if ($f['salary_amt']) {
        $f['wage_display'] = number_format((int)$f['salary_amt']) . '원';
    } else {
        $f['wage_display'] = ($sal && $sal !== '급여협의') ? $sal : '면접 후 협의';
    }
    $wb_map = array('일급'=>'wb-ilbul', '월급'=>'wb-wolbul', '시급'=>'wb-sigan', '급여협의'=>'wb-hyup');
    $f['wage_badge_class'] = isset($wb_map[$sal]) ? $wb_map[$sal] : 'wb-hyup';
    $f['wage_badge_label'] = $sal ?: '협의';

    return $f;
}

/**
 * 줄광고 리스트 행 렌더링 (채용정보 테이블) - 원본 디자인 일치
 */
function render_job_list_row($row) {
    $f = _jlh_extract_fields($row);
    $nick_short = mb_substr($f['nickname'], 0, 5, 'UTF-8');
    $jump_icon = $f['jump_count'] >= 10 ? '🔥' : ($f['jump_count'] >= 3 ? '🥉' : '🥈');

    $benefit_html = '';
    foreach (array_slice($f['amenity'], 0, 4) as $i => $am) {
        $cls = $i < 2 ? 'benefit-tag b-hot' : 'benefit-tag';
        $benefit_html .= '<span class="' . $cls . '">' . htmlspecialchars($am) . '</span>';
    }

    $tag_map = array('급구'=>'tag-urgent','초보가능'=>'tag-init','당일지급'=>'tag-bonus','선불가능'=>'tag-pink',
        '출퇴근지원'=>'tag-bonus','원룸제공'=>'tag-pink','인센티브'=>'tag-pay','숙식제공'=>'tag-pink',
        '만근비지원'=>'tag-bonus','갯수보장'=>'tag-pink','지명우대'=>'tag-pink','성형지원'=>'tag-bonus');
    $tags_html = '';
    if (strpos($f['ad_labels'], '급구') !== false) $tags_html .= '<span class="list-tag tag-urgent">급구</span>';
    foreach (array_slice($f['keyword'], 0, 3) as $kw) {
        $tc = isset($tag_map[$kw]) ? $tag_map[$kw] : 'tag-init';
        $tags_html .= '<span class="list-tag ' . $tc . '">' . htmlspecialchars($kw) . '</span>';
    }
    echo '<tr class="job-list-row">';
    echo '<td class="td-region">' . ($f['region'] ?: '-') . ($f['subregion'] ? '<br>' . htmlspecialchars($f['subregion']) : '') . '</td>';
    echo '<td class="td-type">' . ($f['job1'] ?: '-') . ($f['job2'] ? '<br>' . $f['job2'] : '') . '</td>';
    echo '<td class="col-gender td-gender">여<br>-</td>';
    echo '<td class="list-title-cell">';
    echo '<a href="' . $f['link'] . '" class="list-job-title">' . $f['title'] . '</a>';
    if ($benefit_html || $tags_html) {
        echo '<div class="list-title-bottom">';
        if ($benefit_html) echo '<div class="benefit-tags">' . $benefit_html . '</div>';
        if ($tags_html) echo '<div class="list-tags">' . $tags_html . '</div>';
        echo '</div>';
    }
    echo '</td>';
    echo '<td class="col-benefits td-shop">';
    echo '<div class="shop-name">' . $f['nickname'] . '</div>';
    echo '<div class="shop-mini-banner" style="background:' . $f['grad'] . '">' . $nick_short . '</div>';
    if ($f['jump_count'] > 0) echo '<div class="shop-jump">' . $jump_icon . ' ' . $f['jump_count'] . '회 ' . ($f['jump_count'] * $f['ad_period']) . '일</div>';
    echo '</td>';
    echo '<td class="td-wage">';
    echo '<span class="wage-badge ' . $f['wage_badge_class'] . '">' . htmlspecialchars($f['wage_badge_label']) . '</span><br>';
    echo '<span class="wage-amount">' . htmlspecialchars($f['wage_display']) . '</span>';
    echo '</td>';
    echo '</tr>';
}

/**
 * 모바일 줄광고 카드 렌더링 - 원본 디자인 일치
 */
function render_job_list_mobile($row) {
    $f = _jlh_extract_fields($row);
    $jump_icon = $f['jump_count'] >= 10 ? '🔥' : ($f['jump_count'] >= 3 ? '🥉' : '🥈');

    $tag_map = array('급구'=>'tag-urgent','초보가능'=>'tag-init','당일지급'=>'tag-bonus','선불가능'=>'tag-pink',
        '출퇴근지원'=>'tag-bonus','원룸제공'=>'tag-pink','인센티브'=>'tag-pay','숙식제공'=>'tag-pink',
        '만근비지원'=>'tag-bonus','갯수보장'=>'tag-pink');
    $tags_html = '';
    if (strpos($f['ad_labels'], '급구') !== false) $tags_html .= '<span class="list-tag tag-urgent">급구</span>';
    foreach (array_slice($f['keyword'], 0, 3) as $kw) {
        $tc = isset($tag_map[$kw]) ? $tag_map[$kw] : 'tag-init';
        $tags_html .= '<span class="list-tag ' . $tc . '">' . htmlspecialchars($kw) . '</span>';
    }

    $job_type = ($f['job1'] ?: '-') . ($f['job2'] ? ' ' . $f['job2'] : '');

    echo '<a href="' . $f['link'] . '" class="job-card-m">';
    echo '<div class="job-card-m-row row-1"><span class="job-card-m-region">' . ($f['region'] ?: '-') . '</span><span class="job-card-m-title">' . $f['title'] . '</span></div>';
    echo '<div class="job-card-m-row row-2"><span class="job-card-m-region2">' . htmlspecialchars($f['subregion']) . '</span>';
    if ($tags_html) echo '<span class="job-card-m-tags">' . $tags_html . '</span>';
    echo '</div>';
    echo '<div class="job-card-m-row row-3"><span class="job-card-m-type">' . $job_type . ' | 여 -</span><span class="job-card-m-wage">[' . htmlspecialchars($f['wage_badge_label']) . '] ' . htmlspecialchars($f['wage_display']) . '</span></div>';
    echo '<div class="job-card-m-row row-4"><span class="job-card-m-left"></span><span class="job-card-m-shop">' . $f['nickname'];
    if ($f['jump_count'] > 0) echo ' ' . $jump_icon . $f['jump_count'] . '회 ' . ($f['jump_count'] * $f['ad_period']) . '일';
    echo '</span></div>';
    echo '</a>';
}
