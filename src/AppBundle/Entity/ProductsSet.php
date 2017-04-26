<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductsSet
 */
class ProductsSet
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $idSystem;

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
     * Set number
     *
     * @param string $number
     * @return ProductsSet
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ProductsSet
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
     * Set idSystem
     *
     * @param integer $idSystem
     * @return ProductsSet
     */
    public function setIdSystem($idSystem)
    {
        $this->idSystem = $idSystem;

        return $this;
    }

    /**
     * Get idSystem
     *
     * @return integer 
     */
    public function getIdSystem()
    {
        return $this->idSystem;
    }

    /**
     * Set idSystemProvider
     *
     * @param integer $idSystemProvider
     * @return ProductsSet
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
     * @return ProductsSet
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
