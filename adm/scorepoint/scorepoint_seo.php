<?php
/**
 * ScorePoint SEO 최적화 - Gnuboard Admin
 * - 메타태그·사이트맵·캐노니컬 등 관리
 * - 어드민 메뉴: ScorePoint > SEO 최적화
 *
 * 경로: /adm/scorepoint/scorepoint_seo.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910900';
if ($sub_menu === '') {
    $sub_menu = '910900';
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

// sp_seo_config 테이블 없으면 생성
$sp_seo_table = 'sp_seo_config';
$chk = @sql_fetch("SHOW TABLES LIKE '{$sp_seo_table}'", false);
if (!is_array($chk) || count($chk) === 0) {
    $sql = "CREATE TABLE IF NOT EXISTS `{$sp_seo_table}` (
      `id`                          int            NOT NULL DEFAULT 1,
      `sp_meta_description`         text           NULL,
      `sp_meta_keywords`            varchar(500)   NOT NULL DEFAULT '',
      `sp_og_title`                 varchar(255)   NOT NULL DEFAULT '',
      `sp_og_description`          text           NULL,
      `sp_og_image`                 varchar(500)   NOT NULL DEFAULT '',
      `sp_og_type`                  varchar(50)    NOT NULL DEFAULT 'website',
      `sp_twitter_card`             varchar(50)    NOT NULL DEFAULT 'summary_large_image',
      `sp_google_site_verification` varchar(100)   NOT NULL DEFAULT '',
      `sp_canonical_url`            varchar(255)   NOT NULL DEFAULT '',
      `sp_sitemap_use`              tinyint(1)    NOT NULL DEFAULT 1,
      `sp_sitemap_list_max_pages`   int            NOT NULL DEFAULT 10,
      `sp_robots_txt_use`           tinyint(1)    NOT NULL DEFAULT 1,
      `sp_schema_organization`      text           NULL,
      `sp_updated_at`               datetime       NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    sql_query($sql, false);
    sql_query("INSERT IGNORE INTO `{$sp_seo_table}` (`id`) VALUES (1)", false);
} else {
    // 기존 테이블에 OG/Twitter·updated_at 컬럼 없으면 추가 (하위 호환)
    $col_chk = @sql_fetch("SHOW COLUMNS FROM `{$sp_seo_table}` LIKE 'sp_og_title'", false);
    if (!is_array($col_chk) || count($col_chk) === 0) {
        $alters = array(
            "ALTER TABLE `{$sp_seo_table}` ADD COLUMN `sp_og_title` varchar(255) NOT NULL DEFAULT '' AFTER `sp_meta_keywords`",
            "ALTER TABLE `{$sp_seo_table}` ADD COLUMN `sp_og_description` text AFTER `sp_og_title`",
            "ALTER TABLE `{$sp_seo_table}` ADD COLUMN `sp_og_type` varchar(50) NOT NULL DEFAULT 'website' AFTER `sp_og_image`",
            "ALTER TABLE `{$sp_seo_table}` ADD COLUMN `sp_twitter_card` varchar(50) NOT NULL DEFAULT 'summary_large_image' AFTER `sp_og_type`",
            "ALTER TABLE `{$sp_seo_table}` ADD COLUMN `sp_updated_at` datetime DEFAULT NULL AFTER `sp_schema_organization`",
        );
        foreach ($alters as $q) {
            @sql_query($q, false);
        }
    }
}

// 카테고리(그룹)·게시판별 SEO 테이블 없으면 생성
$sp_seo_board_table = 'sp_seo_board_config';
$sp_seo_group_table = 'sp_seo_group_config';
foreach (array(
    array($sp_seo_board_table, "CREATE TABLE IF NOT EXISTS `{$sp_seo_board_table}` ( `bo_table` varchar(50) NOT NULL, `sp_noindex` tinyint(1) NOT NULL DEFAULT 0, PRIMARY KEY (`bo_table`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4"),
    array($sp_seo_group_table, "CREATE TABLE IF NOT EXISTS `{$sp_seo_group_table}` ( `gr_id` varchar(50) NOT NULL, `sp_noindex` tinyint(1) NOT NULL DEFAULT 0, PRIMARY KEY (`gr_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4"),
) as $t) {
    $chk_t = @sql_fetch("SHOW TABLES LIKE '{$t[0]}'", false);
    if (!is_array($chk_t) || count($chk_t) === 0) {
        sql_query($t[1], false);
    }
}

// 저장 처리 (카테고리·게시판별 수집 차단)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sp_seo_board_form'])) {
    auth_check_menu($auth, $sub_menu, 'w');
    $gr_ids = array();
    $res_gr = sql_query("SELECT gr_id FROM {$g5['group_table']} ORDER BY gr_id", false);
    if ($res_gr) {
        while ($r = sql_fetch_array($res_gr)) {
            $gr_ids[] = $r['gr_id'];
        }
    }
    foreach ($gr_ids as $gr_id) {
        $gr_id_esc = sql_escape_string($gr_id);
        $v = (isset($_POST['sp_noindex_gr']) && is_array($_POST['sp_noindex_gr']) && isset($_POST['sp_noindex_gr'][$gr_id]) && $_POST['sp_noindex_gr'][$gr_id]) ? 1 : 0;
        sql_query("REPLACE INTO `{$sp_seo_group_table}` (gr_id, sp_noindex) VALUES ('{$gr_id_esc}', " . (int)$v . ")", false);
    }
    $bo_tables = array();
    $res_bo = sql_query("SELECT bo_table FROM {$g5['board_table']} ORDER BY gr_id, bo_table", false);
    if ($res_bo) {
        while ($r = sql_fetch_array($res_bo)) {
            $bo_tables[] = $r['bo_table'];
        }
    }
    foreach ($bo_tables as $bo_table) {
        $bo_esc = sql_escape_string($bo_table);
        $v = (isset($_POST['sp_noindex_bo']) && is_array($_POST['sp_noindex_bo']) && isset($_POST['sp_noindex_bo'][$bo_table]) && $_POST['sp_noindex_bo'][$bo_table]) ? 1 : 0;
        sql_query("REPLACE INTO `{$sp_seo_board_table}` (bo_table, sp_noindex) VALUES ('{$bo_esc}', " . (int)$v . ")", false);
    }
    $url = G5_ADMIN_URL . '/scorepoint/scorepoint_seo.php?sub_menu=' . $sub_menu;
    alert('카테고리·게시판별 수집 설정이 저장되었습니다.', $url);
    exit;
}

// 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sp_seo_form_check'])) {
    auth_check_menu($auth, $sub_menu, 'w');
    $sp_meta_description = isset($_POST['sp_meta_description']) ? trim($_POST['sp_meta_description']) : '';
    $sp_meta_keywords = isset($_POST['sp_meta_keywords']) ? trim($_POST['sp_meta_keywords']) : '';
    $sp_og_title = isset($_POST['sp_og_title']) ? trim($_POST['sp_og_title']) : '';
    $sp_og_description = isset($_POST['sp_og_description']) ? trim($_POST['sp_og_description']) : '';
    $sp_og_image = isset($_POST['sp_og_image']) ? trim($_POST['sp_og_image']) : '';

    // OG 이미지 첨부 업로드 처리
    if (defined('G5_DATA_PATH') && defined('G5_DATA_URL') && isset($_FILES['sp_og_image_file']) && $_FILES['sp_og_image_file']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $max_size = 2 * 1024 * 1024; // 2MB
        $fn = $_FILES['sp_og_image_file']['name'];
        $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext, true) && $_FILES['sp_og_image_file']['size'] <= $max_size) {
            $upload_dir = G5_DATA_PATH . '/seo_og';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0755, true);
                if (is_file($upload_dir . '/.htaccess') === false) {
                    @file_put_contents($upload_dir . '/.htaccess', "Order Deny,Allow\nAllow from all");
                }
            }
            if (is_dir($upload_dir) && is_writable($upload_dir)) {
                $new_name = 'og_image_' . date('YmdHis') . '.' . $ext;
                $dest = $upload_dir . '/' . $new_name;
                if (@move_uploaded_file($_FILES['sp_og_image_file']['tmp_name'], $dest)) {
                    $sp_og_image = rtrim(G5_DATA_URL, '/') . '/seo_og/' . $new_name;
                }
            }
        }
    }
    $sp_og_type = isset($_POST['sp_og_type']) ? trim($_POST['sp_og_type']) : 'website';
    if ($sp_og_type === '') $sp_og_type = 'website';
    $sp_twitter_card = isset($_POST['sp_twitter_card']) ? trim($_POST['sp_twitter_card']) : 'summary_large_image';
    if ($sp_twitter_card === '') $sp_twitter_card = 'summary_large_image';
    $sp_google_site_verification = isset($_POST['sp_google_site_verification']) ? trim($_POST['sp_google_site_verification']) : '';
    // content 값만 저장: 따옴표·WWW·공백 제거 후 영문·숫자·일부기호만 유지
    $sp_google_site_verification = preg_replace('/^["\']?\\s*(?:WWW)?["\']?\\s*/i', '', $sp_google_site_verification);
    $sp_google_site_verification = trim(preg_replace('/["\']\\s*$/', '', $sp_google_site_verification));
    $sp_google_site_verification = preg_replace('/[^a-zA-Z0-9_-]/', '', $sp_google_site_verification);
    $sp_canonical_url = isset($_POST['sp_canonical_url']) ? trim($_POST['sp_canonical_url']) : '';
    $sp_sitemap_use = isset($_POST['sp_sitemap_use']) ? (int)$_POST['sp_sitemap_use'] : 1;
    $sp_sitemap_list_max_pages = isset($_POST['sp_sitemap_list_max_pages']) ? (int)$_POST['sp_sitemap_list_max_pages'] : 10;
    if ($sp_sitemap_list_max_pages < 1) $sp_sitemap_list_max_pages = 10;
    if ($sp_sitemap_list_max_pages > 100) $sp_sitemap_list_max_pages = 100;
    $sp_robots_txt_use = isset($_POST['sp_robots_txt_use']) ? (int)$_POST['sp_robots_txt_use'] : 1;
    $sp_schema_organization = isset($_POST['sp_schema_organization']) ? trim($_POST['sp_schema_organization']) : '';

    $sp_meta_description = sql_escape_string($sp_meta_description);
    $sp_meta_keywords = sql_escape_string($sp_meta_keywords);
    $sp_og_title = sql_escape_string($sp_og_title);
    $sp_og_description = sql_escape_string($sp_og_description);
    $sp_og_image = sql_escape_string($sp_og_image);
    $sp_og_type = sql_escape_string($sp_og_type);
    $sp_twitter_card = sql_escape_string($sp_twitter_card);
    $sp_google_site_verification = sql_escape_string($sp_google_site_verification);
    $sp_canonical_url = sql_escape_string($sp_canonical_url);
    $sp_schema_organization = sql_escape_string($sp_schema_organization);

    $sql = "UPDATE `{$sp_seo_table}` SET
      sp_meta_description = '{$sp_meta_description}',
      sp_meta_keywords = '{$sp_meta_keywords}',
      sp_og_title = '{$sp_og_title}',
      sp_og_description = '{$sp_og_description}',
      sp_og_image = '{$sp_og_image}',
      sp_og_type = '{$sp_og_type}',
      sp_twitter_card = '{$sp_twitter_card}',
      sp_google_site_verification = '{$sp_google_site_verification}',
      sp_canonical_url = '{$sp_canonical_url}',
      sp_sitemap_use = " . (int)$sp_sitemap_use . ",
      sp_sitemap_list_max_pages = " . (int)$sp_sitemap_list_max_pages . ",
      sp_robots_txt_use = " . (int)$sp_robots_txt_use . ",
      sp_schema_organization = '{$sp_schema_organization}',
      sp_updated_at = NOW()
    WHERE id = 1";
    sql_query($sql);
    $msg = 'SEO 설정이 저장되었습니다.';
    $url = G5_ADMIN_URL . '/scorepoint/scorepoint_seo.php?sub_menu=' . $sub_menu;
    alert($msg, $url);
    exit;
}

// 현재 설정 로드
$seo = sql_fetch("SELECT * FROM `{$sp_seo_table}` WHERE id = 1 LIMIT 1");
if (!$seo) {
    $seo = array(
        'sp_meta_description' => '',
        'sp_meta_keywords' => '',
        'sp_og_title' => '',
        'sp_og_description' => '',
        'sp_og_image' => '',
        'sp_og_type' => 'website',
        'sp_twitter_card' => 'summary_large_image',
        'sp_google_site_verification' => '',
        'sp_canonical_url' => '',
        'sp_sitemap_use' => 1,
        'sp_sitemap_list_max_pages' => 10,
        'sp_robots_txt_use' => 1,
        'sp_schema_organization' => '',
        'sp_updated_at' => null,
    );
}

function sp_seo_h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// 카테고리(그룹)·게시판 목록 및 수집 차단 설정 로드
$sp_seo_groups = array();
$sp_seo_boards = array();
$sp_noindex_gr = array();
$sp_noindex_bo = array();
$res_gr = @sql_query("SELECT gr_id, gr_subject FROM {$g5['group_table']} ORDER BY gr_id", false);
if ($res_gr) {
    while ($r = sql_fetch_array($res_gr)) {
        $sp_seo_groups[] = $r;
    }
}
$res_bo = @sql_query("SELECT bo_table, bo_subject, gr_id FROM {$g5['board_table']} ORDER BY gr_id, bo_table", false);
if ($res_bo) {
    while ($r = sql_fetch_array($res_bo)) {
        $sp_seo_boards[] = $r;
    }
}
$res_cfg_gr = @sql_query("SELECT gr_id, sp_noindex FROM `{$sp_seo_group_table}`", false);
if ($res_cfg_gr) {
    while ($r = sql_fetch_array($res_cfg_gr)) {
        $sp_noindex_gr[$r['gr_id']] = (int)$r['sp_noindex'];
    }
}
$res_cfg_bo = @sql_query("SELECT bo_table, sp_noindex FROM `{$sp_seo_board_table}`", false);
if ($res_cfg_bo) {
    while ($r = sql_fetch_array($res_cfg_bo)) {
        $sp_noindex_bo[$r['bo_table']] = (int)$r['sp_noindex'];
    }
}

$g5['title'] = 'SEO 최적화';
require_once G5_ADMIN_PATH . '/admin.head.php';
?>

<style>
/* adm/scorepoint 다른 메뉴와 동일하게 폼 왼쪽 정렬 (tbl_head01 기본이 text-align:center 이므로 덮어씀) */
.sp-seo-form tbody th       { vertical-align: top; width: 200px; padding-top: 12px; text-align: left !important; }
.sp-seo-form tbody td       { padding: 8px 10px; text-align: left !important; }
.sp-seo-form .frm_input     { width: 100%; max-width: 560px; box-sizing: border-box; }
.sp-seo-form textarea.frm_input { min-height: 80px; }
.sp-seo-form .frm_info      { margin: 6px 0 0 0; padding: 0; font-size: 0.9em; color: #666; }
</style>

<div class="local_ov01 local_ov">
    <span class="btn_ov01">SEO 최적화</span>
    <?php if (!empty($seo['sp_updated_at'])) { ?>
    <span class="frm_info" style="margin-left:12px;">마지막 저장: <?php echo sp_seo_h($seo['sp_updated_at']); ?></span>
    <?php } ?>
</div>

<p class="local_desc01" style="margin:12px 0;">
    사이트 기본 메타태그·OG·Twitter·Google 검증·캐노니컬·사이트맵 설정을 관리합니다. 저장 후 적용됩니다.
</p>

<form name="fsp_seo" id="fsp_seo" method="post" enctype="multipart/form-data" onsubmit="return confirm('저장하시겠습니까?');">
    <input type="hidden" name="sp_seo_form_check" value="1">

    <div class="tbl_head01 tbl_wrap sp-seo-form" style="margin-top:16px;">
        <table>
            <caption class="sound_only">사이트 공통 SEO</caption>
            <colgroup>
                <col style="width:200px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">사이트 메타 설명</th>
                    <td>
                        <textarea name="sp_meta_description" class="frm_input" rows="3" placeholder="검색 결과에 노출될 기본 설명 (160자 내외 권장)"><?php echo sp_seo_h($seo['sp_meta_description'] ?? ''); ?></textarea>
                        <p class="frm_info">meta name="description"</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">사이트 메타 키워드</th>
                    <td>
                        <input type="text" name="sp_meta_keywords" class="frm_input" value="<?php echo sp_seo_h($seo['sp_meta_keywords'] ?? ''); ?>" placeholder="쉼표로 구분 (선택)">
                        <p class="frm_info">meta name="keywords" (선택 항목)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">OG 제목</th>
                    <td>
                        <input type="text" name="sp_og_title" class="frm_input" value="<?php echo sp_seo_h($seo['sp_og_title'] ?? ''); ?>" placeholder="SNS 공유 시 제목 (비우면 페이지 제목 사용)">
                        <p class="frm_info">og:title</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">OG 설명</th>
                    <td>
                        <textarea name="sp_og_description" class="frm_input" rows="2" placeholder="SNS 공유 시 설명 (비우면 meta description 사용)"><?php echo sp_seo_h($seo['sp_og_description'] ?? ''); ?></textarea>
                        <p class="frm_info">og:description</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">기본 OG 이미지</th>
                    <td>
                        <input type="text" name="sp_og_image" id="sp_og_image" class="frm_input" value="<?php echo sp_seo_h($seo['sp_og_image'] ?? ''); ?>" placeholder="https://도메인/이미지경로.jpg (SNS 공유 시 썸네일)">
                        <p class="frm_info" style="margin-top:8px;">og:image 절대 URL (직접 입력하거나 아래에서 첨부)</p>
                        <p class="frm_info" style="margin-top:6px;"><strong>첨부:</strong> <input type="file" name="sp_og_image_file" id="sp_og_image_file" accept=".jpg,.jpeg,.png,.gif,.webp" class="frm_input" style="max-width:320px; width:auto; display:inline-block;"> <span class="frm_info">JPG/PNG/GIF/WebP, 2MB 이하</span></p>
                        <p class="frm_info" style="margin-top:8px; padding:8px; background:#f5f5f5; border-radius:4px;"><strong>이미지 규격 (권장):</strong><br>
                        · 크기: <strong>1200×630px</strong> (가로×세로)<br>
                        · 비율: <strong>1.91:1</strong> (Facebook/카카오 등 SNS 공유용)<br>
                        · 용량: 8MB 이하 (권장 1MB 이하)<br>
                        · 형식: JPG, PNG, GIF, WebP</p>
                        <?php if (!empty($seo['sp_og_image'])) { ?>
                        <p class="frm_info" style="margin-top:8px;">현재 설정 이미지: <img src="<?php echo sp_seo_h($seo['sp_og_image']); ?>" alt="OG 미리보기" style="max-width:200px; max-height:105px; border:1px solid #ddd; vertical-align:middle;"></p>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">OG 타입</th>
                    <td>
                        <input type="text" name="sp_og_type" class="frm_input" value="<?php echo sp_seo_h($seo['sp_og_type'] ?? 'website'); ?>" placeholder="website" style="width:120px;">
                        <p class="frm_info">og:type (기본값 website)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Twitter 카드</th>
                    <td>
                        <select name="sp_twitter_card" class="frm_input" style="width:200px;">
                            <option value="summary_large_image" <?php echo (($seo['sp_twitter_card'] ?? 'summary_large_image') === 'summary_large_image') ? 'selected' : ''; ?>>summary_large_image</option>
                            <option value="summary" <?php echo (($seo['sp_twitter_card'] ?? '') === 'summary') ? 'selected' : ''; ?>>summary</option>
                            <option value="app" <?php echo (($seo['sp_twitter_card'] ?? '') === 'app') ? 'selected' : ''; ?>>app</option>
                        </select>
                        <p class="frm_info">twitter:card</p>
                        <p class="frm_info" style="margin-top:6px; padding:6px; background:#f0f7ff; border-radius:4px;"><strong>용도:</strong> 트위터·SNS에서 링크 공유 시 큰 이미지+제목+설명이 보이게 합니다. summary_large_image 권장.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Google 사이트 검증</th>
                    <td>
                        <input type="text" name="sp_google_site_verification" class="frm_input" value="<?php echo sp_seo_h($seo['sp_google_site_verification'] ?? ''); ?>" placeholder="영문·숫자만 입력 (따옴표·WWW 제외)">
                        <p class="frm_info">Search Console에서 발급한 <strong>content 값만</strong> 입력 (예: wHgdeNaVFXiWj2vm_BnvBCHlIjUHk4BuwxZrDq9ccoY). 따옴표·WWW 붙이지 마세요. 저장 시 자동 정리됩니다.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">기본 캐노니컬 URL</th>
                    <td>
                        <input type="text" name="sp_canonical_url" class="frm_input" value="<?php echo sp_seo_h($seo['sp_canonical_url'] ?? ''); ?>" placeholder="https://scorepoint.co.kr (끝에 / 없이)">
                        <p class="frm_info">link rel="canonical" 에 사용. 비우면 자동 미출력.</p>
                        <p class="frm_info" style="margin-top:6px; padding:6px; background:#f0f7ff; border-radius:4px;"><strong>설명:</strong> 검색엔진에 "이 주소가 이 페이지의 대표 주소"라고 알려줍니다. www/비www, http/https 등 중복 수집을 줄이고 SEO에 도움이 됩니다. 홈이면 https://도메인 형태만 입력.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tbl_head01 tbl_wrap sp-seo-form" style="margin-top:20px;">
        <table>
            <caption class="sound_only">사이트맵·robots</caption>
            <colgroup>
                <col style="width:200px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">사이트맵 사용</th>
                    <td>
                        <label><input type="radio" name="sp_sitemap_use" value="1" <?php echo ((int)($seo['sp_sitemap_use'] ?? 1) === 1) ? 'checked' : ''; ?>> 사용</label>
                        <label><input type="radio" name="sp_sitemap_use" value="0" <?php echo ((int)($seo['sp_sitemap_use'] ?? 1) === 0) ? 'checked' : ''; ?>> 미사용</label>
                        <p class="frm_info">사이트맵 노출 여부 (실제 생성은 별도 구현)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">목록 최대 페이지 수</th>
                    <td>
                        <input type="number" name="sp_sitemap_list_max_pages" class="frm_input" value="<?php echo (int)($seo['sp_sitemap_list_max_pages'] ?? 10); ?>" min="1" max="100" style="width:80px;">
                        <p class="frm_info">사이트맵에 포함할 게시판 목록 최대 페이지 (1~100)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">robots.txt 사이트맵 노출</th>
                    <td>
                        <label><input type="radio" name="sp_robots_txt_use" value="1" <?php echo ((int)($seo['sp_robots_txt_use'] ?? 1) === 1) ? 'checked' : ''; ?>> 사용</label>
                        <label><input type="radio" name="sp_robots_txt_use" value="0" <?php echo ((int)($seo['sp_robots_txt_use'] ?? 1) === 0) ? 'checked' : ''; ?>> 미사용</label>
                        <p class="frm_info">robots.txt에 Sitemap URL 포함 여부 (실제 파일은 별도)</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tbl_head01 tbl_wrap sp-seo-form" style="margin-top:20px;">
        <table>
            <caption class="sound_only">구조화 데이터</caption>
            <colgroup>
                <col style="width:200px;">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">Organization 스키마(JSON)</th>
                    <td>
                        <textarea name="sp_schema_organization" class="frm_input" rows="4" placeholder='{"@context":"https://schema.org","@type":"Organization","name":"스코어포인트",...}'><?php echo sp_seo_h($seo['sp_schema_organization'] ?? ''); ?></textarea>
                        <p class="frm_info">JSON-LD Organization (선택). 유효한 JSON만 입력.</p>
                        <p class="frm_info" style="margin-top:6px; padding:6px; background:#f0f7ff; border-radius:4px;"><strong>설명:</strong> 검색 결과에 회사·사이트 이름·로고·연락처 등을 풍부하게 보여주는 "구조화 데이터"입니다. 구글·네이버 등이 인식해 노출 품질을 높일 수 있습니다. 선택 입력이며, 유효한 JSON 형식이어야 합니다.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <button type="submit" class="btn btn_02">설정 저장</button>
    </div>
</form>

<!-- 카테고리(그룹)·게시판별 검색 수집 차단 -->
<div class="tbl_head01 tbl_wrap sp-seo-form" style="margin-top:32px;">
    <h2 class="h2_frm" style="margin-bottom:12px;">카테고리·게시판별 검색 수집 설정</h2>
    <p class="local_desc01" style="margin:0 0 16px 0;">RSS 등으로 퍼오는 게시판(예: 스포츠뉴스)은 검색 수집을 차단(noindex)할 수 있습니다. 차단할 카테고리·게시판에 체크 후 저장하세요.</p>
    <form name="fsp_seo_board" id="fsp_seo_board" method="post" onsubmit="return confirm('수집 설정을 저장하시겠습니까?');">
        <input type="hidden" name="sp_seo_board_form" value="1">
        <table>
            <caption class="sound_only">카테고리·게시판별 noindex</caption>
            <colgroup><col style="width:120px;"><col style="width:80px;"><col></col></colgroup>
            <thead>
                <tr>
                    <th scope="col">구분</th>
                    <th scope="col">수집 차단</th>
                    <th scope="col">이름</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sp_seo_groups as $gr) {
                    $gr_id = $gr['gr_id'];
                    $checked = !empty($sp_noindex_gr[$gr_id]);
                ?>
                <tr>
                    <td>카테고리</td>
                    <td><label><input type="checkbox" name="sp_noindex_gr[<?php echo sp_seo_h($gr_id); ?>]" value="1" <?php echo $checked ? 'checked' : ''; ?>> noindex</label></td>
                    <td><?php echo sp_seo_h($gr['gr_subject'] ?? $gr_id); ?> (<?php echo sp_seo_h($gr_id); ?>)</td>
                </tr>
                <?php } ?>
                <?php foreach ($sp_seo_boards as $bo) {
                    $bo_table = $bo['bo_table'];
                    $checked = !empty($sp_noindex_bo[$bo_table]);
                ?>
                <tr>
                    <td>게시판</td>
                    <td><label><input type="checkbox" name="sp_noindex_bo[<?php echo sp_seo_h($bo_table); ?>]" value="1" <?php echo $checked ? 'checked' : ''; ?>> noindex</label></td>
                    <td><?php echo sp_seo_h($bo['bo_subject'] ?? $bo_table); ?> (<?php echo sp_seo_h($bo_table); ?>) · 그룹: <?php echo sp_seo_h($bo['gr_id'] ?? ''); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="btn_fixed_top" style="margin-top:16px;">
            <button type="submit" class="btn btn_02">수집 설정 저장</button>
        </div>
    </form>
</div>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
