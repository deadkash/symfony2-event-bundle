<?php

namespace Sp\EventBundle\Command;


use Doctrine\ORM\EntityManager;
use Sp\EventBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventCheckCommand extends ContainerAwareCommand {

    public function configure()
    {
        $this
            ->setName('event:check')
            ->setDescription('Run check cronable events')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $service = $this->getContainer()->get('event');

        $events = $em->getRepository('SpEventBundle:Event')->findBy(array('enabled' => true, 'cronable' => true));

        /** @var Event $event */
        foreach ($events as $event) {

            $eventInstance = $service->getEventByType($event->getType());
            $eventInstance->bind($event);

            if ($eventInstance->check()) {
                $eventInstance->executeConsequences();
            }
        }

        $output->writeln('Complete!');
    }
} 