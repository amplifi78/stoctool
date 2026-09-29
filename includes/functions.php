<?php
// Core helper functions

// Format a timestamp stored by SQLite (UTC) for display in local (Sydney) time.
// SQLite's CURRENT_TIMESTAMP is UTC; we store UTC and convert on display.
function local_time($utc_string, $format = 'j M Y, g:ia') {
    if (empty($utc_string)) return 'Never';
    $dt = new DateTime($utc_string, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
    return $dt->format($format);
}
