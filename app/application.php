<?php

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;

// Doctrine servisi sağlayıcısını applicationa kayıt eder
$application->register(new DoctrineServiceProvider(), array(
    'db.options' => $application['db.options'],
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

        return sprintf($application['asset_path'].'/%s', ltrim($asset, '/'));
    }));

    return $twig;
}));

// Doctrine Orm servis sağlayıcısını kayıt eder
$application->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
    "orm.proxies_dir" => "/path/to/proxies",
    "orm.em.options" => array(
        "mappings" => array(
            array(
                "type" => "simple_yml",
                "namespace" => "Destek\Entity",
                "path" => __DIR__."/../src/Destek/Resources/config/doctrine",
            ),
        ),
    ),
));

$application->boot();


