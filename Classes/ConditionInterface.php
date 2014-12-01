<?php

namespace Sp\EventBundle\Classes;


use Doctrine\ORM\QueryBuilder;

interface ConditionInterface {

    /**
     * Use for configure properties
     * @return void
     */
    public function configure();

    /**
     * Check event condition
     * @return boolean
     */
    public function check();

    /**
     * Change queryBuilder for select collection
     * @param QueryBuilder $qb
     * @return void
     */
    public function render(QueryBuilder &$qb);
}