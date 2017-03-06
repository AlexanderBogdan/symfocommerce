<?php
namespace Eshop\AdminBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Eshop\ShopBundle\Entity\OrderProduct;
use Eshop\ShopBundle\Entity\Orders;

class IsGiftListener implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->setIsGiftForOrderProducts($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->setIsGiftForOrderProducts($args);
    }

    public function setIsGiftForOrderProducts(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em =$args->getEntityManager();

        /** $entity Orders */
        if ($entity instanceof Orders) {

            if ($entity->getIsGift() ) {
                $orderProducts = $entity->getOrderProducts();

                /**
                 * @var int $key
                 * @var OrderProduct $product
                 */
                foreach ($orderProducts as $key => $product) {
                    $product->setIsGift(true);
                }
                $em->flush();
            }
        }
    }

}
