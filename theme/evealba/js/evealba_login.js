(function () {
  'use strict';

  document.querySelectorAll('.pw-toggle[data-target]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-target');
      var el = document.getElementById(id);
      if (!el) return;
      el.type = el.type === 'password' ? 'text' : 'password';
      btn.textContent = el.type === 'text' ? '🙈' : '👁';
    });
  });

  document.querySelectorAll('.auto-row[data-check]').forEach(function (row) {
    var checkId = row.getAttribute('data-check');
    var box = row.querySelector('.auto-check');
    var input = document.getElementById(checkId);
    if (!input || !box) return;

    function sync() {
      var on = input.checked;
      box.classList.toggle('on', on);
      box.textContent = on ? '✓' : '';
    }

    row.addEventListener('click', function (e) {
      if (e.target === input) return;
      if (checkId === 'login_auto_login' && !input.checked) {
        if (!confirm('자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?')) {
          return;
        }
      }
      input.checked = !input.checked;
      sync();
    });

    input.addEventListener('change', sync);
    sync();
  });
})();
