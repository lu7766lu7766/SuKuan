<?php
// unit sec
$defaultTimeout = 60 * 60 * 2;
ini_set("session.cookie_lifetime", getenv2("SESSION_TIMEOUT", $defaultTimeout));
ini_set("session.gc_maxlifetime", $defaultTimeout);
session_start();