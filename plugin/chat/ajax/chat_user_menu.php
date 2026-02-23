<?php
// /plugin/chat/ajax/chat_user_menu.php
// 닉네임 클릭 메뉴(전적/활동/무시/신고/선물) AJAX
// GNUBOARD 환경 필요

include_once('../../../common.php');
header('Content-Type: application/json; charset=utf-8');

if (!defined('_GNUBOARD_')) {
    echo json_encode(['ok'=>false,'msg'=>'GNUBOARD not loaded']);
    exit;
}


$nickname = trim((string)($_POST['nickname'] ?? ''));
$mb_id    = trim((string)($_POST['mb_id'] ?? '')); // 선택
$mode     = trim((string)($_POST['mode'] ?? ''));  // report 등

if ($nickname === '' && $mb_id === '') {
  echo json_encode(['ok'=>false,'msg'=>'no target']);
  exit;
}

// 대상 회원 찾기(가능하면 mb_id 우선)
$target = null;
if ($mb_id !== '') {
  $target = sql_fetch("SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_id = '".sql_real_escape_string($mb_id)."' ");
}
if (!$target && $nickname !== '') {
  $target = sql_fetch("SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_nick = '".sql_real_escape_string($nickname)."' ");
}
if (!$target) {
  echo json_encode(['ok'=>false,'msg'=>'user not found']);
  exit;
}

$target_id   = $target['mb_id'];
$target_nick = $target['mb_nick'];

// ======================
// 전적(전/승/패 승률) 조회 — 월기준 (sp_cheers 해당 월만, cheer_rank.php와 동일)
// - sp_cheers 없으면 sp_cheer_stat(sport='all', term_code='all') 또는 sp_cheer 전체 누적 폴백
// ======================
$total_games = 0; $wins = 0; $losses = 0; $rate = 0.0;
$term_label = '';

$target_id_esc = sql_real_escape_string($target_id);

// 1) 월기준: sp_cheers 해당 월만 조회 (cheer_rank.php 월간과 동일 조건)
$ym_start = date('Y-m-d 00:00:00', strtotime(date('Y-m-01')));
$ym_end   = date('Y-m-d H:i:s', strtotime(date('Y-m-01') . ' +1 month'));
$chk_cheers = sql_fetch("SHOW TABLES LIKE 'sp_cheers'", false);
if ($chk_cheers && is_array($chk_cheers) && count($chk_cheers) > 0) {
    $rec = sql_fetch("
        SELECT
            COUNT(*) AS total_games,
            SUM(CASE WHEN c.settle_result = 1 THEN 1 ELSE 0 END) AS win_cnt,
            SUM(CASE WHEN c.settle_result = 0 THEN 1 ELSE 0 END) AS lose_cnt
        FROM sp_cheers c
        WHERE c.mb_id = '{$target_id_esc}'
          AND c.status = 1
          AND c.settled_at IS NOT NULL
          AND c.created_at >= '" . sql_real_escape_string($ym_start) . "'
          AND c.created_at < '" . sql_real_escape_string($ym_end) . "'
    ", false);
    if ($rec) {
        $total_games = (int)($rec['total_games'] ?? 0);
        $wins        = (int)($rec['win_cnt'] ?? 0);
        $losses      = (int)($rec['lose_cnt'] ?? 0);
        $rate        = ($total_games > 0) ? round(($wins / $total_games) * 100, 1) : 0.0;
        $term_label  = date('Y년 n월', strtotime($ym_start)) . ' 기준';
    }
}

// 2) 월기준 데이터 없으면 sp_cheer_stat(sport='all', term_code='all') 또는 구스키마 폴백
if ($total_games === 0 && $wins === 0 && $losses === 0) {
    $chk_stat = sql_fetch("SHOW TABLES LIKE 'sp_cheer_stat'", false);
    if ($chk_stat && is_array($chk_stat) && count($chk_stat) > 0) {
        // win_cnt/lose_cnt/games/win_rate 스키마
        $stat = sql_fetch("SELECT win_cnt, lose_cnt, games, win_rate FROM sp_cheer_stat WHERE mb_id = '{$target_id_esc}' AND sport = 'all' AND term_code = 'all' LIMIT 1", false);
        if ($stat && ((int)($stat['games'] ?? 0) > 0 || (int)($stat['win_cnt'] ?? 0) + (int)($stat['lose_cnt'] ?? 0) > 0)) {
            $total_games = (int)($stat['games'] ?? 0) ?: (int)($stat['win_cnt'] ?? 0) + (int)($stat['lose_cnt'] ?? 0);
            $wins        = (int)($stat['win_cnt'] ?? 0);
            $losses     = (int)($stat['lose_cnt'] ?? 0);
            $rate        = (int)($stat['win_rate'] ?? 0);
            $term_label  = '전체 기준';
        }
        if ($total_games === 0 && $wins === 0 && $losses === 0) {
            // 구스키마: wins, losses, rate
            $stat = sql_fetch("SELECT wins, losses, rate FROM sp_cheer_stat WHERE mb_id = '{$target_id_esc}' LIMIT 1", false);
            if ($stat) {
                $wins        = (int)($stat['wins'] ?? 0);
                $losses      = (int)($stat['losses'] ?? 0);
                $total_games = $wins + $losses;
                $rate        = (float)($stat['rate'] ?? 0.0);
                $term_label  = '전체 기준';
            }
        }
    }
    if ($total_games === 0 && $wins === 0 && $losses === 0) {
        $chk_cheer = sql_fetch("SHOW TABLES LIKE 'sp_cheer'", false);
        if ($chk_cheer && is_array($chk_cheer) && count($chk_cheer) > 0) {
            $rec = sql_fetch("
                SELECT
                    SUM(CASE WHEN is_settled=1 AND is_cancel=0 AND is_win=1 THEN 1 ELSE 0 END) AS wins,
                    SUM(CASE WHEN is_settled=1 AND is_cancel=0 AND is_win=0 THEN 1 ELSE 0 END) AS losses
                FROM sp_cheer WHERE mb_id = '{$target_id_esc}'
            ", false);
            if ($rec) {
                $wins        = (int)($rec['wins'] ?? 0);
                $losses      = (int)($rec['losses'] ?? 0);
                $total_games = $wins + $losses;
                $rate        = ($total_games > 0) ? round(($wins / $total_games) * 100, 1) : 0.0;
                $term_label  = '전체 기준';
            }
        }
    }
}



// 색상 규칙
$win_color  = '#e53935'; // 빨강
$lose_color = '#1e88e5'; // 파랑
$rate_color = ($rate >= 50.0) ? '#e53935' : '#1e88e5';

// ======================
// 선택 뱃지 조회 (채팅 팝업 표시용)
// ======================
$badge_name = '';
$badge_color = '';
$badge_inc = (defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/inc/badge.php')) ? G5_THEME_PATH.'/inc/badge.php' : '';
if (!$badge_inc && is_file(__DIR__.'/../../../theme/scorepoint/inc/badge.php')) $badge_inc = __DIR__.'/../../../theme/scorepoint/inc/badge.php';
if ($badge_inc) {
    include_once $badge_inc;
    if (function_exists('sp_badge_get_selected')) {
        $sel = sp_badge_get_selected($target_id);
        if ($sel && !empty($sel['name'])) {
            $badge_name = $sel['name'];
            $badge_color = trim($sel['tier_color'] ?? '') ?: '#9ca3af';
        }
    }
}

// ======================
// 팔로우 여부 조회 (sp_follow)
// ======================
$is_following = 0;
if ($is_member) {
    $inc = (defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/inc/sp_follow_alarm.php')) ? G5_THEME_PATH.'/inc/sp_follow_alarm.php' : '';
    if (!$inc && is_file(__DIR__.'/../../../theme/scorepoint/inc/sp_follow_alarm.php')) $inc = __DIR__.'/../../../theme/scorepoint/inc/sp_follow_alarm.php';
    if ($inc) { include_once $inc; }
    if (function_exists('sp_follow_alarm_tables_exist') && sp_follow_alarm_tables_exist()) {
        $me_esc = sql_real_escape_string($member['mb_id']);
        $row_f = sql_fetch("SELECT 1 FROM sp_follow WHERE follower_mb_id = '{$me_esc}' AND following_mb_id = '{$target_id_esc}' LIMIT 1", false);
        $is_following = ($row_f && !empty($row_f)) ? 1 : 0;
    }
}

// ======================
// 신고 처리(서버 저장) - mode=report
// ======================
if ($mode === 'report') {
  if (!$is_member) {
    echo json_encode(['ok'=>false,'msg'=>'로그인 후 신고 가능합니다.']);
    exit;
  }

  // 자기 자신 신고 방지
  if ($member['mb_id'] === $target_id) {
    echo json_encode(['ok'=>false,'msg'=>'본인은 신고할 수 없습니다.']);
    exit;
  }

  // 테이블이 없으면 운영자가 생성해야 함(자동생성은 위험하니 안내만)
  // CREATE TABLE g5_chat_report (
  //   id INT AUTO_INCREMENT PRIMARY KEY,
  //   reporter_id VARCHAR(255) NOT NULL,
  //   target_id   VARCHAR(255) NOT NULL,
  //   target_nick VARCHAR(255) NOT NULL,
  //   reason      VARCHAR(255) DEFAULT '',
  //   ip          VARCHAR(45)  DEFAULT '',
  //   created_at  DATETIME NOT NULL
  // ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  $report_table = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_').'chat_report';

  // 존재 여부 체크(없으면 실패 응답)
  $chk = sql_fetch("SHOW TABLES LIKE '".sql_real_escape_string($report_table)."' ");
  if (!$chk) {
    echo json_encode([
      'ok'=>false,
      'msg'=>'신고 테이블('.$report_table.')이 없습니다. 관리자에게 테이블 생성 요청하세요.'
    ]);
    exit;
  }

  $reason = trim((string)($_POST['reason'] ?? ''));
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';

  sql_query("INSERT INTO {$report_table}
    SET reporter_id='".sql_real_escape_string($member['mb_id'])."',
        target_id='".sql_real_escape_string($target_id)."',
        target_nick='".sql_real_escape_string($target_nick)."',
        reason='".sql_real_escape_string($reason)."',
        ip='".sql_real_escape_string($ip)."',
        created_at=NOW()
  ");

  // 누적 카운트(원하면 여기서 target의 누적 신고수 업데이트 가능)
  echo json_encode(['ok'=>true,'msg'=>'신고가 접수되었습니다.']);
  exit;
}

// ======================
// 메뉴 HTML 반환
// ======================
// 유저활동: 게시글 검색(기본)
$search_q = urlencode($target_nick);
$activity_url = G5_BBS_URL.'/search.php?sfl=wr_name&stx='.$search_q;

// 프론트에서 쓰기 쉽게 HTML을 같이 내려줌 (전/승/패 승률 + 월기준)
$html = '';
$html .= '<div class="sp-user-menu" data-mb-id="'.htmlspecialchars($target_id, ENT_QUOTES).'">';
$html .= '  <div class="sp-um-head">'.htmlspecialchars($target_nick, ENT_QUOTES).'</div>';
$html .= '  <div class="sp-um-row" data-role="stats">';
$html .= '    <span class="sp-um-item">전 <b>'.(int)$total_games.'</b>경기</span>';
$html .= '    <span class="sp-um-item">승: <b style="color:'.$win_color.'">'.$wins.'</b></span>';
$html .= '    <span class="sp-um-item">패: <b style="color:'.$lose_color.'">'.$losses.'</b></span>';
$html .= '    <span class="sp-um-item">승률: <b style="color:'.$rate_color.'">'.$rate.'%</b></span>';
if ($term_label !== '') {
    $html .= '    <span class="sp-um-term">('.$term_label.')</span>';
}
$html .= '  </div>';
$html .= '  <div class="sp-um-actions">';
$html .= '    <a class="sp-um-btn" href="'.$activity_url.'" target="_blank" rel="noopener">유저활동</a>';
$html .= '    <button type="button" class="sp-um-btn" data-action="ignore">무시하기</button>';
$html .= '    <button type="button" class="sp-um-btn" data-action="report">신고하기</button>';
$html .= '    <button type="button" class="sp-um-btn" data-action="gift" disabled>선물하기(준비중)</button>';
$html .= '  </div>';
$html .= '</div>';

echo json_encode([
  'ok' => true,
  'target' => [
    'mb_id' => $target_id,
    'mb_nick' => $target_nick,
    'total_games' => $total_games,
    'wins' => $wins,
    'losses' => $losses,
    'rate' => $rate,
    'term_label' => $term_label,
    'is_following' => (int)$is_following,
    'badge_name' => $badge_name,
    'badge_color' => $badge_color,
  ],
  'html' => $html
]);