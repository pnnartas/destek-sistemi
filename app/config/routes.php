<?php

$application->get('/', 'Destek\Controller\DashboardController::dashboardAction')
    ->bind('dashboard');

$application->get('/login', 'Destek\Controller\SecurityController::loginAction')
    ->bind('login');

$application->post('/login_check', 'Destek\Controller\SecurityController::checkAction')
    ->bind('login_check');

$application->post('/logout', 'Destek\Controller\SecurityController::logoutAction')
    ->bind('logout');

