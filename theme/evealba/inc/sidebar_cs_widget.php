<?php
/**
 * 고객지원센터 사이드바 위젯 (공통)
 * 모든 페이지에서 동일한 디자인으로 표시
 * - 헤더: 핑크 그라데이션 + 🎀 고객지원센터
 * - 본문: 흰 배경, 전화번호, 운영시간, 카카오 버튼
 */
if (!defined('_GNUBOARD_')) exit;
?>
<!-- 고객지원센터 -->
<div class="sidebar-widget cs-support-widget">
  <div class="cs-support-box">
    <div class="cs-support-header">🎀 고객지원센터</div>
    <div class="cs-support-body">
      <div class="cs-support-label">📞 이브알바 고객센터</div>
      <div class="cs-support-phone">1588-0000</div>
      <div class="cs-support-hours">평일 09:30~19:00 · 점심<br>12:00~13:30</div>
      <div class="cs-support-note">*공휴일·일 근무하지 않습니다.</div>
      <a href="#" class="cs-support-kakao">💬 EvéAlba</a>
    </div>
  </div>
</div>
