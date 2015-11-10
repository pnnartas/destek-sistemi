<?php
namespace Destek\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class TicketCategoriesController
{
    /**
     * Kategorileri Listeler
     */
    public function indexAction(Application $application)
    {
        return $application['twig']->render('categories/categories_list.html.twig');
    }

    public function addAction(Request $request,Application $application)
    {
        return $application['twig']->render('categories/categories_add.html.twig');
    }

    public function editAction(Request $request,Application $application,$categoryId)
    {
        return $application['twig']->render('categories/categories_edit.html.twig');
    }


}