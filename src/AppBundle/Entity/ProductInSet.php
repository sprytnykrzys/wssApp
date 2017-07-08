<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductInSet
 */
class ProductInSet
{

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductsSet
     */
    private $set;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(ProductsSet $set, Product $product, $quantity = 1)
    {
        $this->set = $set;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * Set set
     *
     * @param ProductsSet $set
     * @return ProductInSet
     */
    public function setSet($set)
    {
        $this->set = $set;

        return $this;
    }

    /**
     * Get set
     *
     * @return ProductsSet
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return ProductInSet
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return ProductInSet
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function prepareArray(){
        $prodInSet = array();
        $prodInSet += $this->getProduct()->prepareArray();
        $prodInSet['quantity'] = $this->getQuantity();
        return $prodInSet;
    }
}
