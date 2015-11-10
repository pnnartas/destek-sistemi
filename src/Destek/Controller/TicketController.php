<?php

namespace Destek\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Destek\Form\CategoryForm;
use Destek\Entity\Category;



class TicketController
{
    public function indexAction(Application $application)
    {
        $userId = $application['session']->get('userId');

        // Yöneticinin ticketlerı
        if ($application['security']->isGranted('ROLE_ADMIN')) {

            $tickets = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketList(null, $userId);
        } else if ($application['security']->isGranted('ROLE_USER')) {
            // Kullanıcının ticketları
            $tickets = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketList($userId);
        }

        return $application['twig']->render('ticket/ticket_list.html.twig',array('tickets' => $tickets));
    }

    public function detailAction($id, Application $application ,Request $request)
    {

    }

    public function solveAction($id, Application $application ,Request $request)
    {

        $redirect = $application['url_generator']->generate('ticket');

        $ticket   = $application['orm.em']->getRepository('Destek\Entity\Tickets')->findOneBy(array('id' =>$id, 'deleted' => false));
        if ($ticket === null) {
            $application['session']->getFlashBag()->add('error', 'Ticket bulunamadı.');
            return $application->redirect($redirect);
        } elseif ($ticket->status == 2) {
            $application['session']->getFlashBag()->add('error', 'Bu ticket zaten çözülmüş.');
            return $application->redirect($redirect);
        }
        $ticket->setStatusId(2);
        $ticket->setUpdatedAt(new \DateTime());
        $application['orm.em']->persist($ticket);
        $application['orm.em']->flush();

        ;

        $application['session']->getFlashBag()->add('success', 'Ticket çözüldü.');
        return $application->redirect($redirect);
    }

}