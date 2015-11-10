<?php
namespace Destek\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Destek\Form\CategoryForm;
use Destek\Entity\Category;



class CategoriesController
{
    /**
     * Kategorileri Listeler
     */
    public function indexAction(Application $application)
    {

        $userId = $application['session']->get('userId');

        // Kategorileri sadece yönetici görebilir.
        if ($application['security']->isGranted('ROLE_ADMIN')) {

            $categories = $application['orm.em']->getRepository('Destek\Entity\Category')->findBy(array(
                'deleted' => 0,
            ), array(
                'id' => 'DESC'
            ));
        }

        return $application['twig']->render('category/categories_list.html.twig',array('categories' => $categories));
    }

    /**
     * Kategori Ekleme
     * @param Request $request
     * @param Application $application
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function addAction(Request $request,Application $application)
    {

        $userId = $application['session']->get('userId');
        $view   = new \Zend_View();
        $form   = new CategoryForm();

        $form->setAttrib('class', 'form-horizontal');


        $form->setAction($application['url_generator']->generate('category_add'));
        $form->setView($view);


        if ($request->getMethod() == 'POST') {
            if ($form->isValid($request->request->all())) {
                $data = $form->getValues();

                // Kategori adı var mı?
                $categoryName = $application['orm.em']->getRepository('Destek\Entity\Category')->findOneBy(array(
                    'deleted' => 0,
                    'name' => $data['name']
                ));

                // Kategori adı varsa;
                if ($categoryName !== null) {
                    $form->getElement('name')->setErrors(array('Bu kategori adı sistemde mevcut!'));
                    return $application['twig']->render('category/categories_add.html.twig', array('form' => $form));
                }

                //Kategori adı sistemde mevcut değil ise ekleme yapılır.
                $category = new Category();
                $category->setName($data['name']);
                $category->setUserId($userId);
                $category->setDeleted(false);
                $category->setCreatedAt(new \DateTime());
                $application['orm.em']->persist($category);
                $application['orm.em']->flush();

                $message = 'Yeni Kategori Oluşturuldu.';
                $application['session']->getFlashBag()->add('success', $message);

                $redirect = $application['url_generator']->generate('category');

                return $application->redirect($redirect);
            }
        }


        return $application['twig']->render('category/categories_add.html.twig', array('form' => $form));
    }

    /**
     * @param $id Kategori düzenleme esnasında get parametresinden gelen id' yi alır.
     * @param Request $request
     * @param Application $application
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction($id,Request $request,Application $application)
    {

        $category = $application['orm.em']->getRepository('Destek\Entity\Category')->findOneBy(array(
            'deleted' => 0,
            'id' => $id,
        ));

        if ($category === null) {
            return $application->redirect($application['url_generator']->generate('dashboard'));
        }
        //$userId = $application['session']->get('userId');
        $view   = new \Zend_View();
        $form   = new CategoryForm();


        $form->setAttrib('class', 'form-horizontal');
        $form->getElement('name')->setValue($category->getName());

        $form->setAction($application['url_generator']->generate('category_edit', array('id' => $id)));
        $form->setView($view);

        if ($request->getMethod() == 'POST') {
            //Kategori düzenleme işlemi
            if ($form->isValid($request->request->all())) {
                $data = $form->getValues();
                // Kategori adı sistemde varmı
                $categoryName = $application['orm.em']->getRepository('Destek\Entity\Category')->recordCategory($id, $data['name']);
                // Kategori adı sistemde varsa;
                if ($categoryName !== null) {
                    $form->getElement('name')->setErrors(array('Bu kategori adı sistemde kayıtlı!'));
                    return $application['twig']->render('category/categories_edit.html.twig', array('form' => $form));
                }
                $category->setName($data['name']);
                $category->setUpdatedAt(new \DateTime());
                $application['orm.em']->persist($category);
                $application['orm.em']->flush();
                $message = 'Kategorisi başarıyla güncellendi.';
                $application['session']->getFlashBag()->add('success', $message);
                $redirect = $application['url_generator']->generate('category');
                return $application->redirect($redirect);
            }
        }

        return $application['twig']->render('category/categories_edit.html.twig', array('form' => $form));
    }

    /**
     * @param $id Kategori silme isteği geldiğinde gette bulunan id'yi alır.
     * @param Request $request
     * @param Application $application
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id,Request $request,Application $application)
    {

        $category = $application['orm.em']->getRepository('Destek\Entity\Category')->findOneBy(array(
            'deleted' => 0,
            'id' => $id
        ));

        if ($category !== null) {
            $category->setDeleted(true);
            $category->setDeletedAt(new \DateTime());
            $application['orm.em']->persist($category);
            $application['orm.em']->flush();
            $message = 'Kategori başarıyla silindi.';
            $application['session']->getFlashBag()->add('success', $message);
            $redirect = $application['url_generator']->generate('category');
        } else {
            $redirect = $application['url_generator']->generate('dashboard');
        }
        return $application->redirect($redirect);
    }



}