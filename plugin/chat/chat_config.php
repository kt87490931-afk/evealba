<?php
// /plugin/chat/adm/chat_config.php
$sub_menu = '900100'; // 메뉴권한코드, 필요에 맞게 조정
include_once(__DIR__ . '/../../common.php');
include_once(__DIR__ . '/../_common.php');

auth_check($auth[$sub_menu], 'r');

// 저장 처리
if (isset($_POST['w']) && $_POST['w'] === 'u') {
    check_admin_token();

    $freeze = isset($_POST['cf_freeze']) ? 1 : 0;

    $sql = "UPDATE {$g5['chat_config_table']} SET
        cf_title        = '".sql_real_escape_string($_POST['cf_title'])."',
        cf_freeze       = '{$freeze}',
        cf_online_count = '".intval($_POST['cf_online_count'])."',
        cf_box_width    = '".intval($_POST['cf_box_width'])."',
        cf_box_height   = '".intval($_POST['cf_box_height'])."',
        cf_tab1_title   = '".sql_real_escape_string($_POST['cf_tab1_title'])."',
        cf_tab2_title   = '".sql_real_escape_string($_POST['cf_tab2_title'])."',
        cf_notice_text  = '".sql_real_escape_string($_POST['cf_notice_text'])."',
        cf_rule_text    = '".sql_real_escape_string($_POST['cf_rule_text'])."',
        cf_position     = '".sql_real_escape_string($_POST['cf_position'])."',
        cf_top          = '".intval($_POST['cf_top'])."',
        cf_left         = '".intval($_POST['cf_left'])."'
        WHERE cf_id = 1";
    sql_query($sql);

    goto_url('./chat_config.php');
}

$cfg = sql_fetch("SELECT * FROM {$g5['chat_config_table']} WHERE cf_id = 1");

include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<form method="post">
<input type="hidden" name="w" value="u">
<?php echo get_admin_token(); ?>

<div class="tbl_frm01 tbl_wrap">
<table>
<tr>
  <th scope="row">채팅창 제목</th>
  <td><input type="text" name="cf_title" value="<?php echo get_text($cfg['cf_title']); ?>" class="frm_input" size="30"></td>
</tr>
<tr>
  <th scope="row">채팅 얼리기</th>
  <td><label><input type="checkbox" name="cf_freeze" value="1" <?php echo $cfg['cf_freeze']?'checked':''; ?>> 전체 채팅 잠금</label></td>
</tr>
<tr>
  <th scope="row">표시 접속자 수</th>
  <td><input type="number" name="cf_online_count" value="<?php echo (int)$cfg['cf_online_count']; ?>" class="frm_input"> 명</td>
</tr>
<tr>
  <th scope="row">가로(px)</th>
  <td><input type="number" name="cf_box_width" value="<?php echo (int)$cfg['cf_box_width']; ?>" class="frm_input"></td>
</tr>
<tr>
  <th scope="row">세로(px)</th>
  <td><input type="number" name="cf_box_height" value="<?php echo (int)$cfg['cf_box_height']; ?>" class="frm_input"></td>
</tr>
<tr>
  <th scope="row">탭1 제목</th>
  <td><input type="text" name="cf_tab1_title" value="<?php echo get_text($cfg['cf_tab1_title']); ?>" class="frm_input" size="30"></td>
</tr>
<tr>
  <th scope="row">탭2 제목</th>
  <td><input type="text" name="cf_tab2_title" value="<?php echo get_text($cfg['cf_tab2_title']); ?>" class="frm_input" size="30"></td>
</tr>
<tr>
  <th scope="row">공지문구 (노란바)</th>
  <td><input type="text" name="cf_notice_text" value="<?php echo get_text($cfg['cf_notice_text']); ?>" class="frm_input" size="60"></td>
</tr>
<tr>
  <th scope="row">채팅규정 내용</th>
  <td>
    <textarea name="cf_rule_text" rows="6" style="width:100%;"><?php echo get_text($cfg['cf_rule_text']); ?></textarea>
    <p class="frm_info">탭에서 "채팅규정" 선택 시 이 내용이 표시됩니다.</p>
  </td>
</tr>
<tr>
  <th scope="row">채팅창 위치 방식</th>
  <td>
    <select name="cf_position">
      <option value="static"  <?php echo $cfg['cf_position']=='static'?'selected':''; ?>>static (페이지 안)</option>
      <option value="fixed"   <?php echo $cfg['cf_position']=='fixed'?'selected':''; ?>>fixed (화면 고정)</option>
      <option value="absolute"<?php echo $cfg['cf_position']=='absolute'?'selected':''; ?>>absolute</option>
    </select>
    <span class="frm_info">fixed/absolute 선택 시 아래 top/left 값 사용</span>
  </td>
</tr>
<tr>
  <th scope="row">top 위치(px)</th>
  <td><input type="number" name="cf_top" value="<?php echo (int)$cfg['cf_top']; ?>" class="frm_input"></td>
</tr>
<tr>
  <th scope="row">left 위치(px)</th>
  <td><input type="number" name="cf_left" value="<?php echo (int)$cfg['cf_left']; ?>" class="frm_input"></td>
</tr>
</table>
</div>

<div class="btn_confirm01 btn_confirm">
  <input type="submit" value="저장" class="btn_submit">
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
