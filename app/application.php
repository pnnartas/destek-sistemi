<?php

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;

// Doctrine servisi sağlayıcısını applicationa kayıt eder
$application->register(new DoctrineServiceProvider(), array(
    'db.options' => $app['db.options'],
));

// Url oluşturucu servis sağlayıcısını kayıt eder
$application->register(new UrlGeneratorServiceProvider());

// Twig servis sağlayıcısını kayıt eder

$application->register(new TwigServiceProvider(), array(
    'debug' => true,
    'twig.path' => __DIR__.'/Resources/views',
));

$application->boot();

