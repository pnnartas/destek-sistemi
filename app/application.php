<?php

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Destek\Provider;
use Destek\Provider\UserServiceProvider;

use \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Response;

error_reporting(E_ALL);
ini_set("display_errors", 1);

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
$application->register(new DoctrineOrmServiceProvider(), array(
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







$application['user.manager'] = $application->share(function($application) {
    $userManager = new Destek\Provider\UserProvider($application['db']);
    return $userManager;
});


$application['security.authentication_providers'] = array('main' =>
    new Destek\Provider\AuthProvider($application['user.manager'])
);


// Security Service Provider Register
$application->register(new UserServiceProvider(), array(
    'security.firewalls' => array(
        'main' => array(
            'pattern' => '^.*$',
            'form' => array(
                'login_path' => 'login',
                'check_path' => 'login_check',
                'username_parameter' => 'email',
                'password_parameter' => 'password',
                'user_path'
            ),
            'anonymous' => true,
            'logout' => array(
                'logout_path' => '/logout',
            ),
            'users' => $application->share(function($application) { return $application['user.manager']; }),
        ),
    ),
        'security.authentication_providers' => $application['security.authentication_providers']
));



$application['security.authentication.success_handler.main'] =
    new Destek\Handler\AuthenticationSuccessHandler($application['security.http_utils'], array(), $application);



// Session Service Provider Register
$application->register(new SessionServiceProvider(), array(
    'session.storage.save_path' => __DIR__.'/../vendor/sessions',
));



// Hata ayıklama modunu aktif eder..
$application['debug'] = false;

$application->error(function (\Exception $e, $code) use ($application) {

    if ($application['debug']) {
        return;
    }
    var_dump($e->getMessage());exit;
    switch ($code) {
        case 404:
            $message = 'Aradığınız sayfa bulunamadı.';
            break;
        default:
            $message = 'İşlem sırasında bir hata oluştu.';
    }
    return new Response($message, $code);
});


$application->boot();


