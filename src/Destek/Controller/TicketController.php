<?php

namespace Destek\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Destek\Form\TicketForm;
use Destek\Entity\Tickets;
use Destek\Entity\Category;
use Destek\Entity\TicketCategory;



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
        // Ticket'a ait olan kategorileri listeler
        foreach($tickets as &$ticket)
        {
            $ticketCategories = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketCategories($ticket['id']);
            $ticket['categories'] =$ticketCategories;
        }

        return $application['twig']->render('ticket/ticket_list.html.twig',array('tickets' => $tickets));
    }

    /**
     * @param $id Admin Tarafından Ticket durumunu çözüldü olarak işaretler.
     * @param Application $application
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function solveAction($id, Application $application ,Request $request)
    {
        if ($application['security']->isGranted('ROLE_USER')) {

            return $application->redirect($application['url_generator']->generate('dashboard'));
        } else {

            $redirect = $application['url_generator']->generate('ticket');

            $ticket = $application['orm.em']->getRepository('Destek\Entity\Tickets')->findOneBy(array('id' => $id, 'deleted' => false));
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
            $application['orm.em']->flush();;

            $application['session']->getFlashBag()->add('success', 'Ticket çözüldü.');
            return $application->redirect($redirect);
        }
    }


    public function showAction($id, Application $application ,Request $request)
    {

        return $application['twig']->render('ticket/ticket_show.html.twig');
    }

    public function addAction(Application $application ,Request $request)
    {
        $view = new \Zend_View();
        $form = new TicketForm();


        if ($application['security']->isGranted('ROLE_ADMIN')) {

            return $application->redirect($application['url_generator']->generate('ticket'));
        } else {

            $priorityList = $application['orm.em']->getRepository('Destek\Entity\Priority')->getPriority();

            $categoryList = $application['orm.em']->getRepository('Destek\Entity\Category')->getCategory();




            $form->setAttrib('class', 'form-horizontal');
            $form->setAction($application['url_generator']->generate('ticket_add'));
            $form->getElement('priorityId')->addMultiOptions($priorityList);
            $form->getElement('categories')->setIsArray(true)->addMultiOptions($categoryList);

            $form->setView($view);

            if ($request->getMethod() == 'POST') {
                if ($form->isValid($request->request->all())) {
                    $data = $form->getValues();
                    $ticket = new Tickets();

                    $ticket->setSubject($data['subject']);
                    $ticket->setMessage($data['message']);

                    // Yeni Destek
                    $ticket->setStatusId(1);
                    // Yetkili Yönetici
                    $ticket->setRecipientId(1);
                    // Ticket Oluşturan User Id
                    $ticket->setOwnerUserId($application['session']->get('userId'));
                    $ticket->setPriorityId($data['priorityId']);
                    $ticket->setIp($request->server->get('REMOTE_ADDR'));
                    $ticket->setDeleted(false);
                    $ticket->setCreatedAt(new \DateTime());

                    $application['orm.em']->persist($ticket);
                    $application['orm.em']->flush();


                    if (is_array($data['categories']) && count($data['categories']) > 0) {
                        foreach ($data['categories'] as $category) {
                            $ticketCategories = new TicketCategory();
                            $ticketCategories->setTicketId($ticket->getId());
                            $ticketCategories->setCategoryId($category);
                            $ticketCategories->setCreatedAt(new \DateTime());
                            $ticketCategories->setDeleted(false);

                            $application['orm.em']->persist($ticketCategories);
                            $application['orm.em']->flush();
                        }
                    }

                    $message = 'Destek başarıyla oluşturuldu.';
                    $application['session']->getFlashBag()->add('success', $message);

                    return $application->redirect($application['url_generator']->generate('ticket'));
                }
            }

            return $application['twig']->render('ticket/ticket_add.html.twig',array('form' => $form));
        }

    }

}