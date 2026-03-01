<?php
/**
 * EveAlba 공지/규정/금칙어 - Gnuboard Admin
 * 경로: /adm/scorepoint/scorepoint_chat_notice.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910501';
if ($sub_menu === '') {
    $sub_menu = '910501';
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

if (defined('G5_EDITOR_LIB') && is_file(G5_EDITOR_LIB)) {
    require_once G5_EDITOR_LIB;
}

if (!isset($is_admin) || !$is_admin) {
    alert('관리자만 접근 가능합니다.');
}
auth_check_menu($auth, $sub_menu, 'w');

$tbl_cfg = isset($g5['chat_config_table']) ? $g5['chat_config_table'] : 'g5_chat_config';
$cfg = sql_fetch(" SELECT * FROM {$tbl_cfg} LIMIT 1 ");
if (!$cfg) {
    $cfg = array();
}

$notice = isset($cfg['cf_notice_text']) ? $cfg['cf_notice_text'] : (isset($cfg['cf_notice_txt']) ? $cfg['cf_notice_txt'] : '');
$rule = isset($cfg['cf_rule_text']) ? $cfg['cf_rule_text'] : '';
$bad = isset($cfg['cf_badwords']) ? $cfg['cf_badwords'] : '';

$spam_sec = isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2;
$repeat_sec = isset($cfg['cf_repeat_sec']) ? (int)$cfg['cf_repeat_sec'] : 30;
$report_lim = isset($cfg['cf_report_limit']) ? (int)$cfg['cf_report_limit'] : 10;
$autoban_min = isset($cfg['cf_autoban_min']) ? (int)$cfg['cf_autoban_min'] : 10;
$online_fake_add = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;

$CHAT_AJAX_URL = G5_PLUGIN_URL . '/chat/chat_ajax.php';
$base_url = G5_ADMIN_URL . '/scorepoint/';

$g5['title'] = '공지/규정/금칙어';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>
<style>
.sp-chat-form tbody th,
.sp-chat-form tbody td { text-align: left !important; }
.sp-chat-form tbody th { vertical-align: middle; }
</style>

<div class="local_ov01 local_ov">
    <a href="<?php echo $base_url; ?>scorepoint_chat_manage.php?sub_menu=910500" class="ov_listall">채팅관리</a>
    <span class="btn_ov01"><span class="ov_txt">공지/규정/금칙어</span></span>
    <a href="<?php echo $base_url; ?>scorepoint_chat_reports.php?sub_menu=910700" class="btn_ov01">최근신고</a>
    <a href="<?php echo $base_url; ?>scorepoint_chat_banlist.php?sub_menu=910600" class="btn_ov01">밴리스트</a>
</div>

<form name="fchat_notice" id="fchat_notice" method="post" onsubmit="return false;">
    <input type="hidden" name="form_check" value="1">

    <div class="tbl_head01 tbl_wrap sp-chat-form" style="margin-top:16px;">
        <table>
            <caption class="sound_only">공지/규정/금칙어</caption>
            <colgroup>
                <col style="width:140px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">공지(상단 띠)</th>
                    <td>
                        <?php
                        if (function_exists('editor_html')) {
                            echo editor_html('adm_notice_text', html_purifier($notice), true);
                        } else {
                            echo '<textarea name="adm_notice_text" id="adm_notice_text" class="frm_input" rows="4" style="width:100%;">' . htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') . '</textarea>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">채팅규정</th>
                    <td>
                        <?php
                        if (function_exists('editor_html')) {
                            echo editor_html('adm_rule_text', html_purifier($rule), true);
                        } else {
                            echo '<textarea name="adm_rule_text" id="adm_rule_text" class="frm_input" rows="8" style="width:100%;">' . htmlspecialchars($rule, ENT_QUOTES, 'UTF-8') . '</textarea>';
                        }
                        ?>
                        <p class="frm_info">채팅규정 탭에 표시됩니다.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">금칙어</th>
                    <td>
                        <textarea name="adm_badwords" id="adm_badwords" class="frm_input" rows="6" style="width:100%;"><?php echo htmlspecialchars($bad, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <p class="frm_info">금칙어 목록: <strong>쉼표(,)</strong> 또는 <strong>줄바꿈</strong>으로 구분하여 입력하세요. 포함 시 전송 차단.</p>
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
    var SPAM_SEC = <?php echo (int)$spam_sec; ?>;
    var REPEAT_SEC = <?php echo (int)$repeat_sec; ?>;
    var REPORT_LIMIT = <?php echo (int)$report_lim; ?>;
    var AUTOBAN_MIN = <?php echo (int)$autoban_min; ?>;
    var ONLINE_FAKE_ADD = <?php echo (int)$online_fake_add; ?>;

    function post(body){
        return fetch(AJAX, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body
        }).then(function(r){ return r.json(); });
    }

    function getEditorContent(id){
        var el = document.getElementById(id);
        if (!el) return '';
        if (typeof oEditors !== 'undefined' && oEditors.getById && oEditors.getById[id]) {
            try { oEditors.getById[id].exec('UPDATE_CONTENTS_FIELD', []); } catch(e){}
        }
        return (el.value || '').trim();
    }

    var btn = document.getElementById('adm-config-save');
    if (btn) {
        btn.addEventListener('click', function(){
            var noticeText = getEditorContent('adm_notice_text');
            var ruleText = getEditorContent('adm_rule_text');
            var badwords = (document.getElementById('adm_badwords').value || '').trim();
            var body = 'act=admin_config_save&spam_sec=' + encodeURIComponent(String(SPAM_SEC)) + '&repeat_sec=' + encodeURIComponent(String(REPEAT_SEC)) + '&report_limit=' + encodeURIComponent(String(REPORT_LIMIT)) + '&autoban_min=' + encodeURIComponent(String(AUTOBAN_MIN)) + '&online_fake_add=' + encodeURIComponent(String(ONLINE_FAKE_ADD)) + '&notice_text=' + encodeURIComponent(noticeText) + '&rule_text=' + encodeURIComponent(ruleText) + '&badwords=' + encodeURIComponent(badwords);
            post(body).then(function(j){
                if (!j || j.ok !== 1) { alert(j && j.msg ? j.msg : '저장 실패'); return; }
                alert('저장 완료');
            });
        });
    }
})();
</script>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
