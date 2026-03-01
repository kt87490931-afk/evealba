<?php
/**
 * 인재정보 메인 영역 — DB 연동
 */
if (!defined('_GNUBOARD_')) exit;

$rs_table = 'g5_resume';
$tb_exists = @sql_query("SHOW TABLES LIKE '{$rs_table}'", false);
$rs_table_ok = ($tb_exists && @sql_num_rows($tb_exists));

$page     = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$per_page = 20;
$offset   = ($page - 1) * $per_page;

$where = "rs_status = 'active'";

$tf = isset($talent_filters) ? $talent_filters : array('er_id'=>0,'ei_id'=>0,'ej_id'=>0,'stx'=>'');

$filter_job1 = '';
if ($tf['ei_id'] && isset($ev_industries) && is_array($ev_industries)) {
    foreach ($ev_industries as $ind) {
        if ((int)$ind['ei_id'] === (int)$tf['ei_id']) { $filter_job1 = $ind['ei_name']; break; }
    }
    if ($filter_job1) $where .= " AND rs_job1 = '".addslashes($filter_job1)."'";
}
$filter_job2 = '';
if ($tf['ej_id'] && isset($ev_jobs) && is_array($ev_jobs)) {
    foreach ($ev_jobs as $jj) {
        if ((int)$jj['ej_id'] === (int)$tf['ej_id']) { $filter_job2 = $jj['ej_name']; break; }
    }
    if ($filter_job2) $where .= " AND rs_job2 = '".addslashes($filter_job2)."'";
}
$filter_region = '';
if ($tf['er_id'] && isset($ev_regions) && is_array($ev_regions)) {
    foreach ($ev_regions as $rg) {
        if ((int)$rg['er_id'] === (int)$tf['er_id']) { $filter_region = $rg['er_name']; break; }
    }
    if ($filter_region) $where .= " AND (rs_region = '".addslashes($filter_region)."' OR rs_work_region = '".addslashes($filter_region)."')";
}
if ($tf['stx']) {
    $stx_esc = addslashes($tf['stx']);
    $where .= " AND (rs_title LIKE '%{$stx_esc}%' OR rs_nick LIKE '%{$stx_esc}%' OR rs_region LIKE '%{$stx_esc}%' OR rs_job1 LIKE '%{$stx_esc}%' OR rs_data LIKE '%{$stx_esc}%')";
}

$total_all = 0; $cnt_room = 0; $cnt_karaoke = 0; $cnt_massage = 0;
if ($rs_table_ok) {
    $r = sql_fetch("SELECT COUNT(*) as cnt FROM {$rs_table} WHERE rs_status='active'");
    $total_all = (int)$r['cnt'];
    $r = sql_fetch("SELECT COUNT(*) as cnt FROM {$rs_table} WHERE rs_status='active' AND rs_job1 IN ('룸살롱','룸싸롱')");
    $cnt_room = (int)$r['cnt'];
    $r = sql_fetch("SELECT COUNT(*) as cnt FROM {$rs_table} WHERE rs_status='active' AND rs_job1 IN ('노래주점','노래방','가라오케')");
    $cnt_karaoke = (int)$r['cnt'];
    $r = sql_fetch("SELECT COUNT(*) as cnt FROM {$rs_table} WHERE rs_status='active' AND rs_job1='마사지'");
    $cnt_massage = (int)$r['cnt'];
}

$total_filtered = 0;
if ($rs_table_ok) {
    $r = sql_fetch("SELECT COUNT(*) as cnt FROM {$rs_table} WHERE {$where}");
    $total_filtered = (int)$r['cnt'];
}
$total_pages = max(1, ceil($total_filtered / $per_page));

$rows = array();
if ($rs_table_ok && $total_filtered > 0) {
    $result = sql_query("SELECT * FROM {$rs_table} WHERE {$where} ORDER BY rs_datetime DESC LIMIT {$offset}, {$per_page}");
    while ($row = sql_fetch_array($result)) {
        $rows[] = $row;
    }
}

$talent_form_action = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : 'talent.php';
$talent_view_base   = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent_view.php' : 'talent_view.php';
$resume_register_url= (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php';

$active_tab = 'all';
if ($filter_job1 && in_array($filter_job1, array('룸살롱','룸싸롱'))) $active_tab = 'room';
elseif ($filter_job1 && in_array($filter_job1, array('노래주점','노래방','가라오케'))) $active_tab = 'karaoke';
elseif ($filter_job1 === '마사지') $active_tab = 'massage';
?>
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>

    <!-- 업직종 탭 카운터 -->
    <div class="type-tab-bar">
      <div class="type-tab-card<?php echo $active_tab==='all'?' active':''; ?>" onclick="setTab(this,'all')">
        <div class="ttc-left">
          <span class="ttc-icon">👩</span>
          <span class="ttc-name">전체</span>
        </div>
        <span class="ttc-count"><?php echo number_format($total_all); ?></span>
      </div>
      <div class="type-tab-card<?php echo $active_tab==='room'?' active':''; ?>" onclick="setTab(this,'room')">
        <div class="ttc-left">
          <span class="ttc-icon">🥂</span>
          <span class="ttc-name">룸싸롱</span>
        </div>
        <span class="ttc-count"><?php echo number_format($cnt_room); ?></span>
      </div>
      <div class="type-tab-card<?php echo $active_tab==='karaoke'?' active':''; ?>" onclick="setTab(this,'karaoke')">
        <div class="ttc-left">
          <span class="ttc-icon">🎤</span>
          <span class="ttc-name">노래주점</span>
        </div>
        <span class="ttc-count"><?php echo number_format($cnt_karaoke); ?></span>
      </div>
      <div class="type-tab-card<?php echo $active_tab==='massage'?' active':''; ?>" onclick="setTab(this,'massage')">
        <div class="ttc-left">
          <span class="ttc-icon">💆</span>
          <span class="ttc-name">마사지</span>
        </div>
        <span class="ttc-count"><?php echo number_format($cnt_massage); ?></span>
      </div>
    </div>

    <!-- AI 매칭 배너 -->
    <div class="ai-match-banner">
      <div class="amb-icon">🤖</div>
      <div class="amb-text">
        <h3>AI 인재 매칭 시스템</h3>
        <p>조건에 맞는 인재를 자동으로 매칭해드립니다. 베타테스트중</p>
      </div>
      <div class="amb-badge">✨ 무료 운영 중</div>
    </div>

    <!-- 열람권 안내 배너 -->
    <div class="view-ticket-banner">
      <div class="vtb-icon">🔒</div>
      <div class="vtb-text">
        <h3>인재정보 상세 열람을 위해 열람권이 필요합니다</h3>
        <p>열람권 구매 후 24시간 동안 해당 인재의 상세 정보를 열람하실 수 있습니다</p>
      </div>
      <div class="vtb-btn">열람권 구매</div>
    </div>

    <!-- 검색 필터 -->
    <form method="get" action="<?php echo htmlspecialchars($talent_form_action); ?>" id="talent-search-form" class="filter-box">
      <div class="filter-title">인재정보 검색하기 &nbsp;<small style="font-size:11px;font-weight:500;color:#aaa">조건 하나만 선택해도 검색이 가능합니다!</small></div>
      <div class="filter-rows">
        <div class="filter-row">
          <span class="filter-label">▸ 직종</span>
          <select class="filter-select" name="ei_id" id="talent-filter-ei-id">
            <option value="">1차 직종선택</option>
            <?php foreach ((isset($ev_industries) ? $ev_industries : []) as $i) { ?>
            <option value="<?php echo (int)$i['ei_id']; ?>"<?php echo ($tf['ei_id']==$i['ei_id'])?' selected':''; ?>><?php echo htmlspecialchars($i['ei_name']); ?></option>
            <?php } ?>
          </select>
          <select class="filter-select" name="ej_id" id="talent-filter-ej-id">
            <option value="">2차 세부직종</option>
            <?php foreach ((isset($ev_jobs) ? $ev_jobs : []) as $j) { ?>
            <option value="<?php echo (int)$j['ej_id']; ?>" data-ei-id="<?php echo (int)$j['ei_id']; ?>"<?php echo ($tf['ej_id']==$j['ej_id'])?' selected':''; ?>><?php echo htmlspecialchars($j['ej_name']); ?></option>
            <?php } ?>
          </select>
          &nbsp;&nbsp;
          <span class="filter-label">▸ 지역</span>
          <select class="filter-select" name="er_id" id="talent-filter-er-id">
            <option value="">지역선택</option>
            <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
            <option value="<?php echo (int)$r['er_id']; ?>"<?php echo ($tf['er_id']==$r['er_id'])?' selected':''; ?>><?php echo htmlspecialchars($r['er_name']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">▸ 키워드</span>
          <input class="filter-input" type="text" name="stx" placeholder="이름(닉네임), 지역, 나이, 키워드로 검색 가능합니다." value="<?php echo htmlspecialchars($tf['stx']); ?>">
        </div>
      </div>
      <div class="filter-actions">
        <button type="submit" class="btn-search">🔍 검색</button>
        <button type="button" class="btn-reset" onclick="location.href='<?php echo htmlspecialchars($talent_form_action); ?>'">초기화</button>
      </div>
    </form>

    <!-- 인재정보 리스트 -->
    <div class="section-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <h2 class="section-title">👑 인재정보 리스트</h2>
        <span class="result-count">총 <strong><?php echo number_format($total_filtered); ?></strong>건</span>
      </div>
      <div style="display:flex;gap:7px;">
        <a href="<?php echo $resume_register_url; ?>" class="btn-write" style="display:inline-flex;align-items:center;gap:5px;padding:8px 16px;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;border-radius:20px;font-size:13px;font-weight:700;text-decoration:none;">✏️ 글쓰기</a>
        <a href="<?php echo htmlspecialchars($talent_form_action); ?>" class="btn-list" style="display:inline-flex;align-items:center;gap:5px;padding:8px 14px;background:#f5f5f5;color:#666;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;">📋 목록</a>
      </div>
    </div>

    <div class="talent-table-wrap">
      <table class="talent-table">
        <thead>
          <tr>
            <th style="width:72px">이름</th>
            <th style="width:72px">성별</th>
            <th>제목 / 희망지역</th>
            <th style="width:90px">희망업종</th>
            <th style="width:100px">희망급여</th>
            <th style="width:90px">작성일</th>
          </tr>
        </thead>
        <tbody>
<?php if (count($rows) === 0) { ?>
          <tr><td colspan="6" style="text-align:center;padding:40px 0;color:#999;">등록된 이력서가 없습니다.</td></tr>
<?php } else { foreach ($rows as $row) {
    $nick = htmlspecialchars($row['rs_nick']);
    $nick_masked = mb_substr($nick, 0, 1, 'UTF-8') . str_repeat('○', min(2, max(1, mb_strlen($nick, 'UTF-8')-1)));
    $gender_label = ($row['rs_gender'] === '여성' || $row['rs_gender'] === '여') ? '여' : '남';
    $age_text = $row['rs_age'] ? $row['rs_age'].'세' : '';
    $region_display = $row['rs_work_region'] ? htmlspecialchars($row['rs_work_region']) : htmlspecialchars($row['rs_region']);
    $job1 = htmlspecialchars($row['rs_job1']);
    $salary_type = $row['rs_salary_type'];
    $salary_amt  = (int)$row['rs_salary_amt'];
    $wage_class = 'wb-hyup';
    $wage_label = '협의';
    $wage_amount = '면접 후 협의';
    if ($salary_type && $salary_type !== '협의' && $salary_type !== '-급여유형선택-') {
        if (strpos($salary_type, '일') !== false) { $wage_class = 'wb-ilbul'; $wage_label = '일불'; }
        elseif (strpos($salary_type, '월') !== false) { $wage_class = 'wb-wolbul'; $wage_label = '월급'; }
        elseif (strpos($salary_type, '시') !== false) { $wage_class = 'wb-sibul'; $wage_label = '시급'; }
        else { $wage_label = htmlspecialchars($salary_type); }
        $wage_amount = $salary_amt > 0 ? number_format($salary_amt).'원' : '면접 후 협의';
    }
    $rs_date = $row['rs_datetime'] ? substr($row['rs_datetime'], 0, 10) : '';
    $is_new = (strtotime($row['rs_datetime']) > strtotime('-3 days'));
    $view_url = $talent_view_base . '?rs_id=' . (int)$row['rs_id'];
?>
          <tr class="talent-row" data-region="<?php echo htmlspecialchars($row['rs_region']); ?>" data-type="<?php echo $job1; ?>">
            <td class="td-name"><?php echo $nick_masked; ?></td>
            <td class="td-gender"><span class="gender-badge gender-f"><?php echo $gender_label; ?></span><br><span class="age-text"><?php echo $age_text; ?></span></td>
            <td class="td-title"><a href="<?php echo $view_url; ?>" class="talent-title"><?php echo htmlspecialchars($row['rs_title']); ?> <?php if ($is_new) { ?><span class="badge-new-inline">N</span><?php } ?></a><div class="talent-region">희망지역 : <strong><?php echo $region_display; ?></strong></div></td>
            <td class="td-job-type"><span class="job-type-badge"><?php echo $job1 ?: '기타'; ?></span></td>
            <td class="td-wage"><span class="wage-badge <?php echo $wage_class; ?>"><?php echo $wage_label; ?></span><span class="wage-amount"><?php echo $wage_amount; ?></span></td>
            <td class="td-date"><?php echo $rs_date; ?></td>
          </tr>
<?php } } ?>
        </tbody>
      </table>
    </div>

    <!-- 페이지네이션 -->
<?php if ($total_pages > 1) {
    $q_params = array();
    if ($tf['ei_id']) $q_params['ei_id'] = $tf['ei_id'];
    if ($tf['ej_id']) $q_params['ej_id'] = $tf['ej_id'];
    if ($tf['er_id']) $q_params['er_id'] = $tf['er_id'];
    if ($tf['stx'])   $q_params['stx']   = $tf['stx'];
    function _talent_page_url($pg, $base, $params) {
        $params['page'] = $pg;
        return $base . '?' . http_build_query($params);
    }
    $page_start = max(1, $page - 2);
    $page_end   = min($total_pages, $page + 2);
?>
    <div class="pagination">
<?php if ($page > 1) { ?>
      <a href="<?php echo _talent_page_url($page-1, $talent_form_action, $q_params); ?>" class="page-btn prev-next">◀ PREV</a>
<?php } ?>
<?php for ($p = $page_start; $p <= $page_end; $p++) { ?>
      <a href="<?php echo _talent_page_url($p, $talent_form_action, $q_params); ?>" class="page-btn<?php echo $p===$page?' active':''; ?>"><?php echo $p; ?></a>
<?php } ?>
<?php if ($page < $total_pages) { ?>
      <a href="<?php echo _talent_page_url($page+1, $talent_form_action, $q_params); ?>" class="page-btn prev-next">NEXT ▶</a>
<?php } ?>
    </div>
<?php } ?>

    <!-- 하단 재검색 -->
    <form method="get" action="<?php echo htmlspecialchars($talent_form_action); ?>" class="bottom-search">
      <select class="filter-select" name="ei_id">
        <option value="">1차 직종선택</option>
        <?php foreach ((isset($ev_industries) ? $ev_industries : []) as $i) { ?>
        <option value="<?php echo (int)$i['ei_id']; ?>"<?php echo ($tf['ei_id']==$i['ei_id'])?' selected':''; ?>><?php echo htmlspecialchars($i['ei_name']); ?></option>
        <?php } ?>
      </select>
      <select class="filter-select" name="ej_id">
        <option value="">2차 직종선택</option>
        <?php foreach ((isset($ev_jobs) ? $ev_jobs : []) as $j) { ?>
        <option value="<?php echo (int)$j['ej_id']; ?>"<?php echo ($tf['ej_id']==$j['ej_id'])?' selected':''; ?>><?php echo htmlspecialchars($j['ej_name']); ?></option>
        <?php } ?>
      </select>
      <select class="filter-select" name="er_id">
        <option value="">지역선택</option>
        <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
        <option value="<?php echo (int)$r['er_id']; ?>"<?php echo ($tf['er_id']==$r['er_id'])?' selected':''; ?>><?php echo htmlspecialchars($r['er_name']); ?></option>
        <?php } ?>
      </select>
      <input class="filter-input" type="text" name="stx" placeholder="키워드 입력" value="<?php echo htmlspecialchars($tf['stx']); ?>">
      <button type="submit" class="btn-bottom-search">🔍 검색</button>
    </form>

<script>
function setTab(el, type){
  var url='<?php echo htmlspecialchars($talent_form_action); ?>';
  var map={room:'1',karaoke:'2',massage:'3'};
  if(type!=='all' && map[type]) url+='?ei_id='+map[type];
  location.href=url;
}
</script>
