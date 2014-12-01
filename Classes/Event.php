<?php

namespace Sp\EventBundle\Classes;


use Doctrine\ORM\EntityManager;
use Sp\EventBundle\Entity\EventCondition;
use Sp\EventBundle\Entity\EventConsequence;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class Event implements EventInterface {

    /** @var string Event name */
    protected $name = '';

    /** @var array List of selected conditions */
    protected $conditions = array();

    /** @var array List of available conditions */
    protected $availableConditions = array();

    /** @var array List of selected consequences */
    protected $consequences = array();

    /** @var array List of available consequences */
    protected $availableConsequences = array();

    /** @var string Event type = class name without "Event" */
    protected $type = '';

    /** @var ContainerInterface */
    public $container;

    /** @var bool If true this event run by cron */
    protected $cronable = false;

    /** @var string Unique prefix for log */
    protected $uniquePrefix = '';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    /**
     * @return void
     */
    public function configure() {}

    /**
     * @return array
     */
    public function getCollection() {
        return array();
    }

    /**
     * Check all conditions
     * @param array $options
     * @return bool
     */
    public function check($options = array())
    {
        /** @var Condition $condition */
        foreach ($this->conditions as $condition) {
            if (!$condition->check($options)) return false;
        }

        return true;
    }

    /**
     * Execute all consequences
     * @param array $options
     * @return void
     */
    public function executeConsequences($options = array())
    {
        /** @var Consequence $consequence */
        foreach ($this->consequences as $consequence) {
            if (!$consequence->isAlreadyComplete()) {
                $consequence->queue($options);
                $consequence->log();
            }
        }
    }

    /**
     * Create form for adding condition
     * @param string $action
     * @return \Symfony\Component\Form\Form
     */
    public function createConditionsForm($action)
    {
        $formBuilder = $this->container->get('form.factory')->createBuilder('form');

        /** @var Condition $condition */
        $types = array();
        foreach ($this->availableConditions as $condition) {
            $condition->configure();
            $types[ $condition->getType() ] = $condition->getName();
        }

        $formBuilder->add('condition', 'choice', array(
            'label' => 'Обстоятельства',
            'choices' => $types,
            'empty_data' => '',
            'empty_value' => '',
            'attr' => array('class' => 'sl2')
        ));

        $formBuilder->add('submit', 'submit', array(
            'label' => 'Добавить',
            'attr' => array('class' => 'btn btn-default btn-sm')));
        $formBuilder->setAction($action);

        return $formBuilder->getForm();
    }

    /**
     * Create form for adding consequence
     * @param $action
     * @return \Symfony\Component\Form\Form
     */
    public function createConsequencesForm($action)
    {
        $formBuilder = $this->container->get('form.factory')->createBuilder('form');

        $types = array();
        /** @var Consequence $consequence */
        foreach ($this->availableConsequences as $consequence) {
            $consequence->configure();
            $types[ $consequence->getType() ] = $consequence->getName();
        }

        $formBuilder->add('consequence', 'choice', array(
            'label' => 'Последствия',
            'choices' => $types,
            'empty_data' => '',
            'empty_value' => '',
            'attr' => array('class' => 'sl2')
        ));

        $formBuilder->add('submit', 'submit', array(
            'label' => 'Добавить',
            'attr' => array('class' => 'btn btn-default btn-sm')));
        $formBuilder->setAction($action);

        return $formBuilder->getForm();
    }

    /**
     * Bind DB conditions and consequences to this event
     * @param \Sp\EventBundle\Entity\Event $event
     */
    public function bind(\Sp\EventBundle\Entity\Event $event)
    {
        $service = $this->container->get('event');
        $this->setConditions(array());
        $this->setConsequences(array());

        $eventConditions = $event->getConditions();
        /** @var EventCondition $eventCondition */
        foreach ($eventConditions as $eventCondition) {

            $condition = $service->getConditionByType($eventCondition->getType());
            $condition->setSerializedParams($eventCondition->getParams());
            $condition->setEntity($eventCondition);

            $this->setCondition($condition);
        }

        $eventConsequences = $event->getConsequences();
        /** @var EventConsequence $eventConsequence */
        foreach ($eventConsequences as $eventConsequence) {

            $consequence = $service->getConsequenceByType($eventConsequence->getType());
            $consequence->setSerializedParams($eventConsequence->getParams());
            $consequence->setEntity($eventConsequence);

            $this->setConsequence($consequence);
        }
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $conditions
     * @return $this
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function setCondition(Condition $condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param array $conditions
     * @return $this
     */
    public function setAvailableConditions($conditions)
    {
        $this->availableConditions = $conditions;
        return $this;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function setAvailableCondition(Condition $condition)
    {
        $this->availableConditions[] = $condition;
        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableConditions()
    {
        return $this->availableConditions;
    }

    /**
     * @param Consequence $consequence
     * @return $this
     */
    public function setConsequence(Consequence $consequence)
    {
        $this->consequences[] = $consequence;
        return $this;
    }

    /**
     * @param $consequences
     * @return $this
     */
    public function setConsequences($consequences)
    {
        $this->consequences = $consequences;
        return $this;
    }

    /**
     * @return array
     */
    public function getConsequences()
    {
        return $this->consequences;
    }

    /**
     * @return array
     */
    public function getAvailableConsequences()
    {
        return $this->availableConsequences;
    }

    /**
     * @param array $availableConsequences
     * availableConsequences
     * @return $this
     */
    public function setAvailableConsequences($availableConsequences)
    {
        $this->availableConsequences = $availableConsequences;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCronable()
    {
        return $this->cronable;
    }

    /**
     * @param boolean $cronable
     * @return $this
     */
    public function setCronable($cronable)
    {
        $this->cronable = $cronable;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniquePrefix()
    {
        return $this->uniquePrefix;
    }

    /**
     * @param string $uniquePrefix
     * @return $this
     */
    public function setUniquePrefix($uniquePrefix)
    {
        $this->uniquePrefix = $uniquePrefix;
        return $this;
    }
}