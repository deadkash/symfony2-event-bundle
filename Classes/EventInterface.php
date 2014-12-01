<?php

namespace Sp\EventBundle\Classes;


interface EventInterface {

    /**
     * Configure event, setting up properties
     * @return void
     */
    public function configure();

    /**
     * Check all condtions
     * @param array $options
     * @return boolean
     */
    public function check($options = array());

    /**
     * Returns collection, selected by event conditions
     * @return mixed
     */
    public function getCollection();
}