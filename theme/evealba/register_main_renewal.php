<?php if (!defined('_GNUBOARD_')) exit;
$_reg_base = rtrim(G5_URL, '/');
$_reg_login = G5_BBS_URL . '/login.php';
?>
<div class="breadcrumb">
  <a href="<?php echo G5_URL; ?>">🏠 메인</a>
  <span class="bc-sep">›</span>
  <span class="bc-cur" id="regBreadcrumb">📝 회원가입</span>
</div>

<div class="reg-inner">

  <div class="step-bar">
    <div class="step-item">
      <div class="step-circle active" id="sc1">1</div>
      <div class="step-label active" id="sl1">약관동의</div>
    </div>
    <div class="step-line" id="line1"></div>
    <div class="step-item">
      <div class="step-circle" id="sc2">2</div>
      <div class="step-label" id="sl2">정보입력</div>
    </div>
    <div class="step-line" id="line2"></div>
    <div class="step-item">
      <div class="step-circle" id="sc3">3</div>
      <div class="step-label" id="sl3">가입완료</div>
    </div>
  </div>

  <!-- STEP 1 -->
  <div class="form-section active" id="step1">
    <div class="reg-card">
      <div class="reg-card-head">
        <span class="head-icon">📜</span>
        <span class="head-title">이용약관 동의</span>
      </div>
      <div class="reg-card-body">
        <div class="agree-all" id="agreeAllWrap">
          <div class="custom-check" id="chkAll"></div>
          <div>
            <div class="agree-label">전체 약관에 동의합니다</div>
            <div class="agree-desc">이용약관, 개인정보 보호정책 전체 동의</div>
          </div>
        </div>
        <div class="agree-item" data-chk="chk1" data-terms="terms1" data-arr="arr1">
          <div class="custom-check" id="chk1"></div>
          <div class="agree-item-label"><span class="agree-required">필수</span>이용약관 동의</div>
          <span class="agree-arrow" id="arr1">›</span>
        </div>
        <div class="terms-box" id="terms1">
          <strong>제 1조 (목적)</strong><br>
          본 약관은 이브알바가 운영하는 서비스를 제공함에 있어 이용자와 이브알바의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.<br><br>
          <strong>제 2조 (용어의 정의)</strong><br>
          ① '서비스'라 함은 이브알바를 통해 제공되는 구인구직 정보 및 관련 부대 서비스를 말합니다.<br>
          ② '개인회원'이라 함은 서비스를 이용하기 위해 이용계약을 체결하여 ID를 부여받은 개인을 말합니다.<br>
          ③ '기업회원'이라 함은 이용계약을 체결한 법인 또는 개인사업자를 말합니다.
        </div>
        <div class="agree-item" data-chk="chk2" data-terms="terms2" data-arr="arr2">
          <div class="custom-check" id="chk2"></div>
          <div class="agree-item-label"><span class="agree-required">필수</span>개인정보 보호정책 동의</div>
          <span class="agree-arrow" id="arr2">›</span>
        </div>
        <div class="terms-box" id="terms2">
          이브알바는 이용자들의 개인정보 보호를 매우 중요시하며, 제공한 개인정보가 보호받을 수 있도록 최선을 다하고 있습니다.<br><br>
          <strong>수집하는 개인정보 항목</strong><br>
          · 필수: 이름, 생년월일, 성별, 로그인ID, 비밀번호, 닉네임, 이메일, 연락처<br>
          · 선택: 프로필 사진
        </div>
        <div class="agree-item" data-chk="chk3">
          <div class="custom-check" id="chk3"></div>
          <div class="agree-item-label"><span class="agree-optional">선택</span>SMS 수신 동의</div>
        </div>
      </div>
    </div>

    <div class="reg-card">
      <div class="reg-card-head">
        <span class="head-icon">📱</span>
        <span class="head-title">본인 실명인증</span>
        <span class="head-sub">필수</span>
      </div>
      <div class="reg-card-body">
        <div class="auth-box">
          <div class="auth-desc">
            휴대폰 실명인증을 통해 본인 확인을 진행합니다.<br>
            <strong>고객님의 정보는 인증 외 목적으로 저장되지 않습니다.</strong>
          </div>
          <button type="button" class="btn-auth" id="btnAuth">📲 휴대폰 실명인증 하기</button>
          <div class="auth-done" id="authDone">✅ 본인인증이 완료되었습니다</div>
        </div>
      </div>
    </div>

    <div class="reg-card">
      <div class="reg-card-head">
        <span class="head-icon">👥</span>
        <span class="head-title">회원 유형 선택</span>
      </div>
      <div class="reg-card-body">
        <div class="type-select">
          <div class="type-btn selected" id="typePersonal" data-type="personal">
            <div class="type-icon">👤</div>
            <div class="type-name">개인회원</div>
            <div class="type-desc">이력서 등록 가능</div>
          </div>
          <div class="type-btn" id="typeBiz" data-type="biz">
            <div class="type-icon">🏢</div>
            <div class="type-name">기업회원</div>
            <div class="type-desc">채용공고 등록 가능</div>
          </div>
        </div>
      </div>
    </div>

    <button type="button" class="step-next-btn" id="btnGoStep2">다음 단계로 →</button>
    <div class="login-link">이미 회원이신가요? <a href="<?php echo $_reg_login; ?>">로그인하기</a></div>
  </div>

  <!-- STEP 2 -->
  <div class="form-section" id="step2">
    <div class="reg-card">
      <div class="reg-card-head">
        <span class="head-icon">✏️</span>
        <span class="head-title">기본 정보 입력</span>
        <span class="head-sub" id="memberTypeLabel">개인회원</span>
      </div>
      <div class="reg-card-body">
        <div class="form-group">
          <div class="form-label">아이디 <span class="req">*</span></div>
          <div class="form-input-wrap">
            <input type="text" class="form-input" id="inputId" placeholder="영문, 숫자 4~10자" maxlength="10">
            <button type="button" class="btn-check" id="btnCheckId">중복확인</button>
          </div>
          <div class="form-hint" id="hintId">영문, 숫자 4~10자로 입력해주세요.</div>
        </div>
        <div class="form-group">
          <div class="form-label">비밀번호 <span class="req">*</span></div>
          <div class="pw-wrap">
            <input type="password" class="form-input" id="inputPw" placeholder="영문 + 특수문자 조합, 4~12자">
            <span class="pw-toggle" data-target="inputPw">👁</span>
          </div>
          <div class="form-hint" id="hintPwRule">영문 + 특수문자 조합, 4자 이상 12자 이하</div>
        </div>
        <div class="form-group">
          <div class="form-label">비밀번호 확인 <span class="req">*</span></div>
          <div class="pw-wrap">
            <input type="password" class="form-input" id="inputPwConfirm" placeholder="비밀번호를 다시 입력해주세요">
            <span class="pw-toggle" data-target="inputPwConfirm">👁</span>
          </div>
          <div class="form-hint" id="hintPw"></div>
        </div>
        <div class="form-group">
          <div class="form-label">이름 <span class="req">*</span></div>
          <input type="text" class="form-input" id="inputName" placeholder="휴대폰 인증 시 자동 입력">
        </div>
        <div class="form-group">
          <div class="form-label">닉네임 <span class="req">*</span></div>
          <input type="text" class="form-input" id="inputNick" placeholder="한글, 영문, 숫자 포함 8자 이내" maxlength="8">
          <div class="form-hint">커뮤니티 및 프로필에 표시됩니다.</div>
        </div>
        <div class="form-group">
          <div class="form-label">생년월일 <span class="req">*</span></div>
          <div class="birth-row">
            <select class="form-select" id="inp-birth-y">
              <option value="">년도</option>
              <?php for ($y = 2008; $y >= 1950; $y--) echo '<option value="'.$y.'">'.$y.'년</option>'; ?>
            </select>
            <select class="form-select" id="inp-birth-m">
              <option value="">월</option>
              <?php for ($m = 1; $m <= 12; $m++) echo '<option value="'.str_pad($m, 2, '0', STR_PAD_LEFT).'">'.str_pad($m, 2, '0', STR_PAD_LEFT).'월</option>'; ?>
            </select>
            <select class="form-select" id="inp-birth-d">
              <option value="">일</option>
              <?php for ($d = 1; $d <= 31; $d++) echo '<option value="'.str_pad($d, 2, '0', STR_PAD_LEFT).'">'.str_pad($d, 2, '0', STR_PAD_LEFT).'일</option>'; ?>
            </select>
          </div>
          <div class="form-hint">휴대폰 인증 시 자동 입력됩니다.</div>
        </div>
        <div class="form-group">
          <div class="form-label">성별 <span class="req">*</span></div>
          <div class="gender-row">
            <div class="gender-btn" data-sex="M">👨 남자</div>
            <div class="gender-btn" data-sex="F">👩 여자</div>
          </div>
        </div>
        <div class="form-group">
          <div class="form-label">이메일 <span class="req">*</span></div>
          <div class="email-row">
            <input type="text" class="form-input" id="emailId" placeholder="아이디">
            <div class="email-at">@</div>
            <select class="form-select" id="emailDomainSel">
              <option value="gmail.com">gmail.com</option>
              <option value="naver.com">naver.com</option>
              <option value="daum.net">daum.net</option>
              <option value="kakao.com">kakao.com</option>
              <option value="hotmail.com">hotmail.com</option>
              <option value="custom">직접입력</option>
            </select>
          </div>
          <input type="text" class="form-input" id="emailCustom" placeholder="도메인 직접 입력" style="margin-top:8px;display:none;">
        </div>
        <div class="form-group">
          <div class="form-label">핸드폰 <span class="req">*</span></div>
          <input type="tel" class="form-input" id="inputHp" placeholder="010-0000-0000">
          <div class="form-hint">'-' 포함하여 입력해주세요.</div>
        </div>
        <div class="form-group" id="industryGroup" style="display:none;">
          <div class="form-label">업종 <span class="req">*</span></div>
          <div class="industry-grid" id="industryGrid">
            <?php
            $industries = array('룸싸롱','단란주점','가라오케','노래방','클럽','바(Bar)','퍼블릭','마사지','풀살롱','기타');
            foreach ($industries as $ind) {
                echo '<div class="industry-btn" data-value="'.htmlspecialchars($ind).'">'.htmlspecialchars($ind).'</div>';
            }
            ?>
          </div>
        </div>
        <div class="form-group">
          <div class="form-label">추천인 <span class="opt">선택</span></div>
          <input type="text" class="form-input" id="inputReferral" placeholder="추천인 닉네임 (기프티콘 발급 기준)">
        </div>
        <div class="sms-agree" id="smsAgreeWrap">
          <div class="custom-check" id="chkSms"></div>
          <div class="sms-text">
            <strong>SMS 수신에 동의합니다.</strong><br>
            수신 허용 시 새 구인 알림 등 유용한 정보를 받으실 수 있습니다.
          </div>
        </div>
      </div>
    </div>

    <div class="reg-card biz-extra" id="bizExtra">
      <div class="reg-card-head">
        <span class="head-icon">🏢</span>
        <span class="head-title">기업 추가 정보</span>
        <span class="head-sub">사업자 정보</span>
      </div>
      <div class="reg-card-body">
        <div class="form-group">
          <div class="form-label">상호명 <span class="req">*</span></div>
          <input type="text" class="form-input" id="inp-biz-name" placeholder="업소 상호명을 입력해주세요">
        </div>
        <div class="form-group">
          <div class="form-label">사업자등록증 첨부 <span class="req">*</span></div>
          <div class="file-upload-area" id="bizFileArea">
            <div class="upload-icon">📄</div>
            <div class="upload-title">사업자등록증을 첨부해주세요</div>
            <div class="upload-hint">jpg, png, gif, webp / 최대 10MB</div>
            <input type="file" id="bizFile" accept=".jpg,.jpeg,.png,.gif,.webp">
          </div>
          <div class="form-hint" id="fileHint"></div>
        </div>
        <div class="form-group">
          <div class="form-label">사업자번호 <span class="req">*</span></div>
          <input type="text" class="form-input" id="inp-biz-num" maxlength="10" placeholder="숫자 10자리">
        </div>
        <div class="form-group">
          <div class="form-label">대표자 <span class="req">*</span></div>
          <input type="text" class="form-input" id="inp-biz-rep" placeholder="대표자명 입력">
        </div>
        <div class="form-group">
          <div class="form-label">주소 <span class="req">*</span></div>
          <input type="text" class="form-input" id="inp-biz-addr" placeholder="사업장 주소 입력">
        </div>
      </div>
    </div>

    <div class="row-btns">
      <button type="button" class="btn-back-step" id="btnBackStep1">←</button>
      <button type="button" class="step-next-btn" style="flex:1;" id="btnSubmit">가입 완료하기 🌸</button>
    </div>
  </div>

  <!-- STEP 3 -->
  <div class="complete-section" id="step3">
    <div class="complete-icon">🌸</div>
    <div class="complete-title">환영합니다!<br><span>이브알바</span> 회원이 되셨습니다</div>
    <div class="complete-desc" id="completeMsg">
      회원가입이 완료되었습니다.<br>
      이제 이브알바의 모든 서비스를 이용하실 수 있습니다.
    </div>
    <div class="complete-btns">
      <button type="button" class="btn-complete-main" onclick="location.href='<?php echo G5_URL; ?>'">🏠 채용정보 보러가기</button>
      <button type="button" class="btn-complete-sub" onclick="location.href='<?php echo $_reg_login; ?>'">로그인하기</button>
    </div>
  </div>

</div>

<?php include G5_THEME_PATH.'/inc/renewal_footer_in_main.php'; ?>

<script>
window.EVE_REGISTER = {
  baseUrl: <?php echo json_encode($_reg_base); ?>,
  loginUrl: <?php echo json_encode($_reg_login); ?>
};
</script>
<script src="<?php echo G5_THEME_URL; ?>/js/evealba_register.js?ver=<?php echo @filemtime(G5_THEME_PATH.'/js/evealba_register.js'); ?>"></script>
