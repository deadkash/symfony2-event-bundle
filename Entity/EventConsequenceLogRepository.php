<?php

namespace Sp\EventBundle\Entity;


use Doctrine\ORM\EntityRepository;

class EventConsequenceLogRepository extends EntityRepository {

    /**
     * @param \DateTime $eventUpdated
     * @param integer|mixed $consequenceId
     * @param mixed $unique
     * @return array
     */
    public function findByEventUpdatedAndConsequenceIdAndUnique($eventUpdated, $consequenceId = false, $unique = false)
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.created >= :updated')
            ->setParameter('updated', $eventUpdated);

        if ($consequenceId) {
            $qb->andWhere('l.consequence = :consequence')
               ->setParameter('consequence', $consequenceId);
        }

        if ($unique) {
            $qb->andWhere('l.uniqueField = :unique')
               ->setParameter('unique', $unique);
        }

        return $qb->getQuery()->getResult();
    }
} 