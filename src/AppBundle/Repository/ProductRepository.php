<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Product;

class ProductRepository extends EntityRepository
{
    /**
     * @return Product[]
     */
    public function findAllPublished()
    {
        return $this->findBy(array(
            'isPublished' => true
        ));
    }

    /**
     * @param string $term
     * @return Product[]
     */
    public function search($term)
    {
        if (!$term) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->execute();
    }
}