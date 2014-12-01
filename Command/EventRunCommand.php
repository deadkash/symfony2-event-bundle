<?php

namespace Sp\EventBundle\Command;

use Doctrine\ORM\EntityManager;
use Sp\EventBundle\Entity\EventConsequenceTask;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventRunCommand extends ContainerAwareCommand {

    public function configure()
    {
        $this
            ->setName('event:run')
            ->setDescription('Run event tasks')
            ->addArgument('count', InputArgument::REQUIRED, 'Enter tasks count')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('count');
        $service = $this->getContainer()->get('event');

        $this->fixZombieTasks();

        if ($this->isAlreadyRunning()) {
            $output->writeln('Tasks already running');
            return false;
        }

        $tasks = $this->getTasks($count);
        if (!$tasks) $output->writeln('No tasks found');

        /** @var EventConsequenceTask $task */
        foreach ($tasks as $task) {

            $output->write('.');

            $this->setTaskRunning($task);
            $eventConsequence = $task->getConsequence();
            $consequence = $service->getConsequenceByType($eventConsequence->getType());
            $consequence->setSerializedParams($eventConsequence->getParams());
            $consequence->setEntity($eventConsequence);

            $event = $eventConsequence->getEvent();
            $eventInstance = $service->getEventByType($event->getType());
            $eventInstance->bind($event);

            $consequence->execute($eventInstance);

            $this->removeTask($task);
        }

        $output->writeln('Complete!');

        return true;
    }

    /**
     * @param $task
     */
    private function removeTask($task)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $em->remove($task);
        $em->flush();
    }

    /**
     * @param EventConsequenceTask $task
     */
    private function setTaskRunning($task)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $task->setRunning(true);
        $em->persist($task);
        $em->flush();
    }

    /**
     * @param $count
     * @return array|\Sp\EventBundle\Entity\EventConsequenceTask[]
     */
    private function getTasks($count)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        return $em->getRepository('SpEventBundle:EventConsequenceTask')->findBy(array(), array(), $count);
    }

    /**
     * @return bool
     */
    private function isAlreadyRunning()
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        return (bool) $em->getRepository('SpEventBundle:EventConsequenceTask')->findBy(array('running' => true));
    }

    /**
     * Set running false when task failed
     * @return void
     */
    private function fixZombieTasks() {

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $query = $em->createQueryBuilder();
        $query->update('SpEventBundle:EventConsequenceTask', 't')
            ->set('t.running', '0')
            ->where('DATE_DIFF(CURRENT_DATE(), t.created) > 1')
            ->andWhere('t.running = 1')
            ->getQuery()
            ->execute()
        ;
    }
} 