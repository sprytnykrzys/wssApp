<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request as Request;

/**
 * ProductsSet
 */
class ProductsSet
{
    public static $imagePath = 'images/products_set/';
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $image;

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

    private $hierarchy;
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
     * Set image name
     *
     * @param string $image
     * @return ProductsSet
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image name
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductsSet
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
    public function getProductsIds(){
        $ret = array();
        $keys = $this->products->getKeys();
        foreach ($keys as $key){
            $ret[] = $this->products->get($key)->getId();
        }
        return $ret;
    }

    public function getProductsArray(){
        $ret = array();
        $keys = $this->products->getKeys();
        foreach ($keys as $key){
            $ret[] = $this->products->get($key)->prepareArray();
        }
        return $ret;
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

    /**
     *
     * @param array $products
     *
     * @return ArrayCollection
     */
    public function unsetProduct($product){
        if(!is_object($product)){
            return $this->products;
        }
        $this->products->removeElement($product);
        return $this->products;
    }

    /**
     * Set hierarchy
     *
     * @param Hierarchy $hierarchy
     * @return ProductsSet
     */
    public function setHierarchy($hierarchy)
    {
        $this->hierarchy = $hierarchy;
        return $this;
    }


    public function prepareArray(){
        $ret = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'code' => $this->getNumber(),
            'id_system' => $this->getIdSystem(),
            'products' => $this->getProductsArray(),
            'creation_date' => $this->getCreationDate(),
        );
        if(!is_null($this->image)){
            $base = Request::createFromGlobals()->getSchemeAndHttpHost();
            if('http://wss.v1' != $base){
                $base .= '/web';
            }
            $ret['image'] = $base.'/'.self::$imagePath.$this->image;
        }
        return $ret;
    }

}
