<?php

namespace Eshop\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends ClosureTreeRepository
{
    /**
     * @param bool $showEmpty
     * @param string $order
     * @param string $sort
     * @return array
     */
    public function getAllCategories($showEmpty = true, $sort = 'name', $order = 'ASC')
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('ShopBundle:Category', 'c');

        if (!$showEmpty) {
            $qb->innerJoin('c.products', 'p')
                ->andWhere('p.quantity <> 0');
        }

        $qb->orderBy('c.' . $sort, $order)
        ->setMaxResults(10);

        return $qb->getQuery()->getResult();
    }

    /**
     * query for admin paginator
     *
     * @return QueryBuilder
     */
    public function getAllCategoriesAdminQB($forAutocomplete, $search = null)
    {

        $qb = $this->createQueryBuilder('category');

        if ($forAutocomplete) {
            $qb
                ->select('DISTINCT category.name')
            ;

            return $qb
                ->getQuery()
                ->getResult()
            ;
        }

        $qb->select('category');

        if (!empty($search)) {
            $qb->andWhere('category.name LIKE :product_name')
                ->setParameter('product_name', '%'.$search.'%');
        }
        return $qb;
    }

    /**
     * @param $slug string
     * @return mixed
     */
    public function findBySlug($slug)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('ShopBundle:Category', 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return mixed
     */
    public function getFirstCategory()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('ca')
            ->from('ShopBundle:Category', 'ca')
            ->orderBy('ca.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * query for admin paginator
     *
     * @return array
     */
    public function findForChosenTree()
    {

        $qb = $this->createQueryBuilder('category')
            ->select(
                'category.name as title',
                'category.id',
                'category.level',
                'p.id as parent_id'
            )
            ->leftJoin('category.children', 'ch')
            ->leftJoin('category.parent', 'p')
            ->getQuery()
            ->getResult()
        ;

        return $qb;
    }
}
