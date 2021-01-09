<?php

use comm\Request;
use comm\Session;

function nl2br2($string)
{
    $string = str_replace(["\r\n", "\r", "\n"], "<br />", $string);
    return $string;
}

function getenv2(...$args)
{
    $key = $args[0];
    $default = $args[1] ?? '';
    $string = file_get_contents(dirname(dirname(__DIR__)) . "/env.json");
    $env = json_decode(trim($string), true);
    return $env[$key] ?? $default;
}

function getApiUrl($uri): String
{
    $currentHost = getenv2('API_HOST')
        ? getenv2('API_HOST') . '/'
        : getenv2('DB_IP') . config("folder");
    return '//' . $currentHost . $uri;
}

function config($key)
{
    return \setting\Config::get($key);
}

function session(...$params)
{
    $len = count($params);
    if ($len == 1) {
        return Session::get($params[0]);
    } else if ($len == 2) {
        Session::set($params[0], $params[1]);
    } else {
        throw new Exception("not defined too much params");
    }
}

function isDev()
{
    return getenv2("ENV") == "development";
}

function request()
{
    return Request::getInstance();
}

function url(string $uri): string
{
    return config("folder") . $uri;
}

function public_path(string $uri): string
{
    return config("folder") . "public/" . $uri;
}

function GUID(): String
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf(
        '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(16384, 20479),
        mt_rand(32768, 49151),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535)
    );
}
