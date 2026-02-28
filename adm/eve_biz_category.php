<?php
/**
 * 어드민 - 허용 업태/종목 관리
 * 기업회원 승인 시 OCR로 추출된 업태/종목이 허용 목록에 있는지 판단
 */
$sub_menu = '910400';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$token = get_session('ss_admin_token') ?: get_admin_token();

$tb = 'g5_eve_biz_category';
$tb_check = sql_query("SHOW TABLES LIKE '{$tb}'", false);
if (!$tb_check || !sql_num_rows($tb_check)) {
    sql_query("CREATE TABLE `{$tb}` (
        `cat_id` int NOT NULL AUTO_INCREMENT,
        `cat_type` enum('type','item') NOT NULL DEFAULT 'item' COMMENT 'type=업태, item=종목',
        `cat_name` varchar(100) NOT NULL DEFAULT '',
        `cat_enabled` tinyint NOT NULL DEFAULT 1,
        `cat_datetime` datetime DEFAULT NULL,
        PRIMARY KEY (`cat_id`),
        KEY `idx_type_enabled` (`cat_type`, `cat_enabled`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);

    $seed_types = array(
        '숙박 및 음식점업', '음식점업', '서비스업',
        '예술, 스포츠 및 여가관련 서비스업', '개인서비스업',
        '협회 및 단체, 수리 및 기타 개인서비스업'
    );
    $seed_items = array(
        '유흥주점', '유흥주점업', '단란주점', '단란주점업',
        '일반음식점', '일반음식점업', '휴게음식점', '휴게음식점업',
        '노래연습장', '노래연습장업', '무도장', '무도유흥주점',
        '안마시술소', '안마업', '마사지업', '마사지',
        '이용업', '미용업', '목욕장업',
        '주점업', '주류판매', '간이주점',
        '실내체육시설업', '관광유흥음식점업', '외국인전용유흥음식점업',
        '직업소개소', '직업정보제공사업', '가라오케'
    );
    $now = G5_TIME_YMDHIS;
    foreach ($seed_types as $name) {
        $n = sql_escape_string($name);
        sql_query("INSERT INTO `{$tb}` SET cat_type='type', cat_name='{$n}', cat_enabled=1, cat_datetime='{$now}'", false);
    }
    foreach ($seed_items as $name) {
        $n = sql_escape_string($name);
        sql_query("INSERT INTO `{$tb}` SET cat_type='item', cat_name='{$n}', cat_enabled=1, cat_datetime='{$now}'", false);
    }
}

$g5['title'] = '허용 업태/종목 관리';
require_once './admin.head.php';

$types = array();
$items = array();
$result = sql_query("SELECT * FROM `{$tb}` ORDER BY cat_type, cat_id");
while ($row = sql_fetch_array($result)) {
    if ($row['cat_type'] === 'type') $types[] = $row;
    else $items[] = $row;
}
?>

<style>
.eve-cat-wrap { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
@media(max-width:768px) { .eve-cat-wrap { grid-template-columns:1fr; } }
.eve-cat-box { background:#fff; border:1px solid #eee; border-radius:10px; padding:16px; }
.eve-cat-title { font-size:16px; font-weight:900; margin-bottom:12px; padding-bottom:8px; border-bottom:2px solid #FF1B6B; }
.eve-cat-list { list-style:none; padding:0; margin:0 0 12px; }
.eve-cat-list li { display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid #f5f5f5; font-size:13px; }
.eve-cat-list li:last-child { border-bottom:none; }
.eve-cat-name { flex:1; }
.eve-cat-toggle { padding:3px 10px; border:none; border-radius:4px; font-size:11px; font-weight:700; cursor:pointer; }
.eve-cat-toggle.on { background:#E8F5E9; color:#2E7D32; }
.eve-cat-toggle.off { background:#FFEBEE; color:#C62828; }
.eve-cat-del { padding:3px 8px; border:1px solid #ddd; border-radius:4px; font-size:11px; cursor:pointer; background:#fff; color:#999; }
.eve-cat-del:hover { background:#FFEBEE; color:#C62828; border-color:#C62828; }
.eve-cat-add { display:flex; gap:6px; }
.eve-cat-add input { flex:1; padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px; }
.eve-cat-add button { padding:6px 14px; border:none; border-radius:6px; background:#FF1B6B; color:#fff; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap; }
.eve-cat-count { font-size:12px; color:#999; margin-left:8px; }
</style>

<div class="local_desc01 local_desc">
    <p>기업회원 사업자등록증의 <strong>업태</strong>와 <strong>종목</strong>이 아래 허용 목록에 포함되어야 승인 가능합니다.<br>
    OCR로 인식된 업태/종목과 <strong>부분 일치(포함)</strong> 방식으로 판단합니다.</p>
</div>

<div class="eve-cat-wrap">
    <div class="eve-cat-box">
        <div class="eve-cat-title">📋 허용 업태 <span class="eve-cat-count">(<?php echo count($types); ?>개)</span></div>
        <ul class="eve-cat-list" id="list-type">
        <?php foreach ($types as $t) { ?>
            <li data-id="<?php echo $t['cat_id']; ?>">
                <span class="eve-cat-name"><?php echo htmlspecialchars($t['cat_name']); ?></span>
                <button class="eve-cat-toggle <?php echo $t['cat_enabled'] ? 'on' : 'off'; ?>" onclick="eveCatToggle(<?php echo $t['cat_id']; ?>, this)"><?php echo $t['cat_enabled'] ? '활성' : '비활성'; ?></button>
                <button class="eve-cat-del" onclick="eveCatDel(<?php echo $t['cat_id']; ?>, this)">삭제</button>
            </li>
        <?php } ?>
        </ul>
        <div class="eve-cat-add">
            <input type="text" id="add-type-name" placeholder="새 업태 입력">
            <button onclick="eveCatAdd('type')">+ 추가</button>
        </div>
    </div>

    <div class="eve-cat-box">
        <div class="eve-cat-title">📋 허용 종목 <span class="eve-cat-count">(<?php echo count($items); ?>개)</span></div>
        <ul class="eve-cat-list" id="list-item">
        <?php foreach ($items as $t) { ?>
            <li data-id="<?php echo $t['cat_id']; ?>">
                <span class="eve-cat-name"><?php echo htmlspecialchars($t['cat_name']); ?></span>
                <button class="eve-cat-toggle <?php echo $t['cat_enabled'] ? 'on' : 'off'; ?>" onclick="eveCatToggle(<?php echo $t['cat_id']; ?>, this)"><?php echo $t['cat_enabled'] ? '활성' : '비활성'; ?></button>
                <button class="eve-cat-del" onclick="eveCatDel(<?php echo $t['cat_id']; ?>, this)">삭제</button>
            </li>
        <?php } ?>
        </ul>
        <div class="eve-cat-add">
            <input type="text" id="add-item-name" placeholder="새 종목 입력">
            <button onclick="eveCatAdd('item')">+ 추가</button>
        </div>
    </div>
</div>

<script>
var _catUrl = '<?php echo G5_ADMIN_URL; ?>/eve_biz_category_update.php';
var _catToken = '<?php echo $token; ?>';

function eveCatAdd(catType) {
    var input = document.getElementById('add-' + catType + '-name');
    var name = input.value.trim();
    if (!name) { alert('이름을 입력해주세요.'); return; }
    var fd = new FormData();
    fd.append('token', _catToken);
    fd.append('action', 'add');
    fd.append('cat_type', catType);
    fd.append('cat_name', name);
    fetch(_catUrl, { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.ok) { location.reload(); }
        else { alert(d.msg || '추가 실패'); }
    });
}

function eveCatToggle(catId, btn) {
    var fd = new FormData();
    fd.append('token', _catToken);
    fd.append('action', 'toggle');
    fd.append('cat_id', catId);
    fetch(_catUrl, { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.ok) {
            btn.textContent = d.enabled ? '활성' : '비활성';
            btn.className = 'eve-cat-toggle ' + (d.enabled ? 'on' : 'off');
        }
    });
}

function eveCatDel(catId, btn) {
    if (!confirm('이 항목을 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('token', _catToken);
    fd.append('action', 'delete');
    fd.append('cat_id', catId);
    fetch(_catUrl, { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.ok) { btn.closest('li').remove(); }
        else { alert(d.msg || '삭제 실패'); }
    });
}
</script>

<?php require_once './admin.tail.php'; ?>
