<?php

namespace Sp\EventBundle\Consequences;


use Sp\EventBundle\Classes\Consequence;
use Sp\EventBundle\Classes\Event;
use Sp\EventBundle\Classes\Param;

class SimpleEmailConsequence extends Consequence {

    public function configure()
    {
        $this
            ->setName('Простая отправка e-mail')
            ->setType('SimpleEmail')
            ->setDescription('Отправка письма на указанный адрес')
        ;

        $this->setParams(array(
            new Param('email', 'text', 'Email'),
            new Param('subject', 'text', 'Тема письма'),
            new Param('message', 'textarea', 'Сообщение')
        ));
    }

    /**
     * @param Event $event
     * @param array $options
     */
    public function execute(Event $event, $options = array())
    {
        $mailer = $this->container->get('mailer');
        $noreplyEmail = ($this->container->hasParameter('event_noreply_email')) ?
            $this->container->getParameter('event_noreply_email') : false;
        $collectorEmail = ($this->container->hasParameter('event_email_collector')) ?
            $this->container->getParameter('event_email_collector') : false;

        $message = \Swift_Message::newInstance();
        $message->setSubject($this->getParamValue('subject'));
        $message->setBody($this->getParamValue('message'));
        $message->setTo($this->getParamValue('email'));
        $message->setFrom($noreplyEmail);

        if ($collectorEmail) $message->setBcc($collectorEmail);

        $mailer->send($message);
    }

    public function getView()
    {
        return 'Отравка сообщения на '.$this->getParamValue('email').' с темой "'.$this->getParamValue('subject').'" '.
            ' и сообщением "'.$this->getParamValue('message').'"';
    }
} 