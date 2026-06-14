/**
 * 이브수다방 탭 전환 — evealba_sudabang.html
 */
(function () {
  'use strict';

  function switchTab(tabId) {
    document.querySelectorAll('.comm-tab').forEach(function (t) {
      t.classList.toggle('active', t.getAttribute('data-tab') === tabId);
    });
    document.querySelectorAll('.suda-content').forEach(function (c) {
      c.classList.toggle('active', c.id === 'tab-' + tabId);
    });
    var activeTab = document.querySelector('.comm-tab[data-tab="' + tabId + '"]');
    if (activeTab) {
      activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
    try { sessionStorage.setItem('sudaTab', tabId); } catch (e) {}
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.querySelectorAll('.comm-tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      switchTab(this.getAttribute('data-tab'));
    });
  });

  document.querySelectorAll('[data-switch-tab]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      switchTab(btn.getAttribute('data-switch-tab'));
    });
  });

  document.querySelectorAll('.board-search-bar input').forEach(function (input) {
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        var form = input.closest('form');
        if (form) form.submit();
      }
    });
  });

  try {
    var saved = sessionStorage.getItem('sudaTab');
    if (saved) switchTab(saved);
  } catch (e) {}
})();
