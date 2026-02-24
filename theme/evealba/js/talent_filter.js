/**
 * 인재정보 필터: 직종↔세부직종 연동, 검색 버튼 submit, 결과 필터링
 */
(function() {
  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  function filterSubByParent(parentSelect, subSelect, dataAttr) {
    var parent = document.getElementById(parentSelect);
    var sub = document.getElementById(subSelect);
    if (!parent || !sub) return;

    var placeholder = sub.querySelector('option[value=""]');
    var dataOpts = sub.querySelectorAll('option[data-' + dataAttr + ']');
    var cache = [];
    dataOpts.forEach(function(opt) {
      cache.push({
        value: opt.value,
        text: opt.textContent,
        dataVal: opt.getAttribute('data-' + dataAttr)
      });
    });

    function apply() {
      var selectedId = parent.value;
      while (sub.options.length > 0) sub.remove(0);
      if (placeholder) {
        var p = document.createElement('option');
        p.value = '';
        p.textContent = placeholder.textContent;
        sub.appendChild(p);
      }
      cache.forEach(function(o) {
        if (!selectedId || o.dataVal === selectedId) {
          var opt = document.createElement('option');
          opt.value = o.value;
          opt.textContent = o.text;
          opt.setAttribute('data-' + dataAttr, o.dataVal);
          sub.appendChild(opt);
        }
      });
      sub.value = '';
    }

    parent.addEventListener('change', apply);
    apply();
  }

  function applySearchFilter() {
    var form = document.getElementById('talent-search-form');
    if (!form) return;

    var erSelect = document.getElementById('talent-filter-er-id');
    var eiSelect = document.getElementById('talent-filter-ei-id');
    var ejSelect = document.getElementById('talent-filter-ej-id');
    var stxInput = form.querySelector('input[name="stx"]');

    var regionName = (erSelect && erSelect.value && erSelect.selectedOptions[0]) ? erSelect.selectedOptions[0].textContent.trim() : '';
    var typeName = (eiSelect && eiSelect.value && eiSelect.selectedOptions[0]) ? eiSelect.selectedOptions[0].textContent.trim() : '';
    var subTypeName = (ejSelect && ejSelect.value && ejSelect.selectedOptions[0]) ? ejSelect.selectedOptions[0].textContent.trim() : '';
    var stx = stxInput ? stxInput.value.trim().toLowerCase() : '';

    var hasFilter = !!regionName || !!typeName || !!subTypeName || !!stx;

    function match(el) {
      if (!hasFilter) return true;
      var r = (el.getAttribute('data-region') || '').trim();
      var t = (el.getAttribute('data-type') || '').trim();
      var fullText = (el.textContent || '').toLowerCase();
      if (regionName && r !== regionName) return false;
      if (typeName && t !== typeName) return false;
      if (subTypeName && fullText.indexOf(subTypeName) === -1) return false;
      if (stx && fullText.indexOf(stx) === -1) return false;
      return true;
    }

    document.querySelectorAll('.talent-row').forEach(function(el) {
      el.style.display = match(el) ? '' : 'none';
    });
  }

  function initResetBtn() {
    var form = document.getElementById('talent-search-form');
    var resetBtn = form && form.querySelector('.btn-reset');
    if (resetBtn) {
      resetBtn.addEventListener('click', function() {
        var url = (typeof g5_url !== 'undefined' ? g5_url : '/') + 'talent.php';
        window.location.href = url;
      });
    }
  }

  ready(function() {
    filterSubByParent('talent-filter-ei-id', 'talent-filter-ej-id', 'ei-id');
    applySearchFilter();
    initResetBtn();
  });
})();
