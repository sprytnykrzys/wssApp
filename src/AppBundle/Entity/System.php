<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * System
 */
class System
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $idSystemProvider;

    /**
     * @var \DateTime
     */
    private $creationDate;


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
     * @return System
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
     * Set idSystemProvider
     *
     * @param integer $idSystemProvider
     * @return System
     */
    public function setIdSystemProvider($idSystemProvider)
    {
        $this->idSystemProvider = $idSystemProvider;

        return $this;
    }

    /**
     * Get idSystemProvider
     *
     * @return integer 
     */
    public function getIdSystemProvider()
    {
        return $this->idSystemProvider;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return System
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}
