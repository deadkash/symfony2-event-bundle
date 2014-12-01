<?php

namespace Sp\EventBundle\Classes;


class Param {

    /** @var string Param name */
    protected $name;

    /** @var string Param type = form field type */
    protected $type;

    /** @var string Form label */
    protected $label;

    /** @var mixed Param value */
    protected $value;

    /** @var array Form options */
    protected $options = array();

    /**
     * @param $name
     * @param $type
     * @param $label
     * @param array $options
     */
    public function __construct($name, $type, $label, $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}