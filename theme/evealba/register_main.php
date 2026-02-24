<?php if (!defined('_GNUBOARD_')) exit; ?>
<!-- PAGE (footer-info 섹션 제외, 원본 100% 동일) -->
<div class="page-wrap register-page-wrap">

  <!-- ===== STEP BAR ===== -->
  <div class="step-bar">
    <div class="step-item active" id="step-item-1">
      <div class="step-num">01</div>
      <div>
        <div class="step-label">STEP 01 &nbsp; 약관동의</div>
        <div class="step-sub">이용약관 / 개인정보 동의</div>
      </div>
      <span class="step-icon">⚙️</span>
    </div>
    <div class="step-item inactive" id="step-item-2">
      <div class="step-num">02</div>
      <div>
        <div class="step-label">STEP 02 &nbsp; 회원정보입력</div>
        <div class="step-sub">기본 정보 입력</div>
      </div>
      <span class="step-icon">✏️</span>
    </div>
    <div class="step-item inactive" id="step-item-3">
      <div class="step-num">03</div>
      <div>
        <div class="step-label">STEP 03 &nbsp; 가입완료</div>
        <div class="step-sub">회원가입 완료</div>
      </div>
      <span class="step-icon">🌸</span>
    </div>
  </div>

  <!-- ================================================================
       STEP 1 : 약관동의
  ================================================================ -->
  <div id="screen-step1">

    <!-- 회원가입 & 약관동의 헤더 -->
    <div class="form-card sh-pink">
      <div class="sec-head">
        <span class="sec-head-icon">📜</span>
        <span class="sec-head-title">회원가입 &amp; 약관동의</span>
        <span class="sec-head-sub">회원가입을 위해 실명인증(유,무)/이용약관/개인정보보호정책에 대한 내용을 읽고 동의해주세요.</span>
      </div>
    </div>

    <!-- 이용약관 -->
    <div class="form-card">
      <div class="terms-box-wrap">
        <div class="terms-box-title">📋 이용약관</div>
        <div class="terms-scroll-box" id="terms1-scroll">
          <p><strong>제 1장 총칙</strong></p><br>
          <p><strong>제 1조 (목적)</strong><br>
          본 약관은 유흥알바 정보제공 사이트 이브알바(이하 '회사'라 한다)가 인터넷을 통하여 운영하는 이브알바(www.evealba.com) 서비스를 제공함에 있어 이를 이용하는 이용자와 이브알바의 권리, 의무 및 책임사항을 규정함을 목적으로 한다.</p><br>
          <p><strong>제 2조 (용어의 정의)</strong><br>
          이 약관에서 사용하는 용어의 정의는 이래와 같다.<br>
          ① '회사'라 함은 인터넷 사이트 이브알바(www.evealba.com)를 운영하는 사이트를 말한다.<br>
          ② '서비스'라 함은 인터넷 이브알바(www.evealba.com) 및 정보통신설비를 이용하여 알바 기업에서 등록하는 구인자료, 구직과 교육을 목적으로 등록하는 자료 등을 각각의 목적에 맞게 분류 가공, 집계하여 정보를 제공하는 내용 및 기타 관련된 부대 서비스를 말한다.<br>
          ③ '개인회원'이라 함은 서비스를 이용하기 위하여 동 약관에 동의하고 '회사'와 이용계약을 체결하여 '이용자ID'를 부여 받은 개인을 말한다.<br>
          ④ '기업회원'이라 함은 서비스를 이용하기 위하여 동 약관에 동의하고 '회사'와 이용계약을 체결하여 '이용자ID'를 부여 받은 법인 또는 개인사업자를 말한다.</p><br>
          <p><strong>제 3조 (약관의 효력과 변경)</strong><br>
          ① 이 약관은 서비스를 이용하고자 하는 모든 이용자에 대하여 그 효력을 발생한다.<br>
          ② 회사는 관련 법령에 위배하지 않는 범위에서 이 약관을 개정할 수 있다.<br>
          ③ 이 약관에 동의하는 것은 정기적으로 웹사이트를 방문하여 약관을 확인할 의무가 있음에 동의하는 것으로 간주됩니다.</p><br>
          <p><strong>제 4조 (서비스의 제공)</strong><br>
          ① 회사는 다음과 같은 업무를 수행합니다.<br>
          - 재화 또는 용역에 대한 정보 제공 및 구매계약의 체결<br>
          - 기타 회사가 정하는 업무</p>
        </div>
        <div class="terms-agree-row" id="agree1-row">
          <input type="checkbox" id="agree-terms" onchange="checkAgreements()">
          <label for="agree-terms">✅ 회원이용약관 내용에 동의합니다.</label>
        </div>
      </div>
    </div>

    <!-- 개인정보 보호정책 -->
    <div class="form-card">
      <div class="terms-box-wrap">
        <div class="terms-box-title">🔒 개인정보 보호정책</div>
        <div class="terms-scroll-box" id="terms2-scroll">
          <p>이브알바 (www.evealba.com 이하 '회사'라 함)는 이용자들의 개인정보보호를 매우 중요시하며,이용자가 회사의 서비스(이하 '이브알바 서비스' 또는 '이브알바'라 함)를 이용함과 동시에 온라인상에서 회사에 제공한 개인정보가 보호 받을 수 있도록 최선을 다하고 있습니다.</p><br>
          <p>이에 회사는 통신비밀보호법,전기통신사업법,정보통신망 이용촉진 및 정보보호 등에 관한 법률 등 정보통신서비스제공자가 준수하여야 할 관련 법규상의 개인정보보호규정 및 정보통신부가 제정한 개인정보보호지침을 준수하고 있습니다.</p><br>
          <p>회사는 개인정보 보호정책을 통하여 이용자들이 제공하는 개인정보가 어떠한 용도와 방식으로 이용되고 있으며 개인정보보호를 위해 어떠한 조치가 취해지고 있는지 알려드립니다.</p><br>
          <p>회사는 개인정보 보호정책을 홈페이지 첫 회면에 공개함으로써 이용자들이 언제나 용이하게 보살 수 있도록 조치하고 있습니다.</p><br>
          <p><strong>1. 수집하는 개인정보의 항목</strong><br>
          회사는 회원가입, 상담, 서비스 신청 등을 위해 아래와 같은 개인정보를 수집하고 있습니다.<br>
          - 필수항목: 이름, 생년월일, 성별, 로그인ID, 비밀번호, 닉네임, 이메일, 연락처<br>
          - 선택항목: 프로필 사진</p><br>
          <p><strong>2. 개인정보의 수집 및 이용목적</strong><br>
          회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.<br>
          - 서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산<br>
          - 회원 관리, 서비스 개선, 신규 서비스 개발</p>
        </div>
        <div class="terms-agree-row">
          <input type="checkbox" id="agree-privacy" onchange="checkAgreements()">
          <label for="agree-privacy">✅ 개인정보 보호정책에 동의합니다.</label>
        </div>
      </div>
    </div>

    <!-- 본인인증 섹션 -->
    <div class="form-card">
      <div style="padding:12px 20px 10px;border-bottom:2px solid var(--pale-pink);">
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="font-size:18px;">📱</span>
          <span style="font-size:15px;font-weight:900;color:var(--dark);">본인실명인증</span>
          <span style="font-size:12px;color:#aaa;margin-left:4px;">휴대전화 실명인증을 해주세요.</span>
        </div>
      </div>
      <div class="verify-section">
        <div class="verify-box">
          <span class="verify-phone-img">📱</span>
          <div class="verify-right">
            <p>인증시 본인실명 확인된 분 어떠한 형태로도<br>
            <span>고객님의 정보는 저장되지 않습니다.</span><br>
            핸드폰으로 실명인증을 원하시면 아래 버튼을 클릭하세요.</p>
            <button class="btn-verify" type="button" onclick="doVerify()">
              📲 핸드폰 실명인증
            </button>
          </div>
        </div>
        <div class="verify-done" id="verify-done">
          <span class="verify-done-icon">✅</span>
          <span class="verify-done-text">본인인증이 완료되었습니다! 회원가입을 계속 진행해주세요.</span>
        </div>
      </div>
    </div>

    <!-- 회원 유형 선택 버튼 -->
    <div class="join-type-btns" id="type-btns" style="display:none;">
      <button class="btn-type-biz" type="button" onclick="goStep2('biz')">
        🏢 기업회원 가입하기
        <span class="sub">채용공고 등록가능</span>
      </button>
      <button class="btn-type-personal" type="button" onclick="goStep2('personal')">
        👤 개인회원 가입하기
        <span class="sub">이력서 등록가능</span>
      </button>
    </div>

  </div>
  <!-- /STEP 1 -->

  <!-- ================================================================
       STEP 2 : 회원정보 입력
  ================================================================ -->
  <div id="screen-step2" style="display:none;">

    <!-- 폼 카드 -->
    <div class="form-card">

      <!-- 폼 헤더 -->
      <div class="reg-form-header">
        <span class="reg-form-title">📝 회원가입 정보입력</span>
        <span class="reg-form-type-badge badge-biz" id="member-type-badge">🏢 기업회원</span>
      </div>

      <!-- 필수항목 안내 + 재인증 -->
      <div class="reg-notice">
        <span class="reg-notice-text">✅ <strong>체크된 필수항목만 작성하시면 회원가입 가능합니다.</strong></span>
        <button class="btn-re-verify" type="button" onclick="reVerify()">📲 휴대폰 재인증</button>
      </div>

      <!-- 아이디 -->
      <div class="form-row">
        <div class="form-label">아이디 <span class="req">*</span></div>
        <div class="form-cell col">
          <div style="display:flex;gap:8px;width:100%;">
            <input class="fi fi-md" id="inp-id" type="text" placeholder="아이디를 입력해주세요" oninput="checkIdFormat()">
            <button class="btn-id-check" type="button" onclick="checkIdDuplicate()">중복확인</button>
          </div>
          <span class="fi-hint" id="id-hint">4자 이상 15자이하로 입력해주세요.</span>
        </div>
      </div>

      <!-- 비밀번호 -->
      <div class="form-row">
        <div class="form-label">비밀번호 <span class="req">*</span></div>
        <div class="form-cell col">
          <div class="pw-wrap" style="max-width:280px;">
            <input class="fi" id="inp-pw" type="password" placeholder="비밀번호 입력" oninput="checkPw()">
            <span class="pw-toggle" onclick="togglePw('inp-pw','eye1')" id="eye1">👁</span>
          </div>
          <span class="fi-hint" id="pw-hint">4자 이상 12자이하로 입력해 주세요.</span>
        </div>
      </div>

      <!-- 비밀번호 확인 -->
      <div class="form-row">
        <div class="form-label">비밀번호 확인 <span class="req">*</span></div>
        <div class="form-cell col">
          <div class="pw-wrap" style="max-width:280px;">
            <input class="fi" id="inp-pw2" type="password" placeholder="비밀번호를 다시 입력해주세요" oninput="checkPw2()">
            <span class="pw-toggle" onclick="togglePw('inp-pw2','eye2')" id="eye2">👁</span>
          </div>
          <span class="fi-hint" id="pw2-hint">비밀번호를 다시 한 번 입력해주세요.</span>
        </div>
      </div>

      <!-- 이름 -->
      <div class="form-row">
        <div class="form-label">이름</div>
        <div class="form-cell">
          <input class="fi fi-sm fi-readonly" id="inp-name" type="text" value="차정호" readonly>
          <span style="font-size:11px;color:var(--hot-pink);font-weight:600;">✅ 실명인증 이용시 자동입력됩니다.</span>
        </div>
      </div>

      <!-- 닉네임 -->
      <div class="form-row">
        <div class="form-label">닉네임</div>
        <div class="form-cell col">
          <input class="fi fi-md" type="text" placeholder="닉네임 입력 (게시판에서 표시됩니다)">
          <span class="fi-hint">게시판에서 이름을 대신하여 사용되며, 1일 1회 수정가능합니다.</span>
        </div>
      </div>

      <!-- 생년월일 -->
      <div class="form-row">
        <div class="form-label">생년월일</div>
        <div class="form-cell">
          <div class="date-group">
            <select class="fi-select">
              <option>1988</option><option>1990</option><option>1992</option><option>1995</option><option>1998</option><option>2000</option><option>2002</option><option>2004</option>
            </select><span>년</span>
            <select class="fi-select" style="width:76px;">
              <option>03</option><option>01</option><option>02</option><option>04</option><option>05</option><option>06</option><option>07</option><option>08</option><option>09</option><option>10</option><option>11</option><option>12</option>
            </select><span>월</span>
            <select class="fi-select" style="width:76px;">
              <option>01</option><option>05</option><option>10</option><option>15</option><option>20</option><option>25</option><option>28</option><option>30</option>
            </select><span>일</span>
          </div>
          <span style="font-size:11px;color:var(--hot-pink);">▼</span>
        </div>
      </div>

      <!-- 성별 -->
      <div class="form-row">
        <div class="form-label">성별</div>
        <div class="form-cell">
          <div class="radio-group">
            <div class="radio-item"><input type="radio" name="gender" id="g-m" checked><label for="g-m">남자</label></div>
            <div class="radio-item"><input type="radio" name="gender" id="g-f"><label for="g-f">여자</label></div>
          </div>
          <span style="font-size:11px;color:var(--hot-pink);">▼</span>
        </div>
      </div>

      <!-- 이메일 -->
      <div class="form-row">
        <div class="form-label">이메일 <span class="req">*</span></div>
        <div class="form-cell">
          <div class="email-row">
            <input class="fi fi-sm" type="text" placeholder="이메일 아이디" id="email-id">
            <span class="email-at">@</span>
            <input class="fi" style="width:140px;" type="text" id="email-domain" placeholder="도메인">
            <select class="fi-select" onchange="setEmailDomain(this)">
              <option value="">직접입력</option>
              <option value="gmail.com">gmail.com</option>
              <option value="naver.com">naver.com</option>
              <option value="daum.net">daum.net</option>
              <option value="kakao.com">kakao.com</option>
              <option value="hotmail.com">hotmail.com</option>
              <option value="yahoo.com">yahoo.com</option>
            </select>
          </div>
          <span style="font-size:11px;color:var(--hot-pink);">▼</span>
        </div>
      </div>

      <!-- 업종 -->
      <div class="form-row">
        <div class="form-label">업종</div>
        <div class="form-cell">
          <select class="fi-select-full" style="max-width:280px;">
            <option>룸싸롱</option>
            <option>단란주점</option><option>가라오케</option><option>노래방</option>
            <option>클럽</option><option>바(Bar)</option><option>퍼블릭</option>
            <option>마사지</option><option>풀살롱</option><option>기타</option>
          </select>
        </div>
      </div>

      <!-- 핸드폰 -->
      <div class="form-row">
        <div class="form-label">핸드폰</div>
        <div class="form-cell">
          <input class="fi fi-sm fi-readonly" type="text" value="010-0000-0000" readonly>
          <span style="font-size:11px;color:var(--hot-pink);">▼</span>
        </div>
      </div>

      <!-- SMS 수신 동의 -->
      <div class="form-row">
        <div class="form-label">SMS수신동의</div>
        <div class="form-cell">
          <div class="sms-row">
            <input type="checkbox" id="sms-agree" checked>
            <label for="sms-agree">SMS수신에 동의합니다. &nbsp;<span>수신허용을 하시면 인재분들이 문자보내기 관리해집니다</span></label>
          </div>
        </div>
      </div>

      <!-- 하단 버튼 -->
      <div class="form-btns">
        <button class="btn-cancel" type="button" onclick="goStep1()">← 이전으로</button>
        <button class="btn-join" type="button" onclick="doJoin()">🌸 회원가입 완료</button>
      </div>
    </div>

  </div>
  <!-- /STEP 2 -->

  <!-- ================================================================
       STEP 3 : 가입완료
  ================================================================ -->
  <div id="screen-step3" style="display:none;">
    <div class="complete-wrap">
      <div class="complete-icon">🎉</div>
      <h2 class="complete-title"><span>이브알바</span> 회원가입을 환영합니다!</h2>
      <p class="complete-sub" id="complete-msg">회원가입이 성공적으로 완료되었습니다.<br>이브알바의 다양한 서비스를 이용해보세요.</p>
      <div class="complete-info-box">
        <div class="ci-row"><span class="ci-label">아이디</span><span class="ci-val pink" id="ci-id">—</span></div>
        <div class="ci-row"><span class="ci-label">회원유형</span><span class="ci-val" id="ci-type">—</span></div>
        <div class="ci-row"><span class="ci-label">이름</span><span class="ci-val" id="ci-name">차정호</span></div>
        <div class="ci-row"><span class="ci-label">가입일</span><span class="ci-val" id="ci-date">—</span></div>
      </div>
      <div class="complete-btns">
        <button class="btn-goto-login" type="button" onclick="location.href='<?php echo G5_BBS_URL ?>/login.php'">🔑 로그인하기</button>
        <button class="btn-goto-main" type="button" onclick="location.href='<?php echo G5_URL ?>'">🏠 메인으로 이동</button>
      </div>
    </div>
  </div>
  <!-- /STEP 3 -->

</div><!-- /page-wrap -->

<script>
/* ============================================================
   회원가입 STEP 상태 관리 (eve_alba_register.html 동일)
============================================================ */
var currentMemberType = 'biz';
var verifyDone = false;

function setStep(n) {
  [1,2,3].forEach(function(i){
    var el = document.getElementById('step-item-'+i);
    el.className = 'step-item ' + (i < n ? 'done' : i === n ? 'active' : 'inactive');
  });
  var labels = ['약관동의','회원정보입력','가입완료'];
  document.getElementById('breadcrumb').innerHTML =
    '<a href="<?php echo G5_URL ?>">🏠 메인</a><span class="sep">›</span>'
    + '<a href="#" onclick="goStep1();return false;">회원가입</a><span class="sep">›</span>'
    + '<span class="current">' + labels[n-1] + '</span>';
  window.scrollTo({top:0,behavior:'smooth'});
}

function showScreen(which) {
  ['step1','step2','step3'].forEach(function(s){
    document.getElementById('screen-'+s).style.display = (s === which) ? 'block' : 'none';
  });
}

function checkAgreements() {}

function doVerify() {
  verifyDone = true;
  document.getElementById('verify-done').classList.add('show');
  document.querySelector('.btn-verify').textContent = '✅ 인증완료';
  document.querySelector('.btn-verify').style.background = 'linear-gradient(135deg,#2E7D32,#4CAF50)';
  document.querySelector('.btn-verify').style.animation = 'none';
  document.getElementById('agree-terms').checked = true;
  document.getElementById('agree-privacy').checked = true;
  document.getElementById('type-btns').style.display = 'grid';
  setTimeout(function(){
    document.getElementById('type-btns').scrollIntoView({behavior:'smooth',block:'center'});
  }, 300);
}

function goStep2(type) {
  if(!document.getElementById('agree-terms').checked) { alert('이용약관에 동의해주세요.'); return; }
  if(!document.getElementById('agree-privacy').checked) { alert('개인정보 보호정책에 동의해주세요.'); return; }
  if(!verifyDone) { alert('본인인증을 완료해주세요.'); return; }
  currentMemberType = type;
  var badge = document.getElementById('member-type-badge');
  if(type === 'biz') {
    badge.textContent = '🏢 기업회원';
    badge.className = 'reg-form-type-badge badge-biz';
  } else {
    badge.textContent = '👤 개인회원';
    badge.className = 'reg-form-type-badge badge-personal';
  }
  showScreen('step2');
  setStep(2);
}

function goStep1() {
  showScreen('step1');
  setStep(1);
}

function reVerify() {
  verifyDone = false;
  showScreen('step1');
  setStep(1);
  document.getElementById('verify-done').classList.remove('show');
  document.getElementById('type-btns').style.display = 'none';
  var btn = document.querySelector('.btn-verify');
  btn.textContent = '📲 핸드폰 실명인증';
  btn.style.background = '';
  btn.style.animation = '';
  document.getElementById('agree-terms').checked = false;
  document.getElementById('agree-privacy').checked = false;
}

function checkIdFormat() {
  var val = document.getElementById('inp-id').value;
  var hint = document.getElementById('id-hint');
  if(val.length === 0) { hint.textContent = '4자 이상 15자이하로 입력해주세요.'; hint.className = 'fi-hint'; }
  else if(val.length < 4) { hint.textContent = '⚠ 4자 이상 입력해주세요.'; hint.className = 'fi-hint err'; }
  else if(val.length > 15) { hint.textContent = '⚠ 15자 이하로 입력해주세요.'; hint.className = 'fi-hint err'; }
  else { hint.textContent = '✅ 사용 가능한 형식입니다. 중복확인을 해주세요.'; hint.className = 'fi-hint ok'; }
}

function checkIdDuplicate() {
  var val = document.getElementById('inp-id').value;
  if(!val || val.length < 4) { alert('아이디를 먼저 입력해주세요.'); return; }
  document.getElementById('id-hint').textContent = '✅ 사용 가능한 아이디입니다.';
  document.getElementById('id-hint').className = 'fi-hint ok';
}

function checkPw() {
  var val = document.getElementById('inp-pw').value;
  var hint = document.getElementById('pw-hint');
  if(val.length === 0) { hint.textContent = '4자 이상 12자이하로 입력해 주세요.'; hint.className = 'fi-hint'; }
  else if(val.length < 4) { hint.textContent = '⚠ 4자 이상 입력해주세요.'; hint.className = 'fi-hint err'; }
  else if(val.length > 12) { hint.textContent = '⚠ 12자 이하로 입력해주세요.'; hint.className = 'fi-hint err'; }
  else { hint.textContent = '✅ 사용 가능한 비밀번호입니다.'; hint.className = 'fi-hint ok'; }
  checkPw2();
}

function checkPw2() {
  var pw1 = document.getElementById('inp-pw').value;
  var pw2 = document.getElementById('inp-pw2').value;
  var hint = document.getElementById('pw2-hint');
  if(!pw2) { hint.textContent = '비밀번호를 다시 한 번 입력해주세요.'; hint.className = 'fi-hint'; }
  else if(pw1 !== pw2) { hint.textContent = '⚠ 비밀번호가 일치하지 않습니다.'; hint.className = 'fi-hint err'; }
  else { hint.textContent = '✅ 비밀번호가 일치합니다.'; hint.className = 'fi-hint ok'; }
}

function togglePw(inputId, eyeId) {
  var inp = document.getElementById(inputId);
  var eye = document.getElementById(eyeId);
  if(inp.type === 'password') { inp.type = 'text'; eye.textContent = '🙈'; }
  else { inp.type = 'password'; eye.textContent = '👁'; }
}

function setEmailDomain(sel) {
  if(sel.value) document.getElementById('email-domain').value = sel.value;
}

function doJoin() {
  var id = document.getElementById('inp-id').value;
  var pw = document.getElementById('inp-pw').value;
  var pw2 = document.getElementById('inp-pw2').value;
  if(!id || id.length < 4) { alert('아이디를 입력해주세요 (4자 이상).'); return; }
  if(!pw || pw.length < 4) { alert('비밀번호를 입력해주세요 (4자 이상).'); return; }
  if(pw !== pw2) { alert('비밀번호가 일치하지 않습니다.'); return; }

  document.getElementById('ci-id').textContent = id;
  document.getElementById('ci-type').textContent = currentMemberType === 'biz' ? '🏢 기업회원' : '👤 개인회원';
  var now = new Date();
  document.getElementById('ci-date').textContent =
    now.getFullYear() + '.' + String(now.getMonth()+1).padStart(2,'0') + '.' + String(now.getDate()).padStart(2,'0');
  document.getElementById('complete-msg').innerHTML =
    (currentMemberType === 'biz'
      ? '🏢 기업회원 가입이 완료되었습니다.<br>채용공고 등록 및 다양한 광고 서비스를 이용해보세요!'
      : '👤 개인회원 가입이 완료되었습니다.<br>이력서 등록으로 원하는 일자리를 찾아보세요!');

  showScreen('step3');
  setStep(3);
}
</script>
