<?php
/**
 * 이브수다방 메인 영역 — 리뉴얼 UI (evealba_sudabang.html)
 */
if (!defined('_GNUBOARD_')) exit;

@include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$write_prefix = $g5['write_prefix'];
$bbs_url      = G5_BBS_URL;
$base_url     = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';

function eve_time_ago($datetime) {
    $ts = strtotime($datetime);
    if (!$ts) return '';
    $diff = time() - $ts;
    if ($diff < 60) return '방금';
    if ($diff < 3600) return floor($diff / 60).'분 전';
    if ($diff < 86400) return floor($diff / 3600).'시간 전';
    if ($diff < 604800) return floor($diff / 86400).'일 전';
    return date('Y-m-d', $ts);
}

function eve_post_badge($row, $rank_label = '') {
    if ($rank_label !== '') {
        return '<span class="post-badge best">'.htmlspecialchars($rank_label).'</span>';
    }
    if (isset($row['wr_good']) && (int)$row['wr_good'] >= 10) {
        return '<span class="post-badge best">BEST</span>';
    }
    if ((isset($row['wr_comment']) && (int)$row['wr_comment'] >= 10)
        || (isset($row['wr_hit']) && (int)$row['wr_hit'] >= 100)) {
        return '<span class="post-badge hot">HOT</span>';
    }
    $diff = time() - strtotime($row['wr_datetime']);
    if ($diff < 86400) {
        return '<span class="post-badge new">NEW</span>';
    }
    return '';
}

function eve_is_hot_post($row) {
    if (isset($row['wr_good']) && (int)$row['wr_good'] >= 10) return true;
    if (isset($row['wr_comment']) && (int)$row['wr_comment'] >= 10) return true;
    if (isset($row['wr_hit']) && (int)$row['wr_hit'] >= 100) return true;
    return false;
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
    return ($row && (int)$row['cnt'] > 0);
}

function eve_board_post_count($bo_table) {
    global $write_prefix;
    if (!eve_board_exists($bo_table)) return 0;
    $bo_table = preg_replace('/[^a-z0-9_]/i', '', $bo_table);
    $row = @sql_fetch(" SELECT COUNT(*) as cnt FROM {$write_prefix}{$bo_table} WHERE wr_is_comment = 0 ");
    return ($row) ? (int)$row['cnt'] : 0;
}

function eve_board_label($bo_table) {
    static $labels = array(
        'night'  => '밤문화이야기',
        'couple' => '단짝찾기',
        'law'    => '법률자문',
    );
    return isset($labels[$bo_table]) ? $labels[$bo_table] : $bo_table;
}

function eve_write_href($bo_table = '') {
    global $bbs_url, $is_member;
    if (!$is_member) {
        return $bbs_url.'/login.php';
    }
    if ($bo_table !== '') {
        $bo_table = preg_replace('/[^a-z0-9_]/i', '', $bo_table);
        return $bbs_url.'/write.php?bo_table='.$bo_table;
    }
    return $bbs_url.'/write.php?bo_table=night';
}

function eve_post_href($bo_table, $wr_id) {
    global $bbs_url;
    $bo_table = preg_replace('/[^a-z0-9_]/i', '', $bo_table);
    return $bbs_url.'/board.php?bo_table='.$bo_table.'&wr_id='.(int)$wr_id;
}

function eve_post_thumb($bo_table, $wr_id) {
    if (!function_exists('get_list_thumbnail')) return '';
    $thumb = get_list_thumbnail($bo_table, $wr_id, 68, 68, false, true);
    if (empty($thumb['src'])) return '';
    return '<div class="post-thumb-sm"><img src="'.htmlspecialchars($thumb['src']).'" alt=""></div>';
}

function eve_render_post_stats($row) {
    $good    = isset($row['wr_good']) ? (int)$row['wr_good'] : 0;
    $comment = isset($row['wr_comment']) ? (int)$row['wr_comment'] : 0;
    $hit     = isset($row['wr_hit']) ? (int)$row['wr_hit'] : 0;
    $liked   = ($good > 0) ? ' liked' : '';
    return '<div class="post-meta-stats">'
        .'<div class="post-stat'.$liked.'">❤ '.number_format($good).'</div>'
        .'<div class="post-stat">💬 '.number_format($comment).'</div>'
        .'<div class="post-stat">👁 '.number_format($hit).'</div>'
        .'</div>';
}

function eve_render_post_item($row, $bo_table, $opts = array()) {
    $show_board = !empty($opts['show_board']);
    $rank_label = isset($opts['rank_label']) ? $opts['rank_label'] : '';
    $href       = eve_post_href($bo_table, $row['wr_id']);
    $hot_class  = eve_is_hot_post($row) ? ' is-hot' : '';
    $badge      = eve_post_badge($row, $rank_label);
    $author     = htmlspecialchars($row['wr_name']);
    $subject    = htmlspecialchars($row['wr_subject']);
    $time       = eve_time_ago($row['wr_datetime']);
    $thumb      = eve_post_thumb($bo_table, $row['wr_id']);

    $meta = '<span class="meta-author">'.$author.'</span>';
    if ($show_board) {
        $meta .= '<span class="meta-sep">·</span><span>'.htmlspecialchars(eve_board_label($bo_table)).'</span>';
    }
    $meta .= '<span class="meta-sep">·</span><span>'.$time.'</span>';

    $html  = '<a href="'.htmlspecialchars($href).'" class="post-item'.$hot_class.'">';
    if ($badge !== '') {
        $html .= $badge;
    }
    $html .= '<div class="post-info">';
    $html .= '<div class="post-title">'.$subject.'</div>';
    $html .= '<div class="post-meta">'.$meta.'</div>';
    $html .= eve_render_post_stats($row);
    $html .= '</div>';
    $html .= $thumb;
    $html .= '</a>';
    return $html;
}

function eve_render_best_preview_item($row, $rank) {
    $bo_table = isset($row['src_table']) ? $row['src_table'] : 'night';
    $href     = eve_post_href($bo_table, $row['wr_id']);
    $rank_cls = ($rank <= 3) ? ' rank-'.$rank : '';
    $likes    = isset($row['wr_good']) ? (int)$row['wr_good'] : 0;

    $html  = '<a href="'.htmlspecialchars($href).'" class="best-post-item">';
    $html .= '<div class="best-rank'.$rank_cls.'">'.$rank.'</div>';
    $html .= '<div class="best-info">';
    $html .= '<div class="best-title">'.htmlspecialchars($row['wr_subject']).'</div>';
    $html .= '<div class="best-meta">';
    $html .= '<span>'.htmlspecialchars(eve_board_label($bo_table)).'</span>';
    $html .= '<span>'.htmlspecialchars($row['wr_name']).'</span>';
    $html .= '<span>'.eve_time_ago($row['wr_datetime']).'</span>';
    $html .= '</div></div>';
    $html .= '<div class="best-likes">❤ '.number_format($likes).'</div>';
    $html .= '</a>';
    return $html;
}

function eve_render_empty_state($message) {
    return '<div class="empty-state"><div class="empty-icon">📭</div><p>'.htmlspecialchars($message).'</p></div>';
}

function eve_render_pagination($bo_table, $total, $per_page = 10) {
    global $bbs_url;
    if ($total <= $per_page) return '';
    $pages = (int)ceil($total / $per_page);
    if ($pages < 2) return '';

    $html = '<div class="pagination">';
    if ($pages > 1) {
        $html .= '<a href="'.htmlspecialchars($bbs_url.'/board.php?bo_table='.$bo_table.'&amp;page=1').'" class="page-btn arrow">‹</a>';
    }
    $show = min($pages, 3);
    for ($p = 1; $p <= $show; $p++) {
        $cls = ($p === 1) ? ' active' : '';
        $html .= '<a href="'.htmlspecialchars($bbs_url.'/board.php?bo_table='.$bo_table.'&amp;page='.$p).'" class="page-btn'.$cls.'">'.$p.'</a>';
    }
    if ($pages > 1) {
        $next = min(2, $pages);
        $html .= '<a href="'.htmlspecialchars($bbs_url.'/board.php?bo_table='.$bo_table.'&amp;page='.$next).'" class="page-btn arrow">›</a>';
    }
    $html .= '</div>';
    return $html;
}

function eve_render_search_bar($bo_table, $placeholder) {
    global $bbs_url;
    $bo_table = preg_replace('/[^a-z0-9_]/i', '', $bo_table);
    $action   = $bbs_url.'/board.php?bo_table='.$bo_table;
    return '<form class="board-search-bar" method="get" action="'.htmlspecialchars($action).'">'
        .'<input type="hidden" name="bo_table" value="'.htmlspecialchars($bo_table).'">'
        .'<input type="hidden" name="sfl" value="wr_subject||wr_content">'
        .'<input type="text" name="stx" placeholder="'.htmlspecialchars($placeholder).'">'
        .'<button type="submit">검색</button>'
        .'</form>';
}

function eve_render_write_cta($avatar, $text, $href) {
    return '<a href="'.htmlspecialchars($href).'" class="write-cta">'
        .'<div class="cta-avatar">'.$avatar.'</div>'
        .'<div class="cta-text">'.$text.'</div>'
        .'<span style="color:var(--pink);font-size:18px;">›</span>'
        .'</a>';
}

$has_night  = eve_board_exists('night');
$has_couple = eve_board_exists('couple');
$has_law    = eve_board_exists('law');

$best_posts   = array();
$best_count   = 0;
$night_posts  = array();
$couple_posts = array();
$law_posts    = array();
$night_count  = 0;
$couple_count = 0;
$law_count    = 0;

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
    if ($unions) {
        $count_sql = 'SELECT COUNT(*) AS cnt FROM ('.implode(' UNION ALL ', $unions).') AS best_union';
        $count_row = @sql_fetch($count_sql);
        $best_count = ($count_row) ? (int)$count_row['cnt'] : 0;

        $best_sql = implode(' UNION ALL ', $unions).' ORDER BY wr_good DESC, wr_hit DESC LIMIT 5';
        $best_posts = eve_safe_board_query($best_sql);
    }
}

if ($has_night) {
    $night_count = eve_board_post_count('night');
    $night_posts = eve_safe_board_query("
        SELECT wr_id, wr_subject, wr_name, wr_hit, wr_good, wr_comment, wr_datetime
        FROM {$write_prefix}night
        WHERE wr_is_comment = 0
        ORDER BY wr_id DESC
        LIMIT 8
    ");
}

if ($has_couple) {
    $couple_count = eve_board_post_count('couple');
    $couple_posts = eve_safe_board_query("
        SELECT wr_id, wr_subject, wr_name, wr_hit, wr_good, wr_comment, wr_datetime
        FROM {$write_prefix}couple
        WHERE wr_is_comment = 0
        ORDER BY wr_id DESC
        LIMIT 6
    ");
}

if ($has_law) {
    $law_count = eve_board_post_count('law');
    $law_posts = eve_safe_board_query("
        SELECT wr_id, wr_subject, wr_name, wr_hit, wr_good, wr_comment, wr_datetime
        FROM {$write_prefix}law
        WHERE wr_is_comment = 0
        ORDER BY wr_id DESC
        LIMIT 5
    ");
}

$night_preview  = array_slice($night_posts, 0, 3);
$couple_preview = array_slice($couple_posts, 0, 3);
$law_preview    = array_slice($law_posts, 0, 3);

$hero_write_href = eve_write_href();
$fab_write_href  = $hero_write_href;
$all_cta_href    = eve_write_href();
$all_cta_text    = $is_member
    ? '지금 바로 <strong>수다방에 글을 남겨보세요!</strong>'
    : '로그인 후 <strong>수다방에 글을 남겨보세요!</strong>';
?>
<!-- 빵부스러기 -->
<div class="page-breadcrumb">
  <a href="<?php echo htmlspecialchars($base_url ? $base_url.'/' : '/'); ?>">🏠 메인</a>
  <span class="bc-sep">›</span>
  <span class="bc-current">😆 이브수다방</span>
</div>

<!-- 수다방 히어로 헤더 -->
<div class="suda-hero">
  <div class="suda-hero-inner">
    <div>
      <h1>😆 이브<span>수다방</span></h1>
      <p>밤문화 이야기, 단짝 찾기, 법률 상담까지 — 이브알바 회원 전용 커뮤니티</p>
    </div>
    <a href="<?php echo htmlspecialchars($hero_write_href); ?>" class="suda-write-btn">✏️ 글쓰기</a>
  </div>
</div>

<!-- 커뮤니티 탭 -->
<div class="comm-tabs" id="commTabs">
  <div class="comm-tab active" data-tab="all">📋 전체보기</div>
  <div class="comm-tab" data-tab="best">🏆 베스트글<?php if ($best_count > 0): ?> <span class="tab-count"><?php echo number_format($best_count); ?></span><?php endif; ?></div>
  <div class="comm-tab" data-tab="night">🌙 밤문화이야기<?php if ($night_count > 0): ?> <span class="tab-count"><?php echo number_format($night_count); ?></span><?php endif; ?></div>
  <div class="comm-tab" data-tab="couple">💑 단짝찾기<?php if ($couple_count > 0): ?> <span class="tab-count"><?php echo number_format($couple_count); ?></span><?php endif; ?></div>
  <div class="comm-tab" data-tab="law">⚖️ 무료법률자문<?php if ($law_count > 0): ?> <span class="tab-count"><?php echo number_format($law_count); ?></span><?php endif; ?></div>
</div>

<!-- ══ 탭 1: 전체보기 ══ -->
<div class="suda-content active" id="tab-all">

  <?php echo eve_render_write_cta('✍️', $all_cta_text, $all_cta_href); ?>

  <!-- 베스트글 섹션 -->
  <div class="board-section">
    <div class="board-section-head">
      <div class="board-section-title">
        <span class="b-icon">🏆</span>
        <div>
          <div class="b-name">베스트글</div>
          <div class="b-desc">좋아요 10개 이상 · 자동 선정</div>
        </div>
      </div>
      <div class="board-section-actions">
        <button type="button" class="btn-more" data-switch-tab="best">더보기 →</button>
      </div>
    </div>
    <div class="post-list">
      <?php
      if (empty($best_posts)) {
          echo eve_render_empty_state('아직 베스트글이 없습니다');
      } else {
          foreach ($best_posts as $idx => $bp) {
              echo eve_render_best_preview_item($bp, $idx + 1);
          }
      }
      ?>
    </div>
  </div>

  <!-- 밤문화이야기 섹션 -->
  <div class="board-section">
    <div class="board-section-head">
      <div class="board-section-title">
        <span class="b-icon">🌙</span>
        <div>
          <div class="b-name">밤문화이야기</div>
          <div class="b-desc">업소 경험담 · 노하우 · 팁 공유</div>
        </div>
      </div>
      <div class="board-section-actions">
        <button type="button" class="btn-more" data-switch-tab="night">더보기 →</button>
        <a href="<?php echo htmlspecialchars(eve_write_href('night')); ?>" class="btn-write-sm">✏️ 글쓰기</a>
      </div>
    </div>
    <div class="post-list">
      <?php
      if (empty($night_preview)) {
          echo eve_render_empty_state('등록된 글이 없습니다');
      } else {
          foreach ($night_preview as $np) {
              echo eve_render_post_item($np, 'night');
          }
      }
      ?>
    </div>
  </div>

  <!-- 단짝찾기 섹션 -->
  <div class="board-section">
    <div class="board-section-head">
      <div class="board-section-title">
        <span class="b-icon">💑</span>
        <div>
          <div class="b-name">같이 일할 단짝 찾기</div>
          <div class="b-desc">함께 일할 파트너를 구해요</div>
        </div>
      </div>
      <div class="board-section-actions">
        <button type="button" class="btn-more" data-switch-tab="couple">더보기 →</button>
        <a href="<?php echo htmlspecialchars(eve_write_href('couple')); ?>" class="btn-write-sm">✏️ 글쓰기</a>
      </div>
    </div>
    <div class="post-list">
      <?php
      if (empty($couple_preview)) {
          echo eve_render_empty_state('등록된 글이 없습니다');
      } else {
          foreach ($couple_preview as $cp) {
              echo eve_render_post_item($cp, 'couple');
          }
      }
      ?>
    </div>
  </div>

  <!-- 무료법률자문 섹션 -->
  <div class="board-section">
    <div class="board-section-head">
      <div class="board-section-title">
        <span class="b-icon">⚖️</span>
        <div>
          <div class="b-name">무료 법률 자문</div>
          <div class="b-desc">법적 문제 · 계약 · 분쟁 상담</div>
        </div>
      </div>
      <div class="board-section-actions">
        <button type="button" class="btn-more" data-switch-tab="law">더보기 →</button>
        <a href="<?php echo htmlspecialchars(eve_write_href('law')); ?>" class="btn-write-sm">✏️ 글쓰기</a>
      </div>
    </div>
    <div class="post-list">
      <?php
      if (empty($law_preview)) {
          echo eve_render_empty_state('등록된 글이 없습니다');
      } else {
          foreach ($law_preview as $lp) {
              echo eve_render_post_item($lp, 'law');
          }
      }
      ?>
    </div>
  </div>

</div>

<!-- ══ 탭 2: 베스트글 ══ -->
<div class="suda-content" id="tab-best">
  <?php echo eve_render_search_bar('night', '🔍 베스트글 검색'); ?>
  <div class="post-list">
    <?php
    if (empty($best_posts)) {
        echo eve_render_empty_state('아직 베스트글이 없습니다');
    } else {
        foreach ($best_posts as $idx => $bp) {
            $src = isset($bp['src_table']) ? $bp['src_table'] : 'night';
            echo eve_render_post_item($bp, $src, array(
                'show_board' => true,
                'rank_label' => ($idx + 1).'위',
            ));
        }
    }
    ?>
  </div>
  <?php echo eve_render_pagination('night', $best_count); ?>
</div>

<!-- ══ 탭 3: 밤문화이야기 ══ -->
<div class="suda-content" id="tab-night">
  <?php echo eve_render_search_bar('night', '🔍 밤문화이야기 검색'); ?>
  <?php echo eve_render_write_cta('🌙', '경험담, 노하우, 팁을 <strong>공유해보세요!</strong>', eve_write_href('night')); ?>
  <div class="post-list">
    <?php
    if (empty($night_posts)) {
        echo eve_render_empty_state('등록된 글이 없습니다');
    } else {
        foreach ($night_posts as $np) {
            echo eve_render_post_item($np, 'night');
        }
    }
    ?>
  </div>
  <?php echo eve_render_pagination('night', $night_count); ?>
</div>

<!-- ══ 탭 4: 단짝찾기 ══ -->
<div class="suda-content" id="tab-couple">
  <?php echo eve_render_search_bar('couple', '🔍 단짝찾기 검색'); ?>
  <?php echo eve_render_write_cta('💑', '함께 일할 파트너를 <strong>찾아보세요!</strong>', eve_write_href('couple')); ?>
  <div class="post-list">
    <?php
    if (empty($couple_posts)) {
        echo eve_render_empty_state('등록된 글이 없습니다');
    } else {
        foreach ($couple_posts as $cp) {
            echo eve_render_post_item($cp, 'couple');
        }
    }
    ?>
  </div>
  <?php echo eve_render_pagination('couple', $couple_count); ?>
</div>

<!-- ══ 탭 5: 무료법률자문 ══ -->
<div class="suda-content" id="tab-law">
  <?php echo eve_render_search_bar('law', '🔍 법률 자문 검색'); ?>
  <?php echo eve_render_write_cta('⚖️', '법적 문제, 계약, 분쟁을 <strong>무료로 상담 받으세요!</strong>', eve_write_href('law')); ?>
  <div class="post-list">
    <?php
    if (empty($law_posts)) {
        echo eve_render_empty_state('등록된 글이 없습니다');
    } else {
        foreach ($law_posts as $lp) {
            echo eve_render_post_item($lp, 'law');
        }
    }
    ?>
  </div>
  <?php echo eve_render_pagination('law', $law_count); ?>
</div>

<!-- 모바일 글쓰기 FAB -->
<a href="<?php echo htmlspecialchars($fab_write_href); ?>" class="fab-write" title="글쓰기">✏️</a>

<script src="<?php echo G5_THEME_URL; ?>/js/evealba_sudabang.js?ver=<?php echo @filemtime(G5_THEME_PATH.'/js/evealba_sudabang.js'); ?>"></script>
