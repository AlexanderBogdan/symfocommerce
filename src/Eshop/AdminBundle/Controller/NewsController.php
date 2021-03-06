<?php

namespace Eshop\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Eshop\ShopBundle\Entity\News;
use Symfony\Component\HttpFoundation\Response;

/**
 * News controller.
 *
 * @Route("/admin_news")
 */
class NewsController extends Controller
{
    /**
     * Lists all News entities.
     *
     * @Route("/", name="admin_news")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $newsRepository = $em->getRepository('ShopBundle:News');
        $paginator = $this->get('knp_paginator');
        $search = $request->query->get('search') ?: null;

        $qb = $newsRepository->getAllNewsAdminQB(false, $search);
        $options = $newsRepository->getAllNewsAdminQB(true);

        $limit = $this->getParameter('admin_categories_pagination_count');

        $news = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        return array(
            'entities' => $news,
            'options'  => $options,
        );
    }

    /**
     * Creates a new News entity.
     *
     * @Route("/new", name="admin_news_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $news = new News();
        $form = $this->createForm('Eshop\ShopBundle\Form\Type\NewsType', $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            return $this->redirectToRoute('admin_news_show', array('id' => $news->getId()));
        }

        return array(
            'entity' => $news,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a News entity.
     *
     * @Route("/{id}", name="admin_news_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(News $news)
    {
        $deleteForm = $this->createDeleteForm($news);

        return array(
            'entity' => $news,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing News entity.
     *
     * @Route("/{id}/edit", name="admin_news_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, News $news)
    {
        $deleteForm = $this->createDeleteForm($news);
        $editForm = $this->createForm('Eshop\ShopBundle\Form\Type\NewsType', $news);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($editForm->get('file')->getData() !== null) { // if any file was updated
                $file = $editForm->get('file')->getData();
                $news->removeUpload(); // remove old file, see this at the bottom
                $news->setPath(($file->getClientOriginalName())); // set Image Path because preUpload and upload method will not be called if any doctrine entity will not be changed. It tooks me long time to learn it too.
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('admin_news_edit', array('id' => $news->getId()));
        }

        return array(
            'entity' => $news,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a News entity.
     *
     * @Route("/{id}", name="admin_news_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, News $news)
    {
        $form = $this->createDeleteForm($news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_news'));
    }

    /**
     * Creates a form to delete a News entity.
     *
     * @param News $news The News entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(News $news)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_news_delete', array('id' => $news->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
