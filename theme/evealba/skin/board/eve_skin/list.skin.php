<?php
if (!defined('_GNUBOARD_')) exit;

$colspan = 5;
if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 분류: 어드민 게시판수정 > 분류(bo_category_list) 연동, | 구분
$ev_categories = array();
if ($board['bo_use_category'] && $board['bo_category_list']) {
    $ev_categories = array_filter(array_map('trim', explode('|', $board['bo_category_list'])));
}
?>

<div id="bo_list" class="ev-board-list" style="width:<?php echo $width; ?>">

    <?php if ($is_category && count($ev_categories) > 0) { ?>
    <div class="cat-tabs">
        <a href="<?php echo get_pretty_url($bo_table); ?>" class="cat-tab<?php echo ($sca == '') ? ' active' : ''; ?>">전체</a>
        <?php foreach ($ev_categories as $cat) {
            $cat_href = get_pretty_url($bo_table, '', 'sca='.urlencode($cat));
            $is_active = ($sca == $cat);
        ?>
        <a href="<?php echo $cat_href; ?>" class="cat-tab<?php echo $is_active ? ' active' : ''; ?>"><?php echo htmlspecialchars($cat); ?></a>
        <?php } ?>
    </div>
    <?php } ?>

    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="spt" value="<?php echo $spt; ?>">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="sst" value="<?php echo $sst; ?>">
    <input type="hidden" name="sod" value="<?php echo $sod; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="sw" value="">

    <div class="board-topbar">
        <div class="board-topbar-left">
            <h2 class="board-page-title"><?php echo $board['bo_subject']; ?></h2>
            <span class="board-count">총 <strong><?php echo number_format($total_count); ?></strong>건</span>
        </div>
        <div class="board-btns">
            <?php if ($write_href) { ?><a href="<?php echo $write_href; ?>" class="btn-write">✏️ 글쓰기</a><?php } ?>
            <a href="<?php echo get_pretty_url($bo_table); ?>" class="btn-list">📋 목록</a>
        </div>
    </div>

    <div class="board-wrap<?php echo $is_good ? '' : ' no-rec'; ?>">
        <div class="board-thead">
            <div class="board-th">번호</div>
            <div class="board-th">첨부</div>
            <div class="board-th td-title">제 목</div>
            <div class="board-th">등록인</div>
            <div class="board-th">등록일</div>
            <?php if ($is_good) { ?><div class="board-th">추천</div><?php } ?>
            <div class="board-th">조회수</div>
        </div>

        <?php for ($i=0; $i<count($list); $i++) {
            $row = $list[$i];
            $row_class = $row['is_notice'] ? 'board-row notice-row' : 'board-row';
        ?>
        <a href="<?php echo $row['href']; ?>" class="<?php echo $row_class; ?>">
            <div class="board-td td-num">
                <?php if ($row['is_notice']) { ?>
                <span class="badge-notice">공지</span>
                <?php } elseif ($wr_id == $row['wr_id']) { ?>
                <span class="bo_current">열람중</span>
                <?php } else { ?>
                <?php echo $row['num']; ?>
                <?php } ?>
            </div>
            <div class="board-td td-att"><?php echo !empty($row['icon_file']) ? $row['icon_file'] : '📋'; ?></div>
            <div class="board-td td-title">
                <div class="td-title-inner">
                    <span class="post-title-text<?php echo $row['is_notice'] ? ' notice-title' : ''; ?>"><?php echo $row['subject']; ?></span>
                    <?php if ($row['icon_new']) { ?><span class="badge-new">N</span><?php } ?>
                    <?php if ($row['comment_cnt']) { ?><span class="badge-comment">[<?php echo $row['wr_comment']; ?>]</span><?php } ?>
                    <?php if ($is_category && $row['ca_name']) { ?>
                    <span class="cat-badge cat-<?php echo preg_replace('/[^가-힣a-zA-Z0-9]/', '', $row['ca_name']); ?>"><?php echo $row['ca_name']; ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="board-td td-writer"><?php echo $row['name']; ?></div>
            <div class="board-td td-date"><?php echo $row['datetime2']; ?></div>
            <?php if ($is_good) { ?><div class="board-td td-rec"><?php echo $row['wr_good']; ?></div><?php } ?>
            <div class="board-td td-view"><?php echo $row['wr_hit']; ?></div>
        </a>
        <?php } ?>

        <?php if (count($list) == 0) { ?>
        <div class="board-row empty-row">
            <div class="board-td" style="grid-column:1/-1;text-align:center;padding:40px;">게시물이 없습니다.</div>
        </div>
        <?php } ?>
    </div>

    <div class="board-bottom">
        <?php if ($write_href) { ?><a href="<?php echo $write_href; ?>" class="btn-write">✏️ 글쓰기</a><?php } ?>
        <a href="<?php echo get_pretty_url($bo_table); ?>" class="btn-list">📋 목록</a>
    </div>

    <?php echo $write_pages; ?>

    <div class="search-bar">
        <form name="fsearch" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
        <input type="hidden" name="sca" value="<?php echo $sca; ?>">
        <select name="sfl" class="search-sel">
            <?php echo get_board_sfl_select_options($sfl); ?>
        </select>
        <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" class="search-inp" placeholder="검색어를 입력하세요">
        <button type="submit" class="btn-search-go">🔍 검색</button>
        </form>
    </div>

    </form>
</div>
