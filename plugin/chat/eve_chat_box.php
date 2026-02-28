<?php
// /plugin/chat/eve_chat_box.php — 이브알바 실시간 채팅 프론트엔드
if (!defined('_GNUBOARD_')) exit;
@include_once(G5_PLUGIN_PATH.'/chat/_common.php');

$_chat_admin  = (isset($is_admin) && $is_admin) ? true : false;
$_chat_ajax   = G5_PLUGIN_URL.'/chat/chat_ajax.php';
$_chat_member = (isset($member) && is_array($member) && isset($member['mb_id']) && $member['mb_id']) ? true : false;
$_chat_can    = false;
$_chat_deny   = '';

if (!$_chat_member) {
    $_chat_deny = 'login';
} elseif ($_chat_admin) {
    $_chat_can = true;
} else {
    $type = (isset($member['mb_1']) && $member['mb_1']) ? $member['mb_1'] : '';
    $sex  = (isset($member['mb_sex']) && $member['mb_sex']) ? $member['mb_sex'] : '';
    if ($type === 'normal' && $sex === 'F') {
        $_chat_can = true;
    } else {
        $_chat_deny = 'denied';
    }
}

$_chat_my_id   = $_chat_member ? $member['mb_id'] : '';
$_chat_my_nick = $_chat_member ? (isset($member['mb_nick']) ? $member['mb_nick'] : '') : '';

$_chat_cfg = @sql_fetch(" SELECT * FROM {$g5['chat_config_table']} LIMIT 1 ");
$_chat_notice = '';
if ($_chat_cfg && isset($_chat_cfg['cf_notice_text']) && $_chat_cfg['cf_notice_text'] !== '') {
    $_chat_notice = $_chat_cfg['cf_notice_text'];
}
?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
<style>
/* ===================== CSS VARIABLE 보완 ===================== */
:root {
  --white: #ffffff;
  --border: #F0E0E8;
  --chat-bg: #FDF5F9;
  --shadow: 0 8px 32px rgba(255,27,107,.15);
  --shadow-sm: 0 2px 12px rgba(255,27,107,.1);
  --radius: 16px;
}

/* ===================== ELEMENT RESET (격리) ===================== */
#eveChatWrap,
#eveChatWrap *,
#eveChatWrap *::before,
#eveChatWrap *::after {
  margin: 0 !important;
  padding: 0 !important;
  box-sizing: border-box !important;
  text-decoration: none !important;
  list-style: none !important;
  float: none !important;
  text-transform: none !important;
  letter-spacing: normal !important;
  text-indent: 0 !important;
  text-shadow: none !important;
  min-width: 0 !important;
  min-height: 0 !important;
}
#eveChatWrap button,
#eveChatWrap textarea {
  font-family: 'Noto Sans KR', sans-serif !important;
  -webkit-appearance: none !important;
  appearance: none !important;
}

/* ===================== CHAT WINDOW ===================== */
#eveChatWrap .chat-window {
  width: 360px !important;
  max-width: calc(100vw - 24px) !important;
  max-height: calc(100vh - 110px) !important;
  background: #ffffff !important;
  border-radius: 16px !important;
  box-shadow: 0 8px 32px rgba(255,27,107,.15) !important;
  overflow: hidden !important;
  display: none !important;
  flex-direction: column !important;
  border: 1.5px solid #F0E0E8 !important;
  animation: slideUpIn .3s ease both !important;
  position: fixed !important;
  bottom: 90px !important;
  right: 28px !important;
  z-index: 1100 !important;
  font-family: 'Noto Sans KR', sans-serif !important;
  font-size: 14px !important;
  color: #1A0010 !important;
  line-height: 1.5 !important;
}
#eveChatWrap .chat-window.eve-open { display: flex !important; }
@keyframes slideUpIn {
  from { opacity:0; transform:translateY(20px) scale(.97); }
  to   { opacity:1; transform:translateY(0)    scale(1); }
}

/* -------- CHAT HEADER -------- */
#eveChatWrap .chat-header {
  background: linear-gradient(135deg, #2D0020, #FF1B6B) !important;
  padding: 14px 16px 12px !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-shrink: 0 !important;
  position: relative !important;
  overflow: hidden !important;
  border: none !important;
}
#eveChatWrap .chat-header::before {
  content: '' !important;
  position: absolute !important;
  inset: 0 !important;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.06), transparent) !important;
  animation: shimmer-h 3s linear infinite !important;
  margin: 0 !important; padding: 0 !important;
}
@keyframes shimmer-h {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}
#eveChatWrap .chat-header-icon {
  font-size: 22px !important;
  flex-shrink: 0 !important;
  position: relative !important;
  z-index: 1 !important;
  line-height: 1 !important;
}
#eveChatWrap .chat-header-info {
  flex: 1 !important;
  min-width: 0 !important;
  position: relative !important;
  z-index: 1 !important;
}
#eveChatWrap .chat-header-title {
  color: #ffffff !important;
  font-size: 15px !important;
  font-weight: 900 !important;
  line-height: 1.2 !important;
  display: flex !important;
  align-items: center !important;
  gap: 7px !important;
}
#eveChatWrap .chat-header-region {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  background: rgba(255,255,255,.25) !important;
  border-radius: 10px !important;
  padding: 2px 9px !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  color: #ffffff !important;
  backdrop-filter: blur(4px) !important;
  border: 1px solid rgba(255,255,255,.3) !important;
  cursor: pointer !important;
  transition: background .2s !important;
  white-space: nowrap !important;
  width: auto !important;
  height: auto !important;
  box-shadow: none !important;
}
#eveChatWrap .chat-header-region:hover { background: rgba(255,255,255,.35) !important; }
#eveChatWrap .chat-header-region .arrow {
  font-size: 9px !important;
  margin-left: 2px !important;
  transition: transform .25s !important;
  display: inline-block !important;
}
#eveChatWrap .chat-header-region.open .arrow { transform: rotate(180deg) !important; }
#eveChatWrap .chat-header-sub {
  color: rgba(255,255,255,.75) !important;
  font-size: 11px !important;
  margin-top: 3px !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  position: relative !important;
  z-index: 1 !important;
}
#eveChatWrap .online-dot {
  width: 7px !important;
  height: 7px !important;
  background: #4ADE80 !important;
  border-radius: 50% !important;
  animation: blink-dot 1.5s ease-in-out infinite !important;
  flex-shrink: 0 !important;
}
@keyframes blink-dot {
  0%,100% { opacity:1; }
  50% { opacity:.3; }
}
#eveChatWrap .chat-header-actions {
  display: flex !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
  position: relative !important;
  z-index: 1 !important;
}
#eveChatWrap .chat-icon-btn {
  width: 30px !important;
  height: 30px !important;
  border-radius: 50% !important;
  background: rgba(255,255,255,.18) !important;
  border: 1px solid rgba(255,255,255,.25) !important;
  color: #ffffff !important;
  font-size: 14px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: background .2s !important;
  backdrop-filter: blur(4px) !important;
  cursor: pointer !important;
  padding: 0 !important;
  box-shadow: none !important;
  line-height: 1 !important;
}
#eveChatWrap .chat-icon-btn:hover { background: rgba(255,255,255,.32) !important; }
#eveChatWrap .chat-icon-btn--text {
  width: auto !important;
  border-radius: 14px !important;
  padding: 0 10px !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  letter-spacing: .3px !important;
  white-space: nowrap !important;
}
#eveChatWrap .chat-close-btn {
  width: 28px !important; height: 28px !important;
  border-radius: 50% !important;
  background: rgba(255,255,255,.18) !important;
  border: 1px solid rgba(255,255,255,.25) !important;
  color: #ffffff !important;
  font-size: 15px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  flex-shrink: 0 !important;
  position: relative !important;
  z-index: 1 !important;
  padding: 0 !important;
  box-shadow: none !important;
  line-height: 1 !important;
}
#eveChatWrap .chat-close-btn:hover { background: rgba(255,255,255,.35) !important; }

/* -------- REGION DROPDOWN -------- */
#eveChatWrap .region-dropdown {
  background: #ffffff !important;
  border-bottom: 2px solid #F0E0E8 !important;
  overflow: hidden !important;
  max-height: 0 !important;
  transition: max-height .3s cubic-bezier(.4,0,.2,1), padding .3s !important;
  flex-shrink: 0 !important;
  border-top: none !important; border-left: none !important; border-right: none !important;
}
#eveChatWrap .region-dropdown.open { max-height: 200px !important; }
#eveChatWrap .region-dropdown-inner { padding: 12px 14px 14px !important; }
#eveChatWrap .rd-title {
  font-size: 11px !important;
  font-weight: 700 !important;
  color: #888 !important;
  margin-bottom: 9px !important;
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
}
#eveChatWrap .region-grid { display: flex !important; flex-wrap: wrap !important; gap: 6px !important; }
#eveChatWrap .region-chip {
  padding: 5px 12px !important;
  border-radius: 20px !important;
  border: 1.5px solid #F0E0E8 !important;
  background: #ffffff !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  color: #666 !important;
  cursor: pointer !important;
  transition: all .18s !important;
  white-space: nowrap !important;
  position: relative !important;
  overflow: hidden !important;
  width: auto !important; height: auto !important;
  box-shadow: none !important;
  line-height: 1.4 !important;
}
#eveChatWrap .region-chip::before {
  content: '' !important;
  position: absolute !important;
  inset: 0 !important;
  background: linear-gradient(135deg, #FF6B35, #FF1B6B) !important;
  opacity: 0 !important;
  transition: opacity .18s !important;
}
#eveChatWrap .region-chip span { position: relative !important; z-index: 1 !important; }
#eveChatWrap .region-chip:hover { border-color: #FF6BA8 !important; color: #FF1B6B !important; }
#eveChatWrap .region-chip.active {
  border-color: transparent !important;
  color: #ffffff !important;
  box-shadow: 0 2px 8px rgba(255,27,107,.3) !important;
}
#eveChatWrap .region-chip.active::before { opacity: 1 !important; }
#eveChatWrap .region-chip-all {
  background: linear-gradient(135deg, #2D0020, #FF1B6B) !important;
  color: #ffffff !important;
  border-color: transparent !important;
  box-shadow: 0 2px 8px rgba(255,27,107,.3) !important;
}
#eveChatWrap .region-chip-all::before { display: none !important; }
#eveChatWrap .region-chip-all.active { box-shadow: 0 3px 14px rgba(255,27,107,.5) !important; }
#eveChatWrap .rd-user-count {
  margin-top: 10px !important;
  padding: 7px 12px !important;
  background: linear-gradient(135deg, #fff5f8, #fff0f5) !important;
  border-radius: 10px !important;
  border: 1.5px solid #FFD6E7 !important;
  display: flex !important;
  align-items: center !important;
  gap: 7px !important;
  font-size: 12px !important;
  color: #666 !important;
}
#eveChatWrap .rd-user-count strong { color: #FF1B6B !important; font-weight: 900 !important; font-family: 'Outfit', sans-serif !important; font-size: 14px !important; }

/* -------- CHAT MESSAGES AREA -------- */
#eveChatWrap .chat-messages {
  flex: 1 !important;
  overflow-y: auto !important;
  padding: 14px 12px !important;
  background: #FDF5F9 !important;
  display: flex !important;
  flex-direction: column !important;
  gap: 4px !important;
  min-height: 200px !important;
  scroll-behavior: smooth !important;
}
#eveChatWrap .chat-messages::-webkit-scrollbar { width: 4px; }
#eveChatWrap .chat-messages::-webkit-scrollbar-track { background: transparent; }
#eveChatWrap .chat-messages::-webkit-scrollbar-thumb { background: #FFD6E7; border-radius: 2px; }

#eveChatWrap .chat-date-divider {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  margin: 8px 0 4px !important;
}
#eveChatWrap .chat-date-divider::before, #eveChatWrap .chat-date-divider::after {
  content: '' !important;
  flex: 1 !important;
  height: 1px !important;
  background: #F0E0E8 !important;
}
#eveChatWrap .chat-date-text {
  font-size: 10px !important;
  color: #888 !important;
  font-weight: 600 !important;
  padding: 2px 8px !important;
  background: #f5e8f0 !important;
  border-radius: 10px !important;
  white-space: nowrap !important;
}

#eveChatWrap .chat-system {
  text-align: center !important;
  font-size: 11px !important;
  color: #888 !important;
  padding: 4px 12px !important;
  background: rgba(255,255,255,.7) !important;
  border-radius: 10px !important;
  align-self: center !important;
  border: 1px solid #F0E0E8 !important;
  margin: 2px 0 !important;
}

#eveChatWrap .msg-row {
  display: flex !important;
  align-items: flex-end !important;
  gap: 6px !important;
  animation: msgIn .2s ease both !important;
}
@keyframes msgIn {
  from { opacity:0; transform:translateY(6px); }
  to   { opacity:1; transform:translateY(0); }
}
#eveChatWrap .msg-row.me { flex-direction: row-reverse !important; }
#eveChatWrap .msg-row.cont .msg-avatar { visibility: hidden !important; }

#eveChatWrap .msg-avatar {
  width: 34px !important;
  height: 34px !important;
  border-radius: 50% !important;
  background: linear-gradient(135deg, #FFD6E7, #FF6BA8) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 18px !important;
  flex-shrink: 0 !important;
  border: 2px solid #ffffff !important;
  box-shadow: 0 2px 8px rgba(255,27,107,.15) !important;
  align-self: flex-end !important;
}
#eveChatWrap .msg-avatar.admin-avatar { background: linear-gradient(135deg, #2D0020, #FF1B6B) !important; }
#eveChatWrap .msg-avatar.me-avatar { background: linear-gradient(135deg, #FF6B35, #FF1B6B) !important; }

#eveChatWrap .msg-content { max-width: 230px !important; display: flex !important; flex-direction: column !important; gap: 3px !important; }
#eveChatWrap .msg-row.me .msg-content { align-items: flex-end !important; }
#eveChatWrap .msg-name {
  font-size: 11px !important;
  font-weight: 700 !important;
  color: #666 !important;
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
  padding: 0 4px !important;
  cursor: pointer !important;
}
#eveChatWrap .msg-name .region-tag {
  background: #FFD6E7 !important;
  color: #FF1B6B !important;
  border-radius: 6px !important;
  padding: 1px 6px !important;
  font-size: 10px !important;
  font-weight: 700 !important;
}
#eveChatWrap .msg-name .admin-tag {
  background: linear-gradient(135deg, #2D0020, #FF1B6B) !important;
  color: #ffffff !important;
  border-radius: 6px !important;
  padding: 1px 6px !important;
  font-size: 10px !important;
  font-weight: 700 !important;
}
#eveChatWrap .msg-bubble {
  padding: 9px 13px !important;
  border-radius: 16px !important;
  font-size: 13px !important;
  line-height: 1.6 !important;
  word-break: break-word !important;
  position: relative !important;
  max-width: 100% !important;
  border: none !important;
}
#eveChatWrap .msg-bubble.other {
  background: #ffffff !important;
  color: #1A0010 !important;
  border-radius: 4px 16px 16px 16px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,.06) !important;
  border: 1.5px solid #F0E0E8 !important;
}
#eveChatWrap .msg-bubble.me-bubble {
  background: linear-gradient(135deg, #FF6B35, #FF1B6B) !important;
  color: #ffffff !important;
  border-radius: 16px 4px 16px 16px !important;
  box-shadow: 0 3px 12px rgba(255,27,107,.3) !important;
}
#eveChatWrap .msg-meta {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  padding: 0 4px !important;
}
#eveChatWrap .msg-time { font-size: 10px !important; color: #bbb !important; white-space: nowrap !important; }
#eveChatWrap .msg-read { font-size: 10px !important; color: #FF6B35 !important; font-weight: 700 !important; }

/* 공지 배너 */
#eveChatWrap .chat-notice-wrap { padding: 10px 12px 0 !important; background: #FDF5F9 !important; flex-shrink: 0 !important; }
#eveChatWrap .chat-notice {
  background: linear-gradient(135deg, #fff8f0, #fff0e8) !important;
  border: 1.5px solid #FFD0AA !important;
  border-radius: 10px !important;
  padding: 8px 12px !important;
  display: flex !important;
  align-items: flex-start !important;
  gap: 7px !important;
  margin-bottom: 4px !important;
}
#eveChatWrap .notice-icon { font-size: 14px !important; flex-shrink: 0 !important; margin-top: 1px !important; }
#eveChatWrap .notice-text { font-size: 11px !important; color: #AA5500 !important; line-height: 1.6 !important; }
#eveChatWrap .notice-text strong { font-weight: 700 !important; }

/* -------- CHAT INPUT -------- */
#eveChatWrap .chat-input-area {
  border-top: 2px solid #F0E0E8 !important;
  background: #ffffff !important;
  padding: 10px 12px !important;
  flex-shrink: 0 !important;
  border-bottom: none !important; border-left: none !important; border-right: none !important;
}
#eveChatWrap .chat-input-row {
  display: flex !important;
  align-items: flex-end !important;
  gap: 8px !important;
  background: #fdf5f9 !important;
  border: 1.5px solid #F0E0E8 !important;
  border-radius: 24px !important;
  padding: 8px 8px 8px 14px !important;
  transition: border-color .2s, box-shadow .2s !important;
}
#eveChatWrap .chat-input-row:focus-within {
  border-color: #FF1B6B !important;
  box-shadow: 0 0 0 3px rgba(255,27,107,.08) !important;
}
#eveChatWrap .chat-input {
  flex: 1 !important;
  border: none !important;
  background: transparent !important;
  font-size: 13px !important;
  color: #1A0010 !important;
  resize: none !important;
  outline: none !important;
  max-height: 80px !important;
  min-height: 22px !important;
  line-height: 1.5 !important;
  font-family: 'Noto Sans KR', sans-serif !important;
  padding: 0 !important;
  margin: 0 !important;
  box-shadow: none !important;
  width: auto !important;
  height: auto !important;
}
#eveChatWrap .chat-input::placeholder { color: #bbb !important; }
#eveChatWrap .chat-send-btn {
  width: 38px !important;
  height: 38px !important;
  border-radius: 50% !important;
  background: linear-gradient(135deg, #FF6B35, #FF1B6B) !important;
  border: none !important;
  color: #ffffff !important;
  font-size: 16px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  transition: transform .2s, box-shadow .2s !important;
  box-shadow: 0 3px 10px rgba(255,27,107,.3) !important;
  cursor: pointer !important;
  padding: 0 !important;
  line-height: 1 !important;
}
#eveChatWrap .chat-send-btn:hover { transform: scale(1.08) !important; box-shadow: 0 5px 16px rgba(255,27,107,.45) !important; }
#eveChatWrap .chat-send-btn:active { transform: scale(.95) !important; }
#eveChatWrap .chat-send-btn:disabled { opacity: .5 !important; cursor: not-allowed !important; transform: none !important; box-shadow: none !important; }
#eveChatWrap .chat-input-hint {
  margin-top: 6px !important;
  font-size: 10px !important;
  color: #ccc !important;
  text-align: center !important;
}

#eveChatWrap .chat-status {
  display: none !important;
  padding: 6px 10px !important;
  font-size: 11px !important;
  color: #e53935 !important;
  background: #fff5f5 !important;
  border-top: 1px solid #F0E0E8 !important;
  text-align: center !important;
}
#eveChatWrap .chat-status[style*="display: block"],
#eveChatWrap .chat-status[style*="display:block"] {
  display: block !important;
}

/* -------- LOGIN / DENY WALL -------- */
#eveChatWrap .chat-login-wall {
  flex: 1 !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 32px 20px !important;
  text-align: center !important;
  background: #FDF5F9 !important;
  gap: 14px !important;
}
#eveChatWrap .clw-icon { font-size: 48px !important; margin-bottom: 4px !important; }
#eveChatWrap .clw-title { font-size: 16px !important; font-weight: 900 !important; color: #1A0010 !important; }
#eveChatWrap .clw-sub { font-size: 13px !important; color: #888 !important; line-height: 1.7 !important; }
#eveChatWrap .btn-clw-login {
  padding: 11px 28px !important;
  background: linear-gradient(135deg, #FF6B35, #FF1B6B) !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 24px !important;
  font-size: 14px !important;
  font-weight: 900 !important;
  box-shadow: 0 4px 14px rgba(255,27,107,.3) !important;
  animation: pulse-glow 2s infinite !important;
  transition: transform .2s !important;
  cursor: pointer !important;
}
#eveChatWrap .btn-clw-login:hover { transform: scale(1.04) !important; }
@keyframes pulse-glow { 0%,100%{box-shadow:0 4px 14px rgba(255,27,107,.3)}50%{box-shadow:0 6px 22px rgba(255,27,107,.55)} }

/* -------- MODAL OVERLAY -------- */
#eveChatWrap .modal-overlay {
  position: fixed !important;
  inset: 0 !important;
  background: rgba(0,0,0,.45) !important;
  z-index: 2000 !important;
  display: none !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 16px !important;
  border: none !important;
}
#eveChatWrap .modal-overlay.show { display: flex !important; }
#eveChatWrap .modal-box {
  background: #ffffff !important;
  border-radius: 18px !important;
  width: 100% !important;
  max-width: 360px !important;
  overflow: hidden !important;
  box-shadow: 0 20px 60px rgba(0,0,0,.25) !important;
  animation: modalIn .25s ease both !important;
  max-height: 85vh !important;
  display: flex !important;
  flex-direction: column !important;
  border: none !important;
}
@keyframes modalIn {
  from { opacity:0; transform:scale(.94) translateY(12px); }
  to   { opacity:1; transform:scale(1)   translateY(0); }
}
#eveChatWrap .modal-head {
  background: linear-gradient(135deg, #2D0020, #FF1B6B) !important;
  padding: 16px 18px !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-shrink: 0 !important;
}
#eveChatWrap .modal-head-icon { font-size: 22px !important; }
#eveChatWrap .modal-head-title { color: #ffffff !important; font-size: 15px !important; font-weight: 900 !important; flex: 1 !important; }
#eveChatWrap .modal-close {
  width: 28px !important; height: 28px !important;
  border-radius: 50% !important;
  background: rgba(255,255,255,.2) !important;
  border: 1px solid rgba(255,255,255,.3) !important;
  color: #ffffff !important;
  font-size: 13px !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  cursor: pointer !important;
  transition: background .2s !important;
  flex-shrink: 0 !important;
  padding: 0 !important;
  box-shadow: none !important;
}
#eveChatWrap .modal-close:hover { background: rgba(255,255,255,.38) !important; }
#eveChatWrap .modal-body { padding: 18px !important; overflow-y: auto !important; flex: 1 !important; }
#eveChatWrap .modal-body::-webkit-scrollbar { width: 4px; }
#eveChatWrap .modal-body::-webkit-scrollbar-thumb { background: #FFD6E7; border-radius: 2px; }

#eveChatWrap .rules-list { display: flex !important; flex-direction: column !important; gap: 10px !important; }
#eveChatWrap .rule-item {
  display: flex !important; gap: 10px !important; align-items: flex-start !important;
  padding: 11px 13px !important; border-radius: 12px !important;
  background: #fff8fb !important; border: 1.5px solid #F0E0E8 !important;
  transition: border-color .2s !important;
}
#eveChatWrap .rule-item:hover { border-color: #FFD6E7 !important; }
#eveChatWrap .rule-num {
  width: 22px !important; height: 22px !important; border-radius: 50% !important;
  background: linear-gradient(135deg, #FF6B35, #FF1B6B) !important;
  color: #ffffff !important; font-size: 11px !important; font-weight: 900 !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  flex-shrink: 0 !important; font-family: 'Outfit', sans-serif !important;
}
#eveChatWrap .rule-text { font-size: 12px !important; color: #444 !important; line-height: 1.7 !important; }
#eveChatWrap .rule-text strong { color: #FF1B6B !important; font-weight: 700 !important; }
#eveChatWrap .rules-footer {
  margin-top: 14px !important; padding: 10px 13px !important;
  background: linear-gradient(135deg, #fff0e8, #fff8f0) !important;
  border: 1.5px solid #FFD0AA !important; border-radius: 10px !important;
  font-size: 11px !important; color: #AA5500 !important; line-height: 1.7 !important; text-align: center !important;
}

/* 무시목록 */
#eveChatWrap .ignore-empty { text-align: center !important; padding: 32px 20px !important; color: #888 !important; }
#eveChatWrap .ignore-empty-icon { font-size: 44px !important; margin-bottom: 10px !important; opacity: .5 !important; }
#eveChatWrap .ignore-empty-title { font-size: 14px !important; font-weight: 700 !important; color: #bbb !important; margin-bottom: 5px !important; }
#eveChatWrap .ignore-empty-sub { font-size: 12px !important; color: #ccc !important; }
#eveChatWrap .ignore-list { display: flex !important; flex-direction: column !important; gap: 8px !important; }
#eveChatWrap .ignore-item {
  display: flex !important; align-items: center !important; gap: 10px !important;
  padding: 10px 13px !important; border-radius: 12px !important;
  background: #fff8fb !important; border: 1.5px solid #F0E0E8 !important;
}
#eveChatWrap .ignore-avatar {
  width: 34px !important; height: 34px !important; border-radius: 50% !important;
  background: linear-gradient(135deg, #FFD6E7, #FF6BA8) !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  font-size: 18px !important; flex-shrink: 0 !important;
}
#eveChatWrap .ignore-info { flex: 1 !important; min-width: 0 !important; }
#eveChatWrap .ignore-nick { font-size: 13px !important; font-weight: 700 !important; color: #1A0010 !important; }
#eveChatWrap .ignore-nick span { font-size: 11px !important; font-weight: 400 !important; color: #888 !important; margin-left: 5px !important; }
#eveChatWrap .ignore-since { font-size: 11px !important; color: #bbb !important; margin-top: 1px !important; }
#eveChatWrap .btn-unignore {
  padding: 5px 12px !important; border-radius: 10px !important;
  border: 1.5px solid #ffcdd2 !important; background: #ffebee !important;
  color: #e53935 !important; font-size: 11px !important; font-weight: 700 !important;
  cursor: pointer !important; transition: all .18s !important; white-space: nowrap !important; flex-shrink: 0 !important;
}
#eveChatWrap .btn-unignore:hover { background: #e53935 !important; color: white !important; border-color: #e53935 !important; }
#eveChatWrap .ignore-header-bar {
  display: flex !important; align-items: center !important; justify-content: space-between !important;
  margin-bottom: 12px !important; padding-bottom: 10px !important; border-bottom: 1.5px solid #F0E0E8 !important;
}
#eveChatWrap .ignore-count { font-size: 12px !important; color: #888 !important; }
#eveChatWrap .ignore-count strong { color: #FF1B6B !important; font-weight: 900 !important; font-family: 'Outfit', sans-serif !important; font-size: 15px !important; }

/* 신고 */
#eveChatWrap .report-reasons { display: flex !important; flex-direction: column !important; gap: 6px !important; margin: 12px 0 !important; }
#eveChatWrap .report-reason {
  padding: 10px 14px !important; border-radius: 10px !important;
  border: 1.5px solid #F0E0E8 !important; background: #ffffff !important;
  cursor: pointer !important; font-size: 13px !important; transition: all .15s !important; color: #333 !important;
}
#eveChatWrap .report-reason:hover { border-color: #FF1B6B !important; background: #fff5f8 !important; }
#eveChatWrap .report-reason.selected { border-color: #FF1B6B !important; background: #fff0f5 !important; font-weight: 700 !important; color: #FF1B6B !important; }

/* 닉네임 클릭 메뉴 */
#eveChatWrap .nick-menu {
  position: fixed !important; z-index: 2100 !important;
  min-width: 140px !important; background: #ffffff !important;
  border: 1.5px solid #F0E0E8 !important; border-radius: 12px !important;
  box-shadow: 0 6px 24px rgba(0,0,0,.18) !important;
  padding: 6px !important; display: none !important; font-size: 12px !important;
}
#eveChatWrap .nick-menu-item {
  padding: 8px 12px !important; border-radius: 8px !important; cursor: pointer !important;
  display: flex !important; align-items: center !important; gap: 6px !important; transition: background .15s !important; color: #333 !important;
}
#eveChatWrap .nick-menu-item:hover { background: #fff0f5 !important; }
#eveChatWrap .nick-menu-item.danger { color: #e53935 !important; }
#eveChatWrap .nick-menu-item.danger:hover { background: #ffebee !important; }

/* -------- MOBILE -------- */
@media (max-width: 768px) {
  #eveChatWrap .chat-window { width: calc(100vw - 16px) !important; right: 8px !important; bottom: 80px !important; max-height: calc(100vh - 100px) !important; }
}
</style>

<!-- ============== CHAT WRAP (CSS 격리) ============== -->
<div id="eveChatWrap">

<!-- ============== CHAT WINDOW ============== -->
<div class="chat-window" id="eveChatWindow">

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
      <button class="chat-icon-btn" title="새로고침" id="btnRefresh">🔄</button>
      <button class="chat-icon-btn chat-icon-btn--text" title="채팅규정" id="btnRules">채팅규정</button>
      <button class="chat-icon-btn" title="무시목록" id="btnIgnore">🙈</button>
      <button class="chat-close-btn" title="닫기" id="btnClose">✕</button>
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

  <?php if ($_chat_deny === 'login') { ?>
  <div class="chat-login-wall">
    <div class="clw-icon">🔒</div>
    <div class="clw-title">로그인이 필요합니다</div>
    <div class="clw-sub">이브알바 실시간 채팅은<br>일반회원(여성)만 이용 가능합니다.</div>
    <button class="btn-clw-login" onclick="location.href='<?php echo G5_BBS_URL; ?>/login.php'">로그인</button>
  </div>
  <?php } elseif ($_chat_deny === 'denied') { ?>
  <div class="chat-login-wall">
    <div class="clw-icon">🚫</div>
    <div class="clw-title">이용이 제한됩니다</div>
    <div class="clw-sub">실시간 채팅은<br><strong>일반회원(여성)</strong>만 이용 가능합니다.</div>
  </div>
  <?php } else { ?>

  <!-- 공지 배너 -->
  <?php if ($_chat_notice) { ?>
  <div class="chat-notice-wrap" id="chatNoticeWrap">
    <div class="chat-notice">
      <span class="notice-icon">📢</span>
      <div class="notice-text"><strong>[공지]</strong> <?php echo nl2br(htmlspecialchars($_chat_notice)); ?></div>
    </div>
  </div>
  <?php } ?>

  <!-- 메시지 목록 -->
  <div class="chat-messages" id="chatMessages">
    <div class="chat-system">💗 이브알바 채팅방에 오신 것을 환영합니다!</div>
  </div>

  <!-- 상태 -->
  <div class="chat-status" id="chatStatus"></div>

  <!-- 입력창 -->
  <div class="chat-input-area">
    <div class="chat-input-row">
      <textarea class="chat-input" id="chatInput" placeholder="메시지를 입력하세요 (Enter 전송)" rows="1" autocomplete="off"></textarea>
      <button class="chat-send-btn" id="chatSendBtn">➤</button>
    </div>
    <div class="chat-input-hint">Enter 전송 &nbsp;·&nbsp; Shift+Enter 줄바꿈</div>
  </div>

  <?php } ?>
</div>

<!-- ============== 채팅규정 모달 ============== -->
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

<!-- ============== 무시목록 모달 ============== -->
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

<!-- ============== 신고 모달 ============== -->
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

</div><!-- /eveChatWrap -->

<?php if ($_chat_can) { ?>
<script>
(function(){
  var AJAX = "<?php echo $_chat_ajax; ?>";
  var MY_ID = "<?php echo addslashes($_chat_my_id); ?>";
  var MY_NICK = "<?php echo addslashes($_chat_my_nick); ?>";
  var IS_ADMIN = <?php echo $_chat_admin ? 1 : 0; ?>;

  var state = { last_id:0, region:'전체', freeze:0, sending:false, lastSendTs:0 };
  var pollTimer = null, pingTimer = null, idleTimer = null;
  var lastActiveTs = Date.now(), stoppedByIdle = false;

  var $ = function(id){ return document.getElementById(id); };
  var el = {
    win: $('eveChatWindow'), msgs: $('chatMessages'), input: $('chatInput'),
    sendBtn: $('chatSendBtn'), onlineNum: $('onlineNum'), status: $('chatStatus'),
    regionBtn: $('regionToggle'), regionDD: $('regionDropdown'),
    regionLabel: $('currentRegionLabel'), rdCountNum: $('regionUserNum'),
    rdCountText: $('regionUserText'), nickMenu: $('nickMenu'),
    ignoreList: $('ignoreList'), ignoreCountNum: $('ignoreCountNum')
  };

  var IGNORE_KEY = 'eve_chat_ignored_v1';
  var menuTarget = {mb_id:'',nick:''};
  var reportReason = '';

  function loadIgnored(){ try{var a=JSON.parse(localStorage.getItem(IGNORE_KEY)||'[]');return Array.isArray(a)?a:[];}catch(e){return[];} }
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
    if(!t){el.status.style.cssText='display:none !important';el.status.textContent='';return;}
    el.status.textContent=t;el.status.style.cssText='display:block !important;padding:6px 10px !important;font-size:11px !important;color:#e53935 !important;background:#fff5f5 !important;border-top:1px solid #F0E0E8 !important;text-align:center !important';
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
          if(!this.dataset.mb) return;
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

  // Region
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

  // Input
  el.input.addEventListener('keydown',function(e){
    if(e.isComposing||e.repeat) return;
    if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();e.stopPropagation();chatSend();}
  });
  el.input.addEventListener('input',function(){
    this.style.height='auto';this.style.height=Math.min(this.scrollHeight,80)+'px';
  });
  el.sendBtn.addEventListener('click',chatSend);

  // Close
  $('btnClose').addEventListener('click',function(){el.win.classList.remove('eve-open');});

  // Refresh
  $('btnRefresh').addEventListener('click',function(){
    el.msgs.innerHTML='';addSystemMsg('🔄 새로고침 중...');
    state.last_id=0;
    chatHello(true,function(){
      el.msgs.innerHTML='';addSystemMsg('💗 이브알바 채팅방에 오신 것을 환영합니다!');chatLoad();
    });
  });

  // Rules
  $('btnRules').addEventListener('click',function(){$('rulesModal').classList.add('show');});
  $('rulesModal').addEventListener('click',function(e){if(e.target===this) this.classList.remove('show');});

  // Ignore list
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

  // Nick menu
  function showNickMenu(x,y,mbid,nick){
    menuTarget.mb_id=mbid;menuTarget.nick=nick;
    var m=el.nickMenu;m.style.cssText='display:block !important;position:fixed !important;z-index:2100 !important';
    var w=m.offsetWidth,h=m.offsetHeight;
    m.style.left=Math.min(x,window.innerWidth-w-8)+'px';
    m.style.top=Math.min(y,window.innerHeight-h-8)+'px';
    $('menuIgnore').textContent=isIgnored(mbid)?'🙈 무시해제':'🙈 무시하기';
  }
  document.addEventListener('click',function(e){if(!el.nickMenu.contains(e.target)) el.nickMenu.style.cssText='display:none !important';},true);

  $('menuIgnore').addEventListener('click',function(){
    if(!menuTarget.mb_id) return;
    var ign=toggleIgnore(menuTarget.mb_id);
    addSystemMsg(ign?'🙈 '+menuTarget.nick+'님을 무시합니다.':'✅ '+menuTarget.nick+'님의 무시를 해제했습니다.');
    el.nickMenu.style.cssText='display:none !important';
  });
  $('menuReport').addEventListener('click',function(){
    if(!menuTarget.mb_id) return;
    el.nickMenu.style.cssText='display:none !important';
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

  // Global toggle
  window.toggleEveChat = function(){
    if(el.win.classList.contains('eve-open')){
      el.win.classList.remove('eve-open');
    } else {
      el.win.classList.add('eve-open');
      setTimeout(function(){el.msgs.scrollTop=el.msgs.scrollHeight;},50);
    }
  };

  chatHello(true,function(){startLoop();});
})();
</script>
<?php } else { ?>
<script>
window.toggleEveChat = function(){
  var w=document.getElementById('eveChatWindow');
  if(w.classList.contains('eve-open')) w.classList.remove('eve-open');
  else w.classList.add('eve-open');
};
</script>
<?php } ?>
