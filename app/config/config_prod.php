<?php

// Mevcutta kullanılacak olan timezonu set eder
date_default_timezone_set('Europe/Istanbul');

// Veritabanı bağlantı ayarları
$appplication['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'destek',
    'user'     => 'root',
    'password' => '123',
    'charset' => 'utf8'
);