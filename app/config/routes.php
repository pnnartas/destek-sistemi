<?php


$before = function() use ($application) {
    if ($application['security']->isGranted('IS_AUTHENTICATED_FULLY') == false) {
        $redirect = $application['url_generator']->generate('dashboard');
        return $application->redirect($redirect);
    }
};


$application->get('/', 'Destek\Controller\DashboardController::dashboardAction')
    ->bind('dashboard');

$application->get('/login', 'Destek\Controller\SecurityController::loginAction')
    ->bind('login');

$application->match('/logout', 'Destek\Controller\SecurityController::logoutAction')
    ->bind('logout')->method('POST|GET');

$application->post('/login_check', function() {})
    ->bind('login_check');


//Category
$application->get('/category', 'Destek\Controller\CategoriesController::indexAction')
    ->bind('category')->before($before);

$application->get('/category/add', 'Destek\Controller\CategoriesController::addAction')
    ->bind('category_add')->method('GET|POST')->before($before);

$application->get('/category/edit/{id}', 'Destek\Controller\CategoriesController::editAction')
    ->bind('category_edit')->method('GET|POST')->before($before);

$application->delete('/category/delete/{id}', 'Destek\Controller\CategoriesController::deleteAction')
    ->bind('category_delete')->method('GET|POST')->before($before);


// Ticket
$application->get('/ticket', 'Destek\Controller\TicketController::indexAction')
    ->bind('ticket')->before($before);

$application->get('/ticket/solve/{id}', 'Destek\Controller\TicketController::solveAction')
    ->bind('ticket_solve')->method('GET|POST')->before($before);

$application->get('/ticket/add', 'Destek\Controller\TicketController::addAction')
    ->bind('ticket_add')->method('GET|POST')->before($before);

$application->get('/ticket/show/{id}', 'Destek\Controller\TicketController::showAction')
    ->bind('ticket_show')->method('GET|POST')->before($before);