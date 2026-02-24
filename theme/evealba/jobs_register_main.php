<?php if (!defined('_GNUBOARD_')) exit; ?>

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
            <input class="fi fi-md" type="text" placeholder="업소명을 입력해주세요">
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
            <input class="fi fi-md fi-readonly" type="text" placeholder="" readonly>
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

        <!-- 핸드폰/폰핀 선택 -->
        <div class="form-row">
          <div class="form-label">핸드폰/폰핀 선택</div>
          <div class="form-cell">
            <select class="fi-select">
              <option>핸드폰번호만 사용</option>
              <option>폰핀만 사용</option>
              <option>핸드폰번호 + 폰핀 사용</option>
            </select>
          </div>
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
            <input class="fi fi-full" type="text" placeholder="채용 제목을 입력해주세요" maxlength="40">
            <span style="position:absolute;right:22px;font-size:11px;color:#aaa;">40자 제한</span>
          </div>
        </div>

        <!-- 대표이미지 -->
        <div class="form-row">
          <div class="form-label">대표이미지</div>
          <div class="form-cell col">
            <div class="file-row">
              <button class="btn-file" onclick="triggerFile('main-img')">🖼 파일 선택</button>
              <input type="file" id="main-img" accept="image/*" style="display:none" onchange="setFileName(this,'main-fn')">
              <span class="file-name" id="main-fn">선택된 파일 없음</span>
              <button class="btn-file-cancel" onclick="clearFile('main-img','main-fn')">✕ 취소</button>
            </div>
            <p class="hint">+ 권장 사이즈 : 가로 86px × 세로 46px (jpg, jpeg, png)</p>
            <p class="hint">+ 로고이미지는 결제 완료후 결제순차적으로 작업하여 등록해 드립니다.</p>
          </div>
        </div>

        <!-- 고용형태 -->
        <div class="form-row">
          <div class="form-label">고용형태 <span class="req">*</span></div>
          <div class="form-cell">
            <div class="radio-group">
              <div class="radio-item"><input type="radio" name="employ-type" id="et-hire" checked><label for="et-hire">고용</label></div>
              <div class="radio-item"><input type="radio" name="employ-type" id="et-pa"><label for="et-pa">파견</label></div>
              <div class="radio-item"><input type="radio" name="employ-type" id="et-do"><label for="et-do">도급</label></div>
              <div class="radio-item"><input type="radio" name="employ-type" id="et-we"><label for="et-we">위임</label></div>
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
              <select class="fi-select">
                <option>급여협의</option>
                <option>시급</option>
                <option>일급</option>
                <option>주급</option>
                <option>월급</option>
              </select>
              <input class="fi fi-xs" type="text" placeholder="금액 입력">
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
          <div class="form-label">근무지역 <span class="req">*</span></div>
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

    <!-- =============================
         5. 편의사항
    ============================= -->
    <div class="form-card sh-teal">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">✅</span>
        <span class="sec-head-title">편의사항</span>
        <span class="sec-head-sub">* 리스트에 출력되는 사항입니다. 2개이상 선택해주세요. 검색시 유리합니다.</span>
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
          <div class="chk-item"><input type="checkbox" id="am-16"><label for="am-16">딸당가능</label></div>
          <div class="chk-item"><input type="checkbox" id="am-17"><label for="am-17">쭈쉬가능</label></div>
          <div class="chk-item"><input type="checkbox" id="am-18"><label for="am-18">밀방없음</label></div>
          <div class="chk-item"><input type="checkbox" id="am-19"><label for="am-19">칼퇴보장</label></div>
          <div class="chk-item"><input type="checkbox" id="am-20"><label for="am-20">텃세없음</label></div>
          <div class="chk-item"><input type="checkbox" id="am-21"><label for="am-21">숙식제공</label></div>
        </div>
      </div>
    </div>

    <!-- =============================
         6. 키워드
    ============================= -->
    <div class="form-card sh-green">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">🏷</span>
        <span class="sec-head-title">키워드</span>
        <span class="sec-head-sub">* 업소에서 보장하실 수 있는 사항을 선택하시면 검색시 유리합니다.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div class="amenity-grid" style="padding:14px 18px;">
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
          <div class="chk-item"><input type="checkbox" id="kw-24"><label for="kw-24">기타 등등</label></div>
        </div>
        <!-- 키워드 직접입력 -->
        <div class="form-row" style="border-top:2px solid var(--pale-pink);">
          <div class="form-label" style="font-size:12px;">키워드 직접입력</div>
          <div class="form-cell"><input class="fi fi-full" type="text" placeholder="직접 키워드를 입력하세요 (쉼표로 구분)"></div>
        </div>
      </div>
    </div>

    <!-- =============================
         7. 업소이미지 등록
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
         8. 상세설명 (에디터)
    ============================= -->
    <div class="form-card sh-pink">
      <div class="sec-head open" onclick="toggleSec(this)">
        <span class="sec-head-icon">📝</span>
        <span class="sec-head-title">상세설명</span>
        <span class="sec-head-sub">* 필수</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">
        <div style="padding:14px 18px 16px;">
          <!-- 툴바 -->
          <div style="background:#f8f0f5;border:1.5px solid #f0e0e8;border-bottom:none;border-radius:10px 10px 0 0;padding:8px 12px;display:flex;flex-wrap:wrap;gap:3px;align-items:center;">
            <div style="display:flex;gap:2px;">
              <select style="padding:4px 6px;border:1px solid #e8d8e8;border-radius:5px;font-size:11px;background:#fff;cursor:pointer;font-family:inherit;outline:none;"><option>스타일</option><option>본문</option><option>제목1</option></select>
              <select style="padding:4px 6px;border:1px solid #e8d8e8;border-radius:5px;font-size:11px;background:#fff;cursor:pointer;font-family:inherit;outline:none;"><option>폰트</option><option>나눔고딕</option><option>맑은고딕</option></select>
              <select style="padding:4px 6px;border:1px solid #e8d8e8;border-radius:5px;font-size:11px;background:#fff;cursor:pointer;font-family:inherit;outline:none;width:52px;"><option>크기</option><option>10</option><option>12</option><option>14</option><option>16</option><option>18</option></select>
            </div>
            <div style="width:1px;height:20px;background:#e0d0e0;margin:0 4px;"></div>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;font-weight:700;color:#555;cursor:pointer;"><b>B</b></button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;"><i>I</i></button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;"><u>U</u></button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;"><s>S</s></button>
            <div style="width:1px;height:20px;background:#e0d0e0;margin:0 4px;"></div>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">🖼</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">🔗</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">😊</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">⊞</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">▶</button>
            <div style="width:1px;height:20px;background:#e0d0e0;margin:0 4px;"></div>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">≡</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">☰</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">1.</button>
            <button style="padding:4px 7px;background:#fff;border:1px solid #e8d8e8;border-radius:5px;font-size:12px;cursor:pointer;">•</button>
          </div>
          <!-- 에디터 본문 -->
          <textarea style="width:100%;min-height:260px;padding:14px 16px;border:1.5px solid #f0e0e8;border-top:none;border-radius:0 0 10px 10px;font-size:13px;line-height:1.8;color:#333;background:#fff;resize:vertical;outline:none;font-family:inherit;" placeholder="업소 상세 설명을 입력해주세요.&#10;&#10;• 업소 소개 및 특징&#10;• 근무 환경&#10;• 지원 혜택 및 복리후생&#10;• 지원 자격 및 우대사항"></textarea>
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
              <!-- 필수 결제 안내 -->
              <tr class="ad-tr-highlight">
                <td class="ad-td td-svc" colspan="5">
                  <span style="background:var(--hot-pink);color:#fff;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:900;margin-right:8px;">필수결제사항</span>
                  <span style="font-size:13px;font-weight:700;color:var(--dark);">박스광고와 함께 적용 시 노출기간을 동일하게 해주세요</span>
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

              <!-- 1. 특수배너 -->
              <tr class="ad-tr" style="background:#fff8fb;">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name" style="color:#C850C0;">1. 특수배너</div>
                  <div class="ad-svc-desc">모든 페이지 메뉴 상단에 특수배너형으로<br>사이트 최상단에 배치됩니다.</div>
                </td>
                <td class="ad-td ad-type">—</td>
                <td class="ad-td ad-period" style="color:#C850C0;font-weight:700;">유료광고 최대건수를 넘었습니다<br>(23건 현재 광고중)</td>
                <td class="ad-td ad-price">—</td>
                <td class="ad-td ad-chk">—</td>
              </tr>

              <!-- 2. 우대 -->
              <tr class="ad-tr">
                <td class="ad-td td-svc">
                  <div class="ad-svc-name">2. 우대</div>
                  <div class="ad-svc-desc">메인 중단의 가장 눈에 띄는 위치에 배치됩니다.<br>(지역 3개 노출/자동점프 일 30회 설정 제공, 이력서 등록시 알림 문자 즉시 발송)</div>
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
                  <div class="ad-svc-desc">메인페이지 우대등록 하단의 위치에 배치됩니다.<br>(지역 3개 노출/자동점프 일 30회 설정 제공, 이력서 등록시 알림 문자 즉시 발송)</div>
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
                  <div class="ad-svc-desc">채용정보,지역별 채용페이지 중단에 배치됩니다.<br>(지역 2개 노출/자동점프 일 20회 설정 제공)</div>
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
                  <div class="ad-svc-desc">기본 노출모양이 진하며, 메인좌측하단 위치에 배치됩니다.<br>(지역 2개 노출/자동점프 일 20회 설정 제공)</div>
                </td>
                <td class="ad-td ad-type">기간별</td>
                <td class="ad-td ad-period">30 일<br>60 일<br>90 일</td>
                <td class="ad-td ad-price">100,000 원<br>185,000 원<br>240,000 원</td>
                <td class="ad-td ad-chk">
                  <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                    <input type="checkbox" data-price="100000" data-label="급구 30일" onchange="calcTotal()">
                    <input type="checkbox" data-price="185000" data-label="급구 60일" onchange="calcTotal()">
                    <input type="checkbox" data-price="240000" data-label="급구 90일" onchange="calcTotal()">
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
                  💡 옵션상중만 결제하실 경우 광고노출이 되지않습니다.
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
            특수배너 광고시 모든 광고옵션이 적용되며 <span class="red">모바일상단에 노출됩니다.</span><br><br>
            <span class="red">특수배너 예약은 지금 바로 고객센터로 문의 주세요.</span>
            <span class="phone">고객센터 1588-0000</span>
            * 특수배너 예약은 대기자 상황에 따라 달라질 수 있습니다.
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
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-none" checked><label for="ip-none">광고하지않음</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-free"><label for="ip-free" style="color:var(--hot-pink);font-weight:700;">♥ 초보환영</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-room"><label for="ip-room" style="color:#9C27B0;font-weight:700;">❤️ 원룸제공</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-earn"><label for="ip-earn" style="color:var(--orange);font-weight:700;">🌟 고금시설</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-blk"><label for="ip-blk" style="background:#333;color:#fff;padding:1px 6px;border-radius:3px;font-size:10px;font-weight:900;">블랙 관리</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-pay"><label for="ip-pay" style="color:#E91E63;font-weight:700;">💸 폰비♥ 지급</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-size"><label for="ip-size" style="color:#F44336;font-weight:700;">사이즈 ✕</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-set"><label for="ip-set" style="color:#4CAF50;font-weight:700;">🌿 셋트환영</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-car"><label for="ip-car" style="color:#2196F3;font-weight:700;">🚗 픽업가능</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay" id="ip-mem"><label for="ip-mem" style="color:#FF9800;font-weight:700;">👥 1회원제운영</label></div>
          </div>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;background:#f5f5f5;border-radius:8px;padding:10px;">
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-no" checked><label for="ip-no" style="font-size:12px;">광고하지않음</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-30"><label for="ip-30" style="font-size:12px;color:var(--hot-pink);">기간별 30일 30,000원</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-60"><label for="ip-60" style="font-size:12px;color:var(--hot-pink);">기간별 60일 55,000원</label></div>
            <div class="radio-item"><input type="radio" name="icon-pay-opt" id="ip-90"><label for="ip-90" style="font-size:12px;color:var(--hot-pink);">기간별 90일 70,000원</label></div>
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
          <button class="btn-pay" onclick="checkPayment()">
            💳 결제하기
          </button>
        </div>

      </div>
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

/* 결제하기 유효성 검사 */
function checkPayment() {
  var allTerms = document.querySelectorAll('.term-chk');
  var checkedTerms = document.querySelectorAll('.term-chk:checked');
  if(allTerms.length !== checkedTerms.length){
    alert('모든 약관에 동의해주세요.');
    return;
  }
  alert('결제 페이지로 이동합니다.');
}
</script>
