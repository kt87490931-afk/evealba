<?php
/**
 * 어드민 — AI 생성 콘텐츠 관리 (목록 + 상세 + 재생성 + 버전 이력)
 */
$sub_menu = '910200';
require_once './_common.php';
include_once(G5_LIB_PATH . '/jobs_ai_content.lib.php');

auth_check_menu($auth, $sub_menu, 'r');
$token = get_session('ss_admin_token') ?: get_admin_token();

$g5['title'] = 'AI 생성 콘텐츠 관리';
require_once './admin.head.php';

$tb_aic = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_content'", false);
if (!$tb_aic || !sql_num_rows($tb_aic)) {
    echo '<div class="local_desc01 local_desc"><p>g5_jobs_ai_content 테이블이 없습니다. 마이그레이션을 실행하세요.</p></div>';
    require_once './admin.tail.php';
    exit;
}

$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$view_jr = isset($_GET['jr_id']) ? (int)$_GET['jr_id'] : 0;

// ═══ 상세보기 모드 ═══
if ($view_id || $view_jr) {
    $where = $view_id ? "id = '{$view_id}'" : "jr_id = '{$view_jr}' AND is_active = 1";
    $aic = sql_fetch("SELECT * FROM g5_jobs_ai_content WHERE {$where} ORDER BY id DESC LIMIT 1");
    if (!$aic) {
        echo '<div class="local_desc01 local_desc"><p>데이터가 없습니다.</p></div>';
        require_once './admin.tail.php';
        exit;
    }
    $jr = sql_fetch("SELECT jr_id, mb_id, jr_nickname, jr_company, jr_title, jr_subject_display FROM g5_jobs_register WHERE jr_id = '".(int)$aic['jr_id']."'");
    $ai_data = json_decode($aic['ai_data'], true);
    if (!is_array($ai_data)) $ai_data = array();
    $versions = aic_get_versions((int)$aic['jr_id']);
    $jobs_view_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') . '/jobs_view.php?jr_id=' . (int)$aic['jr_id'] : '/jobs_view.php?jr_id=' . (int)$aic['jr_id'];
    ?>
    <div class="local_desc01 local_desc">
        <a href="./jobs_ai_content_list.php" class="btn btn_02" style="float:right;">← 목록으로</a>
        <h3>📄 AI 생성 상세 — jr_id: <?php echo (int)$aic['jr_id']; ?> / <?php echo htmlspecialchars($jr['jr_nickname'] ?? ''); ?> (v<?php echo (int)$aic['version']; ?>)</h3>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
            <tr><th>jr_id</th><td><?php echo (int)$aic['jr_id']; ?></td><th>회원ID</th><td><?php echo htmlspecialchars($aic['mb_id']); ?></td></tr>
            <tr><th>닉네임</th><td><?php echo htmlspecialchars($jr['jr_nickname'] ?? ''); ?></td><th>업소명</th><td><?php echo htmlspecialchars($jr['jr_company'] ?? ''); ?></td></tr>
            <tr><th>톤</th><td><?php echo htmlspecialchars($aic['ai_tone']); ?></td><th>버전</th><td>v<?php echo (int)$aic['version']; ?> <?php echo $aic['is_active'] ? '<span style="color:#2E7D32;font-weight:700;">✅ 활성</span>' : '<span style="color:#999;">비활성</span>'; ?></td></tr>
            <tr><th>생성일시</th><td><?php echo $aic['created_at']; ?></td><th>소요시간</th><td><?php echo number_format((int)$aic['duration_ms']); ?>ms</td></tr>
        </table>
    </div>

    <h4 style="margin:20px 0 10px;">📝 AI 생성 섹션 (<?php echo count($ai_data); ?>개 키)</h4>
    <div class="tbl_frm01 tbl_wrap">
        <table>
            <?php
            $section_labels = array(
                'ai_intro' => '📝 인사말',
                'ai_card1_title' => '✨ 카드1 제목', 'ai_card1_desc' => '✨ 카드1 설명',
                'ai_card2_title' => '✨ 카드2 제목', 'ai_card2_desc' => '✨ 카드2 설명',
                'ai_card3_title' => '✨ 카드3 제목', 'ai_card3_desc' => '✨ 카드3 설명',
                'ai_card4_title' => '✨ 카드4 제목', 'ai_card4_desc' => '✨ 카드4 설명',
                'ai_location' => '📍 업소 위치', 'ai_env' => '🏢 근무환경',
                'ai_welfare' => '🎁 복리후생', 'ai_qualify' => '📋 자격/우대',
                'ai_extra' => '📄 추가상세', 'ai_mbti_comment' => '🧠 MBTI 한마디',
                'ai_content' => '📜 종합(레거시)', 'ai_benefit' => '💰 혜택(구)', 'ai_wrapup' => '🎀 마무리(구)'
            );
            foreach ($ai_data as $k => $v) {
                if (strpos($k, '_') === 0) continue;
                $label = isset($section_labels[$k]) ? $section_labels[$k] : $k;
                ?>
                <tr>
                    <th style="width:150px;vertical-align:top;"><?php echo htmlspecialchars($label); ?></th>
                    <td style="white-space:pre-wrap;line-height:1.6;font-size:13px;"><?php echo nl2br(htmlspecialchars(trim($v))); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <h4 style="margin:20px 0 10px;">🕐 버전 이력</h4>
    <div class="tbl_head01 tbl_wrap">
        <table>
            <thead><tr><th>버전</th><th>톤</th><th>생성일시</th><th>소요시간</th><th>상태</th><th>관리</th></tr></thead>
            <tbody>
            <?php foreach ($versions as $ver) { ?>
                <tr class="<?php echo $ver['is_active'] ? 'bg_active' : ''; ?>">
                    <td>v<?php echo (int)$ver['version']; ?></td>
                    <td><?php echo htmlspecialchars($ver['ai_tone']); ?></td>
                    <td><?php echo $ver['created_at']; ?></td>
                    <td><?php echo number_format((int)$ver['duration_ms']); ?>ms</td>
                    <td><?php echo $ver['is_active'] ? '<span style="color:#2E7D32;font-weight:700;">✅ 활성</span>' : '비활성'; ?></td>
                    <td>
                        <a href="?view=<?php echo (int)$ver['id']; ?>" class="btn btn_02">보기</a>
                        <?php if (!$ver['is_active']) { ?>
                        <a href="./jobs_ai_content_action.php?act=activate&jr_id=<?php echo (int)$aic['jr_id']; ?>&version=<?php echo (int)$ver['version']; ?>&token=<?php echo $token; ?>" class="btn btn_02" onclick="return confirm('v<?php echo (int)$ver['version']; ?> 버전을 활성화하시겠습니까?');">활성화</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="margin:20px 0;text-align:center;">
        <a href="<?php echo $jobs_view_url; ?>" class="btn btn_02" target="_blank">📋 광고페이지 보기</a>
        <a href="./jobs_ai_content_action.php?act=regenerate&jr_id=<?php echo (int)$aic['jr_id']; ?>&token=<?php echo $token; ?>" class="btn btn_02" onclick="return confirm('AI를 재생성하시겠습니까? 기존 버전은 보존됩니다.');">🔄 재생성</a>
        <a href="./jobs_ai_content_list.php" class="btn btn_01">← 목록</a>
    </div>
    <style>.bg_active td{background:#f0fff0 !important;}</style>
    <?php
    require_once './admin.tail.php';
    exit;
}

// ═══ 목록 모드 ═══
$cnt_done = (int)sql_fetch("SELECT COUNT(DISTINCT jr_id) as c FROM g5_jobs_ai_content WHERE is_active = 1")['c'];
$cnt_q_pending = 0; $cnt_q_processing = 0; $cnt_q_fail = 0; $cnt_q_done = 0;
$tbq = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
$has_queue_table = ($tbq && sql_num_rows($tbq));
if ($has_queue_table) {
    $cnt_q_pending = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status = 'pending'")['c'];
    $cnt_q_processing = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status = 'processing'")['c'];
    $cnt_q_fail = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status = 'failed'")['c'];
    $cnt_q_done = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status = 'done'")['c'];
}
$cnt_q_stuck = 0;
if ($has_queue_table) {
    $cnt_q_stuck = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_queue WHERE status = 'processing' AND TIMESTAMPDIFF(SECOND, COALESCE(processed_at, created_at), NOW()) > 300")['c'];
}
?>
<div class="local_desc01 local_desc">
    <h3>AI 생성 콘텐츠 현황</h3>
</div>
<div class="local_ov01 local_ov" style="margin-bottom:15px;">
    <span class="btn_ov01"><span class="ov_txt">생성완료</span><span class="ov_num" style="color:#2E7D32;"><?php echo $cnt_done; ?>건</span></span>
    <span class="btn_ov01"><span class="ov_txt">대기중</span><span class="ov_num" style="color:#E65100;"><?php echo $cnt_q_pending; ?>건</span></span>
    <span class="btn_ov01"><span class="ov_txt">처리중</span><span class="ov_num" style="color:#1565C0;"><?php echo $cnt_q_processing; ?>건</span></span>
    <span class="btn_ov01"><span class="ov_txt">실패</span><span class="ov_num" style="color:#C62828;"><?php echo $cnt_q_fail; ?>건</span></span>
    <span class="btn_ov01"><span class="ov_txt">완료(큐)</span><span class="ov_num" style="color:#666;"><?php echo $cnt_q_done; ?>건</span></span>
</div>

<!-- API 키 테스트 -->
<div style="background:#f5f5f5;border:1px solid #ddd;border-radius:8px;padding:10px 16px;margin-bottom:15px;display:flex;align-items:center;gap:15px;">
    <strong>API 키 점검</strong>
    <a href="./jobs_ai_content_action.php?act=test_api_key&token=<?php echo $token; ?>" class="btn btn_02">API 키 테스트</a>
    <span style="font-size:12px;color:#666;">Gemini API 키가 유효한지 실시간 검증합니다.</span>
</div>

<?php if ($cnt_q_stuck > 0) { ?>
<!-- 고착 경고 -->
<div style="background:#fff8e1;border:1px solid #ffc107;border-radius:8px;padding:12px 16px;margin-bottom:15px;">
    <strong style="color:#E65100;">⚠ 고착 항목 <?php echo $cnt_q_stuck; ?>건 감지</strong>
    <span style="font-size:12px;color:#666;margin-left:8px;">5분 이상 processing 상태에 머물러 있는 항목입니다.</span>
    <a href="./jobs_ai_content_action.php?act=reset_stuck&token=<?php echo $token; ?>" class="btn btn_02" style="margin-left:10px;" onclick="return confirm('고착된 <?php echo $cnt_q_stuck; ?>건을 초기화하시겠습니까?');">수동 초기화</a>
</div>
<?php } ?>

<?php if ($cnt_q_fail > 0) { ?>
<!-- 실패 섹션 -->
<div style="background:#fff3f3;border:1px solid #e57373;border-radius:8px;padding:12px 16px;margin-bottom:15px;">
    <strong style="color:#C62828;">실패 건 <?php echo $cnt_q_fail; ?>건</strong>
    <span style="margin-left:10px;">
        <a href="./jobs_ai_content_action.php?act=retry_all&token=<?php echo $token; ?>" class="btn btn_02" onclick="return confirm('실패+고착 항목을 모두 재시도하시겠습니까?');">전체 재시도</a>
    </span>
    <?php
    $fail_result = sql_query("SELECT q.jr_id, q.error_msg, q.processed_at, r.jr_nickname FROM g5_jobs_ai_queue q LEFT JOIN g5_jobs_register r ON q.jr_id = r.jr_id WHERE q.status = 'failed' ORDER BY q.id DESC LIMIT 10");
    ?>
    <table style="width:100%;margin-top:10px;font-size:12px;">
        <tr style="background:#ffebee;"><th>jr_id</th><th>닉네임</th><th>에러</th><th>시간</th><th>관리</th></tr>
        <?php while ($frow = sql_fetch_array($fail_result)) { ?>
        <tr>
            <td><?php echo (int)$frow['jr_id']; ?></td>
            <td><?php echo htmlspecialchars($frow['jr_nickname'] ?? ''); ?></td>
            <td style="color:#C62828;"><?php echo htmlspecialchars(cut_str($frow['error_msg'], 60)); ?></td>
            <td><?php echo $frow['processed_at']; ?></td>
            <td><a href="./jobs_ai_content_action.php?act=retry&jr_id=<?php echo (int)$frow['jr_id']; ?>&token=<?php echo $token; ?>" class="btn btn_02" onclick="return confirm('재시도하시겠습니까?');">재시도</a></td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php } ?>

<?php if ($has_queue_table && ($cnt_q_pending > 0 || $cnt_q_processing > 0)) { ?>
<!-- 대기/처리중 큐 모니터링 -->
<div style="background:#e3f2fd;border:1px solid #90caf9;border-radius:8px;padding:12px 16px;margin-bottom:15px;">
    <strong style="color:#1565C0;">대기/처리중 큐 (<?php echo ($cnt_q_pending + $cnt_q_processing); ?>건)</strong>
    <span style="font-size:12px;color:#666;margin-left:8px;">크론이 2분 간격으로 처리합니다. 건당 약 20초 소요.</span>
    <?php
    $queue_active = sql_query("SELECT q.id, q.jr_id, q.status, q.retry_count, q.created_at, q.processed_at,
        TIMESTAMPDIFF(SECOND, COALESCE(q.processed_at, q.created_at), NOW()) as elapsed_sec,
        r.jr_nickname, r.jr_title
        FROM g5_jobs_ai_queue q
        LEFT JOIN g5_jobs_register r ON q.jr_id = r.jr_id
        WHERE q.status IN ('pending','processing')
        ORDER BY q.id ASC LIMIT 20");
    ?>
    <table style="width:100%;margin-top:10px;font-size:12px;">
        <tr style="background:#bbdefb;"><th>#순서</th><th>큐ID</th><th>jr_id</th><th>닉네임</th><th>상태</th><th>등록시간</th><th>경과</th><th>재시도</th></tr>
        <?php
        $order = 0;
        while ($qrow = sql_fetch_array($queue_active)) {
            $order++;
            $elapsed = (int)$qrow['elapsed_sec'];
            $elapsed_str = $elapsed >= 3600 ? floor($elapsed/3600).'시간 '.floor(($elapsed%3600)/60).'분' : ($elapsed >= 60 ? floor($elapsed/60).'분 '.($elapsed%60).'초' : $elapsed.'초');
            $is_stuck = ($qrow['status'] === 'processing' && $elapsed > 300);
            $row_style = $is_stuck ? 'background:#fff3e0;' : '';
            $status_label = $qrow['status'] === 'pending' ? '<span style="color:#E65100;font-weight:700;">대기중</span>' : '<span style="color:#1565C0;font-weight:700;">처리중</span>';
            if ($is_stuck) $status_label = '<span style="color:#D84315;font-weight:700;">고착</span>';
        ?>
        <tr style="<?php echo $row_style; ?>">
            <td style="text-align:center;"><?php echo $order; ?></td>
            <td style="text-align:center;"><?php echo (int)$qrow['id']; ?></td>
            <td style="text-align:center;"><?php echo (int)$qrow['jr_id']; ?></td>
            <td><?php echo htmlspecialchars($qrow['jr_nickname'] ?? ''); ?></td>
            <td style="text-align:center;"><?php echo $status_label; ?></td>
            <td><?php echo $qrow['created_at']; ?></td>
            <td style="text-align:center;<?php echo $is_stuck ? 'color:#D84315;font-weight:700;' : ''; ?>"><?php echo $elapsed_str; ?></td>
            <td style="text-align:center;"><?php echo (int)$qrow['retry_count']; ?>회</td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php } ?>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>AI 생성 콘텐츠 목록</caption>
        <thead>
            <tr>
                <th>ID</th><th>jr_id</th><th>회원ID</th><th>닉네임</th>
                <th>버전</th><th>톤</th><th>생성일시</th><th>소요시간</th><th>상태</th><th>관리</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $page_rows = 20;
        $total = (int)sql_fetch("SELECT COUNT(*) as c FROM g5_jobs_ai_content")['c'];
        $total_page = ceil($total / $page_rows);
        if ($page < 1) $page = 1;
        $from = ($page - 1) * $page_rows;
        $list_result = sql_query("SELECT c.*, r.jr_nickname, r.jr_company
                                  FROM g5_jobs_ai_content c
                                  LEFT JOIN g5_jobs_register r ON c.jr_id = r.jr_id
                                  ORDER BY c.id DESC LIMIT {$from}, {$page_rows}");
        $cnt = 0;
        while ($lrow = sql_fetch_array($list_result)) {
            $cnt++;
        ?>
            <tr class="bg<?php echo $cnt % 2; ?>">
                <td class="td_num"><?php echo (int)$lrow['id']; ?></td>
                <td class="td_num"><?php echo (int)$lrow['jr_id']; ?></td>
                <td class="td_left"><?php echo htmlspecialchars($lrow['mb_id']); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($lrow['jr_nickname'] ?? ''); ?></td>
                <td class="td_num">v<?php echo (int)$lrow['version']; ?></td>
                <td class="td_num"><?php echo htmlspecialchars($lrow['ai_tone']); ?></td>
                <td class="td_datetime"><?php echo $lrow['created_at']; ?></td>
                <td class="td_num"><?php echo number_format((int)$lrow['duration_ms']); ?>ms</td>
                <td class="td_num"><?php echo $lrow['is_active'] ? '<span style="color:#2E7D32;font-weight:700;">✅활성</span>' : '<span style="color:#999;">비활성</span>'; ?></td>
                <td class="td_mng">
                    <a href="?view=<?php echo (int)$lrow['id']; ?>" class="btn btn_02">보기</a>
                </td>
            </tr>
        <?php }
        if ($cnt == 0) echo '<tr><td colspan="10" class="empty_table"><span>자료가 없습니다.</span></td></tr>';
        ?>
        </tbody>
    </table>
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?page="); ?>

<?php require_once './admin.tail.php'; ?>
