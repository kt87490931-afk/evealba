<?php
/**
 * 채용광고 목록 조회 및 카드 렌더링 헬퍼
 * - get_jobs_by_type(): 광고유형별 ongoing 건 조회
 * - render_job_card(): 썸네일 카드 HTML 출력
 * - render_urgency_card(): 급구/추천 텍스트형 카드
 * - render_premium_card(): 프리미엄/스페셜 카드
 */
if (!defined('_GNUBOARD_')) exit;

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
    $sql = "SELECT * FROM g5_jobs_register WHERE {$where} ORDER BY jr_id DESC{$limit_sql}";
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
 * 우대 카드형 썸네일 렌더링
 */
function render_job_card($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $grad_key = isset($jr_data['thumb_gradient']) ? $jr_data['thumb_gradient'] : '1';
    $grad = _jlh_get_gradient($grad_key);
    $title = htmlspecialchars($jr_data['thumb_title'] ?? $row['jr_nickname'] ?? $row['jr_company'] ?? '');
    $text = htmlspecialchars($jr_data['thumb_text'] ?? '');
    $text_color = $jr_data['thumb_text_color'] ?? 'rgb(255,255,255)';
    $jr_id = (int)$row['jr_id'];
    $link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $jr_id;
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $desc = htmlspecialchars(mb_substr($row['jr_title'] ?: ($jr_data['job_title'] ?? ''), 0, 30, 'UTF-8'));
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $is_new = (strtotime($row['jr_datetime']) > strtotime('-3 days'));
    $jump_count = (int)($row['jr_jump_count'] ?? 0);
    $ad_period = (int)($row['jr_ad_period'] ?? 30);
    $carbon_class = ($grad_key === 'P3') ? ' carbon-bg' : '';

    $badge_html = '';
    if ($jump_count > 0) {
        $crown = $jump_count >= 10 ? '<span class="crown-gold">👑</span>' : ($jump_count >= 3 ? '<span class="crown-bronze">🥉</span>' : '<span class="crown-silver">🥈</span>');
        $badge_html = '<span class="job-badge">' . $crown . $jump_count . '회 ' . ($jump_count * $ad_period) . '일</span>';
    }

    echo '<div class="job-card">';
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="job-card-banner' . $carbon_class . '" style="background:' . $grad . ';color:' . $text_color . '"><span>' . $title . '<br>' . $text . '</span></div>';
    if ($is_new) echo '<div class="new-badge">NEW</div>';
    echo '<div class="job-card-body">';
    if ($location) echo '<div class="job-card-location"><span class="job-loc-badge">' . mb_substr($location, 0, 2, 'UTF-8') . '</span>' . $location . '</div>';
    if ($desc) echo '<div class="job-desc">' . $desc . '</div>';
    echo '<div class="job-card-footer"><span class="job-wage">' . $nickname . '</span>' . $badge_html . '</div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
}

/**
 * 프리미엄/스페셜 카드 렌더링
 */
function render_premium_card($row, $card_class = 'premium-card') {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $grad_key = isset($jr_data['thumb_gradient']) ? $jr_data['thumb_gradient'] : '1';
    $grad = _jlh_get_gradient($grad_key);
    $title = htmlspecialchars($jr_data['thumb_title'] ?? $row['jr_nickname'] ?? $row['jr_company'] ?? '');
    $jr_id = (int)$row['jr_id'];
    $link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $jr_id;
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $carbon_class = ($grad_key === 'P3') ? ' carbon-bg' : '';

    echo '<div class="' . $card_class . '">';
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="premium-banner' . $carbon_class . '" style="background:' . $grad . '">' . $title . '</div>';
    echo '<div class="premium-body">';
    echo '<div class="premium-name">' . $nickname . '</div>';
    if ($location) echo '<div class="premium-area">' . $location . '</div>';
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
    $link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $jr_id;
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $desc = htmlspecialchars(mb_substr($row['jr_title'] ?: ($jr_data['job_title'] ?? ''), 0, 30, 'UTF-8'));

    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="urgency-card">';
    echo '<div class="urgency-name">' . $nickname . '</div>';
    if ($location) echo '<div class="urgency-area">' . $location . '</div>';
    if ($desc) echo '<div class="urgency-desc">' . $desc . '</div>';
    echo '</div>';
    echo '</a>';
}

/**
 * 추천 카드 렌더링
 */
function render_recommend_card($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $jr_id = (int)$row['jr_id'];
    $link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $jr_id;
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $desc = htmlspecialchars(mb_substr($row['jr_title'] ?: ($jr_data['job_title'] ?? ''), 0, 40, 'UTF-8'));

    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="recommend-card">';
    echo '<div><div class="rec-name">' . $nickname . ' <span class="rec-area">' . $location . '</span></div>';
    echo '<div class="rec-desc">' . $desc . '</div></div>';
    echo '<div class="rec-right"><div class="rec-wage">' . $nickname . '</div></div>';
    echo '</div>';
    echo '</a>';
}

/**
 * jr_data에서 공통 필드 추출
 */
function _jlh_extract_fields($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $f = array();
    $f['jr_id'] = (int)$row['jr_id'];
    $f['link'] = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $f['jr_id'];
    $f['nickname'] = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $f['location'] = htmlspecialchars($jr_data['desc_location'] ?? '');
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
    $f['is_new'] = (strtotime($row['jr_datetime']) > strtotime('-3 days'));
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
    if ($f['is_new'] && !$tags_html) $tags_html .= '<span class="list-tag tag-init">NEW</span>';

    echo '<tr class="job-list-row">';
    echo '<td class="td-region">' . htmlspecialchars($f['location']) . '</td>';
    echo '<td class="td-type">' . ($f['job1'] ?: '-') . ($f['job2'] ? '<br>' . $f['job2'] : '') . '</td>';
    echo '<td class="col-gender td-gender">-</td>';
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
    echo '<div class="job-card-m-row row-1"><span class="job-card-m-region">' . mb_substr($f['location'], 0, 4, 'UTF-8') . '</span><span class="job-card-m-title">' . $f['title'] . '</span></div>';
    echo '<div class="job-card-m-row row-2"><span class="job-card-m-region2">' . htmlspecialchars($f['location']) . '</span>';
    if ($tags_html) echo '<span class="job-card-m-tags">' . $tags_html . '</span>';
    echo '</div>';
    echo '<div class="job-card-m-row row-3"><span class="job-card-m-type">' . $job_type . '</span><span class="job-card-m-wage">[' . htmlspecialchars($f['wage_badge_label']) . '] ' . htmlspecialchars($f['wage_display']) . '</span></div>';
    echo '<div class="job-card-m-row row-4"><span class="job-card-m-left"></span><span class="job-card-m-shop">' . $f['nickname'];
    if ($f['jump_count'] > 0) echo ' ' . $jump_icon . $f['jump_count'] . '회 ' . ($f['jump_count'] * $f['ad_period']) . '일';
    echo '</span></div>';
    echo '</a>';
}
