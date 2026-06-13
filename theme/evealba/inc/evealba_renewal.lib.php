<?php
/**
 * 리뉴얼 3컬럼 레이아웃 시작
 */
if (!defined('_GNUBOARD_')) exit;

function eve_is_renewal_ui() {
    return defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI;
}

function eve_renewal_skip_mobile_theme() {
    return eve_is_renewal_ui();
}
