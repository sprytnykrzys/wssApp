<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatForUser
 */
class StatForUser
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $idUser;

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
     * Set idUser
     *
     * @param integer $idUser
     * @return StatForUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set loginCount
     *
     * @param integer $loginCount
     * @return StatForUser
     */
    public function setLoginCount($loginCount)
    {
        $this->loginCount = $loginCount;

        return $this;
    }

    /**
     * Get loginCount
     *
     * @return integer 
     */
    public function getLoginCount()
    {
        return $this->loginCount;
    }

    /**
     * Set generatedOffersCount
     *
     * @param integer $generatedOffersCount
     * @return StatForUser
     */
    public function setGeneratedOffersCount($generatedOffersCount)
    {
        $this->generatedOffersCount = $generatedOffersCount;

        return $this;
    }

    /**
     * Get generatedOffersCount
     *
     * @return integer 
     */
    public function getGeneratedOffersCount()
    {
        return $this->generatedOffersCount;
    }
}
