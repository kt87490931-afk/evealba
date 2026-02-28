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
    $sql = "SELECT * FROM g5_jobs_register WHERE {$where} ORDER BY jr_id DESC LIMIT {$limit}";
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

    $carbon_class = ($grad_key === 'P3') ? ' carbon-bg' : '';

    echo '<div class="job-card">';
    echo '<a href="' . $link . '" style="text-decoration:none;color:inherit;">';
    echo '<div class="job-card-banner' . $carbon_class . '" style="background:' . $grad . ';color:' . $text_color . '"><span>' . $title . '<br>' . $text . '</span></div>';
    if ($is_new) echo '<div class="new-badge">NEW</div>';
    echo '<div class="job-card-body">';
    if ($location) echo '<div class="job-card-location"><span class="job-loc-badge">' . mb_substr($location, 0, 2, 'UTF-8') . '</span>' . $location . '</div>';
    if ($desc) echo '<div class="job-desc">' . $desc . '</div>';
    echo '<div class="job-card-footer"><span class="job-wage">' . $nickname . '</span></div>';
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
 * 줄광고 리스트 행 렌더링 (채용정보 테이블)
 */
function render_job_list_row($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $jr_id = (int)$row['jr_id'];
    $link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $jr_id;
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $loc_short = mb_substr($location, 0, 2, 'UTF-8') ?: '-';
    $title_text = htmlspecialchars($row['jr_title'] ?: ($jr_data['job_title'] ?? ''));
    $desc = htmlspecialchars(mb_substr($title_text, 0, 50, 'UTF-8'));
    $grad_key = isset($jr_data['thumb_gradient']) ? $jr_data['thumb_gradient'] : '1';
    $grad = _jlh_get_gradient($grad_key);

    echo '<tr class="job-list-row">';
    echo '<td class="td-region">' . $loc_short . '</td>';
    echo '<td class="td-type">-</td>';
    echo '<td class="col-gender td-gender">-</td>';
    echo '<td class="list-title-cell"><a href="' . $link . '" class="list-job-title">' . $desc . '</a></td>';
    echo '<td class="col-benefits td-shop"><div class="shop-name">' . $nickname . '</div>';
    echo '<div class="shop-mini-banner" style="background:' . $grad . '">' . mb_substr($nickname, 0, 4, 'UTF-8') . '</div></td>';
    echo '<td class="td-wage"><span class="wage-amount">-</span></td>';
    echo '</tr>';
}

/**
 * 모바일 줄광고 카드 렌더링
 */
function render_job_list_mobile($row) {
    $jr_data = is_string($row['jr_data']) ? json_decode($row['jr_data'], true) : (array)$row['jr_data'];
    $jr_id = (int)$row['jr_id'];
    $link = (defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/jobs_view.php?jr_id=' . $jr_id;
    $nickname = htmlspecialchars($row['jr_nickname'] ?: $row['jr_company']);
    $location = htmlspecialchars($jr_data['desc_location'] ?? '');
    $loc_short = mb_substr($location, 0, 2, 'UTF-8') ?: '-';
    $title_text = htmlspecialchars($row['jr_title'] ?: ($jr_data['job_title'] ?? ''));

    echo '<a href="' . $link . '" class="job-card-m">';
    echo '<div class="job-card-m-row row-1"><span class="job-card-m-region">' . $loc_short . '</span><span class="job-card-m-title">' . $title_text . '</span></div>';
    echo '<div class="job-card-m-row row-2"><span class="job-card-m-region2">' . $location . '</span></div>';
    echo '<div class="job-card-m-row row-3"><span class="job-card-m-type">-</span><span class="job-card-m-wage">-</span></div>';
    echo '<div class="job-card-m-row row-4"><span class="job-card-m-left"></span><span class="job-card-m-shop">' . $nickname . '</span></div>';
    echo '</a>';
}
