/**
 * 이브알바 유저 상태창 (채팅용)
 * - 팔로우/전적 숨김, 활동내역→게시글 검색
 */
(function(){
  'use strict';
  var menu = null;
  var reportBox = null;
  var styleInjected = false;
  var _pluginBase = (typeof window.G5_PLUGIN_URL !== 'undefined' && window.G5_PLUGIN_URL) ? window.G5_PLUGIN_URL : ((typeof window.G5_URL !== 'undefined' && window.G5_URL) ? (window.G5_URL + '/plugin') : (window.location.origin + '/plugin'));
  var BBS_URL = (typeof window.G5_BBS_URL !== 'undefined' && window.G5_BBS_URL) ? window.G5_BBS_URL : ((typeof window.G5_URL !== 'undefined' && window.G5_URL) ? (window.G5_URL + '/bbs') : '/bbs');
  var API_URL = (_pluginBase || '').replace(/\/$/, '') + '/chat/ajax/chat_user_menu.php';

  function escapeHtml(s){
    if(typeof s !== 'string') return '';
    var div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
  }

  function injectStyle(){
    if(styleInjected) return;
    styleInjected = true;
    var st = document.createElement('style');
    st.textContent = `
      .sp-user-menu-common{ position:fixed;z-index:999999;min-width:210px;background:#111;color:#fff;border:1px solid rgba(255,255,255,.15);border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.35);padding:8px;font-size:13px;display:none; }
      .sp-user-menu-common .spm-head{ padding:6px 8px;border-bottom:1px solid rgba(255,255,255,.12);margin-bottom:6px;font-weight:700; }
      .sp-user-menu-common .spm-badge-value{ display:block;width:100%;padding:8px 10px;font-size:12px;line-height:1.3;min-height:32px;box-sizing:border-box;background:rgba(255,255,255,.06);border-radius:6px;margin-bottom:6px;text-align:center; }
      .sp-user-menu-common .spm-badge-value.spm-badge-empty{ color:rgba(255,255,255,.6); }
      .sp-user-menu-common .spm-stats{ padding:6px 8px;margin-bottom:6px;background:rgba(255,255,255,.06);border-radius:8px;line-height:1.35; }
      .sp-user-menu-common .spm-item{ display:flex;align-items:center;gap:8px;width:100%;padding:7px 8px;border-radius:8px;cursor:pointer;user-select:none; }
      .sp-user-menu-common .spm-item:hover{ background:rgba(255,255,255,.08); }
      .sp-user-menu-common .spm-item .spm-emo{ width:18px;text-align:center; }
      .sp-user-menu-common[data-hide-ignore] .spm-item[data-action="ignore"]{ display:none !important; }
      .sp-user-menu-common[data-hide-report] .spm-item[data-action="report"]{ display:none !important; }
      .sp-user-menu-common[data-hide-follow] .spm-item[data-action="follow"]{ display:none !important; }
      .sp-user-menu-common[data-hide-stats] .spm-stats{ display:none !important; }
      .sp-user-menu-common[data-hide-badge] .spm-badge-value{ display:none !important; }
      .sp-user-menu-report-box{ position:fixed;z-index:1000000;min-width:240px;background:#111;color:#fff;border:1px solid rgba(255,255,255,.15);border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.35);padding:8px;font-size:13px;display:none; }
      .sp-user-menu-report-box .spr-head{ padding:6px 8px;border-bottom:1px solid rgba(255,255,255,.12);margin-bottom:6px;font-weight:800; }
      .sp-user-menu-report-box .spr-reason{ display:block;padding:7px 8px;border-radius:8px;cursor:pointer;background:rgba(255,255,255,.06);margin:6px 0; }
      .sp-user-menu-report-box .spr-reason:hover{ background:rgba(255,255,255,.10); }
      .sp-user-menu-trigger{ cursor:pointer; }
    `;
    document.head.appendChild(st);
  }

  function ensureMenu(opts){
    injectStyle();
    if(menu) return menu;
    menu = document.createElement('div');
    menu.className = 'sp-user-menu-common';
    menu.setAttribute('data-hide-follow', '1');
    menu.setAttribute('data-hide-stats', '1');
    menu.setAttribute('data-hide-badge', '1');
    menu.innerHTML = '<div class="spm-head"></div><div class="spm-item" data-action="activity"><span class="spm-emo">📝</span><span>활동내역</span></div><div class="spm-item" data-action="ignore"><span class="spm-emo">🙈</span><span>무시하기</span></div><div class="spm-item" data-action="report"><span class="spm-emo">🚨</span><span>신고하기</span></div>';
    document.body.appendChild(menu);

    if(!reportBox){
      reportBox = document.createElement('div');
      reportBox.className = 'sp-user-menu-report-box';
      reportBox.innerHTML = '<div class="spr-head">🚨 신고 사유 선택</div><div class="spr-reason" data-reason="욕설 및 비하">· 욕설 및 비하</div><div class="spr-reason" data-reason="도배 및 홍보">· 도배 및 홍보</div><div class="spr-reason" data-reason="허위 사실 유포 및 선동">· 허위 사실 유포 및 선동</div><div class="spr-reason" data-reason="부적절한 닉네임 및 프로필">· 부적절한 닉네임 및 프로필</div><div class="spr-reason" data-reason="운영 방해 및 부적절한 대화">· 운영 방해 및 부적절한 대화</div>';
      document.body.appendChild(reportBox);
      reportBox.addEventListener('click', function(ev){
        var el = ev.target.closest('.spr-reason');
        if(!el) return;
        var mbid = menu.dataset.mb_id || '';
        var nick = menu.dataset.nick || '';
        var reason = el.getAttribute('data-reason') || '';
        if(!mbid || !nick || !reason) return;
        var onReport = menu._onReport;
        if(typeof onReport === 'function') onReport(mbid, nick, reason);
        reportBox.style.display = 'none';
        hide();
      });
    }

    document.addEventListener('click', function outsideClose(ev){
      if(!menu || menu.style.display !== 'block') return;
      var onTrigger = ev.target.closest('.sp-user-menu-trigger') || ev.target.closest('.livechat-nick');
      if(onTrigger) return;
      var inside = menu.contains(ev.target) || (reportBox && reportBox.contains(ev.target));
      if(!inside){ reportBox && (reportBox.style.display = 'none'); hide(); }
    }, true);

    menu.addEventListener('click', function(ev){
      var el = ev.target.closest('.spm-item');
      if(!el) return;
      var action = el.getAttribute('data-action');
      var mbid = menu.dataset.mb_id || '';
      var nick = menu.dataset.nick || '';
      var opts = menu._opts || {};
      if(action === 'activity' && nick){
        var activityUrl = (BBS_URL || '').replace(/\/$/, '') + '/search.php?sfl=wr_name&stx=' + encodeURIComponent(nick);
        window.open(activityUrl, '_blank', 'noopener');
        reportBox && (reportBox.style.display = 'none');
        hide();
        return;
      }
      if(action === 'ignore'){
        var onIgnore = opts.onIgnore;
        if(typeof onIgnore === 'function'){
          var r = onIgnore(mbid);
          var span = el.querySelector('span:last-child');
          if(span && r && typeof r.ignored === 'boolean') span.textContent = r.ignored ? '무시해제' : '무시하기';
        }
        return;
      }
      if(action === 'report'){
        if(reportBox && !opts.hideReport){
          if(reportBox.style.display === 'block'){ reportBox.style.display = 'none'; return; }
          menu._onReport = opts.onReport;
          reportBox.style.display = 'block';
          var rect = menu.getBoundingClientRect();
          var x = rect.right + 8, y = rect.top;
          reportBox.style.left = x + 'px';
          reportBox.style.top = y + 'px';
          var r2 = reportBox.getBoundingClientRect();
          if(r2.right > window.innerWidth) x = Math.max(8, rect.left - r2.width - 8);
          if(r2.bottom > window.innerHeight) y = Math.max(8, window.innerHeight - r2.height - 8);
          reportBox.style.left = x + 'px';
          reportBox.style.top = y + 'px';
        }
        return;
      }
    });
    return menu;
  }

  function hide(){
    if(menu) menu.style.display = 'none';
    if(reportBox) reportBox.style.display = 'none';
  }

  window.spUserMenuShow = function(x, y, mbid, nick, options){
    var opts = options || {};
    var hideReport = !!opts.hideReport;
    var hideIgnore = !!opts.hideIgnore;
    var m = ensureMenu(opts);
    m._opts = { hideReport: hideReport, hideIgnore: hideIgnore, onReport: opts.onReport, onIgnore: opts.onIgnore };
    if(hideReport) m.setAttribute('data-hide-report', '1'); else m.removeAttribute('data-hide-report');
    if(hideIgnore) m.setAttribute('data-hide-ignore', '1'); else m.removeAttribute('data-hide-ignore');
    if(reportBox) reportBox.style.display = 'none';

    m.dataset.mb_id = String(mbid || '');
    m.dataset.nick = String(nick || '');
    m.querySelector('.spm-head').textContent = nick;
    var ignoreBtn = m.querySelector('.spm-item[data-action="ignore"] span:last-child');
    if(ignoreBtn && !hideIgnore && typeof opts.getIgnoreLabel === 'function'){
      ignoreBtn.textContent = opts.getIgnoreLabel(mbid);
    } else if(ignoreBtn && !hideIgnore) {
      ignoreBtn.textContent = '무시하기';
    }

    var pad = 8;
    m.style.display = 'block';
    m.style.left = (x + pad) + 'px';
    m.style.top = (y + pad) + 'px';
    var rect = m.getBoundingClientRect();
    var nx = x + pad, ny = y + pad;
    if(rect.right > window.innerWidth) nx = Math.max(pad, window.innerWidth - rect.width - pad);
    if(rect.bottom > window.innerHeight) ny = Math.max(pad, window.innerHeight - rect.height - pad);
    m.style.left = nx + 'px';
    m.style.top = ny + 'px';
  };

  window.spUserMenuHide = hide;
})();
