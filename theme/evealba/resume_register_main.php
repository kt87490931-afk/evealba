<?php if (!defined('_GNUBOARD_')) exit;
$mb_id = isset($member['mb_id']) ? get_text($member['mb_id']) : '';
$mb_nick = isset($member['mb_nick']) ? get_text($member['mb_nick']) : '';
?>

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
        <div class="resume-photo-row form-row-photo-mobile">
          <div class="form-label resume-photo-label">사진 등록</div>
          <div class="form-cell form-cell-photo">
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

        <!-- 닉네임 (가입 시 고정, 수정 불가) -->
        <div class="form-row">
          <div class="form-label">닉네임 <span class="req">*</span></div>
          <div class="form-cell">
            <input class="fi fi-sm fi-readonly" type="text" id="resume_nick" value="<?php echo htmlspecialchars($mb_nick); ?>" readonly>
          </div>
        </div>

        <!-- 성별 -->
        <div class="form-row">
          <div class="form-label">성별</div>
          <div class="form-cell">
            <input class="fi fi-sm fi-readonly" type="text" value="여성" readonly id="resume_gender">
          </div>
        </div>

        <!-- 나이 -->
        <div class="form-row">
          <div class="form-label">나이 <span class="req">*</span></div>
          <div class="form-cell">
            <select class="fi fi-select fi-sm" id="resume_age" title="나이 선택">
              <option value="">선택</option>
              <?php for ($a = 20; $a <= 60; $a++) { ?>
              <option value="<?php echo $a; ?>"><?php echo $a; ?>세</option>
              <?php } ?>
            </select>
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
            <input class="fi fi-sm" type="text" placeholder="010-0000-0000" id="resume_phone">
          </div>
        </div>

        <!-- SNS 아이디 -->
        <div class="form-row">
          <div class="form-label">SNS 아이디</div>
          <div class="form-cell" style="gap:6px;">
            <select class="fi-select" id="resume_sns_type">
              <option>라인</option>
              <option>카카오톡</option>
              <option>텔레그램</option>
            </select>
            <input class="fi fi-sm" type="text" placeholder="SNS 아이디" id="resume_sns_id">
          </div>
        </div>

      </div>
    </div>

    <!-- ===== 2. 희망분야 ===== -->
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
            <select class="fi-select" id="resume_job1">
              <option>-1차 직종선택-</option>
              <option>단란주점</option><option>룸살롱</option><option>가라오케</option>
              <option>노래방</option><option>클럽</option><option>바(Bar)</option>
              <option>퍼블릭</option><option>마사지</option>
            </select>
            <select class="fi-select" id="resume_job2">
              <option>-2차 직종선택-</option>
              <option>서빙</option><option>도우미</option><option>아가씨</option>
              <option>TC</option><option>미시</option><option>초미시</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== 3. 기본 정보 ===== -->
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
            <input class="fi fi-full" type="text" placeholder="이력서 제목을 입력해주세요" maxlength="40" id="resume_title">
            <span style="position:absolute;right:22px;font-size:11px;color:#aaa;">40자 제한</span>
          </div>
        </div>

        <!-- 희망급여 -->
        <div class="form-row">
          <div class="form-label">희망급여</div>
          <div class="form-cell">
            <select class="fi-select" id="resume_salary_type">
              <option>급여협의</option>
              <option>시급</option>
              <option>일급</option>
              <option>주급</option>
              <option>월급</option>
            </select>
            <input class="fi fi-xs" type="text" placeholder="금액 입력" id="resume_salary_amt">
            <span style="font-size:13px;color:#888;">원</span>
            <button type="button" class="btn-salary-guide" onclick="openSalaryGuide()">💰 급여 기준표</button>
            <div class="salary-warn" id="salary-warn-resume" style="display:none;color:#FF1B6B;font-size:11px;font-weight:700;margin-top:4px;"></div>
          </div>
        </div>

        <!-- 신장 / 체중 -->
        <div class="form-row">
          <div class="form-label">신장 / 체중</div>
          <div class="form-cell">
            <div class="hw-row">
              <input class="fi" type="text" placeholder="신장" style="width:80px;text-align:center;" id="resume_height">
              <span class="fi-unit">cm</span>
              <span style="color:#ccc;margin:0 4px;">|</span>
              <input class="fi" type="text" placeholder="체중" style="width:80px;text-align:center;" id="resume_weight">
              <span class="fi-unit">kg</span>
            </div>
          </div>
        </div>

        <!-- 사이즈 -->
        <div class="form-row">
          <div class="form-label">사이즈</div>
          <div class="form-cell">
            <select class="fi-select" id="resume_size">
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

        <!-- 거주지역 (jobs.php 채용정보검색과 동일) -->
        <div class="form-row">
          <div class="form-label">거주지역</div>
          <div class="form-cell">
            <select class="fi-select" id="resume_region">
              <option value="">지역선택</option>
              <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
              <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
              <?php } ?>
            </select>
            <select class="fi-select" id="resume_region_detail">
              <option value="">세부지역선택</option>
              <?php foreach ((isset($ev_region_details) ? $ev_region_details : []) as $rd) { ?>
              <option value="<?php echo (int)$rd['erd_id']; ?>" data-er-id="<?php echo (int)$rd['er_id']; ?>"><?php echo htmlspecialchars($rd['erd_name']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <!-- 학력 -->
        <div class="form-row">
          <div class="form-label">학력</div>
          <div class="form-cell">
            <select class="fi-select" id="resume_edu">
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
            <select class="fi-select" id="resume_work_region">
              <option value="">지역선택</option>
              <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
              <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
              <?php } ?>
            </select>
            <select class="fi-select" id="resume_work_region_detail">
              <option value="">세부지역선택</option>
              <?php foreach ((isset($ev_region_details) ? $ev_region_details : []) as $rd) { ?>
              <option value="<?php echo (int)$rd['erd_id']; ?>" data-er-id="<?php echo (int)$rd['er_id']; ?>"><?php echo htmlspecialchars($rd['erd_name']); ?></option>
              <?php } ?>
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
            <select class="fi-select" id="resume_work_time_type"><option>무관</option><option>주간</option><option>야간</option><option>새벽</option></select>
            <span style="font-size:13px;color:#888;">시작</span>
            <input class="fi" type="text" placeholder="00:00" style="width:80px;text-align:center;" id="resume_work_time_start">
            <span style="font-size:13px;color:#888;">~</span>
            <input class="fi" type="text" placeholder="00:00" style="width:80px;text-align:center;" id="resume_work_time_end">
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

    <!-- 🤖 AI 근접 매칭 안내 (희망하는 편의사항 위) -->
    <div class="resume-ai-matching-notice">
      <div class="resume-ai-matching-inner">
        🤖 <strong>AI 근접 매칭</strong>이란? 희망 편의사항, 키워드, MBTI를 선택하면, 업소의 니즈와 구직자의 성향을 분석하여 최적의 매칭을 도와주는 서비스입니다.
      </div>
    </div>

    <!-- ===== 7. 희망하는 편의사항 ===== -->
    <div class="form-card sh-green">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">✅</span>
        <span class="sec-head-title">희망하는 편의사항</span>
        <span class="sec-head-sub">2개 이상 선택하면 매칭에 유리합니다.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="amenity-grid">
          <div class="chk-item"><input type="checkbox" id="am-0"><label for="am-0">당일지급</label></div>
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
        <span class="sec-head-sub">해당하는 키워드를 선택하면 매칭에 유리합니다.</span>
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
            <textarea class="fi fi-full" style="min-height:140px;" placeholder="자신을 어필할 수 있는 내용을 자유롭게 작성해주세요.&#10;예) 성격, 장점, 희망 업소 유형, 특이사항 등" id="resume_intro"></textarea>
            <p class="hint">* 2000자 이내로 작성해주세요.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== 10. MBTI유형 ===== -->
    <div class="form-card" style="border:2px solid var(--pale-pink);">
      <div class="sec-head open" style="background:linear-gradient(135deg,#6A1B9A,#AB47BC);" onclick="toggleSec(this)">
        <span class="sec-head-icon">🧠</span>
        <span class="sec-head-title" style="color:#fff;">MBTI유형</span>
        <span class="sec-head-sub" style="color:rgba(255,255,255,.8);">MBTI 유형을 선택하면 매칭에 유리합니다.</span>
        <span class="sec-chevron" style="color:#fff;">▼</span>
      </div>
      <div class="sec-body">
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

    <!-- ===== AI매칭에 보여지는 이력서 (eve_alba_resume_1.html 디자인) ===== -->
    <div class="ai-preview-card" id="resume-ai-summary-card">
      <div class="ai-preview-header" onclick="toggleAiPreview()">
        <div class="ai-preview-header-left">
          <div class="ai-preview-avatar">👩</div>
          <div>
            <div class="ai-preview-title">AI매칭에 보여지는 이력서</div>
            <div class="ai-preview-subtitle">실시간으로 입력한 내용이 반영됩니다</div>
          </div>
        </div>
        <div class="ai-preview-header-right">
          <span class="ai-preview-badge">제출 전 확인 · AI 매칭 시 노출되는 정보입니다</span>
          <button type="button" class="ai-preview-toggle-btn" id="aiToggleBtn" aria-label="접기/펼치기">▲</button>
        </div>
      </div>
      <div class="ai-preview-body" id="aiPreviewBody">
        <div class="aip-row">
          <div class="aip-label">📄 이력서 제목</div>
          <div class="aip-value" id="resume-summary-title"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-photo">
          <div class="aip-label">📷 사진 · 자기소개</div>
          <div class="aip-value aip-photo-area">
            <div class="aip-photo-box" id="resume-summary-photo">
              <div class="aip-photo-empty"><span style="font-size:28px;opacity:.3;">👤</span><span class="aip-empty" style="font-size:11px;margin-top:4px;">사진 없음</span></div>
            </div>
            <div class="aip-intro" id="resume-summary-intro"><span class="aip-empty">—</span></div>
          </div>
        </div>
        <div class="aip-row">
          <div class="aip-label">👩 닉네임 · 연락방법</div>
          <div class="aip-value" id="resume-summary-contact-wrap"><span class="aip-chip aip-chip-gray">—</span><span class="aip-sep">·</span><span class="aip-chip aip-chip-gray">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">💰 희망급여 · 신장/체중 · 사이즈</div>
          <div class="aip-value" id="resume-summary-salary-wrap"><span class="aip-chip aip-chip-gray">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">🏠 거주지역 · 학력</div>
          <div class="aip-value" id="resume-summary-region-wrap"><span class="aip-chip aip-chip-gray">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">💼 희망분야</div>
          <div class="aip-value" id="resume-summary-job"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">📍 업무가능지역</div>
          <div class="aip-value" id="resume-summary-work-region-wrap"><span class="aip-chip aip-chip-gray">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">⏰ 근무조건</div>
          <div class="aip-value" id="resume-summary-work-cond"><span class="aip-chip aip-chip-gray">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">📚 경력사항</div>
          <div class="aip-value" id="resume-summary-career"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">✅ 희망하는 편의사항</div>
          <div class="aip-value" id="resume-summary-amenity"><span class="aip-empty">선택된 편의사항이 없습니다</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">🏷️ 키워드</div>
          <div class="aip-value" id="resume-summary-keyword"><span class="aip-empty">선택된 키워드가 없습니다</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">🧠 MBTI</div>
          <div class="aip-value" id="resume-summary-mbti"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-footer">
          <div class="aip-footer-icon">🤖</div>
          <div class="aip-footer-text">위 정보는 <strong>AI 근접 매칭</strong> 시 기업회원(업소)에게 노출됩니다. 민감한 개인정보(전화번호 등)는 선택한 공개 방식에 따라 처리됩니다.</div>
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
            <label for="term3">[필수] 만 19세 미만은 이력서 등록이 불가합니다. 본인은 만 19세 이상임을 확인합니다.</label>
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
    if(typeof updateResumeSummary==='function') updateResumeSummary();
  };
  reader.readAsDataURL(input.files[0]);
}
function clearPhoto() {
  document.getElementById('photo-file').value = '';
  document.getElementById('photo-fn').textContent = '선택된 파일 없음';
  document.getElementById('photo-fn').style.color = '#aaa';
  document.getElementById('photoPreview').innerHTML =
    '<span class="photo-preview-icon">📷</span><span class="photo-preview-text">클릭하여<br>사진 등록</span>';
  if(typeof updateResumeSummary==='function') updateResumeSummary();
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
  if(typeof updateResumeSummary==='function') updateResumeSummary();
}
function delCareerRow(btn) {
  var row = btn.closest('tr');
  var tbody = document.getElementById('careerBody');
  if(tbody.rows.length > 1) row.remove();
  else alert('최소 1개 행은 필요합니다.');
  if(typeof updateResumeSummary==='function') updateResumeSummary();
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

/* AI매칭 이력서 요약 실시간 갱신 (eve_alba_resume_1 디자인·칩 반영) */
function updateResumeSummary() {
  function val(id){ var e=document.getElementById(id); return e? (e.value||e.textContent||'').trim():''; }
  function sel(id){ var e=document.getElementById(id); return e&&e.options[e.selectedIndex]? e.options[e.selectedIndex].text:''; }
  function set(id,t){ var e=document.getElementById(id); if(e) e.textContent=t||'—'; }
  function setHtml(id,html){ var e=document.getElementById(id); if(e) e.innerHTML=html; }
  function esc(s){ if(!s) return ''; var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
  function chip(t,c){ return '<span class="aip-chip '+(c||'aip-chip-gray')+'">'+esc(t||'—')+'</span>'; }
  function radioVal(name){ var r=document.querySelector('input[name="'+name+'"]:checked'); return r? (r.nextElementSibling? r.nextElementSibling.textContent: r.labels&&r.labels[0]? r.labels[0].textContent: ''):''; }

  var title = val('resume_title');
  var titleEl = document.getElementById('resume-summary-title');
  if(titleEl) titleEl.innerHTML = title ? esc(title) : '<span class="aip-empty">—</span>';

  var photoBox = document.getElementById('photoPreview');
  var sumPhoto = document.getElementById('resume-summary-photo');
  if(sumPhoto){
    if(photoBox&&photoBox.querySelector('img')){
      sumPhoto.innerHTML=''; var img=photoBox.querySelector('img').cloneNode(true); img.style.width='100%'; img.style.height='100%'; img.style.objectFit='cover'; sumPhoto.appendChild(img); sumPhoto.classList.add('has-img');
    } else { sumPhoto.innerHTML='<div class="aip-photo-empty"><span style="font-size:28px;opacity:.3;">👤</span><span class="aip-empty" style="font-size:11px;margin-top:4px;">사진 없음</span></div>'; sumPhoto.classList.remove('has-img'); }
  }
  set('resume-summary-intro', val('resume_intro')||'—');

  var nick = val('resume_nick'), contactLabel = radioVal('contact');
  var snsType = sel('resume_sns_type'), snsId = val('resume_sns_id');
  var contactParts = chip(nick,'aip-chip-gray')+'<span class="aip-sep">·</span>'+chip(contactLabel,'aip-chip-blue');
  if(snsId) contactParts += '<span class="aip-sep">·</span>'+chip(snsType+': '+snsId,'aip-chip-blue');
  setHtml('resume-summary-contact-wrap', contactParts);

  var salType=sel('resume_salary_type'), salAmt=val('resume_salary_amt');
  var salaryText = salAmt ? (salType+' '+salAmt+'원') : (salType||'—');
  var h=val('resume_height'), w=val('resume_weight');
  var hwText = (h||w) ? (h+'cm / '+w+'kg') : '—';
  var sizeText = sel('resume_size');
  var salaryParts = [];
  if(salaryText&&salaryText!=='—') salaryParts.push(chip(salaryText,'aip-chip-orange'));
  if(hwText&&hwText!=='—') salaryParts.push(chip(hwText,'aip-chip-gray'));
  if(sizeText&&sizeText!=='—'&&sizeText.indexOf('선택')<0) salaryParts.push(chip(sizeText,'aip-chip-orange'));
  setHtml('resume-summary-salary-wrap', salaryParts.length ? salaryParts.join('<span class="aip-sep">·</span>') : chip('—','aip-chip-gray'));

  var r1=sel('resume_region'), r2=sel('resume_region_detail');
  var regionText = (r1&&r1.indexOf('선택')<0) ? (r2&&r2.indexOf('선택')<0 ? r1+' '+r2 : r1) : '—';
  var eduText = sel('resume_edu');
  var regionParts = [];
  if(regionText&&regionText!=='—') regionParts.push(chip(regionText,'aip-chip-gray'));
  if(eduText&&eduText!=='—'&&eduText.indexOf('선택')<0) regionParts.push(chip(eduText,'aip-chip-gray'));
  setHtml('resume-summary-region-wrap', regionParts.length ? regionParts.join('<span class="aip-sep">·</span>') : chip('—','aip-chip-gray'));

  var j1=sel('resume_job1'), j2=sel('resume_job2');
  var jobText = (j1&&j1.indexOf('-')<0) ? (j2&&j2.indexOf('-')<0 ? j1+' / '+j2 : j1) : '—';
  var jobEl = document.getElementById('resume-summary-job');
  if(jobEl) jobEl.innerHTML = jobText && jobText!=='—' ? chip(jobText,'aip-chip-purple') : '<span class="aip-empty">—</span>';

  var wr1=sel('resume_work_region'), wr2=sel('resume_work_region_detail');
  var workRegionText = (wr1&&wr1.indexOf('선택')<0) ? (wr2&&wr2.indexOf('선택')<0 ? wr1+' '+wr2 : wr1) : '—';
  var ex=[];
  if(document.getElementById('rg-all')&&document.getElementById('rg-all').checked) ex.push('전국 가능');
  if(document.getElementById('rg-travel')&&document.getElementById('rg-travel').checked) ex.push('출장 가능');
  if(document.getElementById('rg-abroad')&&document.getElementById('rg-abroad').checked) ex.push('해외 가능');
  var workRegionParts = [];
  if(workRegionText&&workRegionText!=='—') workRegionParts.push(chip(workRegionText,'aip-chip-gray'));
  for(var i=0;i<ex.length;i++) workRegionParts.push(chip(ex[i],'aip-chip-blue'));
  setHtml('resume-summary-work-region-wrap', workRegionParts.length ? workRegionParts.join('<span class="aip-sep">·</span>') : chip('—','aip-chip-gray'));

  var wt=radioVal('work-type');
  var days=[], dayIds=['day-mon','day-tue','day-wed','day-thu','day-fri','day-sat','day-sun'], dayLabels=['월','화','수','목','금','토','일'];
  for(var i=0;i<dayIds.length;i++) if(document.getElementById(dayIds[i])&&document.getElementById(dayIds[i]).checked) days.push(dayLabels[i]);
  var wtType=sel('resume_work_time_type'), wtS=val('resume_work_time_start'), wtE=val('resume_work_time_end');
  var workCond=wt||'—';
  if(days.length) workCond+=' · '+days.join(',');
  if(wtType&&wtType!=='무관') workCond+=' · '+wtType;
  if(wtS||wtE) workCond+=' · '+(wtS||'')+'~'+(wtE||'');
  var workCondEl = document.getElementById('resume-summary-work-cond');
  if(workCondEl) workCondEl.innerHTML = (workCond&&workCond!=='—') ? chip(workCond,'aip-chip-blue') : '<span class="aip-empty">—</span>';

  var careerRows=document.querySelectorAll('#careerBody tr');
  var careerTexts=[];
  for(var i=0;i<careerRows.length;i++){
    var inputs=careerRows[i].querySelectorAll('input[type="text"], select');
    if(inputs.length>=4){
      var a=inputs[0].value.trim(), b=inputs[1].options&&inputs[1].options[inputs[1].selectedIndex]? inputs[1].options[inputs[1].selectedIndex].text:'', c=inputs[2].value.trim(), d=inputs[3].value.trim();
      if(a||b||c||d) careerTexts.push((a||'-')+' / '+(b||'-')+' / '+(c||'-')+' / '+(d||'-'));
    }
  }
  var careerEl = document.getElementById('resume-summary-career');
  if(careerEl) careerEl.innerHTML = careerTexts.length ? chip(careerTexts.join(' | '),'aip-chip-green') : '<span class="aip-empty">—</span>';

  var am=[], amIds=['am-0','am-1','am-2','am-3','am-4','am-5','am-6','am-7','am-8','am-9','am-10','am-11','am-12','am-13','am-14','am-15','am-16','am-17','am-18','am-19','am-20','am-21'];
  for(var i=0;i<amIds.length;i++){ var cb=document.getElementById(amIds[i]); if(cb&&cb.checked&&cb.nextElementSibling) am.push(cb.nextElementSibling.textContent); }
  var amenityEl = document.getElementById('resume-summary-amenity');
  if(amenityEl) amenityEl.innerHTML = am.length ? am.map(function(a){ return chip(a,'aip-chip-pink'); }).join('') : '<span class="aip-empty">선택된 편의사항이 없습니다</span>';

  var kw=[], kwIds=['kw-1','kw-2','kw-3','kw-4','kw-5','kw-6','kw-7','kw-8','kw-9','kw-10','kw-11','kw-12','kw-13','kw-14','kw-15','kw-16','kw-17','kw-18','kw-19','kw-20','kw-21','kw-22','kw-23','kw-24'];
  for(var j=0;j<kwIds.length;j++){ var c=document.getElementById(kwIds[j]); if(c&&c.checked&&c.nextElementSibling) kw.push(c.nextElementSibling.textContent); }
  var keywordEl = document.getElementById('resume-summary-keyword');
  if(keywordEl) keywordEl.innerHTML = kw.length ? kw.map(function(k){ return chip(k,'aip-chip-orange'); }).join('') : '<span class="aip-empty">선택된 키워드가 없습니다</span>';

  var mbtiR=document.querySelector('input[name="mbti"]:checked');
  var mbtiEl = document.getElementById('resume-summary-mbti');
  if(mbtiEl) mbtiEl.innerHTML = mbtiR ? chip(mbtiR.value,'aip-chip-purple') : '<span class="aip-empty">—</span>';
}
function toggleAiPreview(){
  var body=document.getElementById('aiPreviewBody'), btn=document.getElementById('aiToggleBtn');
  if(body){ body.classList.toggle('hide'); if(btn) btn.classList.toggle('collapsed'); if(btn) btn.textContent=body.classList.contains('hide')?'▼':'▲'; }
}
/* 지역 선택 시 세부지역 필터 (jobs.php 채용정보검색과 동일) */
function filterResumeSubByRegion(regionId, detailId) {
  var region = document.getElementById(regionId);
  var detail = document.getElementById(detailId);
  if (!region || !detail) return;
  var opts = detail.querySelectorAll('option[data-er-id]');
  var cache = [];
  opts.forEach(function(o){ cache.push({ value: o.value, text: o.textContent, erId: o.getAttribute('data-er-id') }); });
  var firstOpt = detail.querySelector('option[value=""]');
  function apply() {
    var erId = region.value;
    while (detail.options.length) detail.remove(0);
    if (firstOpt) { var p = document.createElement('option'); p.value = ''; p.textContent = firstOpt.textContent; detail.appendChild(p); }
    cache.forEach(function(o) {
      if (!erId || o.erId === erId) {
        var opt = document.createElement('option');
        opt.value = o.value;
        opt.textContent = o.text;
        opt.setAttribute('data-er-id', o.erId);
        detail.appendChild(opt);
      }
    });
    detail.value = '';
    if (typeof updateResumeSummary === 'function') updateResumeSummary();
  }
  region.addEventListener('change', apply);
  apply();
}
(function(){
  filterResumeSubByRegion('resume_region', 'resume_region_detail');
  filterResumeSubByRegion('resume_work_region', 'resume_work_region_detail');
  var ids=['resume_title','resume_nick','resume_phone','resume_age','resume_salary_type','resume_salary_amt','resume_height','resume_weight','resume_size','resume_region','resume_region_detail','resume_edu','resume_job1','resume_job2','resume_work_region','resume_work_region_detail','resume_work_time_type','resume_work_time_start','resume_work_time_end','resume_intro','resume_sns_type','resume_sns_id'];
  function attach(){ for(var i=0;i<ids.length;i++){ var el=document.getElementById(ids[i]); if(el){ el.addEventListener('input', updateResumeSummary); el.addEventListener('change', updateResumeSummary); } } }
  document.querySelectorAll('input[name="contact"], input[name="work-type"], input[name="mbti"]').forEach(function(el){ el.addEventListener('change', updateResumeSummary); });
  for(var k=0;k<=21;k++){ var amEl=document.getElementById('am-'+k); if(amEl) amEl.addEventListener('change', updateResumeSummary); }
  for(var k=1;k<=24;k++){ var kw=document.getElementById('kw-'+k); if(kw) kw.addEventListener('change', updateResumeSummary); }
  ['rg-all','rg-travel','rg-abroad'].forEach(function(id){ var el=document.getElementById(id); if(el) el.addEventListener('change', updateResumeSummary); });
  var careerBody=document.getElementById('careerBody');
  if(careerBody){ careerBody.addEventListener('input', updateResumeSummary); careerBody.addEventListener('change', updateResumeSummary); }
  attach();
  if(document.readyState==='complete') updateResumeSummary(); else window.addEventListener('load', updateResumeSummary);
})();

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
  var salaryErr = checkResumeSalaryLimit();
  if(salaryErr){ alert(salaryErr); return; }

  var titleEl=document.getElementById('resume_title');
  if(!titleEl||!titleEl.value.trim()){alert('이력서 제목을 입력해 주세요.');return;}
  var ageEl=document.getElementById('resume_age');
  if(!ageEl||!ageEl.value){alert('나이를 선택해 주세요.');return;}
  var introEl=document.getElementById('resume_intro');
  if(!introEl||!introEl.value.trim()){alert('자기소개를 입력해 주세요.');return;}

  function gv(id){var e=document.getElementById(id);return e?(e.value||'').trim():'';}
  function gs(id){var e=document.getElementById(id);return e&&e.selectedIndex>0?e.options[e.selectedIndex].text:'';}
  function rv(name){var r=document.querySelector('input[name="'+name+'"]:checked');return r&&r.nextElementSibling?r.nextElementSibling.textContent:'';}

  var fd=new FormData();
  fd.append('title', gv('resume_title'));
  fd.append('nick', gv('resume_nick'));
  fd.append('gender', gv('resume_gender'));
  fd.append('age', gv('resume_age'));
  fd.append('job1', gs('resume_job1'));
  fd.append('job2', gs('resume_job2'));
  fd.append('region', gs('resume_region'));
  fd.append('region_detail', gs('resume_region_detail'));
  fd.append('work_region', gs('resume_work_region'));
  fd.append('work_region_detail', gs('resume_work_region_detail'));
  fd.append('salary_type', gs('resume_salary_type'));
  fd.append('salary_amt', gv('resume_salary_amt'));
  fd.append('phone', gv('resume_phone'));
  fd.append('sns_type', gs('resume_sns_type'));
  fd.append('sns_id', gv('resume_sns_id'));
  fd.append('contact', rv('contact'));
  fd.append('height', gv('resume_height'));
  fd.append('weight', gv('resume_weight'));
  fd.append('size', gs('resume_size'));
  fd.append('edu', gs('resume_edu'));
  fd.append('work_type', rv('work-type'));
  fd.append('work_time_type', gs('resume_work_time_type'));
  fd.append('work_time_start', gv('resume_work_time_start'));
  fd.append('work_time_end', gv('resume_work_time_end'));
  fd.append('intro', gv('resume_intro'));
  fd.append('career_type', rv('career'));

  var days=[];
  ['day-mon','day-tue','day-wed','day-thu','day-fri','day-sat','day-sun'].forEach(function(id){
    var c=document.getElementById(id); if(c&&c.checked) days.push(c.nextElementSibling?c.nextElementSibling.textContent:'');
  });
  fd.append('work_days', days.join(','));

  var extra=[];
  if(document.getElementById('rg-all')&&document.getElementById('rg-all').checked) extra.push('전국 가능');
  if(document.getElementById('rg-travel')&&document.getElementById('rg-travel').checked) extra.push('출장 가능');
  if(document.getElementById('rg-abroad')&&document.getElementById('rg-abroad').checked) extra.push('해외 가능');
  fd.append('work_region_extra', extra.join(','));

  var careers=[];
  document.querySelectorAll('#careerBody tr').forEach(function(tr){
    var inputs=tr.querySelectorAll('input[type="text"], select');
    if(inputs.length>=4){
      careers.push({name:inputs[0].value,type:inputs[1].options[inputs[1].selectedIndex].text,period:inputs[2].value,pay:inputs[3].value});
    }
  });
  fd.append('careers', JSON.stringify(careers));

  var amenities=[];
  for(var i=0;i<=21;i++){var c=document.getElementById('am-'+i);if(c&&c.checked&&c.nextElementSibling)amenities.push(c.nextElementSibling.textContent);}
  fd.append('amenities', JSON.stringify(amenities));

  var keywords=[];
  for(var i=1;i<=24;i++){var c=document.getElementById('kw-'+i);if(c&&c.checked&&c.nextElementSibling)keywords.push(c.nextElementSibling.textContent);}
  fd.append('keywords', JSON.stringify(keywords));

  var mbtiR=document.querySelector('input[name="mbti"]:checked');
  fd.append('mbti', mbtiR?mbtiR.value:'');

  var photoFile=document.getElementById('photo-file');
  if(photoFile&&photoFile.files&&photoFile.files[0]) fd.append('photo_file', photoFile.files[0]);

  var btn=document.querySelector('.btn-submit');
  if(btn){btn.disabled=true;btn.textContent='등록 중...';}

  var saveUrl='<?php echo (defined("G5_URL")&&G5_URL)?rtrim(G5_URL,"/")."/resume_save.php":"/resume_save.php"; ?>';
  fetch(saveUrl,{method:'POST',body:fd,credentials:'same-origin'})
  .then(function(r){return r.json();})
  .then(function(res){
    if(btn){btn.disabled=false;btn.textContent='📄 이력서 등록';}
    if(res.ok){
      alert(res.msg||'이력서가 등록되었습니다!');
      var talentUrl='<?php echo (defined("G5_URL")&&G5_URL)?rtrim(G5_URL,"/")."/talent.php":"/talent.php"; ?>';
      location.href=talentUrl;
    } else {
      alert(res.msg||'등록에 실패했습니다.');
    }
  })
  .catch(function(e){
    if(btn){btn.disabled=false;btn.textContent='📄 이력서 등록';}
    alert('등록 중 오류가 발생했습니다: '+(e.message||''));
  });
}

var _rSalaryLimits = {
  '단란주점':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '룸살롱':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '가라오케':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '노래방':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '퍼블릭':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '클럽':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '바(Bar)':{시급:150000,일급:500000,주급:3000000,월급:12000000},
  '마사지':{시급:120000,일급:400000,주급:2500000,월급:8400000}
};
function checkResumeSalaryLimit(){
  var j1El=document.getElementById('resume_job1');
  var stEl=document.getElementById('resume_salary_type');
  var amtEl=document.getElementById('resume_salary_amt');
  if(!j1El||!stEl||!amtEl) return '';
  var j1=j1El.value, st=stEl.value, raw=amtEl.value.replace(/[^0-9]/g,'');
  if(!raw||st==='급여협의') return '';
  var amt=parseInt(raw,10);
  if(amt<10320) return '최저임금(10,320원) 이상 입력해주세요.';
  var limits=_rSalaryLimits[j1];
  if(!limits) return '';
  var max=limits[st];
  if(max&&amt>max) return '급여기준표를 확인해주세요. ('+j1+' '+st+' 최대 '+max.toLocaleString()+'원)';
  return '';
}
function showResumeSalaryWarn(){
  var w=document.getElementById('salary-warn-resume');
  if(!w) return;
  var msg=checkResumeSalaryLimit();
  w.textContent=msg; w.style.display=msg?'block':'none';
}
(function(){
  var a=document.getElementById('resume_salary_amt');
  var t=document.getElementById('resume_salary_type');
  var j=document.getElementById('resume_job1');
  if(a) a.addEventListener('input',showResumeSalaryWarn);
  if(t) t.addEventListener('change',showResumeSalaryWarn);
  if(j) j.addEventListener('change',showResumeSalaryWarn);
})();

function openSalaryGuide(){ document.getElementById('modal-salary-guide').style.display='flex'; }
function closeSalaryGuide(){ document.getElementById('modal-salary-guide').style.display='none'; }
</script>

<!-- 급여 기준표 모달 -->
<div id="modal-salary-guide" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;" onclick="closeSalaryGuide()">
<div style="width:100%;max-width:460px;background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.35);display:flex;flex-direction:column;max-height:90vh;" onclick="event.stopPropagation()">
  <div style="background:linear-gradient(135deg,#2D0020,#FF1B6B);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
    <div style="font-size:16px;font-weight:900;color:#fff;display:flex;align-items:center;gap:8px;">💰 급여 기준표</div>
    <button type="button" onclick="closeSalaryGuide()" style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);border:none;color:#fff;font-size:16px;font-weight:700;cursor:pointer;">✕</button>
  </div>
  <div style="overflow-y:auto;flex:1;">
    <table style="width:100%;border-collapse:collapse;">
      <thead><tr>
        <th style="position:sticky;top:0;z-index:10;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-weight:900;font-size:12px;padding:10px 0;text-align:center;width:80px;">업종</th>
        <th style="position:sticky;top:0;z-index:10;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-weight:900;font-size:12px;padding:10px 0;text-align:center;width:72px;">항목</th>
        <th style="position:sticky;top:0;z-index:10;background:linear-gradient(135deg,#FF6B35,#FF1B6B);color:#fff;font-weight:900;font-size:12px;padding:10px 0;text-align:center;">금액제한</th>
      </tr></thead>
      <tbody>
        <?php
        $sg_biz = [
          ['룸싸롱','#FFF0F5','#C9007A','#FF1B6B',[['시급','10,320 ~ 150,000'],['일급','10,320 ~ 500,000'],['주급','10,320 ~ 3,000,000'],['월급','10,320 ~ 12,000,000'],['건당','10,320 ~ 190,000']]],
          ['노래주점','#F3E8FF','#7B1FA2','#9C27B0',[['시급','10,320 ~ 150,000'],['일급','10,320 ~ 500,000'],['주급','10,320 ~ 3,000,000'],['월급','10,320 ~ 12,000,000'],['건당','10,320 ~ 190,000']]],
          ['마사지','#E3F2FD','#1565C0','#1976D2',[['시급','10,320 ~ 120,000'],['일급','10,320 ~ 400,000'],['주급','10,320 ~ 2,500,000'],['월급','10,320 ~ 8,400,000'],['건당','10,320 ~ 170,000']]],
          ['기타','#E8F5E9','#2E7D32','#43A047',[['시급','10,320 ~ 150,000'],['일급','10,320 ~ 500,000'],['주급','10,320 ~ 3,000,000'],['월급','10,320 ~ 12,000,000'],['건당','10,320 ~ 190,000']]],
        ];
        foreach($sg_biz as $bi => $b){
          $cnt = count($b[4]);
          foreach($b[4] as $ri => $r){
            echo '<tr>';
            if($ri===0) echo '<td rowspan="'.$cnt.'" style="text-align:center;font-weight:900;font-size:13px;padding:0 8px;vertical-align:middle;white-space:nowrap;background:'.$b[1].';color:'.$b[2].';border-right:3px solid '.$b[3].';">'.$b[0].'</td>';
            echo '<td style="text-align:center;font-size:12.5px;color:#555;font-weight:500;padding:11px 8px;background:#fafafa;border-bottom:1px solid #f5f5f5;border-right:1px solid #f0f0f0;">'.$r[0].'</td>';
            echo '<td style="text-align:center;font-size:12.5px;color:#FF1B6B;font-weight:700;padding:11px 12px;border-bottom:1px solid #f5f5f5;">'.$r[1].'</td>';
            echo '</tr>';
          }
          if($bi < count($sg_biz)-1) echo '<tr><td colspan="3" style="height:5px;padding:0;border:none;background:linear-gradient(90deg,#FF1B6B,#FF6BA8,#FF1B6B);opacity:.18;"></td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
  <div style="padding:11px 16px;background:linear-gradient(90deg,#fff0f6,#fff8fb);border-top:1.5px solid #fce8f0;font-size:11px;color:#888;display:flex;align-items:center;gap:6px;flex-shrink:0;">
    💡 ※ 급여 승인신청은 <strong style="color:#FF1B6B;">채용공고 수정페이지</strong>에 있습니다.
  </div>
</div>
</div>
