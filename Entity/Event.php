<?php

namespace Sp\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Event
 *
 * @ORM\Table("events")
 * @ORM\Entity(repositoryClass="Sp\EventBundle\Entity\EventRepository")
 * @Gedmo\Loggable
 */
class Event
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
     * @ORM\Column(name="name", type="string", length=512)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cronable", type="boolean")
     */
    private $cronable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="Sp\EventBundle\Entity\EventCondition", mappedBy="event", cascade={"persist"})
     */
    private $conditions;

    /**
     * @ORM\OneToMany(targetEntity="Sp\EventBundle\Entity\EventConsequence", mappedBy="event", cascade={"persist"})
     */
    private $consequences;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
        $this->consequences = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Event
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Event
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Event
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Event
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param EventCondition $condition
     */
    public function setCondition(EventCondition $condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param EventConsequence $consequence
     */
    public function setConsequence(EventConsequence $consequence)
    {
        $this->consequences[] = $consequence;
    }

    /**
     * @return mixed
     */
    public function getConsequences()
    {
        return $this->consequences;
    }

    /**
     * @return boolean
     */
    public function isCronable()
    {
        return $this->cronable;
    }

    /**
     * @param boolean $cronable
     */
    public function setCronable($cronable)
    {
        $this->cronable = $cronable;
    }

    /**
     * Get cronable
     *
     * @return boolean 
     */
    public function getCronable()
    {
        return $this->cronable;
    }

    /**
     * Add conditions
     *
     * @param \Sp\EventBundle\Entity\EventCondition $conditions
     * @return Event
     */
    public function addCondition(\Sp\EventBundle\Entity\EventCondition $conditions)
    {
        $this->conditions[] = $conditions;
    
        return $this;
    }

    /**
     * Remove conditions
     *
     * @param \Sp\EventBundle\Entity\EventCondition $conditions
     */
    public function removeCondition(\Sp\EventBundle\Entity\EventCondition $conditions)
    {
        $this->conditions->removeElement($conditions);
    }

    /**
     * Add consequences
     *
     * @param \Sp\EventBundle\Entity\EventConsequence $consequences
     * @return Event
     */
    public function addConsequence(\Sp\EventBundle\Entity\EventConsequence $consequences)
    {
        $this->consequences[] = $consequences;
    
        return $this;
    }

    /**
     * Remove consequences
     *
     * @param \Sp\EventBundle\Entity\EventConsequence $consequences
     */
    public function removeConsequence(\Sp\EventBundle\Entity\EventConsequence $consequences)
    {
        $this->consequences->removeElement($consequences);
    }
}