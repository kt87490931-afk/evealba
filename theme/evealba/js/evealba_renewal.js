/**
 * 이브알바 UI 리뉴얼 JS — 리스트/피드/그리드 + 점프순 정렬
 */
(function () {
  'use strict';

  function initRenewalBodyClass() {
    if (document.querySelector('.panel-right')) {
      document.body.classList.add('eve-panel-right-on');
    }
  }

  function initCardLinks() {
    document.querySelectorAll('.recruit-card[data-href]').forEach(function (card) {
      card.addEventListener('click', function (e) {
        if (e.target.closest('button')) return;
        var href = card.getAttribute('data-href');
        if (href) window.location.href = href;
      });
    });

    document.querySelectorAll('.story-item[data-href]').forEach(function (item) {
      item.addEventListener('click', function () {
        var href = item.getAttribute('data-href');
        if (href) window.location.href = href;
      });
    });

    document.querySelectorAll('.recommend-item[data-href]').forEach(function (item) {
      item.addEventListener('click', function () {
        var href = item.getAttribute('data-href');
        if (href) window.location.href = href;
      });
    });

    document.querySelectorAll('.tab-btn[data-href]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var href = btn.getAttribute('data-href');
        if (href) window.location.href = href;
      });
    });

    document.querySelectorAll('.similar-item[data-href], .panel-hot-item[data-href]').forEach(function (item) {
      item.addEventListener('click', function (e) {
        if (e.target.closest('button')) return;
        var href = item.getAttribute('data-href');
        if (href) window.location.href = href;
      });
    });
  }

  function cardJumpTs(card) {
    return parseInt(card.getAttribute('data-jump-ts') || '0', 10) || 0;
  }

  function reorderCards(container, mode) {
    var cards = Array.from(container.querySelectorAll('.recruit-card'));
    if (!cards.length) return;

    if (mode === 'feed') {
      cards.sort(function (a, b) {
        return cardJumpTs(b) - cardJumpTs(a);
      });
    } else {
      cards.sort(function (a, b) {
        var ga = a.getAttribute('data-ad-grade') === 'premium' ? 0 : 1;
        var gb = b.getAttribute('data-ad-grade') === 'premium' ? 0 : 1;
        if (ga !== gb) return ga - gb;
        return cardJumpTs(b) - cardJumpTs(a);
      });
    }

    cards.forEach(function (c) {
      container.appendChild(c);
    });
  }

  function initViewTabs() {
    var viewTabs = document.querySelectorAll('.view-tab');
    var feedContainer = document.getElementById('feedContainer');
    if (!viewTabs.length || !feedContainer) return;

    function applyView(view) {
      viewTabs.forEach(function (t) {
        t.classList.toggle('active', t.getAttribute('data-view') === view);
      });

      feedContainer.className = 'feed-container view-' + view;

      if (view === 'grid') {
        feedContainer.style.display = 'grid';
        feedContainer.style.gridTemplateColumns = '';
        feedContainer.style.gap = '';
        feedContainer.style.background = '';
      } else {
        feedContainer.style.display = 'block';
        feedContainer.style.gridTemplateColumns = '';
        feedContainer.style.gap = '';
        feedContainer.style.background = '';
      }

      reorderCards(feedContainer, view === 'feed' ? 'feed' : 'tier');

      try {
        localStorage.setItem('eveView', view);
      } catch (e) {}
    }

    viewTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        applyView(tab.getAttribute('data-view') || 'list');
      });
    });

    var saved = 'list';
    try {
      saved = localStorage.getItem('eveView') || 'list';
    } catch (e) {}
    applyView(saved);
  }

  function initStoryDrag() {
    var storyRow = document.getElementById('storyRow');
    if (!storyRow) return;

    var isDown = false;
    var startX = 0;
    var scrollLeft = 0;

    storyRow.addEventListener('mousedown', function (e) {
      isDown = true;
      startX = e.pageX - storyRow.offsetLeft;
      scrollLeft = storyRow.scrollLeft;
    });
    storyRow.addEventListener('mouseleave', function () {
      isDown = false;
    });
    storyRow.addEventListener('mouseup', function () {
      isDown = false;
    });
    storyRow.addEventListener('mousemove', function (e) {
      if (!isDown) return;
      e.preventDefault();
      var x = e.pageX - storyRow.offsetLeft;
      storyRow.scrollLeft = scrollLeft - (x - startX) * 1.5;
    });

    storyRow.addEventListener('touchstart', function (e) {
      isDown = true;
      startX = e.touches[0].pageX - storyRow.offsetLeft;
      scrollLeft = storyRow.scrollLeft;
    }, { passive: true });
    storyRow.addEventListener('touchend', function () {
      isDown = false;
    });
    storyRow.addEventListener('touchmove', function (e) {
      if (!isDown) return;
      var x = e.touches[0].pageX - storyRow.offsetLeft;
      storyRow.scrollLeft = scrollLeft - (x - startX) * 1.5;
    }, { passive: true });
  }

  function boot() {
    initRenewalBodyClass();
    initCardLinks();
    initViewTabs();
    initStoryDrag();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
