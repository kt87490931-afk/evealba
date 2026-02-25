<?php
/**
 * 광고유료결제 섹션 (채용등록 폼 & 연장 팝업 공용)
 * - jobs_register_main.php, jobs_extend_popup.php 에서 include
 */
if (!defined('_GNUBOARD_')) exit;
?>
    <div class="form-card sh-orange">
      <div class="sec-head open" onclick="typeof toggleSec==='function'&&toggleSec(this)">
        <span class="sec-head-icon">💰</span>
        <span class="sec-head-title">광고유료결제</span>
        <span class="sec-head-sub">노출 서비스를 선택하여 최고의 광고효과를 누려보세요.</span>
        <span class="sec-chevron">▼</span>
      </div>
      <div class="sec-body">

        <div class="total-bar">
          <span class="total-bar-text">🛒 총 신청 금액</span>
          <span class="total-bar-amount" id="totalAmount">0 원</span>
        </div>

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
              <tbody class="ad-line-ad-required">
              <tr class="ad-tr-highlight">
                <td class="ad-td td-svc" colspan="5">
                  <span style="font-size:13px;font-weight:700;color:var(--hot-pink);">줄광고는 필수결제 사항 입니다. 박스광고와 함께 적용 시 노출기간을 동일하게 해주세요</span>
                </td>
              </tr>
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
                    <input type="checkbox" data-price="70000" data-label="줄광고 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="125000" data-label="줄광고 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="170000" data-label="줄광고 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
              </tbody>
              <tbody>
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
                    <input type="checkbox" data-price="230000" data-label="우대 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="415000" data-label="우대 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="550000" data-label="우대 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
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
                    <input type="checkbox" data-price="180000" data-label="프리미엄 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="325000" data-label="프리미엄 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="430000" data-label="프리미엄 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
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
                    <input type="checkbox" data-price="130000" data-label="스페셜 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="235000" data-label="스페셜 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="310000" data-label="스페셜 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
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
                    <input type="checkbox" data-price="150000" data-label="급구 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="285000" data-label="급구 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="420000" data-label="급구 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
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
                    <input type="checkbox" data-price="100000" data-label="추천 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="185000" data-label="추천 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="240000" data-label="추천 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
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
                    <input type="checkbox" data-price="30000" data-label="굵은글씨 30일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="55000" data-label="굵은글씨 60일" onchange="typeof calcTotal==='function'&&calcTotal()">
                    <input type="checkbox" data-price="70000" data-label="굵은글씨 90일" onchange="typeof calcTotal==='function'&&calcTotal()">
                  </div>
                </td>
              </tr>
              <tr class="ad-tr">
                <td colspan="5" class="ad-td" style="background:#e8f5e9;color:#2E7D32;font-weight:700;font-size:12px;text-align:left;">
                  💡 옵션만 결제하실 경우 광고노출이 되지않습니다.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

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

        <div class="total-bottom-bar">
          <span class="tbb-label">💳 총 신청 금액</span>
          <span class="tbb-amount" id="totalAmount2">0 원</span>
        </div>

      </div>
    </div>
