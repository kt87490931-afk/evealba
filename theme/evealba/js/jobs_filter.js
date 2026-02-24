/**
 * 채용정보 필터: 지역 ↔ 세부지역, 직종 ↔ 세부직종 연동
 * 지역/직종 선택 시 세부 옵션을 해당 선택에 맞게 필터링
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

  ready(function() {
    filterSubByParent('filter-er-id', 'filter-erd-id', 'er-id');
    filterSubByParent('filter-ei-id', 'filter-ej-id', 'ei-id');
  });
})();
