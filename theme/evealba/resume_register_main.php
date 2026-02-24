<?php if (!defined('_GNUBOARD_')) exit; $mb_id = isset($member['mb_id']) ? get_text($member['mb_id']) : ''; ?>

    <div class="page-title-bar">
      <h2 class="page-title">📄 이력서 등록</h2>
    </div>

    <!-- 구직자 주의사항 배너 -->
    <div class="caution-bar">
      <span class="caution-icon">⚠️</span>
      <div class="caution-text">
        <div class="caution-title">이력서 등록 시</div>
        <div class="caution-main">구직자 주의사항! 자세히 보기 ▶</div>
      </div>
      <span class="caution-arrow">›</span>
    </div>

    <!-- ===== 1. 기본 신상정보 ===== -->
    <div class="form-card sh-pink">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">👤</span>
        <span class="sec-head-title">기본 신상정보</span>
        <span class="sec-head-sub">기본 회원 정보를 입력해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 사진 등록 -->
        <div style="display:grid;grid-template-columns:140px 1fr;border-bottom:1px solid #fae8f0;">
          <div class="form-label" style="align-self:flex-start;padding-top:15px;border-right:2px solid var(--pale-pink);">사진 등록</div>
          <div style="padding:14px 18px;">
            <div class="photo-upload-area" style="padding:0;gap:16px;">
              <div class="photo-box">
                <div class="photo-preview" id="photoPreview" onclick="triggerFile('photo-file')">
                  <span class="photo-preview-icon">📷</span>
                  <span class="photo-preview-text">클릭하여<br>사진 등록</span>
                </div>
                <input type="file" id="photo-file" accept="image/*" style="display:none" onchange="previewPhoto(this)">
              </div>
              <div class="photo-info">
                <div class="photo-info-title">프로필 사진</div>
                <div class="file-row" style="flex-direction:column;align-items:flex-start;gap:6px;">
                  <div class="file-row">
                    <button class="btn-file" onclick="triggerFile('photo-file')">📷 파일 선택</button>
                    <span class="file-name" id="photo-fn">선택된 파일 없음</span>
                    <button class="btn-file-cancel" onclick="clearPhoto()">✕ 취소</button>
                  </div>
                  <p class="hint">이력서에 등록되는 사진입니다.</p>
                  <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#555;cursor:pointer;">
                    <input type="checkbox" style="accent-color:var(--hot-pink);">
                    체크하고 저장하면 등록한 사진이 삭제됩니다.
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 아이디 -->
        <div class="form-row">
          <div class="form-label">아이디</div>
          <div class="form-cell">
            <input class="fi fi-sm fi-readonly" type="text" value="<?php echo $mb_id; ?>" readonly>
          </div>
        </div>

        <!-- 이름(닉네임) -->
        <div class="form-row">
          <div class="form-label">이름(닉네임) <span class="req">*</span></div>
          <div class="form-cell">
            <input class="fi fi-sm" type="text" placeholder="닉네임을 입력해주세요">
          </div>
        </div>

        <!-- 성별 -->
        <div class="form-row">
          <div class="form-label">성별</div>
          <div class="form-cell">
            <select class="fi-select">
              <option>여성</option>
              <option>남성</option>
            </select>
          </div>
        </div>

        <!-- 생년월일 -->
        <div class="form-row">
          <div class="form-label">생년월일 <span class="req">*</span></div>
          <div class="form-cell" style="gap:5px;">
            <input class="fi fi-xs" type="text" placeholder="YYYY" maxlength="4" style="width:80px;text-align:center;">
            <span style="font-size:13px;color:#888;">년</span>
            <input class="fi" type="text" placeholder="MM" maxlength="2" style="width:56px;text-align:center;">
            <span style="font-size:13px;color:#888;">월</span>
            <input class="fi" type="text" placeholder="DD" maxlength="2" style="width:56px;text-align:center;">
            <span style="font-size:13px;color:#888;">일</span>
          </div>
        </div>

        <!-- 연락방법 -->
        <div class="form-row">
          <div class="form-label">연락방법 <span class="req">*</span></div>
          <div class="form-cell col">
            <div class="radio-group">
              <div class="radio-item"><input type="radio" name="contact" id="ct-phone" checked><label for="ct-phone">핸드폰번호</label></div>
              <div class="radio-item"><input type="radio" name="contact" id="ct-kakao"><label for="ct-kakao">카카오톡</label></div>
              <div class="radio-item"><input type="radio" name="contact" id="ct-line"><label for="ct-line">라인</label></div>
              <div class="radio-item"><input type="radio" name="contact" id="ct-telegram"><label for="ct-telegram">텔레그램</label></div>
            </div>
          </div>
        </div>

        <!-- 핸드폰 번호 -->
        <div class="form-row">
          <div class="form-label">핸드폰 번호</div>
          <div class="form-cell col">
            <input class="fi fi-sm" type="text" placeholder="010-0000-0000">
          </div>
        </div>

        <!-- SNS 아이디 -->
        <div class="form-row">
          <div class="form-label">SNS 아이디</div>
          <div class="form-cell" style="gap:6px;">
            <select class="fi-select">
              <option>라인</option>
              <option>카카오톡</option>
              <option>텔레그램</option>
              <option>위켓</option>
            </select>
            <input class="fi fi-sm" type="text" placeholder="SNS 아이디">
          </div>
        </div>

      </div>
    </div>

    <!-- ===== 2. 기본 정보 ===== -->
    <div class="form-card sh-orange">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📋</span>
        <span class="sec-head-title">기본 정보</span>
        <span class="sec-head-sub">이력서 기본 정보를 입력해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 이력서 제목 -->
        <div class="form-row">
          <div class="form-label">이력서 제목 <span class="req">*</span></div>
          <div class="form-cell" style="position:relative;">
            <input class="fi fi-full" type="text" placeholder="이력서 제목을 입력해주세요" maxlength="40">
            <span style="position:absolute;right:22px;font-size:11px;color:#aaa;">40자 제한</span>
          </div>
        </div>

        <!-- 희망급여 -->
        <div class="form-row">
          <div class="form-label">희망급여</div>
          <div class="form-cell">
            <select class="fi-select">
              <option>급여협의</option>
              <option>시급</option>
              <option>일급</option>
              <option>주급</option>
              <option>월급</option>
            </select>
            <input class="fi fi-xs" type="text" placeholder="금액 입력">
            <span style="font-size:13px;color:#888;">원</span>
          </div>
        </div>

        <!-- 신장 / 체중 -->
        <div class="form-row">
          <div class="form-label">신장 / 체중</div>
          <div class="form-cell">
            <div class="hw-row">
              <input class="fi" type="text" placeholder="신장" style="width:80px;text-align:center;">
              <span class="fi-unit">cm</span>
              <span style="color:#ccc;margin:0 4px;">|</span>
              <input class="fi" type="text" placeholder="체중" style="width:80px;text-align:center;">
              <span class="fi-unit">kg</span>
            </div>
          </div>
        </div>

        <!-- 사이즈 -->
        <div class="form-row">
          <div class="form-label">사이즈</div>
          <div class="form-cell">
            <select class="fi-select">
              <option>선택안함</option>
              <option>44사이즈</option>
              <option>55사이즈</option>
              <option>66사이즈</option>
              <option>77사이즈</option>
              <option>88사이즈</option>
              <option>기타</option>
            </select>
          </div>
        </div>

        <!-- 거주지역 -->
        <div class="form-row">
          <div class="form-label">거주지역</div>
          <div class="form-cell">
            <select class="fi-select">
              <option>지역선택</option>
              <option>서울</option><option>경기</option><option>인천</option>
              <option>부산</option><option>대구</option><option>광주</option>
              <option>대전</option><option>울산</option><option>강원</option>
              <option>충청북도</option><option>충청남도</option><option>전라북도</option>
              <option>전라남도</option><option>경상북도</option><option>경상남도</option>
              <option>제주</option>
            </select>
            <select class="fi-select">
              <option>세부지역선택</option>
              <option>강남구</option><option>서초구</option><option>종로구</option>
              <option>중구</option><option>마포구</option><option>성동구</option>
            </select>
          </div>
        </div>

        <!-- 학력 -->
        <div class="form-row">
          <div class="form-label">학력</div>
          <div class="form-cell">
            <select class="fi-select">
              <option>선택안함</option>
              <option>중학교 졸업</option>
              <option>고등학교 졸업</option>
              <option>대학교 졸업(2~3년)</option>
              <option>대학교 졸업(4년)</option>
              <option>대학원 졸업</option>
            </select>
          </div>
        </div>

      </div>
    </div>

    <!-- ===== 3. 희망분야 ===== -->
    <div class="form-card sh-purple">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">💼</span>
        <span class="sec-head-title">희망분야</span>
        <span class="sec-head-sub">희망하는 업종과 직종을 선택해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="form-row">
          <div class="form-label">희망분야 <span class="req">*</span></div>
          <div class="form-cell">
            <select class="fi-select">
              <option>-1차 직종선택-</option>
              <option>단란주점</option><option>룸살롱</option><option>가라오케</option>
              <option>노래방</option><option>클럽</option><option>바(Bar)</option>
              <option>퍼블릭</option><option>마사지</option><option>풀살롱</option>
            </select>
            <select class="fi-select">
              <option>-2차 직종선택-</option>
              <option>서빙</option><option>도우미</option><option>아가씨</option>
              <option>TC</option><option>미시</option><option>초미시</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== 4. 업무가능지역 ===== -->
    <div class="form-card sh-blue">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📍</span>
        <span class="sec-head-title">업무가능지역</span>
        <span class="sec-head-sub">근무 가능한 지역을 선택해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="form-row">
          <div class="form-label">업무지역 <span class="req">*</span></div>
          <div class="form-cell">
            <select class="fi-select">
              <option>지역선택</option>
              <option>서울</option><option>경기</option><option>인천</option>
              <option>부산</option><option>대구</option><option>광주</option>
              <option>대전</option><option>울산</option><option>강원</option>
              <option>충청북도</option><option>충청남도</option><option>전라북도</option>
              <option>전라남도</option><option>경상북도</option><option>경상남도</option>
              <option>세종</option><option>제주</option>
            </select>
            <select class="fi-select">
              <option>세부지역선택</option>
              <option>강남구</option><option>서초구</option><option>종로구</option>
              <option>중구</option><option>마포구</option><option>성동구</option>
            </select>
          </div>
        </div>
        <!-- 추가 희망지역 -->
        <div class="form-row">
          <div class="form-label">희망지역 추가</div>
          <div class="form-cell">
            <div class="chk-grid">
              <div class="chk-item"><input type="checkbox" id="rg-all"><label for="rg-all">전국 가능</label></div>
              <div class="chk-item"><input type="checkbox" id="rg-travel"><label for="rg-travel">출장 가능</label></div>
              <div class="chk-item"><input type="checkbox" id="rg-abroad"><label for="rg-abroad">해외 가능</label></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== 5. 근무 조건 ===== -->
    <div class="form-card sh-teal">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">⏰</span>
        <span class="sec-head-title">근무 조건</span>
        <span class="sec-head-sub">희망 근무 조건을 입력해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 근무형태 -->
        <div class="form-row">
          <div class="form-label">근무형태</div>
          <div class="form-cell">
            <div class="radio-group">
              <div class="radio-item"><input type="radio" name="work-type" id="wt-full" checked><label for="wt-full">정규직</label></div>
              <div class="radio-item"><input type="radio" name="work-type" id="wt-part"><label for="wt-part">파트타임</label></div>
              <div class="radio-item"><input type="radio" name="work-type" id="wt-week"><label for="wt-week">주말알바</label></div>
              <div class="radio-item"><input type="radio" name="work-type" id="wt-side"><label for="wt-side">투잡</label></div>
              <div class="radio-item"><input type="radio" name="work-type" id="wt-any"><label for="wt-any">무관</label></div>
            </div>
          </div>
        </div>

        <!-- 근무요일 -->
        <div class="form-row">
          <div class="form-label">근무요일</div>
          <div class="form-cell">
            <div class="chk-grid" style="grid-template-columns:repeat(7,auto);gap:8px;">
              <div class="chk-item"><input type="checkbox" id="day-mon"><label for="day-mon">월</label></div>
              <div class="chk-item"><input type="checkbox" id="day-tue"><label for="day-tue">화</label></div>
              <div class="chk-item"><input type="checkbox" id="day-wed"><label for="day-wed">수</label></div>
              <div class="chk-item"><input type="checkbox" id="day-thu"><label for="day-thu">목</label></div>
              <div class="chk-item"><input type="checkbox" id="day-fri"><label for="day-fri">금</label></div>
              <div class="chk-item"><input type="checkbox" id="day-sat"><label for="day-sat">토</label></div>
              <div class="chk-item"><input type="checkbox" id="day-sun"><label for="day-sun">일</label></div>
            </div>
          </div>
        </div>

        <!-- 근무시간 -->
        <div class="form-row">
          <div class="form-label">근무시간</div>
          <div class="form-cell" style="gap:6px;">
            <select class="fi-select"><option>무관</option><option>주간</option><option>야간</option><option>새벽</option></select>
            <span style="font-size:13px;color:#888;">시작</span>
            <input class="fi" type="text" placeholder="00:00" style="width:80px;text-align:center;">
            <span style="font-size:13px;color:#888;">~</span>
            <input class="fi" type="text" placeholder="00:00" style="width:80px;text-align:center;">
          </div>
        </div>

      </div>
    </div>

    <!-- ===== 6. 경력사항 ===== -->
    <div class="form-card sh-dark">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📚</span>
        <span class="sec-head-title">경력사항</span>
        <span class="sec-head-sub">이전 근무 경험을 입력해주세요 (선택)</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 경력구분 -->
        <div class="form-row">
          <div class="form-label">경력구분</div>
          <div class="form-cell">
            <div class="radio-group">
              <div class="radio-item"><input type="radio" name="career-yn" id="cy-new" checked><label for="cy-new">신입</label></div>
              <div class="radio-item"><input type="radio" name="career-yn" id="cy-exp"><label for="cy-exp">경력</label></div>
              <div class="radio-item"><input type="radio" name="career-yn" id="cy-any"><label for="cy-any">신입/경력</label></div>
            </div>
          </div>
        </div>

        <!-- 경력 테이블 -->
        <div style="padding:14px 18px 0;">
          <div style="overflow-x:auto;">
            <table class="career-table" id="careerTable" style="min-width:580px;">
              <thead>
                <tr>
                  <th style="text-align:left;padding-left:14px;width:200px;">업소명</th>
                  <th style="width:100px;">업종</th>
                  <th style="width:100px;">근무기간</th>
                  <th style="width:80px;">급여(일)</th>
                  <th style="width:60px;">삭제</th>
                </tr>
              </thead>
              <tbody id="careerBody">
                <tr>
                  <td><input type="text" placeholder="업소명" style="width:100%;"></td>
                  <td><select style="width:100%;"><option>선택</option><option>룸살롱</option><option>퍼블릭</option><option>마사지</option><option>바(Bar)</option><option>기타</option></select></td>
                  <td><input type="text" placeholder="예) 6개월" style="width:100%;"></td>
                  <td><input type="text" placeholder="금액" style="width:100%;"></td>
                  <td style="text-align:center;"><button class="btn-row-del" onclick="delCareerRow(this)">삭제</button></td>
                </tr>
              </tbody>
            </table>
          </div>
          <button class="btn-career-add" onclick="addCareerRow()" style="margin:10px 0 0;">➕ 경력 추가</button>
        </div>
        <div style="height:14px;"></div>

      </div>
    </div>

    <!-- ===== 7. 편의사항 ===== -->
    <div class="form-card sh-green">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">✅</span>
        <span class="sec-head-title">편의사항</span>
        <span class="sec-head-sub">* 리스트에 출력되는 사항입니다. 2개 이상 선택하면 검색 시 유리합니다.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="amenity-grid">
          <div class="chk-item"><input type="checkbox" id="am-1"><label for="am-1">선불가능</label></div>
          <div class="chk-item"><input type="checkbox" id="am-2"><label for="am-2">순번확실</label></div>
          <div class="chk-item"><input type="checkbox" id="am-3"><label for="am-3">원룸제공</label></div>
          <div class="chk-item"><input type="checkbox" id="am-4"><label for="am-4">만근비지원</label></div>
          <div class="chk-item"><input type="checkbox" id="am-5"><label for="am-5">성형지원</label></div>
          <div class="chk-item"><input type="checkbox" id="am-6"><label for="am-6">출퇴근지원</label></div>
          <div class="chk-item"><input type="checkbox" id="am-7"><label for="am-7">식사제공</label></div>
          <div class="chk-item"><input type="checkbox" id="am-8"><label for="am-8">팁별도</label></div>
          <div class="chk-item"><input type="checkbox" id="am-9"><label for="am-9">인센티브</label></div>
          <div class="chk-item"><input type="checkbox" id="am-10"><label for="am-10">홀복지원</label></div>
          <div class="chk-item"><input type="checkbox" id="am-11"><label for="am-11">갯수보장</label></div>
          <div class="chk-item"><input type="checkbox" id="am-12"><label for="am-12">지명우대</label></div>
          <div class="chk-item"><input type="checkbox" id="am-13"><label for="am-13">초이스없음</label></div>
          <div class="chk-item"><input type="checkbox" id="am-14"><label for="am-14">해외여행지원</label></div>
          <div class="chk-item"><input type="checkbox" id="am-15"><label for="am-15">뒷방없음</label></div>
          <div class="chk-item"><input type="checkbox" id="am-16"><label for="am-16">따당가능</label></div>
          <div class="chk-item"><input type="checkbox" id="am-17"><label for="am-17">푸쉬가능</label></div>
          <div class="chk-item"><input type="checkbox" id="am-18"><label for="am-18">밀방없음</label></div>
          <div class="chk-item"><input type="checkbox" id="am-19"><label for="am-19">칼퇴보장</label></div>
          <div class="chk-item"><input type="checkbox" id="am-20"><label for="am-20">텃새없음</label></div>
          <div class="chk-item"><input type="checkbox" id="am-21"><label for="am-21">숙식제공</label></div>
        </div>
      </div>
    </div>

    <!-- ===== 8. 키워드 ===== -->
    <div class="form-card sh-indigo">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">🏷️</span>
        <span class="sec-head-title">키워드</span>
        <span class="sec-head-sub">* 해당하는 키워드를 선택하면 검색 시 유리합니다.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="amenity-grid">
          <div class="chk-item"><input type="checkbox" id="kw-1"><label for="kw-1">신규업소</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-2"><label for="kw-2">초보가능</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-3"><label for="kw-3">경력우대</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-4"><label for="kw-4">주말알바</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-5"><label for="kw-5">투잡알바</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-6"><label for="kw-6">당일지급</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-7"><label for="kw-7">생리휴무</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-8"><label for="kw-8">룸싸롱</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-9"><label for="kw-9">주점</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-10"><label for="kw-10">바</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-11"><label for="kw-11">요정</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-12"><label for="kw-12">다방</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-13"><label for="kw-13">마사지</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-14"><label for="kw-14">아가씨</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-15"><label for="kw-15">초미씨</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-16"><label for="kw-16">미씨</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-17"><label for="kw-17">TC</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-18"><label for="kw-18">44사이즈우대</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-19"><label for="kw-19">박스환영</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-20"><label for="kw-20">장기근무</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-21"><label for="kw-21">타지역우대</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-22"><label for="kw-22">에이스우대</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-23"><label for="kw-23">업소</label></div>
          <div class="chk-item"><input type="checkbox" id="kw-24"><label for="kw-24">기타</label></div>
        </div>
        <div class="form-row" style="border-top:2px solid var(--pale-pink);">
          <div class="form-label" style="font-size:12px;">키워드 직접입력</div>
          <div class="form-cell"><input class="fi fi-full" type="text" placeholder="직접 키워드를 입력하세요 (쉼표로 구분)"></div>
        </div>
      </div>
    </div>

    <!-- ===== 9. 자기소개 ===== -->
    <div class="form-card sh-rose">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">💌</span>
        <span class="sec-head-title">자기소개</span>
        <span class="sec-head-sub">* 필수 입력 사항입니다.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="form-row" style="min-height:160px;">
          <div class="form-label">자기소개 <span class="req">*</span></div>
          <div class="form-cell col">
            <textarea class="fi fi-full" style="min-height:140px;" placeholder="자신을 어필할 수 있는 내용을 자유롭게 작성해주세요.&#10;예) 성격, 장점, 희망 업소 유형, 특이사항 등"></textarea>
            <p class="hint">* 2000자 이내로 작성해주세요.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== 10. MBTI (AI 매칭) ===== -->
    <div class="form-card" style="border:2px solid var(--pale-pink);">
      <div class="sec-head open" style="background:linear-gradient(135deg,#6A1B9A,#AB47BC);" onclick="toggleSec(this)">
        <span class="sec-head-icon">🧠</span>
        <span class="sec-head-title" style="color:#fff;">MBTI 유형 (AI 근접 매칭)</span>
        <span class="sec-head-sub" style="color:rgba(255,255,255,.8);">선택 입력 · 기업회원 인재정보 열람 시 노출 · AI 매칭에 활용됩니다</span>
        <span class="sec-chevron" style="color:#fff;">▼</span>
      </div>
      <div class="sec-body">
        <div style="padding:12px 18px 0;background:linear-gradient(135deg,#f9f5ff,#fdf0ff);">
          <div style="background:rgba(106,27,154,.08);border:1.5px solid #CE93D8;border-radius:10px;padding:10px 14px;font-size:12px;color:#6A1B9A;line-height:1.7;">
            🤖 <strong>AI 근접 매칭</strong>이란? MBTI 그룹(NT/NF/SJ/SP) 단위로 업소의 니즈와 구직자의 성향을 분석하여 최적의 매칭을 도와주는 서비스입니다.
          </div>
        </div>

        <!-- NT 분석가형 -->
        <div class="mbti-group mbti-group-nt">
          <div class="mbti-group-title">🔵 NT — 분석가형</div>
          <div class="mbti-grid">
            <label class="mbti-card" onclick="selectMbti(this,'INTJ')">
              <input type="radio" name="mbti" value="INTJ">
              <div class="mbti-card-top"><span class="mbti-type">INTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">고객 성향 빠른 분석, 장기 단골 전략 설계에 강함</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'INTP')">
              <input type="radio" name="mbti" value="INTP">
              <div class="mbti-card-top"><span class="mbti-type">INTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">대화 주제 확장력 뛰어나고 지적 매력 어필 가능</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ENTJ')">
              <input type="radio" name="mbti" value="ENTJ">
              <div class="mbti-card-top"><span class="mbti-type">ENTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">목표 매출 설정·관리 능력 우수, 자기 브랜딩 강함</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ENTP')">
              <input type="radio" name="mbti" value="ENTP">
              <div class="mbti-card-top"><span class="mbti-type">ENTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">말 센스 좋고 토론·농담으로 분위기 반전 능력 탁월</div>
            </label>
          </div>
        </div>

        <!-- NF 외교관형 -->
        <div class="mbti-group mbti-group-nf" style="border-top:1.5px solid var(--pale-pink);">
          <div class="mbti-group-title">🟢 NF — 외교관형</div>
          <div class="mbti-grid">
            <label class="mbti-card" onclick="selectMbti(this,'INFJ')">
              <input type="radio" name="mbti" value="INFJ">
              <div class="mbti-card-top"><span class="mbti-type">INFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">깊은 공감 능력, 감정 상담형 고객에게 매우 강함</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'INFP')">
              <input type="radio" name="mbti" value="INFP">
              <div class="mbti-card-top"><span class="mbti-type">INFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">순수·감성 매력, 특정 고객층에게 강한 팬층 형성</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ENFJ')">
              <input type="radio" name="mbti" value="ENFJ">
              <div class="mbti-card-top"><span class="mbti-type">ENFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">고객을 특별하게 만들어주는 능력, VIP 관리 최강</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ENFP')">
              <input type="radio" name="mbti" value="ENFP">
              <div class="mbti-card-top"><span class="mbti-type">ENFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">밝은 에너지와 리액션, 첫인상 흡입력 매우 높음</div>
            </label>
          </div>
        </div>

        <!-- SJ 관리자형 -->
        <div class="mbti-group mbti-group-sj" style="border-top:1.5px solid var(--pale-pink);">
          <div class="mbti-group-title">🟡 SJ — 관리자형</div>
          <div class="mbti-grid">
            <label class="mbti-card" onclick="selectMbti(this,'ISTJ')">
              <input type="radio" name="mbti" value="ISTJ">
              <div class="mbti-card-top"><span class="mbti-type">ISTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">시간·약속 철저, 안정적인 신뢰 구축형</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ISFJ')">
              <input type="radio" name="mbti" value="ISFJ">
              <div class="mbti-card-top"><span class="mbti-type">ISFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">섬세한 배려, 단골 관리 지속력 강함</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ESTJ')">
              <input type="radio" name="mbti" value="ESTJ">
              <div class="mbti-card-top"><span class="mbti-type">ESTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">실적 관리·목표 달성 집요함</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ESFJ')">
              <input type="radio" name="mbti" value="ESFJ">
              <div class="mbti-card-top"><span class="mbti-type">ESFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">친화력 최고 수준, 관계 유지 능력 뛰어남</div>
            </label>
          </div>
        </div>

        <!-- SP 탐험가형 -->
        <div class="mbti-group mbti-group-sp" style="border-top:1.5px solid var(--pale-pink);">
          <div class="mbti-group-title">🔴 SP — 탐험가형</div>
          <div class="mbti-grid">
            <label class="mbti-card" onclick="selectMbti(this,'ISTP')">
              <input type="radio" name="mbti" value="ISTP">
              <div class="mbti-card-top"><span class="mbti-type">ISTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">상황 판단 빠름, 감정 휘둘림 적음</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ISFP')">
              <input type="radio" name="mbti" value="ISFP">
              <div class="mbti-card-top"><span class="mbti-type">ISFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">자연스러운 매력, 부드러운 분위기 형성</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ESTP')">
              <input type="radio" name="mbti" value="ESTP">
              <div class="mbti-card-top"><span class="mbti-type">ESTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">밀당·텐션 조절 능숙, 현장 적응력 강함</div>
            </label>
            <label class="mbti-card" onclick="selectMbti(this,'ESFP')">
              <input type="radio" name="mbti" value="ESFP">
              <div class="mbti-card-top"><span class="mbti-type">ESFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">분위기 메이커, 고객 몰입도 상승 능력 탁월</div>
            </label>
          </div>
        </div>

        <div style="padding:0 18px 14px;">
          <div style="background:#f9f5ff;border:1.5px dashed #CE93D8;border-radius:8px;padding:10px 14px;font-size:11px;color:#7B1FA2;line-height:1.8;">
            💡 MBTI를 선택하지 않아도 이력서 등록이 가능합니다. 선택 시 AI 매칭 정확도가 높아집니다.
          </div>
        </div>

      </div>
    </div>

    <!-- ===== 11. 약관 동의 ===== -->
    <div class="form-card">
      <div class="sec-head open" style="background:linear-gradient(135deg,#37474F,#546E7A);" onclick="toggleSec(this)">
        <span class="sec-head-icon">📜</span>
        <span class="sec-head-title" style="color:#fff;">약관 동의</span>
        <span class="sec-head-sub" style="color:rgba(255,255,255,.8);">이력서 등록을 위한 약관에 동의해주세요</span>
        <span class="sec-chevron" style="color:#fff;">▼</span>
      </div>
      <div class="sec-body">
        <div class="terms-section">
          <div class="terms-all-check" onclick="toggleAllTerms(document.getElementById('agree-all'))">
            <input type="checkbox" id="agree-all" onchange="toggleAllTerms(this)">
            <label for="agree-all">전체 동의하기</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term1">
            <label for="term1">[필수] 이브알바 이력서 등록 이용약관에 동의합니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term2">
            <label for="term2">[필수] 개인정보 수집 및 이용에 동의합니다. 수집 정보는 구인구직 목적으로만 활용됩니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term3">
            <label for="term3">[필수] 만 18세 미만은 이력서 등록이 불가합니다. 본인은 만 18세 이상임을 확인합니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term4">
            <label for="term4">[선택] AI 매칭 서비스를 위한 MBTI 정보 활용에 동의합니다. (거부 시에도 이력서 등록 가능)</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term5">
            <label for="term5">허위 이력서 작성 시 서비스 이용이 제한될 수 있으며, 그에 따른 책임은 본인에게 있습니다.</label>
          </div>
        </div>

        <!-- 주의사항 배너 -->
        <div class="notice-banner" style="margin:0 18px 14px;">
          <div class="nb-title">⚠️ 이력서 등록 전 꼭 확인하세요!</div>
          <div class="nb-body">
            · 개인정보(주민번호, 계좌번호 등)는 절대 기재하지 마세요.<br>
            · 허위 정보 작성 시 즉시 삭제 및 이용 제한 처리됩니다.<br>
            · 이력서는 기업회원(업소)에게만 공개됩니다.<br>
            · 문의: 고객센터 <strong style="color:var(--hot-pink);">1588-0000</strong>
          </div>
        </div>

      </div>
    </div>

    <!-- 등록 버튼 -->
    <div class="submit-btn-wrap">
      <button class="btn-preview" type="button" onclick="alert('이력서 미리보기')">👁 미리보기</button>
      <button class="btn-submit" type="button" onclick="submitResume()">📄 이력서 등록</button>
    </div>

<script>
/* 섹션 열기/닫기 */
function toggleSec(head) {
  head.classList.toggle('open');
  var body = head.nextElementSibling;
  if(body) body.classList.toggle('collapsed');
}

/* 파일 선택 */
function triggerFile(id) { document.getElementById(id).click(); }
function setFileName(input, spanId) {
  var sp = document.getElementById(spanId);
  if(input.files && input.files[0]){ sp.textContent = input.files[0].name; sp.style.color='#333'; }
}
function clearFile(inputId, spanId) {
  document.getElementById(inputId).value = '';
  var sp = document.getElementById(spanId);
  sp.textContent = '선택된 파일 없음'; sp.style.color='#aaa';
}

/* 사진 미리보기 */
function previewPhoto(input) {
  var sp = document.getElementById('photo-fn');
  if(!input.files || !input.files[0]) return;
  sp.textContent = input.files[0].name; sp.style.color='#333';
  var reader = new FileReader();
  reader.onload = function(e) {
    var prev = document.getElementById('photoPreview');
    prev.innerHTML = '<img src="'+e.target.result+'" alt="프로필사진">';
  };
  reader.readAsDataURL(input.files[0]);
}
function clearPhoto() {
  document.getElementById('photo-file').value = '';
  document.getElementById('photo-fn').textContent = '선택된 파일 없음';
  document.getElementById('photo-fn').style.color = '#aaa';
  document.getElementById('photoPreview').innerHTML =
    '<span class="photo-preview-icon">📷</span><span class="photo-preview-text">클릭하여<br>사진 등록</span>';
}

/* 경력 행 추가/삭제 */
function addCareerRow() {
  var tbody = document.getElementById('careerBody');
  var tr = document.createElement('tr');
  tr.innerHTML =
    '<td><input type="text" placeholder="업소명" style="width:100%;"></td>'
    +'<td><select style="width:100%;"><option>선택</option><option>룸살롱</option><option>퍼블릭</option><option>마사지</option><option>바(Bar)</option><option>기타</option></select></td>'
    +'<td><input type="text" placeholder="예) 6개월" style="width:100%;"></td>'
    +'<td><input type="text" placeholder="금액" style="width:100%;"></td>'
    +'<td style="text-align:center;"><button class="btn-row-del" onclick="delCareerRow(this)">삭제</button></td>';
  tbody.appendChild(tr);
}
function delCareerRow(btn) {
  var row = btn.closest('tr');
  var tbody = document.getElementById('careerBody');
  if(tbody.rows.length > 1) row.remove();
  else alert('최소 1개 행은 필요합니다.');
}

/* MBTI 선택 */
function selectMbti(card, type) {
  document.querySelectorAll('.mbti-card').forEach(function(c){ c.classList.remove('selected'); });
  card.classList.add('selected');
}
// 클릭 이벤트 중복 방지 (label onclick + radio change)
document.querySelectorAll('.mbti-card input[type=radio]').forEach(function(radio){
  radio.addEventListener('change', function(){
    document.querySelectorAll('.mbti-card').forEach(function(c){ c.classList.remove('selected'); });
    radio.closest('.mbti-card').classList.add('selected');
  });
});

/* 전체 약관 동의 */
function toggleAllTerms(masterChk) {
  document.querySelectorAll('.term-chk').forEach(function(c){ c.checked = masterChk.checked; });
}
document.querySelectorAll('.term-chk').forEach(function(c){
  c.addEventListener('change', function(){
    var all = document.querySelectorAll('.term-chk');
    var checked = document.querySelectorAll('.term-chk:checked');
    document.getElementById('agree-all').checked = (all.length === checked.length);
  });
});

/* 이력서 등록 */
function submitResume() {
  var required = [
    {id:'term1',msg:'이용약관에 동의해주세요.'},
    {id:'term2',msg:'개인정보 수집 동의가 필요합니다.'},
    {id:'term3',msg:'연령 확인에 동의해주세요.'},
  ];
  for(var i=0;i<required.length;i++){
    if(!document.getElementById(required[i].id).checked){
      alert(required[i].msg); return;
    }
  }
  alert('이력서가 성공적으로 등록되었습니다! 🎉');
}
</script>
