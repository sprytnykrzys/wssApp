<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \DateTime
     */
    private $creationDate;

    private $products;

    public function __construct() {
        $this->products = new ArrayCollection();
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

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     *
     * @param array $products
     *
     * @return ArrayCollection
     */

    public function setProducts($products){
        if(!is_array($products)){
            return $this->products;
        }
        foreach ($products as $prod){
            if(!$this->products->contains($prod)){
                $this->products->add($prod);
            }
        }
        return $this->products;
    }
}
