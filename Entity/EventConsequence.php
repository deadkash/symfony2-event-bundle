<?php

namespace Sp\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventConsequence
 *
 * @ORM\Table("event_consequences")
 * @ORM\Entity
 */
class EventConsequence
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Sp\EventBundle\Entity\Event", inversedBy="consequences", cascade={"persist"})
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(name="params", type="text", nullable=true)
     */
    private $params;

    /**
     * @ORM\OneToMany(targetEntity="Sp\EventBundle\Entity\EventConsequenceLog", mappedBy="consequence", cascade={"persist"})
     */
    private $log;

    /**
     * @ORM\OneToMany(targetEntity="Sp\EventBundle\Entity\EventConsequenceTask", mappedBy="consequence", cascade={"persist"})
     */
    private $tasks;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return EventConsequence
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set event
     *
     * @param Event $event
     * @return EventConsequence
     */
    public function setEvent($event)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set params
     *
     * @param string $params
     * @return EventConsequence
     */
    public function setParams($params)
    {
        $this->params = $params;
    
        return $this;
    }

    /**
     * Get params
     *
     * @return string 
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return mixed
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param mixed $tasks
     */
    public function setTasks($tasks)
    {
        $this->tasks = $tasks;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->log = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add log
     *
     * @param \Sp\EventBundle\Entity\EventConsequenceLog $log
     * @return EventConsequence
     */
    public function addLog(\Sp\EventBundle\Entity\EventConsequenceLog $log)
    {
        $this->log[] = $log;
    
        return $this;
    }

    /**
     * Remove log
     *
     * @param \Sp\EventBundle\Entity\EventConsequenceLog $log
     */
    public function removeLog(\Sp\EventBundle\Entity\EventConsequenceLog $log)
    {
        $this->log->removeElement($log);
    }

    /**
     * Add tasks
     *
     * @param \Sp\EventBundle\Entity\EventConsequenceTask $tasks
     * @return EventConsequence
     */
    public function addTask(\Sp\EventBundle\Entity\EventConsequenceTask $tasks)
    {
        $this->tasks[] = $tasks;
    
        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Sp\EventBundle\Entity\EventConsequenceTask $tasks
     */
    public function removeTask(\Sp\EventBundle\Entity\EventConsequenceTask $tasks)
    {
        $this->tasks->removeElement($tasks);
    }
}