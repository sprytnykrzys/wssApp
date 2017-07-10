<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 */
class Client
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $discount;

    /**
     * @var string
     */
    private $discountCurrency;

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $loginCount;

    /**
     * @var int
     */
    private $generatedOffersCount;


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
     * Set discount
     *
     * @param float $discount
     * @return Client
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param string $discountCurrency
     * @return Client
     */
    public function setDiscountCurrency($discountCurrency)
    {
        $this->discountCurrency = $discountCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountCurrency()
    {
        return $this->discountCurrency;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Client
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
     * Set name
     *
     * @param string $name
     * @return Client
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
     * Set loginCount
     *
     * @param int $loginCount
     * @return Client
     */
    public function setLoginCount($loginCount)
    {
        $this->loginCount = $loginCount;

        return $this;
    }

    /**
     * Increment loginCount
     *
     * @param int $loginCount
     * @return Client
     */
    public function incrementLoginCount()
    {
        $this->loginCount += 1;

        return $this;
    }

    /**
     * Get loginCount
     *
     * @return int
     */
    public function getLoginCount()
    {
        return $this->loginCount;
    }

    /**
     * Set generatedOffersCount
     *
     * @param int $generatedOffersCount
     * @return Client
     */
    public function setGeneratedOffersCount($generatedOffersCount)
    {
        $this->generatedOffersCount = $generatedOffersCount;

        return $this;
    }

    /**
     * Increment generatedOffersCount
     *
     * @param int $generatedOffersCount
     * @return Client
     */
    public function incrementGeneratedOffers()
    {
        $this->generatedOffersCount += 1;

        return $this;
    }

    /**
     * Get generatedOffersCount
     *
     * @return int
     */
    public function getGeneratedOffersCount()
    {
        return $this->generatedOffersCount;
    }


}
