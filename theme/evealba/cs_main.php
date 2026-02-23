<?php
/**
 * 고객센터 메인 영역 (eve_alba_cs.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
    <!-- CS 히어로 배너 -->
    <div class="cs-hero">
      <div class="cs-hero-text">
        <h1>🎀 고객지원 <small style="font-size:14px;font-weight:400;color:rgba(255,255,255,.75);display:inline;font-family:'Outfit',sans-serif;">CUSTOMER CENTER</small><br>
        <small>고객님의 소리를 귀담아 듣겠습니다.<br>더 낮은 자세로 임하겠습니다.<br>여러분의 소중한 의견을 담아주세요.</small></h1>
      </div>
      <div class="cs-hero-phone">
        <span class="phone-label">📞 전화 상담</span>
        <span class="phone-num">1588-0000</span>
        <div class="phone-hours">평일 09:30~19:00 · 점심 12:00~13:30<br>*공휴일·일요일 근무하지 않습니다.</div>
        <div style="margin-top:10px;">
          <span style="background:#FEE500;color:#333;padding:5px 14px;border-radius:14px;font-size:12px;font-weight:900;display:inline-block;">💬 카카오톡 : EvéAlba</span>
        </div>
      </div>
    </div>

    <!-- 3대 진입 카드 -->
    <div class="cs-entry-grid">
      <div class="cs-entry-card" onclick="document.getElementById('notice-section').scrollIntoView({behavior:'smooth'})">
        <div class="cs-entry-icon cei-pink">📢</div>
        <div class="cs-entry-title">NOTICE 공지사항</div>
        <div class="cs-entry-desc">사이트의 공지내용을<br>알려드립니다.</div>
        <span class="cs-entry-btn">공지사항 게시판 →</span>
      </div>
      <div class="cs-entry-card" onclick="document.getElementById('qna-section').scrollIntoView({behavior:'smooth'})">
        <div class="cs-entry-icon cei-purple">💬</div>
        <div class="cs-entry-title">Q&amp;A 문의게시판</div>
        <div class="cs-entry-desc">무엇이든 물어보세요!<br>광고문의 &amp; 일반문의</div>
        <span class="cs-entry-btn">광고문의 &amp; 일반문의 →</span>
      </div>
      <div class="cs-entry-card" onclick="document.getElementById('faq-section').scrollIntoView({behavior:'smooth'})">
        <div class="cs-entry-icon cei-blue">❓</div>
        <div class="cs-entry-title">FAQ 자주하는 질문</div>
        <div class="cs-entry-desc">쉽게 한눈에 확인하는<br>궁금증!</div>
        <span class="cs-entry-btn">FAQ 게시판 →</span>
      </div>
    </div>

    <!-- 공지사항 + FAQ (2열) -->
    <div class="cs-grid-2">

      <!-- 공지사항 -->
      <div id="notice-section" class="cs-board-card bh-notice">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">📢</span>
            <div>
              <div class="cs-board-name">공지사항</div>
              <div class="cs-board-desc">운영팀 공지 · 이벤트 안내</div>
            </div>
          </div>
          <a href="#" class="board-more">더보기 →</a>
        </div>
        <div class="cs-post-list">
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">2026 설연휴 휴무 안내</a>
            <span class="post-meta">2026-01-20</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">2026년 설레는 설맞이 댓글 이벤트</a>
            <span class="post-meta">2026-01-15</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">2026년 신정 및 사내교육 휴무 공지</a>
            <span class="post-meta">2025-12-30</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">2025년 이브알바 워크숍 및 크리스마스 휴무 안내</a>
            <span class="post-meta">2025-12-20</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-new">NEW</span>
            <a href="#" class="post-title">사업자 관련 서류 미제출 안내</a>
            <span class="post-meta">2025-12-10</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-hot">HOT</span>
            <a href="#" class="post-title">퀸퀸퀸을 찾아라! 마음 따뜻해지는 글, 나만 아는 꿀팁, 공감가는 글 작성 이벤...</a>
            <span class="post-meta">2025-12-05</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">굿바이 2025! 따뜻한 댓글 이벤트</a>
            <span class="post-meta">2025-11-30</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">채용공고 열람 이벤트 안내</a>
            <span class="post-meta">2025-11-20</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-notice">공지</span>
            <a href="#" class="post-title">채용공고 신규 등록 및 수정시 사업장 위치 항목 추가 안내</a>
            <span class="post-meta">2025-11-15</span>
          </div>
        </div>
      </div>

      <!-- FAQ -->
      <div id="faq-section" class="cs-board-card">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">❓</span>
            <div>
              <div class="cs-board-name">FAQ 자주하는 질문</div>
              <div class="cs-board-desc">클릭하면 답변이 펼쳐집니다</div>
            </div>
          </div>
          <a href="#" class="board-more">더보기 →</a>
        </div>
        <div class="faq-list">
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">개명 했을 경우 어떻게 해야할까요?</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">개명 후 회원정보 수정 페이지에서 이름을 변경해 주세요. 본인인증이 필요하며, 인증 과정에서 변경된 이름이 반영됩니다.</div>
          </div>
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">이브알바 PC화면이 제대로 안보여요</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">브라우저 캐시 삭제 후 새로고침 해주세요 (Ctrl+Shift+Del). 크롬 최신 버전 사용을 권장드립니다. 계속 문제가 있으시면 고객센터로 연락 주세요.</div>
          </div>
          <div class="faq-item open">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">중복 가입이 가능한가요?</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">동일한 휴대폰 번호로는 중복 가입이 불가합니다. 이미 가입된 번호로 재가입 시도 시 기존 계정으로 로그인 안내가 표시됩니다.</div>
          </div>
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">회원정보 수정에서 휴대전화 인증을 받았는데 정보 수정이 안돼요</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">인증 후 5분 이내에 수정을 완료해 주세요. 시간 초과 시 인증이 만료됩니다. 계속 문제가 있으시면 1:1 문의를 이용해 주세요.</div>
          </div>
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">나의 채용정보 노출여부와 유료옵션 확인하기</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">마이페이지 → 채용공고 관리에서 확인하실 수 있습니다. 유료 옵션(우대/프리미엄/스페셜 등) 만료일도 같은 페이지에서 확인 가능합니다.</div>
          </div>
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">구인광고 등록을 하려면 어떻게 해야 하나요?</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">상단 [채용공고 등록] 버튼을 클릭하시거나 고객센터(1588-0000)로 연락 주시면 안내해 드립니다. 기업회원 가입 후 바로 등록 가능합니다.</div>
          </div>
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">이브알바에서 카드결제했는데 카드명세서의 내용이 다르나요?</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">PG사 정책에 따라 카드명세서에는 결제대행사명이 표기될 수 있습니다. 이는 정상 결제 처리된 것이므로 안심하셔도 됩니다.</div>
          </div>
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
              <div class="faq-q-icon">Q</div>
              <div class="faq-q-text">결제오류 발생시 참고하세요!!</div>
              <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">결제 오류 발생 시 고객센터(1588-0000)로 연락해 주시거나 문의게시판에 오류 내용과 시간을 남겨주세요. 빠르게 처리해 드리겠습니다.</div>
          </div>
        </div>
      </div>

    </div>

    <!-- Q&A 문의게시판 + 디자인 문의 (2열) -->
    <div class="cs-grid-2">

      <!-- Q&A 문의게시판 -->
      <div id="qna-section" class="cs-board-card bh-qna">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">💬</span>
            <div>
              <div class="cs-board-name">광고문의 &amp; 일반문의</div>
              <div class="cs-board-desc">궁금하신 점을 남겨주세요</div>
            </div>
          </div>
          <div style="display:flex;gap:6px;">
            <a href="#" class="board-more">더보기</a>
            <a href="#" class="board-write-btn">✏️ 문의하기</a>
          </div>
        </div>
        <div class="cs-post-list">
          <div class="cs-post-item">
            <span class="post-badge pb-answer">답변완료</span>
            <a href="#" class="post-title">광고문의</a>
            <span class="post-meta">2026-02-22</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-22</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-wait">대기중</span>
            <a href="#" class="post-title">정지 사유</a>
            <span class="post-meta">2026-02-21</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-21</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-new">NEW</span>
            <a href="#" class="post-title">일반문의</a>
            <span class="post-meta">2026-02-20</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-20</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-wait">대기중</span>
            <a href="#" class="post-title">채용공고 등록 문의드립니다</a>
            <span class="post-meta">2026-02-19</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-answer">답변완료</span>
            <a href="#" class="post-title">광고 옵션 변경 관련 문의</a>
            <span class="post-meta">2026-02-18</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-18</span>
          </div>
        </div>
      </div>

      <!-- 디자인 문의 -->
      <div class="cs-board-card bh-design">
        <div class="cs-board-header">
          <div class="cs-board-title-row">
            <span class="cs-board-icon">🎨</span>
            <div>
              <div class="cs-board-name">디자인 문의</div>
              <div class="cs-board-desc">배너·이미지 수정 요청</div>
            </div>
          </div>
          <div style="display:flex;gap:6px;">
            <a href="#" class="board-more">더보기</a>
            <a href="#" class="board-write-btn">✏️ 문의하기</a>
          </div>
        </div>
        <div class="cs-post-list">
          <div class="cs-post-item">
            <span class="post-badge pb-new">NEW</span>
            <a href="#" class="post-title">상세이미지 수정 모청드립니다</a>
            <span class="post-meta">2026-02-23</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-23</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-wait">대기중</span>
            <a href="#" class="post-title">텔레아이디 변경</a>
            <span class="post-meta">2026-02-22</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-22</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-new">NEW</span>
            <a href="#" class="post-title">디자인변경</a>
            <span class="post-meta">2026-02-21</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다</a>
            <span class="post-meta">2026-02-21</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-answer">답변완료</span>
            <a href="#" class="post-title">배너 이미지 사이즈 문의</a>
            <span class="post-meta">2026-02-20</span>
          </div>
          <div class="cs-post-item reply-item">
            <span class="reply-arrow">└</span>
            <span class="post-badge pb-answer">답변</span>
            <a href="#" class="post-title">답변드립니다 (배너 규격 안내)</a>
            <span class="post-meta">2026-02-20</span>
          </div>
          <div class="cs-post-item">
            <span class="post-badge pb-wait">대기중</span>
            <a href="#" class="post-title">우대 배너 색상 변경 요청드립니다</a>
            <span class="post-meta">2026-02-19</span>
          </div>
        </div>
      </div>

    </div>
