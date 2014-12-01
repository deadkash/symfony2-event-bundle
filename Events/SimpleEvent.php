<?php

namespace Sp\EventBundle\Events;


use Sp\EventBundle\Classes\Event;
use Sp\EventBundle\Conditions\DateCondition;
use Sp\EventBundle\Consequences\SimpleEmailConsequence;
use Sp\EventBundle\Consequences\SimpleMegaplanConsequence;
use Sp\EventBundle\Consequences\SimpleSmsConsequence;

class SimpleEvent extends Event {

    public function configure()
    {
        $this
            ->setType('Simple')
            ->setName('Простое')
            ->setCronable(true)
            ->setAvailableConditions(array(
                new DateCondition( $this->container )
            ))
            ->setAvailableConsequences(array(
                new SimpleEmailConsequence( $this->container ),
                new SimpleSmsConsequence( $this->container ),
                new SimpleMegaplanConsequence( $this->container )
            ))
        ;
    }
} 