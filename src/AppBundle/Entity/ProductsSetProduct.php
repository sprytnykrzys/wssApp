<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductsSetProduct
 */
class ProductsSetProduct
{
    /**
     * @var int
     */
    private $idProduct;

    /**
     * @var int
     */
    private $idProductSet;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set idProductSet
     *
     * @param integer $idProductSet
     * @return ProductsSetProduct
     */
    public function setIdProductSet($idProductSet)
    {
        $this->idProductSet = $idProductSet;

        return $this;
    }

    /**
     * Get idProductSet
     *
     * @return integer 
     */
    public function getIdProductSet()
    {
        return $this->idProductSet;
    }
}
