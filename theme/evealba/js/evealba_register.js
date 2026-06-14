/**
 * 회원가입 리뉴얼 UI — evealba_register.html + 기존 API 연동
 */
(function () {
  'use strict';

  var cfg = window.EVE_REGISTER || {};
  var baseUrl = cfg.baseUrl || '';
  var checked = { chk1: false, chk2: false, chk3: false };
  var memberType = 'personal';
  var verifyDone = false;
  var idChecked = false;
  var checkedId = '';
  var selectedIndustry = '';
  var selectedSex = '';
  var _ocrResult = null;

  function $(id) { return document.getElementById(id); }

  function setCheckEl(id, val) {
    var el = $(id);
    if (!el) return;
    el.classList.toggle('checked', val);
    el.textContent = val ? '✓' : '';
  }

  function syncAllCheck() {
    var all = checked.chk1 && checked.chk2 && checked.chk3;
    setCheckEl('chkAll', all);
  }

  function toggleItem(checkId, termsId, arrId) {
    checked[checkId] = !checked[checkId];
    setCheckEl(checkId, checked[checkId]);
    if (termsId && $(termsId)) {
      $(termsId).classList.toggle('open', checked[checkId]);
      if (arrId && $(arrId)) $(arrId).textContent = checked[checkId] ? '∨' : '›';
    }
    syncAllCheck();
  }

  function toggleAll() {
    var val = !(checked.chk1 && checked.chk2 && checked.chk3);
    ['chk1', 'chk2', 'chk3'].forEach(function (k) {
      checked[k] = val;
      setCheckEl(k, val);
    });
    ['terms1', 'terms2'].forEach(function (id) {
      if ($(id)) $(id).classList.toggle('open', val);
    });
    if ($('arr1')) $('arr1').textContent = val ? '∨' : '›';
    if ($('arr2')) $('arr2').textContent = val ? '∨' : '›';
    syncAllCheck();
  }

  function setStep(n) {
    $('step1').classList.toggle('active', n === 1);
    $('step2').classList.toggle('active', n === 2);
    $('step3').classList.toggle('active', n === 3);
    for (var i = 1; i <= 3; i++) {
      var sc = $('sc' + i), sl = $('sl' + i);
      if (!sc || !sl) continue;
      sc.classList.remove('active', 'done');
      sl.classList.remove('active', 'done');
      if (i < n) {
        sc.classList.add('done');
        sl.classList.add('done');
        sc.textContent = '✓';
      } else if (i === n) {
        sc.classList.add('active');
        sl.classList.add('active');
        sc.textContent = String(i);
      } else {
        sc.textContent = String(i);
      }
    }
    for (var j = 1; j <= 2; j++) {
      if ($('line' + j)) $('line' + j).classList.toggle('done', j < n);
    }
    var labels = ['📝 회원가입', '정보입력', '가입완료'];
    if ($('regBreadcrumb')) $('regBreadcrumb').textContent = labels[n - 1] || '📝 회원가입';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function selectType(type) {
    memberType = type;
    $('typePersonal').classList.toggle('selected', type === 'personal');
    $('typeBiz').classList.toggle('selected', type === 'biz');
  }

  function goStep2() {
    if (!checked.chk1 || !checked.chk2) {
      alert('필수 약관에 동의해주세요.');
      return;
    }
    if (!verifyDone) {
      alert('본인인증을 완료해주세요.');
      return;
    }
    var isBiz = memberType === 'biz';
    $('industryGroup').style.display = isBiz ? '' : 'none';
    $('bizExtra').classList.toggle('active', isBiz);
    $('memberTypeLabel').textContent = isBiz ? '기업회원' : '개인회원';
    setStep(2);
  }

  function doAuth() {
    verifyDone = true;
    $('btnAuth').style.display = 'none';
    var d = $('authDone');
    d.style.display = 'flex';
    $('inputName').value = '홍길동';
    $('inputName').readOnly = true;
  }

  function checkIdFormat() {
    idChecked = false;
    checkedId = '';
    var val = $('inputId').value;
    var hint = $('hintId');
    var inp = $('inputId');
    inp.classList.remove('ok', 'err');
    if (!val.length) {
      hint.textContent = '영문, 숫자 4~10자로 입력해주세요.';
      hint.className = 'form-hint';
    } else if (!/^[a-zA-Z0-9]+$/.test(val)) {
      hint.textContent = '영문, 숫자만 사용 가능합니다.';
      hint.className = 'form-hint err';
      inp.classList.add('err');
    } else if (val.length < 4) {
      hint.textContent = '4자 이상 입력해주세요.';
      hint.className = 'form-hint err';
      inp.classList.add('err');
    } else {
      hint.textContent = '✓ 사용 가능한 형식입니다. 중복확인을 해주세요.';
      hint.className = 'form-hint ok';
    }
  }

  function checkIdDuplicate() {
    var val = $('inputId').value.trim();
    if (!val || val.length < 4) {
      alert('아이디를 먼저 입력해주세요 (영문, 숫자 4~10자).');
      return;
    }
    var hint = $('hintId');
    hint.textContent = '⏳ 확인중...';
    hint.className = 'form-hint';
    fetch(baseUrl + '/eve_check_id.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'mb_id=' + encodeURIComponent(val)
    })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.available) {
          hint.textContent = '✓ 사용 가능한 아이디입니다.';
          hint.className = 'form-hint ok';
          $('inputId').classList.add('ok');
          idChecked = true;
          checkedId = val;
        } else {
          hint.textContent = d.msg || '이미 사용중인 아이디입니다.';
          hint.className = 'form-hint err';
          idChecked = false;
        }
      })
      .catch(function () {
        hint.textContent = '확인 실패. 다시 시도해주세요.';
        hint.className = 'form-hint err';
      });
  }

  function checkPwConfirm() {
    var pw = $('inputPw').value;
    var c = $('inputPwConfirm').value;
    var hint = $('hintPw');
    if (!c) { hint.textContent = ''; return; }
    if (pw === c) {
      hint.textContent = '✓ 비밀번호가 일치합니다.';
      hint.className = 'form-hint ok';
    } else {
      hint.textContent = '비밀번호가 일치하지 않습니다.';
      hint.className = 'form-hint err';
    }
  }

  function togglePw(targetId, btn) {
    var el = $(targetId);
    if (!el) return;
    el.type = el.type === 'password' ? 'text' : 'password';
    btn.textContent = el.type === 'text' ? '🙈' : '👁';
  }

  function formatBizNum(el) {
    var v = el.value.replace(/[^0-9]/g, '').substring(0, 10);
    el.value = v;
  }

  function showFileName(input) {
    var h = $('fileHint');
    if (input.files && input.files.length) {
      h.textContent = '✓ ' + input.files[0].name;
      h.className = 'form-hint ok';
    }
  }

  function doJoin() {
    var id = $('inputId').value.trim();
    var pw = $('inputPw').value;
    var pw2 = $('inputPwConfirm').value;
    var name = $('inputName').value.trim();
    var nick = $('inputNick').value.trim();
    var birthY = $('inp-birth-y').value;
    var birthM = $('inp-birth-m').value;
    var birthD = $('inp-birth-d').value;
    var emailId = $('emailId').value.trim();
    var domainSel = $('emailDomainSel').value;
    var emailDomain = domainSel === 'custom' ? $('emailCustom').value.trim() : domainSel;
    var hp = $('inputHp').value.trim();

    if (!id || id.length < 4) { alert('아이디를 입력해주세요.'); return; }
    if (!idChecked || checkedId !== id) { alert('아이디 중복확인을 해주세요.'); return; }
    if (!pw || pw.length < 4 || pw.length > 12) { alert('비밀번호를 확인해주세요.'); return; }
    if (!/[a-zA-Z]/.test(pw) || !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?`~]/.test(pw)) {
      alert('비밀번호는 영문+특수문자 조합이어야 합니다.');
      return;
    }
    if (pw !== pw2) { alert('비밀번호가 일치하지 않습니다.'); return; }
    if (!name || !nick) { alert('이름과 닉네임을 입력해주세요.'); return; }
    if (!birthY || !birthM || !birthD) { alert('생년월일을 선택해주세요.'); return; }
    if (!selectedSex) { alert('성별을 선택해주세요.'); return; }
    if (!emailId || !emailDomain) { alert('이메일을 입력해주세요.'); return; }
    if (!hp) { alert('핸드폰 번호를 입력해주세요.'); return; }

    var jobType = selectedIndustry;
    if (memberType === 'biz') {
      if (!jobType) { alert('업종을 선택해주세요.'); return; }
      var docFile = $('bizFile');
      if (!docFile.files || !docFile.files[0]) { alert('사업자등록증을 첨부해주세요.'); return; }
      if (!$('inp-biz-num').value || $('inp-biz-num').value.length !== 10) { alert('사업자번호 10자리를 입력해주세요.'); return; }
      if (!$('inp-biz-name').value.trim()) { alert('상호명을 입력해주세요.'); return; }
      if (!$('inp-biz-rep').value.trim()) { alert('대표자를 입력해주세요.'); return; }
      if (!$('inp-biz-addr').value.trim()) { alert('주소를 입력해주세요.'); return; }
    } else if (!jobType) {
      jobType = '기타';
    }

    var btn = $('btnSubmit');
    btn.disabled = true;
    btn.textContent = '⏳ 가입 처리중...';

    var fd = new FormData();
    fd.append('mb_id', id);
    fd.append('mb_password', pw);
    fd.append('mb_password_re', pw2);
    fd.append('mb_name', name);
    fd.append('mb_nick', nick);
    fd.append('mb_birth', birthY + '-' + birthM + '-' + birthD);
    fd.append('mb_sex', selectedSex);
    fd.append('mb_email', emailId + '@' + emailDomain);
    fd.append('mb_hp', hp);
    fd.append('mb_sms', $('chkSms').classList.contains('checked') ? '1' : '0');
    fd.append('mb_1', memberType);
    fd.append('mb_9', jobType);
    var ref = $('inputReferral').value.trim();
    if (ref) fd.append('mb_referral_nick', ref);

    if (memberType === 'biz') {
      fd.append('mb_2', $('inp-biz-num').value);
      fd.append('mb_3', $('inp-biz-name').value);
      fd.append('mb_4', $('inp-biz-rep').value);
      fd.append('mb_5', $('inp-biz-addr').value);
      fd.append('biz_doc', $('bizFile').files[0]);
      if (_ocrResult) fd.append('ocr_data', JSON.stringify(_ocrResult));
    }

    fetch(baseUrl + '/eve_register_update.php', { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        btn.disabled = false;
        btn.textContent = '가입 완료하기 🌸';
        if (d.ok) {
          if (memberType === 'biz') {
            $('completeMsg').innerHTML = '기업회원 가입 신청이 완료되었습니다.<br><strong style="color:var(--pink);">관리자 승인 후 로그인이 가능합니다.</strong>';
          }
          setStep(3);
        } else {
          alert(d.msg || '가입에 실패했습니다.');
        }
      })
      .catch(function () {
        btn.disabled = false;
        btn.textContent = '가입 완료하기 🌸';
        alert('네트워크 오류가 발생했습니다.');
      });
  }

  document.querySelectorAll('.agree-item').forEach(function (item) {
    item.addEventListener('click', function () {
      toggleItem(item.getAttribute('data-chk'), item.getAttribute('data-terms'), item.getAttribute('data-arr'));
    });
  });
  if ($('agreeAllWrap')) $('agreeAllWrap').addEventListener('click', toggleAll);
  if ($('btnAuth')) $('btnAuth').addEventListener('click', doAuth);
  document.querySelectorAll('.type-btn').forEach(function (btn) {
    btn.addEventListener('click', function () { selectType(btn.getAttribute('data-type')); });
  });
  if ($('btnGoStep2')) $('btnGoStep2').addEventListener('click', goStep2);
  if ($('btnBackStep1')) $('btnBackStep1').addEventListener('click', function () { setStep(1); });
  if ($('btnCheckId')) $('btnCheckId').addEventListener('click', checkIdDuplicate);
  if ($('inputId')) $('inputId').addEventListener('input', checkIdFormat);
  if ($('inputPw')) $('inputPw').addEventListener('input', checkPwConfirm);
  if ($('inputPwConfirm')) $('inputPwConfirm').addEventListener('input', checkPwConfirm);
  document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () { togglePw(btn.getAttribute('data-target'), btn); });
  });
  document.querySelectorAll('.gender-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.gender-btn').forEach(function (b) { b.classList.remove('selected'); });
      btn.classList.add('selected');
      selectedSex = btn.getAttribute('data-sex');
    });
  });
  document.querySelectorAll('.industry-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.industry-btn').forEach(function (b) { b.classList.remove('selected'); });
      btn.classList.add('selected');
      selectedIndustry = btn.getAttribute('data-value');
    });
  });
  if ($('emailDomainSel')) {
    $('emailDomainSel').addEventListener('change', function () {
      $('emailCustom').style.display = this.value === 'custom' ? '' : 'none';
    });
  }
  if ($('smsAgreeWrap')) {
    $('smsAgreeWrap').addEventListener('click', function () {
      var on = $('chkSms').classList.toggle('checked');
      $('chkSms').textContent = on ? '✓' : '';
    });
  }
  if ($('bizFileArea')) {
    $('bizFileArea').addEventListener('click', function () { $('bizFile').click(); });
  }
  if ($('bizFile')) $('bizFile').addEventListener('change', function () { showFileName(this); });
  if ($('inp-biz-num')) $('inp-biz-num').addEventListener('input', function () { formatBizNum(this); });
  if ($('btnSubmit')) $('btnSubmit').addEventListener('click', doJoin);
})();
