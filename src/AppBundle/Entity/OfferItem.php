<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfferItem
 */
class OfferItem
{
    /**
     * @var int
     */
    private $idOffer;

    /**
     * @var int
     */
    private $idItem;

    /**
     * @var int
     */
    private $idItemType;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getIdOffer()
    {
        return $this->idOffer;
    }
    /**
     * Get id
     *
     * @param integer $idOffer
     * @return integer
     */
    public function setIdOffer($idOffer)
    {
        $this->idOffer = $idOffer;
        return $this->idOffer;
    }

    /**
     * Set idItem
     *
     * @param integer $idItem
     * @return OfferItem
     */
    public function setIdItem($idItem)
    {
        $this->idItem = $idItem;

        return $this;
    }

    /**
     * Get idItem
     *
     * @return integer 
     */
    public function getIdItem()
    {
        return $this->idItem;
    }

    /**
     * Set itemType
     *
     * @param integer $idItemType
     * @return OfferItem
     */
    public function setIdItemType($idItemType)
    {
        $this->idItemType = $idItemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return integer 
     */
    public function getIdItemType()
    {
        return $this->idItemType;
    }
}
