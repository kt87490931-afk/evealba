<?php
/**
 * 채용 상세 우측 패널 — 유사 공고 + 검색 + 추천 구인 + 알림
 */
if (!defined('_GNUBOARD_')) exit;
if (defined('_PANEL_RIGHT_DONE_')) return;
define('_PANEL_RIGHT_DONE_', true);

@include_once(G5_PATH . '/extend/jobs_list_helper.php');

$_pv_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_pv_jr_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'jobs_register';
$_pv_sb_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'special_banner';

$_pv_jr_id = isset($jr_id) ? (int)$jr_id : (isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0);
$_pv_reg1_id = isset($reg1_id) ? trim($reg1_id) : '';
$_pv_job1 = isset($job1) ? trim($job1) : '';
if (!$_pv_reg1_id && isset($data) && is_array($data) && !empty($data['job_work_region_1'])) {
    $_pv_reg1_id = trim($data['job_work_region_1']);
}
if (!$_pv_job1 && isset($data) && is_array($data) && !empty($data['job_job1'])) {
    $_pv_job1 = trim($data['job_job1']);
}

$_pv_similar = array();
if ($_pv_jr_id) {
    $_pv_sim_res = @sql_query("SELECT * FROM {$_pv_jr_table} WHERE jr_status = 'ongoing' AND jr_id != '{$_pv_jr_id}' ORDER BY jr_id DESC LIMIT 80", false);
    if ($_pv_sim_res) {
        while ($_pv_sim_row = sql_fetch_array($_pv_sim_res)) {
            $_pv_sim_jd = is_string($_pv_sim_row['jr_data']) ? json_decode($_pv_sim_row['jr_data'], true) : (array)$_pv_sim_row['jr_data'];
            if (!is_array($_pv_sim_jd)) $_pv_sim_jd = array();
            $_pv_sim_r1 = trim($_pv_sim_jd['job_work_region_1'] ?? '');
            $_pv_sim_j1 = trim($_pv_sim_jd['job_job1'] ?? '');
            if ($_pv_reg1_id && (string)$_pv_sim_r1 !== (string)$_pv_reg1_id) continue;
            if ($_pv_job1 && $_pv_sim_j1 !== $_pv_job1) continue;
            $_pv_similar[] = $_pv_sim_row;
            if (count($_pv_similar) >= 3) break;
        }
    }
}

$_pv_regions = isset($ev_regions) ? $ev_regions : array();
$_pv_jobs = isset($ev_jobs) ? $ev_jobs : array();
if (empty($_pv_regions) && file_exists(G5_LIB_PATH . '/ev_master.lib.php')) {
    include_once G5_LIB_PATH . '/ev_master.lib.php';
    $_pv_regions = ev_get_regions();
    $_pv_jobs = ev_get_jobs();
}

$_pv_recommend = array();
$_pv_tb_check = @sql_query("SHOW TABLES LIKE '{$_pv_sb_table}'", false);
if ($_pv_tb_check && sql_num_rows($_pv_tb_check) > 0) {
    $_pv_rec_res = @sql_query("SELECT jr.*
        FROM {$_pv_sb_table} sb
        LEFT JOIN {$_pv_jr_table} jr ON sb.sb_jr_id = jr.jr_id
        WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
        ORDER BY sb.sb_position ASC LIMIT 5", false);
    if ($_pv_rec_res) {
        while ($_pv_rec_r = sql_fetch_array($_pv_rec_res)) {
            if (!empty($_pv_rec_r['jr_id'])) $_pv_recommend[] = $_pv_rec_r;
        }
    }
}
?>
<aside class="panel-right" aria-label="우측 패널">

  <div class="panel-card">
    <div class="panel-card-head">채용정보</div>
    <div class="search-panel">
      <form method="get" action="<?php echo $_pv_base; ?>/jobs.php">
        <div class="select-row">
          <select name="er_id" aria-label="지역">
            <option value="">지역 전체</option>
<?php foreach ($_pv_regions as $_pv_r) { ?>
            <option value="<?php echo (int)$_pv_r['er_id']; ?>"<?php echo ((string)$_pv_reg1_id === (string)$_pv_r['er_id']) ? ' selected' : ''; ?>><?php echo htmlspecialchars($_pv_r['er_name']); ?></option>
<?php } ?>
          </select>
          <select name="ej_id" aria-label="직종">
            <option value="">직종 전체</option>
<?php foreach ($_pv_jobs as $_pv_j) { ?>
            <option value="<?php echo (int)$_pv_j['ej_id']; ?>"><?php echo htmlspecialchars($_pv_j['ej_name']); ?></option>
<?php } ?>
          </select>
        </div>
        <div class="search-input-row">
          <input type="text" name="stx" placeholder="🔍 채용정보 검색" maxlength="50">
          <button type="submit">검색</button>
        </div>
      </form>
    </div>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">📋 유사 공고</div>
    <div class="similar-list">
<?php if (!empty($_pv_similar)) {
    foreach ($_pv_similar as $_pv_sim) {
        $_pv_sim_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_pv_sim) : $_pv_base . '/jobs_view.php?jr_id=' . (int)$_pv_sim['jr_id'];
        $_pv_sim_jd = is_string($_pv_sim['jr_data']) ? json_decode($_pv_sim['jr_data'], true) : (array)$_pv_sim['jr_data'];
        $_pv_sim_name = $_pv_sim['jr_nickname'] ?: ($_pv_sim['jr_company'] ?: '업소');
        $_pv_sim_title = $_pv_sim['jr_title'] ?: ($_pv_sim_jd['job_title'] ?? $_pv_sim_name);
        $_pv_sim_loc = function_exists('_jlh_region_name') ? _jlh_region_name($_pv_sim_jd['job_work_region_1'] ?? '') : '';
        if ($_pv_sim_loc && !empty($_pv_sim_jd['job_work_region_detail_1']) && function_exists('_jlh_region_detail_name')) {
            $_pv_sim_loc .= ' ' . _jlh_region_detail_name($_pv_sim_jd['job_work_region_detail_1']);
        }
        $_pv_sim_sal = function_exists('_jlh_format_salary_mockup')
            ? _jlh_format_salary_mockup($_pv_sim_jd['job_salary_type'] ?? '', (int)preg_replace('/[^0-9]/', '', (string)($_pv_sim_jd['job_salary_amt'] ?? '')))
            : '협의';
        $_pv_sim_thumb = isset($_pv_sim_jd['thumb_file']) ? trim($_pv_sim_jd['thumb_file']) : '';
        if ($_pv_sim_thumb && defined('G5_DATA_URL')) {
            $_pv_sim_img = G5_DATA_URL . '/jobs/' . $_pv_sim_thumb;
        } elseif (function_exists('_jlh_feed_placeholder_img')) {
            $_pv_sim_img = _jlh_feed_placeholder_img((int)$_pv_sim['jr_id'], 100, 100);
        } else {
            $_pv_sim_img = '';
        }
?>
      <a class="similar-item" href="<?php echo htmlspecialchars($_pv_sim_link, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="similar-thumb">
          <img src="<?php echo htmlspecialchars($_pv_sim_img); ?>" alt="" loading="lazy">
        </div>
        <div class="similar-info">
          <div class="si-title"><?php echo htmlspecialchars(mb_substr($_pv_sim_title, 0, 20, 'UTF-8')); ?></div>
          <div class="si-salary"><?php echo htmlspecialchars($_pv_sim_sal); ?></div>
          <div class="si-loc"><?php echo htmlspecialchars(mb_substr($_pv_sim_loc ?: $region ?? '', 0, 18, 'UTF-8')); ?></div>
        </div>
      </a>
<?php }
} else { ?>
      <p class="panel-empty">유사 공고가 없습니다.</p>
<?php } ?>
    </div>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">💖 추천 구인</div>
    <div class="recommend-list">
<?php if (!empty($_pv_recommend)) {
    foreach ($_pv_recommend as $_pv_rec_row) {
        $_pv_rec_link = function_exists('_jlh_clean_url') ? _jlh_clean_url($_pv_rec_row) : $_pv_base . '/jobs_view.php?jr_id=' . (int)$_pv_rec_row['jr_id'];
        $_pv_rec_name = $_pv_rec_row['jr_nickname'] ?: ($_pv_rec_row['jr_company'] ?: '업소');
        $_pv_rec_jd = is_string($_pv_rec_row['jr_data']) ? json_decode($_pv_rec_row['jr_data'], true) : (array)$_pv_rec_row['jr_data'];
        $_pv_rec_thumb = isset($_pv_rec_jd['thumb_file']) ? trim($_pv_rec_jd['thumb_file']) : '';
        if ($_pv_rec_thumb && defined('G5_DATA_URL')) {
            $_pv_rec_img = G5_DATA_URL . '/jobs/' . $_pv_rec_thumb;
        } elseif (function_exists('_jlh_feed_placeholder_img')) {
            $_pv_rec_img = _jlh_feed_placeholder_img((int)$_pv_rec_row['jr_id'], 100, 100);
        } else {
            $_pv_rec_img = '';
        }
        $_pv_rec_sal = function_exists('_jlh_format_salary_mockup')
            ? _jlh_format_salary_mockup($_pv_rec_jd['job_salary_type'] ?? '', (int)preg_replace('/[^0-9]/', '', (string)($_pv_rec_jd['job_salary_amt'] ?? '')))
            : '협의';
        $_pv_rec_loc = function_exists('_jlh_region_name') ? _jlh_region_name($_pv_rec_jd['job_work_region_1'] ?? '') : '';
        if ($_pv_rec_loc && !empty($_pv_rec_jd['job_work_region_detail_1']) && function_exists('_jlh_region_detail_name')) {
            $_pv_rec_loc .= ' ' . _jlh_region_detail_name($_pv_rec_jd['job_work_region_detail_1']);
        }
?>
      <div class="recommend-item" data-href="<?php echo htmlspecialchars($_pv_rec_link, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="recommend-thumb">
          <img src="<?php echo htmlspecialchars($_pv_rec_img); ?>" alt="" loading="lazy">
        </div>
        <div class="recommend-info">
          <div class="rec-name"><?php echo htmlspecialchars(mb_substr($_pv_rec_name, 0, 16, 'UTF-8')); ?></div>
          <div class="rec-salary"><?php echo htmlspecialchars($_pv_rec_sal); ?></div>
          <div class="rec-loc"><?php echo htmlspecialchars(mb_substr($_pv_rec_loc ?: '›', 0, 18, 'UTF-8')); ?> ›</div>
        </div>
      </div>
<?php }
} else { ?>
      <p class="panel-empty">등록된 추천 구인이 없습니다.</p>
<?php } ?>
    </div>
  </div>

  <div class="panel-card">
    <div class="panel-card-head">🔔 새로운 알림</div>
<?php if ($is_member) { ?>
    <p class="panel-empty">새 알림을 확인해보세요.</p>
    <a class="btn-panel-login" href="<?php echo $_pv_base; ?>/memo_full.php">쪽지함 열기</a>
<?php } else { ?>
    <p class="panel-empty">로그인 후 확인해보세요.</p>
    <a class="btn-panel-login" href="<?php echo G5_BBS_URL; ?>/login.php">로그인하기</a>
<?php } ?>
  </div>

</aside>
