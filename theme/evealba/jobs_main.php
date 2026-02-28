<?php
/**
 * 채용정보 메인 영역
 * - DB 연동: ongoing 광고를 유형별로 조회하여 표시
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('get_jobs_by_type')) {
    @include_once(G5_PATH.'/extend/jobs_list_helper.php');
}

$_jobs_udae    = function_exists('get_jobs_by_type') ? get_jobs_by_type('우대', 8) : array();
$_jobs_premium = function_exists('get_jobs_by_type') ? get_jobs_by_type('프리미엄', 5) : array();
$_jobs_special = function_exists('get_jobs_by_type') ? get_jobs_by_type('스페셜', 6) : array();
$_jobs_urgent  = function_exists('get_jobs_by_type') ? get_jobs_by_type('급구', 3) : array();
$_jobs_recomm  = function_exists('get_jobs_by_type') ? get_jobs_by_type('추천', 4) : array();
$_jobs_list    = function_exists('get_jobs_by_type') ? get_jobs_by_type('줄광고', 20) : array();
?>
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>

    <!-- 검색 필터 박스 -->
    <?php
    $jf = isset($job_filters) ? $job_filters : array('er_id'=>0,'erd_id'=>0,'ei_id'=>0,'ej_id'=>0,'ec_id'=>0,'stx'=>'');
    $jobs_form_action = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : 'jobs.php';
    ?>
    <form method="get" action="<?php echo htmlspecialchars($jobs_form_action); ?>" id="jobs-search-form" class="filter-box">
      <div class="filter-title">채용정보 검색하기 &nbsp;<small style="font-size:11px;font-weight:500;color:#aaa">조건 하나만 선택해도 검색이 가능합니다!</small></div>
      <div class="filter-rows">
        <div class="filter-row">
          <span class="filter-label">▸ 지역</span>
          <select class="filter-select" name="er_id" id="filter-er-id">
            <option value="">지역선택</option>
            <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
            <option value="<?php echo (int)$r['er_id']; ?>"<?php echo ($jf['er_id']==$r['er_id'])?' selected':''; ?>><?php echo htmlspecialchars($r['er_name']); ?></option>
            <?php } ?>
          </select>
          <select class="filter-select" name="erd_id" id="filter-erd-id">
            <option value="">세부지역선택</option>
            <?php foreach ((isset($ev_region_details) ? $ev_region_details : []) as $rd) { ?>
            <option value="<?php echo (int)$rd['erd_id']; ?>" data-er-id="<?php echo (int)$rd['er_id']; ?>"<?php echo ($jf['erd_id']==$rd['erd_id'])?' selected':''; ?>><?php echo htmlspecialchars($rd['erd_name']); ?></option>
            <?php } ?>
          </select>
          &nbsp;&nbsp;
          <span class="filter-label">▸ 직종</span>
          <select class="filter-select" name="ei_id" id="filter-ei-id">
            <option value="">직종선택</option>
            <?php foreach ((isset($ev_industries) ? $ev_industries : []) as $i) { ?>
            <option value="<?php echo (int)$i['ei_id']; ?>"<?php echo ($jf['ei_id']==$i['ei_id'])?' selected':''; ?>><?php echo htmlspecialchars($i['ei_name']); ?></option>
            <?php } ?>
          </select>
          <select class="filter-select" name="ej_id" id="filter-ej-id">
            <option value="">세부직종선택</option>
            <?php foreach ((isset($ev_jobs) ? $ev_jobs : []) as $j) { ?>
            <option value="<?php echo (int)$j['ej_id']; ?>" data-ei-id="<?php echo (int)$j['ei_id']; ?>"<?php echo ($jf['ej_id']==$j['ej_id'])?' selected':''; ?>><?php echo htmlspecialchars($j['ej_name']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">▸ 편의</span>
          <select class="filter-select" name="ec_id">
            <option value="">--선택--</option>
            <?php foreach ((isset($ev_conveniences) ? $ev_conveniences : []) as $c) { ?>
            <option value="<?php echo (int)$c['ec_id']; ?>"<?php echo ($jf['ec_id']==$c['ec_id'])?' selected':''; ?>><?php echo htmlspecialchars($c['ec_name']); ?></option>
            <?php } ?>
          </select>
          &nbsp;&nbsp;
          <span class="filter-label">▸ 고용</span>
          <select class="filter-select" name="employ_type">
            <option value="">고용형태</option>
            <option value="고용"<?php echo (isset($_GET['employ_type'])&&$_GET['employ_type']==='고용')?' selected':''; ?>>고용</option>
            <option value="파견"<?php echo (isset($_GET['employ_type'])&&$_GET['employ_type']==='파견')?' selected':''; ?>>파견</option>
            <option value="도급"<?php echo (isset($_GET['employ_type'])&&$_GET['employ_type']==='도급')?' selected':''; ?>>도급</option>
            <option value="위임"<?php echo (isset($_GET['employ_type'])&&$_GET['employ_type']==='위임')?' selected':''; ?>>위임</option>
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">▸ 키워드</span>
          <input class="filter-input" type="text" name="stx" placeholder="제목, 업체명, 닉네임, 키워드로 검색 가능합니다." value="<?php echo htmlspecialchars($jf['stx']); ?>">
        </div>
      </div>
      <div class="filter-actions">
        <button type="submit" class="btn-search">🔍 검색</button>
        <button type="button" class="btn-reset">초기화</button>
      </div>
    </form>

    <!-- 우대채용정보 -->
    <div class="section-wrap">
      <div class="section-header">
        <h2 class="section-title">💎 우대등록 채용정보</h2>
        <div class="section-actions">
          <a href="#" class="section-more">더보기 →</a>
          <button type="button" class="btn-post-ad">광고신청</button>
        </div>
      </div>
      <div class="featured-grid" id="jobs-featured-grid">
<?php if (!empty($_jobs_udae)) { foreach ($_jobs_udae as $_u) { render_job_card($_u); } } else { ?>
        <div class="job-card" data-region="서울" data-subregion="강남구" data-type="룸싸롱">
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
        <div class="job-card" data-region="경기" data-subregion="부천시" data-type="룸싸롱">
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
        <div class="job-card" data-region="경기" data-subregion="파주시" data-type="노래주점">
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
        <div class="job-card" data-region="서울" data-subregion="기타" data-type="기타">
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
<?php } ?>
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
<?php if (!empty($_jobs_premium)) { ?>
      <div class="premium-grid">
        <?php foreach ($_jobs_premium as $_p) { render_premium_card($_p); } ?>
      </div>
<?php } else { include_once dirname(__FILE__).'/inc/ads_premium.php'; } ?>
    </div>

    <!-- 스페셜 채용정보 -->
    <div class="section-wrap">
      <div class="section-header">
        <h2 class="section-title">⭐ 스페셜채용정보</h2>
        <div class="section-actions">
          <a href="#" class="section-more">더보기 →</a>
          <button type="button" class="btn-post-ad">광고신청</button>
        </div>
      </div>
<?php if (!empty($_jobs_special)) { ?>
      <div class="special-grid">
        <?php foreach ($_jobs_special as $_s) { render_premium_card($_s, 'special-card'); } ?>
      </div>
<?php } else { include_once dirname(__FILE__).'/inc/ads_special.php'; } ?>
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
<?php if (!empty($_jobs_urgent)) { foreach ($_jobs_urgent as $_ug) { render_urgency_card($_ug); } } else { ?>
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
<?php } ?>
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
<?php if (!empty($_jobs_recomm)) { foreach ($_jobs_recomm as $_rc) { render_recommend_card($_rc); } } else { ?>
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
<?php } ?>
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
        <!-- PC: 테이블 -->
        <div class="job-list-desktop">
        <div class="list-table-wrap">
        <table class="list-table">
          <thead>
            <tr>
              <th>지역</th>
              <th>업직종</th>
              <th>채용제목 / 편의사항</th>
              <th class="col-benefits">업소명</th>
              <th>급여조건</th>
            </tr>
          </thead>
          <tbody id="jobs-list-tbody">
<?php if (!empty($_jobs_list)) { foreach ($_jobs_list as $_jl) { render_job_list_row($_jl); } } else { ?>
            <tr class="job-list-row" data-region="서울" data-subregion="강남구" data-type="룸싸롱">
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">룸싸롱<br>퍼블릭</td>
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
            <tr class="job-list-row" data-region="서울" data-subregion="강남구" data-type="기타">
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">기타<br>기타업종</td>
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
            <tr class="job-list-row" data-region="서울" data-subregion="강남구" data-type="기타">
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">기타<br>기타업종</td>
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
            <tr class="job-list-row" data-region="서울" data-subregion="강남구" data-type="룸싸롱">
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">룸싸롱<br>텐프로</td>
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
            <tr class="job-list-row" data-region="서울" data-subregion="강서구" data-type="노래주점">
              <td class="td-region">서울<br>강서구</td>
              <td class="td-type">노래주점<br>아가씨</td>
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
            <tr class="job-list-row" data-region="인천" data-subregion="중구" data-type="노래주점">
              <td class="td-region">인천<br>중구</td>
              <td class="td-type">노래주점<br>아가씨</td>
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
            <tr class="job-list-row" data-region="인천" data-subregion="계양구" data-type="기타">
              <td class="td-region">인천<br>계양구</td>
              <td class="td-type">기타<br>기타업종</td>
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
            <tr class="job-list-row" data-region="경기" data-subregion="파주시" data-type="룸싸롱">
              <td class="td-region">경기<br>파주시</td>
              <td class="td-type">룸싸롱<br>룸싸롱</td>
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
            <tr class="job-list-row" data-region="서울" data-subregion="강남구" data-type="룸싸롱">
              <td class="td-region">서울<br>강남구</td>
              <td class="td-type">룸싸롱<br>퍼블릭</td>
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
            <tr class="job-list-row" data-region="서울" data-subregion="성동구" data-type="노래주점">
              <td class="td-region">서울<br>성동구</td>
              <td class="td-type">노래주점<br>아가씨</td>
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
<?php } ?>
          </tbody>
        </table>
        </div>
        </div>

        <!-- 모바일: 카드형 스택 -->
        <div class="job-list-mobile">
<?php if (!empty($_jobs_list)) { foreach ($_jobs_list as $_jlm) { render_job_list_mobile($_jlm); } } else { ?>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">【급구】 개인 특별이벤트 진행中 하퍼 하이퍼블릭 ♡초보환영</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">강남구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-urgent">급구</span><span class="list-tag tag-init">초보가능</span><span class="list-tag tag-bonus">당일지급</span><span class="list-tag tag-pink">선불가능</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">룸싸롱 퍼블릭 | 여 20~35세</span>
              <span class="job-card-m-wage">[일불] 면접 후 협의</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">엘리트-사라있네 🔥16회 1320일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">★하루100~160★강남1등!최고대추!안전제일 출퇴근지원 당일 당달 500,000만</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">강남구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-bonus">출퇴근지원</span><span class="list-tag tag-pink">원룸제공</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">기타 기타업종 | 여 20대</span>
              <span class="job-card-m-wage">[일불] 600,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">메이드 🔥76회 2970일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">❤정보❤VIP멤버쉽 한달 3천이상</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">강남구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-pay">인센티브</span><span class="list-tag tag-init">초보가능</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">기타 기타업종 | 여 -</span>
              <span class="job-card-m-wage">[월불] 500,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">티파니7080 🥈1회 30일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">LA가라오케 고페이 일급 2,000,000원</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">강남구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-urgent">급구</span><span class="list-tag tag-bonus">출퇴근지원</span><span class="list-tag tag-pink">인센티브</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">룸싸롱 텐프로 | 여 20-28세</span>
              <span class="job-card-m-wage">[일불] 2,000,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">LA가라오케 고페이 🔥7회 630일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">♡강서구 TOP 1등 여왕들을 모시나여♡</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">강서구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-init">초보가능</span><span class="list-tag tag-pink">숙식제공</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">노래주점 아가씨 | 여 전연령</span>
              <span class="job-card-m-wage">[시급] 60,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">아우디리 🥉1회 30일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">인천</span>
              <span class="job-card-m-title">인천 영종도 신규오픈 하루대기없이 일가능</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">중구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-urgent">급구</span><span class="list-tag tag-bonus">만근비</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">노래주점 아가씨 | 여 -</span>
              <span class="job-card-m-wage">[협의] 면접 후 협의</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">영종도 신규 최실장 🥈2회 60일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">인천</span>
              <span class="job-card-m-title">인천패티서1위업소최고수입보장!!수위약감 접대 받으면서 일하세요^^</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">계양구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-init">초보가능</span><span class="list-tag tag-bonus">선불가능</span><span class="list-tag tag-pink">갯수보장</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">기타 기타업종 | 여 20-35세</span>
              <span class="job-card-m-wage">[일불] 600,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">페티서1위 업소 인천본점 🔥13회 480일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">경기</span>
              <span class="job-card-m-title">파주 유일 야당새초름 파주최고 출퇴근지원 차비지원</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">파주시</span>
              <span class="job-card-m-tags"><span class="list-tag tag-bonus">출퇴근지원</span><span class="list-tag tag-pink">차비지원</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">룸싸롱 룸싸롱 | 여 20-40세</span>
              <span class="job-card-m-wage">[일불] 140,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">초콜렛 🥉10회 300일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">♥도파민♥하1퍼♥도파민,슈앤미 퍼펙트,엘리트,갈토,언노♥을 급구!!</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">강남구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-urgent">급구</span><span class="list-tag tag-init">초보가능</span><span class="list-tag tag-bonus">갯수보장</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">룸싸롱 퍼블릭 | 여 20-35세</span>
              <span class="job-card-m-wage">[일불] 100,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">도파민 은성 🔥15회 690일</span>
            </div>
          </a>
          <a href="#" class="job-card-m">
            <div class="job-card-m-row row-1">
              <span class="job-card-m-region">서울</span>
              <span class="job-card-m-title">♡첫날찡떼X♡지정365일 박스♡</span>
            </div>
            <div class="job-card-m-row row-2">
              <span class="job-card-m-region2">성동구</span>
              <span class="job-card-m-tags"><span class="list-tag tag-pink">지명우대</span><span class="list-tag tag-init">초보가능</span></span>
            </div>
            <div class="job-card-m-row row-3">
              <span class="job-card-m-type">노래주점 아가씨 | 여 20대</span>
              <span class="job-card-m-wage">[시급] 70,000원</span>
            </div>
            <div class="job-card-m-row row-4">
              <span class="job-card-m-left"></span>
              <span class="job-card-m-shop">오렌지이벤트 🔥11회 930일</span>
            </div>
          </a>
<?php } ?>
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
      <select class="filter-select" name="er_id">
        <option value="">지역선택</option>
        <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
        <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
        <?php } ?>
      </select>
      <select class="filter-select" name="ei_id">
        <option value="">1차 직종선택</option>
        <?php foreach ((isset($ev_industries) ? $ev_industries : []) as $i) { ?>
        <option value="<?php echo (int)$i['ei_id']; ?>"><?php echo htmlspecialchars($i['ei_name']); ?></option>
        <?php } ?>
      </select>
      <select class="filter-select" name="ej_id">
        <option value="">2차 직종선택</option>
        <?php foreach ((isset($ev_jobs) ? $ev_jobs : []) as $j) { ?>
        <option value="<?php echo (int)$j['ej_id']; ?>"><?php echo htmlspecialchars($j['ej_name']); ?></option>
        <?php } ?>
      </select>
      <input class="filter-input" type="text" placeholder="키워드 입력">
      <button type="button" class="btn-bottom-search">🔍 검색</button>
    </div>
