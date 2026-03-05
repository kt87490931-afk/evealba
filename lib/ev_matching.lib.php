<?php
/**
 * 매칭시스템: 기업회원 ↔ 이브회원 AI 매칭
 * - ev_matching_run(): 메인 진입점 (cron에서 호출)
 * - 필수 조건: 업종/직종 동일, 근무지역 1순위 동일
 * - 일치율 70% 이상 시 매칭
 */
if (!defined('_GNUBOARD_')) exit;

/**
 * 매칭 설정 조회
 */
function ev_matching_config_get($key = null) {
    static $cfg = null;
    if ($cfg === null) {
        $cfg = array();
        $r = sql_query("SELECT mc_key, mc_value FROM g5_ev_matching_config");
        while ($row = sql_fetch_array($r)) {
            $cfg[$row['mc_key']] = $row['mc_value'];
        }
    }
    if ($key !== null) {
        return isset($cfg[$key]) ? $cfg[$key] : null;
    }
    return $cfg;
}

/**
 * 이브회원 후보 (이력서 있고 rs_job1, rs_work_region 있는 자)
 */
function ev_matching_candidates_eve() {
    $tb = 'g5_resume';
    $tb_check = sql_query("SHOW TABLES LIKE '{$tb}'", false);
    if (!$tb_check || !sql_num_rows($tb_check)) return array();

    $list = array();
    $mem_tbl = isset($GLOBALS['g5']['member_table']) ? $GLOBALS['g5']['member_table'] : 'g5_member';
    $r = sql_query("SELECT r.rs_id, r.mb_id, r.rs_job1, r.rs_job2, r.rs_work_region, r.rs_salary_type, r.rs_salary_amt, r.rs_data, r.rs_nick
        FROM {$tb} r
        INNER JOIN {$mem_tbl} m ON r.mb_id = m.mb_id
        WHERE r.rs_status = 'active'
          AND r.rs_job1 != '' AND r.rs_job1 IS NOT NULL
          AND r.rs_work_region != '' AND r.rs_work_region IS NOT NULL
        ORDER BY r.rs_datetime DESC");
    while ($row = sql_fetch_array($r)) {
        $rs_data = $row['rs_data'] ? json_decode($row['rs_data'], true) : array();
        if (!is_array($rs_data)) $rs_data = array();
        $row['rs_data_parsed'] = $rs_data;
        $row['amenity'] = isset($rs_data['amenity']) && is_array($rs_data['amenity']) ? $rs_data['amenity'] : (isset($rs_data['amenities']) ? (is_array($rs_data['amenities']) ? $rs_data['amenities'] : json_decode($rs_data['amenities'], true)) : array());
        $row['keyword'] = isset($rs_data['keyword']) && is_array($rs_data['keyword']) ? $rs_data['keyword'] : (isset($rs_data['keywords']) ? (is_array($rs_data['keywords']) ? $rs_data['keywords'] : json_decode($rs_data['keywords'], true)) : array());
        $row['mbti'] = isset($rs_data['mbti']) ? trim($rs_data['mbti']) : '';
        $row['employ_type'] = isset($rs_data['employ_type']) ? trim($rs_data['employ_type']) : '';
        if (!is_array($row['amenity'])) $row['amenity'] = array();
        if (!is_array($row['keyword'])) $row['keyword'] = array();
        $list[] = $row;
    }
    return $list;
}

/**
 * 기업회원 후보 (진행중 광고, AI 콘텐츠 또는 jr_data 충분한 자)
 */
function ev_matching_candidates_ent() {
    $jr_table = 'g5_jobs_register';
    $jr_check = sql_query("SHOW TABLES LIKE '{$jr_table}'", false);
    if (!$jr_check || !sql_num_rows($jr_check)) return array();

    $list = array();
    $mem_tbl = isset($GLOBALS['g5']['member_table']) ? $GLOBALS['g5']['member_table'] : 'g5_member';
    $r = sql_query("SELECT j.jr_id, j.mb_id, j.jr_data, j.jr_nickname, j.jr_subject_display
        FROM {$jr_table} j
        INNER JOIN {$mem_tbl} m ON j.mb_id = m.mb_id
        WHERE j.jr_status = 'ongoing'
          AND (j.jr_end_date IS NULL OR j.jr_end_date >= CURDATE())
        ORDER BY j.jr_datetime DESC");
    while ($row = sql_fetch_array($r)) {
        $jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
        if (!is_array($jr_data)) $jr_data = array();
        $job1 = isset($jr_data['job_job1']) ? trim($jr_data['job_job1']) : '';
        $work_region_1 = isset($jr_data['job_work_region_1']) ? trim($jr_data['job_work_region_1']) : '';
        if (!$job1 || !$work_region_1) continue;
        $row['jr_data_parsed'] = $jr_data;
        $row['job_job1'] = $job1;
        $row['job_job2'] = isset($jr_data['job_job2']) ? trim($jr_data['job_job2']) : '';
        $row['job_work_region_1'] = $work_region_1;
        $row['job_salary_type'] = isset($jr_data['job_salary_type']) ? trim($jr_data['job_salary_type']) : '';
        $row['job_salary_amt'] = isset($jr_data['job_salary_amt']) ? (int)preg_replace('/[^0-9]/', '', $jr_data['job_salary_amt']) : 0;
        $row['employ_type'] = isset($jr_data['employ_type']) ? trim($jr_data['employ_type']) : '';
        $row['amenity'] = isset($jr_data['amenity']) && is_array($jr_data['amenity']) ? $jr_data['amenity'] : array();
        $row['keyword'] = isset($jr_data['keyword']) && is_array($jr_data['keyword']) ? $jr_data['keyword'] : array();
        $row['mbti_prefer'] = isset($jr_data['mbti_prefer']) && is_array($jr_data['mbti_prefer']) ? $jr_data['mbti_prefer'] : array();
        $row['ai_location'] = isset($jr_data['ai_location']) ? trim($jr_data['ai_location']) : '';
        $row['ai_env'] = isset($jr_data['ai_env']) ? trim($jr_data['ai_env']) : '';
        $row['ai_welfare'] = isset($jr_data['ai_welfare']) ? trim($jr_data['ai_welfare']) : '';
        $row['ai_qualify'] = isset($jr_data['ai_qualify']) ? trim($jr_data['ai_qualify']) : '';
        $list[] = $row;
    }
    return $list;
}

/**
 * 필수 조건 체크: 업종/직종, 1순위 지역
 */
function ev_matching_check_mandatory($eve, $ent) {
    if ($eve['rs_job1'] !== $ent['job_job1']) return false;
    $eve_j2 = isset($eve['rs_job2']) ? trim($eve['rs_job2']) : '';
    $ent_j2 = isset($ent['job_job2']) ? trim($ent['job_job2']) : '';
    if ($eve_j2 !== '' && $ent_j2 !== '' && $eve_j2 !== $ent_j2) return false;
    $eve_reg = isset($eve['rs_work_region']) ? trim((string)$eve['rs_work_region']) : '';
    $ent_reg = isset($ent['job_work_region_1']) ? trim((string)$ent['job_work_region_1']) : '';
    if ($eve_reg === '' || $ent_reg === '' || $eve_reg !== $ent_reg) return false;
    return true;
}

/**
 * 일치율 산출 (0~100)
 */
function ev_matching_calc_rate($eve, $ent) {
    $total = 0;
    $score = 0;

    $w_employ = 10;
    $total += $w_employ;
    $e_emp = !empty($eve['employ_type']) ? $eve['employ_type'] : '';
    $j_emp = !empty($ent['employ_type']) ? $ent['employ_type'] : '';
    if ($e_emp && $j_emp && $e_emp === $j_emp) $score += $w_employ;
    elseif (!$e_emp || !$j_emp) $score += (int)($w_employ * 0.5);

    $w_salary = 10;
    $total += $w_salary;
    $e_st = !empty($eve['rs_salary_type']) ? $eve['rs_salary_type'] : '';
    $j_st = !empty($ent['job_salary_type']) ? $ent['job_salary_type'] : '';
    if ($e_st && $j_st) {
        if ($e_st === $j_st) $score += $w_salary;
        elseif (($e_st === '급여협의' || $j_st === '급여협의')) $score += (int)($w_salary * 0.7);
    } else $score += (int)($w_salary * 0.5);

    $w_amenity = 10;
    $total += $w_amenity;
    $ea = is_array($eve['amenity']) ? $eve['amenity'] : array();
    $ja = is_array($ent['amenity']) ? $ent['amenity'] : array();
    if (count($ea) > 0 || count($ja) > 0) {
        $inter = array_intersect($ea, $ja);
        $union = array_unique(array_merge($ea, $ja));
        $score += $union ? (int)($w_amenity * count($inter) / count($union)) : (int)($w_amenity * 0.5);
    } else $score += (int)($w_amenity * 0.5);

    $w_keyword = 10;
    $total += $w_keyword;
    $ek = is_array($eve['keyword']) ? $eve['keyword'] : array();
    $jk = is_array($ent['keyword']) ? $ent['keyword'] : array();
    if (count($ek) > 0 || count($jk) > 0) {
        $inter = array_intersect($ek, $jk);
        $union = array_unique(array_merge($ek, $jk));
        $score += $union ? (int)($w_keyword * count($inter) / count($union)) : (int)($w_keyword * 0.5);
    } else $score += (int)($w_keyword * 0.5);

    $w_mbti = 5;
    $total += $w_mbti;
    $em = !empty($eve['mbti']) ? $eve['mbti'] : '';
    $jm = is_array($ent['mbti_prefer']) ? $ent['mbti_prefer'] : array();
    if ($em && (count($jm) === 0 || in_array($em, $jm))) $score += $w_mbti;
    elseif (!$em || count($jm) === 0) $score += (int)($w_mbti * 0.5);

    $w_text = 55;
    $total += $w_text;
    $txt_score = 0;
    $j_txt = trim(($ent['ai_location'] ?? '') . ' ' . ($ent['ai_env'] ?? '') . ' ' . ($ent['ai_welfare'] ?? '') . ' ' . ($ent['ai_qualify'] ?? ''));
    $e_txt = '';
    if (!empty($eve['rs_data_parsed']['intro'])) $e_txt .= ' ' . $eve['rs_data_parsed']['intro'];
    if (!empty($eve['rs_data_parsed']['resume_intro'])) $e_txt .= ' ' . $eve['rs_data_parsed']['resume_intro'];
    if (mb_strlen($j_txt) > 20 && mb_strlen($e_txt) > 10) {
        $j_arr = array_unique(preg_split('/[\s,\.]+/u', $j_txt, -1, PREG_SPLIT_NO_EMPTY));
        $e_arr = array_unique(preg_split('/[\s,\.]+/u', $e_txt, -1, PREG_SPLIT_NO_EMPTY));
        $inter = array_intersect($j_arr, $e_arr);
        $union = array_unique(array_merge($j_arr, $e_arr));
        $txt_score = $union ? min($w_text, (int)($w_text * count($inter) / max(1, count($union) * 0.3))) : (int)($w_text * 0.3);
    } else $txt_score = (int)($w_text * 0.5);
    $score += $txt_score;

    if ($total <= 0) return 70;
    $rate = (int)round(100 * $score / $total);
    return min(100, max(0, $rate));
}

/**
 * 최근 N일 이내 매칭 이력 조회
 */
function ev_matching_recent_pairs($days = 7) {
    $tb = 'g5_ev_matching_log';
    $tb_check = sql_query("SHOW TABLES LIKE '{$tb}'", false);
    if (!$tb_check || !sql_num_rows($tb_check)) return array();

    $pairs = array();
    $d = (int)$days;
    $r = sql_query("SELECT mb_id_eve, mb_id_ent, jr_id, matched_at FROM {$tb} WHERE matched_at >= DATE_SUB(NOW(), INTERVAL {$d} DAY)");
    while ($row = sql_fetch_array($r)) {
        $key = $row['mb_id_eve'] . '|' . $row['mb_id_ent'] . '|' . $row['jr_id'];
        $pairs[$key] = 1;
    }
    return $pairs;
}

/**
 * 회원별 1쌍 매칭 (7일 재매칭 허용)
 */
function ev_matching_pair_one_day($eve_list, $ent_list, $min_rate = 70, $re_match_days = 7) {
    $min_rate = (int)$min_rate;
    $recent = ev_matching_recent_pairs($re_match_days);
    $paired_eve = array();
    $paired_ent = array();
    $results = array();

    foreach ($eve_list as $eve) {
        $mb_eve = $eve['mb_id'];
        if (isset($paired_eve[$mb_eve])) continue;

        $best = null;
        $best_rate = 0;

        foreach ($ent_list as $ent) {
            $mb_ent = $ent['mb_id'];
            $jr_id = (int)$ent['jr_id'];
            $key = $mb_eve . '|' . $mb_ent . '|' . $jr_id;
            if (isset($recent[$key]) || isset($paired_ent[$mb_ent])) continue;
            if (!ev_matching_check_mandatory($eve, $ent)) continue;

            $rate = ev_matching_calc_rate($eve, $ent);
            if ($rate >= $min_rate && $rate > $best_rate) {
                $best_rate = $rate;
                $best = array('eve' => $eve, 'ent' => $ent, 'rate' => $rate);
            }
        }

        if ($best && $best_rate >= $min_rate) {
            $results[] = $best;
            $paired_eve[$mb_eve] = 1;
            $paired_ent[$best['ent']['mb_id']] = 1;
        }
    }
    return $results;
}

/**
 * 매칭 실행 (메인)
 * @return array ['ok'=>1|0, 'pairs'=>array, 'msg'=>'', 'diag'=>array]
 */
function ev_matching_run() {
    $diag = array('start' => date('Y-m-d H:i:s'), 'pairs' => 0, 'memos_sent' => 0);

    $tb_log = 'g5_ev_matching_log';
    $tb_cfg = 'g5_ev_matching_config';
    if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb_log}'", false))) {
        return array('ok' => 0, 'pairs' => array(), 'msg' => 'g5_ev_matching_log 테이블 없음', 'diag' => $diag);
    }

    $enabled = ev_matching_config_get('enabled');
    if ($enabled !== '1') {
        return array('ok' => 1, 'pairs' => array(), 'msg' => '매칭시스템 비활성', 'diag' => $diag);
    }

    $min_rate = (int)ev_matching_config_get('min_rate');
    if ($min_rate <= 0) $min_rate = 70;
    $re_days = (int)ev_matching_config_get('re_match_days');
    if ($re_days <= 0) $re_days = 7;
    $min_eve = (int)ev_matching_config_get('min_eve_count');
    $min_ent = (int)ev_matching_config_get('min_ent_count');

    $eve_list = ev_matching_candidates_eve();
    $ent_list = ev_matching_candidates_ent();
    $diag['eve_count'] = count($eve_list);
    $diag['ent_count'] = count($ent_list);

    if (count($eve_list) < $min_eve || count($ent_list) < $min_ent) {
        return array('ok' => 1, 'pairs' => array(), 'msg' => '최소 인원 미달', 'diag' => $diag);
    }

    $pairs = ev_matching_pair_one_day($eve_list, $ent_list, $min_rate, $re_days);
    $diag['pairs'] = count($pairs);

    $jobs_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
    $now = defined('G5_TIME_YMDHIS') ? G5_TIME_YMDHIS : date('Y-m-d H:i:s');
    $memo_sent = 0;

    foreach ($pairs as $p) {
        $eve = $p['eve'];
        $ent = $p['ent'];
        $rate = $p['rate'];
        $jr_id = (int)$ent['jr_id'];
        $rs_id = (int)$eve['rs_id'];
        $mb_eve = $eve['mb_id'];
        $mb_ent = $ent['mb_id'];
        $nick_ent = $ent['jr_nickname'] ?: $ent['mb_id'];
        $nick_eve = $eve['rs_nick'] ?: $eve['mb_id'];

        $esc_fn = function_exists('sql_escape_string') ? 'sql_escape_string' : 'addslashes';
        $log_esc = "INSERT INTO {$tb_log} (mb_id_eve, mb_id_ent, jr_id, rs_id, match_rate, matched_at, memo_sent) VALUES (
            '" . $esc_fn($mb_eve) . "',
            '" . $esc_fn($mb_ent) . "',
            '{$jr_id}', '{$rs_id}', '{$rate}', '{$now}', 0)";
        sql_query($log_esc);
        $mlog_id = sql_insert_id();

        $m = (int)date('n');
        $d = (int)date('j');
        $msg_base = "지금 확인하세요! {$m}/{$d} 기준 매칭률 {$rate}% 돌파 중 🚀";
        $job_url = $jobs_base . '/jobs_view.php?jr_id=' . $jr_id;
        $resume_url = $jobs_base . '/talent_view.php?rs_id=' . $rs_id;

        $content_eve = $msg_base . "\n\n[{$nick_ent}]님의 채용정보와 {$rate}% 매칭되었어요.\n아래 링크에서 자세히 확인해 보세요.\n\n" . $job_url;
        $content_ent = $msg_base . "\n\n[{$nick_eve}]님의 이력서와 {$rate}% 매칭되었어요.\n아래 링크에서 자세히 확인해 보세요.\n\n" . $resume_url;

        if (function_exists('ev_send_memo')) {
            if (ev_send_memo($mb_eve, $content_eve, '')) $memo_sent++;
            if (ev_send_memo($mb_ent, $content_ent, '')) $memo_sent++;
        }
        if ($mlog_id) {
            sql_query("UPDATE {$tb_log} SET memo_sent = 1 WHERE mlog_id = '{$mlog_id}'");
        }
    }

    $diag['memos_sent'] = $memo_sent;
    $diag['end'] = date('Y-m-d H:i:s');
    return array('ok' => 1, 'pairs' => $pairs, 'msg' => '완료', 'diag' => $diag);
}
