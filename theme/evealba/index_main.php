<?php
/**
 * 이브알바 메인 영역
 * - DB 연동: ongoing 광고를 유형별로 조회하여 표시
 * - DB에 건이 없으면 기존 더미 HTML 유지
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH.'/extend/jobs_list_helper.php');
}

$_idx_udae   = function_exists('get_jobs_by_type') ? get_jobs_by_type('우대', 8) : array();
$_idx_premium = function_exists('get_jobs_by_type') ? get_jobs_by_type('프리미엄', 5) : array();
$_idx_special = function_exists('get_jobs_by_type') ? get_jobs_by_type('스페셜', 6) : array();
$_idx_urgent  = function_exists('get_jobs_by_type') ? get_jobs_by_type('급구', 3) : array();
$_idx_recomm  = function_exists('get_jobs_by_type') ? get_jobs_by_type('추천', 4) : array();
?>
<?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>

<!-- 빠른 통계 -->
<div class="quick-stats">
  <div class="stat-card">
    <div class="stat-icon">💼</div>
    <div class="stat-label">오늘 채용공고</div>
    <div class="stat-value">3,427</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">📄</div>
    <div class="stat-label">등록 이력서</div>
    <div class="stat-value">12,841</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🏢</div>
    <div class="stat-label">가입 업소</div>
    <div class="stat-value">8,920</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">👩</div>
    <div class="stat-label">오늘 접속자</div>
    <div class="stat-value">24,153</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">✅</div>
    <div class="stat-label">오늘 매칭</div>
    <div class="stat-value">1,203</div>
  </div>
</div>

<!-- 공지 -->
<div class="notice-bar">
  <span class="notice-label">📢 공지</span>
  <div class="notice-text">
    <a href="#">[공지] 이브알바 신규 서비스 오픈 이벤트 안내 · 채용공고 등록 시 프리미엄 무료 업그레이드 혜택!</a>
  </div>
</div>

<!-- 모바일 전용 추천업소 (PC에서는 숨김, 태블릿 이하에서 표시) -->
<div class="mobile-recommend">
  <div class="section-header">
    <h2 class="section-title">💎 추천업소</h2>
  </div>
  <div class="mobile-recommend-grid">
    <div class="mobile-rec-card">
      <div class="mobile-rec-banner g12">동탄스카이<br>아이퍼블릭<b>60분 TC12만원</b></div>
      <div class="mobile-rec-info">
        <div class="mobile-rec-name">동탄스카이 아이퍼블릭</div>
        <div class="mobile-rec-wage">자유복장 · TC12만원</div>
      </div>
    </div>
    <div class="mobile-rec-card">
      <div class="mobile-rec-banner g1">일프로 &amp; 텐카페<b>300만원 보장</b></div>
      <div class="mobile-rec-info">
        <div class="mobile-rec-name">일프로 · 텐카페</div>
        <div class="mobile-rec-wage">300만원 보장</div>
      </div>
    </div>
    <div class="mobile-rec-card">
      <div class="mobile-rec-banner" style="background:linear-gradient(135deg,#1A0010,#FF1B6B)">당일<b>백만 UP</b></div>
      <div class="mobile-rec-info">
        <div class="mobile-rec-name">당일 백만원 UP 이벤트</div>
        <div class="mobile-rec-wage">기간 한정 특별 혜택</div>
      </div>
    </div>
    <div class="mobile-rec-card">
      <div class="mobile-rec-banner g11">강남 VIP<b>순수테이블 2H</b></div>
      <div class="mobile-rec-info">
        <div class="mobile-rec-name">강남짬오 이태곤대표</div>
        <div class="mobile-rec-wage">면접 후 협의</div>
      </div>
    </div>
  </div>
</div>

<!-- 우대채용정보 -->
<div class="section-wrap">
  <div class="section-header">
    <h2 class="section-title">우대채용정보</h2>
    <a href="#" class="section-more">더보기 →</a>
  </div>
  <div class="featured-grid">
<?php if (!empty($_idx_udae)) { foreach ($_idx_udae as $_u) { render_job_card($_u); } } else { ?>
    <div class="job-card">
      <div class="job-card-banner g1"><span>👑 강남 하이퍼블릭<br>아우라</span></div>
      <div class="hot-badge">HOT</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">경기</span>안양시 룸싸롱</div>
        <div class="job-desc">♥안양하이퍼TC16♥안양1등 이.</div>
        <div class="job-card-footer">
          <span class="job-wage">160,000원</span>
          <span class="job-badge"><span class="crown-gold">👑</span>24회 1170일</span>
        </div>
      </div>
    </div>
    <div class="job-card">
      <div class="job-card-banner g2"><span>💜 부천 하이퍼블릭<br>메쎄</span></div>
      <div class="hot-badge">HOT</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">경기</span>부천시 룸싸롱</div>
        <div class="job-desc">1등·패츠X최고조건·손님많고객.</div>
        <div class="job-card-footer">
          <span class="job-wage">150,000원</span>
          <span class="job-badge"><span class="crown-gold">👑</span>17회 1290일</span>
        </div>
      </div>
    </div>
    <div class="job-card">
      <div class="job-card-banner g3"><span>❤ 파주최고TC<br>REINA</span></div>
      <div class="hot-badge">HOT</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">경기</span>파주시 노래주점</div>
        <div class="job-desc">●●● 퍼블릭 1시간 10만원 ●●●</div>
        <div class="job-card-footer">
          <span class="job-wage">100,000원</span>
          <span class="job-badge"><span class="crown-silver">🥈</span>1회 30일</span>
        </div>
      </div>
    </div>
    <div class="job-card">
      <div class="job-card-banner g4"><span>💎 화류지옥<br>서울</span></div>
      <div class="new-badge">NEW</div>
      <div class="job-card-body">
        <div class="job-card-location"><span class="job-loc-badge">서울</span>기타</div>
        <div class="job-desc">♥최고패이♥화류지옥♥</div>
        <div class="job-card-footer">
          <span class="job-wage">500,000원</span>
          <span class="job-badge"><span class="crown-gold">👑</span>114회 3420일</span>
        </div>
      </div>
    </div>
<?php } ?>
  </div>
</div>

<!-- 프리미엄채용정보 -->
<div class="section-wrap">
  <div class="section-header">
    <h2 class="section-title">프리미엄채용정보</h2>
    <a href="#" class="section-more">더보기 →</a>
  </div>
<?php if (!empty($_idx_premium)) { ?>
  <div class="premium-grid">
    <?php foreach ($_idx_premium as $_p) { render_premium_card($_p); } ?>
  </div>
<?php } else { include_once dirname(__FILE__).'/inc/ads_premium.php'; } ?>
</div>

<!-- 커뮤니티 + 인재정보 -->
<div class="community-resume-row">
  <div class="tab-section">
    <div class="tab-header">
      <button class="tab-btn active">베스트글</button>
      <button class="tab-btn">밤문화이야기</button>
      <button class="tab-btn">단짝찾기</button>
      <button class="tab-btn">법률상담</button>
    </div>
    <div class="tab-content">
      <div class="community-item">
        <span class="comm-badge badge-best">BEST</span>
        <span class="comm-title">3부 강한 하이퍼 어디에요?</span>
        <span class="comm-time">방금</span>
      </div>
      <div class="community-item">
        <span class="comm-badge badge-new">NEW</span>
        <span class="comm-title">근데 일하면서 느낀게... 오히려 짠따언니들 많은거같아요</span>
        <span class="comm-time">3분</span>
      </div>
      <div class="community-item">
        <span class="comm-badge badge-best">BEST</span>
        <span class="comm-title">신림 퇴근차 해줘요? 아니면 차비?</span>
        <span class="comm-time">15분</span>
      </div>
      <div class="community-item">
        <span class="comm-badge badge-night">🌙</span>
        <span class="comm-title">미래고민 - 서울외곽 노도해서 달에 적금만 300씩</span>
        <span class="comm-time">30분</span>
      </div>
      <div class="community-item">
        <span class="comm-badge badge-new">NEW</span>
        <span class="comm-title">손님으로만났는데 알고보니 선배언니였어요</span>
        <span class="comm-time">1시간</span>
      </div>
    </div>
  </div>
  <div class="resume-table">
    <table>
      <thead>
        <tr>
          <th>이름</th>
          <th>나이/성별</th>
          <th>제목</th>
          <th>희망급여</th>
          <th>등록일</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="resume-name">수○○</td><td>22/여</td>
          <td class="resume-title"><a href="#">일구해요 🆕</a></td>
          <td><span class="wage-tag wage-neg">면접협의</span></td><td>02-22</td>
        </tr>
        <tr>
          <td class="resume-name">힘○○</td><td>27/여</td>
          <td class="resume-title"><a href="#">77사이즈 구해요 🆕</a></td>
          <td><span class="wage-tag wage-neg">면접협의</span></td><td>02-22</td>
        </tr>
        <tr>
          <td class="resume-name">고○○</td><td>27/여</td>
          <td class="resume-title"><a href="#">165 68kg 20대후반 일자리 구해요 🆕</a></td>
          <td><span class="wage-tag wage-neg">면접협의</span></td><td>02-21</td>
        </tr>
        <tr>
          <td class="resume-name">잔○○</td><td>35/여</td>
          <td class="resume-title"><a href="#">ㅇㅍ구해요 🆕</a></td>
          <td><span class="wage-tag wage-fixed">400만원</span></td><td>02-21</td>
        </tr>
        <tr>
          <td class="resume-name">ㅇ○○</td><td>23/여</td>
          <td class="resume-title"><a href="#">일 구해봐용 ㅎㅎ 🆕</a></td>
          <td><span class="wage-tag wage-neg">면접협의</span></td><td>02-21</td>
        </tr>
        <tr>
          <td class="resume-name">보○○</td><td>33/여</td>
          <td class="resume-title"><a href="#">기타구해요 🆕</a></td>
          <td><span class="wage-tag wage-neg">면접협의</span></td><td>02-21</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- 스페셜채용정보 -->
<div class="section-wrap">
  <div class="section-header">
    <h2 class="section-title">스페셜채용정보</h2>
    <a href="#" class="section-more">더보기 →</a>
  </div>
<?php if (!empty($_idx_special)) { ?>
  <div class="special-grid">
    <?php foreach ($_idx_special as $_s) { render_premium_card($_s, 'special-card'); } ?>
  </div>
<?php } else { include_once dirname(__FILE__).'/inc/ads_special.php'; } ?>
</div>

<!-- 급구채용 + 추천채용 -->
<div class="urgency-recommend-row">
  <div>
    <div class="section-header">
      <h2 class="section-title" style="font-size:16px">급구채용</h2>
    </div>
    <div class="urgency-list">
<?php if (!empty($_idx_urgent)) { foreach ($_idx_urgent as $_ug) { render_urgency_card($_ug); } } else { ?>
      <div class="urgency-card">
        <div class="urgency-name">♥화류지옥♥</div>
        <div class="urgency-area">서울</div>
        <div class="urgency-desc">♥최고패이♥화류지옥♥</div>
        <div class="urgency-wage">500,000원 <span>· 114회 3420일</span></div>
      </div>
      <div class="urgency-card">
        <div class="urgency-name">강남짬오❤이태곤대표</div>
        <div class="urgency-area">서울 강남구</div>
        <div class="urgency-desc">♥순수테이블♥ 2시간40분!</div>
        <div class="urgency-wage">면접 후 협의 <span>· 42회 1260일</span></div>
      </div>
      <div class="urgency-card">
        <div class="urgency-name">타임</div>
        <div class="urgency-area">경기 광명시 · 노래주점</div>
        <div class="urgency-desc">♥철산1등 타가게/고정/반고정.</div>
        <div class="urgency-wage">130,000원 <span>· 1회 60일</span></div>
      </div>
<?php } ?>
    </div>
  </div>
  <div>
    <div class="section-header">
      <h2 class="section-title" style="font-size:16px">추천채용</h2>
    </div>
    <div class="recommend-list">
<?php if (!empty($_idx_recomm)) { foreach ($_idx_recomm as $_rc) { render_recommend_card($_rc); } } else { ?>
      <div class="recommend-card">
        <div>
          <div class="rec-name">♥파주최고TC♥ <span class="rec-area">경기 파주시</span></div>
          <div class="rec-desc">●●● 퍼블릭 1시간 10만원 ●●● 파주 최고TC</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">100,000원</div>
          <div class="rec-meta">노래주점 · 1회 30일</div>
        </div>
      </div>
      <div class="recommend-card">
        <div>
          <div class="rec-name">구구단 신제니 <span class="rec-area">서울</span></div>
          <div class="rec-desc">구구단 신제니 ♡ 정동 여자 마담 ♡ 하이퍼캠오!</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">150,000원</div>
          <div class="rec-meta">룸싸롱 · 1회 90일</div>
        </div>
      </div>
      <div class="recommend-card">
        <div>
          <div class="rec-name">동탄하퍼대표 <span class="rec-area">경기</span></div>
          <div class="rec-desc">자유복장하이퍼♥TC12♥60분♥당일지급</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">3,000,000원</div>
          <div class="rec-meta">룸싸롱 · 51회 1530일</div>
        </div>
      </div>
      <div class="recommend-card">
        <div>
          <div class="rec-name">아우라 하이퍼블릭 <span class="rec-area">경기</span></div>
          <div class="rec-desc">♥수원1번하이퍼블릭♥ 아우라 대표가 환영합.</div>
        </div>
        <div class="rec-right">
          <div class="rec-wage">면접 후 협의</div>
          <div class="rec-meta">노래주점 · 3회 540일</div>
        </div>
      </div>
<?php } ?>
    </div>
  </div>
</div>
