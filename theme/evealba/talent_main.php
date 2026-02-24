<?php
/**
 * 인재정보 메인 영역 (eve_alba_talent.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>

    <!-- 업직종 탭 카운터 -->
    <div class="type-tab-bar">
      <div class="type-tab-card" onclick="setTab(this,'all')">
        <div class="ttc-left">
          <span class="ttc-icon">👩</span>
          <span class="ttc-name">전체</span>
        </div>
        <span class="ttc-count">17,048</span>
      </div>
      <div class="type-tab-card active" onclick="setTab(this,'room')">
        <div class="ttc-left">
          <span class="ttc-icon">🥂</span>
          <span class="ttc-name">룸싸롱</span>
        </div>
        <span class="ttc-count">2,258</span>
      </div>
      <div class="type-tab-card" onclick="setTab(this,'karaoke')">
        <div class="ttc-left">
          <span class="ttc-icon">🎤</span>
          <span class="ttc-name">노래주점</span>
        </div>
        <span class="ttc-count">1,985</span>
      </div>
      <div class="type-tab-card" onclick="setTab(this,'massage')">
        <div class="ttc-left">
          <span class="ttc-icon">💆</span>
          <span class="ttc-name">마사지</span>
        </div>
        <span class="ttc-count">2,917</span>
      </div>
    </div>

    <!-- AI 매칭 배너 -->
    <div class="ai-match-banner">
      <div class="amb-icon">🤖</div>
      <div class="amb-text">
        <h3>AI 인재 매칭 시스템</h3>
        <p>조건에 맞는 인재를 자동으로 매칭해드립니다 · 하루 최대 1명 (유료 시 5명)</p>
      </div>
      <div class="amb-badge">✨ 무료 운영 중</div>
    </div>

    <!-- 열람권 안내 배너 -->
    <div class="view-ticket-banner">
      <div class="vtb-icon">🔒</div>
      <div class="vtb-text">
        <h3>인재정보 상세 열람을 위해 열람권이 필요합니다</h3>
        <p>열람권 구매 후 24시간 동안 해당 인재의 상세 정보를 열람하실 수 있습니다</p>
      </div>
      <div class="vtb-btn">열람권 구매</div>
    </div>

    <!-- 검색 필터 -->
    <div class="filter-box">
      <div class="filter-title">인재정보 검색하기 &nbsp;<small style="font-size:11px;font-weight:500;color:#aaa">조건 하나만 선택해도 검색이 가능합니다!</small></div>
      <div class="filter-rows">
        <div class="filter-row">
          <span class="filter-label">▸ 직종</span>
          <select class="filter-select" name="ei_id" id="filter-ei-id">
            <option value="">1차 직종선택</option>
            <?php foreach ((isset($ev_industries) ? $ev_industries : []) as $i) { ?>
            <option value="<?php echo (int)$i['ei_id']; ?>"><?php echo htmlspecialchars($i['ei_name']); ?></option>
            <?php } ?>
          </select>
          <select class="filter-select" name="ej_id" id="filter-ej-id">
            <option value="">2차 세부직종</option>
            <?php foreach ((isset($ev_jobs) ? $ev_jobs : []) as $j) { ?>
            <option value="<?php echo (int)$j['ej_id']; ?>" data-ei-id="<?php echo (int)$j['ei_id']; ?>"><?php echo htmlspecialchars($j['ej_name']); ?></option>
            <?php } ?>
          </select>
          &nbsp;&nbsp;
          <span class="filter-label">▸ 지역</span>
          <select class="filter-select" name="er_id">
            <option value="">지역선택</option>
            <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
            <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">▸ 키워드</span>
          <input class="filter-input" type="text" placeholder="이름(닉네임), 지역, 나이, 키워드로 검색 가능합니다.">
        </div>
      </div>
      <div class="filter-actions">
        <button class="btn-search" id="searchBtn">🔍 검색</button>
        <button class="btn-reset">초기화</button>
      </div>
    </div>

    <!-- 인재정보 리스트 -->
    <div class="section-header">
      <h2 class="section-title">👑 인재정보 리스트</h2>
      <span class="result-count">총 <strong>17,048</strong>건</span>
    </div>

    <div class="talent-table-wrap">
      <table class="talent-table">
        <thead>
          <tr>
            <th style="width:72px">이름</th>
            <th style="width:72px">성별</th>
            <th>제목 / 희망지역</th>
            <th style="width:90px">희망업종</th>
            <th style="width:100px">희망급여</th>
            <th style="width:90px">작성일</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="td-name">마○○<span class="name-private">닉네임 비공개</span></td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">26세</span></td>
            <td class="td-title"><a href="#" class="talent-title">강남 룸구해요 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>서울</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">룸싸롱</span></td>
            <td class="td-wage"><span class="wage-badge wb-hyup">협의</span><span class="wage-amount">면접 후 협의</span></td>
            <td class="td-date">2026-02-23</td>
          </tr>
          <tr>
            <td class="td-name">짜○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">27세</span></td>
            <td class="td-title"><a href="#" class="talent-title">서울 경기 인천 쉬어 야간 구해요 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>서울</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">마사지</span></td>
            <td class="td-wage"><span class="wage-badge wb-ilbul">일불</span><span class="wage-amount">400,000원</span></td>
            <td class="td-date">2026-02-23</td>
          </tr>
          <tr class="lock-row">
            <td class="td-name">9090○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">27세</span></td>
            <td class="td-title"><a href="#" class="talent-title">일급 40이상 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>경북</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">기타</span></td>
            <td class="td-wage"><span class="wage-badge wb-ilbul">일불</span><span class="wage-amount">400,000원</span></td>
            <td class="td-date">2026-02-22</td>
          </tr>
          <tr class="lock-row">
            <td class="td-name">넬○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">33세</span></td>
            <td class="td-title"><a href="#" class="talent-title">160 66kg 일종 둘론 일자리 구합니다 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>서울</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">기타</span></td>
            <td class="td-wage"><span class="wage-badge wb-hyup">협의</span><span class="wage-amount">면접 후 협의</span></td>
            <td class="td-date">2026-02-22</td>
          </tr>
          <tr>
            <td class="td-name">훞○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">27세</span></td>
            <td class="td-title"><a href="#" class="talent-title">77사이즈 ㅎC ㄹㅎC ㄴC 구해요 <span class="badge-hot-inline">HOT</span></a><div class="talent-region">희망지역 : <strong>서울</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">노래주점</span></td>
            <td class="td-wage"><span class="wage-badge wb-hyup">협의</span><span class="wage-amount">면접 후 협의</span></td>
            <td class="td-date">2026-02-21</td>
          </tr>
          <tr>
            <td class="td-name">꼬○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">27세</span></td>
            <td class="td-title"><a href="#" class="talent-title">165 68kg 20대초반 일자리 구해요 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>서울</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">노래주점</span></td>
            <td class="td-wage"><span class="wage-badge wb-hyup">협의</span><span class="wage-amount">면접 후 협의</span></td>
            <td class="td-date">2026-02-21</td>
          </tr>
          <tr>
            <td class="td-name">수○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">22세</span></td>
            <td class="td-title"><a href="#" class="talent-title">일구해요 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>경기</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">기타</span></td>
            <td class="td-wage"><span class="wage-badge wb-hyup">협의</span><span class="wage-amount">면접 후 협의</span></td>
            <td class="td-date">2026-02-21</td>
          </tr>
          <tr>
            <td class="td-name">cnjzi○○</td>
            <td class="td-gender"><span class="gender-badge gender-f">여</span><br><span class="age-text">27세</span></td>
            <td class="td-title"><a href="#" class="talent-title">20대 77 여자 일 구해요 <span class="badge-new-inline">N</span></a><div class="talent-region">희망지역 : <strong>서울</strong></div></td>
            <td class="td-job-type"><span class="job-type-badge">노래주점</span></td>
            <td class="td-wage"><span class="wage-badge wb-ilbul">일불</span><span class="wage-amount">260,000원</span></td>
            <td class="td-date">2026-02-20</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="pagination">
      <a href="#" class="page-btn prev-next">◀ PREV</a>
      <a href="#" class="page-btn active">1</a>
      <a href="#" class="page-btn">2</a>
      <a href="#" class="page-btn">3</a>
      <a href="#" class="page-btn">4</a>
      <a href="#" class="page-btn">5</a>
      <a href="#" class="page-btn prev-next">NEXT ▶</a>
    </div>

    <!-- 하단 재검색 -->
    <div class="bottom-search">
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
      <select class="filter-select" name="er_id">
        <option value="">지역선택</option>
        <?php foreach ((isset($ev_regions) ? $ev_regions : []) as $r) { ?>
        <option value="<?php echo (int)$r['er_id']; ?>"><?php echo htmlspecialchars($r['er_name']); ?></option>
        <?php } ?>
      </select>
      <input class="filter-input" type="text" placeholder="키워드 입력">
      <button class="btn-bottom-search">🔍 검색</button>
    </div>
