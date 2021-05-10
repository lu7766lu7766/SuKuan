<?php
// unit sec
// $timeout = getenv2("SESSION_TIMEOUT", 60 * 60 * 2) ;
// ini_set("session.cookie_lifetime", $timeout);
// ini_set("session.gc_maxlifetime", $timeout);
// session_start();

function start_session($expire = 0)
{
    if ($expire == 0) {
        $expire = ini_get('session.gc_maxlifetime');
    } else {
        ini_set('session.gc_maxlifetime', $expire);
    }

    if (empty($_COOKIE['PHPSESSID'])) {
        session_set_cookie_params($expire, "/");
        session_start();
    } else {
        session_start();
        setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time() + $expire, "/");
    }
}

start_session(getenv2("SESSION_TIMEOUT", 60 * 60 * 2));