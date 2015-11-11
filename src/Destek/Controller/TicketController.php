<?php

namespace Destek\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Destek\Form\TicketForm;
use Destek\Entity\Tickets;
use Destek\Entity\Category;
use Destek\Entity\TicketCategory;
use Destek\Form\TicketReplyForm;
use Destek\Entity\TicketReplies;
use Zend_Validate_File_Upload;



class TicketController
{
    public function indexAction(Request $request,Application $application)
    {
        $userId   = $application['session']->get('userId');

        $filter = $this->_setFilter($request);

        $category = $application['orm.em']->getRepository('Destek\Entity\Category')->findBy(array('deleted'=>false));
        $priority = $application['orm.em']->getRepository('Destek\Entity\Priority')->findBy(array('deleted'=>false));

        // Yöneticinin ticketlerı
        if ($application['security']->isGranted('ROLE_ADMIN')) {

            $tickets = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketList($filter,null, $userId);
        } else if ($application['security']->isGranted('ROLE_USER')) {
            // Kullanıcının ticketları
            $tickets = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketList($filter,$userId);
        }
        // Ticket'a ait olan kategorileri listeler
        foreach($tickets as &$ticket)
        {
            $ticketCategories = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketCategories($ticket['id']);
            $ticket['categories'] =$ticketCategories;
        }

        return $application['twig']->render('ticket/ticket_list.html.twig',array(
            'tickets' => $tickets,
            'categories' => $category,
            'priority'  => $priority
        ));
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
        $view = new \Zend_View();
        $form = new TicketReplyForm();
        $userId = $application['session']->get('userId');

        // Kullanıcının ticketları
        if ($application['security']->isGranted('ROLE_USER')) {
            $ticket = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketList(null,$userId, null, $id);
            // Yöneticiye gelen ticketları listeler
        } else if ($application['security']->isGranted('ROLE_ADMIN')) {
            $ticket = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketList(null,null, $userId, $id);
        }

        // Desteğe ait olan kategorileri listeler
        $ticketCategories = $application['orm.em']->getRepository('Destek\Entity\Tickets')->getTicketCategories($ticket['id']);
        // Desteğe verilen cevaplar
        $ticketReplies    = $application['orm.em']->getRepository('Destek\Entity\TicketReplies')->getTicketReplies($ticket['id']);
        // Ticketı oluşturan user bilgilerini çeker.
        $user = $application['orm.em']->getRepository('Destek\Entity\User')->findOneBy(array('id' => $ticket['owner_user_id']));

        $form->setAttrib('class', 'form-horizontal');
        $form->setAction($application['url_generator']->generate('ticket_show', array('id' => $id)));
        $form->setView($view);

        if ($request->getMethod() == 'POST') {
            if ($form->isValid($request->request->all())) {
                $data = $form->getValues();

                $ticketReplies = new TicketReplies();
                $ticketReplies->setTicketId($id);
                $ticketReplies->setReplyUserId($application['session']->get('userId'));
                $ticketReplies->setMessage($data['message']);
                $ticketReplies->setIp($request->server->get('REMOTE_ADDR'));
                $ticketReplies->setDeleted(false);
                $ticketReplies->setCreatedAt(new \DateTime());

                $application['orm.em']->persist($ticketReplies);
                $application['orm.em']->flush();

                $message = 'Destek cevaplandı.';
                $application['session']->getFlashBag()->add('success', $message);
                $redirect = $application['url_generator']->generate('ticket_show', array('id' => $id));
                return $application->redirect($redirect);
            }
        }

        return $application['twig']->render('ticket/ticket_show.html.twig', array(
            'form' => $form,
            'ticketReplies' => $ticketReplies,
            'ticket' => $ticket,
            'ticketCategories' => $ticketCategories,
            'user'  => $user
        ));
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

                    $file = $request->files->get('file');


                    if ($file !== null) {

                        $filename = $file->getClientOriginalName();

                         $file->move(__DIR__.'/../../../web/upload', $file->getClientOriginalName());
                    }

                    $ticket = new Tickets();

                    $ticket->setSubject($data['subject']);
                    $ticket->setMessage($data['message']);
                    $ticket->setTicketFile($filename);
                    //$ticket->setTicketFile()
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

    public function _setFilter($request)
    {
        $filter = array();
        if(NULL != $request->get('subject')) {
            $filter['subject']  = $request->get('subject');
        }
        if(NULL !== $request->get('category') &&  $request->get('category') > 0) {
            $filter['category'] = $request->get('category');
        }
        if(NULL !== $request->get('priority') && $request->get('priority') > 0) {
            $filter['priority'] = $request->get('priority');
        }
        if(NULL != $request->get('date')) {
            $filter['date'] = $request->get('date');
        }

        return $filter;
    }

}