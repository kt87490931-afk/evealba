<?php
/**
 * 이브수다방 메인 영역 - DB 연동
 */
if (!defined('_GNUBOARD_')) exit;

$write_prefix = $g5['write_prefix'];
$bbs_url = G5_BBS_URL;

function eve_time_ago($datetime) {
    $ts = strtotime($datetime);
    if (!$ts) return '';
    $diff = time() - $ts;
    if ($diff < 60) return '방금';
    if ($diff < 3600) return floor($diff/60).'분 전';
    if ($diff < 86400) return floor($diff/3600).'시간 전';
    if ($diff < 604800) return floor($diff/86400).'일 전';
    return date('Y-m-d', $ts);
}

function eve_post_badge($row) {
    if (isset($row['wr_good']) && $row['wr_good'] >= 10) return '<span class="post-badge pb-best">BEST</span>';
    if ((isset($row['wr_comment']) && $row['wr_comment'] >= 10) || (isset($row['wr_hit']) && $row['wr_hit'] >= 100)) return '<span class="post-badge pb-hot">HOT</span>';
    $diff = time() - strtotime($row['wr_datetime']);
    if ($diff < 86400) return '<span class="post-badge pb-new">NEW</span>';
    return '<span class="post-badge pb-free">자유</span>';
}

function eve_safe_board_query($sql) {
    $result = @sql_query($sql, false);
    if (!$result) return array();
    $rows = array();
    while ($row = @sql_fetch_array($result)) {
        if (!$row) break;
        $rows[] = $row;
    }
    return $rows;
}

function eve_board_exists($bo_table) {
    global $g5;
    $bo_table = preg_replace('/[^a-z0-9_]/i', '', $bo_table);
    $row = @sql_fetch(" SELECT COUNT(*) as cnt FROM {$g5['board_table']} WHERE bo_table = '{$bo_table}' ");
    return ($row && $row['cnt'] > 0);
}

$has_night  = eve_board_exists('night');
$has_couple = eve_board_exists('couple');
$has_law    = eve_board_exists('law');

// --- 베스트글: night + couple 에서 좋아요 10개 이상 ---
$best_posts = array();
if ($has_night || $has_couple) {
    $unions = array();
    if ($has_night) {
        $unions[] = "(SELECT wr_id, wr_subject, wr_content, wr_name, wr_hit, wr_good, wr_comment, wr_datetime, 'night' AS src_table
                     FROM {$write_prefix}night
                     WHERE wr_is_comment = 0 AND wr_good >= 10)";
    }
    if ($has_couple) {
        $unions[] = "(SELECT wr_id, wr_subject, wr_content, wr_name, wr_hit, wr_good, wr_comment, wr_datetime, 'couple' AS src_table
                     FROM {$write_prefix}couple
                     WHERE wr_is_comment = 0 AND wr_good >= 10)";
    }
    $best_sql = implode(' UNION ALL ', $unions) . " ORDER BY wr_good DESC, wr_hit DESC LIMIT 5";
    $best_posts = eve_safe_board_query($best_sql);
}

// --- 밤문화이야기: night 최신 8개 ---
$night_posts = array();
if ($has_night) {
    $night_posts = eve_safe_board_query("
        SELECT wr_id, wr_subject, wr_name, wr_hit, wr_good, wr_comment, wr_datetime
        FROM {$write_prefix}night
        WHERE wr_is_comment = 0
        ORDER BY wr_id DESC LIMIT 8
    ");
}

// --- 같이일할단짝찾기: couple 최신 6개 ---
$couple_posts = array();
if ($has_couple) {
    $couple_posts = eve_safe_board_query("
        SELECT wr_id, wr_subject, wr_name, wr_hit, wr_good, wr_comment, wr_datetime
        FROM {$write_prefix}couple
        WHERE wr_is_comment = 0
        ORDER BY wr_id DESC LIMIT 6
    ");
}

// --- 무료법률자문: law 최신 5개 ---
$law_posts = array();
if ($has_law) {
    $law_posts = eve_safe_board_query("
        SELECT wr_id, wr_subject, wr_name, wr_hit, wr_good, wr_comment, wr_datetime
        FROM {$write_prefix}law
        WHERE wr_is_comment = 0
        ORDER BY wr_id DESC LIMIT 5
    ");
}
?>
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>

    <!-- 상단 2열: 베스트글 + 밤문화이야기 -->
    <div class="community-main-grid">
      <!-- 베스트글 -->
      <div class="board-card-wide bh-best">
        <div class="board-card-header">
          <div class="board-title-row">
            <span class="board-icon">🏆</span>
            <div>
              <div class="board-name">베스트글</div>
              <div class="board-desc">좋아요 10개 이상 · 자동 선정</div>
            </div>
          </div>
        </div>
        <div class="board-post-list">
          <?php if (empty($best_posts)): ?>
            <div class="board-post-item" style="justify-content:center;color:#999;padding:20px 0;">아직 베스트글이 없습니다</div>
          <?php else: foreach ($best_posts as $idx => $bp):
            $rank = $idx + 1;
            $rank_class = ($rank <= 3) ? ' r'.$rank : '';
            $rank_style = ($rank > 3) ? ' style="background:#f0f0f0;color:#888;"' : '';
            $raw_content = strip_tags(html_entity_decode($bp['wr_content'], ENT_QUOTES, 'UTF-8'));
            $summary = mb_substr($raw_content, 0, 40, 'UTF-8');
            $href = $bbs_url.'/board.php?bo_table='.$bp['src_table'].'&amp;wr_id='.$bp['wr_id'];
          ?>
          <div class="best-post-item">
            <div class="best-rank<?php echo $rank_class; ?>"<?php echo $rank_style; ?>><?php echo $rank; ?></div>
            <div class="best-info">
              <a href="<?php echo $href; ?>" class="best-title"><?php echo htmlspecialchars($bp['wr_subject']); ?></a>
              <div class="best-sub"><?php echo htmlspecialchars($summary); ?></div>
              <div class="best-stats"><span class="hot">❤️ <?php echo number_format($bp['wr_good']); ?></span><span>💬 <?php echo number_format($bp['wr_comment']); ?></span><span>👁 <?php echo number_format($bp['wr_hit']); ?></span></div>
            </div>
            <div class="best-meta"><?php echo eve_time_ago($bp['wr_datetime']); ?></div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <!-- 밤문화이야기 -->
      <div class="board-card-wide bh-night">
        <div class="board-card-header">
          <div class="board-title-row">
            <span class="board-icon">🌙</span>
            <div>
              <div class="board-name">밤문화이야기</div>
              <div class="board-desc">업소 경험담 · 노하우 · 팁 공유</div>
            </div>
          </div>
          <div style="display:flex;gap:6px">
            <a href="<?php echo $bbs_url; ?>/board.php?bo_table=night" class="board-more">더보기 →</a>
            <a href="<?php echo $bbs_url; ?>/write.php?bo_table=night" class="board-write-btn">✏️ 글쓰기</a>
          </div>
        </div>
        <div class="board-post-list">
          <?php if (empty($night_posts)): ?>
            <div class="board-post-item" style="justify-content:center;color:#999;padding:20px 0;">등록된 글이 없습니다</div>
          <?php else: foreach ($night_posts as $np):
            $href = $bbs_url.'/board.php?bo_table=night&amp;wr_id='.$np['wr_id'];
          ?>
          <div class="board-post-item">
            <?php echo eve_post_badge($np); ?>
            <a href="<?php echo $href; ?>" class="post-title"><?php echo htmlspecialchars($np['wr_subject']); ?><?php if ($np['wr_comment'] > 0): ?> <span class="post-comment">[<?php echo $np['wr_comment']; ?>]</span><?php endif; ?></a>
            <span class="post-meta"><?php echo eve_time_ago($np['wr_datetime']); ?></span>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>

    <!-- 하단 2열: 같이일할단짝찾기 + 무료법률자문 -->
    <div class="community-bottom-grid">
      <div class="board-card">
        <div class="board-card-header">
          <div class="board-title-row">
            <span class="board-icon">💑</span>
            <div>
              <div class="board-name">같이일할단짝찾기</div>
              <div class="board-desc">함께 일할 파트너를 구해요</div>
            </div>
          </div>
          <div style="display:flex;gap:6px">
            <a href="<?php echo $bbs_url; ?>/board.php?bo_table=couple" class="board-more">더보기</a>
            <a href="<?php echo $bbs_url; ?>/write.php?bo_table=couple" class="board-write-btn">✏️ 글쓰기</a>
          </div>
        </div>
        <div class="board-post-list">
          <?php if (empty($couple_posts)): ?>
            <div class="board-post-item" style="justify-content:center;color:#999;padding:20px 0;">등록된 글이 없습니다</div>
          <?php else: foreach ($couple_posts as $cp):
            $href = $bbs_url.'/board.php?bo_table=couple&amp;wr_id='.$cp['wr_id'];
          ?>
          <div class="board-post-item">
            <?php echo eve_post_badge($cp); ?>
            <a href="<?php echo $href; ?>" class="post-title"><?php echo htmlspecialchars($cp['wr_subject']); ?><?php if ($cp['wr_comment'] > 0): ?> <span class="post-comment">[<?php echo $cp['wr_comment']; ?>]</span><?php endif; ?></a>
            <span class="post-meta"><?php echo eve_time_ago($cp['wr_datetime']); ?></span>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <div class="board-card bh-legal" style="border-radius:14px;overflow:hidden;">
        <div class="board-card-header">
          <div class="board-title-row">
            <span class="board-icon">⚖️</span>
            <div>
              <div class="board-name" style="color:#fff">무료법률자문</div>
              <div class="board-desc" style="color:rgba(255,255,255,.75)">법적 문제 · 계약 · 분쟁 상담</div>
            </div>
          </div>
          <div style="display:flex;gap:6px">
            <a href="<?php echo $bbs_url; ?>/board.php?bo_table=law" class="board-more" style="border-color:rgba(255,255,255,.4);color:#fff;">더보기</a>
            <a href="<?php echo $bbs_url; ?>/write.php?bo_table=law" class="board-write-btn">✏️ 글쓰기</a>
          </div>
        </div>
        <div class="board-post-list">
          <?php if (empty($law_posts)): ?>
            <div class="board-post-item" style="justify-content:center;color:#999;padding:20px 0;">등록된 글이 없습니다</div>
          <?php else: foreach ($law_posts as $lp):
            $href = $bbs_url.'/board.php?bo_table=law&amp;wr_id='.$lp['wr_id'];
          ?>
          <div class="board-post-item">
            <?php echo eve_post_badge($lp); ?>
            <a href="<?php echo $href; ?>" class="post-title"><?php echo htmlspecialchars($lp['wr_subject']); ?><?php if ($lp['wr_comment'] > 0): ?> <span class="post-comment">[<?php echo $lp['wr_comment']; ?>]</span><?php endif; ?></a>
            <span class="post-meta"><?php echo eve_time_ago($lp['wr_datetime']); ?></span>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
