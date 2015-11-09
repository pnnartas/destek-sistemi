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

$application['twig'] = $application->share($application->extend('twig', function($twig, $application) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($application) {
        // implement whatever logic you need to determine the asset path

        return sprintf($application['asset_path'].'/%s', ltrim($asset, '/'));
    }));

    return $twig;
}));

$application->boot();

