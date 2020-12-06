<?php
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

use setting\Config;

function getApiUrl($uri)
{
    $config = new Config();
    $currentHost = getenv2('API_HOST')
        ? getenv2('API_HOST') . '/'
        : getenv2('DB_IP') . $config->base["folder"];
    return '//' . $currentHost . $uri;
}

function getDownloaderUrl($uri)
{
    $config = new Config();
    $currentHost = getenv2('DOWNLOAD_HOST')
        ? getenv2('DOWNLOAD_HOST') . '/'
        : getenv2('DB_IP') . $config->base["folder"];
    return '//' . $currentHost . $uri;
}

function GUID()
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

function dd($val)
{
    print_r($val);
    exit();
}
