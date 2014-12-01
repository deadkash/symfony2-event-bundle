<?php

namespace Sp\EventBundle\Classes;


use Doctrine\ORM\QueryBuilder;
use Sp\EventBundle\Entity\EventCondition;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class Condition implements ConditionInterface {

    /** @var string Condition name */
    protected $name = '';

    /** @var string Condition type = class name without "Condition" */
    protected $type = '';

    /** @var string Condition description */
    protected $description = '';

    /** @var array Condition params, this params build user form */
    protected $params = array();

    /** @var ContainerInterface */
    public $container;

    /** @var EventCondition DB entity with current condition type */
    protected $entity;

    /** @var string Trigger name for execute from system */
    protected $trigger = '';

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
     * @param array $options
     * @return bool
     */
    public function check($options = array()) {
        return true;
    }

    /**
     * @param QueryBuilder $qb
     * @return mixed
     */
    public function render(QueryBuilder &$qb) {}

    /**
     * Returns form instance, build by params
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
            if ($param->getValue() !== null) $options['data'] = $param->getValue();

            $formBuilder->add($param->getName(), $param->getType(), $options);
        }

        $formBuilder->add('condition', 'hidden', array('data' => $this->getType()));
        $formBuilder->add('submit', 'submit', array('label' => ($update) ? 'Сохранить' : 'Добавить'));
        $formBuilder->setAction($action);

        return $formBuilder->getForm();
    }

    /**
     * Bind user data with parameters
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
     * Returns serialized params
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
     * Return user params as string
     * @return string
     */
    public function getView()
    {
        $view = '';

        if (empty($this->params)) return $this->getName();

        /** @var Param $param */
        foreach ($this->params as $param) {
            $view .= $param->getLabel().' '.$param->getValue().' ';
        }

        return $view;
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
     * @param Param $param
     * @return $this
     */
    public function setParam(Param $param){
        $this->params[ $param->getName() ] = $param;
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
     * @return array
     */
    public function getParams()
    {
        return $this->params;
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
     * @return EventCondition
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param EventCondition $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * @param string $trigger
     * @return $this
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }
}