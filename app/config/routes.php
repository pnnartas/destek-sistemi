<?php

$application->get('/', 'Destek\Controller\DashboardController::dashboardAction')
    ->bind('dashboard');

$application->get('/login', 'Destek\Controller\SecurityController::loginAction')
    ->bind('login');

$application->match('/logout', 'Destek\Controller\SecurityController::logoutAction')
    ->bind('logout')->method('POST|GET');

$application->post('/login_check', function() {})
    ->bind('login_check');



$application->get('/categories', 'Destek\Controller\TicketCategoriesController::indexAction')
    ->bind('categories');


$application->get('/categories/add', 'Destek\Controller\TicketCategoriesController::addAction')
    ->bind('categories/add');

$application->get('/categories/edit', 'Destek\Controller\TicketCategoriesController::editAction')
    ->bind('categories/edit');