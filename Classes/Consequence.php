<?php

namespace Sp\EventBundle\Classes;


use Doctrine\ORM\EntityManager;
use Sp\EventBundle\Entity\EventConsequence;
use Sp\EventBundle\Entity\EventConsequenceLog;
use Sp\EventBundle\Entity\EventConsequenceTask;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class Consequence implements ConsequenceInterface {

    /** @var string Consequence name */
    protected $name = '';

    /** @var string Consequence type = class name without "Consequence" */
    protected $type = '';

    /** @var string Consequence description */
    protected $description = '';

    /** @var array Consequence parameters, build user form */
    protected $params = array();

    /** @var ContainerInterface */
    public $container;

    /** @var EventConsequence DB entity with current consequence type */
    protected $entity;

    /**
     * Constructor
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     */
    public function configure() {}

    /**
     * Execute consequence
     * @param Event $event
     * @param array $options
     * @return void
     */
    public function execute(Event $event, $options = array()) {}

    /**
     * Add task to queue
     */
    public function queue()
    {
        $em = $this->getEntityManager();

        $task = new EventConsequenceTask();
        $task->setConsequence($this->entity);

        $em->persist($task);
        $em->flush();
    }

    /**
     * Log consequence
     * @param mixed $unique
     * @return void
     */
    public function log($unique = false)
    {
        $em = $this->getEntityManager();

        $log = new EventConsequenceLog();
        $log->setConsequence($this->entity);

        if ($unique)
            $log->setUniqueField($unique);

        $em->persist($log);
        $em->flush();
    }

    /**
     * Checking that this consequence already completed
     * @param mixed $unique
     * @return bool
     */
    public function isAlreadyComplete($unique = false)
    {
        $em = $this->getEntityManager();
        $consequence = $this->getEntity();

        return (bool) $em->getRepository('SpEventBundle:EventConsequenceLog')->findByEventUpdatedAndConsequenceIdAndUnique
        (
            $consequence->getEvent()->getUpdated(),
            $consequence->getId(),
            $unique
        );
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * Create consequence create form
     * @param string $action
     * @param bool $update
     * @return \Symfony\Component\Form\Form
     */
    public function createForm($action, $update = false)
    {
        $formBuilder = $this->container->get('form.factory')->createBuilder('form');

        /** @var Param $param */
        foreach ($this->params as $param) {

            $options = $param->getOptions();
            $options['label'] = $param->getLabel();

            if ($param->getValue() !== null) {
                if ($param->getType() == 'checkbox') {
                    $options['data'] = (bool) $param->getValue();
                }
                else $options['data'] = $param->getValue();
            }

            $formBuilder->add($param->getName(), $param->getType(), $options);
        }

        $formBuilder->add('consequence', 'hidden', array('data' => $this->getType()));
        $formBuilder->add('submit', 'submit', array('label' => ($update) ? 'Сохранить' : 'Добавить'));
        $formBuilder->setAction($action);

        return $formBuilder->getForm();
    }

    /**
     * Bind user data to consequence parameters
     * @param Request $request
     * @return void
     */
    public function handleRequest(Request $request)
    {
        $formData = $request->get('form');

        /** @var Param $param */
        foreach ($this->params as &$param) {
            if (isset($formData[$param->getName()])) {
                $param->setValue( $formData[$param->getName()] );
            }
        }
    }

    /**
     * Returns serialized parameters
     * @return string
     */
    public function getSerializedParams()
    {
        return serialize($this->params);
    }

    /**
     * Set serialized parameters
     * @param string $params
     * @return bool
     */
    public function setSerializedParams($params)
    {
        if (empty($params)) return false;
        $params = unserialize($params);

        /** @var Param $param */
        foreach ($params as $param) {
            $this->params[ $param->getName() ] = $param;
        }

        return true;
    }

    /**
     * Return parameters as string
     * @return string
     */
    public function getView()
    {
        $view = '';

        /** @var Param $param */
        foreach ($this->params as $param) {
            $view .= $param->getLabel().' "<i>'.$param->getValue().'</i>" ';
        }

        return $view;
    }

    /**
     * @param Param $param
     * @return $this
     */
    public function setParam(Param $param){
        $this->params[ $param->getName() ] = $param;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams($params)
    {
        /** @var Param $param */
        foreach ($params as $param) {
            $this->params[ $param->getName() ] = $param;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    public function getParamValue($name)
    {
        if (isset($this->params[$name])) {

            /** @var Param $param */
            $param = $this->params[$name];
            return $param->getValue();
        }

        return false;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
     * @return EventConsequence
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param EventConsequence $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}