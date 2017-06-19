<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Hierarchy
 */
class Hierarchy
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $idParent;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $level;

    /**
     * @var \DateTime
     */
    private $creationDate;

    private $products;

    private $products_sets;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->products_sets = new ArrayCollection();
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
     * Set idParent
     *
     * @param integer $idParent
     * @return Hierarchy
     */
    public function setIdParent($idParent)
    {
        $this->idParent = $idParent;

        return $this;
    }

    /**
     * Get idParent
     *
     * @return integer 
     */
    public function getIdParent()
    {
        return $this->idParent;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Hierarchy
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
     * Set level
     *
     * @param integer $level
     * @return Hierarchy
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Hierarchy
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
     * Get products
     *
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function getProductsIds(){
        $ret = array();
        $keys = $this->products->getKeys();
        foreach ($keys as $key){
            $ret[] = $this->products->get($key)->getId();
        }
        return $ret;
    }

    /**
     * Get products_sets
     *
     * @return ArrayCollection
     */
    public function getProductsSets()
    {
        return $this->products_sets;
    }

    public function getProductsSetsIds(){
        $ret = array();
        $keys = $this->products_sets->getKeys();
        foreach ($keys as $key){
            $ret[] = $this->products_sets->get($key)->getId();
        }
        return $ret;
    }

    public function prepareArray(){
        return array(
            'id' => $this->getId(),
            'id_parent' => $this->getIdParent(),
            'name' => $this->getName(),
            'creation_date' => $this->getCreationDate(),
            'level' => $this->getLevel(),
            'products' => $this->getProductsIds(),
            'products_sets' => $this->getProductsSetsIds()
        );
    }
}
