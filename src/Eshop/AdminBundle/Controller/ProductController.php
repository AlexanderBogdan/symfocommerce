<?php

namespace Eshop\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Eshop\ShopBundle\Entity\Image;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Eshop\ShopBundle\Entity\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Product controller.
 *
 * @Route("/admin/product")
 */
class ProductController extends Controller
{
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="admin_product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $productRepository = $em->getRepository('ShopBundle:Product');
        $paginator = $this->get('knp_paginator');
        $search = $request->query->get('search') ?: null;

        $qb = $productRepository->getAllProductsAdminQB(false, $search);
        $options = $productRepository->getAllProductsAdminQB(true);
        $limit = $this->getParameter('admin_products_pagination_count');

        $products = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        $uploadCsvForm = $this->uploadForm();

        return array(
            'entities' => $products,
            'options'  => $options,
            'upload_csv_form' => $uploadCsvForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="admin_product_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('Eshop\ShopBundle\Form\Type\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //update uploaded images entities
            $imageIdString = $request->request->get('filenames');
            if ($imageIdString != '') {
                $imageIdArray = explode(',', $imageIdString);
                array_pop($imageIdArray);

                $em = $this->getDoctrine()->getManager();
                $imageRepository = $em->getRepository('ShopBundle:Image');
                foreach ($imageIdArray as $imageId) {
                    $image = $imageRepository->find($imageId);
                    $image->setProduct($product);
                    $product->addImage($image);
                    $em->persist($image);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin_product_show', array('id' => $product->getId()));
        }

        return array(
            'entity' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", name="admin_product_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        if ($product->getDeleted()) {
            return $this->render('@Admin/Product/deleted.html.twig');
        }

        return array(
            'entity' => $product,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="admin_product_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('Eshop\ShopBundle\Form\Type\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //update uploaded images entities
            $imageIdString = $request->request->get('filenames');
            if ($imageIdString != '') {
                $imageIdArray = explode(',', $imageIdString);
                array_pop($imageIdArray);

                $em = $this->getDoctrine()->getManager();
                $imageRepository = $em->getRepository('ShopBundle:Image');
                foreach ($imageIdArray as $imageId) {
                    $image = $imageRepository->find($imageId);
                    $image->setProduct($product);
                    $product->addImage($image);
                    $em->persist($image);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('admin_product_edit', array('id' => $product->getId()));
        }

        return array(
            'entity' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="admin_product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_product'));
    }

    /**
     * Creates a form to delete a Product entity by id.
     *
     * @param Product $product The Product id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/remove_image", name="remove_image", defaults={"_format"="json"})
     * @Method("POST")
     */
    public function removeImageAction(Request $request)
    {
        $requestData = $request->request;
        $imageEntityId = (int)$requestData->get('imageEntityId');
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $imageRepository = $em->getRepository('ShopBundle:Image');
        /**
         * @var Image $image , $imageRepository
         */
        $image = $imageRepository->find($imageEntityId);
        $product = $image->getProduct();
        $product->removeImage($image);

        $em->persist($product);
        $em->remove($image);

        $em->flush();

        $data = json_encode(array('success' => true));
        $headers = array('Content-type' => 'application-json; charset=utf8');
        $response = new Response($data, 200, $headers);
        return $response;
    }

//    /**
//     * @Route("/autocomplete/get-products", name="products_autocomplete")
//     */
//    public function getAllProductsAutocomplete(Request $request)
//    {
//        $em = $this->get('doctrine.orm.entity_manager');
//        $result = [];
//        $term = $request->query->get('term');
//
//        $entities = $em
//            ->getRepository(Product::class)
//            ->getForAutocomplete($term)
//        ;
//
//        foreach($entities as $entity) {
//            $result[] = [
//                'id' => $entity->getId(),
//                'value' => $entity->getName()
//            ];
//        }
//
//        $response = new JsonResponse($result);
//
//        return $response;
//    }

    /**
     * @Route("/export/csv", name="export_csv")
     * @Method({"GET", "POST"})
     */
    public function exportCsvAction(Request $request)
    {
        $importService = $this->get('app.import_export');

        $result = $importService->exportProductsCSV();

        return $result;
    }

    /**
     * @Route("/import/csv", name="import_csv")
     * @Method({"GET", "POST"})
     */
    public function importCsvAction(Request $request)
    {
        $importService = $this->get('app.import_export');

        $uploadForm = $this->uploadForm();
        $uploadForm->handleRequest($request);

        if ($uploadForm->isSubmitted()) {
            $file = $uploadForm->get('submitFile');

            // Your csv file here when you hit submit button
            $file = $file->getData();
            $importService->importProductsCSV($file);
        }

        $this->get('session')->getFlashBag()->add(
            'upload',
            'CSV file was uploaded!'
        );


        return $this->redirectToRoute('admin_product');
    }

    private function uploadForm()
    {
         return $this->createFormBuilder()
            ->add('submitFile', FileType::class, [
                'label' => 'File to Submit'
            ])
            ->getForm()
        ;
    }

}
