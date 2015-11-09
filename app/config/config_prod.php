<?php

// Mevcutta kullanılacak olan timezonu set eder
date_default_timezone_set('Europe/Istanbul');

$application['asset_path'] = 'http://destek.lkl';

// Veritabanı bağlantı ayarları
$application['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'destek',
    'user'     => 'root',
    'password' => '123',
    'charset' => 'utf8'
);