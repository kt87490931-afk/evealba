/**
 * 이브알바 UI 리뉴얼 JS — 뷰 전환, 스토리 슬라이더
 */
(function () {
  'use strict';

  function initViewToggle() {
    var container = document.getElementById('recruitContainer');
    if (!container) return;

    var buttons = document.querySelectorAll('.view-toggle .view-btn');
    if (!buttons.length) return;

    function applyView(view) {
      container.className = 'recruit-container view-' + view;
      buttons.forEach(function (btn) {
        btn.classList.toggle('active', btn.getAttribute('data-view') === view);
      });
      try {
        localStorage.setItem('evealba_preferredView', view);
      } catch (e) {}
    }

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        applyView(btn.getAttribute('data-view') || 'feed');
      });
    });

    var saved = 'feed';
    try {
      saved = localStorage.getItem('evealba_preferredView') || 'feed';
    } catch (e) {}
    applyView(saved);
  }

  function initStorySlider() {
    var slider = document.querySelector('.story-slider');
    if (!slider) return;

    var prev = document.querySelector('.story-prev');
    var next = document.querySelector('.story-next');
    var step = 200;

    if (prev) {
      prev.addEventListener('click', function () {
        slider.scrollBy({ left: -step, behavior: 'smooth' });
      });
    }
    if (next) {
      next.addEventListener('click', function () {
        slider.scrollBy({ left: step, behavior: 'smooth' });
      });
    }
  }

  function initRenewalBodyClass() {
    document.body.classList.add('eve-renewal-active');
    if (window.g5_is_mobile === '1' || window.innerWidth <= 768) {
      document.body.classList.add('eve-renewal-mobile');
    }
    if (document.querySelector('.panel-right')) {
      document.body.classList.add('eve-panel-right-on');
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initRenewalBodyClass();
      initViewToggle();
      initStorySlider();
    });
  } else {
    initRenewalBodyClass();
    initViewToggle();
    initStorySlider();
  }
})();
