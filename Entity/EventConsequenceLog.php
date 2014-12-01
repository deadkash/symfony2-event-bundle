<?php

namespace Sp\EventBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EventConsequenceLog
 *
 * @ORM\Table("event_consequence_log")
 * @ORM\Entity(repositoryClass="Sp\EventBundle\Entity\EventConsequenceLogRepository")
 * @Gedmo\Loggable
 */
class EventConsequenceLog
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
     * @ORM\ManyToOne(targetEntity="Sp\EventBundle\Entity\EventConsequence", inversedBy="log", cascade={"persist"})
     * @ORM\JoinColumn(name="consequence_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $consequence;

    /**
     * @var string
     *
     * @ORM\Column(name="unique_field", type="string", length=512, nullable=true)
     */
    private $uniqueField;

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
     * Set гunique
     *
     * @param string $unique
     * @return EventConsequenceLog
     */
    public function setUniqueField($unique)
    {
        $this->uniqueField = $unique;
        return $this;
    }

    /**
     * Get гunique
     *
     * @return string 
     */
    public function getUniqueField()
    {
        return $this->uniqueField;
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
}