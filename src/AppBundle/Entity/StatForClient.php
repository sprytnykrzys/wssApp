<?php

namespace AppBundle\Entity;

/**
 * StatForClient
 */
class StatForClient
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $idClient;

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
     * @return StatForClient
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
     * @return StatForClient
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
     * @return StatForClient
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
