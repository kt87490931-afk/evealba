<?php
/**
 * EveAlba 채팅관리(운영/설정) - Gnuboard Admin
 * 경로: /adm/scorepoint/scorepoint_chat_manage.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910500';
if ($sub_menu === '') {
    $sub_menu = '910500';
}

$adm_dir = dirname(__DIR__);
$adm_dir_real = @realpath($adm_dir);
if ($adm_dir_real && is_dir($adm_dir_real)) {
    $adm_dir = $adm_dir_real;
}
$old_cwd = @getcwd();
if ($adm_dir && is_dir($adm_dir)) {
    @chdir($adm_dir);
}
require_once $adm_dir . '/_common.php';
if ($old_cwd) {
    @chdir($old_cwd);
}

if (!isset($is_admin) || !$is_admin) {
    alert('관리자만 접근 가능합니다.');
}
auth_check_menu($auth, $sub_menu, 'r');

$tbl_cfg = isset($g5['chat_config_table']) ? $g5['chat_config_table'] : 'g5_chat_config';
$cfg = sql_fetch(" SELECT * FROM {$tbl_cfg} LIMIT 1 ");
if (!$cfg) {
    $cfg = array();
}

$freeze = (isset($cfg['cf_freeze']) && (int)$cfg['cf_freeze'] === 1) ? 1 : 0;
$spam_sec = isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2;
$repeat_sec = isset($cfg['cf_repeat_sec']) ? (int)$cfg['cf_repeat_sec'] : 30;
$report_lim = isset($cfg['cf_report_limit']) ? (int)$cfg['cf_report_limit'] : 10;
$autoban_min = isset($cfg['cf_autoban_min']) ? (int)$cfg['cf_autoban_min'] : 10;
$online_fake_add = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
$daily_visit_target = isset($cfg['cf_daily_visit_target']) ? (int)$cfg['cf_daily_visit_target'] : 0;

$notice_txt = isset($cfg['cf_notice_text']) ? $cfg['cf_notice_text'] : (isset($cfg['cf_notice_txt']) ? $cfg['cf_notice_txt'] : '');
$rule_txt = isset($cfg['cf_rule_text']) ? $cfg['cf_rule_text'] : '';
$badwords_txt = isset($cfg['cf_badwords']) ? $cfg['cf_badwords'] : '';
$left_login_notice = isset($cfg['cf_left_login_notice']) ? trim($cfg['cf_left_login_notice']) : '';
$left_login_ticker_speed = 30;
if (isset($cfg['cf_left_login_ticker_speed'])) {
    $sec = (int)$cfg['cf_left_login_ticker_speed'];
    if ($sec >= 10 && $sec <= 45) {
        $left_login_ticker_speed = $sec;
    }
}

$CHAT_AJAX_URL = G5_PLUGIN_URL . '/chat/chat_ajax.php';
$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '?sub_menu=' . $sub_menu . '" class="ov_listall">전체목록</a>';
$base_url = G5_ADMIN_URL . '/scorepoint/';

$g5['title'] = '채팅관리(운영/설정)';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<style>
.sp-chat-form tbody th,
.sp-chat-form tbody td { text-align: left !important; }
.sp-chat-form tbody th { vertical-align: middle; }
.sp-chat-form tbody td .frm_input,
.sp-chat-form tbody td select { text-align: left; }
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <a href="<?php echo $base_url; ?>scorepoint_chat_notice.php?sub_menu=910501" class="btn_ov01">공지/규정/금칙어</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_reports.php?sub_menu=910700" class="btn_ov01">최근신고</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_banlist.php?sub_menu=910600" class="btn_ov01">밴리스트</a>
</div>

<form name="fchat_manage" id="fchat_manage" method="post" onsubmit="return false;">
    <input type="hidden" name="form_check" value="1">
    <input type="hidden" id="adm-notice-text" value="<?php echo htmlspecialchars($notice_txt, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" id="adm-rule-text" value="<?php echo htmlspecialchars($rule_txt, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" id="adm-badwords" value="<?php echo htmlspecialchars($badwords_txt, ENT_QUOTES, 'UTF-8'); ?>">

    <div class="tbl_head01 tbl_wrap sp-chat-form" style="margin-top:16px;">
        <table>
            <caption class="sound_only">채팅 운영/설정</caption>
            <colgroup>
                <col style="width:180px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">채팅 동결(입력 잠금)</th>
                    <td>
                        <label><input type="checkbox" name="adm_freeze" id="adm_freeze" value="1" <?php echo $freeze ? 'checked' : ''; ?>> 동결 적용</label>
                        <button type="button" id="adm-freeze-apply" class="btn btn_02">적용</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">닉네임 밴</th>
                    <td>
                        <input type="text" name="adm_ban_nick" id="adm-ban-nick" class="frm_input" placeholder="밴할 닉네임" style="width:140px;">
                        <select name="adm_ban_min" id="adm-ban-min" class="frm_input" style="width:80px;">
                            <option value="10">10분</option>
                            <option value="60">60분</option>
                            <option value="600">600분</option>
                            <option value="0">영구</option>
                        </select>
                        <input type="text" name="adm_ban_reason" id="adm-ban-reason" class="frm_input" placeholder="사유(관리자용)" style="width:200px;">
                        <button type="button" id="adm-ban-apply" class="btn btn_02">밴</button>
                        <p class="frm_info">밴 대상은 회원만 가능. 밴/해제는 밴리스트에서 관리.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">채팅창 비우기</th>
                    <td>
                        <button type="button" id="adm-clear-chat" class="btn btn_02">채팅창 비우기</button>
                        <p class="frm_info">전체 채팅을 DB에서 삭제합니다. (복구 불가)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">좌측 로그인박스 하단 공지글</th>
                    <td>
                        <input type="text" name="adm_left_login_notice" id="adm-left-login-notice" class="frm_input" value="<?php echo htmlspecialchars($left_login_notice, ENT_QUOTES, 'UTF-8'); ?>" placeholder="공지 내용 입력 시 좌측 로그인박스 하단 띠에 표시됩니다" style="width:100%;max-width:400px;">
                        <p class="frm_info" style="margin-top:6px;">공지 속도(한 바퀴 주기):</p>
                        <select name="adm_left_login_ticker_speed" id="adm-left-login-ticker-speed" class="frm_input" style="width:140px;margin-top:4px;">
                            <?php foreach (array(10 => '10초 (가장 빠름)', 12 => '12초', 15 => '15초 (빠름)', 20 => '20초', 25 => '25초', 30 => '30초 (보통)', 35 => '35초', 40 => '40초', 45 => '45초 (느림)') as $sec => $label): ?>
                            <option value="<?php echo $sec; ?>" <?php echo (int)$left_login_ticker_speed === $sec ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="frm_info">좌측 로그인박스 하단 띠에 오른쪽→왼쪽으로 흐르는 공지글입니다. 비우면 하단 띠가 표시되지 않습니다.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tbl_head01 tbl_wrap sp-chat-form" style="margin-top:20px;">
        <table>
            <caption class="sound_only">도배/신고 설정</caption>
            <colgroup>
                <col style="width:180px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">연속 전송 제한(초)</th>
                    <td><input type="number" name="adm_spam_sec" id="adm-spam-sec" class="frm_input" value="<?php echo (int)$spam_sec; ?>" min="0" style="width:80px;"></td>
                </tr>
                <tr>
                    <th scope="row">동일내용 반복 제한(초)</th>
                    <td><input type="number" name="adm_repeat_sec" id="adm-repeat-sec" class="frm_input" value="<?php echo (int)$repeat_sec; ?>" min="0" style="width:80px;"></td>
                </tr>
                <tr>
                    <th scope="row">신고 누적 임계(명)</th>
                    <td><input type="number" name="adm_report_limit" id="adm-report-limit" class="frm_input" value="<?php echo (int)$report_lim; ?>" min="1" style="width:80px;"></td>
                </tr>
                <tr>
                    <th scope="row">자동밴 시간(분)</th>
                    <td><input type="number" name="adm_autoban_min" id="adm-autoban-min" class="frm_input" value="<?php echo (int)$autoban_min; ?>" min="0" style="width:80px;"></td>
                </tr>
                <tr>
                    <th scope="row">접속자 가산(+n)</th>
                    <td><input type="number" name="adm_online_fake_add" id="adm-online-fake-add" class="frm_input" value="<?php echo (int)$online_fake_add; ?>" min="0" style="width:80px;"></td>
                </tr>
                <tr>
                    <th scope="row">매일 방문자수(목표)</th>
                    <td>
                        <input type="number" name="adm_daily_visit_target" id="adm-daily-visit-target" class="frm_input" value="<?php echo (int)$daily_visit_target; ?>" min="0" style="width:120px;" placeholder="예: 10000">
                        <p class="frm_info">00시~24시 기준, 시간에 비례해 오늘 방문자수에 가산됩니다. 0이면 비활성화.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <button type="button" id="adm-config-save" class="btn btn_02">설정 저장</button>
    </div>
</form>

<script>
(function(){
    var AJAX = <?php echo json_encode($CHAT_AJAX_URL, JSON_UNESCAPED_SLASHES); ?>;

    function post(body){
        return fetch(AJAX, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body
        }).then(function(r){
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        }).then(function(t){
            try { return JSON.parse(t); }
            catch(e) { console.error('[CHAT-ADM] 응답 파싱 실패:', t); throw new Error('JSON parse error'); }
        });
    }

    var btnFreeze = document.getElementById('adm-freeze-apply');
    if (btnFreeze) {
        btnFreeze.addEventListener('click', function(){
            var freeze = document.getElementById('adm_freeze').checked ? 1 : 0;
            post('act=admin_freeze&freeze=' + encodeURIComponent(freeze)).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '적용 실패'); return; }
                alert('적용되었습니다.');
            }).catch(function(e){ alert('오류: ' + e.message); console.error('[CHAT-ADM] freeze error:', e); });
        });
    }

    var btnBan = document.getElementById('adm-ban-apply');
    if (btnBan) {
        btnBan.addEventListener('click', function(){
            var nick = (document.getElementById('adm-ban-nick').value || '').trim();
            var min = document.getElementById('adm-ban-min').value || '10';
            var reason = (document.getElementById('adm-ban-reason').value || '').trim();
            if (!nick) { alert('닉네임을 입력하세요.'); return; }
            if (!confirm('[' + nick + '] 닉네임을 밴 처리할까요?')) return;
            post('act=admin_ban_nick&nick=' + encodeURIComponent(nick) + '&min=' + encodeURIComponent(min) + '&reason=' + encodeURIComponent(reason)).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '밴 실패'); return; }
                alert('밴 처리 완료');
            }).catch(function(e){ alert('오류: ' + e.message); console.error('[CHAT-ADM] ban error:', e); });
        });
    }

    var btnClear = document.getElementById('adm-clear-chat');
    if (btnClear) {
        btnClear.addEventListener('click', function(){
            if (!confirm('전체 채팅을 삭제할까요? (복구 불가)')) return;
            post('act=admin_clear').then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '삭제 실패'); return; }
                alert('삭제 완료');
            }).catch(function(e){ alert('오류: ' + e.message); console.error('[CHAT-ADM] clear error:', e); });
        });
    }

    var btnSave = document.getElementById('adm-config-save');
    if (btnSave) {
        btnSave.addEventListener('click', function(){
            var spamSec = document.getElementById('adm-spam-sec').value || '2';
            var repeatSec = document.getElementById('adm-repeat-sec').value || '30';
            var reportLim = document.getElementById('adm-report-limit').value || '10';
            var autobanMin = document.getElementById('adm-autoban-min').value || '10';
            var onlineFakeAdd = document.getElementById('adm-online-fake-add').value || '0';
            var dailyVisitTarget = document.getElementById('adm-daily-visit-target') ? (document.getElementById('adm-daily-visit-target').value || '0') : '0';
            var noticeText = document.getElementById('adm-notice-text') ? (document.getElementById('adm-notice-text').value || '') : '';
            var ruleText = document.getElementById('adm-rule-text') ? (document.getElementById('adm-rule-text').value || '') : '';
            var badwords = document.getElementById('adm-badwords') ? (document.getElementById('adm-badwords').value || '') : '';
            var leftLoginNotice = document.getElementById('adm-left-login-notice') ? (document.getElementById('adm-left-login-notice').value || '') : '';
            var tickerSpeed = document.getElementById('adm-left-login-ticker-speed') ? (document.getElementById('adm-left-login-ticker-speed').value || '30') : '30';
            var body = 'act=admin_config_save&spam_sec=' + encodeURIComponent(spamSec) + '&repeat_sec=' + encodeURIComponent(repeatSec) + '&report_limit=' + encodeURIComponent(reportLim) + '&autoban_min=' + encodeURIComponent(autobanMin) + '&online_fake_add=' + encodeURIComponent(onlineFakeAdd) + '&daily_visit_target=' + encodeURIComponent(dailyVisitTarget) + '&notice_text=' + encodeURIComponent(noticeText) + '&rule_text=' + encodeURIComponent(ruleText) + '&badwords=' + encodeURIComponent(badwords) + '&left_login_notice=' + encodeURIComponent(leftLoginNotice) + '&left_login_ticker_speed=' + encodeURIComponent(tickerSpeed);
            post(body).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '저장 실패'); return; }
                alert('저장 완료');
            }).catch(function(e){ alert('오류: ' + e.message); console.error('[CHAT-ADM] config save error:', e); });
        });
    }
})();
</script>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
