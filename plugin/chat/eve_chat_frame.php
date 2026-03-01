<?php
// /plugin/chat/eve_chat_frame.php — iframe 전용 채팅 (독립 HTML)
@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
@ini_set('display_errors', '0');

$_frame_dir = __DIR__;
$_common_path = $_frame_dir.'/../../common.php';
if (!is_file($_common_path)) $_common_path = $_SERVER['DOCUMENT_ROOT'].'/common.php';
if (is_file($_common_path)) include_once($_common_path);
@include_once(G5_PLUGIN_PATH.'/chat/_common.php');

$_is_admin  = (isset($is_admin) && $is_admin) ? true : false;
$_is_member = (isset($member) && is_array($member) && isset($member['mb_id']) && $member['mb_id']) ? true : false;
$_can_chat  = false;
$_deny      = '';

if (!$_is_member) {
    $_deny = 'login';
} elseif ($_is_admin) {
    $_can_chat = true;
} else {
    $type = isset($member['mb_1']) ? $member['mb_1'] : '';
    $sex  = isset($member['mb_sex']) ? $member['mb_sex'] : '';
    if ($type === 'normal' && $sex === 'F') $_can_chat = true;
    else $_deny = 'denied';
}

$_my_id   = $_is_member ? $member['mb_id'] : '';
$_my_nick = $_is_member ? (isset($member['mb_nick']) ? $member['mb_nick'] : '') : '';
$_ajax    = (defined('G5_PLUGIN_URL') ? G5_PLUGIN_URL : '/plugin').'/chat/chat_ajax.php';
$_login   = (defined('G5_BBS_URL') ? G5_BBS_URL : '/bbs').'/login.php';

$_cfg = @sql_fetch(" SELECT * FROM {$g5['chat_config_table']} LIMIT 1 ");
$_notice = '';
if ($_cfg && isset($_cfg['cf_notice_text']) && $_cfg['cf_notice_text'] !== '') {
    $_notice = $_cfg['cf_notice_text'];
}
?><!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>이브알바 실시간 채팅</title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
<style>
/* ===================== RESET & VARS ===================== */
:root {
  --hot-pink: #FF1B6B;
  --deep-pink: #C90050;
  --light-pink: #FF6BA8;
  --pale-pink: #FFD6E7;
  --dark: #1A0010;
  --dark2: #2D0020;
  --gold: #FFD700;
  --orange: #FF6B35;
  --bg: #FFF0F5;
  --chat-bg: #FDF5F9;
  --white: #ffffff;
  --gray: #888;
  --border: #F0E0E8;
  --msg-my: linear-gradient(135deg, var(--orange), var(--hot-pink));
  --msg-other: #ffffff;
  --shadow: 0 8px 32px rgba(255,27,107,.15);
  --shadow-sm: 0 2px 12px rgba(255,27,107,.1);
  --radius: 16px;
}
* { margin:0; padding:0; box-sizing:border-box; }
html, body {
  height: 100%;
  overflow: hidden;
  font-family: 'Noto Sans KR', sans-serif;
  background: var(--white);
  color: var(--dark);
  -webkit-text-size-adjust: 100%;
}
a { text-decoration:none; color:inherit; }
button { cursor:pointer; font-family:inherit; }

/* ===================== CHAT WINDOW ===================== */
.chat-window {
  width: 100%;
  height: 100%;
  background: var(--white);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  border: none;
}

/* -------- CHAT HEADER -------- */
.chat-header {
  background: linear-gradient(135deg, var(--dark2), var(--hot-pink));
  padding: 14px 16px 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
  position: relative;
  overflow: hidden;
}
.chat-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.06), transparent);
  animation: shimmer-h 3s linear infinite;
}
@keyframes shimmer-h {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}
.chat-header-icon { font-size: 22px; flex-shrink: 0; position:relative; z-index:1; }
.chat-header-info { flex: 1; min-width: 0; position:relative; z-index:1; }
.chat-header-title {
  color: var(--white);
  font-size: 15px;
  font-weight: 900;
  line-height: 1.2;
  display: flex;
  align-items: center;
  gap: 7px;
}
.chat-header-region {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: rgba(255,255,255,.25);
  border-radius: 10px;
  padding: 2px 9px;
  font-size: 12px;
  font-weight: 700;
  color: var(--white);
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255,255,255,.3);
  cursor: pointer;
  transition: background .2s;
  white-space: nowrap;
}
.chat-header-region:hover { background: rgba(255,255,255,.35); }
.chat-header-region .arrow {
  font-size: 9px;
  margin-left: 2px;
  transition: transform .25s;
  display: inline-block;
}
.chat-header-region.open .arrow { transform: rotate(180deg); }
.chat-header-sub {
  color: rgba(255,255,255,.75);
  font-size: 11px;
  margin-top: 3px;
  display: flex;
  align-items: center;
  gap: 6px;
  position:relative; z-index:1;
}
.online-dot {
  width: 7px;
  height: 7px;
  background: #4ADE80;
  border-radius: 50%;
  animation: blink-dot 1.5s ease-in-out infinite;
  flex-shrink: 0;
}
@keyframes blink-dot {
  0%,100% { opacity:1; }
  50% { opacity:.3; }
}
.chat-header-actions {
  display: flex;
  gap: 6px;
  flex-shrink: 0;
  position:relative; z-index:1;
}
.chat-icon-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: rgba(255,255,255,.18);
  border: 1px solid rgba(255,255,255,.25);
  color: var(--white);
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background .2s;
  backdrop-filter: blur(4px);
}
.chat-icon-btn:hover { background: rgba(255,255,255,.32); }
/* -------- REGION DROPDOWN -------- */
.region-dropdown {
  background: var(--white);
  border-bottom: 2px solid var(--border);
  overflow: hidden;
  max-height: 0;
  transition: max-height .3s cubic-bezier(.4,0,.2,1), padding .3s;
  flex-shrink: 0;
}
.region-dropdown.open { max-height: 200px; }
.region-dropdown-inner { padding: 12px 14px 14px; }
.rd-title {
  font-size: 11px;
  font-weight: 700;
  color: var(--gray);
  margin-bottom: 9px;
  letter-spacing: .5px;
  display: flex;
  align-items: center;
  gap: 5px;
}
.region-grid { display: flex; flex-wrap: wrap; gap: 6px; }
.region-chip {
  padding: 5px 12px;
  border-radius: 20px;
  border: 1.5px solid var(--border);
  background: var(--white);
  font-size: 12px;
  font-weight: 700;
  color: #666;
  cursor: pointer;
  transition: all .18s;
  white-space: nowrap;
  position: relative;
  overflow: hidden;
}
.region-chip::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, var(--orange), var(--hot-pink));
  opacity: 0;
  transition: opacity .18s;
}
.region-chip span { position: relative; z-index: 1; }
.region-chip:hover { border-color: var(--light-pink); color: var(--hot-pink); }
.region-chip.active {
  border-color: transparent;
  color: var(--white);
  box-shadow: 0 2px 8px rgba(255,27,107,.3);
}
.region-chip.active::before { opacity: 1; }
.region-chip-all {
  background: linear-gradient(135deg, var(--dark2), var(--hot-pink));
  color: var(--white);
  border-color: transparent;
  box-shadow: 0 2px 8px rgba(255,27,107,.3);
}
.region-chip-all::before { display: none; }
.region-chip-all.active { box-shadow: 0 3px 14px rgba(255,27,107,.5); }
.rd-user-count {
  margin-top: 10px;
  padding: 7px 12px;
  background: linear-gradient(135deg, #fff5f8, #fff0f5);
  border-radius: 10px;
  border: 1.5px solid var(--pale-pink);
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 12px;
  color: #666;
}
.rd-user-count strong { color: var(--hot-pink); font-weight: 900; font-family: 'Outfit', sans-serif; font-size: 14px; }

/* -------- CHAT MESSAGES AREA -------- */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 14px 12px;
  background: var(--chat-bg);
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-height: 0;
  scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--pale-pink); border-radius: 2px; }

.chat-date-divider {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 8px 0 4px;
}
.chat-date-divider::before, .chat-date-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}
.chat-date-text {
  font-size: 10px;
  color: var(--gray);
  font-weight: 600;
  padding: 2px 8px;
  background: #f5e8f0;
  border-radius: 10px;
  white-space: nowrap;
}

.chat-system {
  text-align: center;
  font-size: 11px;
  color: var(--gray);
  padding: 4px 12px;
  background: rgba(255,255,255,.7);
  border-radius: 10px;
  align-self: center;
  border: 1px solid var(--border);
  margin: 2px 0;
}

.msg-row {
  display: flex;
  align-items: flex-end;
  gap: 6px;
  animation: msgIn .2s ease both;
}
@keyframes msgIn {
  from { opacity:0; transform:translateY(6px); }
  to   { opacity:1; transform:translateY(0); }
}
.msg-row.me { flex-direction: row-reverse; }
.msg-row.cont .msg-avatar { visibility: hidden; }

.msg-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--pale-pink), var(--light-pink));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
  border: 2px solid var(--white);
  box-shadow: 0 2px 8px rgba(255,27,107,.15);
  align-self: flex-end;
}
.msg-avatar.admin-avatar { background: linear-gradient(135deg, var(--dark2), var(--hot-pink)); }
.msg-avatar.me-avatar { background: linear-gradient(135deg, var(--orange), var(--hot-pink)); }

.msg-content { max-width: 230px; display: flex; flex-direction: column; gap: 3px; }
.msg-row.me .msg-content { align-items: flex-end; }
.msg-name {
  font-size: 11px;
  font-weight: 700;
  color: #666;
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 0 4px;
  cursor: pointer;
}
.msg-name .region-tag {
  background: var(--pale-pink);
  color: var(--hot-pink);
  border-radius: 6px;
  padding: 1px 6px;
  font-size: 10px;
  font-weight: 700;
}
.msg-name .admin-tag {
  background: linear-gradient(135deg, var(--dark2), var(--hot-pink));
  color: var(--white);
  border-radius: 6px;
  padding: 1px 6px;
  font-size: 10px;
  font-weight: 700;
}
.msg-bubble {
  padding: 9px 13px;
  border-radius: 16px;
  font-size: 13px;
  line-height: 1.6;
  word-break: break-word;
  position: relative;
  max-width: 100%;
}
.msg-bubble.other {
  background: var(--white);
  color: var(--dark);
  border-radius: 4px 16px 16px 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,.06);
  border: 1.5px solid var(--border);
}
.msg-bubble.me-bubble {
  background: linear-gradient(135deg, var(--orange), var(--hot-pink));
  color: var(--white);
  border-radius: 16px 4px 16px 16px;
  box-shadow: 0 3px 12px rgba(255,27,107,.3);
}
.msg-meta {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 4px;
}
.msg-time { font-size: 10px; color: #bbb; white-space: nowrap; }
.msg-read { font-size: 10px; color: var(--orange); font-weight: 700; }

/* 공지 배너 */
.chat-notice-wrap { padding: 10px 12px 0; background: var(--chat-bg); flex-shrink: 0; }
.chat-notice {
  background: linear-gradient(135deg, #fff8f0, #fff0e8);
  border: 1.5px solid #FFD0AA;
  border-radius: 10px;
  padding: 8px 12px;
  display: flex;
  align-items: flex-start;
  gap: 7px;
  margin-bottom: 4px;
}
.notice-icon { font-size: 14px; flex-shrink: 0; margin-top: 1px; }
.notice-text { font-size: 11px; color: #AA5500; line-height: 1.6; }
.notice-text strong { font-weight: 700; }

/* -------- CHAT INPUT -------- */
.chat-input-area {
  border-top: 2px solid var(--border);
  background: var(--white);
  padding: 10px 12px;
  flex-shrink: 0;
}
.chat-input-row {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  background: #fdf5f9;
  border: 1.5px solid var(--border);
  border-radius: 24px;
  padding: 8px 8px 8px 14px;
  transition: border-color .2s, box-shadow .2s;
}
.chat-input-row:focus-within {
  border-color: var(--hot-pink);
  box-shadow: 0 0 0 3px rgba(255,27,107,.08);
}
.chat-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 13px;
  color: var(--dark);
  resize: none;
  outline: none;
  max-height: 80px;
  min-height: 22px;
  line-height: 1.5;
  font-family: 'Noto Sans KR', sans-serif;
}
.chat-input::placeholder { color: #bbb; }
.chat-send-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--orange), var(--hot-pink));
  border: none;
  color: var(--white);
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform .2s, box-shadow .2s;
  box-shadow: 0 3px 10px rgba(255,27,107,.3);
}
.chat-send-btn:hover { transform: scale(1.08); box-shadow: 0 5px 16px rgba(255,27,107,.45); }
.chat-send-btn:active { transform: scale(.95); }
.chat-send-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }
.chat-input-hint {
  margin-top: 6px;
  font-size: 10px;
  color: #ccc;
  text-align: center;
}

.chat-status {
  display: none;
  padding: 6px 10px;
  font-size: 11px;
  color: #e53935;
  background: #fff5f5;
  border-top: 1px solid var(--border);
  text-align: center;
}

/* -------- LOGIN / DENY WALL -------- */
.chat-login-wall {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 32px 20px;
  text-align: center;
  background: var(--chat-bg);
  gap: 14px;
}
.clw-icon { font-size: 48px; margin-bottom: 4px; }
.clw-title { font-size: 16px; font-weight: 900; color: var(--dark); }
.clw-sub { font-size: 13px; color: var(--gray); line-height: 1.7; }
.btn-clw-login {
  padding: 11px 28px;
  background: linear-gradient(135deg, var(--orange), var(--hot-pink));
  color: var(--white);
  border: none;
  border-radius: 24px;
  font-size: 14px;
  font-weight: 900;
  box-shadow: 0 4px 14px rgba(255,27,107,.3);
  animation: pulse-glow 2s infinite;
  transition: transform .2s;
}
.btn-clw-login:hover { transform: scale(1.04); }
@keyframes pulse-glow { 0%,100%{box-shadow:0 4px 14px rgba(255,27,107,.3)}50%{box-shadow:0 6px 22px rgba(255,27,107,.55)} }

/* -------- MODAL OVERLAY -------- */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 2000;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.modal-overlay.show { display: flex; }
.modal-box {
  background: var(--white);
  border-radius: 18px;
  width: 100%;
  max-width: 360px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  animation: modalIn .25s ease both;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
}
@keyframes modalIn {
  from { opacity:0; transform:scale(.94) translateY(12px); }
  to   { opacity:1; transform:scale(1)   translateY(0); }
}
.modal-head {
  background: linear-gradient(135deg, var(--dark2), var(--hot-pink));
  padding: 16px 18px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}
.modal-head-icon { font-size: 22px; }
.modal-head-title { color: var(--white); font-size: 15px; font-weight: 900; flex: 1; }
.modal-close {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: rgba(255,255,255,.2);
  border: 1px solid rgba(255,255,255,.3);
  color: var(--white);
  font-size: 13px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: background .2s;
  flex-shrink: 0;
}
.modal-close:hover { background: rgba(255,255,255,.38); }
.modal-body { padding: 18px; overflow-y: auto; flex: 1; }
.modal-body::-webkit-scrollbar { width: 4px; }
.modal-body::-webkit-scrollbar-thumb { background: var(--pale-pink); border-radius: 2px; }

.rules-list { display: flex; flex-direction: column; gap: 10px; }
.rule-item {
  display: flex; gap: 10px; align-items: flex-start;
  padding: 11px 13px; border-radius: 12px;
  background: #fff8fb; border: 1.5px solid var(--border);
  transition: border-color .2s;
}
.rule-item:hover { border-color: var(--pale-pink); }
.rule-num {
  width: 22px; height: 22px; border-radius: 50%;
  background: linear-gradient(135deg, var(--orange), var(--hot-pink));
  color: var(--white); font-size: 11px; font-weight: 900;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; font-family: 'Outfit', sans-serif;
}
.rule-text { font-size: 12px; color: #444; line-height: 1.7; }
.rule-text strong { color: var(--hot-pink); font-weight: 700; }
.rules-footer {
  margin-top: 14px; padding: 10px 13px;
  background: linear-gradient(135deg, #fff0e8, #fff8f0);
  border: 1.5px solid #FFD0AA; border-radius: 10px;
  font-size: 11px; color: #AA5500; line-height: 1.7; text-align: center;
}

.ignore-empty { text-align: center; padding: 32px 20px; color: var(--gray); }
.ignore-empty-icon { font-size: 44px; margin-bottom: 10px; opacity: .5; }
.ignore-empty-title { font-size: 14px; font-weight: 700; color: #bbb; margin-bottom: 5px; }
.ignore-empty-sub { font-size: 12px; color: #ccc; }
.ignore-list { display: flex; flex-direction: column; gap: 8px; }
.ignore-item {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 13px; border-radius: 12px;
  background: #fff8fb; border: 1.5px solid var(--border);
}
.ignore-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: linear-gradient(135deg, var(--pale-pink), var(--light-pink));
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0;
}
.ignore-info { flex: 1; min-width: 0; }
.ignore-nick { font-size: 13px; font-weight: 700; color: var(--dark); }
.ignore-nick span { font-size: 11px; font-weight: 400; color: var(--gray); margin-left: 5px; }
.ignore-since { font-size: 11px; color: #bbb; margin-top: 1px; }
.btn-unignore {
  padding: 5px 12px; border-radius: 10px;
  border: 1.5px solid #ffcdd2; background: #ffebee;
  color: #e53935; font-size: 11px; font-weight: 700;
  cursor: pointer; transition: all .18s; white-space: nowrap; flex-shrink: 0;
}
.btn-unignore:hover { background: #e53935; color: white; border-color: #e53935; }
.ignore-header-bar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1.5px solid var(--border);
}
.ignore-count { font-size: 12px; color: var(--gray); }
.ignore-count strong { color: var(--hot-pink); font-weight: 900; font-family: 'Outfit', sans-serif; font-size: 15px; }

.report-reasons { display: flex; flex-direction: column; gap: 6px; margin: 12px 0; }
.report-reason {
  padding: 10px 14px; border-radius: 10px;
  border: 1.5px solid var(--border); background: var(--white);
  cursor: pointer; font-size: 13px; transition: all .15s; color: #333;
}
.report-reason:hover { border-color: var(--hot-pink); background: #fff5f8; }
.report-reason.selected { border-color: var(--hot-pink); background: #fff0f5; font-weight: 700; color: var(--hot-pink); }

.nick-menu {
  position: fixed; z-index: 2100;
  min-width: 140px; background: var(--white);
  border: 1.5px solid var(--border); border-radius: 12px;
  box-shadow: 0 6px 24px rgba(0,0,0,.18);
  padding: 6px; display: none; font-size: 12px;
}
.nick-menu-item {
  padding: 8px 12px; border-radius: 8px; cursor: pointer;
  display: flex; align-items: center; gap: 6px; transition: background .15s; color: #333;
}
.nick-menu-item:hover { background: #fff0f5; }
.nick-menu-item.danger { color: #e53935; }
.nick-menu-item.danger:hover { background: #ffebee; }

::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--pale-pink); border-radius: 3px; }
</style>
</head>
<body>

<div class="chat-window" id="chatWindow">
  <!-- 헤더 -->
  <div class="chat-header">
    <div class="chat-header-icon">💬</div>
    <div class="chat-header-info">
      <div class="chat-header-title">
        실시간 채팅
        <button class="chat-header-region" id="regionToggle">
          <span id="currentRegionLabel">전체</span>
          <span class="arrow" id="regionArrow">▼</span>
        </button>
      </div>
      <div class="chat-header-sub">
        <span class="online-dot"></span>
        <span>👩 <span id="onlineNum">0</span>명 접속 중</span>
      </div>
    </div>
    <div class="chat-header-actions">
      <button class="chat-icon-btn" title="채팅규정" id="btnRules">📢</button>
      <button class="chat-icon-btn" title="새로고침" id="btnRefresh">🔄</button>
      <button class="chat-icon-btn" title="무시목록" id="btnIgnore">🙈</button>
    </div>
  </div>

  <!-- 지역 드롭다운 -->
  <div class="region-dropdown" id="regionDropdown">
    <div class="region-dropdown-inner">
      <div class="rd-title">📍 지역을 선택하세요</div>
      <div class="region-grid" id="regionGrid">
        <button class="region-chip region-chip-all active" data-region="전체"><span>전체</span></button>
        <button class="region-chip" data-region="서울"><span>서울</span></button>
        <button class="region-chip" data-region="경기"><span>경기</span></button>
        <button class="region-chip" data-region="인천"><span>인천</span></button>
        <button class="region-chip" data-region="부산"><span>부산</span></button>
        <button class="region-chip" data-region="대구"><span>대구</span></button>
        <button class="region-chip" data-region="광주"><span>광주</span></button>
        <button class="region-chip" data-region="대전"><span>대전</span></button>
        <button class="region-chip" data-region="울산"><span>울산</span></button>
        <button class="region-chip" data-region="강원"><span>강원</span></button>
        <button class="region-chip" data-region="경남"><span>경남</span></button>
        <button class="region-chip" data-region="경북"><span>경북</span></button>
        <button class="region-chip" data-region="전남"><span>전남</span></button>
        <button class="region-chip" data-region="세종"><span>세종</span></button>
        <button class="region-chip" data-region="제주"><span>제주</span></button>
      </div>
      <div class="rd-user-count">
        <span>👩</span>
        <span id="regionUserText"><strong id="regionUserNum">0</strong>명이 <strong>전체</strong> 채팅 중</span>
      </div>
    </div>
  </div>

  <?php if ($_deny === 'login') { ?>
  <div class="chat-login-wall">
    <div class="clw-icon">🔒</div>
    <div class="clw-title">로그인이 필요합니다</div>
    <div class="clw-sub">이브알바 실시간 채팅은<br>일반회원(여성)만 이용 가능합니다.</div>
    <button class="btn-clw-login" onclick="window.top.location.href='<?php echo $_login; ?>'">로그인</button>
  </div>
  <?php } elseif ($_deny === 'denied') { ?>
  <div class="chat-login-wall">
    <div class="clw-icon">🚫</div>
    <div class="clw-title">이용이 제한됩니다</div>
    <div class="clw-sub">실시간 채팅은<br><strong>일반회원(여성)</strong>만 이용 가능합니다.</div>
  </div>
  <?php } else { ?>

  <?php if ($_notice) { ?>
  <div class="chat-notice-wrap" id="chatNoticeWrap">
    <div class="chat-notice">
      <span class="notice-icon">📢</span>
      <div class="notice-text"><strong>[공지]</strong> <?php echo nl2br(htmlspecialchars($_notice)); ?></div>
    </div>
  </div>
  <?php } ?>

  <div class="chat-messages" id="chatMessages">
    <div class="chat-system">💗 이브알바 채팅방에 오신 것을 환영합니다!</div>
  </div>

  <div class="chat-status" id="chatStatus"></div>

  <div class="chat-input-area">
    <div class="chat-input-row">
      <textarea class="chat-input" id="chatInput" placeholder="메시지를 입력하세요 (Enter 전송)" rows="1" autocomplete="off"></textarea>
      <button class="chat-send-btn" id="chatSendBtn">➤</button>
    </div>
    <div class="chat-input-hint">Enter 전송 &nbsp;·&nbsp; Shift+Enter 줄바꿈</div>
  </div>
  <?php } ?>
</div>

<!-- 채팅규정 모달 -->
<div class="modal-overlay" id="rulesModal">
  <div class="modal-box">
    <div class="modal-head">
      <span class="modal-head-icon">📋</span>
      <span class="modal-head-title">채팅 규정</span>
      <button class="modal-close" onclick="document.getElementById('rulesModal').classList.remove('show')">✕</button>
    </div>
    <div class="modal-body">
      <div class="rules-list">
        <div class="rule-item"><div class="rule-num">1</div><div class="rule-text"><strong>욕설·비방 금지</strong><br>다른 이용자를 향한 욕설, 비방, 인신공격은 즉시 이용 제한됩니다.</div></div>
        <div class="rule-item"><div class="rule-num">2</div><div class="rule-text"><strong>광고·스팸 금지</strong><br>허가되지 않은 광고, 홍보, 스팸 메시지 작성은 금지됩니다.</div></div>
        <div class="rule-item"><div class="rule-num">3</div><div class="rule-text"><strong>도배 금지</strong><br>같은 내용의 반복 작성(도배)은 자동으로 차단됩니다.</div></div>
        <div class="rule-item"><div class="rule-num">4</div><div class="rule-text"><strong>개인정보 보호</strong><br>자신 또는 타인의 연락처·주소 등 개인정보를 공개하지 마세요.</div></div>
        <div class="rule-item"><div class="rule-num">5</div><div class="rule-text"><strong>음란·불법 콘텐츠 금지</strong><br>음란물, 불법 정보 유포 시 법적 조치가 취해질 수 있습니다.</div></div>
        <div class="rule-item"><div class="rule-num">6</div><div class="rule-text"><strong>분쟁 유발 금지</strong><br>다른 이용자와의 의도적 분쟁 유발, 타인 비하 표현을 삼가주세요.</div></div>
        <div class="rule-item"><div class="rule-num">7</div><div class="rule-text"><strong>미성년자 이용 불가</strong><br>본 채팅 서비스는 만 18세 이상만 이용할 수 있습니다.</div></div>
      </div>
      <div class="rules-footer">⚠️ 규정 위반 시 <strong>경고 → 일시정지 → 영구정지</strong> 순으로 제재됩니다.<br>문의: 고객센터 <strong>1588-0000</strong></div>
    </div>
  </div>
</div>

<!-- 무시목록 모달 -->
<div class="modal-overlay" id="ignoreModal">
  <div class="modal-box">
    <div class="modal-head">
      <span class="modal-head-icon">🙈</span>
      <span class="modal-head-title">무시 목록</span>
      <button class="modal-close" onclick="document.getElementById('ignoreModal').classList.remove('show')">✕</button>
    </div>
    <div class="modal-body">
      <div class="ignore-header-bar">
        <div class="ignore-count">차단된 사용자 <strong id="ignoreCountNum">0</strong>명</div>
        <span style="font-size:11px;color:#bbb;">무시 해제 시 메시지가 다시 표시됩니다</span>
      </div>
      <div class="ignore-list" id="ignoreList"></div>
    </div>
  </div>
</div>

<!-- 신고 모달 -->
<div class="modal-overlay" id="reportModal">
  <div class="modal-box">
    <div class="modal-head">
      <span class="modal-head-icon">🚨</span>
      <span class="modal-head-title">신고하기 — <span id="reportTarget"></span></span>
      <button class="modal-close" onclick="document.getElementById('reportModal').classList.remove('show')">✕</button>
    </div>
    <div class="modal-body">
      <div style="font-size:12px;color:var(--gray);margin-bottom:8px;">신고 사유를 선택해 주세요.</div>
      <div class="report-reasons" id="reportReasons">
        <div class="report-reason" data-reason="욕설/비방">🤬 욕설·비방</div>
        <div class="report-reason" data-reason="광고/스팸">📢 광고·스팸</div>
        <div class="report-reason" data-reason="도배">🔁 도배</div>
        <div class="report-reason" data-reason="음란/불법">🔞 음란·불법 콘텐츠</div>
        <div class="report-reason" data-reason="개인정보 노출">🔓 개인정보 노출</div>
        <div class="report-reason" data-reason="기타">📝 기타</div>
      </div>
      <button class="btn-clw-login" id="reportSubmitBtn" style="width:100%;margin-top:8px" disabled>신고 접수</button>
    </div>
  </div>
</div>

<!-- 닉네임 클릭 메뉴 -->
<div class="nick-menu" id="nickMenu">
  <div class="nick-menu-item" id="menuIgnore">🙈 무시하기</div>
  <div class="nick-menu-item danger" id="menuReport">🚨 신고하기</div>
</div>

<?php if ($_can_chat) { ?>
<script>
(function(){
  var AJAX = "<?php echo $_ajax; ?>";
  var MY_ID = "<?php echo addslashes($_my_id); ?>";
  var MY_NICK = "<?php echo addslashes($_my_nick); ?>";

  var state = { last_id:0, region:'전체', freeze:0, sending:false, lastSendTs:0 };
  var pollTimer = null, pingTimer = null, idleTimer = null;
  var lastActiveTs = Date.now(), stoppedByIdle = false;

  var $ = function(id){ return document.getElementById(id); };
  var el = {
    msgs: $('chatMessages'), input: $('chatInput'),
    sendBtn: $('chatSendBtn'), onlineNum: $('onlineNum'), status: $('chatStatus'),
    regionBtn: $('regionToggle'), regionDD: $('regionDropdown'),
    regionLabel: $('currentRegionLabel'), rdCountNum: $('regionUserNum'),
    rdCountText: $('regionUserText'), nickMenu: $('nickMenu'),
    ignoreList: $('ignoreList'), ignoreCountNum: $('ignoreCountNum')
  };

  var IGNORE_KEY = 'eve_chat_ignored_v1';
  var menuTarget = {mb_id:'',nick:''};
  var reportReason = '';

  function loadIgnored(){ try{return JSON.parse(localStorage.getItem(IGNORE_KEY)||'[]')||[];}catch(e){return[];} }
  function saveIgnored(a){ try{localStorage.setItem(IGNORE_KEY,JSON.stringify(a||[]));}catch(e){} }
  function isIgnored(id){ return loadIgnored().indexOf(String(id))>=0; }
  function toggleIgnore(id){
    id=String(id||'');if(!id) return;
    var a=loadIgnored(),idx=a.indexOf(id),ign;
    if(idx>=0){a.splice(idx,1);ign=false;}else{a.push(id);ign=true;}
    saveIgnored(a);
    var nodes=el.msgs.querySelectorAll('.msg-row[data-mb="'+id+'"]');
    nodes.forEach(function(n){n.style.display=ign?'none':'';});
    return ign;
  }

  function escHtml(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
  function setStatus(t){
    if(!el.status) return;
    if(!t){el.status.style.display='none';el.status.textContent='';return;}
    el.status.textContent=t;el.status.style.display='block';
  }
  function fmtTime(dt){
    if(!dt) return '';
    var d=new Date(dt.replace(/-/g,'/'));
    return String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0');
  }
  function addSystemMsg(text){
    var s=document.createElement('div');
    s.className='chat-system';s.textContent=text;
    el.msgs.appendChild(s);el.msgs.scrollTop=el.msgs.scrollHeight;
  }

  function appendMessages(list){
    if(!el.msgs||!Array.isArray(list)) return;
    list.forEach(function(row){
      var cid=parseInt(row.cm_id||0,10)||0;
      state.last_id=Math.max(state.last_id,cid);
      var mbid=row.mb_id||'';
      if(mbid&&isIgnored(mbid)) return;
      var isMe=(mbid===MY_ID);

      var r=document.createElement('div');
      r.className='msg-row'+(isMe?' me':'');
      r.dataset.mb=mbid;
      r.style.marginTop='8px';

      var av=document.createElement('div');
      av.className='msg-avatar'+(isMe?' me-avatar':'');
      av.textContent='👩';

      var content=document.createElement('div');
      content.className='msg-content';

      if(!isMe){
        var name=document.createElement('div');
        name.className='msg-name';
        name.innerHTML='👩 <strong>'+escHtml(row.cm_nick||'')+'</strong>'
          +(row.cm_region&&row.cm_region!=='전체'?'<span class="region-tag">'+escHtml(row.cm_region)+'</span>':'');
        name.dataset.mb=mbid;
        name.dataset.nick=row.cm_nick||'';
        name.addEventListener('click',function(ev){
          ev.preventDefault();ev.stopPropagation();
          showNickMenu(ev.clientX,ev.clientY,this.dataset.mb,this.dataset.nick);
        });
        content.appendChild(name);
      }

      var bwrap=document.createElement('div');
      bwrap.style.cssText='display:flex;align-items:flex-end;gap:5px;'+(isMe?'flex-direction:row-reverse;':'');

      var bubble=document.createElement('div');
      bubble.className='msg-bubble '+(isMe?'me-bubble':'other');
      bubble.innerHTML=escHtml(row.cm_content||'').replace(/\n/g,'<br>');

      var metaEl=document.createElement('div');
      metaEl.className='msg-meta';
      metaEl.style.flexDirection='column';
      metaEl.style.alignItems=isMe?'flex-end':'flex-start';
      if(isMe) metaEl.innerHTML='<span class="msg-read">읽음</span><span class="msg-time">'+fmtTime(row.cm_datetime)+'</span>';
      else metaEl.innerHTML='<span class="msg-time">'+fmtTime(row.cm_datetime)+'</span>';

      bwrap.appendChild(bubble);
      bwrap.appendChild(metaEl);
      content.appendChild(bwrap);
      r.appendChild(av);
      r.appendChild(content);
      el.msgs.appendChild(r);
    });
    el.msgs.scrollTop=el.msgs.scrollHeight;
  }

  function chatLoad(){
    fetch(AJAX+'?act=list&last_id='+encodeURIComponent(state.last_id)+'&region='+encodeURIComponent(state.region),{method:'GET',credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(j){
      if(!j||j.ok!==1){setStatus('연결 점검 중...');return;}
      setStatus('');
      if(typeof j.online_count!=='undefined'){
        var oc=parseInt(j.online_count,10);
        if(!isNaN(oc)){el.onlineNum.textContent=oc;el.rdCountNum.textContent=oc;}
      }
      var pf=state.freeze,nf=(j.freeze==1?1:0);
      if(pf!==nf){state.freeze=nf;addSystemMsg(nf?'⚠️ 운영자가 채팅을 동결했습니다.':'✅ 채팅 동결이 해제되었습니다.');}
      if(j.freeze==1){el.input.disabled=true;el.sendBtn.disabled=true;}
      else{el.input.disabled=false;el.sendBtn.disabled=false;}
      appendMessages(j.list||[]);
    })
    .catch(function(){setStatus('서버 연결이 불안정합니다.');});
  }

  function chatPing(){
    fetch(AJAX+'?act=ping&region='+encodeURIComponent(state.region),{method:'GET',credentials:'same-origin'}).catch(function(){});
  }

  function chatHello(forceReset,done){
    fetch(AJAX+'?act=hello&region='+encodeURIComponent(state.region),{method:'GET',credentials:'same-origin'})
    .then(function(r){return r.json();})
    .then(function(j){
      if(j&&j.ok===1){
        if(typeof j.freeze!=='undefined') state.freeze=(j.freeze==1?1:0);
        if(typeof j.online_count!=='undefined'){
          var oc=parseInt(j.online_count,10);
          if(!isNaN(oc)){el.onlineNum.textContent=oc;el.rdCountNum.textContent=oc;}
        }
        if(typeof j.last_id!=='undefined'){
          var lid=parseInt(j.last_id,10);
          if(!isNaN(lid)&&lid>=0&&forceReset) state.last_id=lid;
        }
      }
      if(typeof done==='function') done();
    })
    .catch(function(){if(typeof done==='function') done();});
  }

  function chatSend(){
    if(!el.input||el.input.disabled) return;
    if(state.sending) return;
    var now=Date.now();
    if(now-state.lastSendTs<1900){setStatus('연속 전송 제한(2초)입니다.');return;}
    var content=(el.input.value||'').trim();
    if(!content) return;
    markActive();
    state.sending=true;state.lastSendTs=now;
    fetch(AJAX,{
      method:'POST',credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body:'act=send&content='+encodeURIComponent(content)+'&region='+encodeURIComponent(state.region)
    })
    .then(function(r){return r.json();})
    .then(function(j){
      if(!j||j.ok!==1){if(j&&j.msg) setStatus(j.msg);else setStatus('전송 실패');return;}
      el.input.value='';el.input.style.height='auto';setStatus('');chatLoad();
    })
    .catch(function(){setStatus('서버 연결이 불안정합니다.');})
    .finally(function(){state.sending=false;});
  }

  function startLoop(){
    if(pollTimer) clearInterval(pollTimer);
    if(pingTimer) clearInterval(pingTimer);
    chatPing();chatLoad();
    pollTimer=setInterval(chatLoad,4000);
    pingTimer=setInterval(chatPing,30000);
  }
  function stopLoop(msg){
    if(pollTimer){clearInterval(pollTimer);pollTimer=null;}
    if(pingTimer){clearInterval(pingTimer);pingTimer=null;}
    if(msg) setStatus(msg);
  }
  function markActive(){
    lastActiveTs=Date.now();
    if(stoppedByIdle){stoppedByIdle=false;setStatus('');startLoop();}
  }
  ['click','keydown','scroll','touchstart','mousemove'].forEach(function(evt){
    document.addEventListener(evt,markActive,{passive:true});
  });
  idleTimer=setInterval(function(){
    if(stoppedByIdle) return;
    if(Date.now()-lastActiveTs>=10*60*1000){
      stoppedByIdle=true;
      stopLoop('10분간 활동이 없어 연결이 중단되었습니다. 클릭하면 재연결됩니다.');
    }
  },5000);

  el.regionBtn.addEventListener('click',function(){
    var dd=el.regionDD,btn=this;
    if(dd.classList.contains('open')){dd.classList.remove('open');btn.classList.remove('open');}
    else{dd.classList.add('open');btn.classList.add('open');}
  });
  el.regionDD.querySelectorAll('.region-chip').forEach(function(chip){
    chip.addEventListener('click',function(){
      el.regionDD.querySelectorAll('.region-chip').forEach(function(c){c.classList.remove('active');});
      this.classList.add('active');
      var newRegion=this.dataset.region;
      if(newRegion!==state.region){
        state.region=newRegion;state.last_id=0;
        el.regionLabel.textContent=newRegion;
        el.rdCountText.innerHTML='<strong>'+el.rdCountNum.textContent+'</strong>명이 <strong>'+escHtml(newRegion)+'</strong> 채팅 중';
        el.msgs.innerHTML='';
        addSystemMsg('📍 '+newRegion+' 채팅방으로 이동했습니다.');
        chatHello(true,function(){startLoop();});
      }
      setTimeout(function(){el.regionDD.classList.remove('open');el.regionBtn.classList.remove('open');},200);
    });
  });

  el.input.addEventListener('keydown',function(e){
    if(e.isComposing||e.repeat) return;
    if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();e.stopPropagation();chatSend();}
  });
  el.input.addEventListener('input',function(){
    this.style.height='auto';this.style.height=Math.min(this.scrollHeight,80)+'px';
  });
  el.sendBtn.addEventListener('click',chatSend);

  $('btnRefresh').addEventListener('click',function(){
    el.msgs.innerHTML='';addSystemMsg('🔄 새로고침 중...');
    state.last_id=0;
    chatHello(true,function(){
      el.msgs.innerHTML='';addSystemMsg('💗 이브알바 채팅방에 오신 것을 환영합니다!');chatLoad();
    });
  });

  $('btnRules').addEventListener('click',function(){$('rulesModal').classList.add('show');});
  $('rulesModal').addEventListener('click',function(e){if(e.target===this) this.classList.remove('show');});

  $('btnIgnore').addEventListener('click',function(){
    renderIgnoreList();$('ignoreModal').classList.add('show');
  });
  $('ignoreModal').addEventListener('click',function(e){if(e.target===this) this.classList.remove('show');});

  function renderIgnoreList(){
    var ignored=loadIgnored();
    el.ignoreCountNum.textContent=ignored.length;
    if(!ignored.length){
      el.ignoreList.innerHTML='<div class="ignore-empty"><div class="ignore-empty-icon">🙈</div><div class="ignore-empty-title">무시 목록이 비어 있습니다</div><div class="ignore-empty-sub">차단한 사용자가 없습니다.</div></div>';
      return;
    }
    el.ignoreList.innerHTML=ignored.map(function(id){
      return '<div class="ignore-item"><div class="ignore-avatar">👩</div><div class="ignore-info"><div class="ignore-nick">'+escHtml(id)+'</div></div><button class="btn-unignore" data-id="'+escHtml(id)+'">무시해제</button></div>';
    }).join('');
    el.ignoreList.querySelectorAll('.btn-unignore').forEach(function(btn){
      btn.addEventListener('click',function(){
        var uid=this.dataset.id;
        toggleIgnore(uid);
        addSystemMsg('✅ '+uid+' 님의 무시를 해제했습니다.');
        renderIgnoreList();
      });
    });
  }

  function showNickMenu(x,y,mbid,nick){
    menuTarget.mb_id=mbid;menuTarget.nick=nick;
    var m=el.nickMenu;m.style.display='block';
    m.style.left=Math.min(x,window.innerWidth-m.offsetWidth-8)+'px';
    m.style.top=Math.min(y,window.innerHeight-m.offsetHeight-8)+'px';
    $('menuIgnore').textContent=isIgnored(mbid)?'🙈 무시해제':'🙈 무시하기';
  }
  document.addEventListener('click',function(e){if(!el.nickMenu.contains(e.target)) el.nickMenu.style.display='none';},true);

  $('menuIgnore').addEventListener('click',function(){
    if(!menuTarget.mb_id) return;
    var ign=toggleIgnore(menuTarget.mb_id);
    addSystemMsg(ign?'🙈 '+menuTarget.nick+'님을 무시합니다.':'✅ '+menuTarget.nick+'님의 무시를 해제했습니다.');
    el.nickMenu.style.display='none';
  });
  $('menuReport').addEventListener('click',function(){
    if(!menuTarget.mb_id) return;
    el.nickMenu.style.display='none';
    reportReason='';
    $('reportTarget').textContent=menuTarget.nick;
    document.querySelectorAll('.report-reason').forEach(function(r){r.classList.remove('selected');});
    $('reportSubmitBtn').disabled=true;
    $('reportModal').classList.add('show');
  });
  document.querySelectorAll('.report-reason').forEach(function(r){
    r.addEventListener('click',function(){
      document.querySelectorAll('.report-reason').forEach(function(rr){rr.classList.remove('selected');});
      this.classList.add('selected');reportReason=this.dataset.reason;$('reportSubmitBtn').disabled=false;
    });
  });
  $('reportSubmitBtn').addEventListener('click',function(){
    if(!reportReason||!menuTarget.mb_id) return;
    this.disabled=true;this.textContent='접수 중...';
    fetch(AJAX,{
      method:'POST',credentials:'same-origin',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body:'act=report&target_id='+encodeURIComponent(menuTarget.mb_id)+'&target_nick='+encodeURIComponent(menuTarget.nick)+'&reason='+encodeURIComponent(reportReason)
    })
    .then(function(r){return r.json();})
    .then(function(j){addSystemMsg('🚨 '+menuTarget.nick+'님을 신고했습니다.');$('reportModal').classList.remove('show');})
    .catch(function(){alert('신고 요청 중 오류가 발생했습니다.');})
    .finally(function(){$('reportSubmitBtn').disabled=false;$('reportSubmitBtn').textContent='신고 접수';});
  });
  $('reportModal').addEventListener('click',function(e){if(e.target===this) this.classList.remove('show');});

  chatHello(true,function(){startLoop();});
})();
</script>
<?php } ?>
</body>
</html>
