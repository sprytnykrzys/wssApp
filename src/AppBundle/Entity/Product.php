<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request as Request;

/**
 * Product
 */
class Product
{
    public static $imagePath = 'images/product/';
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $exportCode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $image;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $measureUnit;

    /**
     * @var \DateTime
     */
    private $creationDate;


    private $hierarchy;

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
     * Set code
     *
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set exportCode
     *
     * @param string $exportCode
     * @return Product
     */
    public function setExportCode($exportCode)
    {
        $this->exportCode = $exportCode;

        return $this;
    }

    /**
     * Get exportCode
     *
     * @return string 
     */
    public function getExportCode()
    {
        return $this->exportCode;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
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
     * Set image name
     *
     * @param string $image
     * @return Product
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
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Product
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set measureUnit
     *
     * @param string $measureUnit
     * @return Product
     */
    public function setMeasureUnit($measureUnit)
    {
        $this->measureUnit = $measureUnit;

        return $this;
    }

    /**
     * Get measureUnit
     *
     * @return string 
     */
    public function getMeasureUnit()
    {
        return $this->measureUnit;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Product
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
     * Set hierarchy
     *
     * @param Hierarchy $hierarchy
     * @return Product
     */
    public function setHierarchy($hierarchy)
    {
        $this->hierarchy = $hierarchy;
        return $this;
    }

    /**
     * Get hierarchy
     *
     * @return Hierarchy
     */
    public function getHierarchy()
    {
        return $this->hierarchy;
    }

    public function prepareArray(){
        $ret = array(
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'export_code' => $this->getExportCode(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'currency' => $this->getCurrency(),
            'measure_unit' => $this->getMeasureUnit(),
            'creation_date' => $this->getCreationDate(),
            'id_system' => 0,
        );
        if(is_object($this->getHierarchy())){
            $ret['id_system'] = $this->getHierarchy()->getId();
        }
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
