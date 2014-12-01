<?php

namespace Sp\EventBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EventConsequenceLog
 *
 * @ORM\Table("event_consequence_tasks")
 * @ORM\Entity()
 * @Gedmo\Loggable
 */
class EventConsequenceTask
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
     * @ORM\ManyToOne(targetEntity="Sp\EventBundle\Entity\EventConsequence", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumn(name="consequence_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $consequence;

    /**
     * @var boolean
     *
     * @ORM\Column(name="running", type="boolean", nullable=true)
     */
    private $running = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

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
     * Set consequence
     *
     * @param EventConsequence $consequence
     * @return EventConsequenceLog
     */
    public function setConsequence($consequence)
    {
        $this->consequence = $consequence;

        return $this;
    }

    /**
     * Get consequence
     *
     * @return EventConsequence
     */
    public function getConsequence()
    {
        return $this->consequence;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return EventConsequenceLog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return boolean
     */
    public function isRunning()
    {
        return $this->running;
    }

    /**
     * @param boolean $running
     */
    public function setRunning($running)
    {
        $this->running = $running;
    }

    /**
     * Get running
     *
     * @return boolean 
     */
    public function getRunning()
    {
        return $this->running;
    }
}