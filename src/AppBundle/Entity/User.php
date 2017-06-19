<?php

namespace AppBundle\Entity;

/**
 * User
 */
class User
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
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $discount;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $tokenExpTime;

    /**
     * @var string
     */
    private $lastHost;

    /**
     * @var \DateTime
     */
    private $creationDate;


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
     * Set idClient
     *
     * @param integer $idClient
     * @return User
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient
     *
     * @return integer 
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = mb_convert_case($role,  MB_CASE_UPPER);

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set discount
     *
     * @param string $discount
     * @return User
     */
    public function setDiscount($discount)
    {
        $this->discount = base64_encode($discount);

        return $this;
    }

    /**
     * Get discount
     *
     * @return string 
     */
    public function getDiscount()
    {
        return base64_decode($this->discount);
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tokenExpTime
     *
     * @param \DateTime $tokenExpTime
     * @return User
     */
    public function setTokenExpTime($tokenExpTime)
    {
        $this->tokenExpTime = $tokenExpTime;

        return $this;
    }

    /**
     * Get tokenExpTime
     *
     * @return \DateTime 
     */
    public function getTokenExpTime()
    {
        return $this->tokenExpTime;
    }

    /**
     * Set lastHost
     *
     * @param string $lastHost
     * @return User
     */
    public function setLastHost($lastHost)
    {
        $this->lastHost = $lastHost;

        return $this;
    }

    /**
     * Get lastHost
     *
     * @return string 
     */
    public function getLastHost()
    {
        return $this->lastHost;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return User
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

    /* Custom user methods */

    public function updateUserStats(){

    }

    /**
     * Generate array based on object for view purposes
     *
     * @return array
     */
    public function prepareArray(){
        return array(
            'uid' => $this->getId(),
            'email' => $this->getEmail(),
            'id_client' => $this->getIdClient(),
            'role' => $this->getRole(),
            'discount' => $this->getDiscount(),
            'last_login' => $this->getLastLogin(),
            'creation_date' => $this->getCreationDate(),
        );
    }
}
