<?php

namespace Sp\EventBundle\Events;


use Sp\EventBundle\Classes\Consequence;
use Sp\EventBundle\Classes\Event;
use Sp\EventBundle\Conditions\DateCondition;
use Sp\EventBundle\Conditions\FamilyBalanceSystemCondition;
use Sp\EventBundle\Conditions\FamilyFirstOperationCondition;
use Sp\EventBundle\Conditions\FamilyStatusChangeCondition;
use Sp\EventBundle\Conditions\FamilyStatusSystemCondition;
use Sp\EventBundle\Conditions\HomeworkFailCondition;
use Sp\EventBundle\Conditions\NewFamilyCondition;
use Sp\EventBundle\Consequences\FamilyStatusChangeMegaplanConsequence;
use Sp\EventBundle\Consequences\SimpleEmailConsequence;
use Sp\EventBundle\Consequences\SimpleMegaplanConsequence;
use Sp\EventBundle\Consequences\SimpleSmsConsequence;
use Sp\EventBundle\Consequences\SingleFamilyManagerEmailConsequence;

class SystemEvent extends Event {

    public function configure()
    {
        $this
            ->setType('System')
            ->setName('Система')
            ->setAvailableConditions(array(
                new NewFamilyCondition( $this->container ),
                new FamilyStatusChangeCondition( $this->container ),
                new DateCondition( $this->container ),
                new FamilyBalanceSystemCondition( $this->container ),
                new HomeworkFailCondition( $this->container ),
                new FamilyFirstOperationCondition( $this->container ),
                new FamilyStatusSystemCondition( $this->container )
            ))
            ->setAvailableConsequences(array(
                new SimpleEmailConsequence( $this->container ),
                new SimpleSmsConsequence( $this->container ),
                new SimpleMegaplanConsequence( $this->container ),
                new FamilyStatusChangeMegaplanConsequence( $this->container ),
                new SingleFamilyManagerEmailConsequence( $this->container )
            ))
        ;
    }

    /**
     * @param array $options
     * @return void
     */
    public function executeConsequences($options = array())
    {
        /** @var Consequence $consequence */
        foreach ($this->consequences as $consequence) {
            $consequence->execute($this, $options);
            $consequence->log();
        }
    }
} 