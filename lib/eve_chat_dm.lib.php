<?php
/**
 * 이브알바 1:1 DM 채팅 공통 함수
 */
if (!defined('_GNUBOARD_')) exit;

function eve_member_is_female_normal($member)
{
    if (!$member || empty($member['mb_id'])) {
        return false;
    }
    if (!empty($GLOBALS['is_admin'])) {
        return true;
    }
    $type = isset($member['mb_1']) ? $member['mb_1'] : '';
    $sex  = isset($member['mb_sex']) ? $member['mb_sex'] : '';
    return ($type === 'normal' && $sex === 'F');
}

function eve_member_is_biz($member)
{
    if (!$member || empty($member['mb_id'])) {
        return false;
    }
    $type = isset($member['mb_1']) ? $member['mb_1'] : '';
    return in_array($type, array('biz', 'business'), true);
}

function eve_chat_dm_ensure_tables()
{
    global $g5;

    if (!isset($g5['chat_dm_room_table'], $g5['chat_dm_msg_table'])) {
        include_once(G5_PLUGIN_PATH . '/chat/_common.php');
    }

    $tbl_room = $g5['chat_dm_room_table'];
    $tbl_msg  = $g5['chat_dm_msg_table'];

    @sql_query("CREATE TABLE IF NOT EXISTS `{$tbl_room}` (
      `dm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `jr_id` INT UNSIGNED NOT NULL DEFAULT 0,
      `female_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
      `biz_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
      `biz_visible` TINYINT NOT NULL DEFAULT 0,
      `last_msg_preview` VARCHAR(255) NOT NULL DEFAULT '',
      `last_msg_at` DATETIME DEFAULT NULL,
      `female_unread` INT UNSIGNED NOT NULL DEFAULT 0,
      `biz_unread` INT UNSIGNED NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL,
      PRIMARY KEY (`dm_id`),
      UNIQUE KEY `uk_pair_job` (`female_mb_id`, `biz_mb_id`, `jr_id`),
      KEY `idx_female` (`female_mb_id`, `last_msg_at`),
      KEY `idx_biz` (`biz_mb_id`, `biz_visible`, `last_msg_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);

    @sql_query("CREATE TABLE IF NOT EXISTS `{$tbl_msg}` (
      `msg_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `dm_id` INT UNSIGNED NOT NULL DEFAULT 0,
      `sender_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
      `msg_content` TEXT NOT NULL,
      `msg_read_at` DATETIME DEFAULT NULL,
      `msg_datetime` DATETIME NOT NULL,
      PRIMARY KEY (`msg_id`),
      KEY `idx_dm` (`dm_id`, `msg_id`),
      KEY `idx_sender` (`sender_mb_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
}

function eve_chat_dm_unread_count($mb_id)
{
    global $g5;

    if (!$mb_id) {
        return 0;
    }

    eve_chat_dm_ensure_tables();

    $tbl_room = $g5['chat_dm_room_table'];
    $mb_id = sql_real_escape_string($mb_id);

    $row = sql_fetch("
        SELECT SUM(female_unread) AS fu, SUM(biz_unread) AS bu
        FROM {$tbl_room}
        WHERE female_mb_id = '{$mb_id}' OR (biz_mb_id = '{$mb_id}' AND biz_visible = 1)
    ");

    if (!$row) {
        return 0;
    }

    return (int)($row['fu'] ?? 0) + (int)($row['bu'] ?? 0);
}

function eve_chat_dm_room_member($room, $mb_id)
{
    if (!$room || !$mb_id) {
        return '';
    }
    if ($room['female_mb_id'] === $mb_id) {
        return 'female';
    }
    if ($room['biz_mb_id'] === $mb_id) {
        return 'biz';
    }
    return '';
}

function eve_chat_dm_get_job_label($jr_row)
{
    if (!$jr_row) {
        return '';
    }
    $nick = !empty($jr_row['jr_nickname']) ? trim($jr_row['jr_nickname']) : '';
    $comp = !empty($jr_row['jr_company']) ? trim($jr_row['jr_company']) : '';
    return $nick ?: $comp ?: '업소';
}

function eve_chat_dm_format_room($room, $viewer_mb_id, $member_table = null)
{
    global $g5;

    if (!$member_table) {
        $member_table = $g5['member_table'];
    }

    $role = eve_chat_dm_room_member($room, $viewer_mb_id);
    $other_id = ($role === 'female') ? $room['biz_mb_id'] : $room['female_mb_id'];
    $other = sql_fetch("SELECT mb_id, mb_nick, mb_1 FROM {$member_table} WHERE mb_id = '" . sql_real_escape_string($other_id) . "' LIMIT 1");

    $jr = null;
    if (!empty($room['jr_id'])) {
        $jr = sql_fetch("SELECT jr_id, jr_nickname, jr_company, jr_title FROM g5_jobs_register WHERE jr_id = '" . (int)$room['jr_id'] . "' LIMIT 1");
    }

    $is_biz_other = $other && eve_member_is_biz($other);
    $unread = ($role === 'female') ? (int)$room['female_unread'] : (int)$room['biz_unread'];

    return array(
        'dm_id' => (int)$room['dm_id'],
        'jr_id' => (int)$room['jr_id'],
        'role' => $role,
        'other_mb_id' => $other_id,
        'other_nick' => $other ? get_text($other['mb_nick']) : '알 수 없음',
        'other_is_biz' => $is_biz_other ? 1 : 0,
        'job_label' => eve_chat_dm_get_job_label($jr),
        'job_title' => $jr && !empty($jr['jr_title']) ? get_text($jr['jr_title']) : '',
        'last_preview' => get_text($room['last_msg_preview']),
        'last_at' => $room['last_msg_at'],
        'unread' => $unread,
        'can_reply' => ($role === 'female' || (int)$room['biz_visible'] === 1) ? 1 : 0,
        'biz_visible' => (int)$room['biz_visible'],
    );
}
