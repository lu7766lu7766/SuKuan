<?php

$database = [
  'driver' => 'sqlsrv',
  'host' => getenv2("DB_IP"),
  'database' => getenv2("DB_NAME"),
  'username' => getenv2("DB_USER"),
  'password' => getenv2("DB_PASSWORD"),
  'port' => getenv2("DB_PORT"),
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci'
];

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

// 创建链接
$capsule->addConnection($database);

// 设置全局静态可访问DB
$capsule->setAsGlobal();

// 启动Eloquent
$capsule->bootEloquent();

Capsule::connection()->enableQueryLog();
