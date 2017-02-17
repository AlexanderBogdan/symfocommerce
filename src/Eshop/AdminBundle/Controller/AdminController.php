<?php

namespace Eshop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Category controller.
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array();

    }

    /**
     * Another Variant to upload photos to summernote
     * @Route("/photo/summernote/upload", name="photo_summernote_upload")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function uploadPicture(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var FileBag $files */
        if($files = $request->files) {
//            var_dump($request);die;
            $file = $files->all()['file'];
            $entityName = $request->request->get('entity_name');

            $path = $this->get('app.photo_upload')
                ->upload($file, $entityName);

            return new Response($path, 200);
        } else {
            header('HTTP/1.1 500 Internal Server Booboo');
            header('Content-Type: application/json; charset=UTF-8');
            echo(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            die;
        }
    }
}
