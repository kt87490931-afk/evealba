<?php if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';

$ongoing_list = array();
if ($is_member) {
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    if ($tb_check && sql_num_rows($tb_check)) {
        $mb_id_esc = addslashes($member['mb_id']);
        $today = date('Y-m-d');
        $sql = "SELECT jr_id, jr_title, jr_subject_display, jr_ad_period, jr_jump_remain, jr_jump_used, jr_jump_total
            FROM g5_jobs_register
            WHERE mb_id = '{$mb_id_esc}' AND jr_status = 'ongoing' AND jr_end_date >= '{$today}'
            ORDER BY jr_datetime DESC";
        $result = sql_query($sql, false);
        if ($result) {
            while ($r = sql_fetch_array($result)) {
                $ongoing_list[] = $r;
            }
        }
    }
}
?>
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/skin/board/eve_skin/style.css?v=<?php echo @filemtime(G5_THEME_PATH.'/skin/board/eve_skin/style.css'); ?>">

<div class="jp-page-wrap">

<div class="page-title-bar">
  <h2 class="page-title">🔝 점프옵션 구매하기</h2>
</div>

<?php if (empty($ongoing_list)) { ?>
<div class="jp-empty">
  <p>진행중인 광고가 없습니다.</p>
  <p>채용공고를 등록하고 결제를 완료하면 점프옵션을 구매할 수 있습니다.</p>
  <a href="<?php echo $jobs_base_url; ?>/jobs_register.php" class="jp-btn-go">✏️ 채용공고 등록하기</a>
</div>
<?php } else { ?>

<div class="jp-section">
  <div class="jp-header">
    <h2 class="jp-title">🔝 점프옵션 구매하기</h2>
    <p class="jp-sub">채용정보를 리스트 최상단으로 점프시킬 수 있는 옵션입니다.<br>점프 1회 사용 시 결제한 모든 광고 유형 리스트에서 동시에 최상단으로 이동합니다.</p>
  </div>

  <div class="jp-select-row">
    <label class="jp-label">광고 선택</label>
    <select id="jp-jr-select" class="jp-select">
<?php foreach ($ongoing_list as $_ol) { ?>
      <option value="<?php echo (int)$_ol['jr_id']; ?>">
        <?php echo htmlspecialchars($_ol['jr_title'] ?: $_ol['jr_subject_display'] ?: '[제목없음]'); ?>
        (<?php echo (int)$_ol['jr_ad_period']; ?>일 / 잔여 <?php echo number_format((int)$_ol['jr_jump_remain']); ?>회)
      </option>
<?php } ?>
    </select>
  </div>

  <div class="jp-current-info">
<?php foreach ($ongoing_list as $idx => $_ol) { ?>
    <div class="jp-ad-info" data-jr-id="<?php echo (int)$_ol['jr_id']; ?>" style="<?php echo $idx > 0 ? 'display:none' : ''; ?>">
      <div class="jp-ad-stat"><span>총 부여</span><strong><?php echo number_format((int)$_ol['jr_jump_total']); ?>회</strong></div>
      <div class="jp-ad-stat"><span>사용</span><strong><?php echo number_format((int)$_ol['jr_jump_used']); ?>회</strong></div>
      <div class="jp-ad-stat"><span>잔여</span><strong class="pink"><?php echo number_format((int)$_ol['jr_jump_remain']); ?>회</strong></div>
    </div>
<?php } ?>
  </div>

  <table class="jp-table">
    <thead>
      <tr>
        <th class="jp-th-service">서비스</th>
        <th>유형</th>
        <th>기간/횟수</th>
        <th>금액</th>
        <th>신청</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td rowspan="5" class="jp-td-service">
          <strong>채용정보 점프하기</strong>
          <p>점프 1회 = 결제한 모든 리스트 동시 최상단 이동<br>(줄광고, 우대, 프리미엄, 스페셜, 급구, 추천)</p>
        </td>
        <td>클릭별</td><td><strong>200</strong> 회</td>
        <td class="jp-price">10,000 원</td>
        <td><input type="checkbox" class="jp-chk" data-pkg="200" data-amt="10000"></td>
      </tr>
      <tr>
        <td>클릭별</td><td><strong>450</strong> 회</td>
        <td class="jp-price">20,000 원</td>
        <td><input type="checkbox" class="jp-chk" data-pkg="450" data-amt="20000"></td>
      </tr>
      <tr>
        <td>클릭별</td><td><strong>700</strong> 회</td>
        <td class="jp-price">30,000 원</td>
        <td><input type="checkbox" class="jp-chk" data-pkg="700" data-amt="30000"></td>
      </tr>
      <tr>
        <td>클릭별</td><td><strong>1,200</strong> 회</td>
        <td class="jp-price">50,000 원</td>
        <td><input type="checkbox" class="jp-chk" data-pkg="1200" data-amt="50000"></td>
      </tr>
      <tr>
        <td>클릭별</td><td><strong>2,000</strong> 회</td>
        <td class="jp-price">80,000 원</td>
        <td><input type="checkbox" class="jp-chk" data-pkg="2000" data-amt="80000"></td>
      </tr>
    </tbody>
  </table>

  <div class="jp-total-bar">
    <span>총 신청 금액</span>
    <strong id="jp-total-amount">0원</strong>
  </div>
  <div class="jp-btn-wrap">
    <button type="button" class="jp-btn-pay" id="jp-btn-pay" onclick="doPurchaseJump()">💳 결제하기</button>
  </div>
</div>

<?php } ?>
</div>

<style>
.jp-page-wrap{max-width:958px;width:100%;font-family:'Noto Sans KR',sans-serif}
.jp-empty{background:#fff;border-radius:14px;padding:50px 20px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.08)}
.jp-empty p{font-size:14px;color:#888;margin:0 0 8px}
.jp-btn-go{display:inline-block;margin-top:16px;padding:10px 24px;background:linear-gradient(135deg,#FF1B6B,#C90050);color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px}
.jp-section{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}
.jp-header{background:linear-gradient(135deg,#6B21A8,#9333EA);padding:20px 24px;color:#fff}
.jp-title{font-size:16px;font-weight:900;margin:0 0 6px}
.jp-sub{font-size:12px;color:rgba(255,255,255,.8);margin:0;line-height:1.6}
.jp-select-row{padding:14px 24px;border-bottom:1px solid #f0e0e8;display:flex;align-items:center;gap:12px}
.jp-label{font-size:13px;font-weight:700;color:#333;white-space:nowrap}
.jp-select{flex:1;padding:8px 12px;border:1.5px solid #f0e0e8;border-radius:8px;font-size:13px;background:#fff;outline:none}
.jp-current-info{padding:12px 24px;border-bottom:1px solid #f0e0e8;display:flex;gap:8px}
.jp-ad-info{display:flex;gap:24px;width:100%}
.jp-ad-stat{display:flex;flex-direction:column;align-items:center;gap:2px}
.jp-ad-stat span{font-size:10px;color:#999}
.jp-ad-stat strong{font-size:14px;color:#333;font-weight:700}
.jp-ad-stat strong.pink{color:#FF1B6B}
.jp-table{width:100%;border-collapse:collapse}
.jp-table th{background:linear-gradient(135deg,#6B21A8,#9333EA);color:#fff;padding:10px 12px;font-size:12px;font-weight:700;text-align:center}
.jp-th-service{text-align:left;width:40%}
.jp-table td{padding:10px 12px;text-align:center;border-bottom:1px solid #f0e0e8;font-size:13px;color:#333}
.jp-td-service{text-align:left;vertical-align:top;padding:16px}
.jp-td-service strong{display:block;font-size:14px;margin-bottom:6px}
.jp-td-service p{font-size:11px;color:#888;line-height:1.5;margin:0}
.jp-price{color:#FF1B6B;font-weight:900;font-size:14px}
.jp-chk{width:18px;height:18px;accent-color:#6B21A8;cursor:pointer}
.jp-total-bar{display:flex;justify-content:space-between;align-items:center;padding:14px 24px;background:linear-gradient(135deg,#6B21A8,#9333EA)}
.jp-total-bar span{font-size:14px;color:rgba(255,255,255,.9)}
.jp-total-bar strong{font-size:20px;color:#fff;font-weight:900}
.jp-btn-wrap{padding:16px 24px;text-align:center}
.jp-btn-pay{padding:12px 40px;border:none;border-radius:10px;background:linear-gradient(135deg,#6B21A8,#9333EA);color:#fff;font-size:15px;font-weight:900;cursor:pointer;transition:all .2s;box-shadow:0 4px 15px rgba(107,33,168,.3)}
.jp-btn-pay:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(107,33,168,.4)}
@media(max-width:600px){
  .jp-select-row{flex-direction:column;align-items:stretch}
  .jp-ad-info{justify-content:space-around}
  .jp-table th:first-child,.jp-table td.jp-td-service{display:none}
}
</style>

<script>
(function(){
  var sel = document.getElementById('jp-jr-select');
  if (sel) {
    sel.addEventListener('change', function(){
      var jrId = this.value;
      document.querySelectorAll('.jp-ad-info').forEach(function(el){
        el.style.display = el.getAttribute('data-jr-id') === jrId ? '' : 'none';
      });
    });
  }
  var chks = document.querySelectorAll('.jp-chk');
  chks.forEach(function(c){
    c.addEventListener('change', function(){
      var total = 0;
      chks.forEach(function(cc){if(cc.checked) total += parseInt(cc.getAttribute('data-amt')||0);});
      document.getElementById('jp-total-amount').textContent = total.toLocaleString()+'원';
    });
  });
})();
function doPurchaseJump(){
  var jrId = document.getElementById('jp-jr-select').value;
  var chks = document.querySelectorAll('.jp-chk:checked');
  if(chks.length===0){alert('구매할 패키지를 선택해 주세요.');return;}
  var promises = [];
  chks.forEach(function(c){
    var pkg = c.getAttribute('data-pkg');
    promises.push(
      fetch('<?php echo rtrim(G5_URL,"/"); ?>/jobs_jump_purchase.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'jr_id='+jrId+'&package='+pkg,
        credentials:'same-origin'
      }).then(function(r){return r.json();})
    );
  });
  Promise.all(promises).then(function(results){
    var msgs = results.map(function(r){return r.msg;}).join('\n');
    alert(msgs);
    chks.forEach(function(c){c.checked=false;});
    document.getElementById('jp-total-amount').textContent='0원';
  }).catch(function(){alert('구매 처리 중 오류가 발생했습니다.');});
}
</script>
