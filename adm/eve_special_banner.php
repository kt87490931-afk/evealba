<?php
/**
 * 어드민 - 특수배너 관리
 * 히어로배너(메인 상단) + 추천업소(플로팅 패널)
 * 기존 채용광고(jr_id)를 특수배너 슬롯에 연결/해제
 */
$sub_menu = '910920';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$token = get_session('ss_admin_token') ?: get_admin_token();
$jr_table = 'g5_jobs_register';
$sb_table = 'g5_special_banner';

// ── 테이블 자동 생성 ──
$tb_check = sql_query("SHOW TABLES LIKE '{$sb_table}'", false);
if (!sql_num_rows($tb_check)) {
    sql_query("CREATE TABLE IF NOT EXISTS `{$sb_table}` (
        sb_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sb_type    ENUM('hero','recommend') NOT NULL DEFAULT 'hero' COMMENT '배너 유형',
        sb_status  ENUM('active','inactive','expired') NOT NULL DEFAULT 'active' COMMENT '상태',
        sb_position TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '표시 순서',
        sb_jr_id   INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '연결 채용광고 ID',
        sb_mb_id   VARCHAR(20) NOT NULL DEFAULT '' COMMENT '기업회원 ID',
        sb_memo    TEXT DEFAULT NULL COMMENT '어드민 메모',
        sb_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        sb_updated DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_type_status (sb_type, sb_status),
        KEY idx_position (sb_position),
        KEY idx_jr_id (sb_jr_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
}

// ── sb_data, sb_link 컬럼 마이그레이션 ──
$col_check = sql_query("SHOW COLUMNS FROM {$sb_table} LIKE 'sb_data'", false);
if (!$col_check || !sql_num_rows($col_check)) {
    sql_query("ALTER TABLE {$sb_table} ADD COLUMN sb_data JSON DEFAULT NULL COMMENT '썸네일 옵션 JSON' AFTER sb_memo", false);
    sql_query("ALTER TABLE {$sb_table} ADD COLUMN sb_link VARCHAR(500) NOT NULL DEFAULT '' COMMENT '클릭 시 이동 URL' AFTER sb_data", false);
}

// ── 현황 조회 ──
$hero_max    = 10;
$recommend_max = 6;

$hero_rows = array();
$res = sql_query("SELECT sb.*, jr.jr_nickname, jr.jr_company, jr.jr_status, jr.jr_end_date, jr.mb_id as jr_mb_id
    FROM {$sb_table} sb
    LEFT JOIN {$jr_table} jr ON sb.sb_jr_id = jr.jr_id
    WHERE sb.sb_type = 'hero' AND sb.sb_status = 'active'
    ORDER BY sb.sb_position ASC, sb.sb_id ASC");
while ($r = sql_fetch_array($res)) { $hero_rows[] = $r; }
$hero_cnt = count($hero_rows);

$recommend_rows = array();
$res = sql_query("SELECT sb.*, jr.jr_nickname, jr.jr_company, jr.jr_status, jr.jr_end_date, jr.mb_id as jr_mb_id, jr.jr_ad_labels
    FROM {$sb_table} sb
    LEFT JOIN {$jr_table} jr ON sb.sb_jr_id = jr.jr_id
    WHERE sb.sb_type = 'recommend' AND sb.sb_status = 'active'
    ORDER BY sb.sb_position ASC, sb.sb_id ASC");
while ($r = sql_fetch_array($res)) { $recommend_rows[] = $r; }
$recommend_cnt = count($recommend_rows);

$jobs_view_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . '/jobs_view.php' : '/jobs_view.php';
$today = date('Y-m-d');

$g5['title'] = '특수배너 관리';
require_once './admin.head.php';
?>

<style>
.sb-dashboard { display:flex; gap:20px; margin-bottom:24px; }
.sb-stat-card {
    flex:1; padding:20px 24px; border-radius:10px; color:#fff; position:relative; overflow:hidden;
}
.sb-stat-card.hero { background:linear-gradient(135deg,#6366f1,#8b5cf6); }
.sb-stat-card.recommend { background:linear-gradient(135deg,#ec4899,#f43f5e); }
.sb-stat-label { font-size:13px; opacity:.85; margin-bottom:4px; }
.sb-stat-num { font-size:32px; font-weight:900; }
.sb-stat-max { font-size:14px; opacity:.7; margin-left:4px; }
.sb-stat-sub { font-size:11px; opacity:.6; margin-top:4px; }

.sb-section { margin-bottom:30px; }
.sb-section-title {
    font-size:15px; font-weight:800; color:#333; margin-bottom:12px;
    display:flex; align-items:center; gap:8px;
}
.sb-section-title .ico { font-size:18px; }
.sb-add-btn {
    display:inline-flex; align-items:center; gap:4px; padding:6px 14px;
    background:#6366f1; color:#fff; border:none; border-radius:6px;
    font-size:12px; font-weight:700; cursor:pointer; text-decoration:none;
    transition:background .2s;
}
.sb-add-btn:hover { background:#4f46e5; color:#fff; }
.sb-add-btn.pink { background:#ec4899; }
.sb-add-btn.pink:hover { background:#db2777; }

.sb-empty { padding:30px; text-align:center; color:#999; font-size:13px; border:1px dashed #ddd; border-radius:8px; }

.sb-remaining { font-size:11px; }
.sb-remaining.ok { color:#2E7D32; }
.sb-remaining.warn { color:#E65100; }
.sb-remaining.end { color:#C62828; }

/* 검색 모달 */
.sb-modal-bg {
    display:none; position:fixed; top:0; left:0; right:0; bottom:0;
    background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;
}
.sb-modal-bg.open { display:flex; }
.sb-modal {
    background:#fff; border-radius:12px; width:640px; max-width:95vw;
    max-height:80vh; overflow:hidden; display:flex; flex-direction:column;
    box-shadow:0 8px 40px rgba(0,0,0,.2);
}
.sb-modal-head {
    padding:16px 20px; background:#f8f9fa; border-bottom:1px solid #eee;
    display:flex; align-items:center; justify-content:space-between;
}
.sb-modal-head h3 { margin:0; font-size:15px; font-weight:800; }
.sb-modal-close { background:none; border:none; font-size:22px; cursor:pointer; color:#999; }
.sb-modal-body { padding:16px 20px; overflow-y:auto; flex:1; }
.sb-search-bar {
    display:flex; gap:8px; margin-bottom:14px;
}
.sb-search-bar input {
    flex:1; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px;
}
.sb-search-bar button {
    padding:8px 16px; background:#6366f1; color:#fff; border:none; border-radius:6px;
    font-size:13px; font-weight:700; cursor:pointer;
}
.sb-search-bar button.pink { background:#ec4899; }
.sb-result-list { max-height:340px; overflow-y:auto; }
.sb-result-item {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 12px; border:1px solid #eee; border-radius:8px; margin-bottom:6px;
    transition:background .15s;
}
.sb-result-item:hover { background:#f8f0ff; }
.sb-result-info { flex:1; }
.sb-result-info .rid { font-weight:800; color:#6366f1; margin-right:6px; }
.sb-result-info .rname { font-weight:700; color:#333; }
.sb-result-info .rsub { font-size:11px; color:#888; margin-top:2px; }
.sb-result-select {
    padding:5px 12px; background:#6366f1; color:#fff; border:none; border-radius:5px;
    font-size:11px; font-weight:700; cursor:pointer;
}
.sb-result-select.pink { background:#ec4899; }
.sb-memo-input {
    width:100%; padding:6px 10px; border:1px solid #ddd; border-radius:5px;
    font-size:12px; margin-top:6px;
}
</style>

<!-- 대시보드 -->
<div class="sb-dashboard">
    <div class="sb-stat-card hero">
        <div class="sb-stat-label">🏆 히어로배너</div>
        <div class="sb-stat-num"><?php echo $hero_cnt; ?><span class="sb-stat-max">/ <?php echo $hero_max; ?></span></div>
        <div class="sb-stat-sub"><?php echo ($hero_max - $hero_cnt); ?>자리 남음</div>
    </div>
    <div class="sb-stat-card recommend">
        <div class="sb-stat-label">💎 추천업소 (플로팅)</div>
        <div class="sb-stat-num"><?php echo $recommend_cnt; ?><span class="sb-stat-max">/ <?php echo $recommend_max; ?></span></div>
        <div class="sb-stat-sub"><?php echo ($recommend_max - $recommend_cnt); ?>자리 남음</div>
    </div>
</div>

<!-- ═══ 히어로배너 섹션 ═══ -->
<div class="sb-section">
    <div class="sb-section-title">
        <span class="ico">🏆</span> 히어로배너
        <a href="./eve_special_banner_hero_form.php" class="sb-add-btn">+ 히어로배너 생성</a>
    </div>

    <?php if ($hero_cnt === 0) { ?>
        <div class="sb-empty">등록된 히어로배너가 없습니다. "히어로배너 생성" 버튼으로 새로 만드세요.</div>
    <?php } else { ?>
    <div class="tbl_head01 tbl_wrap">
        <table>
            <thead>
                <tr>
                    <th scope="col" style="width:50px;">순서</th>
                    <th scope="col" style="width:50px;">sb_id</th>
                    <th scope="col" style="width:60px;">jr_id</th>
                    <th scope="col">업소명</th>
                    <th scope="col">기업회원</th>
                    <th scope="col">상태</th>
                    <th scope="col">남은기간</th>
                    <th scope="col">생성일</th>
                    <th scope="col">메모</th>
                    <th scope="col" style="width:160px;">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hero_rows as $idx => $hr) {
                    $remaining = '—';
                    $rem_class = '';
                    if (!empty($hr['jr_end_date'])) {
                        $end_ts = strtotime($hr['jr_end_date']);
                        $today_ts = strtotime($today);
                        if ($end_ts >= $today_ts) {
                            $days = (int)(($end_ts - $today_ts) / 86400);
                            $remaining = $days . '일';
                            $rem_class = $days > 7 ? 'ok' : 'warn';
                        } else {
                            $remaining = '마감';
                            $rem_class = 'end';
                        }
                    }
                    $view_url = (int)$hr['sb_jr_id'] ? $jobs_view_base . '?jr_id=' . (int)$hr['sb_jr_id'] : '#';
                ?>
                <tr class="bg<?php echo $idx % 2; ?>">
                    <td class="td_num"><?php echo (int)$hr['sb_position']; ?></td>
                    <td class="td_num" style="color:#6366f1;font-weight:700;">#<?php echo (int)$hr['sb_id']; ?></td>
                    <td class="td_num"><?php echo (int)$hr['sb_jr_id'] ? '<a href="'.$view_url.'" target="_blank" style="color:#6366f1;font-weight:700;">#'.(int)$hr['sb_jr_id'].'</a>' : '—'; ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($hr['jr_company'] ?: '—'); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($hr['sb_mb_id'] ?: $hr['jr_mb_id'] ?: '—'); ?></td>
                    <td class="td_num"><?php echo htmlspecialchars($hr['sb_status']); ?></td>
                    <td class="td_num"><span class="sb-remaining <?php echo $rem_class; ?>"><?php echo $remaining; ?></span></td>
                    <td class="td_datetime"><?php echo substr($hr['sb_created'], 0, 10); ?></td>
                    <td class="td_left" style="font-size:11px;"><?php echo htmlspecialchars(cut_str($hr['sb_memo'] ?: '', 30)); ?></td>
                    <td class="td_mng">
                        <a href="./eve_special_banner_hero_form.php?sb_id=<?php echo (int)$hr['sb_id']; ?>" class="btn btn_02">편집</a>
                        <?php if ((int)$hr['sb_jr_id']) { ?>
                        <a href="<?php echo $view_url; ?>" class="btn btn_02" target="_blank">광고</a>
                        <?php } ?>
                        <a href="./eve_special_banner_update.php?act=remove&sb_id=<?php echo (int)$hr['sb_id']; ?>&token=<?php echo $token; ?>" class="btn btn_01" onclick="return confirm('히어로배너를 삭제하시겠습니까?');">삭제</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<!-- ═══ 추천업소 섹션 ═══ -->
<div class="sb-section">
    <div class="sb-section-title">
        <span class="ico">💎</span> 추천업소 (플로팅)
        <?php if ($recommend_cnt < $recommend_max) { ?>
            <button type="button" class="sb-add-btn pink" onclick="openSearchModal('recommend')">+ 추천업소 승인</button>
        <?php } else { ?>
            <span style="font-size:11px;color:#C62828;font-weight:400;">슬롯이 가득 찼습니다 (<?php echo $recommend_max; ?>/<?php echo $recommend_max; ?>)</span>
        <?php } ?>
    </div>

    <?php if ($recommend_cnt === 0) { ?>
        <div class="sb-empty">승인된 추천업소가 없습니다. 위 버튼으로 진행중인 채용광고를 승인하세요.</div>
    <?php } else { ?>
    <div class="tbl_head01 tbl_wrap">
        <table>
            <thead>
                <tr>
                    <th scope="col" style="width:50px;">순서</th>
                    <th scope="col" style="width:60px;">jr_id</th>
                    <th scope="col">업소명</th>
                    <th scope="col">닉네임</th>
                    <th scope="col">기업회원</th>
                    <th scope="col">광고라벨</th>
                    <th scope="col">남은기간</th>
                    <th scope="col">승인일</th>
                    <th scope="col">메모</th>
                    <th scope="col" style="width:120px;">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recommend_rows as $idx => $rr) {
                    $remaining = '—';
                    $rem_class = '';
                    if (!empty($rr['jr_end_date'])) {
                        $end_ts = strtotime($rr['jr_end_date']);
                        $today_ts = strtotime($today);
                        if ($end_ts >= $today_ts) {
                            $days = (int)(($end_ts - $today_ts) / 86400);
                            $remaining = $days . '일';
                            $rem_class = $days > 7 ? 'ok' : 'warn';
                        } else {
                            $remaining = '마감';
                            $rem_class = 'end';
                        }
                    }
                    $view_url = $jobs_view_base . '?jr_id=' . (int)$rr['sb_jr_id'];
                ?>
                <tr class="bg<?php echo $idx % 2; ?>">
                    <td class="td_num"><?php echo (int)$rr['sb_position']; ?></td>
                    <td class="td_num"><a href="<?php echo $view_url; ?>" target="_blank" style="color:#ec4899;font-weight:700;">#<?php echo (int)$rr['sb_jr_id']; ?></a></td>
                    <td class="td_left"><?php echo htmlspecialchars($rr['jr_company'] ?: '—'); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($rr['jr_nickname'] ?: '—'); ?></td>
                    <td class="td_left"><?php echo htmlspecialchars($rr['sb_mb_id'] ?: $rr['jr_mb_id'] ?: '—'); ?></td>
                    <td class="td_left" style="font-size:11px;"><?php echo htmlspecialchars(cut_str(str_replace(',', ', ', $rr['jr_ad_labels'] ?: ''), 30)); ?></td>
                    <td class="td_num"><span class="sb-remaining <?php echo $rem_class; ?>"><?php echo $remaining; ?></span></td>
                    <td class="td_datetime"><?php echo substr($rr['sb_created'], 0, 10); ?></td>
                    <td class="td_left" style="font-size:11px;"><?php echo htmlspecialchars(cut_str($rr['sb_memo'] ?: '', 30)); ?></td>
                    <td class="td_mng">
                        <a href="<?php echo $view_url; ?>" class="btn btn_02" target="_blank">보기</a>
                        <a href="./eve_special_banner_update.php?act=remove&sb_id=<?php echo (int)$rr['sb_id']; ?>&token=<?php echo $token; ?>" class="btn btn_01" onclick="return confirm('추천업소 승인을 해제하시겠습니까?\n(jr_ad_labels에서 추천업소 라벨도 제거됩니다)');">해제</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<!-- ═══ 검색 모달 ═══ -->
<div class="sb-modal-bg" id="sbModal">
    <div class="sb-modal">
        <div class="sb-modal-head">
            <h3 id="sbModalTitle">채용광고 검색</h3>
            <button type="button" class="sb-modal-close" onclick="closeSearchModal()">&times;</button>
        </div>
        <div class="sb-modal-body">
            <div class="sb-search-bar">
                <input type="text" id="sbSearchInput" placeholder="jr_id, 업소명, 닉네임, 회원ID로 검색..." onkeypress="if(event.key==='Enter')doSearch()">
                <button type="button" id="sbSearchBtn" onclick="doSearch()">검색</button>
            </div>
            <div id="sbSearchResult" class="sb-result-list"></div>
        </div>
    </div>
</div>

<script>
var SB_TOKEN = '<?php echo $token; ?>';
var SB_MODE  = '';

function openSearchModal(mode) {
    SB_MODE = mode;
    var title = mode === 'hero' ? '🏆 히어로배너 — 채용광고 연결' : '💎 추천업소 — 채용광고 승인';
    document.getElementById('sbModalTitle').textContent = title;
    var btn = document.getElementById('sbSearchBtn');
    btn.className = mode === 'hero' ? '' : 'pink';
    document.getElementById('sbSearchInput').value = '';
    document.getElementById('sbSearchResult').innerHTML = '';
    document.getElementById('sbModal').classList.add('open');
    document.getElementById('sbSearchInput').focus();
}

function closeSearchModal() {
    document.getElementById('sbModal').classList.remove('open');
}

function doSearch() {
    var q = document.getElementById('sbSearchInput').value.trim();
    if (!q) { alert('검색어를 입력하세요.'); return; }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', './eve_special_banner_update.php?act=search&q=' + encodeURIComponent(q) + '&type=' + SB_MODE + '&token=' + SB_TOKEN);
    xhr.onload = function() {
        if (xhr.status !== 200) { alert('검색 오류'); return; }
        var data;
        try { data = JSON.parse(xhr.responseText); } catch(e) { alert('응답 파싱 오류'); return; }
        renderResults(data);
    };
    xhr.send();
}

function renderResults(data) {
    var box = document.getElementById('sbSearchResult');
    if (!data.length) { box.innerHTML = '<div style="padding:20px;text-align:center;color:#999;">검색 결과가 없습니다.</div>'; return; }

    var html = '';
    var btnClass = SB_MODE === 'hero' ? '' : ' pink';
    for (var i = 0; i < data.length; i++) {
        var r = data[i];
        var remaining = r.remaining || '—';
        html += '<div class="sb-result-item" id="sri_' + r.jr_id + '">';
        html += '<div class="sb-result-info">';
        html += '<span class="rid">#' + r.jr_id + '</span>';
        html += '<span class="rname">' + escHtml(r.jr_company || '—') + '</span>';
        html += '<div class="rsub">';
        html += '닉네임: ' + escHtml(r.jr_nickname || '—');
        html += ' · 회원: ' + escHtml(r.mb_id || '—');
        html += ' · 상태: ' + escHtml(r.jr_status || '—');
        html += ' · 남은기간: ' + remaining;
        html += ' · 라벨: ' + escHtml(r.jr_ad_labels || '—');
        html += '</div>';
        html += '<input type="text" class="sb-memo-input" id="memo_' + r.jr_id + '" placeholder="메모 (선택사항, 예: 계약금액 300만원)">';
        html += '</div>';
        html += '<button type="button" class="sb-result-select' + btnClass + '" onclick="doConnect(' + r.jr_id + ')">연결</button>';
        html += '</div>';
    }
    box.innerHTML = html;
}

function doConnect(jrId) {
    var memo = '';
    var el = document.getElementById('memo_' + jrId);
    if (el) memo = el.value;

    var msg = SB_MODE === 'hero'
        ? '이 채용광고를 히어로배너로 연결하시겠습니까?'
        : '이 채용광고를 추천업소로 승인하시겠습니까?\n(jr_ad_labels에 추천업소 라벨이 추가됩니다)';
    if (!confirm(msg)) return;

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = './eve_special_banner_update.php';

    var fields = {act: 'connect', type: SB_MODE, jr_id: jrId, memo: memo, token: SB_TOKEN};
    for (var k in fields) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = k; inp.value = fields[k];
        form.appendChild(inp);
    }
    document.body.appendChild(form);
    form.submit();
}

function escHtml(s) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}
</script>

<?php
require_once './admin.tail.php';
