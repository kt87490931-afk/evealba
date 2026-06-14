(function () {
  'use strict';

  var CFG = window.EVE_CHAT_HUB || {};
  if (!CFG.dmAjax) return;

  var state = {
    tab: CFG.tab || 'noti',
    dmId: CFG.dmId || 0,
    dmLastId: 0,
    dmPoll: null,
    rooms: [],
    canSend: true,
    region: '서울',
    regionLastId: 0,
    regionPoll: null,
    regionActive: false,
    notiFilter: 'all'
  };

  var MOBILE = function () { return window.innerWidth <= 680; };

  function $(id) { return document.getElementById(id); }

  function esc(s) {
    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function fetchJson(url, opts) {
    opts = opts || {};
    return fetch(url, opts).then(function (r) { return r.json(); });
  }

  function formatTime(dt) {
    if (!dt) return '';
    var d = new Date(dt.replace(/-/g, '/'));
    if (isNaN(d.getTime())) return dt;
    var now = new Date();
    var diff = (now - d) / 1000;
    if (diff < 60) return '방금';
    if (diff < 3600) return Math.floor(diff / 60) + '분 전';
    if (diff < 86400) return Math.floor(diff / 3600) + '시간 전';
    return (d.getMonth() + 1) + '/' + d.getDate();
  }

  function formatMsgTime(dt) {
    if (!dt) return '';
    var d = new Date(dt.replace(/-/g, '/'));
    if (isNaN(d.getTime())) return dt;
    var h = d.getHours();
    var ap = h >= 12 ? '오후' : '오전';
    h = h % 12 || 12;
    return ap + ' ' + h + ':' + String(d.getMinutes()).padStart(2, '0');
  }

  function autoResize(el) {
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
  }

  function switchTab(name) {
    state.tab = name;
    document.querySelectorAll('.chat-hub-main .main-tab').forEach(function (t) {
      t.classList.toggle('active', t.getAttribute('data-tab') === name);
    });
    document.querySelectorAll('.chat-hub-main .tab-panel').forEach(function (p) {
      p.classList.toggle('active', p.id === 'panel-' + name);
    });
    if (name === 'chat' && CFG.canDm) {
      loadRooms();
    }
    if (name === 'region' && CFG.canRegion) {
      startRegionIfNeeded();
    }
    if (name === 'noti') {
      loadNotifications();
    }
  }

  /* ── 알림 ── */
  function loadNotifications() {
    fetchJson(CFG.dmAjax + '?act=notifications').then(function (d) {
      var box = $('notiList');
      if (!box) return;
      if (!d.ok || !d.list || !d.list.length) {
        box.innerHTML = '<div class="noti-empty"><span class="e-icon">🔔</span><p>새 알림이 없습니다</p></div>';
        return;
      }
      var html = '';
      d.list.forEach(function (n) {
        if (state.notiFilter !== 'all' && n.type !== state.notiFilter) return;
        html += '<div class="noti-item' + (n.unread ? ' unread' : '') + '" data-type="' + esc(n.type) + '" data-dm="' + n.dm_id + '">' +
          '<div class="noti-icon-wrap type-chat">💬</div>' +
          '<div class="noti-body">' +
          '<div class="noti-title"><strong>' + esc(n.title) + '</strong>' + (n.job_label ? ' · ' + esc(n.job_label) : '') + '</div>' +
          '<div class="noti-desc">' + esc(n.desc) + '</div>' +
          '<div class="noti-time">' + esc(formatTime(n.time)) + '</div>' +
          '</div></div>';
      });
      box.innerHTML = html || '<div class="noti-empty"><span class="e-icon">🔔</span><p>새 알림이 없습니다</p></div>';
      box.querySelectorAll('.noti-item[data-dm]').forEach(function (el) {
        el.addEventListener('click', function () {
          openRoom(parseInt(el.getAttribute('data-dm'), 10));
          switchTab('chat');
        });
      });
    });
  }

  /* ── 1:1 DM ── */
  function loadRooms() {
    fetchJson(CFG.dmAjax + '?act=rooms').then(function (d) {
      if (!d.ok) return;
      state.rooms = d.rooms || [];
      renderRoomList();
      var openId = CFG.dmId || 0;
      if (openId) {
        openRoom(openId, true);
        CFG.dmId = 0;
      }
    });
  }

  function renderRoomList(filter) {
    var box = $('chatRoomList');
    if (!box) return;
    filter = (filter || '').toLowerCase();
    var list = state.rooms;
    if (filter) {
      list = list.filter(function (r) {
        return (r.other_nick + ' ' + r.job_label + ' ' + r.last_preview).toLowerCase().indexOf(filter) >= 0;
      });
    }
    if (!list.length) {
      box.innerHTML = '<div class="chat-list-empty"><span class="e-icon">💬</span><p>아직 대화가 없습니다.<br>채용공고 상세에서 채팅을 시작해 보세요.</p></div>';
      return;
    }
    var html = '';
    list.forEach(function (r) {
      var emoji = r.other_is_biz ? '🏢' : '👤';
      html += '<button type="button" class="chat-room-item' + (state.dmId === r.dm_id ? ' active' : '') + '" data-dm="' + r.dm_id + '">' +
        '<div class="chat-avatar">' + emoji + '</div>' +
        '<div class="chat-room-info" style="flex:1;min-width:0">' +
        '<div class="chat-room-name">' + esc(r.other_nick) +
        (r.other_is_biz ? ' <span class="chat-room-tag biz">업소</span>' : '') + '</div>' +
        '<div class="chat-room-last">' + esc(r.last_preview || '대화를 시작해 보세요') + '</div></div>' +
        '<div class="chat-room-meta">' +
        '<div class="chat-room-time">' + esc(formatTime(r.last_at)) + '</div>' +
        (r.unread ? '<div class="chat-unread">' + r.unread + '</div>' : '') +
        '</div></button>';
    });
    box.innerHTML = html;
    box.querySelectorAll('.chat-room-item').forEach(function (el) {
      el.addEventListener('click', function () {
        openRoom(parseInt(el.getAttribute('data-dm'), 10));
      });
    });
  }

  function openRoom(dmId, skipMobile) {
    state.dmId = dmId;
    state.dmLastId = 0;
    var room = state.rooms.find(function (r) { return r.dm_id === dmId; });
    if (!room) {
      fetchJson(CFG.dmAjax + '?act=messages&dm_id=' + dmId + '&last_id=0').then(function (d) {
        if (d.ok && d.room) {
          state.rooms.unshift(d.room);
          showRoomPanel(d.room);
          pollDmMessages(true);
        }
      });
      return;
    }
    showRoomPanel(room);
    pollDmMessages(true);
    document.querySelectorAll('.chat-room-item').forEach(function (el) {
      el.classList.toggle('active', parseInt(el.getAttribute('data-dm'), 10) === dmId);
    });
    if (MOBILE() && !skipMobile) {
      $('chatListPane').classList.add('hidden');
      $('chatRoomPane').classList.add('mobile-show');
    }
  }

  function showRoomPanel(room) {
    $('chatEmpty').style.display = 'none';
    var active = $('chatRoomActive');
    active.hidden = false;
    $('chatHeaderEmoji').textContent = room.other_is_biz ? '🏢' : '👤';
    $('chatHeaderName').textContent = room.other_nick;
    $('chatHeaderSub').textContent = room.job_label || room.job_title || '';
    $('chatJobLink').href = CFG.jobsViewUrl + '?jr_id=' + room.jr_id;
    $('chatMessages').innerHTML = '';
    state.canSend = room.can_reply === 1;
    updateDmInputLock();
    fetchJson(CFG.dmAjax + '?act=read&dm_id=' + room.dm_id, { method: 'POST' });
  }

  function updateDmInputLock() {
    var lock = $('chatInputLock');
    var area = $('chatInputArea');
    var qr = $('chatQuickReplies');
    if (!lock || !area) return;
    if (CFG.isBiz && !state.canSend) {
      lock.hidden = false;
      area.style.display = 'none';
      if (qr) qr.style.display = 'none';
    } else {
      lock.hidden = true;
      area.style.display = '';
      if (qr) qr.style.display = CFG.isFemale ? '' : 'none';
    }
  }

  function appendDmMsg(m, room) {
    var box = $('chatMessages');
    if (!box) return;
    var row = document.createElement('div');
    row.className = 'msg-row' + (m.mine ? ' mine' : '');
    var read = m.mine && m.read_at && m.read_at !== '0000-00-00 00:00:00' ? '<span class="msg-read">읽음</span> ' : '';
    if (m.mine) {
      row.innerHTML = '<div class="msg-bubble-wrap"><div class="msg-bubble mine">' + esc(m.content).replace(/\n/g, '<br>') +
        '</div><div class="msg-meta">' + read + esc(formatMsgTime(m.datetime)) + '</div></div>';
    } else {
      row.innerHTML = '<div class="msg-bubble-wrap"><div class="msg-name">' + esc(room.other_nick) + '</div>' +
        '<div class="msg-bubble other">' + esc(m.content).replace(/\n/g, '<br>') +
        '</div><div class="msg-meta">' + esc(formatMsgTime(m.datetime)) + '</div></div>';
    }
    box.appendChild(row);
    box.scrollTop = box.scrollHeight;
    if (m.msg_id > state.dmLastId) state.dmLastId = m.msg_id;
  }

  function pollDmMessages(full) {
    if (!state.dmId) return;
    var url = CFG.dmAjax + '?act=messages&dm_id=' + state.dmId + '&last_id=' + (full ? 0 : state.dmLastId);
    fetchJson(url).then(function (d) {
      if (!d.ok) return;
      if (d.can_send !== undefined) {
        state.canSend = d.can_send === 1;
        updateDmInputLock();
      }
      if (full) {
        $('chatMessages').innerHTML = '';
        state.dmLastId = 0;
      }
      (d.list || []).forEach(function (m) {
        appendDmMsg(m, d.room);
      });
    });
  }

  function sendDm() {
    if (!state.dmId || !state.canSend) return;
    var ta = $('chatInputText');
    var text = ta.value.trim();
    if (!text) return;
    var fd = new FormData();
    fd.append('dm_id', state.dmId);
    fd.append('content', text);
    fetchJson(CFG.dmAjax + '?act=send', { method: 'POST', body: fd }).then(function (d) {
      if (!d.ok) {
        alert(d.msg || '전송 실패');
        return;
      }
      ta.value = '';
      autoResize(ta);
      pollDmMessages(false);
      loadRooms();
    });
  }

  function closeChatRoom() {
    $('chatListPane').classList.remove('hidden');
    $('chatRoomPane').classList.remove('mobile-show');
    $('chatRoomActive').hidden = true;
    $('chatEmpty').style.display = 'flex';
    state.dmId = 0;
    stopDmPoll();
  }

  function stopDmPoll() {
    if (state.dmPoll) {
      clearInterval(state.dmPoll);
      state.dmPoll = null;
    }
  }

  function startDmPoll() {
    stopDmPoll();
    state.dmPoll = setInterval(function () {
      if (state.tab === 'chat' && state.dmId) pollDmMessages(false);
    }, 3000);
  }

  /* ── 지역별 채팅 ── */
  function startRegionIfNeeded() {
    if (state.regionActive) return;
    var first = document.querySelector('.region-chat-item.active');
    if (first && !MOBILE()) {
      openRegion(first.getAttribute('data-region'), first.getAttribute('data-emoji'));
    }
  }

  function openRegion(name, emoji) {
    state.region = name;
    state.regionLastId = 0;
    state.regionActive = true;
    document.querySelectorAll('.region-chat-item').forEach(function (el) {
      el.classList.toggle('active', el.getAttribute('data-region') === name);
    });
    $('regionEmpty').style.display = 'none';
    $('regionRoomActive').hidden = false;
    $('regionHeaderEmoji').textContent = emoji || '🗺️';
    $('regionHeaderName').textContent = name + ' 채팅방';
    $('regionMessages').innerHTML = '<div class="region-system-msg">💬 ' + esc(name) + ' 채팅방에 입장하셨습니다</div>';
    if (MOBILE()) {
      $('regionSelectPane').classList.add('hidden');
      $('regionRoomPane').classList.add('mobile-show');
    }
    regionHello();
    startRegionPoll();
  }

  function regionHello() {
    fetchJson(CFG.regionAjax + '?act=hello&region=' + encodeURIComponent(state.region)).then(function (d) {
      if (!d.ok) return;
      if ($('regionHeaderSub')) {
        $('regionHeaderSub').textContent = (d.online_count || 0) + '명 참여중';
      }
      document.querySelectorAll('.region-chat-item').forEach(function (el) {
        if (el.getAttribute('data-region') === state.region) {
          var num = el.querySelector('.region-online-num');
          if (num) num.textContent = d.online_count || 0;
        }
      });
      if (d.notice_text && $('regionNotice')) {
        $('regionNotice').querySelector('div').textContent = d.notice_text;
      }
      state.regionLastId = d.start_id || 0;
      (d.recent || []).forEach(function (m) {
        appendRegionMsg(m);
      });
    });
  }

  function appendRegionMsg(m) {
    var box = $('regionMessages');
    if (!box) return;
    var mine = m.mb_id && CFG.myMbId && m.mb_id === CFG.myMbId;
    var row = document.createElement('div');
    row.className = 'msg-row' + (mine ? ' mine' : '');
    var icon = m.cm_icon || '🌸';
    if (mine) {
      row.innerHTML = '<div class="msg-bubble-wrap"><div class="msg-bubble mine">' + esc(m.cm_content).replace(/\n/g, '<br>') +
        '</div><div class="msg-meta">' + esc(formatMsgTime(m.cm_datetime)) + '</div></div>';
    } else {
      row.innerHTML = '<div class="msg-avatar" style="width:32px;height:32px;border-radius:50%;background:var(--pink-pale);display:flex;align-items:center;justify-content:center;font-size:15px">' + esc(icon) + '</div>' +
        '<div class="msg-bubble-wrap"><div class="msg-name">' + esc(m.cm_nick) + '</div>' +
        '<div class="msg-bubble other">' + esc(m.cm_content).replace(/\n/g, '<br>') +
        '</div><div class="msg-meta">' + esc(formatMsgTime(m.cm_datetime)) + '</div></div>';
    }
    box.appendChild(row);
    box.scrollTop = box.scrollHeight;
    if (m.cm_id > state.regionLastId) state.regionLastId = m.cm_id;
  }

  function pollRegion() {
    fetchJson(CFG.regionAjax + '?act=list&region=' + encodeURIComponent(state.region) + '&last_id=' + state.regionLastId).then(function (d) {
      if (!d.ok) return;
      (d.list || []).forEach(appendRegionMsg);
    });
  }

  function sendRegion() {
    var ta = $('regionInputText');
    var text = ta.value.trim();
    if (!text) return;
    var fd = new FormData();
    fd.append('content', text);
    fd.append('region', state.region);
    fetchJson(CFG.regionAjax + '?act=send', { method: 'POST', body: fd }).then(function (d) {
      if (!d.ok) {
        alert(d.msg || '전송 실패');
        return;
      }
      ta.value = '';
      autoResize(ta);
      pollRegion();
    });
  }

  function startRegionPoll() {
    stopRegionPoll();
    state.regionPoll = setInterval(function () {
      if (state.tab === 'region' && state.regionActive) pollRegion();
    }, 3000);
  }

  function stopRegionPoll() {
    if (state.regionPoll) {
      clearInterval(state.regionPoll);
      state.regionPoll = null;
    }
  }

  function closeRegionRoom() {
    $('regionSelectPane').classList.remove('hidden');
    $('regionRoomPane').classList.remove('mobile-show');
    $('regionRoomActive').hidden = true;
    $('regionEmpty').style.display = 'flex';
    state.regionActive = false;
    stopRegionPoll();
  }

  /* ── open from job detail ── */
  function openFromJob(jrId) {
    fetchJson(CFG.dmAjax + '?act=open&jr_id=' + jrId).then(function (d) {
      if (!d.ok) {
        alert(d.msg || '채팅을 시작할 수 없습니다.');
        return;
      }
      CFG.dmId = d.room.dm_id;
      switchTab('chat');
      openRoom(d.room.dm_id, MOBILE());
      loadRooms();
    });
  }

  /* ── init ── */
  document.querySelectorAll('.chat-hub-main .main-tab').forEach(function (btn) {
    btn.addEventListener('click', function () {
      switchTab(btn.getAttribute('data-tab'));
    });
  });

  document.querySelectorAll('.noti-filter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.noti-filter-btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      state.notiFilter = btn.getAttribute('data-noti-filter');
      loadNotifications();
    });
  });

  var searchInput = $('chatSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      renderRoomList(searchInput.value.trim());
    });
  }

  var sendBtn = $('chatSendBtn');
  if (sendBtn) sendBtn.addEventListener('click', sendDm);
  var chatTa = $('chatInputText');
  if (chatTa) {
    chatTa.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendDm(); }
    });
    chatTa.addEventListener('input', function () { autoResize(chatTa); });
  }

  document.querySelectorAll('#chatQuickReplies .extra-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (chatTa) { chatTa.value = btn.getAttribute('data-reply'); chatTa.focus(); }
    });
  });

  var backBtn = $('chatBackBtn');
  if (backBtn) backBtn.addEventListener('click', closeChatRoom);

  document.querySelectorAll('.region-chat-item').forEach(function (el) {
    el.addEventListener('click', function () {
      openRegion(el.getAttribute('data-region'), el.getAttribute('data-emoji'));
    });
  });

  var regionBack = $('regionBackBtn');
  if (regionBack) regionBack.addEventListener('click', closeRegionRoom);

  var regionSend = $('regionSendBtn');
  if (regionSend) regionSend.addEventListener('click', sendRegion);
  var regionTa = $('regionInputText');
  if (regionTa) {
    regionTa.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendRegion(); }
    });
    regionTa.addEventListener('input', function () { autoResize(regionTa); });
  }

  var rulesBtn = $('regionRulesBtn');
  if (rulesBtn) {
    rulesBtn.addEventListener('click', function () {
      alert('채팅방 이용 규칙: 욕설·광고·개인정보 공유 금지. 위반 시 강퇴될 수 있습니다.');
    });
  }

  startDmPoll();

  if (CFG.openJr && CFG.isFemale) {
    openFromJob(CFG.openJr);
  } else {
    switchTab(state.tab);
    if (state.tab === 'noti') loadNotifications();
    if (state.tab === 'chat' && CFG.canDm) loadRooms();
    if (state.tab === 'region' && CFG.canRegion) startRegionIfNeeded();
  }

  window.eveOpenChatFromJob = openFromJob;
})();
