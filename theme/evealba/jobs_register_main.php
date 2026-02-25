<?php if (!defined('_GNUBOARD_')) exit;
$jobs_update_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs_register_update.php' : '/jobs_register_update.php';
?>
<form name="fjobregister" id="fjobregister" method="post" action="<?php echo $jobs_update_url; ?>">
<input type="hidden" name="job_data" id="job_data_hidden" value="">
<input type="hidden" name="total_amount" id="total_amount_hidden" value="0">
<input type="hidden" name="ad_period" id="ad_period_hidden" value="30">
<input type="hidden" name="ad_labels" id="ad_labels_hidden" value="">

    <div class="page-title-bar">
      <h2 class="page-title">📝 채용정보등록</h2>
    </div>

    <!-- =============================
         1. 업소 정보
    ============================= -->
    <div class="form-card sh-pink">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">🏢</span>
        <span class="sec-head-title">업소 정보</span>
        <span class="sec-head-sub">기본 업체 정보를 입력해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 닉네임 -->
        <div class="form-row">
          <div class="form-label">닉네임 (업소명) <span class="req">*</span></div>
          <div class="form-cell">
            <input class="fi fi-md" type="text" id="job_nickname" name="job_nickname" placeholder="업소명을 입력해주세요">
          </div>
        </div>

        <!-- 확인문서첨부 -->
        <div class="form-row">
          <div class="form-label">확인문서첨부 <span class="req">*</span></div>
          <div class="form-cell col">
            <div class="file-row">
              <button class="btn-file" onclick="triggerFile('doc-file')">📎 파일 선택</button>
              <input type="file" id="doc-file" style="display:none" onchange="setFileName(this,'doc-fn')">
              <span class="file-name" id="doc-fn">선택된 파일 없음</span>
              <button class="btn-file-cancel" onclick="clearFile('doc-file','doc-fn')">✕ 취소</button>
            </div>
            <p class="hint-red">첨부 서류 : 사업자등록증, 직업소개사업등록증, 영업허가증 中 택1</p>
          </div>
        </div>

        <!-- 사업장 위치 확인 -->
        <div class="form-row">
          <div class="form-label">사업장 위치 확인 <span class="req">*</span></div>
          <div class="form-cell col">
            <div class="radio-group">
              <div class="radio-item"><input type="radio" name="location-match" id="lm-yes"><label for="lm-yes">예</label></div>
              <div class="radio-item"><input type="radio" name="location-match" id="lm-no" checked><label for="lm-no">아니오</label></div>
            </div>
            <p class="hint">첨부서류의 등록상 주소지와 실제 운영 사업장 위치가 동일 합니까?</p>
          </div>
        </div>

        <!-- 상호 -->
        <div class="form-row">
          <div class="form-label">상호 <span class="req">*</span></div>
          <div class="form-cell col">
            <input class="fi fi-md fi-readonly" type="text" id="job_company" name="job_company" placeholder="" readonly>
            <p class="hint-blue">+ 첨부된 확인문서 검수 후 기재된 상호로 자동등록됩니다.</p>
          </div>
        </div>

        <!-- 담당자 -->
        <div class="form-row">
          <div class="form-label">담당자 <span class="req">*</span></div>
          <div class="form-cell"><input class="fi fi-sm" type="text" placeholder="담당자명"></div>
        </div>

        <!-- 담당자 연락처 -->
        <div class="form-row">
          <div class="form-label">담당자 연락처 <span class="req">*</span></div>
          <div class="form-cell"><input class="fi fi-sm" type="text" value="010-0000-0000"></div>
        </div>

        <!-- 카카오톡 ID -->
        <div class="form-row">
          <div class="form-label">카카오톡 ID</div>
          <div class="form-cell"><input class="fi fi-sm" type="text" placeholder="카카오톡 아이디"></div>
        </div>

        <!-- 라인 ID -->
        <div class="form-row">
          <div class="form-label">라인 ID</div>
          <div class="form-cell"><input class="fi fi-sm" type="text" placeholder="라인 아이디"></div>
        </div>

        <!-- 위켓 ID -->
        <div class="form-row">
          <div class="form-label">위켓 ID</div>
          <div class="form-cell"><input class="fi fi-sm" type="text" placeholder="위켓 아이디"></div>
        </div>

        <!-- 텔레그램 ID -->
        <div class="form-row">
          <div class="form-label">텔레그램 ID</div>
          <div class="form-cell"><input class="fi fi-sm" type="text" placeholder="텔레그램 아이디"></div>
        </div>

      </div>
    </div>

    <!-- =============================
         2. 채용 정보
    ============================= -->
    <div class="form-card sh-orange">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📋</span>
        <span class="sec-head-title">채용 정보</span>
        <span class="sec-head-sub">채용 내용을 입력해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 채용제목 -->
        <div class="form-row">
          <div class="form-label">채용제목 <span class="req">*</span></div>
          <div class="form-cell" style="position:relative;">
            <input class="fi fi-full" type="text" id="job_title" name="job_title" placeholder="채용 제목을 입력해주세요" maxlength="40">
            <span style="position:absolute;right:22px;font-size:11px;color:#aaa;">40자 제한</span>
          </div>
        </div>

        <!-- 고용형태 -->
        <div class="form-row">
          <div class="form-label">고용형태 <span class="req">*</span></div>
          <div class="form-cell">
            <div class="radio-group">
              <div class="radio-item"><input type="radio" name="employ-type" id="et-hire" value="고용" checked><label for="et-hire">고용</label></div>
              <div class="radio-item"><input type="radio" name="employ-type" id="et-pa" value="파견"><label for="et-pa">파견</label></div>
              <div class="radio-item"><input type="radio" name="employ-type" id="et-do" value="도급"><label for="et-do">도급</label></div>
              <div class="radio-item"><input type="radio" name="employ-type" id="et-we" value="위임"><label for="et-we">위임</label></div>
            </div>
          </div>
        </div>

        <!-- 마감일 -->
        <div class="form-row">
          <div class="form-label">마감일</div>
          <div class="form-cell">
            <p class="hint" style="margin:0;">마감일은 결제일로부터 필수결제사항인 줄광고(채용정보보리스트)의 입수만을 합산된 날짜입니다.</p>
          </div>
        </div>

        <!-- 급여조건 -->
        <div class="form-row">
          <div class="form-label">급여조건 <span class="req">*</span></div>
          <div class="form-cell">
            <div class="salary-row">
              <select class="fi-select" id="job_salary_type">
                <option>급여협의</option>
                <option>시급</option>
                <option>일급</option>
                <option>주급</option>
                <option>월급</option>
              </select>
              <input class="fi fi-xs" type="text" id="job_salary_amt" placeholder="금액 입력">
              <span class="fi-unit">원</span>
              <button class="btn-salary-guide">💰 급여 기준표</button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- =============================
         3. 근무지역
    ============================= -->
    <div class="form-card sh-purple">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📍</span>
        <span class="sec-head-title">근무지역</span>
        <span class="sec-head-sub">근무 지역을 선택해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="form-row">
          <div class="form-label">1순위 <span class="req">*</span></div>
          <div class="form-cell">
            <select class="fi-select" id="job_work_region_1">
              <option value="">1순위 지역선택</option>
              <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
              <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
              <?php } ?>
            </select>
            <select class="fi-select" id="job_work_region_detail_1">
              <option value="">1순위 세부지역선택</option>
              <?php foreach ((isset($ev_region_details) ? $ev_region_details : []) as $rd) { ?>
              <option value="<?php echo (int)$rd['erd_id']; ?>" data-er-id="<?php echo (int)$rd['er_id']; ?>"><?php echo htmlspecialchars($rd['erd_name']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">2순위</div>
          <div class="form-cell">
            <select class="fi-select" id="job_work_region_2">
              <option value="">2순위 지역선택</option>
              <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
              <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
              <?php } ?>
            </select>
            <select class="fi-select" id="job_work_region_detail_2">
              <option value="">2순위 세부지역선택</option>
              <?php foreach ((isset($ev_region_details) ? $ev_region_details : []) as $rd) { ?>
              <option value="<?php echo (int)$rd['erd_id']; ?>" data-er-id="<?php echo (int)$rd['er_id']; ?>"><?php echo htmlspecialchars($rd['erd_name']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">3순위</div>
          <div class="form-cell">
            <select class="fi-select" id="job_work_region_3">
              <option value="">3순위 지역선택</option>
              <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
              <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
              <?php } ?>
            </select>
            <select class="fi-select" id="job_work_region_detail_3">
              <option value="">3순위 세부지역선택</option>
              <?php foreach ((isset($ev_region_details) ? $ev_region_details : []) as $rd) { ?>
              <option value="<?php echo (int)$rd['erd_id']; ?>" data-er-id="<?php echo (int)$rd['er_id']; ?>"><?php echo htmlspecialchars($rd['erd_name']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- =============================
         4. 업종/직종
    ============================= -->
    <div class="form-card sh-blue">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">💼</span>
        <span class="sec-head-title">업종/직종</span>
        <span class="sec-head-sub">해당하는 업종과 직종을 선택해주세요</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="form-row">
          <div class="form-label">업종/직종 <span class="req">*</span></div>
          <div class="form-cell">
            <select class="fi-select" id="job_job1">
              <option>-1차 직종선택-</option>
              <option>단란주점</option><option>룸살롱</option><option>가라오케</option>
              <option>노래방</option><option>클럽</option><option>바(Bar)</option>
              <option>퍼블릭</option><option>마사지</option><option>풀살롱</option>
            </select>
            <select class="fi-select" id="job_job2">
              <option>-2차 직종선택-</option>
              <option>서빙</option><option>도우미</option><option>아가씨</option>
              <option>TC</option><option>미시</option><option>초미시</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- =============================
         5. 편의사항 (이력서등록과 동일)
    ============================= -->
    <div class="form-card sh-teal">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">✅</span>
        <span class="sec-head-title">편의사항</span>
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

    <!-- =============================
         6. 키워드 (이력서등록과 동일)
    ============================= -->
    <div class="form-card sh-green">
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

    <!-- =============================
         7. 선호하는 MBTI (다중선택)
    ============================= -->
    <div class="form-card" style="border:2px solid var(--pale-pink);">
      <div class="sec-head open" style="background:linear-gradient(135deg,#6A1B9A,#AB47BC);" onclick="toggleSec(this)">
        <span class="sec-head-icon">🧠</span>
        <span class="sec-head-title" style="color:#fff;">선호하는 MBTI</span>
        <span class="sec-head-sub" style="color:rgba(255,255,255,.8);">선호하는 MBTI를 선택하면 매칭에 유리합니다. (다중선택 가능)</span>
        <span class="sec-chevron" style="color:#fff;">▼</span>
      </div>
      <div class="sec-body">
        <!-- NT 분석가형 -->
        <div class="mbti-group mbti-group-nt">
          <div class="mbti-group-title">🔵 NT — 분석가형</div>
          <div class="mbti-grid">
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="INTJ">
              <div class="mbti-card-top"><span class="mbti-type">INTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">고객 성향 빠른 분석, 장기 단골 전략 설계에 강함</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="INTP">
              <div class="mbti-card-top"><span class="mbti-type">INTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">대화 주제 확장력 뛰어나고 지적 매력 어필 가능</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ENTJ">
              <div class="mbti-card-top"><span class="mbti-type">ENTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">목표 매출 설정·관리 능력 우수, 자기 브랜딩 강함</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ENTP">
              <div class="mbti-card-top"><span class="mbti-type">ENTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">말 센스 좋고 토론·농담으로 분위기 반전 능력 탁월</div>
            </label>
          </div>
        </div>
        <!-- NF 외교관형 -->
        <div class="mbti-group mbti-group-nf" style="border-top:1.5px solid var(--pale-pink);">
          <div class="mbti-group-title">🟢 NF — 외교관형</div>
          <div class="mbti-grid">
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="INFJ">
              <div class="mbti-card-top"><span class="mbti-type">INFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">깊은 공감 능력, 감정 상담형 고객에게 매우 강함</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="INFP">
              <div class="mbti-card-top"><span class="mbti-type">INFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">순수·감성 매력, 특정 고객층에게 강한 팬층 형성</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ENFJ">
              <div class="mbti-card-top"><span class="mbti-type">ENFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">고객을 특별하게 만들어주는 능력, VIP 관리 최강</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ENFP">
              <div class="mbti-card-top"><span class="mbti-type">ENFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">밝은 에너지와 리액션, 첫인상 흡입력 매우 높음</div>
            </label>
          </div>
        </div>
        <!-- SJ 관리자형 -->
        <div class="mbti-group mbti-group-sj" style="border-top:1.5px solid var(--pale-pink);">
          <div class="mbti-group-title">🟡 SJ — 관리자형</div>
          <div class="mbti-grid">
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ISTJ">
              <div class="mbti-card-top"><span class="mbti-type">ISTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">시간·약속 철저, 안정적인 신뢰 구축형</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ISFJ">
              <div class="mbti-card-top"><span class="mbti-type">ISFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">섬세한 배려, 단골 관리 지속력 강함</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ESTJ">
              <div class="mbti-card-top"><span class="mbti-type">ESTJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">실적 관리·목표 달성 집요함</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ESFJ">
              <div class="mbti-card-top"><span class="mbti-type">ESFJ</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">친화력 최고 수준, 관계 유지 능력 뛰어남</div>
            </label>
          </div>
        </div>
        <!-- SP 탐험가형 -->
        <div class="mbti-group mbti-group-sp" style="border-top:1.5px solid var(--pale-pink);">
          <div class="mbti-group-title">🔴 SP — 탐험가형</div>
          <div class="mbti-grid">
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ISTP">
              <div class="mbti-card-top"><span class="mbti-type">ISTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">상황 판단 빠름, 감정 휘둘림 적음</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ISFP">
              <div class="mbti-card-top"><span class="mbti-type">ISFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">자연스러운 매력, 부드러운 분위기 형성</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ESTP">
              <div class="mbti-card-top"><span class="mbti-type">ESTP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">밀당·텐션 조절 능숙, 현장 적응력 강함</div>
            </label>
            <label class="mbti-card mbti-multi">
              <input type="checkbox" name="mbti_prefer[]" value="ESFP">
              <div class="mbti-card-top"><span class="mbti-type">ESFP</span><span class="mbti-dot"></span></div>
              <div class="mbti-desc">분위기 메이커, 고객 몰입도 상승 능력 탁월</div>
            </label>
          </div>
        </div>
        <div style="padding:0 18px 14px;">
          <div style="background:#f9f5ff;border:1.5px dashed #CE93D8;border-radius:8px;padding:10px 14px;font-size:11px;color:#7B1FA2;line-height:1.8;">
            💡 선호하는 MBTI를 여러 개 선택하면 AI 매칭 정확도가 높아집니다.
          </div>
        </div>
      </div>
    </div>

    <!-- =============================
         8. 업소이미지 등록
    ============================= -->
    <div class="form-card sh-dark">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">🖼</span>
        <span class="sec-head-title">업소이미지 등록</span>
        <span class="sec-head-sub">+ 업소소개사진을 등록하시면 구인에 도움이 됩니다.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="img-upload-rows">
          <!-- 이미지파일 1~5 -->
          <div id="img-items">
            <!-- JS로 생성 -->
          </div>
        </div>
      </div>
    </div>

    <!-- =============================
         8. 상세설명 (5개 폼)
    ============================= -->
    <div class="form-card sh-pink">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📝</span>
        <span class="sec-head-title">상세설명</span>
        <span class="sec-head-sub">* 필수</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <p class="desc-ai-notice">*작성한 상세설명을 기준으로 AI 자동 상세설명글을 작성해드립니다. 최대한 꼼꼼히 작성 부탁드립니다.</p>
        <div class="form-row">
          <div class="form-label">업소 위치 및 업소 소개 <span class="req">*</span></div>
          <div class="form-cell col">
            <textarea class="desc-field" name="desc_location" id="desc_location" rows="4" placeholder="업소 위치 및 업소 소개를 입력해주세요 (10자 이상)" required minlength="10"></textarea>
            <span class="desc-count" id="cnt_location">0</span> / 10자 이상
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">근무환경 <span class="req">*</span></div>
          <div class="form-cell col">
            <textarea class="desc-field" name="desc_env" id="desc_env" rows="4" placeholder="근무환경을 입력해주세요 (10자 이상)" required minlength="10"></textarea>
            <span class="desc-count" id="cnt_env">0</span> / 10자 이상
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">지원 혜택 및 복리후생 <span class="req">*</span></div>
          <div class="form-cell col">
            <textarea class="desc-field" name="desc_benefit" id="desc_benefit" rows="4" placeholder="지원 혜택 및 복리후생을 입력해주세요 (10자 이상)" required minlength="10"></textarea>
            <span class="desc-count" id="cnt_benefit">0</span> / 10자 이상
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">지원 자격 및 우대사항 <span class="req">*</span></div>
          <div class="form-cell col">
            <textarea class="desc-field" name="desc_qualify" id="desc_qualify" rows="4" placeholder="지원 자격 및 우대사항을 입력해주세요 (10자 이상)" required minlength="10"></textarea>
            <span class="desc-count" id="cnt_qualify">0</span> / 10자 이상
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">추가 상세설명 <span class="req">*</span></div>
          <div class="form-cell col">
            <textarea class="desc-field" name="desc_extra" id="desc_extra" rows="4" placeholder="추가 상세설명을 입력해주세요 (10자 이상)" required minlength="10"></textarea>
            <span class="desc-count" id="cnt_extra">0</span> / 10자 이상
          </div>
        </div>
      </div>
    </div>

    <!-- ===== AI업소소개글용 종합정리 (상세설명 ↔ 광고유료결제 사이) ===== -->
    <div class="ai-preview-card jobs-ai-preview" id="jobs-ai-summary-card">
      <div class="ai-preview-header" onclick="toggleJobsAiPreview()">
        <div class="ai-preview-header-left">
          <div class="ai-preview-avatar">🏢</div>
          <div>
            <div class="ai-preview-title">AI업소소개글용 종합정리</div>
            <div class="ai-preview-subtitle">실시간으로 입력한 내용이 반영됩니다</div>
          </div>
        </div>
        <div class="ai-preview-header-right">
          <span class="ai-preview-badge">제출 전 확인 · AI 업소소개글 생성에 활용됩니다</span>
          <button type="button" class="ai-preview-toggle-btn" id="jobsAiToggleBtn" aria-label="접기/펼치기">▲</button>
        </div>
      </div>
      <div class="ai-preview-body" id="jobsAiPreviewBody">
        <div class="aip-row">
          <div class="aip-label">🏢 닉네임 · 상호</div>
          <div class="aip-value" id="job-summary-name"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">📋 채용제목 · 고용형태</div>
          <div class="aip-value" id="job-summary-title"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">💰 급여조건</div>
          <div class="aip-value" id="job-summary-salary"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">📍 근무지역</div>
          <div class="aip-value" id="job-summary-region"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">💼 업종/직종</div>
          <div class="aip-value" id="job-summary-jobtype"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">✅ 편의사항</div>
          <div class="aip-value" id="job-summary-amenity"><span class="aip-empty">선택된 편의사항이 없습니다</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">🏷️ 키워드</div>
          <div class="aip-value" id="job-summary-keyword"><span class="aip-empty">선택된 키워드가 없습니다</span></div>
        </div>
        <div class="aip-row">
          <div class="aip-label">🧠 선호 MBTI</div>
          <div class="aip-value" id="job-summary-mbti"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">📍 업소 위치 및 업소 소개</div>
          <div class="aip-value" id="job-summary-desc1"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">🏭 근무환경</div>
          <div class="aip-value" id="job-summary-desc2"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">🎁 지원 혜택 및 복리후생</div>
          <div class="aip-value" id="job-summary-desc3"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">📋 지원 자격 및 우대사항</div>
          <div class="aip-value" id="job-summary-desc4"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-row aip-row-tall">
          <div class="aip-label">📝 추가 상세설명</div>
          <div class="aip-value" id="job-summary-desc5"><span class="aip-empty">—</span></div>
        </div>
        <div class="aip-footer">
          <div class="aip-footer-icon">🤖</div>
          <div class="aip-footer-text">위 정보를 기준으로 <strong>AI</strong>가 업소소개글을 자동 작성합니다. 최대한 꼼꼼히 입력해주세요.</div>
        </div>
      </div>
    </div>

    <!-- =============================
         9. 광고유료결제
    ============================= -->
    <div class="form-card sh-orange">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">💰</span>
        <span class="sec-head-title">광고유료결제</span>
        <span class="sec-head-sub">노출 서비스를 선택하여 최고의 광고효과를 누려보세요.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <!-- 총 신청금액 바 -->
        <div class="total-bar">
          <span class="total-bar-text">🛒 총 신청 금액</span>
          <span class="total-bar-amount" id="totalAmount">0 원</span>
        </div>

        <!-- 광고 서비스 표 -->
        <div style="overflow-x:auto;padding:0 0 4px;">
          <table class="ad-table" style="min-width:600px;">
            <thead>
              <tr>
                <th style="text-align:left;padding-left:18px;">서비스명</th>
                <th>유형</th>
                <th>기간/횟수</th>
                <th>금액</th>
                <th>신청</th>
              </tr>
            </thead>
            <tbody>
              <!-- 줄광고 필수영역 (핑크테두리) -->
              <tbody class="ad-line-ad-required">
              <tr class="ad-tr-highlight">
                <td class="ad-td td-svc" colspan="5">
                  <span style="font-size:13px;font-weight:700;color:var(--hot-pink);">줄광고는 필수결제 사항 입니다. 박스광고와 함께 적용 시 노출기간을 동일하게 해주세요</span>
                </td>
              </tr>
              <!-- 7. 줄광고 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">7. 줄광고 (채용정보보리스트)</div>
                  <div class="ad-svc-desc">채용정보보리스트에 배치됩니다.<br>(지역 1개 노출/자동점프 일 10회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">70,000 원<br>125,000 원<br>170,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="70000" data-label="줄광고 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="125000" data-label="줄광고 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="170000" data-label="줄광고 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>
              </tbody>
              <tbody>
              <!-- 1. 특수배너 -->
              <tr class="ad-tr" style="background:#fff8fb;">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name" style="color:#C850C0;">1. 특수배너</div>
                  <div class="ad-svc-desc">모든 페이지 최상단에 특수배너형으로<br>사이트 최상단 or 좌·우측플로팅배너에 배치됩니다.</div>
                </td>
                <td class="ad-td ad-type">—</td>
                <td class="ad-td ad-period" style="color:#C850C0;font-weight:700;">고객센터문의</td>
                <td class="ad-td ad-price">—</td>
                <td class="ad-td ad-chk">—</td>
              </tr>

              <!-- 2. 우대 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">2. 우대</div>
                  <div class="ad-svc-desc">메인 상단의 가장 눈에 띄는 위치에 배치됩니다.<br>(지역 3개 노출/자동점프 일 30회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">230,000 원<br>415,000 원<br>550,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="230000" data-label="우대 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="415000" data-label="우대 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="550000" data-label="우대 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>

              <!-- 3. 프리미엄 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">3. 프리미엄</div>
                  <div class="ad-svc-desc">메인페이지와 채용정보 중단의 위치에 배치됩니다.<br>(지역 3개 노출/자동점프 일 30회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">180,000 원<br>325,000 원<br>430,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="180000" data-label="프리미엄 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="325000" data-label="프리미엄 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="430000" data-label="프리미엄 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>

              <!-- 4. 스페셜 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">4. 스페셜</div>
                  <div class="ad-svc-desc">메인페이지와 채용정보 중단에 배치됩니다.<br>(지역 2개 노출/자동점프 일 20회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">130,000 원<br>235,000 원<br>310,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="130000" data-label="스페셜 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="235000" data-label="스페셜 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="310000" data-label="스페셜 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>

              <!-- 5. 급구 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">5. 급구</div>
                  <div class="ad-svc-desc">최상단 "급구"영역에 1줄노출, 하단 급구란에 배치됩니다.<br>(지역 2개 노출/자동점프 일 20회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">150,000 원<br>285,000 원<br>420,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="150000" data-label="급구 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="285000" data-label="급구 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="420000" data-label="급구 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>

              <!-- 6. 추천 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">6. 추천</div>
                  <div class="ad-svc-desc">메인 급구채용정보 우측에 위치하며 비교적 진한 노출모양을 가지고 있습니다.<br>(지역 2개 노출/자동점프 일 20회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">100,000 원<br>185,000 원<br>240,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="100000" data-label="추천 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="185000" data-label="추천 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="240000" data-label="추천 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>

              <!-- 굵은글씨 -->
              <tr class="ad-tr" style="background:#f0faf8;">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name" style="color:#00897B;">굵은글씨 적용</div>
                  <div class="ad-svc-desc">채용정보의 제목을 굵게 표시되어 어디든 눈에 띌수 있도록 표시</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">30,000 원<br>55,000 원<br>70,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="30000" data-label="굵은글씨 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="55000" data-label="굵은글씨 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="70000" data-label="굵은글씨 90일" onchange="calcTotal()">
                  </div>
                </td>
              </tr>

              <!-- 음선상중 안내 -->
              <tr class="ad-tr">
                <td colspan="5" class="ad-td" style="background:#e8f5e9;color:#2E7D32;font-weight:700;font-size:12px;text-align:left;">
                  💡 옵션만 결제하실 경우 광고노출이 되지않습니다.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 특수배너 & 점프옵션 안내 박스 -->
        <div class="notice-box">
          <div class="nb-title">🌟 특수배너</div>
          <div class="nb-body">
            특수배너 광고등록은 고객센터와 일정 협의 후 진행 가능합니다<br>
            특수배너 광고시 모든 광고옵션이 적용되며 모바일상단에 노출됩니다.<br><br>
            *모든 특수배너는 우대채용정보, 프리미엄채용정보, 줄광고가 함께 등록됩니다.
          </div>
          <hr class="nb-divider">
          <div class="nb-title">⚡ 점프옵션 서비스제공 안내</div>
          <div class="nb-body">
            이브알바 광고 결제시 점프옵션이 서비스로 재공됩니다.<br><br>
            <div class="jump-grid">
              <div class="jump-box">
                <div class="jump-box-title">📦 줄광고 결제시</div>
                <div class="jump-box-line"><span>30일:</span><span>점프 300회 (15,000원) 제공</span></div>
                <div class="jump-box-line"><span>60일:</span><span>점프 700회 (30,000원) 제공</span></div>
                <div class="jump-box-line"><span>90일:</span><span>점프 1200회 (50,000원) 제공</span></div>
              </div>
              <div class="jump-box">
                <div class="jump-box-title">🏆 줄광고 초과 결제시</div>
                <div class="jump-box-line"><span>30일:</span><span>점프 900회 (40,000원) 제공</span></div>
                <div class="jump-box-line"><span>60일:</span><span>점프 1900회 (75,000원) 제공</span></div>
                <div class="jump-box-line"><span>90일:</span><span>점프 3200회 (130,000원) 제공</span></div>
              </div>
            </div>
            <br>* 우대등록, 프리미엄, 스페셜, 급구, 추천 등 줄광고 외 유료광고 초과 결제시
          </div>
        </div>

        <!-- 할인 배너 -->
        <div class="discount-banner">
          <div class="discount-banner-inner">
            <div class="db-left">
              <div class="db-title">🎉 구인효과UP!<br>1년패키지출시<br>신뢰도UP!!</div>
              <div class="db-sub">장기 계약으로 더 큰 혜택을 누려보세요!</div>
              <span class="db-btn">자세히 보기 →</span>
            </div>
            <div class="db-right">
              <div class="db-rate">60일 결제시 <b>10% 할인</b></div>
              <div class="db-rate">90일 결제시 <b>20% 할인</b></div>
              <div class="db-val">360일 결제시<br>25% 할인+추가혜택!</div>
            </div>
          </div>
        </div>

        <!-- 아이콘 추가 -->
        <div class="icon-section">
          <div style="background:linear-gradient(135deg,var(--dark2),#5C0040);padding:10px 16px;border-radius:8px;margin-bottom:12px;">
            <p style="font-size:13px;color:var(--gold);font-weight:700;">⭐ 아이콘 추가</p>
            <p style="font-size:11px;color:rgba(255,255,255,.8);margin-top:4px;">7개 줄광고 옵션을 사용할 경우에 부가적으로 추가 가능한 옵션입니다. 단독으로 옵션사용시 채용광고가 노출되지 않습니다.</p>
          </div>

          <!-- 아이콘 출력 결제 -->
          <p style="font-size:13px;font-weight:700;color:#555;margin-bottom:10px;">📱 아이콘출력 결제</p>
          <div class="icon-options-grid">
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-none" checked><label for="ip-none">광고하지않음</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-free"><label for="ip-free"><span class="icon-badge icon-badge-1">💖 초보환영</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-room"><label for="ip-room"><span class="icon-badge icon-badge-2">🏡 원룸제공</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-earn"><label for="ip-earn"><span class="icon-badge icon-badge-3">💎 고급시설</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-blk"><label for="ip-blk"><span class="icon-badge icon-badge-4">블랙 관리</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-pay"><label for="ip-pay"><span class="icon-badge icon-badge-5">📱 폰비지급</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-size"><label for="ip-size"><span class="icon-badge icon-badge-6">사이즈✘</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-set"><label for="ip-set"><span class="icon-badge icon-badge-7">🎀 세트환영</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-car"><label for="ip-car"><span class="icon-badge icon-badge-8">🚗 픽업가능</span></label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-mem"><label for="ip-mem"><span class="icon-badge icon-badge-9">🙋 1회원제운영</span></label></div>
          </div>
          <div class="icon-period-box">
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-no" checked data-price="0" onchange="calcTotal()"><label for="ip-no" style="font-size:12px;">광고하지않음</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-30" data-price="30000" onchange="calcTotal()"><label for="ip-30" style="font-size:12px;color:var(--hot-pink);">기간별 30일 30,000원</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-60" data-price="55000" onchange="calcTotal()"><label for="ip-60" style="font-size:12px;color:var(--hot-pink);">기간별 60일 55,000원</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-90" data-price="70000" onchange="calcTotal()"><label for="ip-90" style="font-size:12px;color:var(--hot-pink);">기간별 90일 70,000원</label></div>
          </div>
        </div>

        <!-- 형광펜 선택 -->
        <div class="highlight-section">
          <p class="hl-title">🖊 형광펜 선택  <span style="font-size:11px;color:#aaa;">사용할 형광색을 설정하세요</span></p>
          <div style="padding:10px;background:#f9f9f9;border-radius:10px;margin-bottom:10px;">
            <div style="font-size:12px;font-weight:700;color:#555;margin-bottom:8px;">형광펜 채용정보</div>
            <div class="hl-price-row">
              <div class="hl-price-item"><input type="checkbox" id="hl-30" data-price="30000" onchange="calcTotal()"><label for="hl-30" class="hl-price-label">기간별 30일</label><span class="hl-price-val">30,000원</span></div>
              <div class="hl-price-item"><input type="checkbox" id="hl-60" data-price="55000" onchange="calcTotal()"><label for="hl-60" class="hl-price-label">기간별 60일</label><span class="hl-price-val">55,000원</span></div>
              <div class="hl-price-item"><input type="checkbox" id="hl-90" data-price="70000" onchange="calcTotal()"><label for="hl-90" class="hl-price-label">기간별 90일</label><span class="hl-price-val">70,000원</span></div>
            </div>
          </div>
          <!-- 형광펜 컬러 8종 -->
          <div class="hl-colors-grid">
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc1">
              <label for="hc1"><div class="hl-swatch" style="background:#FFE000;color:#333;">1번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc2">
              <label for="hc2"><div class="hl-swatch" style="background:#00FF90;color:#333;">2번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc3">
              <label for="hc3"><div class="hl-swatch" style="background:#FF69B4;color:#fff;">3번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc4">
              <label for="hc4"><div class="hl-swatch" style="background:#99CCFF;color:#333;">4번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc5">
              <label for="hc5"><div class="hl-swatch" style="background:#FF8C00;color:#fff;">5번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc6">
              <label for="hc6"><div class="hl-swatch" style="background:#DA70D6;color:#fff;">6번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc7">
              <label for="hc7"><div class="hl-swatch" style="background:#20B2AA;color:#fff;">7번</div></label>
            </div>
            <div class="hl-option" style="padding:5px;border:1.5px solid #f0e0e8;border-radius:7px;cursor:pointer;">
              <input type="radio" name="hl-color" id="hc8">
              <label for="hc8"><div class="hl-swatch" style="background:#FF4500;color:#fff;">8번</div></label>
            </div>
          </div>
        </div>

        <!-- 하단 총액 -->
        <div class="total-bottom-bar">
          <span class="tbb-label">💳 총 신청 금액</span>
          <span class="tbb-amount" id="totalAmount2">0 원</span>
        </div>

      </div>
    </div>

    <!-- =============================
         10. 약관 동의 + 결제
    ============================= -->
    <div class="form-card sh-dark">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📜</span>
        <span class="sec-head-title">약관 동의 및 결제</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <div class="terms-section">
          <!-- 전체 동의 -->
          <div class="terms-all-check">
            <input type="checkbox" id="agree-all" onchange="toggleAllTerms(this)">
            <label for="agree-all">✅ 전체 동의</label>
          </div>

          <!-- 개별 약관 -->
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term1">
            <label for="term1">최저임금을 준수하지 않는 경우, 공고 강제 마감 및 행정처분을 받을 수 있습니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term2">
            <label for="term2">모집 채용에서 허위 및 과장으로 작성된 내용이 확인될 경우, 공고 강제 마감 및 행정처분을 받을 수 있습니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term3">
            <label for="term3">모집 채용에서 보이스피싱, 불법 성매매, 구인사기, 채용과 관련없는 모집 등으로 추정되는 내용이 확인될 경우, 공고 게재가 불가하여 일의 마감 및 삭제될 수 있습니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term4">
            <label for="term4">소정 근로 시간 기준의 급여 외 수당이 발생했을 경우, 공고에 입력한 급여 외 추가로 지급되어야 할 수 있습니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term5">
            <label for="term5">채용절차 공정화법상 금지되는 개인정보를 요구하는 경우, 공고 강제 마감 및 행정처분을 받을 수 있습니다.</label>
          </div>
          <div class="terms-item">
            <input type="checkbox" class="term-chk" id="term6">
            <label for="term6">확인문서에 첨부한 문서의 책임은 본인에게 있습니다. 위 변조 및 도용일 경우 민 형사상의 책임이 따를 수 있습니다.</label>
          </div>
        </div>

        <!-- 결제 안내 -->
        <div class="pay-notice-bar">
          💳 결제는 PC와 모바일 모두 가능합니다.
        </div>

        <!-- 신용카드 결제 불가 안내 -->
        <div class="card-notice-bar">
          ⚠️ 신용카드결제 불가 변경 안내
        </div>

        <!-- 이력서 알림 문자 안내 -->
        <div class="alarm-link">
          📱 이력서 알림문자 순차발송 안내 <span style="color:var(--hot-pink);">▶</span>
        </div>

        <!-- 결제하기 버튼 -->
        <div class="pay-btn-wrap">
          <button type="button" class="btn-pay" onclick="checkPayment()">
            💳 결제하기
          </button>
        </div>

      </div>
    </div>

</form>

<script>
/* MBTI 다중선택: 체크박스 변경 시 카드 selected 클래스 동기화 */
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.mbti-multi input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
      var card = this.closest('.mbti-card');
      if (card) card.classList.toggle('selected', this.checked);
    });
  });
  /* 근무지역-세부지역 연동 (1~3순위) */
  filterJobRegionDetail('job_work_region_1', 'job_work_region_detail_1');
  filterJobRegionDetail('job_work_region_2', 'job_work_region_detail_2');
  filterJobRegionDetail('job_work_region_3', 'job_work_region_detail_3');
  /* AI업소소개글 종합정리 토글 */
  window.toggleJobsAiPreview = function(){
    var body = document.getElementById('jobsAiPreviewBody'), btn = document.getElementById('jobsAiToggleBtn');
    if (body) { body.classList.toggle('hide'); if (btn) { btn.classList.toggle('collapsed'); btn.textContent = body.classList.contains('hide') ? '▼' : '▲'; } }
  };
  /* 상세설명 5개 폼 글자수 카운트 */
  ['desc_location','desc_env','desc_benefit','desc_qualify','desc_extra'].forEach(function(id){
    var el = document.getElementById(id);
    var cnt = document.getElementById('cnt_'+id.replace('desc_',''));
    if(el && cnt){ el.addEventListener('input',function(){ cnt.textContent = this.value.length; }); }
  });
  /* AI 종합정리 자동 반영: 폼 변경 시 업데이트 */
  var summaryFields = ['job_nickname','job_company','job_title','job_salary_type','job_salary_amt','job_work_region_1','job_work_region_detail_1','job_work_region_2','job_work_region_detail_2','job_work_region_3','job_work_region_detail_3','job_job1','job_job2','desc_location','desc_env','desc_benefit','desc_qualify','desc_extra'];
  summaryFields.forEach(function(id){
    var el = document.getElementById(id);
    if(el){ el.addEventListener('input', updateJobsAiSummary); el.addEventListener('change', updateJobsAiSummary); }
  });
  document.querySelectorAll('input[name="employ-type"]').forEach(function(r){ r.addEventListener('change', updateJobsAiSummary); });
  document.querySelectorAll('#am-0,#am-1,#am-2,#am-3,#am-4,#am-5,#am-6,#am-7,#am-8,#am-9,#am-10,#am-11,#am-12,#am-13,#am-14,#am-15,#am-16,#am-17,#am-18,#am-19,#am-20,#am-21').forEach(function(c){ c.addEventListener('change', updateJobsAiSummary); });
  document.querySelectorAll('[id^="kw-"]').forEach(function(c){ c.addEventListener('change', updateJobsAiSummary); });
  document.querySelectorAll('input[name="mbti_prefer[]"]').forEach(function(c){ c.addEventListener('change', updateJobsAiSummary); });
  updateJobsAiSummary();
});

function updateJobsAiSummary() {
  function val(id){ var e=document.getElementById(id); return e?e.value.trim():''; }
  function txt(id){ var e=document.getElementById(id); return e?e.value.trim():'—'; }
  function sel(id){ var e=document.getElementById(id); if(!e||!e.options[e.selectedIndex]) return '—'; var o=e.options[e.selectedIndex]; return o.value?o.text:'—'; }
  function set(id,v){ var e=document.getElementById(id); if(!e) return; var s=v||'—'; e.innerHTML = s==='—'?'<span class="aip-empty">—</span>':s.replace(/\n/g,'<br>'); }
  var nick = val('job_nickname'), comp = val('job_company');
  set('job-summary-name', nick || comp ? [nick,comp].filter(Boolean).join(' · ') : null);
  var title = val('job_title'), emp = document.querySelector('input[name="employ-type"]:checked');
  set('job-summary-title', title || emp ? [title, emp?emp.nextElementSibling.textContent:''].filter(Boolean).join(' · ') : null);
  var st = sel('job_salary_type'), sa = val('job_salary_amt');
  set('job-summary-salary', st!=='—' || sa ? (st==='급여협의' || st==='—' ? (sa?sa+'원':'급여협의') : st+(sa?' '+sa+'원':'')) : null);
  var r1=sel('job_work_region_1'), d1=sel('job_work_region_detail_1'), r2=sel('job_work_region_2'), d2=sel('job_work_region_detail_2'), r3=sel('job_work_region_3'), d3=sel('job_work_region_detail_3');
  var arr=[]; if(r1!=='—'||d1!=='—') arr.push('1순위:'+(d1!=='—'?d1:r1)); if(r2!=='—'||d2!=='—') arr.push('2순위:'+(d2!=='—'?d2:r2)); if(r3!=='—'||d3!=='—') arr.push('3순위:'+(d3!=='—'?d3:r3));
  set('job-summary-region', arr.length?arr.join(' / '):null);
  var j1=sel('job_job1'), j2=sel('job_job2');
  set('job-summary-jobtype', (j1!=='—'||j2!=='—') ? [j1,j2].filter(function(x){return x!=='—';}).join(' / ') : null);
  var am = []; document.querySelectorAll('#am-0,#am-1,#am-2,#am-3,#am-4,#am-5,#am-6,#am-7,#am-8,#am-9,#am-10,#am-11,#am-12,#am-13,#am-14,#am-15,#am-16,#am-17,#am-18,#am-19,#am-20,#am-21').forEach(function(c){ if(c.checked){ var l=c.nextElementSibling; if(l) am.push(l.textContent); } });
  set('job-summary-amenity', am.length?am.join(', '):'<span class="aip-empty">선택된 편의사항이 없습니다</span>');
  var kw = []; document.querySelectorAll('[id^="kw-"]').forEach(function(c){ if(c.checked){ var l=c.nextElementSibling; if(l) kw.push(l.textContent); } });
  set('job-summary-keyword', kw.length?kw.join(', '):'<span class="aip-empty">선택된 키워드가 없습니다</span>');
  var mbti = []; document.querySelectorAll('input[name="mbti_prefer[]"]:checked').forEach(function(c){ mbti.push(c.value); });
  set('job-summary-mbti', mbti.length?mbti.join(', '):null);
  set('job-summary-desc1', txt('desc_location')===''?null:txt('desc_location'));
  set('job-summary-desc2', txt('desc_env')===''?null:txt('desc_env'));
  set('job-summary-desc3', txt('desc_benefit')===''?null:txt('desc_benefit'));
  set('job-summary-desc4', txt('desc_qualify')===''?null:txt('desc_qualify'));
  set('job-summary-desc5', txt('desc_extra')===''?null:txt('desc_extra'));
}

function filterJobRegionDetail(regionId, detailId) {
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
  }
  region.addEventListener('change', apply);
  apply();
}

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

/* 이미지 업로드 행 동적 생성 */
(function(){
  var container = document.getElementById('img-items');
  if(!container) return;
  var html = '';
  for(var i=1;i<=5;i++){
    html += '<div class="img-upload-item" style="margin-bottom:10px;">'
      + '<span class="img-upload-label-num">이미지파일'+i+'</span>'
      + '<div class="img-upload-right">'
      +   '<div class="img-upload-fileline">'
      +     '<button class="btn-file" onclick="triggerFile(\'img-f'+i+'\')">🖼 파일 선택</button>'
      +     '<input type="file" id="img-f'+i+'" accept="image/*" style="display:none" onchange="setFileName(this,\'img-fn'+i+'\')">'
      +     '<span class="file-name" id="img-fn'+i+'">선택된 파일 없음</span>'
      +     '<button class="btn-file-cancel" onclick="clearFile(\'img-f'+i+'\',\'img-fn'+i+'\')">✕ 취소</button>'
      +   '</div>'
      +   '<input class="img-desc-input" type="text" placeholder="파일설명'+i+'">'
      + '</div></div>';
  }
  container.innerHTML = html;
})();

/* 광고 금액 계산 */
function calcTotal() {
  var total = 0;
  document.querySelectorAll('[data-price]').forEach(function(chk){
    if(chk.checked) total += parseInt(chk.dataset.price);
  });
  // 형광펜
  document.querySelectorAll('#hl-30,#hl-60,#hl-90').forEach(function(chk){
    if(chk.checked) total += parseInt(chk.dataset.price);
  });
  var fmt = total.toLocaleString('ko-KR') + ' 원';
  var el1 = document.getElementById('totalAmount');
  var el2 = document.getElementById('totalAmount2');
  if(el1) el1.textContent = fmt;
  if(el2) el2.textContent = fmt;
}

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

/* 결제하기 유효성 검사 → 입금대기중으로 저장 */
function checkPayment() {
  var descIds = ['desc_location','desc_env','desc_benefit','desc_qualify','desc_extra'];
  var descLabels = ['업소 위치 및 업소 소개','근무환경','지원 혜택 및 복리후생','지원 자격 및 우대사항','추가 상세설명'];
  for(var i=0;i<descIds.length;i++){
    var el = document.getElementById(descIds[i]);
    if(el && el.value.trim().length < 10){
      alert('상세설명 "'+descLabels[i]+'"은(는) 10자 이상 입력해주세요.');
      el.focus();
      return;
    }
  }
  var allTerms = document.querySelectorAll('.term-chk');
  var checkedTerms = document.querySelectorAll('.term-chk:checked');
  if(allTerms.length !== checkedTerms.length){
    alert('모든 약관에 동의해주세요.');
    return;
  }
  var nick = document.getElementById('job_nickname');
  if(!nick || !nick.value.trim()){
    alert('닉네임(업소명)을 입력해주세요.');
    if(nick) nick.focus();
    return;
  }
  var title = document.getElementById('job_title');
  if(!title || !title.value.trim()){
    alert('채용제목을 입력해주세요.');
    if(title) title.focus();
    return;
  }
  var data = {};
  var ids = ['job_nickname','job_company','job_title','job_salary_type','job_salary_amt','job_work_region_1','job_work_region_detail_1','job_work_region_2','job_work_region_detail_2','job_work_region_3','job_work_region_detail_3','job_job1','job_job2','desc_location','desc_env','desc_benefit','desc_qualify','desc_extra'];
  ids.forEach(function(id){ var e=document.getElementById(id); data[id]=e?e.value:''; });
  var emp = document.querySelector('input[name="employ-type"]:checked');
  data['employ_type'] = emp ? emp.value : '';
  var total = 0;
  var adPeriod = 30;
  var adLabels = [];
  document.querySelectorAll('.ad-table input[type="checkbox"][data-price]:checked').forEach(function(cb){
    total += parseInt(cb.dataset.price||0);
    var lb = cb.dataset.label||'';
    if(lb) adLabels.push(lb);
    if(/줄광고\s*30/.test(lb)) adPeriod=30;
    if(/줄광고\s*60/.test(lb)) adPeriod=60;
    if(/줄광고\s*90/.test(lb)) adPeriod=90;
  });
  if(total === 0){
    alert('광고 옵션을 1개 이상 선택해주세요. (줄광고 필수)');
    return;
  }
  try {
    document.getElementById('job_data_hidden').value = btoa(unescape(encodeURIComponent(JSON.stringify(data))));
  } catch (e) {
    document.getElementById('job_data_hidden').value = JSON.stringify(data);
  }
  document.getElementById('total_amount_hidden').value = total;
  document.getElementById('ad_period_hidden').value = adPeriod;
  document.getElementById('ad_labels_hidden').value = adLabels.join(',');
  document.getElementById('fjobregister').submit();
}
</script>
