/**
 * 이브알바 UI 리뉴얼 JS — 시안 (evealba_main.html) 1:1
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

  function initViewTabs() {
    var viewTabs = document.querySelectorAll('.view-tab');
    var feedContainer = document.getElementById('feedContainer');
    if (!viewTabs.length || !feedContainer) return;

    function applyView(view) {
      viewTabs.forEach(function (t) {
        t.classList.toggle('active', t.getAttribute('data-view') === view);
      });

      feedContainer.className = 'feed-container view-' + view;

      var cards = feedContainer.querySelectorAll('.recruit-card');
      if (view === 'list') {
        feedContainer.style.display = 'block';
        feedContainer.style.gridTemplateColumns = '';
        feedContainer.style.gap = '';
        feedContainer.style.background = '';
      } else if (view === 'feed') {
        feedContainer.style.display = 'block';
        feedContainer.style.gridTemplateColumns = '';
        feedContainer.style.gap = '';
        feedContainer.style.background = '';
      } else if (view === 'grid') {
        feedContainer.style.display = 'grid';
        feedContainer.style.gridTemplateColumns = 'repeat(2,1fr)';
        feedContainer.style.gap = '1px';
        feedContainer.style.background = 'var(--border)';
      }

      cards.forEach(function (card) {
        var thumb = card.querySelector('.card-thumb');
        if (view === 'list') {
          card.style.cssText = '';
          if (thumb) thumb.style.cssText = '';
        } else if (view === 'feed') {
          card.style.cssText = 'border-bottom:8px solid #F5F5F5;';
          if (thumb) {
            thumb.style.width = '100%';
            thumb.style.height = '240px';
          }
        } else if (view === 'grid') {
          card.style.cssText = 'background:#fff;';
          if (thumb) {
            thumb.style.width = '100%';
            thumb.style.height = '140px';
          }
        }
      });

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
