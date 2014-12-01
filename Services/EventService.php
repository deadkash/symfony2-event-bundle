<?php

namespace Sp\EventBundle\Services;


use Doctrine\ORM\EntityManager;
use Sp\EventBundle\Classes\Condition;
use Sp\EventBundle\Classes\Consequence;
use Sp\EventBundle\Classes\Event;
use Sp\EventBundle\Entity\EventCondition;
use Sp\EventBundle\Entity\EventConsequence;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventService {

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /**
     * Constructor
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns events list
     * @return array
     */
    public function getEventTypes()
    {
        $dir = __DIR__.'/../Events/';

        $files = scandir($dir);
        $events = array();
        foreach ($files as $file) {

            $className = "Sp\\EventBundle\\Events\\".substr($file, 0, strlen($file) - 4);
            if (!is_dir($dir.$file) && class_exists($className)) {

                $event = new $className( $this->container );
                if (!$event instanceof Event) continue;

                $event->configure();
                $events[ $event->getType() ] = $event->getName();
            }
        }

        return $events;
    }

    /**
     * Return available conditions
     * @return array
     */
    public function getConditionTypes()
    {
        $dir = __DIR__.'/../Conditions/';

        $files = scandir($dir);
        $types = array();

        foreach ($files as $file) {

            $className = "Sp\\EventBundle\\Conditions\\".substr($file, 0, strlen($file) - 4);
            if (!is_dir($dir.$file) && class_exists($className)) {

                $condition = new $className( $this->container );
                if (!$condition instanceof Condition) continue;

                $condition->configure();
                $types[ $condition->getType() ] = $condition;
            }
        }

        return $types;
    }

    /**
     * Returns available consequences
     * @return array
     */
    public function getConsequenceTypes()
    {
        $dir = __DIR__.'/../Consequences/';

        $files = scandir($dir);
        $types = array();

        foreach ($files as $file) {

            $className = "Sp\\EventBundle\\Consequences\\".substr($file, 0, strlen($file) - 4);
            if (!is_dir($dir.$file) && class_exists($className)) {

                $consequence = new $className( $this->container );
                if (!$consequence instanceof Consequence) continue;

                $consequence->configure();
                $types[ $consequence->getType() ] = $consequence->getName();
            }
        }

        return $types;
    }

    /**
     * Returns event instance by type
     * @param string $type
     * @return Event
     */
    public function getEventByType($type)
    {
        $className = "Sp\\EventBundle\\Events\\".$type."Event";
        if (class_exists($className)) {

            /** @var Event $event */
            $event =  new $className( $this->container );
            $event->configure();

            return $event;
        }
        else throw new NotFoundHttpException('Event with type "'.$type.'" not found');
    }

    /**
     * Returns condition instance by type
     * @param string $type
     * @return Condition
     */
    public function getConditionByType($type)
    {
        $className = "Sp\\EventBundle\\Conditions\\".$type."Condition";
        if (class_exists($className)) {

            /** @var Condition $condition */
            $condition = new $className( $this->container );
            $condition->configure();

            return $condition;
        }
        else throw new NotFoundHttpException('Condition with type "'.$type.'" not found');
    }

    /**
     * Returns consequence instance by type
     * @return Consequence
     * @param $type
     */
    public function getConsequenceByType($type)
    {
        $className = "Sp\\EventBundle\\Consequences\\".$type."Consequence";
        if (class_exists($className)) {

            /** @var Consequence $consequence */
            $consequence = new $className( $this->container );
            $consequence->configure();

            return $consequence;
        }
        else throw new NotFoundHttpException('Consequence with type "'.$type.'" not found');
    }

    /**
     * Returns conditions by event
     * @param \Sp\EventBundle\Entity\Event $event
     * @return array
     */
    public function getConditionsByEvent(\Sp\EventBundle\Entity\Event $event)
    {
        $eventConditions = $event->getConditions();
        $conditions = array();

        /** @var EventCondition $eventCondition */
        foreach ($eventConditions as $eventCondition) {

            $condition = $this->getConditionByType($eventCondition->getType());
            $condition->setSerializedParams($eventCondition->getParams());

            $conditions[ $eventCondition->getId() ] = $condition;
        }

        return $conditions;
    }

    /**
     * Returns consequences by event
     * @param \Sp\EventBundle\Entity\Event $event
     * @return array
     */
    public function getConsequencesByEvent(\Sp\EventBundle\Entity\Event $event)
    {
        $eventConsequences = $event->getConsequences();
        $consequences = array();

        /** @var EventConsequence $eventConsequence */
        foreach ($eventConsequences as $eventConsequence) {

            $consequence = $this->getConsequenceByType($eventConsequence->getType());
            $consequence->setSerializedParams($eventConsequence->getParams());

            $consequences[ $eventConsequence->getId() ] = $consequence;
        }

        return $consequences;
    }

    /**
     * Return conditions types with trigger name
     * @param $trigger
     * @return array
     */
    public function getConditionTypesByTrigger($trigger)
    {
        $types = array();
        $availableConditions = $this->getConditionTypes();

        /** @var Condition $condition */
        foreach ($availableConditions as $condition) {
            if ($condition->getTrigger() == $trigger) {
                $types[] = $condition->getType();
            }
        }

        return $types;
    }

    /**
     * Execute event consequences by trigger name
     * @param $name
     * @param $options
     * @return bool
     */
    public function trigger($name, $options = array())
    {
        $types = $this->getConditionTypesByTrigger($name);
        if (empty($types)) return false;

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();

        $events = $em->getRepository('SpEventBundle:Event')->findByConditionTypes($types);

        /** @var \Sp\EventBundle\Entity\Event $event */
        foreach ($events as $event) {

            $eventInstance = $this->getEventByType($event->getType());
            $eventInstance->bind($event);

            if ($eventInstance->check($options)) {
                $eventInstance->executeConsequences($options);
            }
        }

        return true;
    }
} 