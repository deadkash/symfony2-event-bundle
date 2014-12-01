<?php

namespace Sp\EventBundle\Entity;


use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository {

    /**
     * @param $conditionsTypes
     * @return array
     */
    public function findByConditionTypes($conditionsTypes)
    {
        $qb = $this->createQueryBuilder('e');
        return $qb->join('e.conditions', 'c')
            ->where($qb->expr()->in('c.type', $conditionsTypes))
            ->andWhere('e.enabled = 1')
            ->getQuery()
            ->getResult();
    }
} 