<?php
/**
 * 기업회원 승인대기 로그인 차단
 * GnuBoard extend 폴더에 위치하여 자동 로드됨
 * login_session_before 이벤트에서 mb_1='biz' && mb_7='pending' 이면 로그인 차단
 */
if (!defined('_GNUBOARD_')) exit;

add_event('login_session_before', 'eve_check_biz_member_login', 1, 2);

function eve_check_biz_member_login($mb, $is_social_login = false) {
    if (!is_array($mb) || empty($mb['mb_id'])) return;

    $member_type = isset($mb['mb_1']) ? trim($mb['mb_1']) : '';
    $approval_status = isset($mb['mb_7']) ? trim($mb['mb_7']) : '';

    if ($member_type === 'biz') {
        if ($approval_status === 'pending') {
            alert('기업회원 승인 대기중입니다.\\n관리자가 제출하신 서류를 검토 후 승인해드립니다.\\n승인까지 영업일 기준 1~2일 소요됩니다.');
        }
        if ($approval_status === 'rejected') {
            alert('기업회원 가입이 반려되었습니다.\\n사유를 확인하시려면 관리자에게 문의해주세요.');
        }
    }
}
