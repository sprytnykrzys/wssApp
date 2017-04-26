<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemType
 */
class ItemType
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $itemName;


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
     * @param string $itemName
     * @return ItemType
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getItemName()
    {
        return $this->itemName;
    }
}
