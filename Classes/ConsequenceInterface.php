<?php

namespace Sp\EventBundle\Classes;


interface ConsequenceInterface {

    /**
     * Configure consequence, setting up properties
     * @return void
     */
    public function configure();

    /**
     * Execute consequence
     * @param Event $event
     * @param array $options
     * @return void
     */
    public function execute(Event $event, $options = array());
} 