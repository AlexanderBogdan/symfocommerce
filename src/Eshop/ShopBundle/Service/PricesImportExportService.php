<?php
namespace Eshop\ShopBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Eshop\ShopBundle\Entity\Product;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Router;

/**
 * Class PhotoUploadService
 * @package Eshop\ShopBundle\Service
 */
class PricesImportExportService
{

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * PricesImportExportService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return StreamedResponse
     */
    public function exportProductsCSV()
    {
        $response = new StreamedResponse();

        $callback = function() {
            $handle = fopen('php://output', 'w+');

            $results = $this->em->getRepository(Product::class)
                ->getProductsForCSV();

            fputcsv($handle, array('Id', 'Name', 'Price'),',');

            foreach ($results as $key => $result) {
                fputcsv(
                    $handle,
                    array($result['id'], $result['name'], $result['price']),
                    ','
                );
            }

            fclose($handle);
        };

        $response->setCallback($callback);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }

    /**
     * @param File $file
     * @return string
     * @throws \Exception
     */
    public function importProductsCSV($file)
    {
        $fileType = $file->getMimeType();

        $handle = fopen($file, 'r');

        $productRepository = $this->em->getRepository(Product::class);

        $i = 0;
        try{
            while (($data = fgetcsv($handle, $file->getSize(), ",")) !== FALSE) {
                $i++;
                if ( $i == 1) { continue; }
                dump($data);
                /** @var Product $product */
                $product = $productRepository->find($data[0]);

                if($product) {
                    $product->setPrice($data[2]);
                }

            }
            $this->em->flush();

            fclose($handle);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        $result = 'ok';

        return $result;
    }
}
