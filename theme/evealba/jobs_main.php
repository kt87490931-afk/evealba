<?php
/**
 * 채용정보 메인 영역 (eve_alba_jobs.html 100% 동일)
 * - 디자인만 구현, 기능 연동은 추후 진행
 */
if (!defined('_GNUBOARD_')) exit;
?>
    <!-- 검색 필터 박스 -->
    <div class="filter-box">
      <div class="filter-title">채용정보 검색하기 &nbsp;<small style="font-size:11px;font-weight:500;color:#aaa">조건 하나만 선택해도 검색이 가능합니다!</small></div>
      <div class="filter-rows">
        <div class="filter-row">
          <span class="filter-label">▸ 지역</span>
          <select class="filter-select">
            <option>지역선택</option>
            <option>서울</option>
            <option>경기</option>
            <option>인천</option>
            <option>부산</option>
            <option>대구</option>
            <option>광주</option>
            <option>대전</option>
            <option>울산</option>
            <option>강원</option>
            <option>충청</option>
            <option>전라</option>
            <option>경상</option>
            <option>제주</option>
          </select>
          <select class="filter-select">
            <option>세부지역선택</option>
            <option>강남구</option>
            <option>서초구</option>
            <option>마포구</option>
            <option>홍대</option>
            <option>이태원</option>
            <option>신사동</option>
          </select>
          &nbsp;&nbsp;
          <span class="filter-label">▸ 직종</span>
          <select class="filter-select">
            <option>직종선택</option>
            <option>룸싸롱</option>
            <option>하이퍼블릭</option>
            <option>퍼블릭</option>
            <option>주점</option>
            <option>바</option>
            <option>다방</option>
            <option>마사지</option>
            <option>기타</option>
          </select>
          <select class="filter-select">
            <option>세부직종선택</option>
            <option>아가씨</option>
            <option>초미씨</option>
            <option>미씨</option>
            <option>TC</option>
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">▸ 편의</span>
          <select class="filter-select">
            <option>--선택--</option>
            <option>선불가능</option>
            <option>순번확실</option>
            <option>원룸제공</option>
            <option>만근비지원</option>
            <option>성형지원</option>
            <option>출퇴근지원</option>
            <option>식사제공</option>
            <option>팁별도</option>
            <option>인센티브</option>
            <option>갯수보장</option>
            <option>초이스없음</option>
            <option>당일지급</option>
          </select>
          &nbsp;&nbsp;
          <span class="filter-label">▸ 고용</span>
          <select class="filter-select">
            <option>고용형태</option>
            <option>고용</option>
            <option>파견</option>
            <option>도급</option>
            <option>위임</option>
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">▸ 키워드</span>
          <input class="filter-input" type="text" placeholder="제목, 업체명, 닉네임, 키워드로 검색 가능합니다.">
        </div>
      </div>
      <div class="filter-actions">
        <button type="button" class="btn-search">🔍 검색</button>
        <button type="button" class="btn-reset">초기화</button>
      </div>
    </div>

    <!-- 우대채용정보 -->
    <div class="section-wrap">
      <div class="section-header">
        <h2 class="section-title">💎 우대등록 채용정보</h2>
        <div class="section-actions">
          <a href="#" class="section-more">더보기 →</a>
          <button type="button" class="btn-post-ad">광고신청</button>
        </div>
      </div>
      <div class="featured-grid">
        <div class="job-card">
          <div class="job-card-banner g1"><span>👑 강남 하이퍼블릭<br>아우라</span></div>
          <div class="hot-badge">HOT</div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">서울</span>강남구 룸싸롱</div>
            <div class="job-desc">♥하이퍼TC16♥강남1등 이브!</div>
            <div class="job-card-footer">
              <span class="job-wage">160,000원</span>
              <span class="job-badge"><span class="crown-gold">👑</span> 24회 1170일</span>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="job-card-banner g2"><span>💜 부천 하이퍼블릭<br>메메</span></div>
          <div class="hot-badge">HOT</div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">경기</span>부천시 룸싸롱</div>
            <div class="job-desc">1등 패초X최고조건 손님많고객!</div>
            <div class="job-card-footer">
              <span class="job-wage">150,000원</span>
              <span class="job-badge"><span class="crown-gold">👑</span> 17회 1290일</span>
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
              <span class="job-badge"><span class="crown-silver">🥈</span> 1회 30일</span>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="job-card-banner g4"><span>🔵 화류지옥<br>서울</span></div>
          <div class="new-badge">NEW</div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">서울</span>기타</div>
            <div class="job-desc">♥최고패이♥화류지옥♥</div>
            <div class="job-card-footer">
              <span class="job-wage">500,000원</span>
              <span class="job-badge"><span class="crown-gold">👑</span> 114회 3420일</span>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="job-card-banner g5"><span>🌿 강남 유엔미<br>강인한 사장</span></div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">서울</span>강남구 룸싸롱</div>
            <div class="job-desc">★하루100~160★강남1등!</div>
            <div class="job-card-footer">
              <span class="job-wage">600,000원</span>
              <span class="job-badge"><span class="crown-gold">👑</span> 76회 2970일</span>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="job-card-banner g9"><span>🎀 수원1등<br>재광팀</span></div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">경기</span>수원시 노래주점</div>
            <div class="job-desc">♥수원 인계동1♥ 최상급 조건</div>
            <div class="job-card-footer">
              <span class="job-wage">160,000원</span>
              <span class="job-badge"><span class="crown-bronze">🥉</span> 1회 90일</span>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="job-card-banner g10"><span>⚡ 아우디리<br>강서구</span></div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">서울</span>강서구 노래주점</div>
            <div class="job-desc">♥강서구 TOP 1등 여성 최고조건♥</div>
            <div class="job-card-footer">
              <span class="job-wage">60,000원</span>
              <span class="job-badge"><span class="crown-silver">🥈</span> 1회 30일</span>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="job-card-banner g11"><span>💜 익산 정부장<br>전북</span></div>
          <div class="job-card-body">
            <div class="job-card-location"><span class="job-loc-badge">경기</span>고양시 룸싸롱</div>
            <div class="job-desc">익산 1등 출근만해도 보너스!</div>
            <div class="job-card-footer">
              <span class="job-wage">170,000원</span>
              <span class="job-badge"><span class="crown-silver">🥈</span> 2회 60일</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 프리미엄 채용정보 -->
    <div class="section-wrap">
      <div class="section-header">
        <h2 class="section-title">✨ 프리미엄 채용정보</h2>
        <div class="section-actions">
          <a href="#" class="section-more">더보기 →</a>
          <button type="button" class="btn-post-ad">광고신청</button>
        </div>
      </div>
      <div class="premium-grid">
        <div class="premium-card">
          <div class="premium-banner g1"><span>비상구<br>[서울 강남구]</span></div>
          <div class="premium-body">
            <div class="premium-name">강남 역삼 선중 ★ ...</div>
            <div class="premium-area">기타 · 91회 2880일</div>
            <div class="premium-wage">500,000원</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g10"><span>★ 별 ★<br>[경기]</span></div>
          <div class="premium-body">
            <div class="premium-name">❤ 송파24시 #테...</div>
            <div class="premium-area">노래주점 · 38회 2910일</div>
            <div class="premium-wage">면접 후 협의</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g2"><span>민비신<br>[광주 서구]</span></div>
          <div class="premium-body">
            <div class="premium-name">하루보장 160만원!!</div>
            <div class="premium-area">노래주점 · 1회 30일</div>
            <div class="premium-wage">12,000,000원</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g8"><span>모범 MODERN<br>[서울 관악구]</span></div>
          <div class="premium-body">
            <div class="premium-name">♥20,30대 환영 ♥...</div>
            <div class="premium-area">노래주점 · 6회 180일</div>
            <div class="premium-wage">60,000원</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g12"><span>GUCCI<br>[경기 안산시]</span></div>
          <div class="premium-body">
            <div class="premium-name">CHEGO 중에 CHEGO 남양...</div>
            <div class="premium-area">노래주점 · 3회 210일</div>
            <div class="premium-wage">500,000원</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g6"><span>하이퍼블릭<br>[경기 성남시]</span></div>
          <div class="premium-body">
            <div class="premium-name">하이퍼블릭 60분 TC17...</div>
            <div class="premium-area">룸싸롱 · 20회 1200일</div>
            <div class="premium-wage">150,000원</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g3"><span>강남 VIP룸<br>[경기]</span></div>
          <div class="premium-body">
            <div class="premium-name">★★★ 정중 하이점...</div>
            <div class="premium-area">노래주점 · 59회 1770일</div>
            <div class="premium-wage">면접 후 협의</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g5"><span>개구리 뒷다리<br>[전남 목포시]</span></div>
          <div class="premium-body">
            <div class="premium-name">●목포 ●보도환영...</div>
            <div class="premium-area">노래주점 · 1회 30일</div>
            <div class="premium-wage">면접 후 협의</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g11"><span>썸데이 도파민<br>마동석</span></div>
          <div class="premium-body">
            <div class="premium-name">썸데이 도파인 마동...</div>
            <div class="premium-area">룸싸롱 · 16회 1350일</div>
            <div class="premium-wage">100,000원</div>
          </div>
        </div>
        <div class="premium-card">
          <div class="premium-banner g7"><span>박프로<br>[서울 노원구]</span></div>
          <div class="premium-body">
            <div class="premium-name">강북구 청등 최고...</div>
            <div class="premium-area">룸싸롱 · 1회 90일</div>
            <div class="premium-wage">180,000원</div>
          </div>
        </div>
      </div>
    </div>

    <!-- 스페셜 채용정보 -->
    <div class="section-wrap">
      <div class="section-header">
        <h2 class="section-title">⭐ SPECIAL 채용정보</h2>
        <div class="section-actions">
          <a href="#" class="section-more">더보기 →</a>
          <button type="button" class="btn-post-ad">광고신청</button>
        </div>
      </div>
      <div class="special-grid">
        <div class="special-card" style="grid-column:span 2;">
          <div style="display:flex;gap:0;height:100%;">
            <div style="width:130px;flex-shrink:0;background:linear-gradient(135deg,#FF1B6B,#8338EC);display:flex;align-items:center;justify-content:center;padding:12px;font-size:14px;font-weight:900;color:#fff;text-align:center;line-height:1.4;">카지노<br><span style="font-size:11px;font-weight:400;opacity:.85">서울 강남구</span></div>
            <div style="flex:1;padding:12px 14px;background:#fff;">
              <div style="font-size:12px;color:#888;margin-bottom:4px;">기타 &nbsp;|&nbsp; 서울 강남구</div>
              <div style="font-size:14px;font-weight:700;color:#222;margin-bottom:4px;">❤카지노❤ 무식이...</div>
              <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:6px;">
                <span class="list-tag tag-urgent">급구</span>
                <span class="list-tag tag-init">초보환영</span>
                <span class="list-tag tag-bonus">당일지급</span>
              </div>
              <div style="font-size:16px;font-weight:900;color:var(--hot-pink);">600,000원</div>
              <div style="font-size:11px;color:#aaa;margin-top:2px;">🔥 88회 2640일</div>
            </div>
          </div>
        </div>
        <div class="special-card" style="grid-column:span 3;">
          <div style="display:flex;gap:0;height:100%;">
            <div style="width:130px;flex-shrink:0;background:linear-gradient(135deg,#4A0E8F,#A855F7);display:flex;align-items:center;justify-content:center;padding:12px;font-size:13px;font-weight:900;color:#fff;text-align:center;line-height:1.4;">비상구<br><span style="font-size:10px;font-weight:400;opacity:.8">서울 강남구</span></div>
            <div style="flex:1;padding:12px 14px;background:#fff;">
              <div style="font-size:12px;color:#888;margin-bottom:4px;">기타 &nbsp;|&nbsp; 서울 강남구</div>
              <div style="font-size:14px;font-weight:700;color:#222;margin-bottom:4px;">친절업소★매니저넘 환경 최고!...</div>
              <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:6px;">
                <span class="list-tag tag-pink">순번확실</span>
                <span class="list-tag tag-bonus">선불가능</span>
                <span class="list-tag tag-init">원룸제공</span>
              </div>
              <div style="font-size:16px;font-weight:900;color:var(--hot-pink);">600,000원</div>
              <div style="font-size:11px;color:#aaa;margin-top:2px;">🔥 96회 2850일</div>
            </div>
          </div>
        </div>
        <div class="special-card" style="grid-column:span 5;">
          <div style="display:flex;gap:0;height:100%;">
            <div style="width:130px;flex-shrink:0;background:linear-gradient(135deg,#FF6B35,#FF1B6B);display:flex;align-items:center;justify-content:center;padding:12px;font-size:14px;font-weight:900;color:#fff;text-align:center;">챔믹스<br><span style="font-size:10px;font-weight:400;opacity:.8">서울</span></div>
            <div style="flex:1;padding:12px 14px;background:#fff;display:flex;align-items:center;justify-content:space-between;">
              <div>
                <div style="font-size:12px;color:#888;margin-bottom:3px;">기타 &nbsp;|&nbsp; 서울</div>
                <div style="font-size:14px;font-weight:700;color:#222;">◎고페이1등◎챔믹스◎</div>
                <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:5px;">
                  <span class="list-tag tag-urgent">급구</span>
                  <span class="list-tag tag-pay">인센티브</span>
                  <span class="list-tag tag-bonus">갯수보장</span>
                  <span class="list-tag tag-init">초보환영</span>
                </div>
              </div>
              <div style="text-align:right;">
                <div style="font-size:18px;font-weight:900;color:var(--hot-pink);">500,000원</div>
                <div style="font-size:11px;color:#aaa;margin-top:2px;">🔥 106회 3180일</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 급구 + 추천 -->
    <div class="urgency-recommend-row">
      <div>
        <div class="section-header">
          <h2 class="section-title" style="font-size:16px">🚨 급구채용</h2>
          <div class="section-actions">
            <a href="#" class="section-more">더보기</a>
            <button type="button" class="btn-post-ad">광고신청</button>
          </div>
        </div>
        <div class="urgency-list">
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
            <div class="urgency-name">영종도 신규 최실장</div>
            <div class="urgency-area">인천 중구 · 노래주점</div>
            <div class="urgency-desc">인천 영종도 신규오픈 하루대기없이!</div>
            <div class="urgency-wage">면접 후 협의 <span>· 2회 60일</span></div>
          </div>
        </div>
      </div>
      <div>
        <div class="section-header">
          <h2 class="section-title" style="font-size:16px">💖 추천채용</h2>
          <div class="section-actions">
            <a href="#" class="section-more">더보기</a>
            <button type="button" class="btn-post-ad">광고신청</button>
          </div>
        </div>
        <div class="recommend-list">
          <div class="recommend-card">
            <div>
              <div class="rec-name">트리거 <span class="rec-area">서울 기타</span></div>
              <div class="rec-desc">강남최고대우!! 갯수OK!! 최고조건 환영!</div>
            </div>
            <div class="rec-right">
              <div class="rec-wage">500,000원</div>
              <div class="rec-meta">기타 · 100회 3000일</div>
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
              <div class="rec-desc">♥수원1번하이퍼블릭♥ 아우라 대표환영.</div>
            </div>
            <div class="rec-right">
              <div class="rec-wage">면접 후 협의</div>
              <div class="rec-meta">노래주점 · 3회 540일</div>
            </div>
          </div>
          <div class="recommend-card">
            <div>
              <div class="rec-name">♥파주최고TC <span class="rec-area">경기 파주시</span></div>
              <div class="rec-desc">●●● 퍼블릭 1시간 10만원 ●●●</div>
            </div>
            <div class="rec-right">
              <div class="rec-wage">100,000원</div>
              <div class="rec-meta">노래주점 · 1회 30일</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 채용정보 리스트 테이블 -->
    <div class="section-wrap">
      <div class="section-header">
        <h2 class="section-title">📋 채용정보 리스트</h2>
        <span style="font-size:12px;color:#888;">총 <strong style="color:var(--hot-pink);">3,427</strong>건</span>
      </div>
      <div class="list-section">
        <div class="list-table-wrap">
        <table class="list-table">
          <thead>
            <tr>
              <th>지역</th>
              <th>업직종</th>
              <th class="col-gender">성별/연령</th>
              <th>채용제목 / 편의사항</th>
              <th class="col-benefits">업소명</th>
              <th>급여조건</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">룸싸롱<br>퍼블릭</td>
              <td class="col-gender td-gender">여<br>20-35세</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">【급구】 개인 특별이벤트 진행中 하퍼 하이퍼블릭 ♡초보환영</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">선불가능</span>
                    <span class="benefit-tag b-hot">순번확실</span>
                    <span class="benefit-tag">식사제공</span>
                    <span class="benefit-tag">팁별도</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-urgent">급구</span>
                    <span class="list-tag tag-init">초보가능</span>
                    <span class="list-tag tag-bonus">당일지급</span>
                    <span class="list-tag tag-pink">선불가능</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">엘리트_사라있네</div>
                <div class="shop-mini-banner g1">엘리트</div>
                <div class="shop-jump">🔥 16회 1320일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-ilbul">일불</span><br>
                <span class="wage-amount">면접 후 협의</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">기타<br>기타업종</td>
              <td class="col-gender td-gender">여<br>20대</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">★하루100~160★강남1등!최고대추!안전제일 출퇴근지원 당일 당달 500,000만</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">갯수보장</span>
                    <span class="benefit-tag">지명우대</span>
                    <span class="benefit-tag">푸쉬가능</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-bonus">출퇴근지원</span>
                    <span class="list-tag tag-pink">원룸제공</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">메이드</div>
                <div class="shop-mini-banner g12">메이드</div>
                <div class="shop-jump">🔥 76회 2970일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-ilbul">일불</span><br>
                <span class="wage-amount">600,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">기타<br>기타업종</td>
              <td class="col-gender td-gender">여<br>-</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">❤정보❤VIP멤버쉽 한달 3천이상</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">팁별도</span>
                    <span class="benefit-tag">인센티브</span>
                    <span class="benefit-tag">초이스없음</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-pay">인센티브</span>
                    <span class="list-tag tag-init">초보가능</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">티파니7080</div>
                <div class="shop-mini-banner g2">티파니</div>
                <div class="shop-jump">🥈 1회 30일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-wolbul">월불</span><br>
                <span class="wage-amount">500,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">룸싸롱<br>텐프로</td>
              <td class="col-gender td-gender">여<br>20-28세</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title" style="color:var(--hot-pink);font-weight:900;">LA가라오케 고페이 일급 2,000,000원</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">출퇴근지원</span>
                    <span class="benefit-tag b-hot">팁별도</span>
                    <span class="benefit-tag">인센티브</span>
                    <span class="benefit-tag">갯수보장</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-urgent">급구</span>
                    <span class="list-tag tag-bonus">출퇴근지원</span>
                    <span class="list-tag tag-pink">인센티브</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">LA가라오케 고페이</div>
                <div class="shop-mini-banner g3">LA고페이</div>
                <div class="shop-jump">🔥 7회 630일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-ilbul">일불</span><br>
                <span class="wage-amount" style="font-size:12px;">2,000,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">서울<br>강서구</td>
              <td class="td-type">노래주점<br>아가씨</td>
              <td class="col-gender td-gender">여<br>전연령</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">♡강서구 TOP 1등 여왕들을 모시나여♡</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">갯수보장</span>
                    <span class="benefit-tag">지명우대</span>
                    <span class="benefit-tag">초이스없음</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-init">초보가능</span>
                    <span class="list-tag tag-pink">숙식제공</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">아우디리</div>
                <div class="shop-mini-banner g9">아우디리</div>
                <div class="shop-jump">🥉 1회 30일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-sigan">시급</span><br>
                <span class="wage-amount">60,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">인천<br>중구</td>
              <td class="td-type">노래주점<br>아가씨</td>
              <td class="col-gender td-gender">여<br>-</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">인천 영종도 신규오픈 하루대기없이 일가능</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag">순번확실</span>
                    <span class="benefit-tag">만근비지원</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-urgent">급구</span>
                    <span class="list-tag tag-bonus">만근비</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">영종도 신규 최실장</div>
                <div class="shop-mini-banner g4">영종도</div>
                <div class="shop-jump">🥈 2회 60일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-hyup">협의</span><br>
                <span class="wage-amount">면접 후 협의</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">인천<br>계양구</td>
              <td class="td-type">기타<br>기타업종</td>
              <td class="col-gender td-gender">여<br>20-35세</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">인천패티서1위업소최고수입보장!!수위약감 접대 받으면서 일하세요^^</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">선불가능</span>
                    <span class="benefit-tag">만근비지원</span>
                    <span class="benefit-tag">성형지원</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-init">초보가능</span>
                    <span class="list-tag tag-bonus">선불가능</span>
                    <span class="list-tag tag-pink">갯수보장</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">페티서1위 업소 인천본점</div>
                <div class="shop-mini-banner g11">페티서</div>
                <div class="shop-jump">🔥 13회 480일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-ilbul">일불</span><br>
                <span class="wage-amount">600,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">경기<br>파주시</td>
              <td class="td-type">룸싸롱<br>룸싸롱</td>
              <td class="col-gender td-gender">여<br>20-40세</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">파주 유일 야당새초름 파주최고 출퇴근지원 차비지원</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">출퇴근지원</span>
                    <span class="benefit-tag">차비지원</span>
                    <span class="benefit-tag">초보가능</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-bonus">출퇴근지원</span>
                    <span class="list-tag tag-pink">차비지원</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">초콜렛</div>
                <div class="shop-mini-banner g7">초콜렛</div>
                <div class="shop-jump">🥉 10회 300일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-ilbul">일불</span><br>
                <span class="wage-amount">140,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">룸싸롱<br>퍼블릭</td>
              <td class="col-gender td-gender">여<br>20-35세</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">♥도파민♥하1퍼♥도파민,슈앤미 퍼펙트,엘리트,갈토,언노♥을 급구!!</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag b-hot">갯수보장</span>
                    <span class="benefit-tag b-hot">인센티브</span>
                    <span class="benefit-tag">초이스없음</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-urgent">급구</span>
                    <span class="list-tag tag-init">초보가능</span>
                    <span class="list-tag tag-bonus">갯수보장</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">도파민 은성</div>
                <div class="shop-mini-banner g6">도파민</div>
                <div class="shop-jump">🔥 15회 690일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-ilbul">일불</span><br>
                <span class="wage-amount">100,000원</span>
              </td>
            </tr>
            <tr>
              <td class="td-region">서울<br>성동구</td>
              <td class="td-type">노래주점<br>아가씨</td>
              <td class="col-gender td-gender">여<br>20대</td>
              <td class="list-title-cell">
                <a href="#" class="list-job-title">♡첫날찡떼X♡지정365일 박스♡</a>
                <div class="list-title-bottom">
                  <div class="benefit-tags">
                    <span class="benefit-tag">만근비지원</span>
                    <span class="benefit-tag">식사제공</span>
                    <span class="benefit-tag b-hot">출퇴근지원</span>
                  </div>
                  <div class="list-tags">
                    <span class="list-tag tag-pink">지명우대</span>
                    <span class="list-tag tag-init">초보가능</span>
                  </div>
                </div>
              </td>
              <td class="col-benefits td-shop">
                <div class="shop-name">오렌지이벤트</div>
                <div class="shop-mini-banner g8">오렌지</div>
                <div class="shop-jump">🔥 11회 930일</div>
              </td>
              <td class="td-wage">
                <span class="wage-badge wb-sigan">시급</span><br>
                <span class="wage-amount">70,000원</span>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
    </div>

    <!-- 페이지네이션 -->
    <div class="pagination">
      <a href="#" class="page-btn prev-next">◀ PREV</a>
      <a href="#" class="page-btn active">1</a>
      <a href="#" class="page-btn">2</a>
      <a href="#" class="page-btn">3</a>
      <a href="#" class="page-btn">4</a>
      <a href="#" class="page-btn">5</a>
      <a href="#" class="page-btn">6</a>
      <a href="#" class="page-btn">7</a>
      <a href="#" class="page-btn">8</a>
      <a href="#" class="page-btn">9</a>
      <a href="#" class="page-btn">10</a>
      <a href="#" class="page-btn prev-next">NEXT ▶</a>
    </div>

    <!-- 하단 재검색 -->
    <div class="bottom-search">
      <select class="filter-select">
        <option>지역선택</option>
        <option>서울</option>
        <option>경기</option>
        <option>인천</option>
        <option>부산</option>
        <option>대구</option>
        <option>광주</option>
        <option>대전</option>
        <option>울산</option>
        <option>강원</option>
      </select>
      <select class="filter-select">
        <option>–1차 직종선택–</option>
        <option>룸싸롱</option>
        <option>노래주점</option>
        <option>마사지</option>
        <option>기타</option>
      </select>
      <select class="filter-select">
        <option>–2차 직종선택–</option>
        <option>아가씨</option>
        <option>초미씨</option>
        <option>미씨</option>
        <option>TC</option>
      </select>
      <input class="filter-input" type="text" placeholder="키워드 입력">
      <button type="button" class="btn-bottom-search">🔍 검색</button>
    </div>
