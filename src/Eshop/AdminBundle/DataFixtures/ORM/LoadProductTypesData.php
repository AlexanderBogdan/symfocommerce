<?php
/**
 * Created by PhpStorm.
 * User: user01
 * Date: 27.01.17
 * Time: 11:37
 */

namespace Eshop\AdminBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Eshop\ShopBundle\Entity\ProductType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProductTypesData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $types = file($this->container->get('kernel')->getRootDir() . '/../types.txt');

        $prodType = new ProductType();

        foreach ($types as $type) {
            $productType = clone $prodType;

            $productType->setName($type);

            $manager->persist($productType);
        }
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 10; // the order in which fixtures will be loaded
    }

}