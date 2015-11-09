<?php

$application->get('/', 'Destek\Controller\DashboardController::dashboardAction')
    ->bind('dashboard');

