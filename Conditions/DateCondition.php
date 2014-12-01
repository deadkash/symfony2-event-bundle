<?php

namespace Sp\EventBundle\Conditions;


use Sp\EventBundle\Classes\Condition;
use Sp\EventBundle\Classes\Param;

class DateCondition extends Condition {

    public function configure()
    {
        $this
            ->setName('Дата')
            ->setType('Date')
            ->setDescription('Наступление опредленной даты');

        $date = new Param( 'date', 'text', 'Дата', array(
            'attr' => array('class' => 'date')
        ));

        $this->setParam($date);
    }

    /**
     * @return bool
     */
    public function check() {
        return date('d-m-Y') == $this->getParamValue('date');
    }
} 